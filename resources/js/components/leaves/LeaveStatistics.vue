<template>
    <v-container-fluid>
        <v-card>


            <v-data-table :headers="tableHeaders" item-key="id" class="elevation-10" :items="leaves" :search="search"
                responsive>
                <template v-slot:item="{ item, index }">
                    <tr>
                        <td>{{ index + 1 }}</td>
                        <td><strong>{{ getUserFullName(item.user) }}</strong></td>
                        <td>{{ item.phone }}</td>
                        <td>{{ item.leave_type.name.replace('_', ' ') }}</td>
                        <td>{{ formatDate(item.created_at) }}</td>
                        <td>{{ item.from }}</td>
                        <td>{{ item.to }}</td>
                        <td :style="getStatusClass(item.status)">
                            <i class="mdi mdi-information"></i> {{ item.status.toUpperCase() }}
                        </td>
                        <td>
                            <v-icon @click="openLogsModal(item)" color="info" style="margin-right: 8px;"
                                title="View Logs">mdi-history
                            </v-icon>
                            <v-icon @click="viewLeave(item)" color="info" style="margin-right: 8px;"
                                title="View Leave">mdi-eye-check-outline
                            </v-icon>
                            <v-icon @click="cancelLeave(item)"
                                v-if="item.status !== 'Cancelled' && item.status !== 'Approved' && item.status !== 'Expired'"
                                color="warning" style="margin-right: 8px;" title="Cancel Leave">mdi-close-circle
                            </v-icon>
                            <v-icon @click="approveLeave(item)"
                                v-if="item.status !== 'Approved' && item.status !== 'Expired' && item.status !== 'Cancelled'"
                                color="success" style="margin-right: 8px;" title="Approve Leave">mdi-thumb-up-outline
                            </v-icon>
                            <v-icon @click="deleteLeave(item)" v-if="item.status == 'Pending'" color="error"
                                style="margin-right: 8px;" title="Delete Leave">mdi-delete
                            </v-icon>
                        </td>
                    </tr>
                </template>
            </v-data-table>
        </v-card>
        <!-- view Leave Dialog -->
        <v-dialog v-model="viewLeaveModal" max-width="600px" persistent responsive>
            <v-card class="view-leave-card">
                <v-card-title class="headline mb-3"> <!-- Added margin-bottom -->
                    <v-icon color="primary">mdi-information-outline</v-icon>
                    Leave Information
                </v-card-title>
                <v-card-subtitle class="mb-3"> <!-- Added margin-bottom -->
                    <v-icon color="primary">mdi-account-circle</v-icon>
                    Employee Name: {{ selectedItem.user.firstname }} {{ selectedItem.user.lastname }}
                </v-card-subtitle>
                <v-card-subtitle class="mb-3"> <!-- Added margin-bottom -->
                    <v-icon color="info">mdi-calendar-text</v-icon>
                    Leave Type: {{ selectedItem.leave_type.name.replace('_', ' ') }}
                </v-card-subtitle>
                <v-card-text class="mb-4"> <!-- Added margin-bottom -->
                    <!-- Display other leave information here -->
                    <div class="mb-2"> <!-- Added margin-bottom -->
                        <v-icon color="grey darken-2">mdi-calendar-clock</v-icon>
                        Application Date: {{ formatDate(selectedItem.created_at) }}
                    </div>
                    <div class="mb-2"> <!-- Added margin-bottom -->
                        <v-icon color="success">mdi-clock-time-eight</v-icon>
                        From: {{ selectedItem.from }}
                    </div>
                    <div class="mb-2"> <!-- Added margin-bottom -->
                        <v-icon color="error">mdi-clock-end</v-icon>
                        To: {{ selectedItem.to }}
                    </div>
                    <div class="mb-2"> <!-- Added margin-bottom -->
                        <v-icon color="indigo">mdi-calendar-star</v-icon>
                        Leave Days: {{ selectedItem.days }}
                    </div>
                    <div class="mb-2"> <!-- Added margin-bottom -->
                        <v-icon color="teal">mdi-comment-text-multiple</v-icon>
                        Comment: {{ selectedItem.comment || 'N/A' }}
                    </div>
                    <div class="mb-2"> <!-- Added margin-bottom -->
                        <v-icon color="purple">mdi-office-building</v-icon>
                        Department: {{ selectedItem.user.department }}
                    </div>
                    <div class="mb-2">
                        <v-icon color="blue">mdi-check-circle</v-icon>
                        Leave Status: {{ selectedItem.status }}
                    </div>
                    <div class="mb-2"> <!-- Added margin-bottom -->
                        <v-icon color="orange">mdi-progress-clock</v-icon>
                        Progress: 20%
                    </div>
                </v-card-text>
                <v-card-actions class="justify-end"> <!-- Align to the right -->
                    <v-btn color="primary" @click="closeLeaveViewModal">
                        <v-icon left>mdi-close-circle-outline</v-icon> Close
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- Cancel Leave Dialog -->
        <v-dialog v-model="cancelLeaveModal" max-width="600px" persistent>
            <v-card>
                <v-card-title class="headline mb-3">
                    <v-icon color="warning">mdi-alert-circle-outline</v-icon>
                    Cancel Leave
                </v-card-title>
                <v-card-text>
                    Are you sure you want to cancel this leave request?
                    <!-- Text area for adding notes -->
                    <v-textarea v-model="cancelNotes" label="Notes"
                        hint="Add any additional notes or comments"></v-textarea>
                </v-card-text>
                <v-card-actions class="justify-end">
                    <v-btn @click="closeCancelLeaveModal">No</v-btn>
                    <v-btn color="warning" @click="cancelLeaveAction">Yes, Cancel</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- Approve Leave Dialog -->
        <v-dialog v-model="approveLeaveModal" max-width="600px" persistent>
            <v-card>
                <v-card-title class="headline mb-3">
                    <v-icon color="success">mdi-check-circle-outline</v-icon>
                    Approve Leave
                </v-card-title>
                <v-card-text>
                    Are you sure you want to approve this leave request?

                    <!-- Text area for adding notes -->
                    <v-textarea v-model="approveNotes" label="Notes"
                        hint="Add any additional notes or comments"></v-textarea>
                </v-card-text>
                <v-card-actions class="justify-end">
                    <v-btn @click="closeApproveLeaveModal">No</v-btn>
                    <v-btn color="success" @click="approveLeaveAction">Yes, Approve</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>


        <!-- Delete Leave Dialog -->
        <v-dialog v-model="deleteLeaveModal" max-width="600px" persistent>
            <v-card>
                <v-card-title class="headline mb-3">
                    <v-icon color="error">mdi-delete-outline</v-icon>
                    Delete Leave
                </v-card-title>
                <v-card-text>
                    Are you sure you want to delete this leave request? This action cannot be undone.
                </v-card-text>
                <v-card-actions class="justify-end">
                    <v-btn @click="closeDeleteLeaveModal">No</v-btn>
                    <v-btn color="error" @click="deleteLeaveAction">Yes, Delete</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- view logs modal -->
        <v-dialog v-model="logsModal" max-width="600px" full-height top>
            <v-card>
                <v-card-title class="headline">
                    <v-icon color="primary">mdi-history</v-icon>
                    View Logs
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <v-list dense>
                        <v-list-item-group>
                            <v-list-item v-for="(log, index) in logs" :key="index">
                                <!-- Display user, action, and time details -->
                                <v-list-item-content>
                                    <v-list-item-title class="mb-3">
                                        <v-icon color="info" class="mr-1">mdi-account-circle</v-icon>
                                        <strong>User:</strong> {{ log.user }}
                                    </v-list-item-title>
                                    <v-list-item-title class="mb-3">
                                        <v-icon color="success" class="mr-1">mdi-check-circle-outline</v-icon>
                                        <strong>Action:</strong> {{ log.action }}
                                    </v-list-item-title>
                                    <v-list-item-subtitle>
                                        <v-icon color="red darken-2" class="mr-1">mdi-clock-time-eight</v-icon>
                                        <strong>Time:</strong> {{ log.time }}
                                    </v-list-item-subtitle>
                                </v-list-item-content>
                                <v-divider v-if="index < logs.length - 1"></v-divider>
                            </v-list-item>
                        </v-list-item-group>
                    </v-list>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-btn color="primary" @click="closeLogsModal">
                        <v-icon left>mdi-close-circle-outline</v-icon> Close
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

    </v-container-fluid>
</template>

<script>


export default {
    props: {
        userId: {
            type: [Number, String],
            required: true
        }
    },
    data() {
        return {
            base_url: '/',
            viewLeaveModal: false,
            approveLeaveModal: false,
            cancelLeaveModal: false,
            deleteLeaveModal: false,
            selectedItem: null,
            approveNotes: '',
            cancelNotes: '',
            logsModal: false,
            logs: [],
            form: {
                application_date: '',
                user_id: '',
                status: ''
            },
            applicationDateOptions: ['All', 'Today', 'Current Week', 'Last Week', 'Current Month', 'Current Year'],
            statusOptions: ['All', 'Approved', 'Pending', 'Cancelled'],
            users: [],
            offices: [],
            departments: [],
            leaveTypes: [],
            leaveStatistics: [],
            tableHeaders: [
                { title: '#', value: 'id' },
                { title: 'Employee', value: 'user.firstname' },
                { title: 'Phone', value: 'phone' },
                { title: 'Leave Type', value: 'leave_type.name' },
                { title: 'Application Date', value: 'created_at' },
                { title: 'From', value: 'from' },
                { title: 'To', value: 'to' },
                { title: 'Status', value: 'status' },
                { title: 'Action', value: 'actions' }
            ],

        };
    },
    created() {
        this.fetchUsers();
        this.fetchUnits();
        this.fetchOffices();
        this.fetchDepartments();
        this.fetchLeaveBalance();

    },
    computed: {
        tableItems() {
            return this.leaves.map(item => ({
                ...item,
                user_full_name: this.getUserFullName(item.user),
                status_icon: this.getStatusIcon(item.status),
            }));
        },
        usersWithFullName() {
            return this.users.map(user => ({
                ...user,
                fullName: `${user.first_name} ${user.last_name}`
            }));
        }
    },
    methods: {

        getUserFullName(user) {
            return `${user.firstname} ${user.lastname}`;
        },
        formatDate(date) {
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return new Date(date).toLocaleDateString(undefined, options);
        },

        fetchLeaveBalance() {
            const apiUrl = this.base_url + 'api/v1/leave_statistics';

            axios.get(apiUrl)
                .then(response => {
                    this.leaveStatistics = response.data.leave_statistics;
                })
                .catch(error => {
                    console.error('Error fetching Leave Statistics:', error);
                });
        },
        async submitForm() {
            try {
                // Assuming you have an API endpoint to fetch leaves with a POST request
                const response = await axios.post('/api/v1/fetch-leaves', this.form);

                // Update the leaves array with the fetched leaves
                this.leaves = response.data.leaves; // Assuming the response has a 'leaves' property

                // Handle the response as needed
                console.log('Leaves fetched successfully:', this.leaves);

                // Optionally, you can reset the form after fetching leaves
                this.resetForm();
            } catch (error) {
                // Handle errors, show user feedback, etc.
                console.error('Error fetching leaves:', error);
            }
        },

        fetchUsers() {
            const apiUrl = this.base_url + 'api/v1/users';

            axios.get(apiUrl)
                .then(response => {
                    this.users = response.data.users.map(user => ({
                        id: user.id,
                        first_name: user.firstname,
                        last_name: user.lastname,
                        email: user.email,
                        phone: user.phone,
                        unit: user.unit,
                        department: user.department,
                        designation: user.designation,
                        is_enabled: user.is_enabled,
                    }));
                })
                .catch(error => {
                    console.error('Error fetching users:', error);
                });
        },
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
        fetchOffices() {
            const apiUrl = this.base_url + 'api/v1/offices';

            axios.get(apiUrl)
                .then(response => {
                    this.offices = response.data.offices;
                })
                .catch(error => {
                    console.error('Error fetching offices:', error);
                });
        },
        fetchDepartments() {
            const apiUrl = this.base_url + 'api/v1/departments';

            axios.get(apiUrl)
                .then(response => {
                    this.departments = response.data.departments;
                })
                .catch(error => {
                    console.error('Error fetching departments:', error);
                });
        },

        fetchLeaves() {
            const apiUrl = this.base_url + 'api/v1/leaves';

            axios.get(apiUrl)
                .then(response => {
                    this.leaves = response.data.leaves;
                })
                .catch(error => {
                    console.error('Error fetching leaves:', error);
                });
        },
        async submitForm() {
            try {
                const response = await axios.post('/api/v1/fetch-leaves', this.form);

                this.leaves = response.data.leaves;

                console.log('Leaves fetched successfully:', this.leaves);

                this.resetForm();
            } catch (error) {
                console.error('Error fetching leaves:', error);
            }
        },
        resetForm() {
            this.form = {
                application_date: null,
                user_id: null,
                status: null
            };
        },

        getStatusClass(status) {

            return {
                color: status === 'Approved' ? 'green' :
                    status === 'Pending' ? 'orange' :
                        status === 'Manager Approved' ? 'indigo' :
                            status === 'Hr Approved' ? 'blue' : 'black'
            };
        },


        viewLeave(item) {
            this.selectedItem = item;
            this.viewLeaveModal = true;
        },
        closeLeaveViewModal() {
            this.viewLeaveModal = false;
        },

        openLogsModal(item) {
            const apiUrl = `${this.base_url}api/v1/leaves/${item.id}/logs`;

            axios.get(apiUrl)
                .then(response => {
                    this.logs = response.data.logs;
                })
                .catch(error => {
                    console.error('Error fetching logs:', error);
                })
                .finally(() => {
                    this.logsModal = true;
                });
        },
        closeLogsModal() {
            this.logsModal = false;
        },

        cancelLeave(item) {
            this.selectedItem = item;
            this.cancelLeaveModal = true;
        },

        approveLeave(item) {
            this.selectedItem = item;
            this.approveLeaveModal = true;
        },

        deleteLeave(item) {
            this.selectedItem = item;
            this.deleteLeaveModal = true;
        },

        cancelLeaveAction() {
            const apiUrl = `${this.base_url}api/v1/leaves/${this.selectedItem.id}/cancel`;

            const requestData = {
                userId: this.userId,
            };

            axios.put(apiUrl, requestData)
                .then(response => {
                    toastr.success('Leave canceled successfully!');
                    this.fetchLeaves();
                })
                .catch(error => {
                    // Handle error
                    toastr.error('Error canceling leave!');
                })
                .finally(() => {
                    this.closeCancelLeaveModal();
                });
        },

        approveLeaveAction() {
            const apiUrl = `${this.base_url}api/v1/leaves/${this.selectedItem.id}/approve`;
            const requestData = { userId: this.userId };

            axios.put(apiUrl, requestData)
                .then(response => {
                    toastr.success(response.data.message);
                    this.fetchLeaves();
                })
                .catch(error => {
                    toastr.error(error.response.data.error);
                })
                .finally(() => {
                    this.closeApproveLeaveModal();
                });
        },

        deleteLeaveAction() {
            const apiUrl = `${this.base_url}api/v1/leaves/${this.selectedItem.id}`;

            axios.delete(apiUrl)
                .then(response => {
                    if (response.status === 200) {
                        // Handle successful response
                        toastr.success('Leave deleted successfully!');
                        this.closeDeleteLeaveModal();
                        // Refresh the leaves after successful deletion
                        this.fetchLeaves();
                    } else {
                        // Handle unexpected response status
                        toastr.error('Error deleting leave!');
                        this.closeDeleteLeaveModal();
                    }
                })
                .catch(error => {
                    // Handle error
                    toastr.error('Error deleting leave!');
                    this.closeDeleteLeaveModal();
                });
        },


        closeApproveLeaveModal() {
            this.approveLeaveModal = false;
        },

        closeCancelLeaveModal() {
            this.cancelLeaveModal = false;
        },

        closeDeleteLeaveModal() {
            this.deleteLeaveModal = false;
        },


    },
};
</script>
