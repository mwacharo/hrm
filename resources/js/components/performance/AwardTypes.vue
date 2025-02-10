<template>
    <v-container fluid>
        <v-row>
            <v-col cols="12">
                <v-row align="center" justify="space-between" style="width: 100%;">
                    <v-col cols="8">
                        <v-text-field v-model="search" append-icon="mdi-magnify" label="Search" single-line
                            hide-details>
                        </v-text-field>
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
                            size="15">mdi-account-clock
                        </v-icon>
                        <v-icon class="mx-3" color="primary" @click="addNotesModal = true" title="Update target"
                            size="15">mdi-target
                        </v-icon>
                        <v-icon class="mx-3" color="error" @click="deleteAttendanceModal = true" title="Delete awards"
                            size="15">mdi-delete
                        </v-icon>
                    </v-col>
                </v-row>

                <v-data-table v-model="selected" :headers="headers" :items="awardTypes" show-select :search="search">
                    <template v-slot:item.status="{ item }">
                        <v-icon :color="item.status ? 'green' : 'red'">
                            {{ item.status ? 'mdi-check-circle' : 'mdi-close-circle' }}
                        </v-icon>
                    </template>
                    <template v-slot:item.actions="{ item }">
                        <v-icon small color="black" @click="viewHistory(item)">mdi-history</v-icon>
                        <v-icon small color="blue" class="mx-2" @click="editAwardType(item)">mdi-pen</v-icon>
                        <v-icon small color="red" @click="confirmDelete(item)">mdi-delete</v-icon>
                    </template>
                </v-data-table>
            </v-col>
        </v-row>

        <v-dialog v-model="showAddModal" persistent max-width="600px">
            <v-card>
                <v-card-title>
                    <span class="headline">Add Award Type</span>
                </v-card-title>
                <v-card-text>
                    <v-form ref="addForm" v-model="valid">
                        <v-text-field label="Name" placeholder="Enter award name" v-model="newAwardType.name"
                            :rules="[rules.required]">
                        </v-text-field>

                        <v-text-field label="Reward(Ksh)" placeholder="2000" v-model="newAwardType.reward"
                            :rules="[rules.required]">
                        </v-text-field>
                        <v-text-field label="Target Points(%)" placeholder="70" v-model="newAwardType.target"
                            :rules="[rules.required]">
                        </v-text-field>
                        <v-textarea label="Description(Optional)" placeholder="Enter award description"
                            v-model="newAwardType.description">
                        </v-textarea>
                    </v-form>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="red" text @click="showAddModal = false">Cancel</v-btn>
                    <v-btn color="blue darken-1" text @click="addAwardType">Save</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <v-dialog v-model="showEditModal" persistent max-width="600px">
            <v-card>
                <v-card-title>
                    <span class="headline">Edit Award Type</span>
                </v-card-title>
                <v-card-text>
                    <v-form ref="editForm" v-model="valid">
                        <v-text-field label="Name" placeholder="Enter award name" v-model="editedAwardType.name"
                            :rules="[rules.required]">
                        </v-text-field>

                        <v-text-field label="Reward" placeholder="Enter reward" v-model="editedAwardType.reward"
                            :rules="[rules.required]">
                        </v-text-field>
                        <v-text-field label="Target Points(%)" placeholder="70" v-model="editedAwardType.target"
                            :rules="[rules.required]">
                        </v-text-field>
                        <v-textarea label="Description(Optional)" placeholder="Enter award description"
                            v-model="editedAwardType.description">
                        </v-textarea>
                    </v-form>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="red" text @click="showEditModal = false">Cancel</v-btn>
                    <v-btn color="blue darken-1" text @click="updateAwardType">Save</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <v-dialog v-model="showDeleteModal" persistent max-width="400px">
            <v-card>
                <v-card-title class="headline">Confirm Delete</v-card-title>
                <v-card-text>Are you sure you want to delete this award type?</v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="blue darken-1" text @click="showDeleteModal = false">Cancel</v-btn>
                    <v-btn color="blue darken-1" text @click="deleteAwardType">Delete</v-btn>
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
            awardTypes: [],
            headers: [
                { title: 'Name', value: 'name' },
                { title: 'Description', value: 'description' },
                { title: 'Reward', value: 'reward' },
                { title: 'Target(%)', value: 'target' },
                { title: 'Status', value: 'status' },
                { title: 'Actions', value: 'actions', sortable: false },
            ],
            search: '',
            showAddModal: false,
            showEditModal: false,
            showDeleteModal: false,
            newAwardType: {
                name: '',
                description: '',
                reward: '',
                target: '',
            },
            editedAwardType: null,
            awardTypeToDelete: null,
            statuses: ['Active', 'Inactive'],
            rules: {
                required: value => !!value || 'Required.',
            },
            valid: false,
        };
    },
    mounted() {
        this.fetchAwardTypes();
    },
    methods: {
        fetchAwardTypes() {
            axios.get('/api/v1/award-types')
                .then(response => {
                    this.awardTypes = response.data;
                })
                .catch(error => {
                    console.error("There was an error fetching the award types: ", error);
                    this.awardTypes = [];
                });
        },
        addAwardType() {
            if (this.$refs.addForm.validate()) {
                axios.post('/api/v1/award-types', this.newAwardType)
                    .then(response => {
                        this.$toastr.success('Award type added successfully!');
                        this.fetchAwardTypes()
                        this.showAddModal = false;
                        this.newAwardType = { name: '', description: '', reward: '', target: '' };
                        console.log(response)
                    })
                    .catch(error => {
                        this.$toastr.error('Failed to add award type. Please try again later!');
                        console.error("There was an error adding the award type: ", error);
                    });
            }
        },
        editAwardType(item) {
            this.editedAwardType = { ...item };
            this.showEditModal = true;
        },
        updateAwardType() {
            if (this.$refs.editForm.validate()) {
                axios.put(`/api/v1/award-types/${this.editedAwardType.id}`, this.editedAwardType)
                    .then(response => {
                        const index = this.awardTypes.findIndex(awardType => awardType.id === this.editedAwardType.id);
                        if (index !== -1) {
                            this.awardTypes.splice(index, 1, response.data);
                        }
                        this.$toastr.success('Award type updated successfully!');
                        this.showEditModal = false;
                    })
                    .catch(error => {
                        this.$toastr.error('Failed to update award type. Please try again later!');
                        console.error("There was an error updating the award type: ", error);
                    });
            }
        },

        confirmDelete(item) {
            this.awardTypeToDelete = item;
            this.showDeleteModal = true;
        },
        deleteAwardType() {
            axios.delete(`/api/v1/award-types/${this.awardTypeToDelete.id}`)
                .then(() => {
                    this.awardTypes = this.awardTypes.filter(awardType => awardType.id !== this.awardTypeToDelete.id);
                    this.$toastr.success('Award type deleted successfully!');
                    this.showDeleteModal = false;
                })
                .catch(error => {
                    this.$toastr.error('Failed to delete award type. Please try again later!');
                    console.error("There was an error deleting the award type: ", error);
                });
        },
        viewHistory(item) {
            // Handle the logic to view the history of the award type here.
            axios.get(`/api/v1/award-types/${item.id}/history`)
                .then(response => {
                    console.log('History: ', response.data);
                    // You can display the history data as needed.
                })
                .catch(error => {
                    console.error('Error fetching history: ', error);
                });
        },
    },
};
</script>

<style scoped>
/* Add any custom styles here */
</style>
