<template>
    <div id="attendanceAnalyticsChart"></div>
</template>

<script>
import ApexCharts from 'apexcharts';
import axios from 'axios';

export default {
    name: 'AttendanceAnalyticsChart',
    data() {
        return {
            attendances: [],
            chart: null,
        };
    },
    mounted() {
        this.fetchAttendances();
    },
    methods: {
        fetchAttendances() {
            const uri = '/api/v1/attendances';
            axios.get(uri)
                .then(response => {
                    this.attendances = response.data.attendances.map(attendance => ({
                        date: attendance.attendance_date,
                        name: `${attendance.user.firstname} ${attendance.user.lastname}`,
                        clockIn: attendance.clock_in_time,
                        clockOut: attendance.clock_out_time,
                    }));
                    this.renderChart();
                })
                .catch(error => {
                    console.error('Error fetching attendances:', error);
                });
        },
        renderChart() {
            const options = {
                chart: {
                    type: 'line',
                    height: 350,
                    toolbar: {
                        show: false,
                    },
                },
                colors: ['#00BAEC', '#FF5733'],
                stroke: {
                    width: 2,
                    curve: 'smooth',
                },
                grid: {
                    borderColor: '#f1f1f1',
                },
                markers: {
                    size: 4,
                    colors: ['#fff'],
                    strokeColors: ['#00BAEC', '#FF5733'],
                    strokeWidth: 2,
                    hover: {
                        size: 6,
                    },
                },
                series: [
                    {
                        name: 'Clock-In Time',
                        data: [],
                    },
                    {
                        name: 'Clock-Out Time',
                        data: [],
                    },
                ],
                tooltip: {
                    x: {
                        format: 'yyyy-MM-dd',
                    },
                    y: {
                        formatter: function (value) {
                            return Math.floor(value / 60) + ':' + ('0' + (value % 60)).slice(-2) + ' hrs';
                        },
                    },
                },
                xaxis: {
                    type: 'datetime',
                    labels: {
                        rotate: -45,
                    },
                },
                yaxis: {
                    min: 0,
                    max: 1440, // 24 hours * 60 minutes
                    labels: {
                        formatter: function (value) {
                            return Math.floor(value / 60) + ':' + ('0' + (value % 60)).slice(-2) + ' hrs';
                        },
                    },
                },
            };

            this.chart = new ApexCharts(document.querySelector('#attendanceAnalyticsChart'), options);
            this.chart.render();
            this.updateChart();
        },
        updateChart() {
            const clockInData = this.attendances.map(item => ({
                x: new Date(item.date).getTime(),
                y: this.timeToMinutes(item.clockIn),
                name: item.name,
            }));

            const clockOutData = this.attendances.map(item => ({
                x: new Date(item.date).getTime(),
                y: item.clockOut ? this.timeToMinutes(item.clockOut) : null,
                name: item.name,
            }));

            this.chart.updateSeries([
                {
                    name: 'Clock-In Time',
                    data: clockInData,
                },
                {
                    name: 'Clock-Out Time',
                    data: clockOutData,
                },
            ]);
        },
        timeToMinutes(time) {
            if (!time) return null;
            const [hours, minutes, seconds] = time.split(':').map(Number);
            return hours * 60 + minutes + seconds / 60;
        },
    },
};
</script>

<style scoped>
#attendanceAnalyticsChart {
    max-width: 100%;
    margin: 0 auto;
}
</style>
