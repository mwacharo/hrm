<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
// import DataTable from "vue3-datatable";

// State variables
const roles = ref([]);
const loading = ref(false);
const modalOpen = ref(false);
const editMode = ref(false);

// Form data
const form = ref({
    id: null,
    name: "",
    guard_name: "web",
});


const headers = ref([
  { title: "ID", key: "id", sortable: true },
  { title: "Role Name", key: "name", sortable: true },
  { title: "Guard", key: "guard_name", sortable: true },
  { title: "Actions", key: "actions", sortable: false },
]);


// Fetch roles
const fetchRoles = async () => {
    try {
        loading.value = true;
        const response = await axios.get("/api/v1/roles");
        roles.value = response.data.roles;
    } catch (error) {
        toast.error("Failed to fetch roles");
    } finally {
        loading.value = false;
    }
};

// Open modal for create/update
const openModal = (role = null) => {
    if (role) {
        form.value = { ...role };
        editMode.value = true;
    } else {
        form.value = { id: null, name: "", guard_name: "web" };
        editMode.value = false;
    }
    modalOpen.value = true;
};

// Save role (Create or Update)
const saveRole = async () => {
    try {
        if (editMode.value) {
            await axios.put(`/api/v1/roles/${form.value.id}`, form.value);
            toast.success("Role updated successfully!");
        } else {
            await axios.post("/api/v1/roles", form.value);
            toast.success("Role added successfully!");
        }
        fetchRoles();
        modalOpen.value = false;
    } catch (error) {
        toast.error("Failed to save role");
    }
};

// Delete role
const deleteRole = async (id) => {
    if (!confirm("Are you sure you want to delete this role?")) return;

    try {
        await axios.delete(`/api/v1/roles/${id}`);
        toast.success("Role deleted successfully!");
        fetchRoles();
    } catch (error) {
        toast.error("Failed to delete role");
    }
};

// Fetch roles on component mount
onMounted(fetchRoles);
</script>

<template>
    <v-container>
        <v-card>
            <v-card-title class="d-flex justify-space-between">
                <span>Manage Roles</span>
                <v-btn color="primary" @click="openModal()">Add Role</v-btn>
            </v-card-title>

            <v-card-text>
                <v-data-table :headers="headers" :items="roles" item-value="id" :loading="loading" 
        >
                    <!-- Actions Column -->
                    <template v-slot:item.actions="{ item }">
                        <v-btn icon color="primary" @click="openModal(item)" >
                            <v-icon>mdi-pencil</v-icon>
                        </v-btn>
                        <v-btn icon color="error" @click="deleteRole(item.id)">
                            <v-icon>mdi-delete</v-icon>
                        </v-btn>
                    </template>
                </v-data-table>

            </v-card-text>
        </v-card>

        <!-- Role Form Dialog -->
        <v-dialog v-model="modalOpen" max-width="500px">
            <v-card>
                <v-card-title>{{ editMode ? "Edit Role" : "Add Role" }}</v-card-title>
                <v-card-text>
                    <v-text-field v-model="form.name" label="Role Name" prepend-icon="mdi-account-key"></v-text-field>
                    <v-text-field v-model="form.guard_name" label="Guard" prepend-icon="mdi-security"></v-text-field>
                </v-card-text>
                <v-card-actions class="justify-end">
                    <v-btn color="error" @click="modalOpen = false" >Close</v-btn>
                    <v-btn color="success" @click="saveRole">{{ editMode ? "Update" : "Submit" }}</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-container>
</template>

<style>
/* Optional: Add any custom styling */
</style>
