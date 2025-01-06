<template>
    <div class="container mt-5">
        <h2>Enter Product Key</h2>
        <form @submit.prevent="submitForm">
            <div class="mb-3">
                <label for="productKey" class="form-label">Product Key</label>
                <input
                    type="text"
                    class="form-control"
                    id="productKey"
                    v-model="productKey"
                    @input="formatKey"
                    placeholder="XXXX-XXXX-XXXX"
                    maxlength="14"
                    required
                />
                <div v-if="validationMessage" class="text-danger mt-1">{{ validationMessage }}</div>
            </div>
            <button type="submit" class="btn btn-primary" :disabled="!isValid || loading">
                <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                <span v-else>
                    <i class="fa fa-save"></i> Submit
                </span>
            </button>
            <div v-if="successMessage" class="text-success mt-3">{{ successMessage }}</div>
        </form>
    </div>
</template>

<script>
import axios from "axios";

export default {
    data() {
        return {
            productKey: "",
            validationMessage: "",
            isValid: false,
            loading: false,
            successMessage: "",
        };
    },
    methods: {
        formatKey() {
            // Remove all non-alphanumeric characters
            let rawKey = this.productKey.replace(/[^A-Za-z0-9]/g, "");

            // Convert to uppercase
            rawKey = rawKey.toUpperCase();

            // Add dashes every 4 characters
            const formattedKey = rawKey.match(/.{1,4}/g)?.join("-") || "";

            // Update the model
            this.productKey = formattedKey;

            // Validate the formatted key
            this.validateKey();
        },
        validateKey() {
            const keyFormat = /^[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}$/;

            if (!keyFormat.test(this.productKey)) {
                this.validationMessage = "Product key must be in the format XXXX-XXXX-XXXX";
                this.isValid = false;
            } else {
                this.validationMessage = "";
                this.isValid = true;
            }
        },
        async submitForm() {
            if (!this.isValid) {
                alert("Please enter a valid product key.");
                return;
            }

            this.loading = true;
            this.successMessage = "";
            try {
                const response = await axios.post("/api/product-keys", { key: this.productKey });
                if (response.data.status === 200) {
                    this.successMessage = response.data.message;
                    this.productKey = ""; // Clear the input
                    this.isValid = false; // Reset form state
                    alert(this.successMessage)
                    window.location.href = '/login'
                } else {
                    this.validationMessage = response.data.message;
                    this.isValid = false; // Reset form state
                }
            } catch (error) {
                alert(error.response.data.message);
                console.error(error);
            } finally {
                this.loading = false;
            }
        },
    },
};
</script>

<style>
.mt-5 {
    margin-top: 3rem;
}

.spinner-border {
    display: inline-block;
    margin-right: 5px;
}
</style>
