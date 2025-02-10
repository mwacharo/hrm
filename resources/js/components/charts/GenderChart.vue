<template>
    <div>
        <div id="genderChart"></div>
    </div>
</template>

<script>
import ApexCharts from 'apexcharts';
export default {
    name: 'EmployeePieChart',
    data() {
        return {
            chartData: {
                male: 0,
                female: 0
            }
        };
    },
    mounted() {
        this.fetchUsersAndRenderChart();
    },
    methods: {
        async fetchUsersAndRenderChart() {
            try {
                const response = await axios.get('/api/v1/users');
                const users = response.data.users;
                const maleUsers = users.filter(user => user.gender === 'Male');
                const femaleUsers = users.filter(user => user.gender === 'Female');
                this.chartData.male = maleUsers.length;
                this.chartData.female = femaleUsers.length;
                this.renderChart();
            } catch (error) {
                console.error('Error fetching users:', error);
            }
        },
        renderChart() {
            const options = {
                chart: {
                    type: 'donut',
                },
                labels: ['Male', 'Female'],
                series: [this.chartData.male, this.chartData.female],
                colors: ['#1ab7ea', '#fc0345'],
                legend: {
                    position: 'bottom',
                },
                title: {
                    text: 'Employee Count by Gender',
                    align: 'center',
                    margin: 20,
                    offsetX: 0,
                    offsetY: -10,
                    style: {
                        fontSize: '20px'
                    }
                }
            };

            const chart = new ApexCharts(document.querySelector('#genderChart'), options);
            chart.render();
        }
    }
}
</script>
