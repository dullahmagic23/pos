<template>
  <div class="container-fluid mt-5">
    <h2 class="title-header">
      <i class="fas fa-users"></i> Manage Customers
    </h2>
    <div class="card shadow-lg border-0">
      <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <span><i class="fas fa-list"></i> Customer List</span>
        <input
            type="text"
            v-model="searchQuery"
            class="form-control form-control-sm w-25"
            placeholder="Search by name or phone"
        />
        <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#addCustomerModal">
          <i class="fas fa-plus"></i> Add Customer
        </button>
      </div>
      <div class="card-body">
        <table class="table table-hover table-bordered">
          <thead class="table-primary">
          <tr>
            <th>Name</th>
            <th>Address</th>
            <th>Phone</th>
            <th>TIN</th>
            <th>Actions</th>
          </tr>
          </thead>
          <tbody>
          <tr
              v-for="customer in paginatedCustomers"
              :key="customer.id"
              class="table-row"
          >
            <td>{{ customer.name }}</td>
            <td>{{ customer.address }}</td>
            <td>{{ customer.phone }}</td>
            <td>{{ customer.tin }}</td>
            <td>
              <button
                  class="btn btn-sm btn-outline-primary"
                  @click="selectCustomer(customer)"
                  data-bs-toggle="modal"
                  data-bs-target="#editCustomerModal"
              >
                <i class="fas fa-edit"></i> Edit
              </button>
            </td>
          </tr>
          <tr v-if="filteredCustomers.length === 0">
            <td colspan="5" class="text-center">No customers found</td>
          </tr>
          </tbody>
        </table>
        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-3">
          <span>
            Showing {{ startIndex + 1 }} to
            {{ Math.min(startIndex + pageSize, filteredCustomers.length) }} of
            {{ filteredCustomers.length }} entries
          </span>
          <nav>
            <ul class="pagination pagination-sm mb-0">
              <li
                  class="page-item"
                  :class="{ disabled: currentPage === 1 }"
                  @click="changePage(currentPage - 1)"
              >
                <a class="page-link" href="#">Previous</a>
              </li>
              <li
                  class="page-item"
                  :class="{ active: currentPage === page }"
                  v-for="page in totalPages"
                  :key="page"
                  @click="changePage(page)"
              >
                <a class="page-link" href="#">{{ page }}</a>
              </li>
              <li
                  class="page-item"
                  :class="{ disabled: currentPage === totalPages }"
                  @click="changePage(currentPage + 1)"
              >
                <a class="page-link" href="#">Next</a>
              </li>
            </ul>
          </nav>
        </div>
      </div>
    </div>

    <!-- Edit Customer Modal -->
    <div
        class="modal fade"
        id="editCustomerModal"
        tabindex="-1"
        aria-labelledby="editCustomerModalLabel"
        aria-hidden="true"
    >
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title" id="editCustomerModalLabel">
              Edit Customer
            </h5>
            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="modal"
                aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="validateAndUpdateCustomer">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="editName" class="form-label">Name</label>
                  <div class="input-group">
                    <span class="input-group-text">
                      <i class="fas fa-user"></i>
                    </span>
                    <input
                        type="text"
                        class="form-control"
                        id="editName"
                        v-model="selectedCustomer.name"
                        required
                    />
                  </div>
                  <div v-if="errors.name" class="text-danger">{{ errors.name }}</div>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="editAddress" class="form-label">Address</label>
                  <div class="input-group">
                    <span class="input-group-text">
                      <i class="fas fa-map-marker-alt"></i>
                    </span>
                    <input
                        type="text"
                        class="form-control"
                        id="editAddress"
                        v-model="selectedCustomer.address"
                        required
                    />
                  </div>
                  <div
                      v-if="errors.address"
                      class="text-danger"
                  >{{ errors.address }}</div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="editPhone" class="form-label">Phone Number</label>
                  <div class="input-group">
                    <span class="input-group-text">
                      <i class="fas fa-phone"></i>
                    </span>
                    <input
                        type="tel"
                        class="form-control"
                        id="editPhone"
                        v-model="selectedCustomer.phone"
                        required
                    />
                  </div>
                  <div v-if="errors.phone" class="text-danger">{{ errors.phone }}</div>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="editTin" class="form-label">TIN (optional)</label>
                  <div class="input-group">
                    <span class="input-group-text">
                      <i class="fas fa-id-card"></i>
                    </span>
                    <input
                        type="text"
                        class="form-control"
                        id="editTin"
                        v-model="selectedCustomer.tin"
                    />
                  </div>
                </div>
              </div>
              <button type="submit" class="btn btn-primary w-100" :disabled="loading">
                <i :class="loading ? 'fas fa-spinner fa-spin' : 'fas fa-save'"></i>
                {{ loading ? 'Updating...' : 'Update Customer' }}
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Add Customer Modal -->
    <div
        class="modal fade"
        id="addCustomerModal"
        tabindex="-1"
        aria-labelledby="addCustomerModalLabel"
        aria-hidden="true"
    >
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header bg-success text-white">
            <h5 class="modal-title" id="addCustomerModalLabel">
              Add Customer
            </h5>
            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="modal"
                aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="validateAndAddCustomer">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="addName" class="form-label">Name</label>
                  <div class="input-group">
                    <span class="input-group-text">
                      <i class="fas fa-user"></i>
                    </span>
                    <input
                        type="text"
                        class="form-control"
                        id="addName"
                        v-model="newCustomer.name"
                        required
                    />
                  </div>
                  <div v-if="errors.name" class="text-danger">{{ errors.name }}</div>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="addAddress" class="form-label">Address</label>
                  <div class="input-group">
                    <span class="input-group-text">
                      <i class="fas fa-map-marker-alt"></i>
                    </span>
                    <input
                        type="text"
                        class="form-control"
                        id="addAddress"
                        v-model="newCustomer.address"
                        required
                    />
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="addPhone" class="form-label">Phone Number</label>
                  <div class="input-group">
                    <span class="input-group-text">
                      <i class="fas fa-phone"></i>
                    </span>
                    <input
                        type="tel"
                        class="form-control"
                        id="addPhone"
                        v-model="newCustomer.phone"
                        required
                    />
                  </div>
                  <div v-if="errors.phone" class="text-danger">{{ errors.phone }}</div>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="addTin" class="form-label">TIN (optional)</label>
                  <div class="input-group">
                    <span class="input-group-text">
                      <i class="fas fa-id-card"></i>
                    </span>
                    <input
                        type="text"
                        class="form-control"
                        id="addTin"
                        v-model="newCustomer.tin"
                    />
                  </div>
                </div>
              </div>
              <button type="submit" class="btn btn-success w-100" :disabled="loading">
                <i :class="loading ? 'fas fa-spinner fa-spin' : 'fas fa-save'"></i>
                {{ loading ? 'Adding...' : 'Add Customer' }}
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import { debounce } from "lodash";

export default {
  data() {
    return {
      customers: [],
      searchQuery: "",
      searchQueryDebounced: "",  // Debounced search query
      currentPage: 1,
      pageSize: 10,
      selectedCustomer: {
        id: null,
        name: "",
        address: "",
        phone: "",
        tin: "",
      },
      newCustomer: {
        name: "",
        address: "",
        phone: "",
        tin: "",
      },
      errors: {},
      loading: false,
    };
  },
  watch: {
    searchQuery: debounce(function () {
      this.searchQueryDebounced = this.searchQuery;
    }, 500),  // Debounce time
  },
  computed: {
    filteredCustomers() {
      return this.customers.filter(
          (customer) =>
              customer.name.toLowerCase().includes(this.searchQueryDebounced.toLowerCase()) ||
              customer.phone.includes(this.searchQueryDebounced)
      );
    },
    totalPages() {
      return Math.ceil(this.filteredCustomers.length / this.pageSize);
    },
    startIndex() {
      return (this.currentPage - 1) * this.pageSize;
    },
    paginatedCustomers() {
      return this.filteredCustomers.slice(
          this.startIndex,
          this.startIndex + this.pageSize
      );
    },
  },
  methods: {
    fetchCustomers() {
      axios
          .get("/api/customers")
          .then((response) => {
            this.customers = response.data;
          })
          .catch((error) => {
            console.error("Error fetching customers:", error);
          });
    },
    changePage(page) {
      if (page > 0 && page <= this.totalPages) {
        this.currentPage = page;
      }
    },
    selectCustomer(customer) {
      this.selectedCustomer = { ...customer };
      this.errors = {};
    },
    validateAndUpdateCustomer() {
      this.errors = {};
      if (!this.selectedCustomer.name) this.errors.name = "Name is required.";
      if (!this.selectedCustomer.address)
        this.errors.address = "Address is required.";
      if (!this.selectedCustomer.phone) {
        this.errors.phone = "Phone number is required.";
      } else if (!/^[0-9\-\+]{9,15}$/.test(this.selectedCustomer.phone)) {
        this.errors.phone = "Phone number is not valid.";
      }
      if (!Object.keys(this.errors).length) this.updateCustomer();
    },
    updateCustomer() {
      this.loading = true;
      axios
          .put(`/api/customers/${this.selectedCustomer.id}`, this.selectedCustomer)
          .then(() => {
            this.fetchCustomers();
            this.loading = false;
            const editModal = new bootstrap.Modal(
                document.getElementById("editCustomerModal")
            );
            editModal.hide();
            this.$notify("Customer updated successfully");
          })
          .catch((error) => {
            console.error("Error updating customer:", error);
            this.loading = false;
          });
    },
    validateAndAddCustomer() {
      this.errors = {};
      if (!this.newCustomer.name) this.errors.name = "Name is required.";
      if (!this.newCustomer.address) this.errors.address = "Address is required.";
      if (!this.newCustomer.phone) {
        this.errors.phone = "Phone number is required.";
      } else if (!/^[0-9\-\+]{9,15}$/.test(this.newCustomer.phone)) {
        this.errors.phone = "Phone number is not valid.";
      }
      if (!Object.keys(this.errors).length) this.addCustomer();
    },
    addCustomer() {
      this.loading = true;
      axios
          .post("/api/customers", this.newCustomer)
          .then(() => {
            this.$notify("A customer has been registered successfully")
            this.fetchCustomers();
            this.loading = false;
            const addModal = new bootstrap.Modal(
                document.getElementById("addCustomerModal")
            );
            addModal.hide();
            this.$notify("Customer added successfully");
          })
          .catch((error) => {
            console.error("Error adding customer:", error);
            this.loading = false;
          });
    },
  },
  mounted() {
    this.fetchCustomers();
  },
};
</script>

<style scoped>
/* Add your custom styles here */
</style>
