<template>
    <v-container-fluid>
        <v-responsive>
            <!-- Create Button -->
            <v-row class="mb-2">
                <v-col class="text-right">
                    <v-btn @click="createLeaveType" icon>
                        <v-icon>mdi-plus</v-icon>
                    </v-btn>
                </v-col>
            </v-row>

            <!-- Data Table -->
            <v-data-table :headers="headers" item-key="id" class="elevation-10" :items="leaveTypes" responsive>
                <template v-slot:item="{ item, index }">
                    <tr>
                        <td>{{ index + 1 }}</td>
                        <td>{{ item.name }}</td>
                        <td>{{ item.days }}</td>
                        <td>{{ item.notes }}</td>
                        <td>
                            <v-icon @click="viewLeaveType(item)" color="history" style="margin-right: 8px;"
                                title="View Leave Type">mdi-eye</v-icon>
                            <v-icon @click="editLeaveType(item)" color="info" style="margin-right: 8px;"
                                title="Edit Leave Type">mdi-pencil</v-icon>
                            <v-icon @click="deleteLeaveType(item)" color="error" style="margin-right: 8px;"
                                title="Delete Leave Type">mdi-delete</v-icon>
                        </td>
                    </tr>
                </template>
            </v-data-table>

            <!-- Add/Edit Modal -->
            <v-dialog v-model="editModal" max-width="600px" persistent responsive>
                <v-card>
                    <v-card-title v-if="!editMode">Create Leave Type</v-card-title>
                    <v-card-title v-else>Edit Leave Type</v-card-title>
                    <v-card-text>
                        <v-text-field v-model="form.name" label="Leave Type Name"></v-text-field>
                        <v-text-field v-model="form.days" label="Days"></v-text-field>
                        <v-textarea v-model="form.notes" label="Notes"></v-textarea>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn color="error" @click="cancelEditLeaveType">Close</v-btn>
                        <v-btn color="primary" @click="saveLeaveType">
                            Save
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </v-responsive>
    </v-container-fluid>
</template>

<script>
export default {
    data() {
        return {
            base_url: '/',
            editModal: false,
            editMode: false,
            form: {
                id: null,
                name: '',
                days: '',
                notes: ''
            },
            leaveTypes: [],
            headers: [
                { title: '#', value: 'id' },
                { title: 'Leave Type', value: 'name' },
                { title: 'Days', value: 'days' },
                { title: 'Notes', value: 'notes' },
                { title: 'Action', value: 'actions' }
            ],
        };
    },
    mounted() {
        this.fetchLeaveTypes();
    },
    methods: {
        fetchLeaveTypes() {
            const apiUrl = this.base_url + 'api/v1/leave-types';
            axios.get(apiUrl)
                .then(response => {
                    this.leaveTypes = response.data.leaveTypes;
                })
                .catch(error => {
                    console.error('Error fetching Leave Types:', error);
                });
        },
        createLeaveType() {
            this.editMode = false;
            this.editModal = true; // Open add modal
        },
        saveLeaveType() {
            // Prepare the data to be sent in the request body
            const requestData = {
                name: this.form.name,
                days: this.form.days,
                notes: this.form.notes
            };

            // Make a POST request to the API endpoint to save the new leave type
            axios.post('/api/v1/leave-types', requestData)
                .then(response => {
                    // Handle successful response
                    console.log('New leave type created:', response.data);
                    // Close the modal
                    this.editModal = false;
                    // Reset form fields after saving
                    this.resetForm();
                    // Refresh leave types
                    this.fetchLeaveTypes();
                })
                .catch(error => {
                    // Handle error
                    this.$toastr.error('An error occurred')
                    console.error('Error creating leave type:', error);
                });
        },
        editLeaveType(leaveType) {
            this.editMode = true;
            this.form.id = leaveType.id;
            this.form.name = leaveType.name;
            this.form.days = leaveType.days;
            this.form.notes = leaveType.notes;
            this.editModal = true; // Open edit modal
        },
        updateLeaveType() {
            // Prepare the data to be sent in the request body
            const requestData = {
                id: this.form.id,
                name: this.form.name,
                days: this.form.days,
                notes: this.form.notes
            };

            // Make a PUT request to the API endpoint to update the leave type
            axios.put(`/api/v1/leave-types/${this.form.id}`, requestData)
                .then(response => {
                    // Handle successful response
                    console.log('Leave type updated:', response.data);
                    // Close the modal
                    this.editModal = false;
                    // Reset form fields after updating
                    this.resetForm();
                    // Refresh leave types
                    this.fetchLeaveTypes();
                })
                .catch(error => {
                    // Handle error
                    console.error('Error updating leave type:', error);
                });
        },
        cancelEditLeaveType() {
            // Close the add/edit modal and reset form fields
            this.editModal = false;
            this.resetForm();
        },
        viewLeaveType(leaveType) {
            // Implement code to display detailed view of leave type
        },
        deleteLeaveType(leaveType) {
            // Implement code to show delete confirmation modal and handle deletion
        },
        resetForm() {
            // Reset form fields
            this.form.id = null;
            this.form.name = '';
            this.form.days = '';
            this.form.notes = '';
        }
    }
}
</script>
