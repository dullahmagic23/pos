<template>
    <div class="container">
        <h4 class="mb-4"><i class="fas fa-exclamation-triangle"></i> Expiring Products</h4>
        <table class="table table-striped">
            <thead class="table-dark">
            <tr>
                <th>Product Name</th>
                <th>Description</th>
                <th>Unit</th>
                <th>Quantity</th>
                <th>Expiry Date</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="product in filteredProducts" :key="product.id">
                <td>{{ product.name }}</td>
                <td>{{ product.description }}</td>
                <td>{{ product.unit }}</td>
                <td>{{ product.quantity }}</td>
                <td>{{ formatDate(product.expiry_date) }}</td>
                <td>{{ product.status }}</td>
            </tr>
            <tr v-if="filteredProducts.length === 0">
                <td colspan="6" class="text-center">No expiring products found.</td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            products: [],
            searchQuery: '',
            thresholdDays: 30, // Define threshold for "about to expire" in days
        };
    },
    computed: {
        filteredProducts() {
            return this.products.filter(product =>
                product.name.toLowerCase().includes(this.searchQuery.toLowerCase()) &&
                (this.isExpired(product.expiry_date) || this.isAboutToExpire(product.expiry_date)) &&
                product.quantity > 0
            );
        },
    },
    created() {
        this.fetchProducts();
    },
    methods: {
        async fetchProducts() {
            try {
                const response = await axios.get('/api/products');
                this.products = response.data.flatMap(product =>
                    product.units.map(unit => ({
                        ...product,
                        unit: unit.name,
                        quantity: unit.pivot.quantity,
                        expiry_date: unit.pivot.expiry_date,
                        status: this.getProductStatus(unit.pivot.expiry_date)
                    }))
                );
            } catch (error) {
                console.error('Error fetching products:', error);
            }
        },
        getProductStatus(expiryDate) {
            if (this.isExpired(expiryDate)) {
                return 'Expired';
            } else if (this.isAboutToExpire(expiryDate)) {
                return 'About to Expire';
            }
            return 'Valid';
        },
        isExpired(expiryDate) {
            return new Date(expiryDate) < new Date();
        },
        isAboutToExpire(expiryDate) {
            const currentDate = new Date();
            const expiry = new Date(expiryDate);
            const timeDifference = expiry - currentDate;
            const daysDifference = timeDifference / (1000 * 3600 * 24);
            return daysDifference <= this.thresholdDays && daysDifference >= 0;
        },
        formatDate(dateString) {
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return new Date(dateString).toLocaleDateString('en-US', options);
        },
    },
};
</script>

<style scoped>
.container {
    width: 100%;
}
.table-dark {
    background-color: #343a40;
    color: white;
}
.table-striped tbody tr:nth-of-type(odd) {
    background-color: rgba(0, 0, 0, 0.05);
}
.text-center {
    text-align: center;
}
</style>
