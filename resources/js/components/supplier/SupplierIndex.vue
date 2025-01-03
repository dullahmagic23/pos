<template>
  <div class="container-fluid mt-5">
    <h2><i class="fas fa-warehouse"></i> Manage Suppliers</h2>
    <div class="card">
      <div class="card-header bg-primary text-white">
        <i class="fas fa-list"></i> Supplier List
      </div>
      <div class="card-body">
        <table class="table table-striped">
          <thead>
          <tr>
            <th>Name</th>
            <th>Address</th>
            <th>Phone</th>
            <th>TIN</th>
            <th>Actions</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="supplier in suppliers" :key="supplier.id">
            <td>{{ supplier.name }}</td>
            <td>{{ supplier.address }}</td>
            <td>{{ supplier.phone }}</td>
            <td>{{ supplier.tin }}</td>
            <td>
              <button
                  class="btn btn-sm btn-primary"
                  @click="selectSupplier(supplier)"
                  data-bs-toggle="modal"
                  data-bs-target="#editSupplierModal"
              >
                <i class="fas fa-edit"></i> Edit
              </button>
            </td>
          </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Edit Supplier Modal -->
    <div
        class="modal fade"
        id="editSupplierModal"
        tabindex="-1"
        aria-labelledby="editSupplierModalLabel"
        aria-hidden="true"
    >
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editSupplierModalLabel">Edit Supplier</h5>
            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="modal"
                aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="validateAndUpdateSupplier">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="editName" class="form-label">Name</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-building"></i></span>
                    <input
                        type="text"
                        class="form-control"
                        id="editName"
                        v-model="selectedSupplier.name"
                        required
                    />
                  </div>
                  <div v-if="errors.name" class="text-danger">{{ errors.name }}</div>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="editAddress" class="form-label">Address</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                    <input
                        type="text"
                        class="form-control"
                        id="editAddress"
                        v-model="selectedSupplier.address"
                        required
                    />
                  </div>
                  <div v-if="errors.address" class="text-danger">{{ errors.address }}</div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="editPhone" class="form-label">Phone Number</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                    <input
                        type="tel"
                        class="form-control"
                        id="editPhone"
                        v-model="selectedSupplier.phone"
                        required
                    />
                  </div>
                  <div v-if="errors.phone" class="text-danger">{{ errors.phone }}</div>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="editTin" class="form-label">TIN</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                    <input
                        type="text"
                        class="form-control"
                        id="editTin"
                        v-model="selectedSupplier.tin"
                    />
                  </div>
                </div>
              </div>
              <button type="submit" class="btn btn-primary" :disabled="loading">
                <i :class="loading ? 'fas fa-spinner fa-spin' : 'fas fa-save'"></i>
                {{ loading ? 'Updating...' : 'Update Supplier' }}
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      suppliers: [],
      selectedSupplier: {
        name: '',
        address: '',
        phone: '',
        tin: '',
      },
      errors: {},
      loading: false, // To track the loading state
    };
  },
  methods: {
    fetchSuppliers() {
      axios.get('/api/suppliers')
          .then(response => {
            this.suppliers = response.data;
          })
          .catch(error => {
            console.error('Error fetching suppliers:', error);
          });
    },
    selectSupplier(supplier) {
      this.selectedSupplier = { ...supplier };
      this.errors = {}; // Clear previous errors
    },
    validateAndUpdateSupplier() {
      this.errors = {};

      if (!this.selectedSupplier.name) {
        this.errors.name = 'Name of Supplier is required.';
      }

      if (!this.selectedSupplier.address) {
        this.errors.address = 'Address is required.';
      }

      if (!this.selectedSupplier.phone) {
        this.errors.phone = 'Phone number is required.';
      } else if (!this.isValidPhone(this.selectedSupplier.phone)) {
        this.errors.phone = 'Phone number is not valid.';
      }

      if (Object.keys(this.errors).length === 0) {
        this.updateSupplier();
      }
    },
    isValidPhone(phone) {
      const re = /^[0-9\-\+]{9,15}$/;
      return re.test(phone);
    },
    updateSupplier() {
      this.loading = true;
      axios.put(`/api/suppliers/${this.selectedSupplier.id}`, this.selectedSupplier)
          .then(() => {
            this.fetchSuppliers(); // Refresh the supplier list
            this.loading = false;
            document.addEventListener('DOMContentLoaded', () => {
              var editSupplierModal = new bootstrap.Modal(document.getElementById('editSupplierModal'))
              document.addEventListener('closeModal', () => {
                editSupplierModal.hide();
              });
            });
            this.$notify('Customer updated successfully');
          })
          .catch(error => {
            console.error('Error updating supplier:', error);
            this.loading = false;
          });
    }
  },
  mounted() {
    this.fetchSuppliers();
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
