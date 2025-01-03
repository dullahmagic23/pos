<template>
  <div class="container-fluid mt-5">
    <h2 class="mb-4"><i class="fas fa-users-cog"></i> Manage Users</h2>
    <div class="card">
      <div class="card-header bg-primary text-white">
        <i class="fas fa-user"></i> User List
      </div>
      <div class="card-body">
        <table class="table table-striped">
          <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Roles</th>
            <th>Actions</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="user in users" :key="user.id">
            <td>{{ user.name }}</td>
            <td>{{ user.email }}</td>
            <td>{{ getUserRoles(user) }}</td>
            <td>
              <button
                  class="btn btn-sm btn-primary"
                  @click="selectUser(user)"
                  data-bs-toggle="modal"
                  data-bs-target="#editUserModal"
              >
                <i class="fas fa-edit"></i> Edit
              </button>
            </td>
          </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Edit User Modal -->
    <div
        class="modal  fade"
        id="editUserModal"
        tabindex="-1"
        aria-labelledby="editUserModalLabel"
        aria-hidden="true"
    >
      <div class="modal-dialog modal-fullscreen-lg-down">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="modal"
                aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="validateAndSubmit">
              <div class="mb-3">
                <label for="editName" class="form-label">Name</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="fas fa-user"></i></span>
                  <input
                      type="text"
                      class="form-control"
                      id="editName"
                      v-model="selectedUser.name"
                      required
                  />
                </div>
                <div v-if="errors.name" class="text-danger">{{ errors.name }}</div>
              </div>
              <div class="mb-3">
                <label for="editEmail" class="form-label">Email</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                  <input
                      type="email"
                      class="form-control"
                      id="editEmail"
                      v-model="selectedUser.email"
                      required
                  />
                </div>
                <div v-if="errors.email" class="text-danger">{{ errors.email }}</div>
              </div>
              <div class="mb-3">
                <label class="form-label">Roles</label>
                <div class="form-check" v-for="role in allRoles" :key="role.id">
                  <input
                      class="form-check-input"
                      type="checkbox"
                      :id="'editRole_' + role.id"
                      :value="role.id"
                      v-model="selectedUser.role_ids"
                  />
                  <label class="form-check-label" :for="'editRole_' + role.id">
                    <i :class="getRoleIcon(role.name)"></i> {{ role.name }}
                  </label>
                </div>
                <div v-if="errors.roles" class="text-danger">{{ errors.roles }}</div>
              </div>
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update User
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
      users: [],
      allRoles: [], // To store all available roles
      selectedUser: {
        name: '',
        email: '',
        role_ids: [], // To store selected role IDs
      },
      errors: {},
    };
  },
  methods: {
    fetchUsers() {
      axios.get('/api/users')
          .then(response => {
            this.users = response.data;
          })
          .catch(error => {
            console.error('Error fetching users:', error);
          });
    },
    fetchRoles() {
      axios.get('/api/roles')
          .then(response => {
            this.allRoles = response.data;
          })
          .catch(error => {
            console.error('Error fetching roles:', error);
          });
    },
    selectUser(user) {
      this.selectedUser = {
        ...user,
        role_ids: user.roles.map(role => role.id)
      };
      this.errors = {}; // Clear previous errors
    },
    validateAndSubmit() {
      this.errors = {};

      if (!this.selectedUser.name) {
        this.errors.name = 'Name is required.';
      }

      if (!this.selectedUser.email) {
        this.errors.email = 'Email is required.';
      } else if (!this.isValidEmail(this.selectedUser.email)) {
        this.errors.email = 'Email is not valid.';
      }

      if (this.selectedUser.role_ids.length === 0) {
        this.errors.roles = 'At least one role must be selected.';
      }

      if (Object.keys(this.errors).length === 0) {
        this.updateUser();
      }
    },
    isValidEmail(email) {
      const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return re.test(email);
    },
    updateUser() {
      axios.put(`/api/users/${this.selectedUser.id}`, this.selectedUser)
          .then(() => {
            alert('User updated successfully');
            window.location.reload();

          })
          .catch(error => {
            console.error('Error updating user:', error);
          });
    },
    getUserRoles(user) {
      return user.roles.map(role => role.name).join(', ');
    },
    getRoleIcon(roleName) {
      switch (roleName) {
        case 'administrator':
          return 'fas fa-user-shield';
        case 'standard_user':
          return 'fas fa-user-tag';
        default:
          return 'fas fa-user';
      }
    }
  },
  mounted() {
    this.fetchUsers();
    this.fetchRoles(); // Fetch all available roles
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
