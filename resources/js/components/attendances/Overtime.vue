<template>
    <v-container>
        <v-row>
            <v-col cols="12">
                <v-card>
                    <v-data-table :headers="headers" :items="overtimes" class="elevation-1">
                        <template v-slot:top>
                            <v-toolbar flat>
                                <v-toolbar-title>Overtime Report</v-toolbar-title>
                                <v-divider class="mx-4" inset vertical></v-divider>
                                <v-spacer></v-spacer>
                                <v-dialog v-model="showCreateModal" max-width="600px">
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-btn color="primary" @click="showCreateModal = true">Add Overtime</v-btn>
                                    </template>
                                    <v-card>
                                        <v-card-title>
                                            <span class="headline">Add Overtime</span>
                                        </v-card-title>
                                        <v-card-text>
                                            <v-form ref="createForm">
                                                <v-text-field v-model="newOvertime.employee" label="Employee Name"
                                                    required></v-text-field>
                                                <v-text-field v-model="newOvertime.department" label="Department"
                                                    required></v-text-field>
                                                <v-text-field v-model="newOvertime.date" label="Date" type="date"
                                                    required></v-text-field>
                                                <v-text-field v-model="newOvertime.hours" label="Hours" type="number"
                                                    required></v-text-field>
                                            </v-form>
                                        </v-card-text>
                                        <v-card-actions>
                                            <v-spacer></v-spacer>
                                            <v-btn color="error" text
                                                @click="showCreateModal = false"><v-icon>mdi-cancel</v-icon>
                                                Cancel</v-btn>
                                            <v-btn color="blue darken-1" text @click="createOvertime">Save</v-btn>
                                        </v-card-actions>
                                    </v-card>
                                </v-dialog>
                            </v-toolbar>
                        </template>
                        <template v-slot:item.actions="{ item }">
                            <v-icon small class="mr-2" @click="editOvertime(item)">mdi-pencil</v-icon>
                            <v-icon small @click="deleteOvertime(item.id)">mdi-delete</v-icon>
                        </template>
                    </v-data-table>
                </v-card>
            </v-col>
        </v-row>

        <!-- Edit Modal -->
        <v-dialog v-model="showEditModal" max-width="600px">
            <v-card>
                <v-card-title>
                    <span class="headline">Edit Overtime</span>
                </v-card-title>
                <v-card-text>
                    <v-form ref="editForm">
                        <v-text-field v-model="currentOvertime.employee" label="Employee Name" required></v-text-field>
                        <v-text-field v-model="currentOvertime.department" label="Department" required></v-text-field>
                        <v-text-field v-model="currentOvertime.date" label="Date" type="date" required></v-text-field>
                        <v-text-field v-model="currentOvertime.hours" label="Hours" type="number"
                            required></v-text-field>
                    </v-form>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="error" text @click="showEditModal = false">Cancel</v-btn>
                    <v-btn color="blue darken-1" text @click="updateOvertime">Save</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-container>
</template>
<script>
export default {
    data() {
        return {
            overtimes: [],
            headers: [
                { title: 'Employee Name', value: 'employee' },
                { title: 'Department', value: 'department' },
                { title: 'Date', value: 'date' },
                { title: 'Hours', value: 'hours' },
                { title: 'Actions', value: 'actions', sortable: false },
            ],
            newOvertime: {
                employee: '',
                department: '',
                date: '',
                hours: ''
            },
            currentOvertime: {},
            showCreateModal: false,
            showEditModal: false
        };
    },
    created() {
        this.fetchOvertimeData();
    },
    methods: {
        async fetchOvertimeData() {
            try {
                const response = await axios.get('/api/v1/overtime');
                this.overtimes = response.data.overtimes;
            } catch (error) {
                console.error('Error fetching overtime data:', error);
            }
        },
        async createOvertime() {
            try {
                const response = await axios.post('/api/v1/overtime', this.newOvertime);
                this.overtimes.push(response.data.overtime);
                this.showCreateModal = false;
                this.resetNewOvertime();
            } catch (error) {
                console.error('Error creating overtime:', error);
            }
        },
        async deleteOvertime(id) {
            try {
                await axios.delete(`/api/v1/overtime/${id}`);
                this.overtimes = this.overtimes.filter(overtime => overtime.id !== id);
            } catch (error) {
                console.error('Error deleting overtime:', error);
            }
        },
        editOvertime(overtime) {
            this.currentOvertime = { ...overtime };
            this.showEditModal = true;
        },
        async updateOvertime() {
            try {
                const response = await axios.put(`/api/v1/overtime/${this.currentOvertime.id}`, this.currentOvertime);
                const index = this.overtimes.findIndex(overtime => overtime.id === this.currentOvertime.id);
                this.$set(this.overtimes, index, response.data.overtime);
                this.showEditModal = false;
                this.currentOvertime = {};
            } catch (error) {
                console.error('Error updating overtime:', error);
            }
        },
        resetNewOvertime() {
            this.newOvertime = {
                employee: '',
                department: '',
                date: '',
                hours: ''
            };
        }
    }
};
</script>
