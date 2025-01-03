<template>
  <div class="container">
    <h4 class="mb-4"><i class="fas fa-money-check-alt"></i> Latest Payments</h4>

    <table class="table table-striped">
      <thead class="table-dark">
      <tr>
        <th>Payment ID</th>
        <th>Customer Name</th>
        <th>Amount</th>
        <th>Payment Method</th>
        <th>Date</th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="payment in latestPayments" :key="payment.id">
        <td>{{ formatPaymentId(payment.id) }}</td>
        <td>{{ payment.customer.name }}</td>
        <td>{{ formatCurrency(payment.amount) }}</td>
        <td>{{ payment.payment_method.name }}</td>
        <td>{{ formatDate(payment.created_at) }}</td>
      </tr>
      <tr v-if="latestPayments.length === 0">
        <td colspan="6" class="text-center">No payments found.</td>
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
      payments: [],
      searchQuery: '',
    };
  },
  computed: {
    filteredPayments() {
      return this.payments.filter(payment =>
          payment.customer.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
          payment.id.toString().includes(this.searchQuery)
      );
    },
    latestPayments() {
      return this.filteredPayments.slice(0, 3);
    },
  },
  created() {
    this.fetchPayments();
  },
  methods: {
    async fetchPayments() {
      try {
        const response = await axios.get('/api/payments');
        this.payments = response.data.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
      } catch (error) {
        console.error('Error fetching payments:', error);
      }
    },
    formatPaymentId(id) {
      return id.toString().padStart(6, '0'); // Format to 6 digits with leading zeros
    },
    formatCurrency(value) {
      return new Intl.NumberFormat('sw-TZ', { style: 'currency', currency: 'TZS' }).format(value);
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
