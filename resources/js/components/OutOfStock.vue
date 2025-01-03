<template>
  <div class="container">
    <h4 class="mb-4"><i class="fas fa-exclamation-triangle"></i> Low Stock Products</h4>

    <table class="table table-striped">
      <thead class="table-dark">
      <tr>
        <th>Product Name</th>
        <th>Description</th>
        <th>Unit</th>
        <th>Quantity</th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="product in filteredProducts" :key="product.id">
        <td>{{ product.name }}</td>
        <td>{{ product.description }}</td>
        <td>{{ product.unit?product.unit.name:'' }}</td>
        <td>{{ product.quantity }}</td>
      </tr>
      <tr v-if="filteredProducts.length === 0">
        <td colspan="4" class="text-center">No low stock products found.</td>
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
      lowStockThreshold: 10,  // Directly define threshold as a data property
    };
  },
  computed: {
    filteredProducts() {
      return this.products.filter(product =>
          product.name.toLowerCase().includes(this.searchQuery.toLowerCase()) &&
          product.quantity <= this.lowStockThreshold
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
        this.products = response.data.map(product => ({
          ...product,
          unit: product.units.find(unit => unit.pivot.quantity !== null),
          quantity: product.units.reduce((acc, unit) => acc + (unit.pivot.quantity || 0), 0)
        }));
        console.log(this.products)
      } catch (error) {
        console.error('Error fetching products:', error);
      }
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
