<template>
  <div class="container-fluid mt-5">
    <h2 class="mb-4"><i class="fas fa-truck"></i> Register Supplier</h2>
    <div class="card">
      <div class="card-header bg-primary text-white">
        <i class="fas fa-info-circle"></i> Supplier Information
      </div>
      <div class="card-body">
        <form @submit.prevent="validateAndRegisterSupplier">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="supplierName" class="form-label">Name of Supplier</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-building"></i></span>
                <input
                    type="text"
                    class="form-control"
                    id="supplierName"
                    v-model="supplier.name"
                    required
                />
              </div>
              <div v-if="errors.name" class="text-danger">{{ errors.name }}</div>
            </div>
            <div class="col-md-6 mb-3">
              <label for="supplierAddress" class="form-label">Address</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                <input
                    type="text"
                    class="form-control"
                    id="supplierAddress"
                    v-model="supplier.address"
                    required
                />
              </div>
              <div v-if="errors.address" class="text-danger">{{ errors.address }}</div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="supplierPhone" class="form-label">Phone Number</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                <input
                    type="tel"
                    class="form-control"
                    id="supplierPhone"
                    v-model="supplier.phone"
                    required
                />
              </div>
              <div v-if="errors.phone" class="text-danger">{{ errors.phone }}</div>
            </div>
            <div class="col-md-6 mb-3">
              <label for="supplierTin" class="form-label">TIN</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                <input
                    type="text"
                    class="form-control"
                    id="supplierTin"
                    v-model="supplier.tin"
                />
              </div>
              <div v-if="errors.tin" class="text-danger">{{ errors.tin }}</div>
            </div>
          </div>
          <button type="submit" class="btn btn-primary" :disabled="loading">
            <i :class="loading ? 'fas fa-spinner fa-spin' : 'fas fa-save'"></i>
            {{ loading ? 'Registering...' : 'Register Supplier' }}
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      supplier: {
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
    validateAndRegisterSupplier() {
      this.errors = {};

      if (!this.supplier.name) {
        this.errors.name = 'Name of Supplier is required.';
      }

      if (!this.supplier.address) {
        this.errors.address = 'Address is required.';
      }

      if (!this.supplier.phone) {
        this.errors.phone = 'Phone number is required.';
      } else if (!this.isValidPhone(this.supplier.phone)) {
        this.errors.phone = 'Phone number is not valid.';
      }

      if (!this.supplier.tin) {
        this.errors.tin = 'Tin number is required.';
      }

      if (Object.keys(this.errors).length === 0) {
        this.registerSupplier();
      }
    },
    isValidPhone(phone) {
      const re = /^[0-9\-\+]{9,15}$/;
      return re.test(phone);
    },
    registerSupplier() {
      this.loading = true;
      axios.post('/api/suppliers', this.supplier)
          .then(() => {
            this.$notify('Supplier registered successfully');
            this.supplier = { name: '', address: '', phone: '', tin: '' };
            this.loading = false;
          })
          .catch(error => {
            console.error('Error registering supplier:', error);
            this.loading = false;
          });
    }
  }
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
