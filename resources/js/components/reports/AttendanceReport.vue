<template>
    <v-container>
        <v-form>
            <v-row>
                <v-col cols="12" md="6">
                    <v-select label="Employee" :items="employees" item-value="id" item-title="fullname"
                        v-model="selectedEmployee" outlined></v-select>
                </v-col>
                <v-col cols="12" md="4">
                    <v-select label="Attendance Status" :items="attendanceStatuses" v-model="selectedAttendanceStatus"
                        outlined></v-select>
                </v-col>
            </v-row>
            <v-row>
                <v-col cols="12" md="3">
                    <v-text-field v-model="dates.start" label="Start Date" type="date" outlined></v-text-field>
                </v-col>
                <v-col cols="12" md="3">
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
                    <template v-slot:[`item.date`]="{ item }">
                        {{ new Date(item.date).toLocaleDateString() }}
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
            attendanceStatuses: ['Present', 'In Time', 'Late'],
            selectedEmployee: null,
            selectedAttendanceStatus: null,
            dates: {
                start: null,
                end: null,
            },
            statuses: [
                { label: 'total', value: 0 },
                { label: 'present', value: 0 },
                { label: 'in time', value: 0 },
                { label: 'late', value: 0 },
            ],
            report: [],
            tableHeaders: [
                { title: 'Created At', value: 'created_at' },
                { title: 'Time in', value: 'clock_in' },
                { title: 'Time Out', value: 'clock_out' },
                { title: 'Date', value: 'attendance_date' },
                { title: 'Employee', value: 'name' },
                { title: 'Status', value: 'status' },
            ],
        };
    },
    mounted() {
        this.fetchEmployees();
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
        fetchReport() {
            const apiUrl = `${this.base_url}api/v1/attendance-report`;
            axios.post(apiUrl, {
                employee: this.selectedEmployee,
                attendanceStatus: this.selectedAttendanceStatus,
                start: this.dates.start,
                end: this.dates.end,
            })
                .then((response) => {
                    this.report = response.data.attendanceReport;
                    this.statuses = [
                        { label: 'Total', value: response.data.attendanceReportStatuses.total },
                        { label: 'In Time', value: response.data.attendanceReportStatuses.in_time },
                        { label: 'Late', value: response.data.attendanceReportStatuses.late },
                    ];
                })
                .catch((error) => {
                    console.error('Error fetching attendance report:', error);
                });
        },
        generateExcel() {
            const currentDate = new Date().toISOString().replace(/[-:]/g, '').slice(0, 14);
            const fileName = `attendance_report_${currentDate}.xlsx`;

            const uri = this.base_url + 'api/v1/attendance-report/excel';
            axios({
                url: uri,
                method: 'POST',
                responseType: 'blob',
                data: {
                    attendances: JSON.stringify(this.report),
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




