<template>
  <div class="container-fluid mt-5">
    <h2 class="mb-4"><i class="fas fa-user-plus"></i> Create User</h2>
    <div class="card">
      <div class="card-header bg-primary text-white">
        <i class="fas fa-user-edit"></i> User Information
      </div>
      <div class="card-body">
        <form @submit.prevent="createUser">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="name" class="form-label">Name</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-user"></i></span>
                <input
                    type="text"
                    class="form-control"
                    id="name"
                    v-model="form.name"
                    required
                />
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <label for="email" class="form-label">Email</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                <input
                    type="email"
                    class="form-control"
                    id="email"
                    v-model="form.email"
                    required
                />
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="password" class="form-label">Password</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                <input
                    type="password"
                    class="form-control"
                    id="password"
                    v-model="form.password"
                    required
                />
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <label for="password_confirmation" class="form-label">Confirm Password</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                <input
                    type="password"
                    class="form-control"
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    required
                />
              </div>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Select Roles</label>
            <div class="form-check">
              <input
                  class="form-check-input"
                  type="checkbox"
                  id="role_standard_user"
                  value="2"
                  v-model="form.roles"
              />
              <label class="form-check-label" for="role_standard_user">
                <i class="fas fa-user-tag"></i> Standard User
              </label>
            </div>
            <div class="form-check">
              <input
                  class="form-check-input"
                  type="checkbox"
                  id="role_administrator"
                  value="1"
                  v-model="form.roles"
              />
              <label class="form-check-label" for="role_administrator">
                <i class="fas fa-user-shield"></i> Administrator
              </label>
            </div>
          </div>
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Create User
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
      form: {
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        roles: [],
      },
    };
  },
  methods: {
    createUser() {
      if (this.form.password !== this.form.password_confirmation) {
        this.$notify("Passwords don't match!")
        return;
      }

      // Prepare the data to be sent to the server
      const userData = {
        name: this.form.name,
        email: this.form.email,
        password: this.form.password,
        password_confirmation: this.form.password_confirmation,
        roles: this.form.roles,
      };

      // Send the data to the backend server using Axios
      axios
          .post('/users', userData) // Replace '/api/users' with your backend API endpoint
          .then(response => {
            // Handle successful response
            this.$notify("User created successfully!")

            // Reset the form fields after successful creation
            this.form = {
              name: '',
              email: '',
              password: '',
              password_confirmation: '',
              roles: [],
            };

            // You can add more logic here, like redirecting the user
          })
          .catch(error => {
            // Handle errors
            console.error('An error occurred:', error);
            if (error.response && error.response.data) {
              // Display specific error from the server
              this.$notify(error.response.data.message || 'Error creating user');
            } else {
              // Handle unexpected errors
              this.$notify('An unexpected error occurred.');
            }
          });
    },
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
