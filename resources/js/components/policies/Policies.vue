<template>
    <v-row>
        <v-col>
            <!-- Add Policy Dialog -->
            <v-dialog v-model="addPolicyDialog" max-width="600px" persistent>
                <v-card>
                    <v-card-title class="headline">
                        <v-icon>mdi-plus</v-icon>
                        Add Policy
                    </v-card-title>
                    <v-card-text>
                        <v-form @submit.prevent="addPolicy">
                            <v-text-field v-model="newPolicy.title" label="Policy Title"></v-text-field>
                            <v-textarea v-model="newPolicy.description" label="Description"></v-textarea>
                            <v-menu v-model="datePicker1" :close-on-content-click="false" transition="scale-transition"
                                offset-y>
                                <template v-slot:activator="{ on, attrs }">
                                    <v-text-field v-model="newPolicy.effective_date" label="Effective Date"
                                        v-bind="attrs" v-on="on"></v-text-field>
                                </template>
                                <v-date-picker v-model="newPolicy.effective_date" no-title></v-date-picker>
                            </v-menu>

                            <v-menu v-model="datePicker2" :close-on-content-click="false" transition="scale-transition"
                                offset-y>
                                <template v-slot:activator="{ on, attrs }">
                                    <v-text-field v-model="newPolicy.revision_date" label="Revision Date" v-bind="attrs"
                                        v-on="on"></v-text-field>
                                </template>
                                <v-date-picker v-model="newPolicy.revision_date" no-title></v-date-picker>
                            </v-menu>
                            <v-select v-model="newPolicy.status" :items="['Draft', 'Published', 'Archived']"
                                label="Status"></v-select>
                            <v-select v-model="newPolicy.visibility" :items="['Public', 'Private']"
                                label="Visibility"></v-select>

                            <!-- New attachment input field -->
                            <v-file-input v-model="newPolicy.attachment" label="Attachment"
                                placeholder="Select file..."></v-file-input>

                            <div class="d-flex justify-space-between">
                                <v-btn @click="closeAddPolicyDialog">
                                    <v-icon>mdi-close</v-icon>
                                    Cancel
                                </v-btn>
                                <v-btn type="submit" color="primary">
                                    <v-icon>mdi-check</v-icon>
                                    Add
                                </v-btn>
                            </div>
                        </v-form>
                    </v-card-text>
                </v-card>
            </v-dialog>

            <v-data-table :headers="headers" :items="policies" item-key="id">

                <template v-slot:item="{ item }">
                    <tr>
                        <td>{{ item.title }}</td>
                        <td>{{ item.description.substring(0, 50) + ' ....' }}</td>
                        <td>{{ item.status }}</td>
                        <td>
                            <v-btn @click="downloadPolicy(item.id)" color="primary" small>
                                <v-icon>mdi-download</v-icon>
                                Download
                            </v-btn>
                        </td>
                        <td>{{ item.effective_date }}</td>
                        <td>{{ formatDate(item.created_at) }}</td>
                        <td>
                            <v-icon @click="viewPolicy(item.id)" color="info">mdi-eye</v-icon>
                            <v-icon @click="editPolicy(item.id)" color="primary">mdi-pencil</v-icon>
                            <v-icon @click="publishPolicy(item.id)" color="dark">mdi-lock</v-icon>
                            <v-icon @click="revokePolicy(item.id)" color="dark">mdi-cancel</v-icon>
                            <v-icon @click="deletePolicy(item.id)" color="red">mdi-delete</v-icon>
                        </td>
                    </tr>
                </template>
            </v-data-table>
        </v-col>
    </v-row>
</template>

<script>
export default {
    data() {
        return {
            datePicker1: false,
            datePicker2: false,
            base_url: '/',
            headers: [
                { title: 'Policy Title', key: 'title' },
                { title: 'Description', key: 'description' },
                { title: 'Status', key: 'status' },
                { title: 'Attachment', key: 'attachment', sortable: false },
                { title: 'Effective Date', key: 'effective_date' },
                { title: 'Created', key: 'created_at' },
                { title: 'Action', key: 'actions', sortable: false },
            ],
            policies: [],
            addPolicyDialog: false,
            newPolicy: {
                title: '',
                description: '',
                effective_date: null,
                revision_date: null,
                status: '',
                visibility: '',
                policy_id: null,
                attachement: null,
            },
        };
    },
    created() {
        this.fetchPolicies();
    },
    methods: {
        fetchPolicies() {
            const apiUrl = this.base_url + 'api/v1/policies';
            axios.get(apiUrl)
                .then(response => {
                    this.policies = response.data.policies;
                })
                .catch(error => {
                    console.error('Error fetching policies:', error);
                });
        },
        formatDate(dateString) {
            // Implement your date formatting logic here
        },
        openAddPolicyDialog() {
            this.addPolicyDialog = true;
        },
        closeAddPolicyDialog() {
            this.addPolicyDialog = false;
            // Clear form fields
            this.newPolicy = {
                title: '',
                description: '',
                effective_date: null,
                revision_date: null,
                status: '',
                visibility: '',
                policy_id: null,
                attachement: null,
            };
        },
        addPolicy() {
            // Implement logic to add a new policy
            // Access form fields from this.newPolicy
            // e.g., this.newPolicy.title, this.newPolicy.description, etc.
            // Don't forget to close the dialog after adding the policy
            console.log('Adding policy:', this.newPolicy);
            // Close the dialog
            this.closeAddPolicyDialog();
        },
        // ... other methods for actions like downloadPolicy, viewPolicy, etc.
    },
};
</script>

<style scoped>
/* Add your custom styles here */
</style>
