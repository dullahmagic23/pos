<template>
    <div class="container-fluid mt-5">
        <h2>Product List</h2>

        <!-- Search Bar -->
        <div class="mb-3">
            <input
                type="text"
                class="form-control"
                v-model="searchQuery"
                @input="searchProducts"
                placeholder="Search products by name or code"
            />
        </div>

        <!-- Product Table -->
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Category</th>
                <th>Units</th>
                <th>Code</th>
                <th>DISCOUNTS</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="product in paginatedProducts" :key="product.id">
                <td>{{ product.name }}</td>
                <td>{{ product.description }}</td>
                <td>{{ product.category.name }}</td>
                <td>{{ product.units?.map(unit => unit.name).join(', ') }}</td>
                <td>{{ product.code }}</td>
                <td>
                    <ul v-if="product.discounts.length">
                        <li v-for="discount in product.discounts" :key="discount.id">
                            {{ discount.name }}
                            <button class="btn btn-danger btn-sm" @click="cancelDiscounts(product.id, discount.id)">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                        </li>
                    </ul>
                </td>
                <td>
                    <button class="btn btn-info btn-sm" @click="launchDiscountModal(product)">
                        <i class="fas fa-percent"></i> Discount
                    </button>
                    <button class="btn btn-warning btn-sm" @click="launchEditModal(product)">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                </td>
            </tr>
            </tbody>
        </table>

        <!-- Pagination Controls -->
        <nav>
            <ul class="pagination justify-content-center">
                <li class="page-item" :class="{ disabled: currentPage === 1 }">
                    <button class="page-link" @click="changePage(currentPage - 1)">Previous</button>
                </li>
                <li
                    v-for="page in totalPages"
                    :key="page"
                    class="page-item"
                    :class="{ active: currentPage === page }"
                >
                    <button class="page-link" @click="changePage(page)">{{ page }}</button>
                </li>
                <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                    <button class="page-link" @click="changePage(currentPage + 1)">Next</button>
                </li>
            </ul>
        </nav>

        <!-- Discount Modal -->
        <div class="modal fade" id="discountModal" tabindex="-1" aria-labelledby="discountModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="discountModalLabel">Apply Discount</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="applyDiscount">
                            <div class="mb-3">
                                <label for="discount" class="form-label">Select Discount:</label>
                                <select id="discount" v-model="selectedDiscount" class="form-select">
                                    <option value="" disabled>Select a discount</option>
                                    <option v-for="discount in discounts" :key="discount.id" :value="discount.id">
                                        {{ discount.name }} - {{ discount.value }}%
                                    </option>
                                </select>
                                <span v-if="discountError" class="text-danger">{{ discountError }}</span>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check"></i> Apply Discount
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" v-if="currentProduct">
                        <form @submit.prevent="updateProduct">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name:</label>
                                <input type="text" id="name" v-model="currentProduct.name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description:</label>
                                <textarea id="description" v-model="currentProduct.description" class="form-control" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="category" class="form-label">Category:</label>
                                <select id="category" v-model="currentProduct.category_id" class="form-select" required>
                                    <option value="" disabled>Select a category</option>
                                    <option v-for="category in categories" :key="category.id" :value="category.id">
                                        {{ category.name }}
                                    </option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="units" class="form-label">Units:</label>
                                <div class="form-check form-check-inline" v-for="unit in units" :key="unit.id">
                                    <input
                                        type="checkbox"
                                        class="form-check-input"
                                        :id="`unit-${unit.id}`"
                                        :value="unit.id"
                                        v-model="currentProduct.unit_ids"
                                    >
                                    <label :for="`unit-${unit.id}`" class="form-check-label">{{ unit.name }}</label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="code" class="form-label">Code:</label>
                                <input type="text" id="code" v-model="currentProduct.code" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check"></i> Update Product
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
import axios from "axios";
import { Modal } from "bootstrap";

export default {
    data() {
        return {
            products: [],
            categories: [],
            units: [],
            discounts: [],
            currentProduct: null,
            selectedDiscount: "",
            discountError: "",
            errors: {},
            searchQuery: "",
            currentPage: 1,
            itemsPerPage: 10,
        };
    },
    computed: {
        filteredProducts() {
            if (!this.searchQuery) {
                return this.products;
            }
            return this.products.filter(
                (product) =>
                    product.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                    product.code.toLowerCase().includes(this.searchQuery.toLowerCase())
            );
        },
        paginatedProducts() {
            const startIndex = (this.currentPage - 1) * this.itemsPerPage;
            return this.filteredProducts.slice(startIndex, startIndex + this.itemsPerPage);
        },
        totalPages() {
            return Math.ceil(this.filteredProducts.length / this.itemsPerPage);
        },
    },
    created() {
        this.fetchProducts();
        this.fetchCategories();
        this.fetchUnits();
        this.fetchDiscounts();
    },
    methods: {
        async fetchProducts() {
            try {
                const response = await axios.get("/api/products");
                this.products = response.data;
            } catch (error) {
                console.error("Error fetching products:", error);
            }
        },
        async fetchCategories() {
            try {
                const response = await axios.get("/api/categories");
                this.categories = response.data;
            } catch (error) {
                console.error("Error fetching categories:", error);
            }
        },
        async fetchUnits() {
            try {
                const response = await axios.get("/api/units");
                this.units = response.data;
            } catch (error) {
                console.error("Error fetching units:", error);
            }
        },
        async fetchDiscounts() {
            try {
                const response = await axios.get("/api/discounts");
                this.discounts = response.data;
            } catch (error) {
                console.error("Error fetching discounts:", error);
            }
        },
        launchDiscountModal(product) {
            this.currentProduct = product;
            this.selectedDiscount = "";
            this.discountError = "";
            const modal = new Modal(document.getElementById("discountModal"));
            modal.show();
        },
        launchEditModal(product) {
            this.currentProduct = { ...product, unit_ids: product.units.map(unit => unit.id) };
            const modal = new Modal(document.getElementById("editModal"));
            modal.show();
        },
        async updateProduct() {
            try {
                const response = await axios.put(`/api/products/${this.currentProduct.id}`, this.currentProduct);
                this.$notify(response.data.message);
                document.querySelector("#editModal .btn-close").click();
                this.fetchProducts();
            } catch (error) {
                console.error("Error updating product:", error);
                this.$notify("An error occurred while updating the product.", "error");
            }
        },
        async applyDiscount() {
            if (!this.selectedDiscount) {
                this.discountError = "Please select a discount.";
                return;
            }
            try {
                const response = await axios.post(`/api/discounts/product/${this.currentProduct.id}`, {
                    discount_id: this.selectedDiscount,
                });
                this.$notify(response.data.message);
                document.querySelector("#discountModal .btn-close").click();
                this.fetchProducts();
            } catch (error) {
                this.discountError = "An error occurred while applying the discount.";
            }
        },
        cancelDiscounts(productId, discounId) {
            if (!confirm("Are you sure you want to cancel all discounts for this product?")) {
                return;
            }
            axios
                .delete(`/api/discounts/product/${productId}/${discounId}`)
                .then((response) => {
                    alert("Discounts canceled successfully!");
                    this.$notify(response.data.message);
                    this.fetchProducts();
                })
                .catch((error) => {
                    console.error("Error canceling discounts:", error);
                    this.$notify("An error occurred while canceling the discounts.", "error");
                });
        },
        changePage(page) {
            this.currentPage = page;
        },
        searchProducts() {
            this.currentPage = 1;
        },
    },
};
</script>
