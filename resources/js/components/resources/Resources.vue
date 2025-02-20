<template>
    <v-container-fluid>
        <div class="text-center">

            <v-chip @click="filterLaptops" class="ma-2" color="indigo" prepend-icon="mdi-laptop">
                Laptops: {{ assets.laptops }}
            </v-chip>

            <v-chip @click="filterDesktops" prepend-icon="mdi-monitor" class="ma-2" color="dark">
                Desktops: {{ assets.desktops }}
            </v-chip>

            <v-chip @click="filterHeadsets" prepend-icon="mdi-headset" class="ma-2" color="primary">
                Headsets: {{ assets.headsets }}
            </v-chip>

            <v-chip @click="filterPhones" :model-value="true" class="ma-2" color="teal" prepend-icon="mdi-cellphone">
                Phones: {{ assets.phones }}
            </v-chip>
        </div>
        <v-row justify="end" class="text-right">
            <v-col>
                <v-row justify="start" class="text-right">
                    <v-col cols="auto">
                        <v-btn v-if="roles.includes('admin')" @click="resourceClear" variant="tonal" icon>
                            <v-tooltip activator="parent" location="top">Asset clearance</v-tooltip>
                            <v-icon color="warning" x-small>mdi-format-rotate-90</v-icon>
                        </v-btn>
                    </v-col>
                </v-row>
            </v-col>
            <v-col>
                <v-row justify="end" class="text-right">
                    <v-col cols="auto">
                        <v-btn v-if="roles.includes('admin')" @click="addResource" icon>
                            <v-tooltip activator="parent" location="top">Add Resource</v-tooltip>
                            <v-icon color="primary">mdi-plus</v-icon>
                        </v-btn>
                    </v-col>
                    <v-col cols="auto" v-if="resources.length > 0">
                        <v-btn v-if="roles.includes('admin')" @click="openFilterDialog" icon>
                            <v-tooltip activator="parent" location="top">Filter Resource</v-tooltip>
                            <v-icon color="primary" x-small>mdi-filter</v-icon>
                        </v-btn>
                    </v-col>
                    <v-col cols="auto" v-if="resources.length > 0">
                        <v-btn v-if="roles.includes('admin')" @click.prevent="downloadReport" icon>
                            <v-tooltip activator="parent" location="top">Generate Report</v-tooltip>
                            <v-icon color="success" x-small>mdi-download</v-icon>
                        </v-btn>
                    </v-col>
                    <v-col cols="auto">
                        <v-btn v-if="roles.includes('admin')" @click.prevent="refreshResources" icon>
                            <v-tooltip activator="parent" location="top">Refresh Assets</v-tooltip>
                            <v-icon color="danger" x-small>mdi-refresh</v-icon>
                        </v-btn>
                    </v-col>
                </v-row>
            </v-col>
        </v-row>
        <v-row>
            <v-responsive>

                <v-data-table v-model="selected" :headers="headers" :items="resources" item-key="id"
                    class="elevation-10" responsive show-select>

                    <template v-slot:item.issued_to="{ item }">
                        <td>
                            <v-span>
                                {{ item.issued_to ? `${item.issued_to.firstname ?? '..'} ${item.issued_to.lastname ??
                                    '..'}` : '..' }}
                            </v-span>
                        </td>
                    </template>

                    <template v-slot:item.issued_by="{ item }">
                        <td>
                            <v-span>
                                {{ item.issued_by ? `${item.issued_by.firstname ?? '..'} ${item.issued_by.lastname ??
                                    '..'}` : '..' }}
                            </v-span>
                        </td>
                    </template>

                    <template v-slot:item.condition="{ item }">
                        <td>
                            <v-span>{{ item.condition ?? '..' }}</v-span>
                        </td>
                    </template>

                    <template v-slot:item.actions="{ item }">
                        <td class="d-flex justify-content-around align-items-center">
                            <!-- Edit User -->
                            <v-icon v-if="roles.includes('admin')" @click="editResource(item)" title="Edit User"
                                color="primary">
                                mdi-pencil
                            </v-icon>

                            <!-- Issue to a User -->
                            <v-icon v-if="roles.includes('admin')" @click="openAssignUserModal(item)"
                                title="Issue to a user" color="orange">
                                mdi-account-arrow-right
                            </v-icon>

                            <!-- Schedule Maintenance -->
                            <v-icon v-if="roles.includes('admin')" @click="scheduleMaintence(item)"
                                title="Schedule Maintenance" color="blue">
                                mdi-calendar-clock
                            </v-icon>

                            <!-- View History -->
                            <v-icon @click="viewHistory(item)" title="View History" color="black">
                                mdi-history
                            </v-icon>

                            <!-- Delete Resource -->
                            <v-icon v-if="roles.includes('admin')" @click="deleteResource(item)" title="Delete"
                                color="red">
                                mdi-delete
                            </v-icon>
                        </td>
                    </template>


                </v-data-table>

            </v-responsive>
        </v-row>
        <v-dialog v-model="dialog" max-width="600px" transition="scale-transition">
            <v-card elevation="12">
                <v-toolbar>
                    <v-toolbar-title>{{ editMode ? 'Edit Resource' : 'Add Resource' }}</v-toolbar-title>
                    <v-spacer></v-spacer>
                    <v-btn icon @click="dialog = false">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-toolbar>
                <v-card-text>
                    <v-form>
                        <v-container>
                            <v-row>
                                <v-col cols="12" sm="6">
                                    <v-select v-model="formData.name"
                                        :items="['Laptop', 'Headset', 'Desktop', 'Phone', 'Charger', 'Mouse', 'Other']"
                                        label="Name" variant="outlined">
                                    </v-select>
                                </v-col>
                                <v-col cols="12" sm="6">
                                    <v-text-field v-model="formData.issuance_date" label="Date of issue" type="date"
                                        variant="outlined">
                                    </v-text-field>
                                </v-col>
                                <v-col cols="12" sm="6">
                                    <v-text-field v-model="formData.serial_no" label="Serial no" placeholder="Serial no"
                                        variant="outlined">
                                    </v-text-field>
                                </v-col>
                                <v-col cols="12" sm="6">
                                    <v-select v-model="formData.category"
                                        :items="['Hardware', 'Software', 'Stationery', 'Furniture', 'Electronics']"
                                        label="Category" variant="outlined">
                                    </v-select>
                                </v-col>
                                <v-col cols="12">
                                    <v-textarea v-model="formData.description" label="Description/Specification"
                                        placeholder="HP HP Pavilion Laptop 15-eg1xxx" variant="outlined">
                                    </v-textarea>
                                </v-col>

                                <v-col cols="12" sm="6">
                                    <v-select :items="users" item-title="fullName" item-value="id" search-input
                                        v-model="formData.issued_to" label="Assign to Employee">
                                    </v-select>

                                </v-col>
                                <v-col cols="12" sm="6">
                                    <v-select v-model="formData.condition"
                                        :items="['New', 'In Use', 'Under Repair', 'Disposed', 'Fault']"
                                        label="Condition" variant="outlined">
                                    </v-select>
                                </v-col>
                                <v-col cols="12">
                                    <v-textarea v-model="formData.comment"
                                        placeholder="8GB RAM,11th Gen Intel® Core™ i7-1195G7 @ 2.90GHz × 8"
                                        label="Comment (Optional)" variant="outlined">
                                    </v-textarea>
                                </v-col>
                            </v-row>
                        </v-container>
                    </v-form>

                </v-card-text>
                <v-card-actions class="justify-space-between">
                    <v-btn color="danger" @click="cancelEdit">close</v-btn>
                    <v-btn color="primary" @click="saveResource">Save</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-dialog v-model="assignUserModal" max-width="500px">
            <v-card>
                <v-card-title>Assign Employee</v-card-title>
                <v-card-text>
            

                    <v-select :items="users" item-title="fullName" item-value="id" search-input
                        v-model="formData.issued_to" label="Assign to Employee">
                    </v-select>


                </v-card-text>
                <v-card-actions class="justify-space-between">
                    <v-btn color="danger" @click="assignUserModal = false">Cancel</v-btn>
                    <v-btn color="primary" variant="tonal" @click="assignUser">Assign</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-dialog v-model="resourceClearanceModal" max-width="500px" persistent>
            <v-card>
                <v-card-title class="text-center">
                    <v-icon>mdi-laptop</v-icon> Asset clearance
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <v-col cols="12">
                        <v-text-field v-model="clearanceForm.serial_no" label="Enter/scan asset serial no"
                            placeholder="Serial no..." @keyup.enter="checkSerialNumber">
                        </v-text-field>
                    </v-col>
                    <v-col cols="12">
                        <v-text-field v-model="clearanceForm.user_id" label="User" ref="user_id" variant="outlined">
                            {{ getUserFullName(clearanceForm.user_id) }}
                        </v-text-field>
                    </v-col>

                    <v-col cols="12">
                        <v-textarea v-model="clearanceForm.comment" label="Comment" placeholder="cleared.."
                            variant="outlined">
                        </v-textarea>
                    </v-col>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions class="justify-space-between">
                    <v-btn @click="resourceClearanceModal = false">Close</v-btn>
                    <v-btn variant="tonal" :loading="loading" @click="loading = !loading" color="primary">
                        Submit
                        <template v-slot:loader>
                            <v-progress-linear indeterminate></v-progress-linear>
                        </template>
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-container-fluid>
</template>
<script>
export default {
    props: {
        user: Object,
        roles: Array,
        permissions: Array

    },
    data() {
        return {
            selected: [],
            resources: [],
            users: [],
            loading: false,
            assignUserModal: false,
            selected: this.User,
            dialog: false,
            drawer: false,
            assets: {
                laptops: 0,
                desktops: 0,
                headsets: 0,
                phones: 0,
            },

            formData: {
                name: '',
                serial_no: '',
                category: '',
                issuance_date: '',
                issued_to: null,
                condition: '',
                description: '',
                comment: '',
                issued_by: null,
            },
            editMode: false,
            editedResourceId: null,
            resourceClearanceModal: false,
            clearanceForm: {
                serial_no: null,
                user_id: null,
                comment: null,
                is_cleared: false,

            },
            headers: [
                { title: 'Name', value: 'name' },
                { title: 'Description', value: 'description' },
                { title: 'Serial No', value: 'serial_no' },
                { title: 'Category', value: 'category' },
                { title: 'Issued To', value: 'issued_to' },
                { title: 'Issued By', value: 'issued_by' },
                { title: 'Condition', value: 'condition' },
                { title: 'Comment', value: 'comment' },

                { title: 'Actions', value: 'actions', sortable: false },
            ],
        };
    },
    watch: {
        loading(val) {
            if (!val) return
            setTimeout(() => (this.loading = false), 2000)
        },
    },

    mounted() {
        console.log("User:", this.user);
        console.log("Roles:", this.roles);
        console.log("Permissions:", this.permissions);
        this.fetchResources();
        this.fetchUsers();


    },
    methods: {

        fetchResources(name) {
            let url = '/api/v1/resources';
            if (name) {
                url += `?name=${name}`;
            }
            axios.get(url)
                .then(response => {
                    this.resources = response.data.resources;
                    this.updateAssetCounts();
                })
                .catch(error => {
                    console.error('Error fetching resources:', error);
                });
        },

        updateAssetCounts() {
            this.assets = {
                laptops: this.resources.filter(resource => resource.name === 'Laptop').length,
                desktops: this.resources.filter(resource => resource.name === 'Desktop').length,
                headsets: this.resources.filter(resource => resource.name === 'Headset').length,
                phones: this.resources.filter(resource => resource.name === 'Phone').length,
            };
        },

        filterResources(name) {
            this.fetchResources(name);
        },
        filterHeadsets() {
            this.filterResources('Headset');
        },

        filterDesktops() {
            this.filterResources('Desktop');
        },

        filterLaptops() {
            this.filterResources('Laptop');
        },

        filterPhones() {
            this.filterResources('Phone');
        },
        fetchUsers() {

            console.log('Fetching users from the API...');


            axios.get('/api/v1/users')

                .then(response => {

                    console.log('API response received:', response);

                    if (response.data && response.data.users) {

                        console.log('Users data found:', response.data.users);

                        this.users = response.data.users.map(user => ({

                            id: user.id,

                            fullName: `${user.firstname} ${user.lastname}`,

                        }));

                        // console.log('Processed users:', this.users); 
                        console.log('Processed users:', JSON.parse(JSON.stringify(this.users)));

                    } else {

                        console.warn('No users found in the response.');

                    }

                })

                .catch(error => {

                    console.error('Error fetching users:', error); // Log the error

                });

        },
        getUserFullName(userId) {
            const user = this.users.find(user => user.id === userId);

            if (user) {
                return user.firstname;
            } else {
                return '';
            }
        },

        addResource() {
            this.editMode = false;
            this.editedResourceId = null;
            this.formData = {
                name: '',
                description: '',
                serial_no: '',
                category: '',
                issued_to: null,
                condition: '',
                issuance_date: '',
                comment: '',
                issued_by: null
            };
            this.dialog = true;
        },
        editResource(resource) {
            this.editMode = true;
            this.editedResourceId = resource.id;
            this.formData = {
                name: resource.name,
                serial_no: resource.serial_no,
                category: resource.category,
                issued_to: resource.issued_to,
                issued_by: resource.issued_by.id,
                condition: resource.condition,
                description: resource.description,
                issuance_date: resource.issuance_date,
                comment: resource.comment
            };
            this.dialog = true;
        },
        saveResource() {
            if (this.editMode) {
                axios.put(`/api/v1/resources/${this.editedResourceId}`, this.formData)
                    .then(response => {
                        this.$toastr.success(response.data.message);
                        this.fetchResources();
                        this.dialog = false;
                    })
                    .catch(error => {
                        console.error('Error updating resource:', error);
                    });
            } else {
                axios.post('/api/v1/resources', this.formData)
                    .then(response => {
                        this.$toastr.success('Success');
                        this.fetchResources();
                        this.dialog = false;
                    })
                    .catch(error => {
                        this.$toastr.error('failed');
                        console.error('Error adding new resource:', error);
                    });
            }
        },
        checkSerialNumber() {
            if (this.clearanceForm.serial_no) {
                const resource = this.resources.find(resource => resource.serial_no === this.clearanceForm.serial_no);
                if (resource) {
                    this.clearanceForm.user_id = resource.user_id;
                } else {
                    this.clearanceForm.user_id = null;
                    this.$toastr.error("The asset is not assigned to a user");
                }
            } else {
                this.clearanceForm.user_id = null;
                this.$toastr.warning("Please enter a serial number");
            }
        },

        resourceClear() {

            this.resourceClearanceModal = true;
        },


        deleteResource(resource) {
            axios.delete(`/api/v1/resources/${resource.id}`)
                .then(response => {
                    this.$toastr.success(response.data.message);
                    this.fetchResources();
                })
                .catch(error => {
                    if (error.response && error.response.data && error.response.data.error) {
                        this.$toastr.error(error.response.data.error);
                    } else {
                        this.$toastr.error('An error occurred while deleting the resource.');
                    }
                    console.error('Error deleting resource:', error);
                    this.fetchResources();
                });
        },

        openAssignUserModal(item) {
            this.assignUserModal = true;
            this.selectedItemId = item.id

        },
        cancelEdit() {
            this.dialog = false;
        },
        refreshResources() {
            this.fetchResources();
        },

        async downloadReport() {
            try {
                const response = await axios.get("/api/v1/asset-report", {
                    responseType: "blob",
                });

                // Create a blob from the response data
                const blob = new Blob([response.data], { type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" });

                // Create a temporary download link
                const link = document.createElement("a");
                link.href = URL.createObjectURL(blob);
                link.download = "asset_report.xlsx";
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            } catch (error) {
                console.error("Error downloading Excel:", error);
            }
        },
        openFilterDialog() {
            this.drawer = true;
        },
        viewResource(resource) {
            // Method to view resource details
            // Implement based on your requirements
        },



    },

};
</script>
