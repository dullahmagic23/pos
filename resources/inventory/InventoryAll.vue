<template>
  <div class="container mt-5">
    <h2 class="mb-4"><i class="fas fa-warehouse"></i> Stock List</h2>
    <div class="mb-3">
      <label for="search" class="form-label"><i class="fas fa-search"></i> Search:</label>
      <input type="text" id="search" v-model="searchQuery" class="form-control" placeholder="Search by product name" />
    </div>

    <!-- Export Buttons -->
    <div class="d-flex justify-content-end mb-4">
      <button class="btn btn-success me-2" @click="exportToExcel"><i class="fas fa-file-excel"></i> Export to Excel</button>
      <button class="btn btn-danger me-2" @click="exportToPDF"><i class="fas fa-file-pdf"></i> Export to PDF</button>
      <button class="btn btn-secondary" @click="printPage"><i class="fas fa-print"></i> Print</button>
    </div>

    <table class="table table-striped">
      <thead class="table-dark">
      <tr>
        <th>Product Name</th>
        <th>Description</th>
        <th>Units</th>
        <th>Quantity</th>
        <th>Buying Price</th>
        <th>Selling Price</th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="stock in paginatedStocks" :key="stock.id">
        <td>{{ stock.product.name }}</td>
        <td>{{ stock.product.description }}</td>
        <td>{{ stock.unit.name }}</td>
        <td>{{ stock.quantity }}</td>
        <td>{{ formatCurrency(stock.buying_price) }}</td>
        <td>{{ formatCurrency(stock.selling_price) }}</td>
      </tr>
      </tbody>
    </table>

    <!-- Pagination Controls -->
    <nav>
      <ul class="pagination justify-content-center">
        <li class="page-item" :class="{ disabled: currentPage === 1 }">
          <button class="page-link" @click="changePage(currentPage - 1)">Previous</button>
        </li>
        <li
            v-for="page in totalPages"
            :key="page"
            class="page-item"
            :class="{ active: currentPage === page }"
        >
          <button class="page-link" @click="changePage(page)">{{ page }}</button>
        </li>
        <li class="page-item" :class="{ disabled: currentPage === totalPages }">
          <button class="page-link" @click="changePage(currentPage + 1)">Next</button>
        </li>
      </ul>
    </nav>
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
      stocks: [],
      searchQuery: '',
      currentPage: 1,
      itemsPerPage: 10
    };
  },
  created() {
    this.fetchStock();
  },
  methods: {
    async fetchStock() {
      try {
        const response = await axios.get('/api/inventory/all');
        this.stocks = response.data;
      } catch (error) {
        console.error('Error fetching stock:', error);
      }
    },
    changePage(page) {
      if (page < 1 || page > this.totalPages) return;
      this.currentPage = page;
    },
    formatCurrency(value) {
      return new Intl.NumberFormat('sw-TZ', { style: 'currency', currency: 'TZS' }).format(value);
    },
    exportToExcel() {
      const wsData = this.stocks.map(stock => ({
        'Product Name': stock.product.name,
        'Description': stock.product.description,
        'Quantity': stock.quantity,
        'Buying Price': this.formatCurrency(stock.buying_price),
        'Selling Price': this.formatCurrency(stock.selling_price)
      }));
      const ws = XLSX.utils.json_to_sheet(wsData);
      const wb = XLSX.utils.book_new();
      XLSX.utils.book_append_sheet(wb, ws, 'Stock List');
      const wbout = XLSX.write(wb, { bookType: 'xlsx', type: 'array' });
      saveAs(new Blob([wbout], { type: 'application/octet-stream' }), 'stock_list.xlsx');
    },
    exportToPDF() {
      const doc = new jsPDF('landscape');
      const columns = ['Product Name', 'Description', 'Quantity', 'Buying Price', 'Selling Price'];
      const rows = this.stocks.map(stock => [
        stock.product.name,
        stock.product.description,
        stock.quantity,
        this.formatCurrency(stock.buying_price),
        this.formatCurrency(stock.selling_price)
      ]);
      doc.autoTable(columns, rows);
      doc.save('stock_list.pdf');
    },
    printPage() {
      window.print();
    }
  },
  computed: {
    filteredStocks() {
      return this.stocks.filter(stock => stock.product.name.toLowerCase().includes(this.searchQuery.toLowerCase()));
    },
    paginatedStocks() {
      const startIndex = (this.currentPage - 1) * this.itemsPerPage;
      return this.filteredStocks.slice(startIndex, startIndex + this.itemsPerPage);
    },
    totalPages() {
      return Math.ceil(this.filteredStocks.length / this.itemsPerPage);
    }
  }
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
