<template>
    <v-container>
        <v-form>
            <v-row>
                <v-col cols="12" md="5">
                    <v-autocomplete label="Employee" :items="employees" item-value="id" item-title="fullname"
                        v-model="selectedEmployee" outlined multiple clearable placeholde="Select Employee">
                    </v-autocomplete>
                </v-col>
                <v-col cols="12" md="4">
                    <v-select label="Leave Type" :items="leaveTypes" item-value="id" item-title="name"
                        v-model="selectedLeaveType" outlined multiple placeholde="Select Leave type">
                    </v-select>
                </v-col>
                <v-col cols="12" md="3" >
                    <v-autocomplete label="Leave Status" :items="leaveStatuses" v-model="selectedLeaveStatus" outlined multiple placeholde="Select Status">
                    </v-autocomplete>
                </v-col>
            </v-row>
            <v-row>
                <v-col cols="12" md="4">
                    <v-text-field v-model="dates.start" label="Start Date" type="date" outlined></v-text-field>
                </v-col>
                <v-col cols="12" md="4">
                    <v-text-field v-model="dates.end" label="End Date" type="date" outlined></v-text-field>
                </v-col>

                <v-col cols="12" md="2">
                    <v-btn icon color="primary" @click="fetchReport">
                        <v-icon>mdi-magnify</v-icon>
                    </v-btn>
                </v-col>
                <v-col cols="12" md="2" v-if="report.length > 0">
                    <v-btn icon color="success" @click="generateExcel">
                        <v-icon>mdi-file-excel</v-icon>
                    </v-btn>
                </v-col>
            </v-row>

            <v-divider></v-divider>
        </v-form>
        <v-row class="justify-center">
            <v-span v-for="status in statuses" :key="status.label" color="black" text-color="white">
                {{ capitalizeFirstLetter(status.label) }}: {{ status.value }}
            </v-span>
        </v-row>

        <v-row>
            <v-col cols="12">
                <v-data-table :headers="tableHeaders" :items="report" class="elevation-1">
                    <template v-slot:[`item.leave_date`]="{ item }">
                        {{ new Date(item.leave_date).toLocaleDateString() }}
                    </template>
                </v-data-table>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
export default {
    data() {
        return {
            base_url: '/',
            employees: [],
            leaveTypes: [],
            leaveStatuses: ['Pending', 'Approved', 'Rejected', 'Cancelled'],
            selectedEmployee: null,
            selectedLeaveStatus: null,
            selectedLeaveType: null,
            dates: {
                start: null,
                end: null,
            },
            statuses: [
                { label: 'total', value: 0 },
                { label: 'approved', value: 0 },
                { label: 'pending', value: 0 },
                { label: 'rejected', value: 0 },
                { label: 'cancelled', value: 0 },
            ],
            report: [],
            tableHeaders: [
                { title: 'Application Date', value: 'created_at' },
                { title: 'Employee', value: 'fullName' },
                { title: 'Leave Type', value: 'leaveType' },
                { title: 'From', value: 'from' },
                { title: 'To', value: 'to' },
                { title: 'Duration(days)', value: 'days' },
                { title: 'Status', value: 'status' },
                { title: 'Notes', value: 'notes' },
            ],
        };
    },
    mounted() {
        this.fetchEmployees();
        this.fetchLeaveTypes()
    },
    methods: {
        fetchEmployees() {
            const apiUrl = `${this.base_url}api/v1/users`;
            axios
                .get(apiUrl)
                .then((response) => {
                    this.employees = response.data.users.map((user) => ({
                        ...user,
                        fullname: `${user.firstname} ${user.lastname}`,
                    }));
                })
                .catch((error) => {
                    console.error('Error fetching users:', error);
                });
        },
        fetchLeaveTypes() {
            const apiUrl = this.base_url + 'api/v1/leave-types';

            axios.get(apiUrl)
                .then(response => {
                    this.leaveTypes = response.data.leaveTypes;
                })
                .catch(error => {
                    console.error('Error fetching Leave Types:', error);
                });
        },
        fetchReport() {
            const apiUrl = `${this.base_url}api/v1/leave-report`;
            axios.post(apiUrl, {
                employee: this.selectedEmployee,
                leaveType: this.selectedLeaveType,
                leaveStatus: this.selectedLeaveStatus,
                start: this.dates.start,
                end: this.dates.end,
            })
                .then((response) => {
                    this.report = response.data.leaveReport;
                    this.statuses = [
                        { label: 'total', value: response.data.leaveReportStatuses.total },
                        { label: 'approved', value: response.data.leaveReportStatuses.approved },
                        { label: 'pending', value: response.data.leaveReportStatuses.pending },
                        { label: 'rejected', value: response.data.leaveReportStatuses.rejected },
                        { label: 'cancelled', value: response.data.leaveReportStatuses.cancelled },
                    ];
                })
                .catch((error) => {
                    console.error('Error fetching leave report:', error);
                });
        },

        generateExcel() {
            const currentDate = new Date().toISOString().replace(/[-:]/g, '').slice(0, 14);
            const fileName = `leave_report_${currentDate}.xlsx`;

            const uri = `${this.base_url}api/v1/leave-report/excel`;
            axios({
                url: uri,
                method: 'POST',
                responseType: 'blob',
                data: {
                    leaves: JSON.stringify(this.report),
                }
            }).then((response) => {
                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', fileName);
                document.body.appendChild(link);
                link.click();
            });
        },
        capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        },

    },
};
</script>
