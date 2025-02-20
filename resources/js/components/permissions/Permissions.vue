<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";

const permissions = ref([]);
const loading = ref(false);
const modalOpen = ref(false);
const editMode = ref(false);
const selectedPermissions = ref([]);
const permissionModal = ref(false);

// Form data
const form = ref({
  id: null,
  name: "",
  guard_name: "web",
});

// Fetch permissions
const fetchPermissions = async () => {
  try {
    loading.value = true;
    const response = await axios.get("/api/v1/permissions");
    permissions.value = response.data.permissions;
  } catch (error) {
    toast.error("Failed to fetch permissions");
  } finally {
    loading.value = false;
  }
};

// Open modal for create/update
const openModal = (permission = null) => {
  if (permission) {
    form.value = { ...permission };
    editMode.value = true;
  } else {
    form.value = { id: null, name: "", guard_name: "web" };
    editMode.value = false;
  }
  permissionModal.value = true;
};

// Save permission (Create or Update)
const savePermission = async () => {
  try {
    if (editMode.value) {
      await axios.put(`/api/v1/permissions/${form.value.id}`, form.value);
      toast.success("Permission updated successfully!");
    } else {
      await axios.post("/api/v1/permissions", form.value);
      toast.success("Permission added successfully!");
    }
    fetchPermissions();
    permissionModal.value = false;
  } catch (error) {
    toast.error("Failed to save permission");
  }
};

// Delete permission
const deletePermission = async (id) => {
  if (!confirm("Are you sure you want to delete this permission?")) return;

  try {
    await axios.delete(`/api/v1/permissions/${id}`);
    toast.success("Permission deleted successfully!");
    fetchPermissions();
  } catch (error) {
    toast.error("Failed to delete permission");
  }
};

const closePermissionsDialog = () => {
  permissionModal.value = false;
};

const formatPermissionName = (name) => {
  return name.replace(/_/g, " ").toUpperCase();
};

// Fetch permissions on component mount
onMounted(fetchPermissions);
</script>

<template>
  <v-card>
    <v-card-title class="d-flex justify-space-between align-center">
      Manage Permissions
      <v-btn color="primary" @click="openModal()">Add Permission</v-btn>
    </v-card-title>
    <v-card-text>
      <v-row justify="start">
        <v-col v-for="permission in permissions" :key="permission.id" cols="12" md="4">
          <v-checkbox
            v-model="selectedPermissions"
            :label="formatPermissionName(permission.name)"
            :value="permission.id"
          ></v-checkbox>
          <v-btn color="primary" small @click="openModal(permission)">Edit</v-btn>
          <v-btn color="error" small @click="deletePermission(permission.id)">Delete</v-btn>
        </v-col>
      </v-row>
    </v-card-text>

    <!-- Permission Modal -->
    <v-dialog v-model="permissionModal" max-width="500px">
      <v-card>
        <v-card-title>{{ editMode ? "Edit Permission" : "Add Permission" }}</v-card-title>
        <v-card-text>
          <v-text-field v-model="form.name" label="Name" prepend-icon="mdi-lock"></v-text-field>
          <v-text-field v-model="form.guard_name" label="Guard" prepend-icon="mdi-security"></v-text-field>
        </v-card-text>
        <v-card-actions class="justify-end">
          <v-btn color="error" @click="closePermissionsDialog">Close</v-btn>
          <v-btn color="success" @click="savePermission">{{ editMode ? "Update" : "Submit" }}</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-card>
</template>

<style scoped>
.loader {
  border: 4px solid #f3f3f3;
  border-top: 4px solid #3498db;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}
</style>
