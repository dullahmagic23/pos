<template>
  <form @submit.prevent="handleSubmit">
    <div class="mb-3">
      <label for="roleName" class="form-label">Role Name</label>
      <input
          type="text"
          class="form-control"
          id="roleName"
          v-model="roleData.name"
          :disabled="loading"
          required
      />
      <div v-if="errors.name" class="text-danger">{{ errors.name }}</div>
    </div>
    <button type="submit" class="btn btn-primary" :disabled="loading">
      <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
      <i class="fas fa-save"></i> {{ isEditing ? "Update Role" : "Create Role" }}
    </button>
  </form>
</template>

<script>
export default {
  props: {
    role: {
      type: Object,
      required: true,
    },
    isEditing: {
      type: Boolean,
      default: false,
    },
    loading: {
      type: Boolean,
      default: false,
    },
    errors: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    return {
      roleData: {
        name: this.role.name || "",
      },
    };
  },
  watch: {
    role: {
      deep: true,
      handler(newRole) {
        this.roleData = { ...newRole };
      },
    },
  },
  methods: {
    handleSubmit() {
      if (!this.validateForm()) return;
      this.$emit("submit", this.roleData);
    },
    validateForm() {
      const errors = {};
      if (!this.roleData.name) {
        errors.name = "Role name is required.";
      } else if (this.roleData.name.length < 3) {
        errors.name = "Role name must be at least 3 characters long.";
      }
      this.$emit("update:errors", errors);
      return Object.keys(errors).length === 0;
    },
  },
};
</script>

<style scoped>
.text-danger {
  font-size: 0.875rem;
}
</style>
