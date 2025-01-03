<template>
    <div>
      <h1>Create Discount</h1>
      <form @submit.prevent="createDiscount">
        <div class="row mb-3">
          <div class="col-md-6">
            <label for="discountName" class="form-label">Name</label>
            <input
              type="text"
              class="form-control"
              id="discountName"
              v-model="newDiscount.name"
              :class="{ 'is-invalid': errors.name }"
              required
            />
            <div class="invalid-feedback" v-if="errors.name">{{ errors.name[0] }}</div>
          </div>
          <div class="col-md-6">
            <label for="discountCode" class="form-label">Code</label>
            <input
              type="text"
              class="form-control"
              id="discountCode"
              v-model="newDiscount.code"
              :class="{ 'is-invalid': errors.code }"
              required
            />
            <div class="invalid-feedback" v-if="errors.code">{{ errors.code[0] }}</div>
          </div>
        </div>
        <div class="row mb-3">
          <div class="col-md-6">
            <label for="discountType" class="form-label">Type</label>
            <select
              class="form-select"
              id="discountType"
              v-model="newDiscount.discount_type_id"
              :class="{ 'is-invalid': errors.discount_type_id }"
              required
            >
              <option v-for="type in discountTypes" :key="type.id" :value="type.id">{{ type.name }}</option>
            </select>
            <div class="invalid-feedback" v-if="errors.discount_type_id">{{ errors.discount_type_id[0] }}</div>
          </div>
          <div class="col-md-6">
            <label for="discountValue" class="form-label">Value</label>
            <input
              type="number"
              class="form-control"
              id="discountValue"
              v-model="newDiscount.value"
              :class="{ 'is-invalid': errors.value }"
              required
              min="0"
            />
            <div class="invalid-feedback" v-if="errors.value">{{ errors.value[0] }}</div>
          </div>
        </div>
        <div class="row mb-3">
          <div class="col-md-6">
            <label for="discountStatus" class="form-label">Status</label>
            <select
              class="form-select"
              id="discountStatus"
              v-model="newDiscount.status"
              :class="{ 'is-invalid': errors.status }"
              required
            >
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
            <div class="invalid-feedback" v-if="errors.status">{{ errors.status[0] }}</div>
          </div>
          <div class="col-md-6">
            <label for="discountStartDate" class="form-label">Start Date</label>
            <input
              type="date"
              class="form-control"
              id="discountStartDate"
              v-model="newDiscount.start_date"
              :class="{ 'is-invalid': errors.start_date }"
              required
            />
            <div class="invalid-feedback" v-if="errors.start_date">{{ errors.start_date[0] }}</div>
          </div>
        </div>
        <div class="row mb-3">
          <div class="col-md-6">
            <label for="discountEndDate" class="form-label">End Date</label>
            <input
              type="date"
              class="form-control"
              id="discountEndDate"
              v-model="newDiscount.end_date"
              :class="{ 'is-invalid': errors.end_date }"
              required
            />
            <div class="invalid-feedback" v-if="errors.end_date">{{ errors.end_date[0] }}</div>
          </div>
        </div>
        <button type="submit" class="btn btn-success">
          <i class="fas fa-plus"></i> Create Discount
        </button>
      </form>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    name: 'DiscountCreation',
    data() {
      return {
        newDiscount: {
          name: '',
          code: '',
          discount_type_id: '',
          value: 0,
          status: 'active',
          start_date: '',
          end_date: ''
        },
        discountTypes: [],
        errors: {}
      };
    },
    created() {
      this.fetchDiscountTypes();
    },
    methods: {
      async fetchDiscountTypes() {
        try {
          const response = await axios.get('/api/discount_types');
          this.discountTypes = response.data;
        } catch (error) {
          console.error('Error fetching discount types:', error);
        }
      },
      async createDiscount() {
        try {
          const response = await axios.post('/api/discounts', this.newDiscount);
          console.log('Discount created:', response.data);
          alert('Discount created successfully!');
          this.resetForm();
        } catch (error) {
          if (error.response && error.response.status === 422) {
            this.errors = error.response.data.errors;
          } else {
            console.error('Error creating discount:', error);
          }
        }
      },
      resetForm() {
        this.newDiscount = {
          name: '',
          code: '',
          discount_type_id: '',
          value: 0,
          status: 'active',
          start_date: '',
          end_date: ''
        };
        this.errors = {};
      }
    }
  };
  </script>
  
  <style scoped>
  .mb-3 {
    margin-bottom: 1rem;
  }
  .is-invalid {
    border-color: #dc3545;
  }
  .invalid-feedback {
    display: block;
  }
  </style>
  