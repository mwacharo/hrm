<template>
    <v-container fluid>
        <v-row>
            <v-col>
                <v-responsive>

                    <v-data-table :headers="headers" :items="voices" item-key="id" class="elevation-1" show-select>
                        <template v-slot:top>
                            <!-- <v-toolbar flat color="primary"> -->
                                <v-col class="d-flex justify-end">
                                <v-btn class="mr-4" color="primary" @click="openDialog">Make Suggestion</v-btn>
                            </v-col>
                                
                            <!-- </v-toolbar> -->
                        </template>

                        <template v-slot:item.status="{ item }">
                            <v-chip :color="getStatusColor(item.status)" dark
                                @click="openStatusDialog(item, 'status')">{{ item.status }}</v-chip>
                        </template>

                        <template v-slot:item.comments="{ item }">
                            <v-chip :color="getStatusColor(item.status)" dark
                                @click="openStatusDialog(item, 'comment')">{{ item.comments }}</v-chip>
                        </template>

                        <template v-slot:item.resolution="{ item }">
                            <v-chip :color="getStatusColor(item.status)" dark
                                @click="openStatusDialog(item, 'resolution')">{{ item.resolution }}</v-chip>
                        </template>

                        <template v-slot:item.priority="{ item }">
                            <v-chip :color="getPriorityColor(item.priority)" dark
                                @click="openStatusDialog(item, 'priority')">{{ item.priority }}</v-chip>
                        </template>

                        <template v-slot:item.actions="{ item }">
                            <v-icon small color="primary" @click="viewVoice(item.id)"
                                title="View Voice">mdi-account-search</v-icon>
                            <v-icon small color="success" @click="editVoice(item)"
                                title="Edit Voice">mdi-pencil</v-icon>
                            <v-icon small color="red" @click="openconfirmDeleteDialog(item)"
                                title="Delete Voice">mdi-delete</v-icon>
                        </template>
                    </v-data-table>
                </v-responsive>
            </v-col>
        </v-row>


        <!-- update status dialog  -->
        <v-dialog v-model="statusDialog" max-width="500px">
            <v-card>
                <v-card-title class="headline">Update {{ dialogField.charAt(0).toUpperCase() + dialogField.slice(1)
                }}</v-card-title>
                <v-card-text>
                    <v-select v-if="dialogField === 'status'" v-model="editedVoice.status" :items="statuses"
                        label="Status" :rules="[v => !!v || 'Status is required']"></v-select>
                    <v-select v-if="dialogField === 'priority'" v-model="editedVoice.priority"
                        :items="['High', 'Medium', 'Low']" label="Priority"
                        :rules="[v => !!v || 'Priority is required']"></v-select>
                    <v-textarea v-if="dialogField === 'comment'" v-model="editedVoice.comment" label="Comment"
                        :rules="[v => !!v || 'Comment is required']"></v-textarea>
                    <v-textarea v-if="dialogField === 'resolution'" v-model="editedVoice.resolution" label="Resolution"
                        :rules="[v => !!v || 'Resolution is required']"></v-textarea>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="green darken-1" text @click="statusDialog = false">Cancel</v-btn>
                    <v-btn color="blue darken-1" text @click="updateField">Update</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- dialog to view voice shows the history of the leave -->
        <v-dialog v-model="viewVoiceDialog" max-width="800px">
            <v-card>
                <v-card-title>
                    <span class="headline">Voice Details</span>
                </v-card-title>
                <v-card-text>
                    <v-container>
                        <v-row>
                            <v-col cols="12">
                                <v-list-item>
                                    <v-list-item-content>
                                        <v-list-item-title>Subject: {{ selectedVoice.subject }}</v-list-item-title>
                                        <v-list-item-subtitle>Description: {{ selectedVoice.description
                                        }}</v-list-item-subtitle>
                                    </v-list-item-content>
                                </v-list-item>
                            </v-col>
                            <v-col cols="12">
                                <v-list-item>
                                    <v-list-item-content>
                                        <v-list-item-title>Category: {{ selectedVoice.category }}</v-list-item-title>
                                        <v-list-item-subtitle>Status: {{ selectedVoice.status }}</v-list-item-subtitle>
                                    </v-list-item-content>
                                </v-list-item>
                            </v-col>
                            <v-col cols="12">
                                <v-list-item>
                                    <v-list-item-content>
                                        <v-list-item-title>Priority: {{ selectedVoice.priority }}</v-list-item-title>
                                        <v-list-item-subtitle>Anonymous: {{ selectedVoice.is_anonymous ? 'Yes' : 'No'
                                        }}</v-list-item-subtitle>
                                    </v-list-item-content>
                                </v-list-item>
                            </v-col>
                            <v-col cols="12">
                                <v-list-item>
                                    <v-list-item-content>
                                        <v-list-item-title>Assigned To:</v-list-item-title>
                                        <v-list-item-subtitle>
                                            <v-chip v-for="user in selectedVoice.assigned_to" :key="user.id"
                                                class="ma-2">
                                                {{ user.fullname }}
                                            </v-chip>
                                        </v-list-item-subtitle>
                                    </v-list-item-content>
                                </v-list-item>
                            </v-col>
                            <v-col cols="12">
                                <v-list-item>
                                    <v-list-item-content>
                                        <v-list-item-title>Followers:</v-list-item-title>
                                        <v-list-item-subtitle>
                                            <v-chip v-for="user in selectedVoice.followers" :key="user.id" class="ma-2">
                                                {{ user.fullname }}
                                            </v-chip>
                                        </v-list-item-subtitle>
                                    </v-list-item-content>
                                </v-list-item>
                            </v-col>
                            <v-col cols="12">
                                <v-list-item>
                                    <v-list-item-content>
                                        <v-list-item-title>Attachments:</v-list-item-title>
                                        <v-list-item-subtitle>
                                            <v-chip v-for="(file, index) in selectedVoice.attachments" :key="index"
                                                class="ma-2">
                                                {{ file.name }}
                                            </v-chip>
                                        </v-list-item-subtitle>
                                    </v-list-item-content>
                                </v-list-item>
                            </v-col>
                            <v-col cols="12">
                                <v-list-item>
                                    <v-list-item-content>
                                        <v-list-item-title>Links:</v-list-item-title>
                                        <v-list-item-subtitle>
                                            <v-chip v-for="(link, index) in selectedVoice.links" :key="index"
                                                class="ma-2">
                                                {{ link }}
                                            </v-chip>
                                        </v-list-item-subtitle>
                                    </v-list-item-content>
                                </v-list-item>
                            </v-col>
                            <v-col cols="12">
                                <v-list-item>
                                    <v-list-item-content>
                                        <v-list-item-title>History:</v-list-item-title>
                                        <v-list-item-subtitle>
                                            <v-timeline>
                                                <v-timeline-item v-for="(history, index) in selectedVoice.history"
                                                    :key="index" :color="getStatusColor(history.status)"
                                                    :icon="getStatusIcon(history.status)">
                                                    <v-card>
                                                        <v-card-title>{{ history.status }}</v-card-title>
                                                        <v-card-subtitle>{{ history.date }}</v-card-subtitle>
                                                        <v-card-text>{{ history.description }}</v-card-text>
                                                    </v-card>
                                                </v-timeline-item>
                                            </v-timeline>
                                        </v-list-item-subtitle>
                                    </v-list-item-content>
                                </v-list-item>
                            </v-col>
                        </v-row>
                    </v-container>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="primary" text @click="viewVoiceDialog = false">Close</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <!-- confirm delete modal -->



        <v-dialog v-model="confirmDeleteDialog" max-width="500px">
            <v-card>
                <v-card-title class="headline">Confirm Delete</v-card-title>
                <v-card-text>Are you sure you want to delete this voice?</v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="green darken-1" text @click="confirmDeleteDialog = false">Cancel</v-btn>
                    <v-btn color="red darken-1" text @click="confirmDelete(editedVoice)">Delete</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>


        <!-- edit voice -->
        <v-dialog v-model="showDialog" max-width="600px">
            <v-card>
                <v-card-title>
                    <span class="headline">{{ formTitle }}</span>
                </v-card-title>
                <v-card-text>
                    <v-container>
                        <v-row>
                            <v-col cols="12">
                                <v-text-field variant="outlined" v-model="editedVoice.subject" label="Subject"
                                    :rules="[v => !!v || 'Subject is required']"></v-text-field>
                            </v-col>
                            <v-col cols="12">
                                <v-select variant="outlined" v-model="editedVoice.category" :items="categories"
                                    label="Category" :rules="[v => !!v || 'Category is required']"></v-select>
                            </v-col>
                            <v-col cols="12">
                                <v-select variant="outlined" v-model="editedVoice.assigned_to" :items="users"
                                    label="Address To" item-value="id" item-title="fullname" multiple
                                    :rules="[v => (v && v.length) || 'Addressee is required']"></v-select>
                            </v-col>
                            <v-col cols="12">
                                <v-select variant="outlined" v-model="editedVoice.followers" :items="users"
                                    label="Followers" item-value="id" item-title="fullname" multiple></v-select>
                            </v-col>
                            <v-col cols="12">
                                <v-textarea variant="outlined" v-model="editedVoice.description" label="Description"
                                    :rules="[v => !!v || 'Description is required']"></v-textarea>
                            </v-col>


                            <v-col>
                                <v-file-input ref="fileInput" v-model="attachments" label="Attach Documents (Optional)"
                                    multiple accept=".pdf, .doc, .docx, .png" outlined clearable
                                    @change="logAttachments"></v-file-input>
                                <div v-if="attachments && attachments.length > 0">
                                    <v-chip v-for="(file, index) in attachments" :key="index" class="ma-2"
                                        close @click:close="removeAttachment(index)">
                                        {{ file.name }}
                                    </v-chip>
                                </div>
                            </v-col>

                            <v-col cols="12">
                                <v-text-field v-model="newLink" label="Attach Link" append-icon="mdi-plus"
                                    @click:append="addLink"></v-text-field>
                                <v-chip v-for="(link, index) in editedVoice.links" :key="index" class="ma-2" close
                                    @click:close="removeLink(index)">
                                    {{ link }}
                                </v-chip>
                            </v-col>
                            <v-col cols="12">
                                <v-switch v-model="editedVoice.is_anonymous"
                                    :color="editedVoice.is_anonymous ? 'green' : 'blue'"
                                    label="Submit Anonymously"></v-switch>
                            </v-col>
                        </v-row>
                    </v-container>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="danger" text @click="closeDialog">Cancel</v-btn>
                    <v-btn color="green darken-1" text @click="saveVoice">Save</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-container>
</template>

<script>
export default {
    data() {
        return {

            statusDialog: false,
            selectedVoice: {},
            viewVoiceDialog: false,
            attachments: [],
            confirmDeleteDialog: false,
            followers: [],
            filteredUsers: [],
            loading: false,
            base_url: '/',
            showDialog: false,
            search: '',
            selectedFilter: null,
            filters: ['All', 'Closed', 'Request', 'Incident'],
            voices: [],
            headers: [
                { title: 'ID', value: 'id' },
                { title: 'Subject', value: 'subject' },
                { title: 'Description', value: 'description' },
                { title: 'Category', value: 'category' },
                // { title: 'Priority', value: 'priority' },
                // { title: 'Date Opened', value: 'created_at' },
                { title: 'Attachments', value: 'attachments' },
                { title: 'Links', value: 'links' },
                { title: 'Addressee', value: 'addressed_to' },
                { title: 'Followers', value: 'followers' },
                { title: 'Comments', value: 'comments' },
                { title: 'Status', value: 'status' },

                // { title: 'Resolution', value: 'resolution' },
                { title: 'Creation Date', value: 'created_at' },
                { title: 'Actions', value: 'actions', sortable: false },
            ],
            editedIndex: -1,
            editedVoice: this.getDefaultVoice(),
            defaultVoice: this.getDefaultVoice(),
            users: [],
            statuses: ['Open', 'In Progress', 'Resolved', 'Closed'],
            categories: ['Harassment', 'Workplace Safety', 'Compensation & Benefits', 'Employee Rights', 'Workload & Scheduling', 'Other'],
            newLink: '',

        };
    },
    computed: {
        formTitle() {
            return this.editedIndex === -1 ? 'Make Suggestion' : 'Edit Suggestion';
        }
    },
    created() {
        this.fetchUsers();
        this.fetchVoices();
    },
    methods:
    {
        openconfirmDeleteDialog(item) {
            this.editedVoice = item;
            this.confirmDeleteDialog = true;
        },


        // logAttachments(attachments) {
        //     console.log('logAttachments called with:', attachments);
        //     if (attachments) {
        //         this.attachments = Array.isArray(attachments) ? [...attachments] : [attachments];
        //         console.log('Updated attachments:', this.attachments);
        //     }

        logAttachments(attachments) {
    console.log('logAttachments called with:', attachments);
    if (attachments) {
        this.attachments = Array.isArray(attachments) ? [...attachments] : [attachments];
        console.log('Updated attachments:', this.attachments);
    }
        },
        removeAttachment(index) {
            console.log('removeAttachment called with index:', index);
            this.attachments.splice(index, 1);
            console.log('Updated attachments after removal:', this.attachments);
        },
        getDefaultVoice() {
            return {
                subject: '',
                status: 'Open',
                assigned_to: [],
                description: '',
                category: '',
                priority: 'Medium',
                is_anonymous: true,
                attachments: [],
                links: [],
                followers: [],
                viewVoiceDialog: false,
            };
        },
        fetchUsers() {
            this.loading = true;
            axios.get(`${this.base_url}api/v1/users`)
                .then(response => {
                    this.users = response.data.users.map(user => ({
                        ...user,
                        fullname: `${user.firstname} ${user.lastname}`
                    }));
                    this.loading = false;
                })
                .catch(error => {
                    this.loading = false;
                    console.error('Error fetching users:', error);
                    this.showError('Failed to load users. Please try again.');
                });
        },
        fetchVoices() {
            this.loading = true;
            axios.get(`${this.base_url}api/v1/voices`)
                .then(response => {
                    // Make sure the property name matches what the API returns
                    this.voices = response.data.complaints || response.data.voices || [];
                    this.loading = false;
                })
                .catch(error => {
                    this.loading = false;
                    console.error('Error fetching voices:', error);
                    this.showError('Failed to load voices. Please try again.');
                });
        },
        viewVoice(id) {
            this.viewVoiceDialog = true;
            // this.loading = true;
            // axios.get(`${this.base_url}api/v1/voices/${id}`)
            // .then(response => {
            //     this.selectedVoice = response.data.voice;
            //     // this.viewVoiceDialog = true;
            //     this.loading = false;
            // })
            // .catch(error => {
            //     this.loading = false;
            //     console.error('Error fetching voice details:', error);
            //     this.showError('Failed to load voice details. Please try again.');
            // });
        },
        openDialog() {
            this.editedIndex = -1;
            this.editedVoice = Object.assign({}, this.defaultVoice);
            this.showDialog = true;
        },
        editVoice(item) {
            this.editedIndex = this.voices.indexOf(item);
            this.editedVoice = Object.assign({}, item);
            this.showDialog = true;
        },
        confirmDelete(item) {
            axios.delete(`${this.base_url}api/v1/voices/${item.id}`)
                .then(() => {
                    this.voices.splice(this.voices.indexOf(item), 1);
                    this.showSuccess('Voice deleted successfully');
                    this.loading = false;
                    this.confirmDeleteDialog = false; // Close the dialog
                })
                .catch(error => {
                    this.loading = false;
                    console.error('Error deleting voice:', error);
                    this.showError('Failed to delete voice. Please try again.');
                });
        }
        ,
        closeDialog() {
            this.showDialog = false;
            this.editedVoice = Object.assign({}, this.defaultVoice);
            this.editedIndex = -1;
        },
        saveVoice() {
            if (!this.validateForm()) {
            return;
            }

            const formData = new FormData();
            formData.append('subject', this.editedVoice.subject);
            console.log('Appending subject:', this.editedVoice.subject);
            formData.append('description', this.editedVoice.description);
            console.log('Appending description:', this.editedVoice.description);
            formData.append('category', this.editedVoice.category);
            console.log('Appending category:', this.editedVoice.category);
            formData.append('status', this.editedVoice.status);
            console.log('Appending status:', this.editedVoice.status);
            formData.append('priority', this.editedVoice.priority);
            console.log('Appending priority:', this.editedVoice.priority);

            // Handle array values correctly
            if (this.editedVoice.assigned_to && this.editedVoice.assigned_to.length) {
            this.editedVoice.assigned_to.forEach(assignee => {
                formData.append('assigned_to[]', assignee);
                console.log('Appending assigned_to:', assignee);
            });
            }

            if (this.editedVoice.followers && this.editedVoice.followers.length) {
            this.editedVoice.followers.forEach(follower => {
                formData.append('followers[]', follower);
                console.log('Appending follower:', follower);
            });
            }

            formData.append('is_anonymous', this.editedVoice.is_anonymous ? 1 : 0);
            console.log('Appending is_anonymous:', this.editedVoice.is_anonymous ? 1 : 0);

            if (this.attachments && this.attachments.length > 0) {
            const file = this.attachments[0];
            console.log('Appending file:', file);
            formData.append('attachments', file);
            } else {
            console.log('No file selected');
            }

            if (this.editedVoice.links && this.editedVoice.links.length) {
            this.editedVoice.links.forEach(link => {
                formData.append('links[]', link);
                console.log('Appending link:', link);
            });
            }

            this.loading = true;
            const url = this.editedIndex === -1
            ? `${this.base_url}api/v1/voices`
            : `${this.base_url}api/v1/voices/${this.editedVoice.id}`;

            const method = this.editedIndex === -1 ? 'post' : 'put';

            axios[method](url, formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
            })
            .then(response => {
                this.loading = false;
                this.showSuccess('Voice saved successfully');
                this.closeDialog();
                this.fetchVoices(); // Refresh the list
            })
            .catch(error => {
                this.loading = false;
                console.error('Error saving voice:', error);
                this.showError('Failed to save voice. Please try again.');
            });
        },openStatusDialog(item, field) {
            this.editedVoice = item;
            this.dialogField = field;
            this.statusDialog = true;
        },
        validateForm() {
            // Basic validation
            if (!this.editedVoice.subject) {
                this.showError('Subject is required');
                return false;
            }
            if (!this.editedVoice.description) {
                this.showError('Description is required');
                return false;
            }
            if (!this.editedVoice.category) {
                this.showError('Category is required');
                return false;
            }
            if (!this.editedVoice.assigned_to || !this.editedVoice.assigned_to.length) {
                this.showError('At least one addressee is required');
                return false;
            }
            return true;
        },
        addLink() {
            if (this.newLink) {
                if (!this.editedVoice.links) {
                    this.editedVoice.links = [];
                }
                this.editedVoice.links.push(this.newLink);
                this.newLink = '';
            }
        },
        removeLink(index) {
            this.editedVoice.links.splice(index, 1);
        },
        getStatusColor(status) {
            switch (status) {
                case 'Open':
                    return 'blue';
                case 'In Progress':
                    return 'orange';
                case 'Resolved':
                    return 'green';
                case 'Closed':
                    return 'red';
                default:
                    return 'grey';
            }
        },
        getPriorityColor(priority) {
            switch (priority) {
                case 'High':
                    return 'red';
                case 'Medium':
                    return 'orange';
                case 'Low':
                    return 'green';
                default:
                    return 'grey';
            }
        },
        showSuccess(message) {
            // Replace with your preferred notification method
            if (typeof this.$toastr !== 'undefined') {
                this.$toastr.success(message);
            } else {
                console.error(message);
            }
        },
        showError(message) {
            // Replace with your preferred notification method
            if (typeof this.$toastr !== 'undefined') {
                this.$toastr.error(message);
            } else {
                console.error(message);
            }
        }
    }
};
</script>









