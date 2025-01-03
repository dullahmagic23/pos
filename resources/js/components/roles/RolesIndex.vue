<template>
  <div class="container-fluid mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2><i class="fas fa-user-shield"></i> Manage Roles</h2>
      <button class="btn btn-primary" @click="openCreateModal">
        <i class="fas fa-plus"></i> Create Role
      </button>
    </div>
    <div class="card">
      <div class="card-header bg-primary text-white">
        <i class="fas fa-list"></i> Role List
      </div>
      <div class="card-body">
        <table class="table table-striped">
          <thead>
          <tr>
            <th>Name</th>
            <th>Actions</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="role in roles" :key="role.id">
            <td>{{ role.name }}</td>
            <td>
              <button
                  class="btn btn-sm btn-primary"
                  @click="selectRole(role)"
                  data-bs-toggle="modal"
                  data-bs-target="#editRoleModal"
              >
                <i class="fas fa-edit"></i> Edit
              </button>
            </td>
          </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Create Role Modal -->
    <div
        class="modal fade"
        id="createRoleModal"
        tabindex="-1"
        aria-labelledby="createRoleModalLabel"
        aria-hidden="true"
    >
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="createRoleModalLabel">Create Role</h5>
            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="modal"
                aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="validateAndCreateRole">
              <div class="mb-3">
                <label for="createRoleName" class="form-label">Name</label>
                <input
                    type="text"
                    class="form-control"
                    id="createRoleName"
                    v-model="newRole.name"
                    required
                />
                <div v-if="errors.name" class="text-danger">{{ errors.name }}</div>
              </div>
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Create Role
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Role Modal -->
    <div
        class="modal fade"
        id="editRoleModal"
        tabindex="-1"
        aria-labelledby="editRoleModalLabel"
        aria-hidden="true"
    >
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editRoleModalLabel">Edit Role</h5>
            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="modal"
                aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="validateAndUpdateRole">
              <div class="mb-3">
                <label for="editRoleName" class="form-label">Name</label>
                <input
                    type="text"
                    class="form-control"
                    id="editRoleName"
                    v-model="selectedRole.name"
                    required
                />
                <div v-if="errors.name" class="text-danger">{{ errors.name }}</div>
              </div>
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update Role
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {Modal} from 'bootstrap';

export default {
  data() {
    return {
      roles: [],
      newRole: {
        name: '',
      },
      selectedRole: {
        name: '',
      },
      errors: {},
    };
  },
  methods: {
    fetchRoles() {
      axios.get('/api/roles')
          .then(response => {
            this.roles = response.data;
          })
          .catch(error => {
            console.error('Error fetching roles:', error);
          });
    },
    openCreateModal() {
      this.newRole = {name: ''};
      this.errors = {};
      const createRoleModal = new Modal(document.getElementById('createRoleModal'));
      createRoleModal.show();
    },
    selectRole(role) {
      this.selectedRole = {...role};
      this.errors = {};
      const editRoleModal = new Modal(document.getElementById('editRoleModal'));
      editRoleModal.show();
    },
    validateAndCreateRole() {
      this.errors = {};

      if (!this.newRole.name) {
        this.errors.name = 'Name is required.';
      }

      if (Object.keys(this.errors).length === 0) {
        this.createRole();
      }
    },
    createRole() {
      axios.post('/api/roles', this.newRole)
          .then(() => {
            alert('Role created successfully');
            this.fetchRoles();
            document.addEventListener('DOMContentLoaded', () => {
              var createRoleModal = new bootstrap.Modal(document.getElementById('createRoleModal'))
              document.addEventListener('closeModal', () => {
                createRoleModal.hide();
              });
            });
          })
          .catch(error => {
            console.error('Error creating role:', error);
          });
    },
    validateAndUpdateRole() {
      this.errors = {};

      if (!this.selectedRole.name) {
        this.errors.name = 'Name is required.';
      }

      if (Object.keys(this.errors).length === 0) {
        this.updateRole();
      }
    },
    updateRole() {
      axios.put(`/api/roles/${this.selectedRole.id}`, this.selectedRole)
          .then(() => {
            this.$notify('Role updated successfully');
            window.location.reload()
          })
          .catch(error => {
            console.error('Error updating role:', error);
          });
    }
  },
  mounted() {
    this.fetchRoles();
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
</style>
