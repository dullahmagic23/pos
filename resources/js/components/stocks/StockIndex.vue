<template>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                        <h3><i class="fas fa-warehouse"></i> Products</h3>
                        <input
                            type="text"
                            v-model="searchQuery"
                            class="form-control w-25"
                            placeholder="Search by product name"
                            @input="filterProducts"
                        />
                    </div>
                    <div class="card-body">
                        <!-- Loader -->
                        <div v-if="loading" class="d-flex justify-content-center align-items-center" style="height: 300px;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>

                        <!-- Export Buttons -->
                        <div v-else class="d-flex justify-content-end mb-4">
                            <button class="btn btn-success me-2" @click="exportToExcel"><i class="fas fa-file-excel"></i> Export to Excel</button>
                            <button class="btn btn-danger me-2" @click="exportToPDF"><i class="fas fa-file-pdf"></i> Export to PDF</button>
                            <button class="btn btn-secondary" @click="printPage"><i class="fas fa-print"></i> Print</button>
                        </div>

                        <table v-if="!loading" class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Description</th>
                                <th>Units</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Expire Date</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="product in filteredProducts" :key="product.id">
                                <td>{{ product.id }}</td>
                                <td>{{ product.name }}</td>
                                <td>{{ product.description }}</td>
                                <td>
                                    <ul class="list-unstyled">
                                        <li v-for="unit in product.filteredUnits" :key="unit.name">{{ unit.name }}</li>
                                    </ul>
                                </td>
                                <td>
                                    <ul class="list-unstyled">
                                        <li v-for="unit in product.filteredUnits" :key="unit.name">{{ unit.pivot.quantity }}</li>
                                    </ul>
                                </td>
                                <td>
                                    <ul class="list-unstyled">
                                        <li v-for="unit in product.filteredUnits" :key="unit.name">{{ unit.pivot.price }}</li>
                                    </ul>
                                </td>
                                <td>
                                    <ul class="list-unstyled">
                                        <li v-for="unit in product.filteredUnits" :key="unit.name">{{ unit.pivot.expiry_date }}</li>
                                    </ul>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import * as XLSX from 'xlsx';
import { saveAs } from 'file-saver';
import jsPDF from 'jspdf';
import 'jspdf-autotable';

export default {
    data() {
        return {
            products: [],
            filteredProducts: [],
            searchQuery: '',
            selectedStock: null,
            newQuantity: '',
            errors: {},
            loading: true // Track the loading state
        };
    },
    mounted() {
        this.fetchProducts();
    },
    computed: {
        filteredProducts() {
            return this.products.map(product => {
                return {...product, filteredUnits: product.units.filter(unit => unit.pivot.quantity !== null)};
            });
        }
    },
    methods: {
        async fetchProducts() {
            try {
                const response = await axios.get('/api/products');
                this.products = response.data;
                this.filteredProducts = response.data;
            } catch (error) {
                console.error('Error fetching products:', error);
            } finally {
                this.loading = false; // Set loading to false once the data is fetched
            }
        },
        filterProducts() {
            this.filteredProducts = this.products.filter(product =>
                product.name.toLowerCase().includes(this.searchQuery.toLowerCase())
            );
        },
        selectStock(stock) {
            this.selectedStock = stock;
            this.newQuantity = '';
            this.errors = {};
        },
        validate() {
            let errors = {};
            if (!this.newQuantity || this.newQuantity <= 0) {
                errors.newQuantity = 'Quantity must be greater than zero';
            }
            return errors;
        },
        async updateQuantity() {
            this.errors = this.validate();
            if (Object.keys(this.errors).length === 0) {
                try {
                    const response = await axios.post(`/api/products/${this.selectedStock.id}/addQuantity`, {
                        quantity: this.newQuantity,
                        stock: this.selectedStock.id
                    });
                    this.fetchProducts();
                    this.$notify("Quantity updated successfully");
                    this.filterProducts(); // Update the filtered products
                    // Close modal
                    document.querySelector('#addQuantityModal .btn-close').click();
                } catch (error) {
                    console.error('Error updating quantity:', error);
                }
            }
        },
        formatCurrency(value) {
            return new Intl.NumberFormat('sw-TZ', {style: 'currency', currency: 'TZS'}).format(value);
        },
        formatDate(dateString) {
            const options = {year: 'numeric', month: 'long', day: 'numeric'};
            return new Date(dateString).toLocaleDateString('sw-TZ', options);
        },
        exportToExcel() {
            const wsData = this.filteredProducts.map((product, index) => {
                const unitsWithQuantity = product.units.filter(unit => unit.pivot.quantity !== null);
                return {
                    '#': index + 1,
                    'Product': product.name,
                    'Description': product.description,
                    'Unit': unitsWithQuantity.map(unit => unit.name).join(', '),
                    'Quantity': unitsWithQuantity.map(unit => unit.pivot.quantity).join(', '),
                    'Price': unitsWithQuantity.map(unit => this.formatCurrency(unit.pivot.price)).join(', '),
                    'Expire Date': unitsWithQuantity.map(unit => this.formatDate(unit.pivot.expiry_date)).join(', ')
                };
            });
            const ws = XLSX.utils.json_to_sheet(wsData);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Products');
            const wbout = XLSX.write(wb, {bookType: 'xlsx', type: 'array'});
            saveAs(new Blob([wbout], {type: 'application/octet-stream'}), 'products.xlsx');
        },
        exportToPDF() {
            const doc = new jsPDF('landscape');
            const columns = ['#', 'Product', 'Description', 'Unit', 'Quantity', 'Price', 'Expire Date'];
            const rows = this.filteredProducts.map((product, index) => {
                const unitsWithQuantity = product.units.filter(unit => unit.pivot.quantity !== null);
                return [
                    index + 1,
                    product.name,
                    product.description,
                    unitsWithQuantity.map(unit => unit.name).join(', '),
                    unitsWithQuantity.map(unit => unit.pivot.quantity).join(', '),
                    unitsWithQuantity.map(unit => this.formatCurrency(unit.pivot.price)).join(', '),
                    unitsWithQuantity.map(unit => this.formatDate(unit.pivot.expiry_date)).join(', ')
                ];
            });

            // Use the latest autoTable method
            doc.autoTable({
                head: [columns],
                body: rows,
            });

            doc.save('products.pdf');
        },

        printPage() {
            const doc = new jsPDF('landscape');
            const columns = ['#', 'Product', 'Description', 'Unit', 'Quantity', 'Price', 'Expire Date'];
            const rows = this.filteredProducts.map((product, index) => {
                const unitsWithQuantity = product.units.filter(unit => unit.pivot.quantity !== null);
                return [
                    index + 1,
                    product.name,
                    product.description,
                    unitsWithQuantity.map(unit => unit.name).join(', '),
                    unitsWithQuantity.map(unit => unit.pivot.quantity).join(', '),
                    unitsWithQuantity.map(unit => this.formatCurrency(unit.pivot.price)).join(', '),
                    unitsWithQuantity.map(unit => this.formatDate(unit.pivot.expiry_date)).join(', ')
                ];
            });

            // Use the latest autoTable method
            doc.autoTable({
                head: [columns],
                body: rows,
            });

            // Open the print dialog
            doc.output('dataurlnewwindow');
        }
    }
}
</script>

<style scoped>
.container {
    width: 100%;
}

.card-header {
    background-color: #f7f7f7;
    border-bottom: 1px solid #e3e3e3;
}

.card-header h3 {
    margin: 0;
}

.table-hover tbody tr:hover {
    background-color: #e9ecef;
}

.invalid-feedback {
    display: block;
}

.modal-header {
    background-color: #f8f9fa;
}

.modal-title {
    font-size: 1.25rem;
}

.modal-body {
    padding: 1.5rem;
}

.btn-primary {
    background-color: #007bff;
    border-color: #007bff;
}

.btn-primary:hover {
    background-color: #0056b3;
    border-color: #0056b3;
}

.btn-close {
    background-color: transparent;
    border: none;
    cursor: pointer;
}

.btn-close:hover {
    color: #000;
}
</style>
