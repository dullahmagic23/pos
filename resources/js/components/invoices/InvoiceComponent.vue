<template>
  <div class="container mt-2 p-6 bg-white" id="invoiceContent">
    <div class="invoice-header">
      <div class="row">
        <div class="col-md-6">
          <h2>{{ companyName }}</h2>
          <p class="m-0 p-0" v-for="(line, index) in companyAddress" :key="index">{{ line }}</p>
        </div>
        <div class="col-md-6 text-end">
          <h2>Invoice</h2>
          <p><strong>Invoice Number:</strong> {{ formatInvoiceId(invoice.id) }}</p>
          <p><strong>Date Issued:</strong> {{ formatDate(invoice.created_at) }}</p>
        </div>
      </div>
    </div>

    <div class="invoice-details mt-4">
      <div class="row">
        <div class="col-md-6">
          <h4>Billed To</h4>
          <p>{{ invoice.sale?.customer?.name || 'N/A' }}<br>
            {{ invoice.sale?.customer?.address || 'N/A' }}<br>
            {{ invoice.sale?.customer?.phone || 'N/A' }}</p>
        </div>
        <div class="col-md-6 text-end">
          <h4>Amount Due</h4>
          <h2>{{ formatCurrency(totalAmount - totalPayments) }}</h2>
        </div>
      </div>
    </div>

    <div class="invoice-items mt-4">
      <table class="table table-striped">
        <thead>
        <tr>
          <th>Description</th>
          <th>Unit</th>
          <th>Quantity</th>
          <th>Price</th>
          <th>Total</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(product, index) in invoice.sale?.products || []" :key="index">
          <td>{{ product.description || 'N/A' }}</td>
          <td>{{ product.units.find(u=>u.id === product.pivot.unit_id).name }}</td>
          <td>{{ product.pivot?.quantity || 0 }}</td>
          <td>{{ formatCurrency(product.pivot?.price || 0) }}</td>
          <td>{{ formatCurrency((product.pivot?.quantity || 0) * (product.pivot?.price || 0)) }}</td>
        </tr>
        </tbody>
      </table>
    </div>

    <div class="invoice-summary mt-4">
      <div class="row">
        <div class="col-md-6">
          <h4>Notes</h4>
          <p>Thank you for your business!</p>
        </div>
        <div class="col-md-6 text-end">
          <p><strong>Subtotal:</strong> {{ formatCurrency(totalAmount) }}</p>
          <p><strong>Total Payments:</strong> {{ formatCurrency(totalPayments) }}</p>
          <h4><strong>Total Due:</strong> {{ formatCurrency(totalAmount - totalPayments) }}</h4>
        </div>
      </div>
    </div>

    <div class="invoice-actions mt-4">
      <button class="btn btn-danger" id="downloadBtn" @click="printInvoice" aria-label="Download PDF">
        <i class="fas fa-file-pdf"></i> Download PDF
      </button>
      <div v-if="loading" class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>
  </div>
</template>

<script>
import { jsPDF } from 'jspdf';
import html2canvas from 'html2canvas';

export default {
  props: {
    invoice: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      companyName: "Your Company",
      companyAddress: [
        "1234 Your Street",
        "City, California",
        "90210",
        "United States",
        "1-888-123-4567",
      ],
      loading: false,
    };
  },
  computed: {
    totalAmount() {
      return (
          this.invoice.sale?.products?.reduce(
              (total, product) =>
                  total +
                  (parseFloat(product.pivot?.quantity || 0) *
                      parseFloat(product.pivot?.price || 0)),
              0
          ) || 0
      );
    },
    totalPayments() {
      return (
          this.invoice.sale?.payments?.reduce(
              (total, payment) => total + parseFloat(payment.amount || 0),
              0
          ) || 0
      );
    },
  },
  methods: {
    async printInvoice() {
      const downloadBtn = document.getElementById('downloadBtn');
      this.loading = true;
      downloadBtn.style.display = 'none'; // Hide the button

      try {
        const invoiceContent = document.getElementById('invoiceContent');
        const canvas = await html2canvas(invoiceContent);
        const imgData = canvas.toDataURL('image/png');
        const doc = new jsPDF('p', 'mm', 'a4');
        const pdfWidth = doc.internal.pageSize.getWidth();
        const pdfHeight = (canvas.height * pdfWidth) / canvas.width;

        doc.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
        doc.save('invoice.pdf');
      } catch (error) {
        console.error("Error generating PDF:", error);
        alert("Failed to generate PDF. Please try again.");
      } finally {
        this.loading = false;
        downloadBtn.style.display = 'block'; // Show the button again
      }
    },
    formatCurrency(value) {
      return new Intl.NumberFormat('sw-TZ', { style: 'currency', currency: 'TZS' }).format(value);
    },
    formatDate(dateString) {
      return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
      });
    },
    formatInvoiceId(id) {
      return `INV-${id.toString().padStart(6, '0')}`;
    },
  },
};
</script>

<style scoped>
.container {
  width: 100%;
  background-color: white;
}
.table-striped tbody tr:nth-of-type(odd) {
  background-color: #f9f9f9;
}
.text-end {
  text-align: right;
}
@media print {
  .invoice-actions {
    display: none;
  }
}
</style>
