<template>
    <div class="container mt-5">
        <h2>Create Company</h2>
        <form @submit.prevent="submitForm">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" v-model="company.name" required>
                <div v-if="errors.name" class="text-danger">{{ errors.name }}</div>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <input type="text" class="form-control" id="address" v-model="company.address" required>
                <div v-if="errors.address" class="text-danger">{{ errors.address }}</div>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Phone Number</label>
                <input type="text" class="form-control" id="phone" v-model="company.phone" required>
                <div v-if="errors.phone" class="text-danger">{{ errors.phone }}</div>
            </div>
            <div class="mb-3">
                <label for="tin" class="form-label">TIN</label>
                <input type="text" class="form-control" id="tin" v-model="company.tin" required>
                <div v-if="errors.tin" class="text-danger">{{ errors.tin }}</div>
            </div>
            <button type="submit" class="btn btn-primary" :disabled="isLoading">
                <i v-if="isLoading" class="fa fa-spinner fa-spin"></i>
                <i v-else class="fa fa-save"></i> Submit
            </button>
        </form>

        <!-- Bootstrap Toast Notifications -->
        <div class="toast-container position-fixed bottom-0 end-0 p-3" v-if="showToast">
            <div v-for="(toast, index) in toasts" :key="index" class="toast show" :class="toast.type === 'success' ? 'bg-success text-white' : 'bg-danger text-white'" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <strong class="me-auto">{{ toast.type === 'success' ? 'Success' : 'Error' }}</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close" @click="removeToast(index)"></button>
                </div>
                <div class="toast-body">
                    {{ toast.message }}
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
            company: {
                name: '',
                address: '',
                phone: '',
                tin: ''
            },
            errors: {
                name: '',
                address: '',
                phone: '',
                tin: ''
            },
            isLoading: false,
            showToast: true,
            toasts: []
        };
    },
    methods: {
        validateForm() {
            // Reset errors
            this.errors = {
                name: '',
                address: '',
                phone: '',
                tin: ''
            };

            let isValid = true;

            // Check if all fields are filled
            if (!this.company.name) {
                this.errors.name = 'Name is required.';
                isValid = false;
            }
            if (!this.company.address) {
                this.errors.address = 'Address is required.';
                isValid = false;
            }
            if (!this.company.phone) {
                this.errors.phone = 'Phone number is required.';
                isValid = false;
            } else {
                // Validate phone number format (example pattern)
                const phonePattern = /^[0-9]{10}$/;  // Simple validation for a 10-digit phone number
                if (!phonePattern.test(this.company.phone)) {
                    this.errors.phone = 'Please enter a valid phone number (10 digits).';
                    isValid = false;
                }
            }
            if (!this.company.tin) {
                this.errors.tin = 'TIN is required.';
                isValid = false;
            } else {
                // Validate TIN format (example pattern)
                const tinPattern = /^[1-9]{3}-[1-9]{3}-[1-9]{3}$/;  // Simple alphanumeric TIN validation
                if (!tinPattern.test(this.company.tin)) {
                    this.errors.tin = 'Please enter a valid TIN (9 alphanumeric characters).';
                    isValid = false;
                }
            }

            return isValid;
        },
        submitForm() {
            if (!this.validateForm()) {
                return;
            }

            this.isLoading = true;

            axios.post('/api/companies', this.company)
                .then(response => {
                    this.showToastMessage('Company registered successfully!', 'success');
                    this.resetForm();
                   window.location.href='/login'
                })
                .catch(error => {
                    this.showToastMessage('There was an error registering the company.', 'error');
                    console.error(error);
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },
        resetForm() {
            this.company = {
                name: '',
                address: '',
                phone: '',
                tin: ''
            };
            this.errors = {
                name: '',
                address: '',
                phone: '',
                tin: ''
            };
        },
        showToastMessage(message, type) {
            this.toasts.push({message, type});
            setTimeout(() => {
                this.showToast = true;
            }, 100); // Delay to show toast immediately after it's added
        },
        removeToast(index) {
            this.toasts.splice(index, 1);
        }
    }
};
</script>

<style scoped>
.mt-5 {
    margin-top: 3rem;
}

.text-danger {
    font-size: 0.875rem;
}

.toast-container {
    z-index: 9999;
}
</style>
