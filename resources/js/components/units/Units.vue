<template>
  <v-container fluid>
    <v-row>
      <v-col class="text-right">
        <v-btn @click="addUnitDialog = true" color="primary" dark>
          <v-icon left>mdi-plus</v-icon>
          Add Branch
        </v-btn>
      </v-col>
    </v-row>
    <v-row>
      
          
      <v-col cols="12">
        <v-data-table :headers="tableHeaders" :items="branches" item-value="id" class="elevation-1" dense>

          <template v-slot:[`item.index`]="{ index }">
            {{ index + 1 }}
          </template>
          <template v-slot:[`item.actions`]="{ item }">
            <v-icon color="success" class="mx-1" @click="openEditUnitDialog(item)" left>mdi-pencil</v-icon>
            <v-icon color="error" class="mx-1" @click="deleteUnit(item.id)" left>mdi-delete</v-icon>
          </template>
        </v-data-table>
      </v-col>
    </v-row>

    <!-- Add Branch Dialog -->
    <v-dialog v-model="addUnitDialog" max-width="600px" persistent>
      <v-card>
        <v-card-title class="headline">
          <v-icon left>mdi-plus</v-icon>
          Add Branch
        </v-card-title>
        <v-card-text>
          <v-form ref="addUnitForm" @submit.prevent="submitAddUnitForm">
            <v-text-field v-model="newUnit.name" label="Branch" required></v-text-field>
            <v-text-field v-model="newUnit.address" label="Address" required></v-text-field>
            <v-text-field v-model="newUnit.phone" label="Phone" required></v-text-field>
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-btn @click="closeAddUnitDialog" color="error">
            <v-icon left>mdi-close</v-icon>
            Close
          </v-btn>
          <v-btn @click="addUnit" color="success">
            <v-icon left>mdi-check-circle</v-icon>
            Add
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Edit Branch Dialog -->
    <v-dialog v-model="editUnitDialog" max-width="600px" persistent>
      <v-card>
        <v-card-title class="headline">
          <v-icon left>mdi-pencil</v-icon>
          Edit Branch
        </v-card-title>
        <v-card-text>
          <v-form ref="editUnitForm" @submit.prevent="submitEditUnit">
            <v-text-field v-model="editUnit.name" label="Branch" required></v-text-field>
            <v-text-field v-model="editUnit.address" label="Address" required></v-text-field>
            <v-text-field v-model="editUnit.phone" label="Phone" required></v-text-field>
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-btn @click="closeEditUnitDialog" color="error">
            <v-icon left>mdi-close</v-icon>
            Close
          </v-btn>
          <v-btn @click="updateUnit" color="success">
            <v-icon left>mdi-check-circle</v-icon>
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
      newUnit: {
        name: '',
        address: '',
        phone: '',
      },
      editUnit: {
        id: 0,
        name: '',
        address: '',
        phone: '',
      },
      branches: [],
      search: '',
      addUnitDialog: false,
      editUnitDialog: false,
      tableHeaders: [
        { title: '#', value: 'index' },
        { title: 'Branch Name', value: 'name' },
        { title: 'Address', value: 'address' },
        { title: 'Contact', value: 'phone' },
        { title: 'Employees', value: 'users.length', sortable: false },
        { title: 'Actions', value: 'actions', sortable: false },
      ],
    };
  },
  created() {
    this.fetchUnits();
  },
  methods: {
    fetchUnits() {
      const apiUrl = this.base_url + 'api/v1/branches';

      axios.get(apiUrl)
        .then(response => {
          this.branches = response.data.branches;
        })
        .catch(error => {
          console.error('Error fetching branches:', error);
        });
    },
    viewUnitDetails(unitId) {
      // Implement view branch logic
    },
    openEditUnitDialog(branch) {
      this.editUnit = { ...branch };
      this.editUnitDialog = true;
    },
    updateUnit() {
      const apiUrl = this.base_url + 'api/v1/branches/' + this.editUnit.id;

      axios.put(apiUrl, this.editUnit)
        .then(() => {
          this.closeEditUnitDialog();
          this.fetchUnits();
          this.$toastr.success('Branch updated successfully!');
        })
        .catch(error => {
          this.$toastr.error('Error updating branch. Please try again.');
          console.error('Error updating branch:', error);
        });
    },
    submitEditUnit() {
      this.updateUnit();
    },
    closeEditUnitDialog() {
      this.editUnitDialog = false;
      this.editUnit = { id: 0, name: '', address: '', phone: '' };
    },
    deleteUnit(unitId) {
      const apiUrl = this.base_url + 'api/v1/branches/' + unitId;

      if (confirm('Are you sure you want to delete this branch?')) {
        axios.delete(apiUrl)
          .then(() => {
            this.fetchUnits();
            this.$toastr.success('Branch deleted successfully!');
          })
          .catch(error => {
            this.$toastr.error('Error deleting branch. Please try again.');
            console.error('Error deleting branch:', error);
          });
      }
    },
    addUnit() {
      const apiUrl = this.base_url + 'api/v1/branches';

      axios.post(apiUrl, this.newUnit)
        .then(() => {
          this.closeAddUnitDialog();
          this.fetchUnits();
          this.$toastr.success('Branch added successfully!');
        })
        .catch(error => {
          this.$toastr.error('Error adding branch. Please try again.');
          console.error('Error adding branch:', error);
        });
    },
    closeAddUnitDialog() {
      this.addUnitDialog = false;
      this.newUnit = { name: '', address: '', phone: '' };
    },
    submitAddUnitForm() {
      this.addUnit();
    },
  },
};
</script>
