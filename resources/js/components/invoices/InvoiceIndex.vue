<template>
  <div class="container mt-2">
    <h2 class="mb-4"><i class="fas fa-file-alt"></i> Invoices</h2>
    <table class="table table-striped">
      <thead class="table-dark">
      <tr>
        <th>Invoice Number</th>
        <th>Customer</th>
        <th>Date Created</th>
        <th>Created By</th>
        <th>Actions</th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="invoice in invoices" :key="invoice.id">
        <td>{{ formatInvoiceId(invoice.id) }}</td>
        <td>{{ invoice.sale.customer.name }}</td>
        <td>{{ formatDate(invoice.created_at) }}</td>
        <td>{{ invoice.user.name }}</td>
        <td>
          <a :href="`/invoices/show/${invoice.uuid}`">
            <button class="btn btn-info btn-sm">
              <i class="fas fa-eye"></i> View
            </button>
          </a>
        </td>
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
      invoices: [],
    };
  },
  created() {
    this.fetchInvoices();
  },
  methods: {
    async fetchInvoices() {
      try {
        const response = await axios.get('/api/invoices');
        this.invoices = response.data;
      } catch (error) {
        console.error('Error fetching invoices:', error);
      }
    },
    formatDate(dateString) {
      const options = { year: 'numeric', month: 'long', day: 'numeric' };
      return new Date(dateString).toLocaleDateString('en-US', options);
    },
    formatInvoiceId(id) {
      return `INV-${id.toString().padStart(6, '0')}`; // Example: INV-000123
    }
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
</style>
