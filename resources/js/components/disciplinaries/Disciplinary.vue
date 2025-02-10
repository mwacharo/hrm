<template>
    <v-row>
        <v-col md="12">
            <v-card class="p-4">
                <!-- Title and Search Input -->
                <v-row class="mb-4">
                    <v-col md="6">
                        <h2 class="headline">Disciplinaries</h2>
                    </v-col>
                    <v-col md="6" class="text-right">
                        <v-btn @click="openAddDialog" class="rounded-button" color="primary" icon>
                            <v-icon>mdi-plus</v-icon>
                        </v-btn>
                    </v-col>
                </v-row>

                <v-dialog v-model="editDialog" max-width="600px" persistent>
                    <v-card>
                        <v-card-title class="headline">
                            {{ editMode ? 'Edit' : 'Add' }} Disciplinary
                        </v-card-title>
                        <v-card-text>
                            <v-select v-model="editDisciplinary.user_id" :items="users" label="Employee"
                                item-title="fullName" item-value="id" placeholder="Select Employee"></v-select>
                            <v-text-field v-model="editDisciplinary.violation" label="Violation"
                                placeholder="Enter Violation"></v-text-field>
                            <v-text-field v-model="editDisciplinary.violation_date" type="date" label="Violation Date"
                                placeholder="Select Violation Date"></v-text-field>
                            <v-select v-model="editDisciplinary.type_of_disciplinary" :items="disciplinaryTypes"
                                label="Type of Disciplinary" placeholder="Select Type of Disciplinary"></v-select>
                            <v-file-input v-model="editDisciplinary.document" label="Document" show-size
                                placeholder="Select Document"></v-file-input>
                            <v-textarea v-model="editDisciplinary.description" label="Description (Optional)"
                                placeholder="Enter Description">
                            </v-textarea>
                            <v-text-field v-model="editDisciplinary.comment" label="Comment (Optional)"
                                placeholder="Enter Comment">
                            </v-text-field>
                        </v-card-text>
                        <v-card-actions>
                            <v-btn @click="saveDisciplinary" color="primary">
                                Save
                            </v-btn>
                            <v-btn @click="closeEditDialog">
                                Close
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </v-dialog>

                <!-- Search Input -->
                <v-text-field v-model="search" label="Search"></v-text-field>

                <!-- Data Table -->
                <v-data-table :headers="headers" :items="filteredDisciplinaries" item-key="id">
                    <template v-slot:item="{ item, index }">
                        <tr @click="showDetails(item)" class="cursor-pointer">
                            <td>{{ index + 1 }}</td>
                            <td>{{ `${item.user.fullname}` }}</td>
                            <td>{{ item.violation }}</td>
                            <td>{{ item.violation_date }}</td>
                            <td>{{ item.type_of_disciplinary }}</td>
                            <td>
                                <v-icon v-if="item.document"
                                    @click.stop="downloadDocument(item)">mdi-cloud-download
                                </v-icon>
                                <a v-if="item.document" :href="getDocumentUrl(item.document)" target="_blank"></a>
                                {{ !item.document ? 'Null' : '' }}
                            </td>

                            <td>{{ item.comment || 'N/A' }}</td>
                            <td>{{ item.status || 'N/A' }}</td>
                            <td>
                                <v-icon color="blue" @click.stop="editDisciplinaryAction(item)">mdi-pencil</v-icon>
                                <v-icon color="green" v-if="!item.closed"
                                    @click.stop="closeCaseAction(item)">mdi-comment</v-icon>
                                <v-icon color="red" @click.stop="deleteDisciplinaryAction(item)">mdi-delete</v-icon>
                            </td>
                        </tr>
                    </template>
                </v-data-table>
            </v-card>
            <v-dialog v-model="resolutionDialog" max-width="400px">
                <v-card>
                    <v-card-title class="headline">
                        Enter Resolution
                    </v-card-title>
                    <v-card-text>
                        <v-textarea v-model="resolutionText" label="Resolution"></v-textarea>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn @click="saveResolutionAndClose" color="primary">
                            Save
                        </v-btn>
                        <v-btn @click="resolutionDialog = false">
                            Close
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </v-col>
    </v-row>
</template>

<script>

export default {
    data() {
        return {
            base_url: '/',
            headers: [
                { title: '#', value: 'id' },
                { title: 'Employee', value: 'user.fullname' },
                { title: 'Violation', value: 'violation' },
                { title: 'Violation Date', value: 'violation_date' },
                { title: 'Type of Disciplinary', value: 'type_of_disciplinary' },
                { title: 'Document', value: 'document' },
                { title: 'Comment', value: 'comment' },
                { title: 'Status', value: 'status' },
                { title: 'Actions', value: 'actions' }
            ],
            disciplinaries: [],
            disciplinaryTypes: ['Verbal', 'Written', 'Suspension', 'Termination', 'Other'],
            users: [],

            editDialog: false,
            editMode: false,
            editDisciplinary: {
                id: null,
                user_id: null,
                violation: '',
                violation_date: '',
                type_of_disciplinary: '',
                document: '',
                description: '',
                comment: '',
                status: '',
            },
            search: '',
        };
    },

    computed: {
        filteredDisciplinaries() {
            return this.disciplinaries.filter(item =>
                Object.values(item).some(value =>
                    String(value).toLowerCase().includes(this.search.toLowerCase())
                )
            );
        },
    },

    created() {
        this.fetchDisciplinaries();
        this.fetchUsers();
    },

    methods: {
        fetchDisciplinaries() {
            const apiUrl = this.base_url + 'api/v1/disciplinaries';

            axios.get(apiUrl)
                .then(response => {
                    this.disciplinaries = response.data.disciplinaries;
                })
                .catch(error => {
                    console.error('Error fetching disciplinaries:', error);
                });
        },

        fetchUsers() {
            const apiUrl = this.base_url + 'api/v1/users';

            axios.get(apiUrl)
                .then(response => {
                    this.users = response.data.users.map(user => ({
                        id: user.id,
                        fullName: `${user.firstname} ${user.lastname}`,
                    }));
                })
                .catch(error => {
                    console.error('Error fetching users:', error);
                });
        },
        getDocumentUrl(documentPath) {
            return `${this.base_url}/${documentPath}`;
        },

        downloadDocument(item) {
            const documentUrl = this.getDocumentUrl(item.document);
        },

        showDetails(item) {
            console.log('Clicked row:', item);
        },

        closeCaseAction(item) {
            // Store the current disciplinary being closed
            this.selectedDisciplinary = item;
            // Open the resolution dialog
            this.resolutionDialog = true;
        },

        saveResolutionAndClose() {
            // Implement your logic to save the resolution and close the case
            // You can use this.selectedDisciplinary and this.resolutionText
            console.log('Resolution:', this.resolutionText);
            console.log('Disciplinary:', this.selectedDisciplinary);

            // After saving, close the dialog and update the data if needed
            this.resolutionDialog = false;
            this.fetchDisciplinaries();
        },

        openAddDialog() {
            this.editMode = false;
            this.editDisciplinary = {
                id: null,
                user_id: null,
                violation: '',
                violation_date: '',
                type_of_disciplinary: '',
                document: '',
                comment: '',
                status: '',
            };
            this.editDialog = true;
        },

        editDisciplinaryAction(item) {
            this.editMode = true;
            this.editDisciplinary = {
                id: item.id,
                user_id: item.user_id,
                violation: item.violation,
                violation_date: item.violation_date,
                type_of_disciplinary: item.type_of_disciplinary,
                document: item.document,
                comment: item.comment,
                status: item.status,
            };
            this.editDialog = true;
        },

        closeEditDialog() {
            this.editDialog = false;
            this.editDisciplinary = {
                id: null,
                user_id: null,
                violation: '',
                violation_date: '',
                type_of_disciplinary: '',
                document: '',
                comment: '',
                status: '',
            };
        },

        saveDisciplinary() {
            const apiUrl = this.base_url + 'api/v1/disciplinaries';
            const formData = new FormData();
            formData.append('user_id', this.editDisciplinary.user_id);
            formData.append('violation', this.editDisciplinary.violation);
            formData.append('violation_date', this.editDisciplinary.violation_date);
            formData.append('type_of_disciplinary', this.editDisciplinary.type_of_disciplinary);
            formData.append('comment', this.editDisciplinary.comment);

            if (this.editDisciplinary.document && this.editDisciplinary.document[0]) {
                formData.append('document', this.editDisciplinary.document[0]);
            }

            if (this.editMode) {
                axios
                    .put(`${apiUrl}/${this.editDisciplinary.id}`, formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                        },
                    })
                    .then(() => {
                        toastr.success('Disciplinary updated successfully!');
                        this.closeEditDialog();
                    })
                    .catch(error => {
                        toastr.error('Error updating disciplinary!');
                    });
            } else {
                axios
                    .post(apiUrl, formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                        },
                    })
                    .then(() => {
                        toastr.success('Disciplinary added successfully!');
                        this.closeEditDialog();
                        this.fetchDisciplinaries();
                    })
                    .catch(error => {
                        toastr.error('Error adding disciplinary!');
                    });
            }
        },


        deleteDisciplinaryAction(item) {
            const apiUrl = this.base_url + 'api/v1/disciplinaries';

            axios
                .delete(`${apiUrl}/${item.id}`)
                .then(() => {
                    console.log('Disciplinary deleted successfully:', item);
                    this.fetchDisciplinaries(); // Update the data after deletion
                })
                .catch(error => {
                    console.error('Error deleting disciplinary:', error);
                });
        },

    },
};
</script>

<style scoped>
.rounded-button {
    border-radius: 20px;
}
</style>
