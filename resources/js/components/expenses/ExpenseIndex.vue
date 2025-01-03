<template>
  <div class="container mt-2">
    <div class="card">
      <div class="card-header">
        <h2>
          <i class="fas fa-file-alt"></i> Expense List
        </h2>
      </div>
      <div class="card-body">
        <div class="row mb-3">
          <div class="col-md-4">
            <input type="text" class="form-control" v-model="search" placeholder="Search by description">
          </div>
          <div class="col-md-4">
            <select class="form-control" v-model="filterCategory">
              <option value="">All Categories</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>
          </div>
          <div class="col-md-4">
            <input type="date" class="form-control" v-model="filterDate">
          </div>
        </div>
        <table class="table table-bordered" id="expense-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Date</th>
              <th>Category</th>
              <th>Payment Method</th>
              <th>Description</th>
              <th>Amount</th>
              <th>Supplier</th>
              <th>Notes</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="expense in filteredExpenses" :key="expense.id">
              <td>{{ formatId(expense.id) }}</td>
              <td>{{ expense.date }}</td>
              <td>{{ getCategoryName(expense.expense_category_id) }}</td>
              <td>{{ getPaymentMethodName(expense.payment_method_id) }}</td>
              <td>{{ expense.description }}</td>
              <td>{{ formatAmount(expense.amount) }}</td>
              <td>{{ expense.supplier }}</td>
              <td>{{ expense.notes }}</td>
            </tr>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="5"></td>
              <td>{{ formatAmount(totalAmount) }}</td>
              <td colspan="2"></td>
            </tr>
          </tfoot>
        </table>
        <button @click="exportToExcel" class="btn btn-success">
          <i class="fas fa-file-excel"></i> Export to Excel
        </button>
        <button @click="exportToPdf" class="btn btn-danger ml-2">
          <i class="fas fa-file-pdf"></i> Export to PDF
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { jsPDF } from 'jspdf';
import autoTable from 'jspdf-autotable';
import * as XLSX from 'xlsx';

export default {
  data() {
    return {
      expenses: [],
      categories: [],
      paymentMethods: [],
      search: '',
      filterCategory: '',
      filterDate: ''
    };
  },
  computed: {
    filteredExpenses() {
      return this.expenses.filter(expense => {
        const matchesDescription = expense.description.toLowerCase().includes(this.search.toLowerCase());
        const matchesCategory = this.filterCategory ? expense.expense_category_id === this.filterCategory : true;
        const matchesDate = this.filterDate ? expense.date === this.filterDate : true;
        return matchesDescription && matchesCategory && matchesDate;
      });
    },
    totalAmount() {
      return this.filteredExpenses.reduce((total, expense) => total + parseFloat(expense.amount), 0);
    }
  },
  created() {
    this.fetchData();
  },
  methods: {
    fetchData() {
      axios.get('/api/expenses')
        .then(response => {
          this.expenses = response.data;
        });

      axios.get('/api/expense_categories')
        .then(response => {
          this.categories = response.data;
        });

      axios.get('/api/payment_methods')
        .then(response => {
          this.paymentMethods = response.data;
        });
    },
    getCategoryName(categoryId) {
      const category = this.categories.find(c => c.id === categoryId);
      return category ? category.name : 'Unknown';
    },
    getPaymentMethodName(methodId) {
      const method = this.paymentMethods.find(m => m.id === methodId);
      return method ? method.name : 'Unknown';
    },
    formatId(id) {
      return `EXP-${id.toString().padStart(6, '0')}`;
    },
    formatAmount(amount) {
      return new Intl.NumberFormat('sw-TZ', { style: 'currency', currency: 'TZS' }).format(amount);
    },
    exportToExcel() {
      const worksheet = XLSX.utils.json_to_sheet([
        ...this.filteredExpenses.map(expense => ({
          ID: this.formatId(expense.id),
          Date: expense.date,
          Category: this.getCategoryName(expense.expense_category_id),
          'Payment Method': this.getPaymentMethodName(expense.payment_method_id),
          Description: expense.description,
          Amount: this.formatAmount(expense.amount),
          Supplier: expense.supplier,
          Notes: expense.notes
        })),
        {
          ID: 'Total',
          Date: '',
          Category: '',
          'Payment Method': '',
          Description: '',
          Amount: this.formatAmount(this.totalAmount),
          Supplier: '',
          Notes: ''
        }
      ]);
      const workbook = XLSX.utils.book_new();
      XLSX.utils.book_append_sheet(workbook, worksheet, "Expenses");
      XLSX.writeFile(workbook, "expenses.xlsx");
    },
    exportToPdf() {
      const doc = new jsPDF({ orientation: 'landscape' });
      autoTable(doc, {
        head: [['ID', 'Date', 'Category', 'Payment Method', 'Description', 'Amount', 'Supplier', 'Notes']],
        body: [
          ...this.filteredExpenses.map(expense => [
            this.formatId(expense.id),
            expense.date,
            this.getCategoryName(expense.expense_category_id),
            this.getPaymentMethodName(expense.payment_method_id),
            expense.description,
            this.formatAmount(expense.amount),
            expense.supplier,
            expense.notes
          ]),
          [
            'Total', '', '', '', '', this.formatAmount(this.totalAmount), '', ''
          ]
        ]
      });
      doc.save("expenses.pdf");
    }
  }
};
</script>

<style scoped>
.table-bordered {
  width: 100%;
  margin-top: 20px;
}
</style>
