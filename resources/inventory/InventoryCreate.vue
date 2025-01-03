<template>
  <div class="container mt-5">
    <h2><i class="fas fa-truck-loading"></i> Register Supplier Order</h2>

    <form @submit.prevent="addItemToOrder">
      <!-- Supplier Selection -->
      <div class="mb-3">
        <label for="supplier" class="form-label">
          <i class="fas fa-user"></i> Supplier:
        </label>
        <select id="supplier" v-model="order.supplier_id" class="form-select" :disabled="!suppliers.length">
          <option value="">Select a supplier</option>
          <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">{{ supplier.name }}</option>
        </select>
        <span v-if="errors.supplier_id" class="text-danger">{{ errors.supplier_id }}</span>
      </div>

      <!-- Product Selection -->
      <div class="mb-3">
        <label for="product" class="form-label">
          <i class="fas fa-box"></i> Product:
        </label>
        <select id="product" v-model="currentItem.product_id" @change="fetchProductUnits" class="form-select" :disabled="!products.length">
          <option value="">Select a product</option>
          <option v-for="product in products" :key="product.id" :value="product.id">{{ product.name }}</option>
        </select>
        <span v-if="errors.product_id" class="text-danger">{{ errors.product_id }}</span>
      </div>

      <!-- Unit Selection -->
      <div class="mb-3">
        <label for="unit" class="form-label">
          <i class="fas fa-ruler"></i> Unit:
        </label>
        <select id="unit" v-model="currentItem.unit_id" class="form-select" :disabled="!units.length">
          <option value="">Select a unit</option>
          <option v-for="unit in units" :key="unit.id" :value="unit.id">{{ unit.name }}</option>
        </select>
        <span v-if="errors.unit_id" class="text-danger">{{ errors.unit_id }}</span>
      </div>

      <!-- Quantity Input -->
      <div class="mb-3">
        <label for="quantity" class="form-label">
          <i class="fas fa-sort-numeric-up-alt"></i> Quantity:
        </label>
        <input type="number" id="quantity" v-model="currentItem.quantity" class="form-control" />
        <span v-if="errors.quantity" class="text-danger">{{ errors.quantity }}</span>
      </div>

      <!-- Buying Price Input -->
      <div class="mb-3">
        <label for="buying_price" class="form-label">
          <i class="fas fa-dollar-sign"></i> Buying Price:
        </label>
        <input type="number" id="buying_price" v-model="currentItem.buying_price" class="form-control" />
        <span v-if="errors.buying_price" class="text-danger">{{ errors.buying_price }}</span>
      </div>

      <!-- Selling Price Input -->
      <div class="mb-3">
        <label for="selling_price" class="form-label">
          <i class="fas fa-tags"></i> Selling Price:
        </label>
        <input type="number" id="selling_price" v-model="currentItem.selling_price" class="form-control" />
        <span v-if="errors.selling_price" class="text-danger">{{ errors.selling_price }}</span>
      </div>

      <button type="submit" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add Item
      </button>
    </form>

    <h3 class="mt-5">Order Items</h3>
    <table class="table table-striped">
      <thead>
      <tr>
        <th>Product</th>
        <th>Description</th>
        <th>Unit</th>
        <th>Quantity</th>
        <th>Buying Price</th>
        <th>Selling Price</th>
        <th>Actions</th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="(item, index) in order.items" :key="index">
        <td>{{ getProduct(item.product_id).name }}</td>
        <td>{{ getProduct(item.product_id).description }}</td>
        <td>{{ getUnit(item.unit_id).name }}</td>
        <td>{{ item.quantity }}</td>
        <td>{{ item.buying_price }}</td>
        <td>{{ item.selling_price }}</td>
        <td>
          <button class="btn btn-danger btn-sm" @click="removeItem(index)">
            <i class="fas fa-trash"></i> Remove
          </button>
        </td>
      </tr>
      </tbody>
    </table>

    <!-- Date Selection -->
    <div class="mb-3">
      <label for="date" class="form-label">
        <i class="fas fa-calendar-alt"></i> Date:
      </label>
      <input type="date" id="date" v-model="order.date" class="form-control" />
      <span v-if="errors.date" class="text-danger">{{ errors.date }}</span>
    </div>

    <!-- Submit Button -->
    <button type="button" class="btn btn-success" @click="handleSubmit">
      <i class="fas fa-save"></i> Submit Order
    </button>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      order: {
        supplier_id: '',  // Default empty value to avoid undefined error
        items: [],
        date: ''
      },
      currentItem: {
        product_id: '',
        unit_id: '',
        quantity: '',
        buying_price: '',
        selling_price: ''
      },
      suppliers: [],
      products: [],
      units: [],
      errors: {}
    };
  },
  created() {
    this.fetchSuppliers();
    this.fetchProducts();
  },
  methods: {
    async fetchSuppliers() {
      try {
        const response = await axios.get('/api/suppliers');
        this.suppliers = response.data;
      } catch (error) {
        console.error('Error fetching suppliers:', error);
      }
    },
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
        const response = await axios.get(`/api/products/${this.currentItem.product_id}/units`);
        this.units = response.data;
      } catch (error) {
        console.error('Error fetching units:', error);
      }
    },
    addItemToOrder() {
      if (this.validateItem()) {
        this.order.items.push({ ...this.currentItem });
        this.resetCurrentItem();
      }
    },
    removeItem(index) {
      this.order.items.splice(index, 1);
    },
    async handleSubmit() {
      if (this.validateOrder()) {
        try {
          const response = await axios.post('/api/inventory', this.order);
          this.$notify("Order registered successfully.");
          // Reset the form or show a success message
          this.order = {
            supplier_id: '',
            items: [],
            date: ''
          };
        } catch (error) {
          console.error('Error registering order:', error);
          if (error.response && error.response.data && error.response.data.errors) {
            this.errors = error.response.data.errors;
          }
        }
      }
    },
    validateItem() {
      this.errors = {};

      if (!this.currentItem.product_id) this.errors.product_id = 'Product is required.';
      if (!this.currentItem.unit_id) this.errors.unit_id = 'Unit is required.';
      if (!this.currentItem.quantity) this.errors.quantity = 'Quantity is required.';
      if (!this.currentItem.buying_price) this.errors.buying_price = 'Buying price is required.';
      if (!this.currentItem.selling_price) this.errors.selling_price = 'Selling price is required.';

      return Object.keys(this.errors).length === 0;
    },
    validateOrder() {
      this.errors = {};

      if (!this.order.supplier_id) this.errors.supplier_id = 'Supplier is required.';
      if (this.order.items.length === 0) this.errors.items = 'At least one item is required.';
      if (!this.order.date) this.errors.date = 'Date is required.';

      return Object.keys(this.errors).length === 0;
    },
    resetCurrentItem() {
      this.currentItem = {
        product_id: '',
        unit_id: '',
        quantity: '',
        buying_price: '',
        selling_price: ''
      };
    },
    getProduct(id) {
      return this.products.find(product => product.id === id) || {};
    },
    getUnit(id) {
      return this.units.find(unit => unit.id === id) || {};
    }
  }
};
</script>

<style>
@import 'bootstrap/dist/css/bootstrap.min.css';
</style>
