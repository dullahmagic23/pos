<template>
  <div class="container mt-5">
    <h2><i class="fas fa-boxes"></i> Add Product to Stock</h2>
    <form @submit.prevent="handleSubmit">
      <div class="mb-3">
        <label for="product" class="form-label">
          <i class="fas fa-box"></i> Product:
        </label>
        <select id="product" v-model="stock.product_id" @change="fetchProductUnits" class="form-select">
          <option value="">Select a product</option>
          <option v-for="product in products" :key="product.id" :value="product.id">
              {{product.code}} - {{ product.name }} || {{product.description}}
          </option>
        </select>
        <span v-if="errors.product_id" class="text-danger">{{ errors.product_id }}</span>
      </div>

      <div class="mb-3">
        <label for="unit" class="form-label">
          <i class="fas fa-ruler"></i> Unit:
        </label>
        <select id="unit" v-model="stock.unit_id" class="form-select">
          <option value="">Select a unit</option>
          <option v-for="unit in units" :key="unit.id" :value="unit.id">{{ unit.name }}</option>
        </select>
        <span v-if="errors.unit_id" class="text-danger">{{ errors.unit_id }}</span>
      </div>

      <div class="mb-3">
        <label for="quantity" class="form-label">
          <i class="fas fa-sort-numeric-up-alt"></i> Quantity:
        </label>
        <input type="number" id="quantity" v-model="stock.quantity" class="form-control" />
        <span v-if="errors.quantity" class="text-danger">{{ errors.quantity }}</span>
      </div>

      <div class="mb-3">
        <label for="expiry_date" class="form-label">
          <i class="fas fa-calendar-times"></i> Expiry Date:
        </label>
        <input type="date" id="expiry_date" v-model="stock.expiry_date" class="form-control" />
        <span v-if="errors.expiry_date" class="text-danger">{{ errors.expiry_date }}</span>
      </div>

      <div class="mb-3">
        <label for="added_date" class="form-label">
          <i class="fas fa-calendar-plus"></i> Date Added:
        </label>
        <input type="date" id="added_date" v-model="stock.added_date" class="form-control" />
        <span v-if="errors.added_date" class="text-danger">{{ errors.added_date }}</span>
      </div>

      <button type="submit" class="btn btn-primary">
        <i class="fas fa-save"></i> Add to Stock
      </button>
    </form>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      stock: {
        product_id: '',
        unit_id: '',
        quantity: '',
        expiry_date: '',
        added_date: ''
      },
      products: [],
      units: [],
      errors: {}
    };
  },
  created() {
    this.fetchProducts();
  },
  methods: {
    async fetchProducts() {
      try {
        const response = await axios.get('/api/products');
        this.products = response.data;
      } catch (error) {
        console.error('Error fetching products:', error);
      }
    },
    async fetchProductUnits() {
      try {
        const response = await axios.get(`/api/products/${this.stock.product_id}/units`);
        this.units = response.data;
      } catch (error) {
        console.error('Error fetching units:', error);
      }
    },
    async handleSubmit() {
      if (this.validateForm()) {
        try {
          await axios.post('/api/stocks', this.stock);
          this.$notify("Product added to stock successfully.");
          // Reset the form or show a success message
          this.stock = {
            product_id: '',
            unit_id: '',
            quantity: '',
            expiry_date: '',
            added_date: ''
          };
        } catch (error) {
          console.error('Error adding to stock:', error);
          if (error.response && error.response.data && error.response.data.errors) {
            this.errors = error.response.data.errors;
          }
        }
      }
    },
    validateForm() {
      this.errors = {};

      if (!this.stock.product_id) this.errors.product_id = 'Product is required.';
      if (!this.stock.unit_id) this.errors.unit_id = 'Unit is required.';
      if (!this.stock.quantity) this.errors.quantity = 'Quantity is required.';
      if (!this.stock.expiry_date) this.errors.expiry_date = 'Expiry date is required.';
      if (!this.stock.added_date) this.errors.added_date = 'Date added is required.';

      return Object.keys(this.errors).length === 0;
    }
  }
};
</script>

<style>
@import 'bootstrap/dist/css/bootstrap.min.css';
</style>
