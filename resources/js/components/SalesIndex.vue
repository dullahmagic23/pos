<template>
    <div class="container-fluid mt-2">
        <h2 class="text-center mb-4"><i class="fas fa-receipt"></i> Sales List</h2>
        <div v-if="isLoading" class="text-center my-4">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
        <div v-else>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="search" class="form-label"><i class="fas fa-search"></i> Search by Customer Name:</label>
                    <input type="text" id="search" v-model="searchQuery" @input="debouncedSearch" class="form-control" placeholder="Enter customer name" />
                </div>
                <div class="col-md-4">
                    <label for="startDate" class="form-label"><i class="fas fa-calendar-alt"></i> Start Date:</label>
                    <input type="date" id="startDate" v-model="startDate" class="form-control" />
                </div>
                <div class="col-md-4">
                    <label for="endDate" class="form-label"><i class="fas fa-calendar-alt"></i> End Date:</label>
                    <input type="date" id="endDate" v-model="endDate" class="form-control" />
                </div>
            </div>
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                <tr>
                    <th>Customer</th>
                    <th>Products</th>
                    <th>Units</th>
                    <th>Quantities</th>
                    <th>Prices</th>
                    <th>Amount</th>
                    <th>Paid</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="sale in filteredSales" :key="sale.id">
                    <td>{{ sale.customer.name }}</td>
                    <td>
                        <ul class="list-unstyled">
                            <li v-for="product in sale.products" :key="product.id">{{ product.name }}</li>
                        </ul>
                    </td>
                    <td>
                        <ul class="list-unstyled">
                            <li v-for="product in sale.products" :key="product.id">{{ product.units.find(u => u.id === product.pivot.unit_id).name }}</li>
                        </ul>
                    </td>
                    <td>
                        <ul class="list-unstyled">
                            <li v-for="product in sale.products" :key="product.id">{{ product.pivot.quantity }}</li>
                        </ul>
                    </td>
                    <td>
                        <ul class="list-unstyled">
                            <li v-for="product in sale.products" :key="product.id">{{ formatCurrency(product.pivot.price) }}</li>
                        </ul>
                    </td>
                    <td>{{ formatCurrency(sale.products.reduce((total, product) => total + parseFloat(product.pivot.total), 0)) }}</td>
                    <td>{{ formatCurrency(sale.payments.reduce((amount, payment) => amount + parseFloat(payment.amount), 0)) }}</td>
                    <td>{{ formatDate(sale.created_at) }}</td>
                    <td>
                        <div class="text-end">
                            <button title="Cancel sales" class="btn btn-danger btn-sm" @click="handleCancel(sale.id)" aria-label="Cancel this sale">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                        </div>
                    </td>
                </tr>
                <tr class="table-light">
                    <td colspan="6"><strong>Total Sales Amount</strong></td>
                    <td colspan="3"><strong>{{ formatCurrency(totalSalesAmount) }}</strong></td>
                </tr>
                </tbody>
            </table>
            <div v-if="errorMessage" class="alert alert-danger" role="alert">
                {{ errorMessage }}
            </div>
        </div>
    </div>
</template>


<script>
import axios from 'axios';
import debounce from 'lodash.debounce';

export default {
    data() {
        return {
            sales: [],
            searchQuery: '',
            startDate: '',
            endDate: '',
            isLoading: false,
            errorMessage: '',
        };
    },
    created() {
        this.fetchSales();
    },
    methods: {
        async fetchSales() {
            this.isLoading = true;
            this.errorMessage = '';
            try {
                const response = await axios.get('/api/sales');
                this.sales = response.data;
            } catch (error) {
                this.errorMessage = `Failed to load sales data. Error: ${error.message}`;
                console.error('Error fetching sales:', error);
            } finally {
                this.isLoading = false;
            }
        },
        formatCurrency(value) {
            return new Intl.NumberFormat('sw-TZ', { style: 'currency', currency: 'TZS' }).format(value);
        },

        formatDate(dateString) {
            const options = {year: 'numeric', month: 'numeric', day: 'numeric'};
            return new Date(dateString).toLocaleDateString('sw-TZ', options);
        },
        async handleCancel(saleId) {
            const sale = this.sales.find(s => s.id === saleId);
            console.log(sale);
            if (confirm('Are you sure you want to cancel this sale?')) {
                try {
                    const response = await axios.delete(`/api/sales/${saleId}/cancel`);
                    alert(response.data.message);
                    this.fetchSales();
                } catch (error) {
                    this.errorMessage = `Failed to cancel the sale. Error: ${error.message}`;
                    console.error('Error cancelling sale:', error);
                }
            }
        },
        debouncedSearch: debounce(function () {
            this.fetchSales();
        }, 300),
    },
    computed: {
        filteredSales() {
            return this.sales.filter((sale) => {
                const saleDate = new Date(sale.date);
                const matchesCustomer = sale.customer && sale.customer.name.toLowerCase().includes(this.searchQuery.toLowerCase());
                const matchesDate = (!this.startDate || saleDate >= new Date(this.startDate)) && (!this.endDate || saleDate <= new Date(this.endDate));
                return matchesCustomer && matchesDate
            });
        },
        totalSalesAmount() {
            return this.filteredSales.reduce((total, sale) => {
                return total + sale.products.reduce((sum, product) => {
                    return sum + parseFloat(product.pivot.total);
                }, 0);
            }, 0);
        },
        totalPaidAmount() {
            return this.filteredSales.reduce((total, sale) => {
                return total + sale.payments.reduce((sum, payment) => {
                    return sum + parseFloat(payment.amount);
                }, 0);
            }, 0);
        },
    },
};

</script>

<style>
.container {
    background-color: #f8f9fa;
    border-radius: 8px;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
}
.table {
    border: 2px solid black;
}
.table-hover tbody tr:hover {
    background-color: #e9ecef;
}
.table-light {
    font-weight: bold;
}
.btn {
    min-width: 100px;
}
</style>
