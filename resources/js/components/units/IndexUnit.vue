<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h2>Product Units</h2>
      <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createModal">
        <i class="fas fa-plus"></i> Add Unit
      </button>
    </div>

    <table class="table table-striped mt-3">
      <thead>
      <tr>
        <th scope="col">#</th>
        <th scope="col">Unit Name</th>
        <th scope="col">Actions</th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="(unit, index) in units" :key="unit.id">
        <th scope="row">{{ index + 1 }}</th>
        <td>{{ unit.name }}</td>
        <td>
          <button @click="editUnit(unit)" class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#editModal">
            <i class="fas fa-edit"></i> Edit
          </button>
        </td>
      </tr>
      </tbody>
    </table>

    <!-- Create Unit Modal -->
    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="createModalLabel">Add New Unit</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label for="unitName" class="form-label">Unit Name</label>
              <input type="text" id="unitName" v-model="newUnit.name" class="form-control" />
              <div v-if="errors.name" class="text-danger">{{ errors.name }}</div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" @click="addUnit">Add Unit</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Unit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editModalLabel">Edit Unit</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label for="editUnitName" class="form-label">Unit Name</label>
              <input type="text" id="editUnitName" v-model="editUnitData.name" class="form-control" />
              <div v-if="editErrors.name" class="text-danger">{{ editErrors.name }}</div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" @click="updateUnit">Save changes</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      units: [],
      newUnit: { name: '' },
      editUnitData: { name: '' },
      errors: {},
      editErrors: {},
      editIndex: null
    };
  },
  mounted() {
    this.fetchUnits();
  },
  methods: {
    async fetchUnits() {
      try {
        const response = await axios.get('/api/units');
        this.units = response.data;
      } catch (error) {
        console.error('Error fetching units:', error);
      }
    },
    validate(unit) {
      let errors = {};
      if (!unit.name) {
        errors.name = 'Unit name is required';
      }
      return errors;
    },
    async addUnit() {
      this.errors = this.validate(this.newUnit);
      if (Object.keys(this.errors).length === 0) {
        try {
          const response = await axios.post('/api/units', this.newUnit);
          this.units.push(response.data);
          this.newUnit.name = '';
          document.querySelector('#createModal .btn-close').click();
        } catch (error) {
          console.error('Error adding unit:', error);
        }
      }
    },
    editUnit(unit) {
      this.editIndex = this.units.indexOf(unit);
      this.editUnitData = { ...unit };
      this.editErrors = {};
    },
    async updateUnit() {
      this.editErrors = this.validate(this.editUnitData);
      if (Object.keys(this.editErrors).length === 0) {
        try {
          axios.put(`/api/units/${this.editUnitData.id}`, this.editUnitData)
          .then(response => {
            this.editIndex = null;
            this.editUnitData.name = '';
            this.fetchUnits()
            this.$notify('Unit updated successfully');
          })
          // Close modal
          document.querySelector('#editModal .btn-close').click();
        } catch (error) {
          console.error('Error updating unit:', error);
        }
      }
    }
  }
};
</script>
