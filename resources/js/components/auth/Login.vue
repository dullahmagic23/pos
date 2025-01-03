<template>
  <div id="layoutAuthentication">
    <div id="layoutAuthentication_content">
      <main>
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-5">
              <div class="card shadow-lg border-0 rounded-lg mt-5">
                <div class="card-header">
                  <h3 class="text-center font-weight-light my-4">Login</h3>
                </div>
                <div class="card-body">
                  <form @submit.prevent="handleLogin">
                    <div class="form-floating mb-3">
                      <input v-model="email" class="form-control" id="inputEmail" type="email" placeholder="name@example.com" />
                      <label for="inputEmail">
                        <i class="fas fa-envelope"></i> Email address
                      </label>
                      <div v-if="errors.email" class="text-danger">{{ errors.email }}</div>
                    </div>
                    <div class="form-floating mb-3">
                      <input v-model="password" class="form-control" id="inputPassword" type="password" placeholder="Password" />
                      <label for="inputPassword">
                        <i class="fas fa-lock"></i> Password
                      </label>
                      <div v-if="errors.password" class="text-danger">{{ errors.password }}</div>
                    </div>
                    <div class="form-check mb-3">
                      <input v-model="remember" class="form-check-input" id="inputRememberPassword" type="checkbox" />
                      <label class="form-check-label" for="inputRememberPassword">Remember Password</label>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                      <a class="small" href="#">Forgot Password?</a>
                      <button class="btn btn-primary" type="submit" :disabled="loading">
                        <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Login
                      </button>
                    </div>
                  </form>
                  <div v-if="generalError" class="mt-3 alert alert-danger">{{ generalError }}</div>
                </div>
                <div class="card-footer text-center py-3">
                  <div class="small">
                    <p>&copy; RoyalTech Services {{ new Date().getFullYear() }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'Login',
  data() {
    return {
      email: '',
      password: '',
      remember: false,
      errors: {},
      loading: false,
      generalError: null
    };
  },
  methods: {
    async handleLogin() {
      this.errors = {};
      this.generalError = null;

      // Basic validation before sending the request
      if (!this.email) {
        this.errors.email = 'Email is required.';
      }
      if (!this.password) {
        this.errors.password = 'Password is required.';
      }
      if (Object.keys(this.errors).length > 0) {
        return;
      }

      this.loading = true;

      try {
        const response = await axios.post('/login', {
          email: this.email,
          password: this.password,
          remember: this.remember
        });

        // Simulate storing the authentication token in localStorage
        localStorage.setItem('authToken', response.data.token);

        // Handle successful login (refresh the page in this case)
        window.location.reload();
      } catch (error) {
        this.loading = false;
        if (error.response && error.response.data) {
          this.generalError = error.response.data.message || 'Login failed, please try again.';
        } else {
          this.generalError = 'An unexpected error occurred. Please try again later.';
        }
      } finally {
        this.loading = false;
      }
    }
  }
};
</script>

<style>

/* Add any custom styles here */
</style>
