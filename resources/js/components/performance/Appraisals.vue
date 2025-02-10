<template>
    <v-container fluid>
        <v-row>
            <v-col cols="12">
                <v-row align="center" justify="space-between" style="width: 100%;">
                    <v-col cols="8">
                        <v-text-field v-model="search" append-icon="mdi-magnify" label="Search" single-line
                            hide-details></v-text-field>
                    </v-col>
                    <v-col cols="auto">
                        <v-btn icon color="primary" @click="showAddModal = true">
                            <v-icon>mdi-plus</v-icon>
                        </v-btn>
                    </v-col>
                </v-row>
                <v-divider></v-divider>
                <v-row v-if="selected.length > 0">
                    <v-col>
                        <v-icon class="mx-3" color="success" @click="updateStatusesModal = true" title="Update status"
                            size="15">mdi-account-clock</v-icon>
                        <v-icon class="mx-3" color="primary" @click="addNotesModal = true" title="Update target"
                            size="15">mdi-target</v-icon>
                        <v-icon class="mx-3" color="error" @click="deleteAppraisalModal = true" title="Delete appraisal"
                            size="15">mdi-delete</v-icon>
                    </v-col>
                </v-row>

                <v-data-table v-model="selected" :headers="headers" :items="appraisals" show-select :search="search">
                    <template v-slot:item.status="{ item }">
                        <v-icon :color="item.status ? 'green' : 'red'">{{ item.status ? 'mdi-check-circle' :
                            'mdi-close-circle' }}</v-icon>
                    </template>
                    <template v-slot:item.actions="{ item }">
                        <v-icon small color="black" @click="viewHistory(item)">mdi-history</v-icon>
                        <v-icon small color="blue" class="mx-2" @click="editAppraisal(item)">mdi-pen</v-icon>
                        <v-icon small color="red" @click="confirmDelete(item)">mdi-delete</v-icon>
                    </template>
                </v-data-table>
            </v-col>
        </v-row>

        <v-dialog v-model="showAddModal" persistent max-width="600px">
            <v-card>
                <v-card-title>
                    <span class="headline">Add Employee Appraisal</span>
                </v-card-title>
                <v-card-text>
                    <v-form ref="addForm" v-model="valid">
                        <v-text-field label="Employee Name" placeholder="Enter employee name"
                            v-model="newAppraisal.employee_name" :rules="[rules.required]"></v-text-field>
                        <v-text-field label="Position" placeholder="Enter employee position"
                            v-model="newAppraisal.position" :rules="[rules.required]"></v-text-field>
                        <v-text-field label="Appraisal Score" placeholder="Enter appraisal score"
                            v-model="newAppraisal.score" :rules="[rules.required]"></v-text-field>
                        <v-textarea label="Comments" placeholder="Enter comments"
                            v-model="newAppraisal.comments"></v-textarea>
                        <v-select :items="statuses" label="Status" v-model="newAppraisal.status"
                            :rules="[rules.required]"></v-select>
                    </v-form>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="red" text @click="showAddModal = false">Cancel</v-btn>
                    <v-btn color="blue darken-1" text @click="addAppraisal">Save</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <v-dialog v-model="showEditModal" persistent max-width="600px">
            <v-card>
                <v-card-title>
                    <span class="headline">Edit Employee Appraisal</span>
                </v-card-title>
                <v-card-text>
                    <v-form ref="editForm" v-model="valid">
                        <v-text-field label="Employee Name" placeholder="Enter employee name"
                            v-model="editedAppraisal.employee_name" :rules="[rules.required]"></v-text-field>
                        <v-text-field label="Position" placeholder="Enter employee position"
                            v-model="editedAppraisal.position" :rules="[rules.required]"></v-text-field>
                        <v-text-field label="Appraisal Score" placeholder="Enter appraisal score"
                            v-model="editedAppraisal.score" :rules="[rules.required]"></v-text-field>
                        <v-textarea label="Comments" placeholder="Enter comments"
                            v-model="editedAppraisal.comments"></v-textarea>
                        <v-select :items="statuses" label="Status" v-model="editedAppraisal.status"
                            :rules="[rules.required]"></v-select>
                    </v-form>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="red" text @click="showEditModal = false">Cancel</v-btn>
                    <v-btn color="blue darken-1" text @click="updateAppraisal">Save</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <v-dialog v-model="showDeleteModal" persistent max-width="400px">
            <v-card>
                <v-card-title class="headline">Confirm Delete</v-card-title>
                <v-card-text>Are you sure you want to delete this appraisal?</v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="blue darken-1" text @click="showDeleteModal = false">Cancel</v-btn>
                    <v-btn color="blue darken-1" text @click="deleteAppraisal">Delete</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-container>
</template>

<script>
export default {
    data() {
        return {
            selected: [],
            appraisals: [],
            headers: [
                { title: 'Employee Name', value: 'employee_name' },
                { title: 'Position', value: 'position' },
                { title: 'Appraisal Score', value: 'score' },
                { title: 'Comments', value: 'comments' },
                { title: 'Status', value: 'status' },
                { title: 'Actions', value: 'actions', sortable: false },
            ],
            search: '',
            showAddModal: false,
            showEditModal: false,
            showDeleteModal: false,
            newAppraisal: {
                employee_name: '',
                position: '',
                score: '',
                comments: '',
                status: '',
            },
            editedAppraisal: null,
            appraisalToDelete: null,
            statuses: ['Completed', 'Pending', 'Reviewed'],
            rules: {
                required: value => !!value || 'Required.',
            },
            valid: false,
        };
    },
    mounted() {
        this.fetchAppraisals();
    },
    methods: {
        fetchAppraisals() {
            axios.get('/api/v1/employee-appraisals')
                .then(response => {
                    this.appraisals = response.data;
                })
                .catch(error => {
                    console.error("There was an error fetching the appraisals: ", error);
                    this.appraisals = [];
                });
        },
        addAppraisal() {
            if (this.$refs.addForm.validate()) {
                axios.post('/api/v1/employee-appraisals', this.newAppraisal)
                    .then(response => {
                        this.$toastr.success('Appraisal added successfully!');
                        this.fetchAppraisals();
                        this.showAddModal = false;
                        this.newAppraisal = { employee_name: '', position: '', score: '', comments: '', status: '' };
                    })
                    .catch(error => {
                        this.$toastr.error('Failed to add appraisal. Please try again later!');
                        console.error("There was an error adding the appraisal: ", error);
                    });
            }
        },
        editAppraisal(item) {
            this.editedAppraisal = { ...item };
            this.showEditModal = true;
        },
        updateAppraisal() {
            if (this.$refs.editForm.validate()) {
                axios.put(`/api/v1/employee-appraisals/${this.editedAppraisal.id}`, this.editedAppraisal)
                    .then(response => {
                        const index = this.appraisals.findIndex(appraisal => appraisal.id === this.editedAppraisal.id);
                        if (index !== -1) {
                            this.appraisals.splice(index, 1, response.data);
                        }
                        this.$toastr.success('Appraisal updated successfully!');
                        this.showEditModal = false;
                    })
                    .catch(error => {
                        this.$toastr.error('Failed to update appraisal. Please try again later!');
                        console.error("There was an error updating the appraisal: ", error);
                    });
            }
        },
        confirmDelete(item) {
            this.appraisalToDelete = item;
            this.showDeleteModal = true;
        },
        deleteAppraisal() {
            axios.delete(`/api/v1/employee-appraisals/${this.appraisalToDelete.id}`)
                .then(() => {
                    this.appraisals = this.appraisals.filter(appraisal => appraisal.id !== this.appraisalToDelete.id);
                    this.$toastr.success('Appraisal deleted successfully!');
                    this.showDeleteModal = false;
                })
                .catch(error => {
                    this.$toastr.error('Failed to delete appraisal. Please try again later!');
                    console.error("There was an error deleting the appraisal: ", error);
                });
        },
        viewHistory(item) {
            // Handle viewing history logic here
        },
    },
};
</script>

<style scoped>
.v-data-table {
    height: 100%;
}

.v-data-table .v-data-table__wrapper {
    max-height: 80vh;
}

.v-data-table .v-data-table__actions {
    display: flex;
    justify-content: flex-end;
}

.v-data-table .v-data-table__actions .v-btn {
    margin-left: 8px;
}
</style>
