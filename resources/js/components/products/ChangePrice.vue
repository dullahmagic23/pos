<template>
    <div>
        <h2>Change Product Price</h2>

        <form @submit.prevent="updatePrice">
            <!-- Product Selection -->
            <div class="form-group mb-3">
                <label for="product">Select Product:</label>
                <select v-model="selectedProduct" id="product" class="form-control" @change="fetchUnitsAndExpiryDate">
                    <option value="" disabled>Select a product</option>
                    <option v-for="product in products" :key="product.id" :value="product.id">
                        {{product.code}} - {{ product.name }} || {{product.description}}
                    </option>
                </select>
                <span v-if="errors.product" class="error">{{ errors.product }}</span>
            </div>

            <!-- Unit Selection -->
            <div class="form-group mb-3">
                <label for="unit">Select Unit:</label>
                <select v-model="selectedUnit" id="unit" class="form-control" @change="fetchExpiryDate">
                    <option value="" disabled>Select a unit</option>
                    <option v-for="unit in units" :key="unit.id" :value="unit">
                        {{ unit.name }}
                    </option>
                </select>
                <span v-if="errors.unit" class="error">{{ errors.unit }}</span>
            </div>

            <!-- Price Input -->
            <div class="form-group mb-3">
                <label for="price">New Price:</label>
                <input
                    type="number"
                    id="price"
                    v-model="newPrice"
                    class="form-control"
                    placeholder="Enter new price"
                    step="0.01"
                />
                <span v-if="errors.price" class="error">{{ errors.price }}</span>
            </div>

            <!-- Expiry Date Input -->
            <div class="form-group mb-3">
                <label for="expiryDate">Expiry Date:</label>
                <input
                    type="date"
                    id="expiryDate"
                    v-model="expiryDate"
                    class="form-control"
                    :disabled="expiryDateFetched"
                />
                <span v-if="errors.expiryDate" class="error">{{ errors.expiryDate }}</span>
            </div>

            <!-- Submit Button with Loader -->
            <button type="submit" class="btn btn-primary" :disabled="loading">
                <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Update Price
            </button>
        </form>

        <!-- Bootstrap Toast Notification -->
        <div v-if="message" class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999">
            <div class="toast fade show text-bg-success" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <strong class="me-auto" :class="message.type === 'success' ? 'text-success' : 'text-danger'">
                        {{ message.type === 'success' ? 'Success' : 'Error' }}
                    </strong>
                    <button type="button" class="btn-close" @click="closeToast"></button>
                </div>
                <div class="toast-body">
                    {{ message.text }}
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    data() {
        return {
            selectedProduct: "",
            selectedUnit: "",
            newPrice: null,
            expiryDate: null,
            expiryDateFetched: false,
            products: [],
            units: [],
            errors: {},
            message: null,
            loading: false,
        };
    },
    created() {
        this.fetchProducts();
    },
    methods: {
        // Fetch available products from the backend
        async fetchProducts() {
            try {
                const response = await axios.get("/api/products");
                this.products = response.data;
            } catch (error) {
                console.error("Error fetching products:", error);
            }
        },

        // Fetch available units and expiry date based on the selected product
        async fetchUnitsAndExpiryDate() {
            if (!this.selectedProduct) {
                this.units = [];
                this.expiryDate = null;
                this.expiryDateFetched = false;
                return;
            }

            try {
                const response = await axios.get(`/api/products/${this.selectedProduct}/units`);
                this.units = response.data;
                await this.fetchExpiryDate();
            } catch (error) {
                console.error("Error fetching units and expiry date:", error);
            }
        },

        // Fetch expiry date based on the selected product and unit
        async fetchExpiryDate() {
            if (!this.selectedProduct || !this.selectedUnit) {
                this.expiryDate = null;
                this.expiryDateFetched = false;
                return;
            }

            try {
                const response = await axios.get(`/api/products/${this.selectedProduct}/units/${this.selectedUnit.id}/expiry-date`);
                if (response.data.expiry) {
                    this.expiryDate = response.data.expiry;
                    this.expiryDateFetched = true;
                } else {
                    this.expiryDate = null;
                    this.expiryDateFetched = false;
                }
            } catch (error) {
                console.error("Error fetching expiry date:", error);
            }
        },

        // Show Toast Message
        showToast(type, text) {
            this.message = { type, text };
            // Automatically hide the toast after 3 seconds
            setTimeout(() => {
                this.message = null;
            }, 3000);
        },

        // Close toast manually
        closeToast() {
            this.message = null;
        },

        // Validate the form inputs
        validate() {
            this.errors = {};

            if (!this.selectedProduct) {
                this.errors.product = "Product is required.";
            }
            if (!this.selectedUnit) {
                this.errors.unit = "Unit is required.";
            }
            if (!this.newPrice || this.newPrice <= 0) {
                this.errors.price = "Price must be a positive number.";
            }
            if (!this.expiryDate) {
                this.errors.expiryDate = "Expiry date is required.";
            }

            return Object.keys(this.errors).length === 0;
        },

        // Update the price and send the data to the backend
        async updatePrice() {
            if (!this.validate()) {
                return; // Stop if validation fails
            }

            this.loading = true;  // Start loader
            try {
                const payload = {
                    productId: this.selectedProduct,
                    unitId: this.selectedUnit.id,
                    price: this.newPrice,
                    expiryDate: this.expiryDate,
                };

                await axios.post(`/api/products/change-price/${this.selectedProduct}`, payload);

                // Handle success response
                this.showToast("success", "Price updated successfully!");

                // Optionally, refresh product list or clear inputs
                this.selectedProduct = "";
                this.selectedUnit = "";
                this.newPrice = null;
                this.expiryDate = null;
                this.expiryDateFetched = false;
            } catch (error) {
                // Handle error response
                this.showToast("error", "Error updating price. Please try again.");
                console.error("Error updating price:", error);
            } finally {
                this.loading = false;  // Stop loader
            }
        },
    },
};
</script>

<style scoped>
.error {
    color: red;
    font-size: 12px;
}

button:disabled {
    background-color: #ccc;
}

.toast-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
}

.spinner-border {
    margin-right: 10px;
}
</style>
