<template>
  <div class="container-fluid mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2><i class="fas fa-tags"></i> Manage Products Categories</h2>
      <button class="btn btn-primary" @click="openCreateModal">
        <i class="fas fa-plus"></i> Create Category
      </button>
    </div>
    <div class="card">
      <div class="card-header bg-primary text-white">
        <i class="fas fa-list"></i> Category List
      </div>
      <div class="card-body">
        <table class="table table-striped">
          <thead>
          <tr>
            <th>Name</th>
            <th>Description</th>
            <th>Actions</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="category in categories" :key="category.id">
            <td>{{ category.name }}</td>
            <td>{{ category.description }}</td>
            <td>
              <button
                  class="btn btn-sm btn-primary"
                  @click="selectCategory(category)"
                  data-bs-toggle="modal"
                  data-bs-target="#editCategoryModal"
              >
                <i class="fas fa-edit"></i> Edit
              </button>
            </td>
          </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Create Category Modal -->
    <div
        class="modal fade"
        id="createCategoryModal"
        tabindex="-1"
        aria-labelledby="createCategoryModalLabel"
        aria-hidden="true"
    >
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="createCategoryModalLabel">Create Category</h5>
            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="modal"
                aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="validateAndCreateCategory">
              <div class="mb-3">
                <label for="createName" class="form-label">Name</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="fas fa-tag"></i></span>
                  <input
                      type="text"
                      class="form-control"
                      id="createName"
                      v-model="newCategory.name"
                      required
                  />
                </div>
                <div v-if="errors.name" class="text-danger">{{ errors.name }}</div>
              </div>
              <div class="mb-3">
                <label for="createDescription" class="form-label">Description</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="fas fa-info-circle"></i></span>
                  <input
                      type="text"
                      class="form-control"
                      id="createDescription"
                      v-model="newCategory.description"
                      required
                  />
                </div>
                <div v-if="errors.description" class="text-danger">{{ errors.description }}</div>
              </div>
              <button type="submit" class="btn btn-primary" :disabled="loading">
                <i :class="loading ? 'fas fa-spinner fa-spin' : 'fas fa-save'"></i>
                {{ loading ? 'Creating...' : 'Create Category' }}
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Category Modal -->
    <div
        class="modal fade"
        id="editCategoryModal"
        tabindex="-1"
        aria-labelledby="editCategoryModalLabel"
        aria-hidden="true"
    >
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editCategoryModalLabel">Edit Category</h5>
            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="modal"
                aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="validateAndUpdateCategory">
              <div class="mb-3">
                <label for="editName" class="form-label">Name</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="fas fa-tag"></i></span>
                  <input
                      type="text"
                      class="form-control"
                      id="editName"
                      v-model="selectedCategory.name"
                      required
                  />
                </div>
                <div v-if="errors.name" class="text-danger">{{ errors.name }}</div>
              </div>
              <div class="mb-3">
                <label for="editDescription" class="form-label">Description</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="fas fa-info-circle"></i></span>
                  <input
                      type="text"
                      class="form-control"
                      id="editDescription"
                      v-model="selectedCategory.description"
                      required
                  />
                </div>
                <div v-if="errors.description" class="text-danger">{{ errors.description }}</div>
              </div>
              <button type="submit" class="btn btn-primary" :disabled="loading">
                <i :class="loading ? 'fas fa-spinner fa-spin' : 'fas fa-save'"></i>
                {{ loading ? 'Updating...' : 'Update Category' }}
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Modal } from 'bootstrap';
export default {
  data() {
    return {
      categories: [],
      newCategory: {
        name: '',
        description: '',
      },
      selectedCategory: {
        name: '',
        description: '',
      },
      errors: {},
      loading: false, // To track the loading state
    };
  },
  methods: {
    fetchCategories() {
      axios.get('/api/categories')
          .then(response => {
            this.categories = response.data;
          })
          .catch(error => {
            console.error('Error fetching categories:', error);
          });
    },
    openCreateModal() {
      this.newCategory = { name: '', description: '' };
      this.errors = {}; // Clear previous errors
      const createCategoryModal = new Modal(document.getElementById('createCategoryModal'));
      createCategoryModal.show();
    },
    selectCategory(category) {
      this.selectedCategory = { ...category };
      this.errors = {}; // Clear previous errors
    },
    validateAndCreateCategory() {
      this.errors = {};

      if (!this.newCategory.name) {
        this.errors.name = 'Name is required.';
      }

      if (!this.newCategory.description) {
        this.errors.description = 'Description is required.';
      }

      if (Object.keys(this.errors).length === 0) {
        this.createCategory();
      }
    },
    createCategory() {
      this.loading = true;
      axios.post('/api/categories', this.newCategory)
          .then(() => {
            alert('Category created successfully');
            this.fetchCategories(); // Refresh the category list
            this.loading = false;
            const createCategoryModal = new bootstrap.Modal(document.getElementById('createCategoryModal'));
            createCategoryModal.hide();
          })
          .catch(error => {
            console.error('Error creating category:', error);
            this.loading = false;
          });
    },
    validateAndUpdateCategory() {
      this.errors = {};

      if (!this.selectedCategory.name) {
        this.errors.name = 'Name is required.';
      }

      if (!this.selectedCategory.description) {
        this.errors.description = 'Description is required.';
      }

      if (Object.keys(this.errors).length === 0) {
        this.updateCategory();
      }
    },
    updateCategory() {
      this.loading = true;
      axios.put(`/api/categories/${this.selectedCategory.id}`, this.selectedCategory)
          .then(() => {
            alert('Category updated successfully');
            this.fetchCategories(); // Refresh the category list
            this.loading = false;
            const editCategoryModal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
            editCategoryModal.hide();
          })
          .catch(error => {
            console.error('Error updating category:', error);
            this.loading = false;
          });
    }
  },
  mounted() {
    this.fetchCategories();
  },
};
</script>

<style scoped>
.container-fluid {
  width: 100%;
}
.card-header {
  display: flex;
  align-items: center;
}
.card-header i {
  margin-right: 10px;
}
.input-group-text {
  background-color: #e9ecef;
}
</style>