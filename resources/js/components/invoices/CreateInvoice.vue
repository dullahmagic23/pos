<template>
  <div class="container mt-2">
    <h2 class="mb-4"><i class="fas fa-file-invoice"></i> Create Invoice</h2>

    <div v-if="loading" class="text-center my-3">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>

    <div v-else>
      <div class="mb-3">
        <label for="saleSelect" class="form-label">Select Sale:</label>
        <select
            id="saleSelect"
            v-model="selectedSaleId"
            @change="fetchSaleDetails"
            class="form-select"
        >
          <option value="" disabled>Select a sale...</option>
          <option v-for="sale in sales" :key="sale.id" :value="sale.id">
            Sale ID: {{ formatSaleId(sale.id) }} - {{ formatDate(sale.created_at) }}
          </option>
        </select>
      </div>

      <div v-if="error" class="alert alert-danger">{{ error }}</div>

      <div v-if="selectedSale">
        <div class="mb-3">
          <h4>Customer Details</h4>
          <p><strong>Name:</strong> {{ selectedSale.customer.name }}</p>
          <p><strong>Address:</strong> {{ selectedSale.customer.address }}</p>
          <p><strong>Phone:</strong> {{ selectedSale.customer.phone }}</p>
          <div v-if="selectedSale.invoice">
            <p><strong>Invoice Status:</strong> Invoice already exists</p>
            <a :href="`/invoices/show/${selectedSale.invoice.uuid}`" class="btn btn-info btn-sm">
              <i class="fas fa-eye"></i> View Invoice
            </a>
          </div>
          <div v-else>
            <p><strong>Invoice Status:</strong> No invoice yet</p>
          </div>
        </div>

        <div class="mb-3">
          <h4>Products</h4>
          <div class="table-responsive">
            <table class="table table-striped">
              <thead class="table-dark">
              <tr>
                <th>Product Name</th>
                <th>Description</th>
                <th>Unit</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="(product, index) in selectedSale.products" :key="index">
                <td>{{ product.name }}</td>
                <td>{{ product.description }}</td>
                <td>{{ product.units.find((u) => u.id === product.pivot.unit_id).name }}</td>
                <td>{{ product.pivot.quantity }}</td>
                <td>{{ formatCurrency(parseFloat(product.pivot.price)) }}</td>
                <td>{{ formatCurrency(parseFloat(product.pivot.quantity * product.pivot.price)) }}</td>
              </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="mb-3">
          <h4>Total Amount: {{ formatCurrency(parseFloat(totalAmount)) }}</h4>
        </div>

        <button
            class="btn btn-success"
            @click="generateInvoice"
            :disabled="selectedSale.invoice || generatingInvoice"
        >
          <i class="fas fa-check"></i>
          {{ generatingInvoice ? "Generating..." : "Generate Invoice" }}
        </button>

        <div v-if="invoiceGenerated" class="mt-5">
          <h3>Invoice</h3>
          <div id="invoiceContent">
            <p><strong>Customer Name:</strong> {{ selectedSale.customer.name }}</p>
            <p><strong>Invoice Date:</strong> {{ invoiceDate }}</p>
            <div class="table-responsive">
              <table class="table table-striped">
                <thead class="table-dark">
                <tr>
                  <th>Product Name</th>
                  <th>Description</th>
                  <th>Unit</th>
                  <th>Quantity</th>
                  <th>Price</th>
                  <th>Total</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(product, index) in selectedSale.products" :key="index">
                  <td>{{ product.name }}</td>
                  <td>{{ product.description }}</td>
                  <td>{{ product.units.find((u) => u.id === product.pivot.unit_id).name }}</td>
                  <td>{{ product.pivot.quantity }}</td>
                  <td>{{ formatCurrency(parseFloat(product.pivot.price)) }}</td>
                  <td>{{ formatCurrency(parseFloat(product.pivot.quantity * product.pivot.price)) }}</td>
                </tr>
                </tbody>
              </table>
            </div>
            <h4>Total Amount: {{ formatCurrency(parseFloat(totalAmount)) }}</h4>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import { Toast } from "bootstrap";

export default {
  data() {
    return {
      sales: [],
      selectedSaleId: "",
      selectedSale: null,
      invoiceDate: new Date().toISOString().substr(0, 10),
      invoiceGenerated: false,
      loading: false,
      generatingInvoice: false,
      error: null,
    };
  },
  computed: {
    totalAmount() {
      return this.selectedSale
          ? this.selectedSale.products.reduce(
              (total, product) => total + parseFloat(product.pivot.quantity) * parseFloat(product.pivot.price),
              0
          )
          : 0;
    },
  },
  created() {
    this.fetchSales();
  },
  methods: {
    async fetchSales() {
      this.loading = true;
      this.error = null;
      try {
        const response = await axios.get("/api/sales");
        this.sales = response.data;
      } catch (error) {
        this.error = "Error fetching sales data. Please try again.";
        console.error("Error fetching sales:", error);
      } finally {
        this.loading = false;
      }
    },
    async fetchSaleDetails() {
      if (!this.selectedSaleId) return;
      this.loading = true;
      this.error = null;
      try {
        const response = await axios.get(`/api/sales/${this.selectedSaleId}`);
        this.selectedSale = response.data;
      } catch (error) {
        this.error = "Error fetching sale details. Please try again.";
        console.error("Error fetching sale details:", error);
      } finally {
        this.loading = false;
      }
    },
    async generateInvoice() {
      this.generatingInvoice = true;
      this.error = null;
      try {
        const response = await axios.post("/api/invoices", {sale: this.selectedSale});
        this.invoiceGenerated = true;
        this.selectedSale.invoice = response.data;
        await this.fetchSaleDetails(); // Fetch sale details again to update the invoice URL
        this.showToast("Invoice generated successfully!", "success");
      } catch (error) {
        this.error = "Error generating invoice. Please try again.";
        console.error("Error generating invoice:", error);
      } finally {
        this.generatingInvoice = false;
      }
    },
    formatCurrency(value) {
      return new Intl.NumberFormat("sw-TZ", {
        style: "currency",
        currency: "TZS",
      }).format(value);
    },
    formatDate(dateString) {
      const options = {year: "numeric", month: "long", day: "numeric"};
      return new Date(dateString).toLocaleDateString("en-US", options);
    },
    showToast(message, type) {
      const toast = document.createElement("div");
      toast.className = `toast align-items-center text-white bg-${type} border-0 position-fixed bottom-0 end-0 m-3`;
      toast.role = "alert";
      toast.style.zIndex = 1050;
      toast.innerHTML = `
        <div class="d-flex">
          <div class="toast-body">${message}</div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>`;
      document.body.appendChild(toast);
      new Toast(toast).show();
      setTimeout(() => toast.remove(), 5000);
    },
      formatSaleId(id) {
          return id.toString().padStart(5, '0');
      },
  },
};
</script>

<style scoped>
.container {
  width: 80%;
  padding: 20px;
}

.table-dark {
  background-color: #343a40;
  color: white;
}

.table-responsive {
  overflow-x: auto;
}

.spinner-border {
  width: 3rem;
  height: 3rem;
}
</style>
