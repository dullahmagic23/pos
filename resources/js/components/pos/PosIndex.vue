<template>
  <div class="pos">
    <h2 class="text-center"><i class="fas fa-cash-register"></i> Point of Sale</h2>

    <!-- Customer Selection -->
    <div class="form-group">
      <label for="customer">Customer</label>
      <select v-model="selectedCustomer" id="customer" class="form-control" aria-label="Select Customer">
        <option value="" disabled>Select Customer</option>
        <option v-for="customer in customers" :key="customer.id" :value="customer.id">
          {{ customer.name }}
        </option>
      </select>
      <p v-if="errors.customer" class="text-danger">{{ errors.customer }}</p>
    </div>

    <!-- Loading Indicator for Customers -->
    <div v-if="isLoadingCustomers" class="loading-spinner">Loading Customers...</div>

    <!-- Product and Unit Selection -->
    <div class="row form-group">
      <div class="col-md-6">
        <label for="product">Product</label>
        <select v-model="selectedProduct" id="product" class="form-control" @change="getUnits" aria-label="Select Product">
          <option value="" disabled>Select Product</option>
          <option v-for="product in products" :key="product.id" :value="product.id">
            {{ product.name }} - {{ product.description }}
          </option>
        </select>
        <p v-if="errors.product" class="text-danger">{{ errors.product }}</p>
      </div>
      <div class="col-md-6">
        <label for="unit">Unit</label>
        <select v-model="selectedUnit" id="unit" class="form-control" @change="getPrice" aria-label="Select Unit">
          <option value="" disabled>Select Unit</option>
          <option v-for="unit in units" :key="unit.id" :value="unit.id">
            {{ unit.name }}
          </option>
        </select>
        <p v-if="errors.unit" class="text-danger">{{ errors.unit }}</p>
      </div>
    </div>

    <!-- Loading Indicator for Products -->
    <div v-if="isLoadingProducts" class="loading-spinner">Loading Products...</div>

    <!-- Price and Quantity Fields -->
    <div class="row form-group">
      <div class="col-md-6">
        <label for="price">Price</label>
        <input type="text" id="price" class="form-control" v-model="price" readonly />
      </div>
      <div class="col-md-6">
        <label for="quantity">Quantity</label>
        <input type="number" id="quantity" class="form-control" v-model="quantity" @input="updateTotal" aria-label="Enter Quantity" />
        <p v-if="errors.quantity" class="text-danger">{{ errors.quantity }}</p>
      </div>
    </div>

    <!-- Total Price -->
    <div class="form-group">
      <label for="total">Total</label>
      <input type="text" id="total" class="form-control" v-model="total" readonly />
    </div>

    <!-- Add to Order -->
    <button class="btn btn-primary" @click="addItemToOrder" :disabled="isAddButtonDisabled">Add to Order</button>

    <!-- Loading Indicator for Quantity -->
    <div v-if="isLoadingQuantity" class="loading-spinner">Checking Stock...</div>

    <!-- Order Table -->
    <table class="table mt-4 table-striped table-hover" v-if="orderItems.length > 0">
      <thead class="table-dark">
      <tr>
        <th>Product</th>
        <th>Unit</th>
        <th>Quantity</th>
        <th>Total</th>
        <th>Action</th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="(item, index) in orderItems" :key="index">
        <td>{{ item.product.name }}</td>
        <td>{{ item.unit.name }}</td>
        <td>{{ item.quantity }}</td>
        <td>{{ formatCurrency(item.total) }}</td>
        <td><button @click="removeItem(index)" class="btn btn-danger btn-sm">Remove</button></td>
      </tr>
      </tbody>
    </table>

    <!-- Clear Order -->
    <button v-if="orderItems.length > 0" class="btn btn-warning mt-2" @click="clearOrder">Clear Order</button>

    <!-- Submit Order -->
    <button v-if="orderItems.length > 0" class="btn btn-success mt-2" @click="submitOrder">Submit Order</button>

    <!-- General Error -->
    <p v-if="errors.general" class="text-danger">{{ errors.general }}</p>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      customers: [],
      products: [],
      units: [],
      selectedCustomer: '',
      selectedProduct: '',
      selectedUnit: '',
      price: 0,
      quantity: 1,
      total: 0,
      orderItems: [],
      errors: {},
      currentQuantity: 0,
      isLoadingCustomers: false,
      isLoadingProducts: false,
      isLoadingQuantity: false,
    };
  },
  computed: {
    isAddButtonDisabled() {
      return (
          !this.selectedCustomer ||
          !this.selectedProduct ||
          !this.selectedUnit ||
          !this.quantity ||
          this.quantity <= 0
      );
    },
  },
  methods: {
    formatCurrency(value) {
      const locale = 'sw-TZ'; // Set the locale
      const currency = 'TZS'; // Set the currency
      return new Intl.NumberFormat(locale, { style: 'currency', currency }).format(value);
    },
    fetchCustomers() {
      this.isLoadingCustomers = true;
      axios
          .get('/api/customers')
          .then((response) => {
            this.customers = response.data;
          })
          .catch((error) => {
            console.error('Error fetching customers:', error);
          })
          .finally(() => {
            this.isLoadingCustomers = false;
          });
    },
    fetchProducts() {
      this.isLoadingProducts = true;
      axios
          .get('/api/products')
          .then((response) => {
            this.products = response.data;
          })
          .catch((error) => {
            console.error('Error fetching products:', error);
          })
          .finally(() => {
            this.isLoadingProducts = false;
          });
    },
    getUnits() {
      if (!this.selectedProduct) return;
      const product = this.products.find((p) => p.id === this.selectedProduct);
      this.units = product ? product.units : [];
    },
    checkProductQuantity() {
      if (!this.selectedProduct || !this.selectedUnit) return;
      this.isLoadingQuantity = true;
      axios
          .get(`/api/pos/getQuantity/${this.selectedProduct}/${this.selectedUnit}`)
          .then((response) => {
            this.currentQuantity = response.data.quantity;
            this.price = response.data.price;
            if (this.quantity > this.currentQuantity) {
              this.errors.quantity = `Available quantity: ${this.currentQuantity}. You cannot order more than this.`;
            } else {
              this.errors.quantity = '';
            }
          })
          .catch((error) => {
            console.error('Error fetching product price:', error);
          })
          .finally(() => {
            this.isLoadingQuantity = false;
          });
    },
    getPrice() {
      this.checkProductQuantity();
    },
    updateTotal() {
      this.total = this.price * this.quantity;
      if (this.quantity > this.currentQuantity) {
        this.errors.quantity = `Available: ${this.currentQuantity}. Adjust your quantity.`;
      }
    },
    addItemToOrder() {
      if (
          this.selectedProduct &&
          this.selectedUnit &&
          this.quantity > 0 &&
          !this.errors.quantity
      ) {
        const product = this.products.find((p) => p.id === this.selectedProduct);
        const unit = this.units.find((u) => u.id === this.selectedUnit);

        this.orderItems.push({
          product,
          unit,
          quantity: this.quantity,
          price: this.price,
          total: this.price * this.quantity,
        });

        this.selectedProduct = '';
        this.selectedUnit = '';
        this.quantity = 1;
        this.price = 0;
        this.total = 0;
        this.errors = {};
      }
    },
    removeItem(index) {
      this.orderItems.splice(index, 1);
    },
    clearOrder() {
      this.orderItems = [];
    },
    submitOrder() {
      if (this.orderItems.length === 0) {
        this.errors.general = 'Cannot submit an empty order.';
        return;
      }

      const orderData = {
        customer_id: this.selectedCustomer,
        items: this.orderItems.map((item) => ({
          product_id: item.product.id,
          unit_id: item.unit.id,
          quantity: item.quantity,
          total: item.total,
          price: item.price,
        })),
      };

      axios
          .post('/api/pos', orderData)
          .then(() => {
            alert('Order submitted successfully!');
            this.clearOrder();
          })
          .catch((error) => {
            console.error('Error submitting order:', error);
            alert(
                `Error submitting order: ${
                    error.response ? error.response.data.message : 'Unknown error'
                }`
            );
          });
    },
    validateInputs() {
      this.errors = {};
      if (!this.selectedCustomer) this.errors.customer = 'Customer is required.';
      if (!this.selectedProduct) this.errors.product = 'Product is required.';
      if (!this.selectedUnit) this.errors.unit = 'Unit is required.';
      if (!this.quantity || this.quantity <= 0)
        this.errors.quantity = 'Quantity must be greater than 0.';
    },
  },
  created() {
    this.fetchCustomers();
    this.fetchProducts();
  },
};
</script>

<style scoped>
/* General Layout */
.pos {
  max-width: 100%;
  margin: 0 auto;
  padding: 2rem;
  background-color: #f9f9f9;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

/* Title */
h2 {
  margin-bottom: 2rem;
  color: #2c3e50;
  font-size: 2rem;
  font-weight: bold;
  text-align: center;
}

/* Form Labels */
label {
  font-weight: bold;
  color: #34495e;
}

/* Form Fields */
.form-control {
  border: 1px solid #ccc;
  border-radius: 4px;
  padding: 0.5rem;
  font-size: 1rem;
}

/* Error Messages */
.text-danger {
  font-size: 0.875rem;
  margin-top: 0.25rem;
}

/* Buttons */
button {
  margin-top: 1rem;
  font-size: 1rem;
  font-weight: bold;
  padding: 0.75rem 1rem;
  border-radius: 4px;
  transition: all 0.3s;
}

button.btn-primary {
  background-color: #3498db;
  border: none;
  color: white;
}

button.btn-primary:hover {
  background-color: #2980b9;
}

button.btn-danger {
  background-color: #e74c3c;
  border: none;
  color: white;
}

button.btn-danger:hover {
  background-color: #c0392b;
}

button.btn-warning {
  background-color: #f39c12;
  border: none;
  color: white;
}

button.btn-warning:hover {
  background-color: #e67e22;
}

button.btn-success {
  background-color: #2ecc71;
  border: none;
  color: white;
}

button.btn-success:hover {
  background-color: #27ae60;
}

/* Loading Spinner */
.loading-spinner {
  text-align: center;
  font-size: 1rem;
  color: #95a5a6;
}

/* Table */
table {
  width: 100%;
  border-collapse: collapse;
}

table thead {
  background-color: #2c3e50;
  color: white;
}

table tbody tr {
  border-bottom: 1px solid #ddd;
}

table tbody tr:hover {
  background-color: #f4f4f4;
}

.table th,
.table td {
  padding: 0.75rem;
  text-align: left;
}

.table th {
  font-weight: bold;
}
</style>

