<template>
    <div>
      <h1>Discounts List</h1>
      <table>
        <thead>
          <tr>
            <th>Name</th>
            <th>Code</th>
            <th>Type</th>
            <th>Value</th>
            <th>Status</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="discount in discounts" :key="discount.id">
            <td>{{ discount.name }}</td>
            <td>{{ discount.code }}</td>
            <td>{{ discount.discount_type ? discount.discount_type.name : 'N/A' }}</td>
            <td>{{ discount.value }}</td>
            <td>{{ discount.status }}</td>
            <td>{{ discount.start_date }}</td>
            <td>{{ discount.end_date }}</td>
            <td>
              <button @click="editDiscount(discount)" class="btn btn-primary">Edit</button>
              <button @click="deleteDiscount(discount.id)" class="btn btn-danger">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
  
      <!-- Edit Discount Modal -->
      <div class="modal fade" id="editDiscountModal" tabindex="-1" aria-labelledby="editDiscountModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="editDiscountModalLabel">Edit Discount</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <form>
                <div class="mb-3">
                  <label for="discountName" class="form-label">Name</label>
                  <input type="text" class="form-control" id="discountName" v-model="currentDiscount.name">
                </div>
                <div class="mb-3">
                  <label for="discountCode" class="form-label">Code</label>
                  <input type="text" class="form-control" id="discountCode" v-model="currentDiscount.code">
                </div>
                <div class="mb-3">
                  <label for="discountType" class="form-label">Type</label>
                  <select class="form-select" id="discountType" v-model="currentDiscount.discount_type_id">
                    <option v-for="type in discountTypes" :key="type.id" :value="type.id">{{ type.name }}</option>
                  </select>
                </div>
                <div class="mb-3">
                  <label for="discountValue" class="form-label">Value</label>
                  <input type="number" class="form-control" id="discountValue" v-model="currentDiscount.value">
                </div>
                <div class="mb-3">
                  <label for="discountStatus" class="form-label">Status</label>
                  <select class="form-select" id="discountStatus" v-model="currentDiscount.status">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                  </select>
                </div>
                <div class="mb-3">
                  <label for="discountStartDate" class="form-label">Start Date</label>
                  <input type="date" class="form-control" id="discountStartDate" v-model="currentDiscount.start_date">
                </div>
                <div class="mb-3">
                  <label for="discountEndDate" class="form-label">End Date</label>
                  <input type="date" class="form-control" id="discountEndDate" v-model="currentDiscount.end_date">
                </div>
              </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary" @click="updateDiscount">Save changes</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  import { Modal } from 'bootstrap';
  
  export default {
    name: 'DiscountsList',
    data() {
      return {
        discounts: [],
        discountTypes: [],
        currentDiscount: {},
        modalInstance: null,
      };
    },
    created() {
      this.fetchDiscounts();
      this.fetchDiscountTypes();
    },
    methods: {
      async fetchDiscounts() {
        try {
          const response = await axios.get('/api/discounts');
          this.discounts = response.data;
        } catch (error) {
          console.error('Error fetching discounts:', error);
        }
      },
      async fetchDiscountTypes() {
        try {
          const response = await axios.get('/api/discount_types');
          this.discountTypes = response.data;
        } catch (error) {
          console.error('Error fetching discount types:', error);
        }
      },
      editDiscount(discount) {
        this.currentDiscount = { ...discount, discount_type_id: discount.discount_type ? discount.discount_type.id : null };
        this.modalInstance = new Modal(document.getElementById('editDiscountModal'));
        this.modalInstance.show();
      },
      async updateDiscount() {
        try {
          await axios.put(`/api/discounts/${this.currentDiscount.id}`, this.currentDiscount);
          this.fetchDiscounts(); // Refresh the discounts list
          this.modalInstance.hide();
          alert('Discount updated successfully!');
        } catch (error) {
          console.error('Error updating discount:', error);
        }
      },
      async deleteDiscount(id) {
        if (confirm('Are you sure you want to delete this discount?')) {
          try {
            await axios.delete(`/api/discounts/${id}`);
            this.fetchDiscounts(); // Refresh the discounts list
            alert('Discount deleted successfully!');
          } catch (error) {
            console.error('Error deleting discount:', error);
          }
        }
      },
    },
  };
  </script>
  
  <style scoped>
  table {
    width: 100%;
    border-collapse: collapse;
  }
  th, td {
    border: 1px solid #ddd;
    padding: 8px;
  }
  th {
    background-color: #f2f2f2;
  }
  </style>
  