<template>
  <v-container>
    <!-- Add Office Button -->
    <v-row>
      <v-col class="text-right">
        <v-btn @click="addOfficeDialog = true" color="primary" outlined>
          <v-icon left>mdi-plus</v-icon>
          Add Office
        </v-btn>
      </v-col>
    </v-row>

    <!-- Offices Table -->
    <v-row>
      <v-col cols="12">
        <v-data-table :headers="headers" :items="offices" item-value="id" class="elevation-1" dense>
          
          <template #item.index="{ index }">
            {{ index + 1 }}
          </template>

          <template #item.actions="{ item }">
           
              <v-icon class="mx-1" color="info" @click="viewOffice(item)">mdi-eye</v-icon>
              <v-icon class="mx-1" color="success" @click="viewOffice(item)">mdi-pencil</v-icon>    
              <v-icon class="mx-1" color="info"  @click="deleteOffice(item.id)">mdi-delete</v-icon>
           
          </template>
        </v-data-table>
      </v-col>
    </v-row>

    <!-- Add Office Dialog -->
    <v-dialog v-model="addOfficeDialog" max-width="600px" persistent>
      <v-card>
        <v-card-title>
          <v-icon>mdi-plus</v-icon>
          Add Office
        </v-card-title>
        <v-card-text>
          <v-form ref="addOfficeForm" @submit.prevent="submitAddOfficeForm">
            <v-text-field v-model="newOffice.name" label="Office Name" required></v-text-field>
            <v-select v-model="newOffice.unit_id" :items="branches" label="Branch" item-value="id"
              item-title="name"></v-select>
            <v-text-field v-model="newOffice.phone" label="Phone" required></v-text-field>
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-btn @click="closeAddOfficeDialog" color="error">Close</v-btn>
          <v-btn @click="addOffice" color="success">
            <v-icon>mdi-check-circle</v-icon>
            Add
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Edit Office Dialog -->
    <v-dialog v-model="editOfficeDialog" max-width="600px" persistent>
      <v-card>
        <v-card-title>
          <v-icon>mdi-pencil</v-icon>
          Edit Office
        </v-card-title>
        <v-card-text>
          <v-form ref="editOfficeForm" @submit.prevent="submitEditOfficeForm">
            <v-text-field v-model="editOffice.name" label="Office Name" required></v-text-field>
            <v-select v-model="editOffice.unit_id" :items="branches" label="Branch" item-value="id"
              item-title="name"></v-select>
            <v-text-field v-model="editOffice.phone" label="Phone" required></v-text-field>
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-btn @click="closeEditOfficeDialog" color="error">Close</v-btn>
          <v-btn @click="updateOffice" color="success">
            <v-icon>mdi-check-circle</v-icon>
            Update
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
      base_url: '/',
      headers: [
        { title: '#', value: 'index' },
        { title: 'Name', value: 'name' },
        { title: 'Address', value: 'unit.name' },
        { title: 'Phone', value: 'phone' },
        { title: 'Employees', value: 'users.length' },
        { title: 'Actions', value: 'actions', sortable: false },
      ],
      newOffice: {
        name: '',
        unit_id: null,
        phone: '',
      },
      editOffice: {
        id: 0,
        name: '',
        unit_id: null,
        phone: '',
      },
      offices: [],
      branches: [], // Assuming you have a list of branches
      addOfficeDialog: false,
      editOfficeDialog: false,
    };
  },
  created() {
    this.fetchOffices();
    this.fetchUnits(); // Fetch the list of branches
  },
  methods: {
    fetchOffices() {
      const apiUrl = this.base_url + 'api/v1/offices';

      axios
        .get(apiUrl)
        .then((response) => {
          this.offices = response.data.offices;
        })
        .catch((error) => {
          console.error('Error fetching offices:', error);
        });
    },
    fetchUnits() {
      const apiUrl = this.base_url + 'api/v1/branches';

      axios
        .get(apiUrl)
        .then((response) => {
          this.branches = response.data.branches;
        })
        .catch((error) => {
          console.error('Error fetching branches:', error);
        });
    },
    viewOffice(office) {
      console.log('View Office:', office);
    },
    addOffice() {
      const apiUrl = this.base_url + 'api/v1/offices';

      axios
        .post(apiUrl, this.newOffice)
        .then((response) => {
          this.closeAddOfficeDialog();
          this.fetchOffices();
          this.$toastr.success('Office added successfully!');
        })
        .catch((error) => {
          this.$toastr.error('Error adding office. Please try again.');
          console.error('Error adding office:', error);
        });
    },
    closeAddOfficeDialog() {
      this.addOfficeDialog = false;
      this.newOffice = { name: '', unit_id: null, phone: '' };
    },
    openEditOfficeDialog(office) {
      this.editOffice = { ...office };
      this.editOfficeDialog = true;
    },
    updateOffice() {
      const apiUrl = this.base_url + 'api/v1/offices/' + this.editOffice.id;

      axios
        .put(apiUrl, this.editOffice)
        .then((response) => {
          this.closeEditOfficeDialog();
          this.fetchOffices();
          this.$toastr.success('Office updated successfully!');
        })
        .catch((error) => {
          this.$toastr.error('Error updating office. Please try again.');
          console.error('Error updating office:', error);
        });
    },
    closeEditOfficeDialog() {
      this.editOfficeDialog = false;
      this.editOffice = { id: 0, name: '', unit_id: null, phone: '' };
    },
    deleteOffice(officeId) {
      const apiUrl = this.base_url + 'api/v1/offices/' + officeId;

      if (confirm('Are you sure you want to delete this office?')) {
        axios
          .delete(apiUrl)
          .then((response) => {
            this.fetchOffices();
            this.$toastr.success('Office deleted successfully!');
          })
          .catch((error) => {
            this.$toastr.error('Error deleting Office. Please try again.');
            console.error('Error deleting Office:', error);
          });
      }
    },
  },
};
</script>
