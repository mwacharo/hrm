<template>
    <div>
        <div id="todayStats">
        </div>
    </div>
</template>

<script>
import ApexCharts from 'apexcharts';
export default {
    name: 'AttendancePieChart',
    data() {
        return {
            loading:true,
            base_url: '/',
            usersCount: 0,
            chartData: {
                inTime: 0,
                late: 0,
                unregistered: 0
            },
       };
    },
    mounted() {
        this.fetchUsers();
        this.fetchAttendanceAndRenderChart();
    },
    methods: {
        fetchUsers() {
            const apiUrl = this.base_url + 'api/v1/users';
            axios.get(apiUrl)
                .then(response => {
                    this.usersCount = response.data.users.length;
                })
                .catch(error => {
                    console.error('Error fetching users:', error);
                });
        },
        async fetchAttendanceAndRenderChart() {
            try {
                const response = await axios.get('/api/v1/attendances');
                const attendance = response.data.attendances;

                const currentDate = new Date().toISOString().split('T')[0];
                const filteredAttendance = attendance.filter(record => record.attendance_date === currentDate);

                const inTimeCount = filteredAttendance.filter(record => record.status === 'In Time').length;
                const lateCount = filteredAttendance.filter(record => record.status === 'Late').length;
                this.chartData.inTime = inTimeCount;
                this.chartData.late = lateCount;
                this.chartData.unregistered = this.usersCount - (inTimeCount + lateCount);
                this.renderChart();
            } catch (error) {
                console.error('Error fetching attendance:', error);
            }
        },
        renderChart() {
            const options = {
                chart: {
                    type: 'donut',
                },
                plotOptions: {
                    pie: {
                        startAngle: -90,
                        endAngle: 270
                    }
                },
                labels: ['In Time', 'Late', 'Unregistered'],
                series: [this.chartData.inTime, this.chartData.late, this.chartData.unregistered],
                colors: ['#4CAF50', '#fc0345', '#FFA500'],
                legend: {
                    position: 'bottom',
                },

                title: {
                    text: "Today's Attendance",
                    align: 'center',
                    margin: 20,
                    offsetX: 0,
                    offsetY: -10,
                    style: {
                        fontSize: '22px'
                    }
                }
            };

            const chart = new ApexCharts(document.querySelector('#todayStats'), options);
            chart.render();
        }
    }
}
</script>
