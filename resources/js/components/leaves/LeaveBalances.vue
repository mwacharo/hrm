<template>
    <v-layout>
        <v-navigation-drawer v-model="drawer" temporary location="right" width="500">
            <v-card>
                <v-card-title>Filter Leave Balances</v-card-title>
                <v-card-text>
                    <v-col class="mt-lg-5">
                        <v-autocomplete v-model="filterOptions.user_ids" :items="users" label="Employee"
                            variant="outlined" item-title="fullname" item-value="id" multiple clearable />
                    </v-col>
                    <v-col>
                        <v-select v-model="filterOptions.leave_type_ids" :items="leaveTypes" label="Leave Type"
                            variant="outlined" multiple item-title="name" item-value="id" clearable />
                    </v-col>
                    <v-col>
                        <v-select v-model="filterOptions.country_ids" :items="countries" label="Country"
                            variant="outlined" multiple item-title="name" item-value="id" clearable />
                    </v-col>
                    <v-col class="d-flex justify-end">
                        <v-btn color="dark" @click="filterBalances">
                            <v-icon color="light">mdi-filter</v-icon>
                        </v-btn>
                    </v-col>
                    <v-overlay :value="loading">
                        <v-progress-circular indeterminate color="primary"></v-progress-circular>
                    </v-overlay>
                </v-card-text>
            </v-card>
        </v-navigation-drawer>

        <v-main>
            <v-row justify="end" class="text-right">
                <v-col>
                    <v-text-field v-model="search" label="Search" clearable @clear="clearSearch"></v-text-field>
                </v-col>
                <v-col cols="auto">
                    <v-icon @click="openAddDialog" class="mx-2" title="New Leave Balance">mdi-plus</v-icon>
                    <v-icon @click="drawer = !drawer" color="blue" class="mx-2"
                        title="Filter leave balances">mdi-filter</v-icon>
                    <v-icon @click="fetchLeaveBalances" color="red" class="mx-2"
                        title="Refresh leave balances">mdi-refresh</v-icon>
                    <v-icon @click="generateReport" color="success" class="mx-2"
                        title="Generate report">mdi-file-excel</v-icon>
                </v-col>
            </v-row>
            <v-data-table v-model="selected" :headers="headers" :items="leaveBalances" item-key="id" :search="search"
                responsive show-select>
                <template v-slot:item.user="{ item }">
                    <strong>{{ formatUserName(item.user) }}</strong>
                </template>
                <template v-slot:item.leave_type="{ item }">
                    {{ item.leave_type.name.replace('_', ' ') }}
                </template>
                <template v-slot:item.actions="{ item }">

                    <v-icon @click="viewLeaveHistory(item)" color="info" class="mx-2" title="View Leave">mdi-history
                    </v-icon>
                    <v-icon @click="editLeaveBalance(item)" color="primary" class="mx-2" title="Edit Leave">mdi-pencil
                    </v-icon>
                    <v-icon @click="deleteLeaveBalance(item)" color="danger" class="mx-2"
                        title="Delete Leave">mdi-delete
                    </v-icon>
                </template>
            </v-data-table>

            <v-dialog v-model="dialog" max-width="500px" persistent>
                <v-card>
                    <v-card-title>{{ editing ? 'Edit' : 'Add' }} Leave Balance</v-card-title>
                    <v-card-text>
                        <v-form @submit.prevent="saveLeaveBalance(formData)">
                            <input type="hidden" v-model="formData.id">

                            <v-autocomplete v-model="formData.user_id" :items="users" label="Employee"
                                variant="outlined" item-title="fullname" item-value="id" />

                            <v-autocomplete v-model="formData.leave_type_id" :items="leaveTypes" label="Leave Type"
                                item-title="name" item-value="id" variant="outlined" />

                            <v-text-field v-model="formData.allocated" label="Days Allocated" variant="outlined" />
                            <v-text-field v-model="formData.taken" label="Days Taken" variant="outlined" />
                            <v-text-field v-model="formData.balance" label="Balance" variant="outlined" />
                        </v-form>
                    </v-card-text>
                    <v-card-actions class="justify-end">
                        <v-btn color="error" @click="closeDialog">Cancel</v-btn>
                        <v-btn color="primary" @click="saveLeaveBalance(formData)">Save</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </v-main>
    </v-layout>
</template>

<script>
export default {
    data() {
        return {
            base_url: '/',
            leaveBalances: [],
            countries: [],
            headers: [
                // { title: 'Employee', value: 'user', sortable: true },
                { title: "Employee Name", value: "fullname" },

                { title: 'Leave Type', value: 'leave_type', sortable: true },
                { title: 'Allocated Days', value: 'allocated', sortable: true },
                { title: 'Leave Taken', value: 'taken', sortable: true },
                { title: 'Balance', value: 'balance', sortable: true },
                { title: 'Action', value: 'actions', sortable: false },
            ],
            dialog: false,
            editing: false,
            formData: {
                id: '',
                user_id: '',
                leave_type_id: null,
                allocated: 0,
                taken: 0,
                balance: 0,
            },
            leaveTypes: [],
            users: [],
            filterOptions: {
                user_ids: [],
                leave_type_ids: [],
                country_ids: [],
            },
            search: '',
            loading: false,
            drawer: false,
        };
    },
    mounted() {
        this.fetchLeaveBalances();
        this.fetchUsers();
        this.fetchLeaveTypes();
        this.fetchUnits();
    },

    methods: {


        async apiCall(url, method = 'get', data = null) {
            try {
                this.loading = true;
                const response = await axios[method](url, data);
                this.loading = false;
                return response.data;
            } catch (error) {
                this.loading = false;
                console.error(error);
            }
        },

        async fetchLeaveBalances() {
            const response = await this.apiCall(this.base_url + 'api/v1/leave-balances');
            this.leaveBalances = response.leaveBalances.map(item => ({
                ...item,
                fullname: item.user ? `${item.user.firstname} ${item.user.lastname}` : "Unknown" // Handle null user
            }));
        }
        ,
        async fetchUsers() {
            const { users } = await this.apiCall(this.base_url + 'api/v1/users');
            this.users = users.map(user => ({
                id: user.id,
                fullname: `${user.firstname} ${user.lastname}`,
            }));
        },
        async fetchLeaveTypes() {
            this.leaveTypes = (await this.apiCall(this.base_url + 'api/v1/leave-types')).leaveTypes;
        },
        async fetchUnits() {
            this.countries = (await this.apiCall(this.base_url + 'api/v1/branches')).branches;
        },
        formatUserName(user) {
            return user ? `${user.firstname || ''} ${user.lastname || ''}`.trim() : ' ';
        },
        openAddDialog() {
            this.editing = false;
            this.formData = {
                id: null,
                user_id: null,
                leave_type_id: null,
                balance: null,
                taken: null,
                allocated: null,
            };
            this.dialog = true;
        },
        editLeaveBalance(item) {
            this.editing = true;
            this.formData = { ...item };
            this.dialog = true;
        },
        async deleteLeaveBalance(item) {
            await this.apiCall(`/api/v1/leave-balances/${item.id}`, 'delete');
            this.fetchLeaveBalances();
        },
        async saveLeaveBalance(formData) {
            const apiUrl = formData.id
                ? `/api/v1/leave-balances/${formData.id}`
                : '/api/v1/leave-balances';
            await this.apiCall(apiUrl, formData.id ? 'put' : 'post', formData);
            this.dialog = false;
            this.fetchLeaveBalances();
        },
        closeDialog() {
            this.dialog = false;
        },
        generateReport() {
            console.log('Generating Excel report...');
        },
        async filterBalances() {
            const filterParams = new URLSearchParams();

            if (this.filterOptions.user_ids.length) {
                filterParams.append('user_ids', this.filterOptions.user_ids.join(','));
            }
            if (this.filterOptions.leave_type_ids.length) {
                filterParams.append('leave_type_ids', this.filterOptions.leave_type_ids.join(','));
            }
            if (this.filterOptions.country_ids.length) {
                filterParams.append('country_ids', this.filterOptions.country_ids.join(','));
            }

            try {
                this.loading = true;
                const response = await axios.get(`${this.base_url}api/v1/leave-balances`, {
                    params: filterParams,
                });

                this.leaveBalances = response.data.leaveBalances;
                this.drawer = false;

                console.log('Filtered leave balances:', this.leaveBalances);
            } catch (error) {
                this.$toastr.error('Error filtering leave balances:', error);
            } finally {
                this.loading = false;
            }
        },
        clearSearch() {
            this.search = '';
        },
        viewLeaveHistory(item) {
            console.log('Viewing history for: ', item);
        },
    },
};
</script>

<style scoped></style>
