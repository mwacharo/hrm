<template>
  <v-container fluid>
    <v-row>
      <v-col class="text-right mb-4">
        <v-btn @click="addDepartmentDialog = true" dark icon>
          <v-icon left>mdi-plus</v-icon>
        </v-btn>
      </v-col>
    </v-row>

    <v-data-table :headers="headers" :items="departments" item-value="id" class="elevation-1" dense>
      
      <template v-slot:[`item.index`]='{ index }'>
        {{ index + 1 }}
      </template>

      <template v-slot:[`item.actions`]='{ item }'>
        <v-tooltip bottom>
          <template v-slot:activator="{ on, attrs }">
            <v-btn color="success" class="mx-2" v-bind="attrs" v-on="on" @click="openEditDepartmentDialog(item)">
              <v-icon left>mdi-pencil</v-icon>
              Edit
            </v-btn>
          </template>
          Edit {{ item.name }} department
        </v-tooltip>

        <v-tooltip bottom>
          <template v-slot:activator="{ on, attrs }">
            <v-btn color="error" class="mx-2" v-bind="attrs" v-on="on" @click="confirmDeleteDepartment(item.id)">
              <v-icon left>mdi-delete</v-icon>
              Delete
            </v-btn>
          </template>
          Delete {{ item.name }} department
        </v-tooltip>
      </template>
    </v-data-table>

    <!-- Add, Edit, and Confirm Delete Dialogs -->
    <v-dialog v-model="addDepartmentDialog" max-width="400px" persistent>
      <v-card>
        <v-card-title class="headline">
          <v-icon left>mdi-plus</v-icon>
          Add Department
        </v-card-title>
        <v-card-text>
          <v-form ref="addDepartmentForm" @submit.prevent="addDepartment">
            <v-text-field v-model="newDepartment.name" label="Name" placeholder="Human Resource"
              required></v-text-field>
          </v-form>
        </v-card-text>
        <v-card-actions class="justify-end">
          <v-btn @click="closeAddDepartmentDialog" color="error">
            <v-icon left>mdi-cancel</v-icon>
            Close
          </v-btn>
          <v-btn @click="addDepartment" color="success">
            <v-icon left>mdi-check-circle</v-icon>
            Add
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="editDepartmentDialog" max-width="400px" persistent>
      <v-card>
        <v-card-title class="headline">
          <v-icon left>mdi-pencil</v-icon>
          Update Department
        </v-card-title>
        <v-card-text>
          <v-form ref="editDepartmentForm" @submit.prevent="updateDepartment">
            <v-text-field v-model="editDepartment.name" label="Department Name" required></v-text-field>
          </v-form>
        </v-card-text>
        <v-card-actions class="justify-end">
          <v-btn @click="closeEditDepartmentDialog" color="error">
            <v-icon left>mdi-cancel</v-icon>
            Close
          </v-btn>
          <v-btn @click.prevent="updateDepartment" color="success">
            <v-icon left>mdi-check-circle</v-icon>
            Save
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="confirmDeleteDialog" max-width="400px" persistent>
      <v-card>
        <v-card-title class="headline">
          <v-icon left>mdi-alert-circle</v-icon>
          Confirm Delete
        </v-card-title>
        <v-card-text>
          Are you sure you want to delete this department?
        </v-card-text>
        <v-card-actions class="justify-end">
          <v-btn @click="closeConfirmDeleteDialog" color="primary">
            No
          </v-btn>
          <v-btn @click="deleteDepartment" color="error">
            Yes
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script>
export default {
  data() {
    return {
      headers: [
        { title: '#', value: 'index' },
        { title: "Department Name", value: "name" },
        { title: "Employees", value: "employeeCount", align: "center" },
        { title: "Actions", value: "actions", sortable: false },
      ],
      base_url: "/",
      newDepartment: {
        name: "",
      },
      editDepartment: {
        id: 0,
        name: "",
      },
      departments: [],
      addDepartmentDialog: false,
      editDepartmentDialog: false,
      confirmDeleteDialog: false,
      selectedDepartmentId: null,
    };
  },
  created() {
    this.fetchDepartments();
  },
  methods: {
    fetchDepartments() {
      const apiUrl = `${this.base_url}api/v1/departments`;
      axios
        .get(apiUrl)
        .then((response) => {
          this.departments = response.data.departments.map((department) => ({
            ...department,
            employeeCount: department.users ? department.users.length : 0,
          }));
        })
        .catch((error) => {
          console.error("Error fetching departments:", error);
        });
    },
    addDepartment() {
      const apiUrl = `${this.base_url}api/v1/departments`;
      axios
        .post(apiUrl, { name: this.newDepartment.name })
        .then(() => {
          this.fetchDepartments();
          this.$toastr.success("Department added successfully!");
          this.closeAddDepartmentDialog();
        })
        .catch((error) => {
          this.$toastr.error("Error adding department. Please try again.");
          console.error("Error adding department:", error);
        });
    },
    openEditDepartmentDialog(department) {
      this.editDepartmentDialog = true;
      this.editDepartment = { ...department };
    },
    updateDepartment() {
      const apiUrl = `${this.base_url}api/v1/departments/${this.editDepartment.id}`;
      axios
        .put(apiUrl, { name: this.editDepartment.name })
        .then(() => {
          this.fetchDepartments();
          this.$toastr.success("Department updated successfully!");
          this.closeEditDepartmentDialog();
        })
        .catch((error) => {
          this.$toastr.error("Error updating department. Please try again.");
          console.error("Error updating department:", error);
        });
    },
    confirmDeleteDepartment(departmentId) {
      this.selectedDepartmentId = departmentId;
      this.confirmDeleteDialog = true;
    },
    deleteDepartment() {
      const apiUrl = `${this.base_url}api/v1/departments/${this.selectedDepartmentId}`;
      axios
        .delete(apiUrl)
        .then(() => {
          this.fetchDepartments();
          this.$toastr.success("Department deleted successfully!");
          this.closeConfirmDeleteDialog();
        })
        .catch((error) => {
          this.$toastr.error("Error deleting department. Please try again.");
          console.error("Error deleting department:", error);
        });
    },
    closeAddDepartmentDialog() {
      this.addDepartmentDialog = false;
      this.newDepartment.name = "";
    },
    closeEditDepartmentDialog() {
      this.editDepartmentDialog = false;
      this.editDepartment = { id: 0, name: "" };
    },
    closeConfirmDeleteDialog() {
      this.confirmDeleteDialog = false;
      this.selectedDepartmentId = null;
    },
  },
};
</script>

<style scoped>
.v-btn {
  margin: 0.5rem 0;
}
</style>
