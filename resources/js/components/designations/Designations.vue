<template>
  <v-container>
    <!-- Table Title and Add Button -->
    <v-row class="mb-4">
      <v-col class="text-right">
        <v-btn @click="openAddDesignationDialog" color="primary" dark>
          <v-icon left>mdi-plus</v-icon>
          Add Designation
        </v-btn>
      </v-col>
    </v-row>

    <!-- Vuetify Data Table -->
    <v-data-table :headers="headers" :items="designations" item-value="id" class="elevation-1">
      <!-- Custom Action Buttons -->
      <template v-slot:item.actions="{ item }">
        <v-icon color="info" @click="viewDesignation(item.id)">mdi-eye</v-icon>
        <v-icon color="success" @click="openEditDesignationDialog(item)">mdi-pencil</v-icon>
        <v-icon color="error" @click="openConfirmDeleteDialog(item)">mdi-delete</v-icon>
      </template>
    </v-data-table>

    <!-- Add Designation Dialog -->
    <v-dialog v-model="addDesignationDialog" max-width="600px" persistent>
      <v-card>
        <v-card-title>
          <v-icon left>mdi-plus</v-icon>
          Add Designation
        </v-card-title>
        <v-card-text>
          <v-form ref="addDesignationForm" @submit.prevent="addDesignation">
            <v-text-field v-model="newDesignation.name" label="Designation Name" required></v-text-field>
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-btn color="error" @click="closeAddDesignationDialog">Close</v-btn>
          <v-btn color="success" @click="addDesignation">Add</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Edit Designation Dialog -->
    <v-dialog v-model="editDesignationDialog" max-width="600px" persistent>
      <v-card>
        <v-card-title>
          <v-icon left>mdi-pencil</v-icon>
          Edit Designation
        </v-card-title>
        <v-card-text>
          <v-form ref="editDesignationForm" @submit.prevent="editDesignation">
            <v-text-field v-model="selectedDesignation.name" label="Designation Name" required></v-text-field>
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-btn color="error" @click="closeEditDesignationDialog">Close</v-btn>
          <v-btn color="success" @click="editDesignation">Save Changes</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Confirm Delete Dialog -->
    <v-dialog v-model="confirmDeleteDialog" max-width="600px" persistent>
      <v-card>
        <v-card-title>
          <v-icon left>mdi-delete</v-icon>
          Confirm Delete
        </v-card-title>
        <v-card-text>
          Are you sure you want to delete the designation
          "<strong>{{ selectedDesignation.name }}</strong>"?
        </v-card-text>
        <v-card-actions>
          <v-btn color="error" @click="closeConfirmDeleteDialog">Cancel</v-btn>
          <v-btn color="success" @click="deleteDesignation">Confirm Delete</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      base_url: "/",
      headers: [
        { title: "#", key: "id", sortable: true },
        { title: "Designation", key: "name", sortable: true },
        { title: "Employee Count", key: "users.length", sortable: true },
        { title: "Actions", key: "actions", sortable: false },
      ],
      designations: [], // Array to hold designation data
      addDesignationDialog: false,
      editDesignationDialog: false,
      confirmDeleteDialog: false,
      selectedDesignation: null,
      newDesignation: { name: "" },
    };
  },
  created() {
    this.fetchDesignations();
  },
  methods: {
    // Fetch all designations
    fetchDesignations() {
      const apiUrl = this.base_url + "api/v1/designations";
      axios
        .get(apiUrl)
        .then((response) => {
          this.designations = response.data.designations;
        })
        .catch((error) => {
          console.error("Error fetching designations:", error);
        });
    },

    // Open Add Dialog
    openAddDesignationDialog() {
      this.addDesignationDialog = true;
    },
    closeAddDesignationDialog() {
      this.addDesignationDialog = false;
      this.newDesignation = { name: "" };
    },

    // Add a New Designation
    addDesignation() {
      const apiUrl = this.base_url + "api/v1/designations";
      axios
        .post(apiUrl, this.newDesignation)
        .then(() => {
          this.closeAddDesignationDialog();
          this.fetchDesignations();
          this.$toastr.success("Designation added successfully!");
        })
        .catch((error) => {
          this.$toastr.error("Error adding designation. Please try again.");
          console.error("Error adding designation:", error);
        });
    },

    // Open Edit Dialog
    openEditDesignationDialog(designation) {
      this.selectedDesignation = { ...designation };
      this.editDesignationDialog = true;
    },
    closeEditDesignationDialog() {
      this.editDesignationDialog = false;
    },

    // Edit Designation
    editDesignation() {
      const apiUrl =
        this.base_url + "api/v1/designations/" + this.selectedDesignation.id;
      axios
        .put(apiUrl, this.selectedDesignation)
        .then(() => {
          this.closeEditDesignationDialog();
          this.fetchDesignations();
          this.$toastr.success("Designation updated successfully!");
        })
        .catch((error) => {
          this.$toastr.error("Error updating designation. Please try again.");
          console.error("Error updating designation:", error);
        });
    },

    // Open Delete Confirmation
    openConfirmDeleteDialog(designation) {
      this.selectedDesignation = { ...designation };
      this.confirmDeleteDialog = true;
    },
    closeConfirmDeleteDialog() {
      this.confirmDeleteDialog = false;
    },

    // Delete Designation
    deleteDesignation() {
      const apiUrl =
        this.base_url + "api/v1/designations/" + this.selectedDesignation.id;
      axios
        .delete(apiUrl)
        .then(() => {
          this.closeConfirmDeleteDialog();
          this.fetchDesignations();
          this.$toastr.success("Designation deleted successfully!");
        })
        .catch((error) => {
          this.$toastr.error("Error deleting designation. Please try again.");
          console.error("Error deleting designation:", error);
        });
    },

    // View Designation
    viewDesignation(designationId) {
      this.$toastr.info(`View Designation ID: ${designationId}`);
      // Add additional logic for viewing details if needed
    },
  },
};
</script>
