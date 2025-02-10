<template>
    <v-container fluid>
        <v-row>
            <v-responsive>
                <v-data-table :headers="headers" :items="complaints" item-key="id" class="elevation-1" show-select>
                    <template v-slot:top>
                        <v-toolbar flat color="primary">
                            <v-btn class="mr-4" @click="openDialog">Raise A Complaint (CTR+N)</v-btn>
                            <v-select v-model="selectedFilter" :items="filters" label="-- select --" class="mr-4">
                            </v-select>
                            <v-spacer></v-spacer>
                            <v-btn color="secondary" class="mr-4">Discard current criteria</v-btn>
                            <v-text-field v-model="search" append-icon="mdi-magnify" label="Introduce your search"
                                single-line hide-details>
                            </v-text-field>
                        </v-toolbar>
                    </template>
                    <template v-slot:item.actions="{ item }">
                        <v-icon small @click="viewComplaint(item.id)" title="View Complaint">mdi-account-search</v-icon>
                        <v-icon small @click="editComplaint(item)" title="Edit Complaint">mdi-pencil</v-icon>
                        <v-icon small @click="deleteComplaint(item)" title="Delete Complaint">mdi-delete</v-icon>
                    </template>
                </v-data-table>
            </v-responsive>
        </v-row>

        <v-dialog v-model="showDialog" max-width="600px">
            <v-card>
                <v-card-title>
                    <span class="headline">{{ formTitle }}</span>
                </v-card-title>
                <v-card-text>
                    <v-container>
                        <v-row>
                            <v-col cols="12">
                                <v-text-field variant="outlined" v-model="editedComplaint.subject" label="Subject"
                                    :rules="[v => !!v || 'Subject is required']" placeholder="Workplace Harrassement">
                                </v-text-field>
                            </v-col>
                            <v-col cols="12">
                                <v-select variant="outlined" v-model="editedComplaint.category" :items="categories"
                                    label="Category" :rules="[v => !!v || 'Category is required']">
                                </v-select>
                            </v-col>
                            <v-col cols="12">
                                <v-select variant="outlined" v-model="editedComplaint.assigned_to" :items="users"
                                    label="Address To" item-value="id" item-title="fullname"
                                    :rules="[v => !!v || 'Addressee is required']">
                                </v-select>
                            </v-col>
                            <v-col cols="12">
                                <v-select variant="outlined" v-model="editedComplaint.followers" :items="users"
                                    label="Followers (Optional)" item-value="id" item-title="fullname"
                                    placeholder="Select followers" multiple clearable>
                                </v-select>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="12">
                                <v-textarea variant="outlined" v-model="editedComplaint.description" label="Description"
                                    :rules="[v => !!v || 'Description is required']">
                                </v-textarea>
                            </v-col>
                            <v-col cols="12">
                                <v-switch v-model="editedComplaint.is_anonymous"
                                    :color="editedComplaint.is_anonymous ? 'green' : 'blue'" label="Submit Anonymously">
                                </v-switch>
                            </v-col>
                        </v-row>
                    </v-container>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="danger" text @click="closeDialog">Cancel</v-btn>
                    <v-btn color="green darken-1" text @click="saveComplaint">Save</v-btn>
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
            showDialog: false,
            search: '',
            selectedFilter: null,
            filters: ['All', 'Closed', 'Request', 'Incident'],
            complaints: [],
            headers: [
                { title: 'ID', value: 'id' },
                { title: 'Subject', value: 'subject' },
                { title: 'Description', value: 'description' },
                { title: 'Category', value: 'category' },
                { title: 'Status', value: 'status' },
                { title: 'Priority', value: 'priority' },
                { title: 'Date Opened', value: 'date_opened' },
                { title: 'Date Closed', value: 'closed_date' },
                { title: 'Attachment', value: 'attachment' },
                { title: 'Addressee', value: 'addressed_to' },
                { title: 'Creation Date', value: 'created_at' },
                { title: 'Actions', value: 'actions', sortable: false },
            ],
            editedIndex: -1,
            editedComplaint: this.getDefaultComplaint(),
            defaultComplaint: this.getDefaultComplaint(),
            users: [],
            statuses: ['Open', 'In Progress', 'Resolved', 'Closed'],
            categories: ['Harassment', 'Workplace Safety', 'Compensation & Benefits', 'Employee Rights', 'Workload & Scheduling', 'Other']
        };
    },
    computed: {
        formTitle() {
            return this.editedIndex === -1 ? 'Create Complaint' : 'Edit Complaint';
        }
    },
    created() {
        this.fetchUsers();
        this.fetchComplaints();
    },
    methods: {
        getDefaultComplaint() {
            return {
                subject: '',
                status: '',
                assigned_to: '',
                description: '',
                resolution: '',
                category: '',
                priority: '',
                entryChannel: '',
                followers: [],
                is_anonymous: true
            };
        },
        fetchUsers() {
            const apiUrl = `${this.base_url}api/v1/users?`;

            axios.get(apiUrl)
                .then((response) => {
                    this.users = response.data.users.map(user => ({
                        ...user,
                        fullname: `${user.firstname} ${user.lastname}`
                    }));
                    this.users = this.users;
                })
                .catch((error) => {
                    toastr.error('Error fetching users:', error.message);
                });
        },

        fetchComplaints() {
            const apiUrl = `${this.base_url}api/v1/complaints?user_id=${this.user_id}`;
            axios.get(apiUrl)
                .then(response => {
                    this.complaints = response.data.complaints;
                })
                .catch(error => {
                    console.error('Error fetching complaints:', error);
                });
        },
        openDialog() {
            this.editedIndex = -1;
            this.editedComplaint = Object.assign({}, this.defaultComplaint);
            this.showDialog = true;
        },
        editComplaint(item) {
            this.editedIndex = this.complaints.indexOf(item);
            this.editedComplaint = Object.assign({}, item);
            this.showDialog = true;
        },
        deleteComplaint(item) {
            const index = this.complaints.indexOf(item);
            if (confirm('Are you sure you want to delete this complaint?')) {
                this.complaints.splice(index, 1);
                const apiUrl = `${this.base_url}api/v1/complaints/${item.id}`;
                axios.delete(apiUrl)
                    .then(() => {
                        console.log('Complaint deleted');
                    })
                    .catch(error => {
                        console.error('Error deleting complaint:', error);
                    });
            }
        },
        closeDialog() {
            this.showDialog = false;
            this.editedComplaint = Object.assign({}, this.defaultComplaint);
            this.editedIndex = -1;
        },
        saveComplaint() {
            const complaintData = {
                ...this.editedComplaint,
                is_anonymous: !!this.editedComplaint.is_anonymous
            };
            if (this.editedIndex > -1) {
                Object.assign(this.complaints[this.editedIndex], complaintData);
                const apiUrl = `${this.base_url}api/v1/complaints/${complaintData.id}`;
                axios.put(apiUrl, complaintData)
                    .then(() => {
                        console.log('Complaint updated');
                    })
                    .catch(error => {
                        console.error('Error updating complaint:', error);
                    });
            } else {
                const apiUrl = `${this.base_url}api/v1/complaints`;
                axios.post(apiUrl, complaintData)
                    .then(() => {
                        this.$toastr.success('created')
                        this.closeDialog();
                    })
                    .catch(error => {
                        this.$toastr.error('Failed, try again!')
                        console.error('Error creating complaint:', error);
                    });
            }

        }
    }
};
</script>
