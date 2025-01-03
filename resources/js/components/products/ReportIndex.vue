<template>
  <div class="container mt-5">
    <h2 class="text-center mb-4"><i class="fas fa-chart-bar"></i> Sales Report</h2>

    <!-- Filters Section -->
    <div class="row mb-4">
      <div class="col-md-3">
        <label for="startDate" class="form-label"><i class="fas fa-calendar-alt"></i> Start Date</label>
        <input type="date" id="startDate" v-model="startDate" class="form-control" />
      </div>
      <div class="col-md-3">
        <label for="endDate" class="form-label"><i class="fas fa-calendar-alt"></i> End Date</label>
        <input type="date" id="endDate" v-model="endDate" class="form-control" />
      </div>
      <div class="col-md-3">
        <label for="customer" class="form-label"><i class="fas fa-user"></i> Customer</label>
        <select id="customer" v-model="selectedCustomer" class="form-select">
          <option value="">All Customers</option>
          <option v-for="customer in customers" :key="customer.id" :value="customer.id">{{ customer.name }}</option>
        </select>
      </div>
      <div class="col-md-3 d-flex align-items-end">
        <button class="btn btn-primary w-100" @click="applyFilters"><i class="fas fa-filter"></i> Apply Filters</button>
      </div>
    </div>

    <!-- Export Buttons -->
    <div class="d-flex justify-content-end mb-4">
      <button class="btn btn-success me-2" @click="exportToExcel"><i class="fas fa-file-excel"></i> Export to Excel</button>
      <button class="btn btn-danger" @click="exportToPDF"><i class="fas fa-file-pdf"></i> Export to PDF</button>
    </div>

    <!-- Sales Table -->
    <table class="table table-bordered table-hover">
      <thead class="table-dark">
      <tr>
        <th>Sale ID</th>
        <th>Customer Name</th>
        <th>Product Name</th>
        <th>Unit</th>
        <th>Quantity</th>
        <th>Price</th>
        <th>Total</th>
        <th>Amount Paid</th>
        <th>Date</th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="sale in filteredSales" :key="sale.id">
        <td>{{ formatSaleId(sale.id) }}</td>
        <td>{{ sale.customer.name }}</td>
        <td>
          <ul class="list-unstyled">
            <li v-for="product in sale.products" :key="product.id">{{ product.name }}</li>
          </ul>
        </td>
        <td>
          <ul class="list-unstyled">
            <li v-for="product in sale.products" :key="product.id">{{ product.units.find(unit => unit.id === product.pivot.unit_id)?.name }}</li>
          </ul>
        </td>
        <td>
          <ul class="list-unstyled">
            <li v-for="product in sale.products" :key="product.id">{{ product.pivot.quantity }}</li>
          </ul>
        </td>
        <td>
          <ul class="list-unstyled">
            <li v-for="product in sale.products" :key="product.id">{{ formatCurrency(product.pivot.price) }}</li>
          </ul>
        </td>
        <td>{{ formatCurrency(sale.products.reduce((total, product) => total + parseFloat(product.pivot.total), 0)) }}</td>
        <td>{{ formatCurrency(calculateTotalPaid(sale.payments)) }}</td>
        <td>{{ formatDate(sale.created_at) }}</td>
      </tr>
      </tbody>
      <tfoot class="table-light">
      <tr>
        <td colspan="6"><strong>Total Sales</strong></td>
        <td><strong>{{ formatCurrency(totalSales) }}</strong></td>
        <td><strong>{{ formatCurrency(totalAmountPaid) }}</strong></td>
        <td></td>
      </tr>
      </tfoot>
    </table>
  </div>
</template>

<script>
import axios from 'axios';
import * as XLSX from 'xlsx';
import { saveAs } from 'file-saver';
import jsPDF from 'jspdf';
import 'jspdf-autotable';

export default {
  data() {
    return {
      sales: [],
      customers: [],
      startDate: '',
      endDate: '',
      selectedCustomer: '',
    };
  },
  created() {
    this.fetchSales();
    this.fetchCustomers();
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
    async fetchCustomers() {
      try {
        const response = await axios.get('/api/customers');
        this.customers = response.data;
      } catch (error) {
        console.error('Error fetching customers:', error);
      }
    },
    formatSaleId(id) {
      return id.toString().padStart(5, '0');
    },
    formatCurrency(value) {
      return new Intl.NumberFormat('sw-TZ', { style: 'currency', currency: 'TZS' }).format(value);
    },
    formatDate(dateString) {
      const options = { year: 'numeric', month: 'numeric', day: 'numeric' };
      return new Date(dateString).toLocaleDateString('en-US', options);
    },
    applyFilters() {
      this.fetchSales(); // Call fetchSales again to apply filters with the current date range and customer ID
    },
    calculateTotalPaid(payments) {
      return payments.reduce((total, payment) => total + parseFloat(payment.amount), 0);
    },
    exportToExcel() {
      const wsData = this.filteredSales.map(sale => ({
        'Sale ID': this.formatSaleId(sale.id),
        'Customer Name': sale.customer.name,
        'Product Name': sale.products.map(product => product.name).join(', '),
        'Unit': sale.products.map(product => product.units.find(unit => unit.id === product.pivot.unit_id)?.name).join(', '),
        'Quantity': sale.products.map(product => product.pivot.quantity).join(', '),
        'Price': sale.products.map(product => this.formatCurrency(product.pivot.price)).join(', '),
        'Total': this.formatCurrency(sale.products.reduce((total, product) => total + parseFloat(product.pivot.total), 0)),
        'Amount Paid': this.formatCurrency(this.calculateTotalPaid(sale.payments)),
        'Date': this.formatDate(sale.created_at)
      }));

      // Adding totals to the Excel
      wsData.push({
        'Sale ID': 'TOTALS',
        'Customer Name': '',
        'Product Name': '',
        'Unit': '',
        'Quantity': '',
        'Price': '',
        'Total': this.formatCurrency(this.totalSales),
        'Amount Paid': this.formatCurrency(this.totalAmountPaid),
        'Date': ''
      });

      const ws = XLSX.utils.json_to_sheet(wsData);
      const wb = XLSX.utils.book_new();
      XLSX.utils.book_append_sheet(wb, ws, 'Sales Report');
      const wbout = XLSX.write(wb, { bookType: 'xlsx', type: 'array' });
      saveAs(new Blob([wbout], { type: 'application/octet-stream' }), 'sales_report.xlsx');
    },
    exportToPDF() {
      const doc = new jsPDF('landscape');
      const columns = ['Sale ID', 'Customer Name', 'Product Name', 'Unit', 'Quantity', 'Price', 'Total', 'Amount Paid', 'Date'];
      const rows = this.filteredSales.map(sale => [
        this.formatSaleId(sale.id),
        sale.customer.name,
        sale.products.map(product => product.name).join(', '),
        sale.products.map(product => product.units.find(unit => unit.id === product.pivot.unit_id)?.name).join(', '),
        sale.products.map(product => product.pivot.quantity).join(', '),
        sale.products.map(product => this.formatCurrency(product.pivot.price)).join(', '),
        this.formatCurrency(sale.products.reduce((total, product) => total + parseFloat(product.pivot.total), 0)),
        this.formatCurrency(this.calculateTotalPaid(sale.payments)),
        this.formatDate(sale.created_at)
      ]);

      // Adding totals to the PDF
      rows.push([
        'TOTALS',
        '',
        '',
        '',
        '',
        '',
        this.formatCurrency(this.totalSales),
        this.formatCurrency(this.totalAmountPaid),
        ''
      ]);

      doc.autoTable(columns, rows);
      doc.save('sales_report.pdf');
    }
  },
  computed: {
    filteredSales() {
      return this.sales.filter(sale => {
        const saleDate = new Date(sale.created_at);
        const matchesCustomer = !this.selectedCustomer || sale.customer.id === parseInt(this.selectedCustomer);
        const matchesStartDate = !this.startDate || saleDate >= new Date(this.startDate);
        const matchesEndDate = !this.endDate || saleDate <= new Date(this.endDate);
        return matchesCustomer && matchesStartDate
      });
    },
    totalSales() {
      return this.filteredSales.reduce((total, sale) => total + sale.products.reduce((subTotal, product) => subTotal + parseFloat(product.pivot.total), 0), 0);
    },
    totalAmountPaid() {
      return this.filteredSales.reduce((total, sale) => total + this.calculateTotalPaid(sale.payments), 0);
    }
  }
};
</script>

<style>
.container {
  background-color: #f8f9fa;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
}
</style>
