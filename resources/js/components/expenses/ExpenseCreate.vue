<template>
  <div class="card w-100 mt-2">
    <div class="card-header">
      <h2>
        <i class="fas fa-file-alt"></i> Submit Expense
      </h2>
    </div>
    <div class="card-body">
      <form @submit.prevent="submitExpense" class="p-3">
        <!-- Loader -->
        <div v-if="loading" class="spinner-border" role="status">
          <span class="sr-only">Loading...</span>
        </div>

        <!-- Form Fields -->
        <div v-else>
          <div class="row mb-3">
            <div class="col-md-6">
              <div :class="['form-group', { 'has-error': errors.category }]">
                <label for="category">
                  <i class="fas fa-list"></i> Expense Category
                </label>
                <select id="category" v-model="expense.category" class="form-control" aria-describedby="category-error" ref="category">
                  <option value="" disabled>Select a category</option>
                  <option v-for="category in categories" :key="category.id" :value="category.id">
                    {{ category.name }}
                  </option>
                </select>
                <span v-if="errors.category" id="category-error" class="text-danger">{{ errors.category }}</span>
              </div>
            </div>
            <div class="col-md-6">
              <div :class="['form-group', { 'has-error': errors.paymentMethod }]">
                <label for="paymentMethod">
                  <i class="fas fa-credit-card"></i> Payment Method
                </label>
                <select id="paymentMethod" v-model="expense.paymentMethod" class="form-control" aria-describedby="paymentMethod-error" ref="paymentMethod">
                  <option value="" disabled>Select a payment method</option>
                  <option v-for="method in paymentMethods" :key="method.id" :value="method.id">
                    {{ method.name }}
                  </option>
                </select>
                <span v-if="errors.paymentMethod" id="paymentMethod-error" class="text-danger">{{ errors.paymentMethod }}</span>
              </div>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <div :class="['form-group', { 'has-error': errors.date }]">
                <label for="date">
                  <i class="fas fa-calendar-alt"></i> Date
                </label>
                <input type="date" id="date" v-model="expense.date" class="form-control" aria-describedby="date-error" ref="date">
                <span v-if="errors.date" id="date-error" class="text-danger">{{ errors.date }}</span>
              </div>
            </div>
            <div class="col-md-6">
              <div :class="['form-group', { 'has-error': errors.amount }]">
                <label for="amount">
                  <i class="fas fa-dollar-sign"></i> Amount
                </label>
                <input type="number" id="amount" v-model="expense.amount" class="form-control" aria-describedby="amount-error" ref="amount" min="0" step="0.01">
                <span v-if="errors.amount" id="amount-error" class="text-danger">{{ errors.amount }}</span>
              </div>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <div :class="['form-group', { 'has-error': errors.description }]">
                <label for="description">
                  <i class="fas fa-info-circle"></i> Description
                </label>
                <input type="text" id="description" v-model="expense.description" class="form-control" aria-describedby="description-error" ref="description" placeholder="Enter a description">
                <span v-if="errors.description" id="description-error" class="text-danger">{{ errors.description }}</span>
              </div>
            </div>
            <div class="col-md-6">
              <div :class="['form-group', { 'has-error': errors.vendor }]">
                <label for="vendor">
                  <i class="fas fa-store"></i> Vendor
                </label>
                <input type="text" id="vendor" v-model="expense.vendor" class="form-control" aria-describedby="vendor-error" ref="vendor" placeholder="Enter vendor name">
                <span v-if="errors.vendor" id="vendor-error" class="text-danger">{{ errors.vendor }}</span>
              </div>
            </div>
          </div>

          <div class="form-group mb-3" :class="{ 'has-error': errors.notes }">
            <label for="notes">
              <i class="fas fa-sticky-note"></i> Notes
            </label>
            <textarea id="notes" v-model="expense.notes" class="form-control" aria-describedby="notes-error" ref="notes" placeholder="Enter any additional notes"></textarea>
            <span v-if="errors.notes" id="notes-error" class="text-danger">{{ errors.notes }}</span>
          </div>

          <button type="submit" class="btn btn-primary">
            <i class="fas fa-paper-plane"></i> Submit Expense
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      expense: {
        category: '',
        paymentMethod: '',
        date: '',
        description: '',
        amount: 0,
        vendor: '',
        notes: ''
      },
      categories: [],
      paymentMethods: [],
      loading: false,
      errors: {}
    };
  },
  created() {
    this.loading = true;
    Promise.all([
      axios.get('/api/expense_categories'),
      axios.get('/api/payment_methods')
    ])
        .then(([categoriesResponse, paymentMethodsResponse]) => {
          this.categories = categoriesResponse.data;
          this.paymentMethods = paymentMethodsResponse.data;
          this.loading = false;
        })
        .catch(() => {
          this.loading = false;
        });
  },
  methods: {
    validateInputs() {
      this.errors = {};
      if (!this.expense.category) {
        this.errors.category = 'Category is required';
        this.$refs.category.focus();
      }
      if (!this.expense.paymentMethod) {
        this.errors.paymentMethod = 'Payment method is required';
        this.$refs.paymentMethod.focus();
      }
      if (!this.expense.date) {
        this.errors.date = 'Date is required';
        this.$refs.date.focus();
      }
      if (!this.expense.description) {
        this.errors.description = 'Description is required';
        this.$refs.description.focus();
      }
      if (!this.expense.amount || this.expense.amount <= 0) {
        this.errors.amount = 'Amount must be greater than 0';
        this.$refs.amount.focus();
      }
      if (!this.expense.vendor) {
        this.errors.vendor = 'Vendor is required';
        this.$refs.vendor.focus();
      }
      return Object.keys(this.errors).length === 0;
    },
    resetForm() {
      this.expense = {
        category: '',
        paymentMethod: '',
        date: '',
        description: '',
        amount: 0,
        vendor: '',
        notes: ''
      };
    },
    submitExpense() {
      if (this.validateInputs()) {
        axios.post('/api/expenses', this.expense)
            .then(response => {
              this.$notify("Expenses stored successfully")
              this.resetForm();
            })
            .catch(error => {
              if (error.response && error.response.data) {
                this.errors = error.response.data.errors;
              } else {
                console.error('Unexpected error:', error);
              }
            });
      }
    }
  }
};
</script>

<style scoped>
.card {
  width: 100%;
  margin: 0 auto;
}

.form-group.has-error input,
.form-group.has-error select,
.form-group.has-error textarea {
  border-color: #dc3545;
}

.text-danger {
  font-size: 0.875em;
  color: #dc3545;
}

.spinner-border {
  width: 3rem;
  height: 3rem;
}

.btn-primary:hover {
  background-color: #0056b3;
}

@media (max-width: 576px) {
  .card {
    padding: 1rem;
  }
}
</style>
