<template>
  <div class="container-fluid mt-2">
    <div class="card shadow-sm">
      <div class="card-header bg-success text-white">
        <h5><i class="fas fa-money-bill-wave"></i> Sales Payments</h5>
      </div>
      <div class="card-body">
        <!-- Search Input -->
        <div class="mb-3">
          <label for="search" class="form-label">
            <i class="fas fa-search"></i> Search by Customer Name:
          </label>
          <input type="text" id="search" v-model="searchQuery" @input="debounceSearch" class="form-control" placeholder="Enter customer name" />
        </div>

        <!-- Date Filters -->
        <div class="row mb-3">
          <div class="col-md-4">
            <label for="startDate" class="form-label">
              <i class="fas fa-calendar-alt"></i> Start Date:
            </label>
            <input type="date" id="startDate" v-model="startDate" @change="filterPayments" class="form-control" />
          </div>
          <div class="col-md-4">
            <label for="endDate" class="form-label">
              <i class="fas fa-calendar-alt"></i> End Date:
            </label>
            <input type="date" id="endDate" v-model="endDate" @change="filterPayments" class="form-control" />
          </div>
          <div class="col-md-4 d-flex align-items-end">
            <button @click="toggleSortOrder" class="btn btn-primary w-100">
              Sort by Date: {{ sortOrder === 'asc' ? 'Ascending' : 'Descending' }}
            </button>
          </div>
        </div>

        <!-- Export Buttons -->
        <div class="d-flex justify-content-end mb-4">
          <button class="btn btn-success me-2" @click="exportToExcel"><i class="fas fa-file-excel"></i> Export to Excel</button>
          <button class="btn btn-danger me-2" @click="exportToPDF"><i class="fas fa-file-pdf"></i> Export to PDF</button>
        </div>

        <!-- Loading Spinner -->
        <div v-if="isLoading" class="text-center">
          <div class="spinner-border text-success" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
        </div>

        <!-- Payments Table -->
        <div v-if="filteredPayments && Object.keys(filteredPayments).length && !isLoading">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead class="table-light">
              <tr>
                <th>Sale ID</th>
                <th>Customer</th>
                <th>Total Payment</th>
                <th>Payment Method</th>
                <th>Date</th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="(payments, saleId) in paginatedPayments" :key="saleId">
                <td>{{ formatSaleId(saleId) }}</td>
                <td>{{ payments[0].customer?.name || 'Unknown' }}</td>
                <td>{{ formatCurrency(totalPaymentAmount(payments)) }}</td>
                <td>{{payments[0].payment_method.name  }}</td>
                <td>{{ formatDate(payments[0].payment_date) }}</td>
              </tr>
              </tbody>
            </table>
          </div>

          <!-- Total Payments -->
          <p class="fw-bold mt-4">Total Payments: {{ formatCurrency(totalPayments) }}</p>

          <!-- Pagination Controls -->
          <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
              <li class="page-item" :class="{ disabled: currentPage === 1 }">
                <button class="page-link" @click="changePage(1)">First</button>
              </li>
              <li class="page-item" :class="{ disabled: currentPage === 1 }">
                <button class="page-link" @click="changePage(currentPage - 1)">Previous</button>
              </li>
              <li class="page-item" v-for="page in totalPages" :key="page" :class="{ active: currentPage === page }">
                <button class="page-link" @click="changePage(page)">{{ page }}</button>
              </li>
              <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                <button class="page-link" @click="changePage(currentPage + 1)">Next</button>
              </li>
              <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                <button class="page-link" @click="changePage(totalPages)">Last</button>
              </li>
            </ul>
          </nav>
        </div>

        <!-- No Data Message -->
        <div v-else>
          <p>No payments found. <i class="fas fa-exclamation-circle"></i></p>
        </div>
      </div>
    </div>
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
      payments: [],
      groupedPayments: {},
      searchQuery: '',
      startDate: '',
      endDate: '',
      filteredPayments: {},
      totalPayments: 0,
      sortOrder: 'asc',
      currentPage: 1,
      itemsPerPage: 10,
      isLoading: false,
      debounceTimer: null,
    };
  },
  computed: {
    totalPages() {
      return Math.ceil(Object.keys(this.filteredPayments).length / this.itemsPerPage);
    },
    paginatedPayments() {
      const start = (this.currentPage - 1) * this.itemsPerPage;
      const end = start + this.itemsPerPage;
      const entries = Object.entries(this.filteredPayments).slice(start, end);

      // Sort the payments within each sale group
      const sortedEntries = entries.map(([saleId, payments]) => {
        return [saleId, this.sortedPayments(payments)];
      });

      return Object.fromEntries(sortedEntries);
    },
  },
  created() {
    this.fetchPayments();
  },
  methods: {
    async fetchPayments() {
      this.isLoading = true;
      try {
        const response = await axios.get('/api/payments');
        this.payments = Array.isArray(response.data) ? response.data : Object.values(response.data).flat();
        this.groupPaymentsBySaleId();
        this.filterPayments();
      } catch (error) {
        console.error('Error fetching payments:', error);
      } finally {
        this.isLoading = false;
      }
    },
    groupPaymentsBySaleId() {
      this.groupedPayments = this.payments.reduce((grouped, payment) => {
        const saleId = payment.sale_id;
        if (!grouped[saleId]) {
          grouped[saleId] = [];
        }
        grouped[saleId].push(payment);
        return grouped;
      }, {});
    },
    filterPayments() {
      const query = this.searchQuery.toLowerCase();
      const start = this.startDate ? new Date(this.startDate) : null;
      const end = this.endDate ? new Date(this.endDate) : null;
      let total = 0;

      this.filteredPayments = Object.entries(this.groupedPayments).reduce((filtered, [saleId, payments]) => {
        const matchingPayments = payments.filter(payment => {
          const matchesQuery = payment.customer?.name?.toLowerCase().includes(query);
          const paymentDate = new Date(payment.payment_date);
          const withinDateRange = (!start || paymentDate >= start) && (!end || paymentDate <= end);
          return matchesQuery && withinDateRange;
        });
        if (matchingPayments.length > 0) {
          filtered[saleId] = matchingPayments;
          total += matchingPayments.reduce((sum, payment) => sum + parseFloat(payment.amount), 0);
        }
        return filtered;
      }, {});

      this.totalPayments = total;
      this.currentPage = 1; // Reset to the first page after filtering
    },
    sortedPayments(payments) {
      return payments.slice().sort((a, b) => {
        const dateA = new Date(a.payment_date);
        const dateB = new Date(b.payment_date);
        return this.sortOrder === 'asc' ? dateA - dateB : dateB - dateA;
      });
    },
    toggleSortOrder() {
      this.sortOrder = this.sortOrder === 'asc' ? 'desc' : 'asc';
      this.filterPayments(); // Re-filter payments to apply the sorting order
    },
    changePage(page) {
      if (page >= 1 && page <= this.totalPages) {
        this.currentPage = page;
      }
    },
    formatSaleId(id) {
      return id.toString().padStart(5, '0');
    },
    formatCurrency(value) {
      return new Intl.NumberFormat('sw-TZ', { style: 'currency', currency: 'TZS' }).format(value);
    },
    formatDate(dateString) {
      const options = { year: 'numeric', month: 'long', day: 'numeric' };
      return new Date(dateString).toLocaleDateString('en-US', options);
    },
    totalPaymentAmount(payments) {
      return payments.reduce((sum, payment) => sum + parseFloat(payment.amount), 0);
    },
    getSaleTotal(saleId) {
      const sale = this.payments.find(payment => payment.sale_id === saleId);
      return this.getTotal(saleId) // Assuming each sale has a unique sale_id and the 'amount' field represents the total
    },

    getTotal(saleId){
      axios.get(`/api/sales/total/${saleId}`)
          .then(resp=>{
            return parseFloat(resp.data)
          })
    },
    totalAmountDue(payments, saleId) {
      const saleTotal = this.getSaleTotal(saleId);
      return saleTotal - this.totalPaymentAmount(payments);
    },
    getPaymentStatus(payments, saleId) {
      const saleTotal = this.getSaleTotal(saleId);
      const totalPayment = this.totalPaymentAmount(payments);
      return totalPayment >= saleTotal ? 'Complete' : 'Incomplete';
    },
    // Export to Excel
    exportToExcel() {
      const wsData = Object.entries(this.filteredPayments).flatMap(([saleId, payments]) =>
          payments.map(payment => ({
            'Sale ID': this.formatSaleId(saleId),
            'Customer Name': payment.customer?.name || 'Unknown',
            'Total Payment': this.formatCurrency(this.totalPaymentAmount(payments)),
            'Payment Method': payment.payment_method.name,
            'Date': this.formatDate(payment.payment_date)
          }))
      );
      const ws = XLSX.utils.json_to_sheet(wsData);
      const wb = XLSX.utils.book_new();
      XLSX.utils.book_append_sheet(wb, ws, 'Sales Payments');
      const wbout = XLSX.write(wb, { bookType: 'xlsx', type: 'array' });
      saveAs(new Blob([wbout], { type: 'application/octet-stream' }), 'sales-payments.xlsx');
    },
    // Export to PDF
    exportToPDF() {
      const doc = new jsPDF();
      const tableData = Object.entries(this.filteredPayments).flatMap(([saleId, payments]) =>
          payments.map(payment => [
            this.formatSaleId(saleId),
            payment.customer?.name || 'Unknown',
            this.formatCurrency(this.totalPaymentAmount(payments)),
            payment.payment_method.name,
            this.formatDate(payment.payment_date)
          ])
      );

      doc.autoTable({
        head: [
          ['Sale ID', 'Customer Name', 'Total Payment', 'Payment Method', 'Date']
        ],
        body: tableData
      });

      doc.save('sales-payments.pdf');
    },
    // Debounce search query
    debounceSearch() {
      clearTimeout(this.debounceTimer);
      this.debounceTimer = setTimeout(this.filterPayments, 300);
    }
  }
};
</script>


<style scoped>
.container-fluid {
  width: 100%;
}

.card {
  border-radius: 10px;
  overflow: hidden;
}

.card-header {
  background-color: #28a745;
  border-bottom: 1px solid #e3e3e3;
  color: white;
}

.card-body {
  background-color: #fff;
}

.table {
  margin-top: 20px;
}

.table-bordered th,
.table-bordered td {
  border: 1px solid #dee2e6;
}

.table thead th {
  background-color: #f8f9fa;
}

.table-hover tbody tr:hover {
  background-color: #f1f1f1;
}

.shadow-sm {
  box-shadow: 0 .125rem .25rem rgba(0, 0, 0, 0.075) !important;
}

.fw-bold {
  font-weight: bold;
}

.pagination {
  margin-top: 20px;
}

.page-item.active .page-link {
  background-color: #28a745;
  border-color: #28a745;
}

.page-link {
  color: #28a745;
}

.page-link:hover {
  background-color: #e9ecef;
}
</style>
