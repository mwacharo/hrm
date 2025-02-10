<template>
    <v-container fluid>
        <v-row>
            <v-responsive>
                <v-data-table v-model="selected" :headers="headers" :items="tickets" item-key="id" class="elevation-1"
                    show-select>
                    <template v-slot:top>
                        <v-toolbar flat color="primary">
                            <v-btn class="mr-4" @click="openCreateDialog">Create Ticket (CTR+N)</v-btn>
                            <v-select v-model="selectedFilter" :items="filters" label="-- select --" class="mr-4">
                            </v-select>

                            <v-text-field v-model="search" append-icon="mdi-magnify" label="Introduce your search"
                                single-line hide-details>
                            </v-text-field>
                        </v-toolbar>
                        <v-row v-if="selected.length > 0" class="bg-primary">
                            <v-col>
                                <v-icon class="mx-3" color="white" @click="" title="Update status"
                                    size="15">mdi-account-clock
                                </v-icon>
                                <v-icon class="mx-3" color="white" @click="" title="Add a comments"
                                    size="15">mdi-comment
                                </v-icon>
                                <v-icon class="mx-3" color="white" @click="" title="Delete tickets" size="15">mdi-delete
                                </v-icon>
                            </v-col>
                        </v-row>
                    </template>
                    <template v-slot:item.followers="{ item }">
                        <td>
                            <span v-if="item.followers">
                                {{ trimFollowers(item.followers) }}
                                <v-tooltip activator="parent" location="top"> {{ item.followers }}</v-tooltip>
                            </span>
                            <span v-else>
                                {{ trimFollowers(item.followers) }}
                            </span>
                        </td>
                    </template>
                    <template v-slot:item.actions="{ item }">
                        <v-icon small class="mr-2" color="blue" title="View history"
                            @click="fetchTicketComments(item)">mdi-history
                        </v-icon>
                        <v-icon v-if="item.user_id === user_id" title="Edit ticket" small class="mr-2" color="blue"
                            @click="editTicket(item)">mdi-pencil</v-icon>
                        <v-icon v-if="item.user_id === 1" small title="Delete ticket" class="mr-2" color="red"
                            @click="deleteTicket(item)">mdi-delete</v-icon>
                        <v-icon small class="mr-2" color="green" title="Assign user"
                            @click="openAssignUserDialog(item)">mdi-account-plus</v-icon>
                        <v-icon small class="mr-2" color="orange" title="Close ticket"
                            @click="markAsClosed(item)">mdi-checkbox-marked-circle
                        </v-icon>
                        <v-icon small class="mr-2" color="purple" @click="openCommentDialog(item)">mdi-comment</v-icon>
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
                    <v-col cols="12">
                        <v-text-field v-model="editedTicket.title" label="Subject" placeholder="Enter ticket subject"
                            :rules="[v => !!v || 'Title is required']" addressee>
                        </v-text-field>
                    </v-col>
                    <v-col cols="12">
                        <v-select v-model="editedTicket.category" :items="categories" label="Category"
                            placeholder="Select category" :rules="[v => !!v || 'Category is required']" clearable>
                        </v-select>
                    </v-col>
                    <v-col cols="12">
                        <v-textarea v-model="editedTicket.description" label="Description(Optional)"
                            placeholder="Enter ticket description">
                        </v-textarea>
                    </v-col>
                    <v-col cols="12">
                        <v-select v-model="editedTicket.addressed_to" :items="admins" label="Address To" item-value="id"
                            item-title="fullname" placeholder="Select Addressee"
                            :rules="[v => !!v || 'Addressee is required']" clearable>
                        </v-select>
                    </v-col>
                    <v-col cols="12">
                        <v-select v-model="editedTicket.followers" :items="users" label="Followers (Optional)"
                            item-value="id" item-title="fullname" placeholder="Select followers" multiple clearable>
                        </v-select>

                    </v-col>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="red" text @click="closeDialog">Cancel</v-btn>
                    <v-btn color="green" text @click="saveTicket">Save</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- Assign User Dialog -->
        <v-dialog v-model="showAssignUserDialog" max-width="400px">
            <v-card>
                <v-card-title>
                    <span class="headline">Assign User</span>
                </v-card-title>
                <v-card-text>
                    <v-select v-model="selectedUser" :items="admins" label="Select User" item-value="id"
                        item-title="fullname" placeholder="Select user"></v-select>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="red" text @click="closeAssignUserDialog">Cancel</v-btn>
                    <v-btn color="blue darken-1" text @click="assignUserToTicket">Assign</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- Comment Dialog -->
        <v-dialog v-model="showCommentDialog" max-width="600px">
            <v-card>
                <v-card-title>
                    <span class="headline">Add Comment</span>
                </v-card-title>
                <v-card-text>
                    <v-textarea v-model="comment" label="Comment" placeholder="Enter your comment"></v-textarea>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="danger" text @click="closeCommentDialog">Cancel</v-btn>
                    <v-btn color="blue darken-1" text @click="addCommentToTicket">Add Comment</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- ticket resolution Dialog -->
        <v-dialog v-model="ticketResolutionDialog" max-width="600px">
            <v-card>
                <v-card-title>
                    <span class="headline">Resolution</span>
                </v-card-title>
                <v-card-text>
                    <v-text-field v-model="closeTicket.status" label="Status" readonly>
                    </v-text-field>
                    <v-textarea v-model="closeTicket.resolution" label="Resolution" placeholder="Enter your resolution">
                    </v-textarea>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="red" text @click="ticketResolutionDialog = false">Cancel</v-btn>
                    <v-btn color="green darken-1" @click="submitTicketResoluton">Save</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <!--Ticket Comments Dialog -->
        <v-dialog v-model="ticketCommentsDialog" max-width="600px">
            <v-card>
                <v-card-title>
                    <span class="text-success">History & Comments</span>
                </v-card-title>
                <v-card-text>
                    <v-dialog v-model="ticketCommentsDialog" max-width="600px">
                        <v-card>
                            <v-card-title class="text-success text-h6">
                                History & Comments
                            </v-card-title>
                            <v-card-text>
                                <v-list dense>
                                    <v-list-item v-for="comment in ticketComments" :key="comment.id">
                                        <v-list-item-content>
                                            <v-list-item-title class="text-subtitle-2 text--primary">
                                                {{ comment.user }}
                                            </v-list-item-title>
                                            <v-list-item-content class="text-body-2">
                                                {{ comment.comment }}
                                            </v-list-item-content>
                                            <v-card-actions class="action-buttons">
                                                <span class="text-caption text--secondary">{{ comment.time }}</span>
                                                <v-btn icon small @click="likeComment(comment.id)">
                                                    <v-icon small color="primary">mdi-thumb-up-outline</v-icon>
                                                </v-btn>
                                                <span @click="likeComment(comment.id)"
                                                    class="action-text text-caption text--secondary">Like</span>
                                                <v-btn icon small @click="replyToComment(comment.id)">
                                                    <v-icon small color="primary">mdi-reply-outline</v-icon>
                                                </v-btn>
                                                <span @click="replyToComment(comment.id)"
                                                    class="action-text text-caption text--secondary">Reply</span>
                                            </v-card-actions>
                                        </v-list-item-content>
                                        <v-divider></v-divider>
                                    </v-list-item>
                                </v-list>
                            </v-card-text>
                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn color="danger" text @click="ticketCommentsDialog = false">Close</v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-dialog>

                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="danger" text @click="ticketCommentsDialog = false">Close</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

    </v-container>
</template>

<script>
export default {
    props: {
        user_id: {
            type: Number,
            required: true,
        },
    },
    data() {
        return {
            selected: [],
            base_url: '/',
            showDialog: false,
            showAssignUserDialog: false,
            showCommentDialog: false,
            search: '',
            selectedFilter: null,
            filters: ['All', 'Closed', 'Request', 'Incident'],
            tickets: [],
            ticketComments: [],
            ticketCommentsDialog: false,
            ticketResolutionDialog: false,
            resolution: '',
            headers: [
                { title: 'Creatied At', value: 'created_at' },
                { title: 'Title', value: 'title' },
                { title: 'Category', value: 'category' },
                { title: 'Priority', value: 'priority' },
                { title: 'Status', value: 'status' },
                { title: 'Created By', value: 'created_by' },
                { title: 'Addressed To', value: 'addressed_to' },
                { title: 'Followers', value: 'followers' },
                { title: 'Last Updated', value: 'updated_at' },
                { title: 'Actions', value: 'actions', sortable: false },
            ],
            editedIndex: -1,
            editedTicket: {
                title: '',
                description: '',
                status: '',
                addressed_to: '',
                followers: [],
                category: '',
                priority: '',
                date_opened: '',
                closed_date: '',
                attachment: '',
                comments: '',
                resolution: ''
            },
            defaultTicket: {
                title: '',
                description: '',
                status: '',
                addressed_to: '',
                followers: [],
                category: '',
                priority: '',
                date_opened: '',
                closed_date: '',
                attachment: '',
                comments: '',
                resolution: ''
            },
            closeTicket: {
                status: 'Closed',
                resolution: ''
            },
            admins: [],
            users: [],
            categories: [
                'Leave Requests',
                'Payroll Issues',
                'Benefits and Compensation',
                'Workplace Issues',
                'Performance and Appraisals',
                'Training and Development',
                'Onboarding and Offboarding',
                'Policy and Procedure Queries',
                'General HR Inquiries',
                'IT and Equipment Requests',
                'Remote Work and Flexibility',
                'Health and Safety',
                'Other'
            ],
            selectedUser: null,
            selectedTicket: null,
            comment: ''
        }
    },
    computed: {
        formTitle() {
            return this.editedIndex === -1 ? 'Create Ticket' : 'Edit Ticket';
        }
    },
    created() {
        this.fetchUsers();
        this.fetchTickets();
    },
    methods: {
        fetchUsers() {
            const apiUrl = `${this.base_url}api/v1/users`;

            axios.get(apiUrl)
                .then((response) => {
                    this.users = response.data.users.map(user => ({
                        ...user,
                        fullname: `${user.firstname} ${user.lastname}`
                    }));

                    this.admins = this.users
                        .filter(user => user.is_hod);
                })
                .catch((error) => {
                    toastr.error('Error fetching users:', error.message);
                });
        },

        fetchTickets() {
            const apiUrl = `${this.base_url}api/v1/tickets`;

            axios.get(apiUrl)
                .then((response) => {
                    this.tickets = response.data.tickets;
                })
                .catch((error) => {
                    toastr.error('Error fetching tickets:', error.message);
                });
        },
        trimFollowers(followers) {
            const maxLength = 1;
            const followersArray = followers.split(',');
            if (followersArray.length > maxLength) {
                return followersArray.slice(0, maxLength).join(', ') + '...';
            }
            return followersArray.join(', ');
        },
        fetchTicketComments(item) {
            const apiUrl = `${this.base_url}api/v1/ticket-comments/${item.id}`;

            axios.get(apiUrl)
                .then((response) => {
                    this.ticketCommentsDialog = true;
                    this.ticketComments = response.data.ticketComments;
                })
                .catch((error) => {
                    toastr.error('Error fetching ticketLogs:', error.message);
                });

        },
        openCreateDialog() {
            this.editedTicket = Object.assign({}, this.defaultTicket);
            this.editedIndex = -1;
            this.showDialog = true;
        },
        editTicket(item) {
            this.editedIndex = this.tickets.indexOf(item);
            this.editedTicket = Object.assign({}, item);
            this.showDialog = true;
        },
        deleteTicket(item) {
            const index = this.tickets.indexOf(item);
            if (confirm('Are you sure you want to delete this ticket?')) {
                const apiUrl = `${this.base_url}api/v1/tickets/${item.id}`;
                axios.delete(apiUrl)
                    .then(() => {
                        this.tickets.splice(index, 1);
                        toastr.success('Ticket deleted successfully');
                    })
                    .catch((error) => {
                        toastr.error('Error deleting ticket:', error.message);
                    });
            }
        },
        closeDialog() {
            this.showDialog = false;
            this.editedTicket = Object.assign({}, this.defaultTicket);
            this.editedIndex = -1;
        },
        saveTicket() {
            if (this.editedIndex > -1) {
                const apiUrl = `${this.base_url}api/v1/tickets/${this.editedTicket.id}`;
                axios.put(apiUrl, this.editedTicket)
                    .then(() => {
                        Object.assign(this.tickets[this.editedIndex], this.editedTicket);
                        this.closeDialog();
                        this.$toastr.success('Success');
                    })
                    .catch((error) => {
                        this.$toastr.error('Failed, try again!');
                        console.log(error)
                    });
            } else {
                const apiUrl = `${this.base_url}api/v1/tickets`;
                axios.post(apiUrl, this.editedTicket)
                    .then((response) => {
                        this.$toastr.success('Success');
                        this.fetchTickets()
                        this.closeDialog();
                        console.log(response)
                    })
                    .catch((error) => {
                        this.$toastr.error('Error creating ticket:', error.message);
                    });
            }
        },
        discardCriteria() {
            this.selectedFilter = null;
            this.search = '';
            this.fetchTickets();
        },
        openAssignUserDialog(item) {
            this.selectedTicket = item;
            this.showAssignUserDialog = true;
        },
        closeAssignUserDialog() {
            this.showAssignUserDialog = false;
            this.selectedUser = null;
        },
        assignUserToTicket() {
            if (this.selectedUser) {
                this.selectedTicket.addressed_to = this.selectedUser;
                const apiUrl = `${this.base_url}api/v1/tickets/${this.selectedTicket.id}`;
                axios.put(apiUrl, this.selectedTicket)
                    .then(() => {
                        this.fetchTickets();
                        this.closeAssignUserDialog();
                        toastr.success('User addressed successfully');
                    })
                    .catch((error) => {
                        toastr.error('Error assigning user:', error.message);
                    });
            } else {
                toastr.warning('Please select a user to assign');
            }
        },
        markAsClosed(item) {
            this.ticketResolutionDialog = true
            // item.status = 'Closed';
            // const apiUrl = `${this.base_url}api/v1/tickets/${item.id}`;
            // axios.put(apiUrl, item)
            //     .then(() => {
            //         this.fetchTickets();
            //         toastr.success('Ticket marked as closed');
            //     })
            //     .catch((error) => {
            //         toastr.error('Error marking ticket as closed:', error.message);
            //     });
        },
        submitTicketResoluton() {

        },
        openCommentDialog(item) {
            this.selectedTicket = item;
            this.comment = '';
            this.showCommentDialog = true;
        },
        closeCommentDialog() {
            this.showCommentDialog = false;
            this.comment = '';
        },
        addCommentToTicket() {
            if (this.comment) {
                const apiUrl = `${this.base_url}api/v1/tickets/${this.selectedTicket.id}/comments`;
                axios.post(apiUrl, { comment: this.comment })
                    .then((response) => {
                        this.fetchTickets();
                        this.closeCommentDialog();
                        this.$toastr.success(response.data.message);
                    })
                    .catch((error) => {
                        this.$toastr.error('Error adding comment:', error.message);
                    });
            } else {
                this.$toastr.error('Please enter a comment');
            }
        }
    }
}
</script>
