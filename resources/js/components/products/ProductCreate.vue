<template>
  <div>
    <!-- Create Product Form -->
    <div class="card mt-5">
      <div class="card-header">
        <h5><i class="fas fa-box-open"></i> Create Product</h5>
      </div>
      <div class="card-body">
        <form @submit.prevent="handleSubmit" :disabled="isSubmitting">
          <div class="mb-3">
            <label for="name" class="form-label">
              <i class="fas fa-tag"></i> Product Name:
            </label>
            <input type="text" id="name" v-model="product.name" class="form-control" @input="clearError('name')" />
            <span v-if="errors.name" class="text-danger">{{ errors.name }}</span>
          </div>

          <div class="mb-3">
            <label for="description" class="form-label">
              <i class="fas fa-info-circle"></i> Description:
            </label>
            <input type="text" id="description" v-model="product.description" class="form-control" @input="clearError('description')" />
            <span v-if="errors.description" class="text-danger">{{ errors.description }}</span>
          </div>

          <div class="mb-3">
            <label for="category" class="form-label">
              <i class="fas fa-list"></i> Category:
            </label>
            <select id="category" v-model="product.category" class="form-select" @change="clearError('category')">
              <option value="">Select a category</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
            </select>
            <span v-if="errors.category" class="text-danger">{{ errors.category }}</span>
          </div>

          <div class="mb-3">
            <label class="form-label">
              <i class="fas fa-box"></i> Product Units:
            </label>
            <div class="d-flex flex-wrap">
              <div v-for="unit in units" :key="unit.id" class="form-check me-3">
                <input type="checkbox" :id="'unit-' + unit.id" :value="unit.id" v-model="product.units" class="form-check-input" @change="clearError('units')" />
                <label :for="'unit-' + unit.id" class="form-check-label">{{ unit.name }}</label>
              </div>
            </div>
            <span v-if="errors.units" class="text-danger">{{ errors.units }}</span>
          </div>

          <div class="mb-3">
            <label for="code" class="form-label">
              <i class="fas fa-barcode"></i> Product Code:
            </label>
            <input type="text" id="code" v-model="product.code" class="form-control" @input="clearError('code')" />
            <span v-if="errors.code" class="text-danger">{{ errors.code }}</span>
          </div>

          <button type="submit" class="btn btn-primary" :disabled="isSubmitting">
            <i class="fas fa-save"></i> Create Product
            <span v-if="isSubmitting" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
          </button>
        </form>
      </div>
    </div>

    <!-- Search Input -->
    <div class="search-container mt-4">
      <input
          type="text"
          v-model="searchQuery"
          @input="handleSearch"
          class="form-control"
          placeholder="Search products..."
      />
    </div>

    <!-- Pagination Controls -->
    <div class="pagination-container mt-4">
      <button class="btn btn-secondary" :disabled="currentPage === 1" @click="goToPage(currentPage - 1)">
        <i class="fas fa-arrow-left"></i> Previous
      </button>
      <span class="mx-3">Page {{ currentPage }} of {{ totalPages }}</span>
      <button class="btn btn-secondary" :disabled="currentPage === totalPages" @click="goToPage(currentPage + 1)">
        Next <i class="fas fa-arrow-right"></i>
      </button>
    </div>

    <!-- Display Products -->
    <div class="mt-4">
      <h3>Products</h3>
      <ul class="list-group">
        <li v-for="product in products" :key="product.id" class="list-group-item">
          {{ product.name }} - {{ product.code }}
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      product: {
        name: '',
        description: '',
        category: '',
        units: [],
        code: ''
      },
      categories: [],
      units: [],
      errors: {},
      isSubmitting: false,
      products: [],
      searchQuery: '',
      currentPage: 1,
      totalPages: 1,
      perPage: 10,
      totalItems: 0
    };
  },
  created() {
    this.fetchCategories();
    this.fetchUnits();
    this.fetchProducts();
  },
  methods: {
    // Fetch categories from the API
    async fetchCategories() {
      try {
        const response = await axios.get('/api/categories');
        this.categories = response.data;
      } catch (error) {
        console.error('Error fetching categories:', error);
      }
    },

    // Fetch units from the API
    async fetchUnits() {
      try {
        const response = await axios.get('/api/units');
        this.units = response.data;
      } catch (error) {
        console.error('Error fetching units:', error);
      }
    },

    // Fetch products with pagination and search query from the API
    async fetchProducts() {
      try {
        const response = await axios.get('/api/products', {
          params: {
            page: this.currentPage,
            per_page: this.perPage,
            search: this.searchQuery
          }
        });
        this.products = response.data.products;
        this.totalItems = response.data.total;
        this.totalPages = Math.ceil(this.totalItems / this.perPage);
      } catch (error) {
        console.error('Error fetching products:', error);
      }
    },

      notify(type, message) {
        this.$notify({
          type: type,
          message: message
        });
      },

    // Handle form submission
    async handleSubmit() {
      if (this.validateForm()) {
        this.isSubmitting = true;
        try {
          const response = await axios.post('/api/products', this.product);
          this.$notify && this.$notify({ type: 'success', message: "Product created successfully." });
          this.resetForm();
          this.fetchProducts(); // Re-fetch products after creating a new one
        } catch (error) {
          console.error('Error creating product:', error);
          if (error.response && error.response.data && error.response.data.errors) {
            this.errors = error.response.data.errors;
          }
        } finally {
          this.isSubmitting = false;
        }
      }
    },

    // Validate form fields
    validateForm() {
      this.errors = {};
      if (!this.product.name) this.errors.name = 'Product name is required.';
      if (!this.product.description) this.errors.description = 'Description is required.';
      if (!this.product.category) this.errors.category = 'Category is required.';
      if (this.product.units.length === 0) this.errors.units = 'Select at least one unit.';
      if (!this.product.code) this.errors.code = 'Product code is required.';
      return Object.keys(this.errors).length === 0;
    },

    // Clear error messages when the user starts typing
    clearError(field) {
      if (this.errors[field]) {
        this.$delete(this.errors, field);
      }
    },

    // Reset form fields
    resetForm() {
      this.product = {
        name: '',
        description: '',
        category: '',
        units: [],
        code: ''
      };
      this.errors = {};
    },

    // Change page and fetch products accordingly
    goToPage(page) {
      if (page > 0 && page <= this.totalPages) {
        this.currentPage = page;
        this.fetchProducts(); // Fetch products for the new page
      }
    },

    // Handle search input and reset to first page
    handleSearch() {
      this.currentPage = 1; // Reset to page 1 when search query changes
      this.fetchProducts(); // Fetch products based on the search query
    }
  }
};
</script>

<style scoped>
.card-header {
  background-color: #f8f9fa;
  border-bottom: 1px solid #e3e3e3;
}

.card-header h5 {
  margin: 0;
}

.card-body {
  background-color: #fff;
}

.mb-3 {
  margin-bottom: 1rem;
}

.btn {
  display: inline-block;
  margin-top: 10px;
}

.spinner-border {
  margin-left: 10px;
}

.text-danger {
  font-size: 0.875rem;
}

.pagination-container {
  display: flex;
  justify-content: center;
  margin-top: 20px;
}

.search-container {
  margin-top: 20px;
  max-width: 400px;
  margin-left: auto;
  margin-right: auto;
}

.search-container input {
  width: 100%;
}
</style>
