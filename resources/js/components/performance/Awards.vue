<template>
    <v-layout>
        <v-navigation-drawer location="right" width="500" v-model="drawer" temporary>
            <v-container>
                <v-row justify="space-between" class="drawer-header">
                    <v-col>
                        <v-list-item-title>Filter</v-list-item-title>
                    </v-col>
                    <v-col class="text-right">
                        <v-icon @click="drawer = false">mdi-close</v-icon>
                    </v-col>
                </v-row>
                <v-divider></v-divider>
                <v-row align="center" justify="center">
                    <v-col cols="12">
                        <v-list dense nav>
                            <v-list-item>
                                <v-col cols="12">
                                    <v-label>Award Type:</v-label>
                                    <v-select v-model="filters.award_type_id" item-value="id" item-title="name"
                                        :items="awardTypes" clearable dense>
                                    </v-select>
                                </v-col>
                            </v-list-item>
                            <v-list-item>
                                <v-col cols="12">
                                    <v-label>Period:</v-label>
                                    <v-text-field v-model="filters.period">
                                    </v-text-field>
                                </v-col>
                            </v-list-item>
                            <v-list-item>
                                <v-col cols="12">
                                    <v-label>Employee:</v-label>
                                    <v-autocomplete v-model="filters.employee_id" :items="employees"
                                        item-title="fullName" item-value="id" clearable dense>
                                    </v-autocomplete>
                                </v-col>
                            </v-list-item>
                        </v-list>
                    </v-col>
                </v-row>
                <v-row align="center" justify="center" class="drawer-footer">
                    <v-col cols="12">
                        <v-btn @click.prevent="filterAwards">
                            <v-icon>mdi-filter</v-icon>
                        </v-btn>
                    </v-col>
                </v-row>
            </v-container>
        </v-navigation-drawer>
        <v-main>
            <v-row class="mb-4">
                <v-col>
                    <v-text-field v-model="search" label="Search Awards" clearable>
                    </v-text-field>
                </v-col>
                <v-col cols="auto">
                    <v-icon size="16" color="success mx-1" @click="generateExcel" v-if="awards.length > 0"
                        small>mdi-file-excel
                    </v-icon>
                    <v-icon size="16" color="primary mx-1" @click.stop="drawer = !drawer" small>mdi-filter</v-icon>
                    <v-icon size="16" color="dark mx-2" @click="fetchAwards" small>mdi-refresh</v-icon>
                    <v-icon color="primary" @click="openDialog('add')">mdi-plus</v-icon>
                </v-col>

            </v-row>
            <v-divider></v-divider>
            <v-row v-if="selected.length > 0">
                <v-col>
                    <v-icon class="mx-3" color="success" @click="updateStatusesModal = true" title="Update status"
                        size="15">mdi-account-clock
                    </v-icon>
                    <v-icon class="mx-3" color="primary" @click="addNotesModal = true" title="Notify candidates"
                        size="15">mdi-bell
                    </v-icon>
                    <v-icon class="mx-3" color="error" @click="deleteAttendanceModal = true" title="Delete awards"
                        size="15">mdi-delete
                    </v-icon>
                </v-col>
            </v-row>
            <v-data-table v-model="selected" :headers="awardHeaders" :items="awards" class="elevation-1"
                :items-per-page="10" item-value="id" dense show-select :search="search">
                <template v-slot:item.awardName="{ item }">
                    <div>
                        <strong>{{ item.name }}</strong>
                        <div class="text-subtitle-2">{{ item.description }}</div>
                    </div>
                </template>
                <template v-slot:item.rewardEligibility="{ item }">
                    <v-span :color="item.rewardEligibility ? 'green' : 'red'" dark>
                        {{ item.rewardEligibility ? 'Eligible' : 'Not Eligible' }}
                    </v-span>
                </template>
                <template v-slot:item.action="{ item }">
                    <v-icon x-small color="green" @click="viewScore(item)">mdi-chart-line</v-icon>
                    <v-icon small color="blue" class="mx-2" @click="openDialog('edit', item)">mdi-pen</v-icon>
                    <v-icon small color="red" @click="deleteAward(item.id)">mdi-delete</v-icon>
                </template>
            </v-data-table>

            <v-dialog v-model="dialog" max-width="600px">
                <v-card>
                    <v-card-title>
                        <span class="headline">{{ dialogType === 'add' ? 'Add New Award' : 'Edit Award' }}</span>
                    </v-card-title>
                    <v-card-text>
                        <v-form ref="form" v-model="formValid">
                            <v-select v-model="form.employee_id" :items="employees" item-title="fullName"
                                item-value="id" label="Employee" :rules="[v => !!v || 'Employee is required']">
                            </v-select>
                            <v-select v-model="form.award_type_id" :items="awardTypes" item-title="name" item-value="id"
                                label="Award Type" :rules="[v => !!v || 'Award type is required']">
                            </v-select>
                            <v-text-field v-model="form.points" label="Points(%)" placeholder="74"
                                :rules="[v => !!v || 'Points is required']">
                            </v-text-field>
                            <v-text-field v-model="form.period" placeholder="01 2024" label="Period(MM YYYY)"
                                :rules="[v => !!v || 'Month & Year is required']">
                            </v-text-field>
                            <v-textarea v-model="form.notes" label="Notes (Optional)">
                            </v-textarea>
                        </v-form>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="red" @click="closeDialog">Cancel</v-btn>
                        <v-btn color="blue darken-1" text @click="saveAward">Save</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <v-dialog v-model="scoreDialog" max-width="600px">
                <v-card>
                    <v-card-title>
                        <span class="headline">Performance metrics</span>
                    </v-card-title>
                    <v-card-text>
                        <h6>Name: {{ }}</h6>

                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" text @click="scoreDialog = false">Cancel</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <v-dialog v-model="deleteDialog" max-width="500px">
                <v-card>
                    <v-card-title class="headline">Are you sure you want to delete this award?</v-card-title>
                    <v-card-text>
                        <div>{{ selectedAward.name }}</div>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="red darken-1" text @click="closeDeleteDialog">Cancel</v-btn>
                        <v-btn color="blue darken-1" text @click="confirmDeleteAward">Delete</v-btn>
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
            selected: [],
            search: '',
            drawer: false,
            awards: [],
            employees: [],
            awardTypes: [],
            awardHeaders: [
                { title: 'Award Name', value: 'name' },
                { title: 'Employee', value: 'employee' },
                { title: 'Department', value: 'department' },
                { title: 'Score(%)', value: 'points' },
                { title: 'Target(%)', value: 'target' },
                { title: 'Reward', value: 'reward' },
                { title: 'Reward Eligibility', value: 'rewardEligibility' },
                { title: 'Period', value: 'period' },
                { title: 'Action', value: 'action', sortable: false },
            ],
            dialog: false,
            deleteDialog: false,
            scoreDialog: false,
            dialogType: '',
            filters: {
                award_type_id: null,
                employee_id: null,
                period: null,
            },
            form: {
                employee_id: '',
                award_type_id: '',
                points: '',
                period: '',
                notes: ''
            },
            selectedAward: null,
            formValid: false,
        };
    },
    created() {
        this.fetchEmployees();
        this.fetchAwardTypes();
        this.fetchAwards();
    },
    methods: {
        fetchAwards() {
            axios.get('/api/v1/awards')
                .then((response) => {
                    this.awards = response.data.map(award => {
                        award.rewardEligibility = award.points >= award.target;
                        return award;
                    });
                })
                .catch((error) => {
                    console.log(error);
                });
        },
        fetchAwardTypes() {
            axios.get('/api/v1/award-types')
                .then((response) => {
                    this.awardTypes = response.data;
                })
                .catch((error) => {
                    console.log(error);
                });
        },
        fetchEmployees() {
            axios.get('/api/v1/users')
                .then((response) => {
                    this.employees = response.data.users.map(user => ({
                        ...user,
                        fullName: `${user.firstname} ${user.lastname}`
                    }));
                })
                .catch((error) => {
                    console.log(error);
                });
        },
        openDialog(type, award = null) {
            this.dialogType = type;
            if (type === 'edit' && award) {
                this.form = { ...award };
            } else {
                this.form = {
                    employee_id: '',
                    award_type_id: '',
                    points: '',
                    period: '',
                    notes: ''
                };
            }
            this.dialog = true;
        },
        closeDialog() {
            this.dialog = false;
        },
        saveAward() {
            if (this.$refs.form.validate()) {
                if (this.dialogType === 'add') {
                    axios.post('/api/v1/awards', this.form)
                        .then(response => {
                            this.$toastr.success('Award added successfully!');
                            this.closeDialog();
                            this.fetchAwards();
                        })
                        .catch(error => {
                            console.error(error);
                            this.$toastr.error('Error adding award.');
                        });
                } else {
                    const index = this.awards.findIndex((award) => award.id === this.form.id);
                    if (index !== -1) {
                        axios.put(`/api/v1/awards/${this.form.id}`, this.form)
                            .then(response => {
                                this.$set(this.awards, index, response.data);
                                this.$toastr.success('Award updated successfully!');
                                this.closeDialog();
                                this.fetchAwards();
                            })
                            .catch(error => {
                                console.error(error);
                                this.$toastr.error('Error updating award.');
                            });
                    }
                }
            }
        },
        filterAwards() {

        },
        deleteAward(id) {
            this.selectedAward = this.awards.find((award) => award.id === id);
            this.deleteDialog = true;
        },
        viewScore() {
            this.scoreDialog = true;
        },
        closeDeleteDialog() {
            this.deleteDialog = false;
        },
        confirmDeleteAward() {
            const index = this.awards.findIndex((award) => award.id === this.selectedAward.id);
            if (index !== -1) {
                axios.delete(`/api/v1/awards/${this.selectedAward.id}`)
                    .then(response => {
                        this.awards.splice(index, 1);
                        this.$toastr.success('Award deleted successfully!');
                    })
                    .catch(error => {
                        console.error(error);
                        this.$toastr.error('Error deleting award.');
                    });
            }
            this.closeDeleteDialog();
        },
    },
};
</script>
