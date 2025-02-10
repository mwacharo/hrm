<template>
    <div>
        <div id="attendanceChart"></div>
    </div>
</template>
<script>
import ApexCharts from 'apexcharts';
export default {
    name: 'AttendanceGraph',
    mounted() {
        this.fetchAttendanceDataAndRenderChart();
    },
    methods: {
        async fetchAttendanceDataAndRenderChart() {
            try {
                const response = await axios.get('/api/v1/attendance-analytics');
                const attendanceData = response.data;
                const daysOfWeek = Object.keys(attendanceData);
                const inTimeData = daysOfWeek.map(day => attendanceData[day][0].in_time);
                const lateData = daysOfWeek.map(day => attendanceData[day][0].late);
                const onLeaveData = daysOfWeek.map(day => attendanceData[day][0].on_leave);
                this.renderChart(daysOfWeek, inTimeData, lateData, onLeaveData);
            } catch (error) {
                console.error('Error fetching attendance data:', error);
            }
        },
        renderChart(daysOfWeek, inTimeData, lateData, onLeaveData) {
            const options = {
                chart: {
                    type: 'bar',
                    stacked: true,
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                    },
                },
                series: [
                    {
                        name: 'In Time',
                        data: inTimeData,
                        color: '#28a745'
                    },
                    {
                        name: 'Late',
                        data: lateData,
                        color: '#fc0345'
                    },
                    {
                        name: 'On Leave',
                        data: onLeaveData,
                        color: '#007bff'
                    },
                ],
                xaxis: {
                    categories: daysOfWeek
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'left',
                },
                fill: {
                    opacity: 1
                },
                title: {
                    text: 'Attendance Status (Previous Week)',
                    align: 'center',
                    margin: 20,
                    offsetY: 20,
                    style: {
                        fontSize: '12px'
                    }
                }
            };

            const chart = new ApexCharts(document.querySelector('#attendanceChart'), options);
            chart.render();
        }
    }
}
</script>
