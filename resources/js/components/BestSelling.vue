<template>
  <div class="container">
    <h4 class="mb-4"><i class="fas fa-trophy"></i> Best Sellers</h4>

    <table class="table table-striped">
      <thead class="table-dark">
      <tr>
        <th>Product Name</th>
        <th>Description</th>
        <th>Category</th>
        <th>Units Sold</th>
        <th>Total Revenue</th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="product in filteredBestSellers" :key="product.id">
        <td>{{ product.name }}</td>
        <td>{{ product.description }}</td>
        <td>{{ product.category.name }}</td>
        <td>{{ product.unitsSold }}</td>
        <td>{{ formatCurrency(product.totalRevenue) }}</td>
      </tr>
      <tr v-if="filteredBestSellers.length === 0">
        <td colspan="5" class="text-center">No best sellers found.</td>
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
      sales: [],
      products: [],
      searchQuery: '',
    };
  },
  computed: {
    filteredBestSellers() {
      return this.bestSellers.filter(product =>
          product.name.toLowerCase().includes(this.searchQuery.toLowerCase())
      );
    },
    bestSellers() {
      const productSales = this.sales.reduce((acc, sale) => {
        sale.products.forEach(product => {
          if (!acc[product.id]) {
            acc[product.id] = {...product, unitsSold: 0, totalRevenue: 0};
          }
          acc[product.id].unitsSold += product.pivot.quantity;
          acc[product.id].totalRevenue += product.pivot.quantity * product.pivot.price;
        });
        return acc;
      }, {});

      return Object.values(productSales)
          .sort((a, b) => b.unitsSold - a.unitsSold)
          .slice(0, 3); // Show top 10 best sellers
    },
  },
  created() {
    this.fetchSales();
  },
  methods: {
    async fetchSales() {
      try {
        const response = await axios.get('/api/sales');
        this.sales = response.data;
      } catch (error) {
        console.error('Error fetching sales:', error);
      }
    },
    formatCurrency(value) {
      return new Intl.NumberFormat('sw-TZ', {style: 'currency', currency: 'TZS'}).format(value);
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
