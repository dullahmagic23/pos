<template>
  <div class="container-fluid mt-5">
    <h2 class="mb-4"><i class="fas fa-user-plus"></i> Create Customer</h2>
    <div class="card shadow-lg">
      <div class="card-header bg-primary text-white">
        <i class="fas fa-user"></i> Customer Information
      </div>
      <div class="card-body">
        <form @submit.prevent="validateAndCreateCustomer">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="name" class="form-label">Name</label>
              <div class="input-group input-group-lg">
                <span class="input-group-text"><i class="fas fa-user"></i></span>
                <input
                    type="text"
                    class="form-control input-rounded"
                    id="name"
                    v-model="customer.name"
                    required
                />
              </div>
              <div v-if="errors.name" class="text-danger d-flex align-items-center">
                <i class="fas fa-exclamation-circle me-2"></i>{{ errors.name }}
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <label for="address" class="form-label">Address</label>
              <div class="input-group input-group-lg">
                <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                <input
                    type="text"
                    class="form-control input-rounded"
                    id="address"
                    v-model="customer.address"
                    required
                />
              </div>
              <div v-if="errors.address" class="text-danger d-flex align-items-center">
                <i class="fas fa-exclamation-circle me-2"></i>{{ errors.address }}
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="phone" class="form-label">Phone Number</label>
              <div class="input-group input-group-lg">
                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                <input
                    type="tel"
                    class="form-control input-rounded"
                    id="phone"
                    v-model="customer.phone"
                    required
                    @input="formatPhone"
                />
              </div>
              <div v-if="errors.phone" class="text-danger d-flex align-items-center">
                <i class="fas fa-exclamation-circle me-2"></i>{{ errors.phone }}
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <label for="tin" class="form-label">TIN (optional)</label>
              <div class="input-group input-group-lg">
                <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                <input
                    type="text"
                    class="form-control input-rounded"
                    id="tin"
                    v-model="customer.tin"
                />
              </div>
            </div>
          </div>
          <button type="submit" class="btn btn-primary w-100" :disabled="loading">
            <i :class="loading ? 'fas fa-spinner fa-spin' : 'fas fa-save'"></i>
            {{ loading ? 'Creating...' : 'Create Customer' }}
          </button>
        </form>

        <!-- Success/Error Message -->
        <div v-if="message" class="alert" :class="message.type">
          <i :class="message.icon + ' me-2'"></i>{{ message.text }}
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      customer: {
        name: '',
        address: '',
        phone: '',
        tin: '',
      },
      errors: {},
      loading: false, // To track the loading state
      message: null, // For success or error messages
    };
  },
  methods: {
    validateAndCreateCustomer() {
      this.errors = {};
      this.message = null; // Clear any previous message

      if (!this.customer.name) {
        this.errors.name = 'Name is required.';
      }

      if (!this.customer.address) {
        this.errors.address = 'Address is required.';
      }

      if (!this.customer.phone) {
        this.errors.phone = 'Phone number is required.';
      } else if (!this.isValidPhone(this.customer.phone)) {
        this.errors.phone = 'Phone number is not valid.';
      }

      if (Object.keys(this.errors).length === 0) {
        this.createCustomer();
      }
    },
    isValidPhone(phone) {
      const re = /^[0-9\-\+]{9,15}$/;
      return re.test(phone);
    },
    formatPhone() {
      // Format phone number (add some basic formatting)
      this.customer.phone = this.customer.phone.replace(/[^\d\+]/g, '').slice(0, 15);
    },
    createCustomer() {
      this.loading = true;
      axios.post('/api/customers', this.customer)
          .then(() => {
            this.$notify('Customer created successfully');
            this.customer = { name: '', address: '', phone: '', tin: '' };
            this.loading = false;
            this.message = {
              type: 'alert-success',
              icon: 'fas fa-check-circle',
              text: 'Customer created successfully!',
            };
          })
          .catch(error => {
            console.error('Error creating customer:', error);
            this.loading = false;
            this.message = {
              type: 'alert-danger',
              icon: 'fas fa-times-circle',
              text: 'There was an error creating the customer. Please try again.',
            };
          });
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
}

.card-header {
  display: flex;
  align-items: center;
  font-weight: 600;
}

.card-header i {
  margin-right: 10px;
}

.input-group-text {
  background-color: #e9ecef;
  border: 1px solid #ccc;
}

.input-rounded {
  border-radius: 20px;
}

.btn {
  border-radius: 20px;
  transition: background-color 0.3s ease;
}

.btn:hover {
  background-color: #007bff;
}

.alert {
  margin-top: 20px;
  border-radius: 10px;
  padding: 10px;
}

.alert-success {
  background-color: #d4edda;
  color: #155724;
}

.alert-danger {
  background-color: #f8d7da;
  color: #721c24;
}

.text-danger {
  font-size: 0.9rem;
}

.text-danger i {
  font-size: 1.2rem;
}
</style>
