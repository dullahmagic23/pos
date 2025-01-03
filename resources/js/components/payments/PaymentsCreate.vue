<template>
  <div class="container-fluid mt-2">
    <div class="card shadow-sm">
      <div class="card-header bg-primary text-white">
        <h5><i class="fas fa-money-check-alt"></i> Accept Payment</h5>
      </div>
      <div class="card-body">
        <!-- Sale Selection -->
        <div class="mb-3">
          <label for="sale" class="form-label">
            <i class="fas fa-receipt"></i> Select Sale:
          </label>
          <select v-model="selectedSale" @change="fetchSaleDetails" id="sale" class="form-select" aria-label="Sale selection">
            <option value="" disabled>Select Sale</option>
            <option v-for="sale in sales" :key="sale.id" :value="sale.id">
              Sale ID: {{ formatSaleId(sale.id) }} - {{ sale.customer?.name || 'Unknown Customer' }}
            </option>
          </select>
          <span v-if="errors.sale" class="text-danger">{{ errors.sale }}</span>
        </div>

        <!-- Sale Information -->
        <div v-if="saleDetails" class="mb-3">
          <h6>Sale Information</h6>
          <p><strong>Customer:</strong> {{ saleDetails.customer?.name || 'Unknown' }}</p>
          <table class="table table-bordered table-hover">
            <thead class="table-light">
            <tr>
              <th>Name</th>
              <th>Description</th>
              <th>Unit</th>
              <th>Price per Unit</th>
              <th>Quantity</th>
              <th>Amount</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="product in saleDetails.products" :key="product.id">
              <td>{{ product.name }}</td>
              <td>{{ product.description }}</td>
              <td>{{ product.units.find(unit => unit.id === product.pivot.unit_id)?.name }}</td>
              <td>{{ formatCurrency(product.pivot.price) }}</td>
              <td>{{ product.pivot.quantity }}</td>
              <td>{{ formatCurrency(product.pivot.total) }}</td>
            </tr>
            </tbody>
          </table>
          <p class="fw-bold"><strong>Total Amount:</strong> {{ formatCurrency(calculateTotal(saleDetails.products)) }}</p>
          <p class="fw-bold"><strong>Amount Paid:</strong> {{ formatCurrency(calculateTotalPaid(saleDetails.payments)) }}</p>
          <p v-if="isFullyPaid" class="text-success"><strong>This sale is fully paid for.</strong></p>
        </div>

        <!-- Payment Form -->
        <form @submit.prevent="handlePayment" v-if="!isFullyPaid">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="amount" class="form-label">
                <i class="fas fa-dollar-sign"></i> Amount:
              </label>
              <input type="number" id="amount" v-model="payment.amount" class="form-control" aria-label="Payment Amount" />
              <span v-if="errors.amount" class="text-danger">{{ errors.amount }}</span>
            </div>
            <div class="col-md-6 mb-3">
              <label for="paymentMethod" class="form-label">
                <i class="fas fa-credit-card"></i> Payment Method:
              </label>
              <select v-model="payment.method" id="paymentMethod" class="form-select" aria-label="Payment Method">
                <option value="" disabled>Select Payment Method</option>
                <option v-for="method in paymentMethods" :key="method.id" :value="method.id">
                  {{ method.name }}
                </option>
              </select>
              <span v-if="errors.method" class="text-danger">{{ errors.method }}</span>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="status" class="form-label">
                <i class="fas fa-check-circle"></i> Payment Status:
              </label>
              <select v-model="payment.status" id="status" class="form-select" aria-label="Payment Status">
                <option value="" disabled>Select Payment Status</option>
                <option value="full">Full</option>
                <option value="partial">Partial</option>
              </select>
              <span v-if="errors.status" class="text-danger">{{ errors.status }}</span>
            </div>
            <div class="col-md-6 mb-3">
              <label for="paymentDate" class="form-label">
                <i class="fas fa-calendar-alt"></i> Payment Date:
              </label>
              <input type="date" id="paymentDate" v-model="payment.date" class="form-control" aria-label="Payment Date" />
              <span v-if="errors.date" class="text-danger">{{ errors.date }}</span>
            </div>
          </div>

          <button type="submit" class="btn btn-primary w-100" :disabled="isSubmitting">
            <i class="fas fa-save"></i> Submit Payment
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      sales: [],
      selectedSale: '',
      saleDetails: null,
      paymentMethods: [],
      payment: {
        amount: '',
        method: '',
        status: '',
        date: ''
      },
      errors: {},
      isSubmitting: false // Track form submission state
    };
  },
  computed: {
    isFullyPaid() {
      return (
          this.saleDetails &&
          this.calculateTotal(this.saleDetails.products) === this.calculateTotalPaid(this.saleDetails.payments)
      );
    }
  },
  created() {
    this.fetchSales();
    this.fetchPaymentMethods();
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
    async fetchSaleDetails() {
      if (!this.selectedSale) return;
      try {
        const response = await axios.get(`/api/sales/${this.selectedSale}`);
        this.saleDetails = response.data;
      } catch (error) {
        console.error('Error fetching sale details:', error);
      }
    },
    async fetchPaymentMethods() {
      try {
        const response = await axios.get('/api/payment_methods');
        this.paymentMethods = response.data;
      } catch (error) {
        console.error('Error fetching payment methods:', error);
      }
    },
    calculateTotal(products) {
      return products.reduce((total, product) => total + parseFloat(product.pivot.total), 0);
    },
    calculateTotalPaid(payments) {
      return payments.reduce((total, payment) => total + parseFloat(payment.amount), 0);
    },
    formatSaleId(id) {
      return id.toString().padStart(5, '0');
    },
    async handlePayment() {
      if (this.validateForm()) {
        this.isSubmitting = true; // Start submission
        if (confirm('Are you sure you want to accept this payment?')) {
          try {
            const response = await axios.post('/api/payments', {
              sale_id: this.selectedSale,
              amount: this.payment.amount,
              method_id: this.payment.method,
              status: this.payment.status,
              date: this.payment.date
            });
            alert(response.data.message);
            this.resetForm();
            this.fetchSaleDetails(); // Refresh sale details to show updated payments
          } catch (error) {
            console.error('Error accepting payment:', error);
            alert('An error occurred! ' + error.response.data.message);
          } finally {
            this.isSubmitting = false; // Reset submission state
          }
        }
      }
    },
    validateForm() {
      this.errors = {};
      if (!this.selectedSale) this.errors.sale = 'Please select a sale.';
      if (!this.payment.amount) this.errors.amount = 'Amount is required.';
      if (!this.payment.method) this.errors.method = 'Payment method is required.';
      if (!this.payment.status) this.errors.status = 'Payment status is required.';
      if (!this.payment.date) this.errors.date = 'Payment date is required.';
      return Object.keys(this.errors).length === 0;
    },
    resetForm() {
      this.payment = {
        amount: '',
        method: '',
        status: '',
        date: ''
      };
      this.selectedSale = '';
      this.saleDetails = null;
      this.errors = {};
    },
    formatCurrency(value) {
      return new Intl.NumberFormat('sw-TZ', { style: 'currency', currency: 'TZS' }).format(value);
    }
  }
};
</script>

<style scoped>
/* Styling for improved UX */
.container-fluid {
  width: 100%;
}

.card {
  border-radius: 10px;
  overflow: hidden;
}

</style>