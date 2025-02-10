<template>
    <v-container fluid>
        <div id="employeeAttendances"></div>
    </v-container>
</template>

<script>
import ApexCharts from 'apexcharts';
import axios from 'axios';

export default {
    name: 'AttendancePieChartComponent',
    props: {
        user: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            chartData: {
                inTime: 0,
                late: 0,
                onLeave: 0
            }
        };
    },
    mounted() {
        this.fetchAnalyticsAndRenderChart();
        console.log('The User ID  is:' + this.user.id);
    },
    methods: {
        async fetchAnalyticsAndRenderChart() {
            try {
                const response = await axios.get(`/api/v1/analytics/${this.user.id}`);
                const data = response.data;

                this.chartData.inTime = data.inTimeAttendances;
                this.chartData.late = data.lateAttendances;
                this.chartData.onLeave = data.leaveDays;

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
                labels: ['In Time', 'Late', 'On Leave'],
                series: [this.chartData.inTime, this.chartData.late, this.chartData.onLeave],
                colors: ['#1E88E5', '#D32F2F', '#FFB300'], // Updated color scheme
                legend: {
                    position: 'bottom',
                },
                title: {
                    text: "Attendance (Current Month)",
                    align: 'center',
                    margin: 20,
                    offsetX: 0,
                    offsetY: -5,
                    style: {
                        fontSize: '15px'
                    }
                }
            };

            const chart = new ApexCharts(document.querySelector('#employeeAttendances'), options);
            chart.render();
        }
    }
}
</script>

<style scoped>
#employeeAttendanceStats {
    margin-top: 20px;
}
</style>
