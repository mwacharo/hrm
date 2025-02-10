<template>
    <div>
        <div id="departmentCountChart"></div>
    </div>
</template>
<script>
import ApexCharts from 'apexcharts';
import axios from 'axios';

export default {
    name: 'EmployeePieChart',
    mounted() {
        this.fetchDepartmentsAndRenderChart();
    },
    methods: {
        async fetchDepartmentsAndRenderChart() {
            try {
                const response = await axios.get('/api/v1/departments');
                const departments = response.data.departments;
                const departmentNames = departments.map(department => department.name);
                const departmentCountData = departments.map(department => department.users.length);
                this.renderChart(departmentNames, departmentCountData);
            } catch (error) {
                console.error('Error fetching departments:', error);
            }
        },
        renderChart(departmentNames, departmentCountData) {
            const options = {
                chart: {
                    type: 'donut',
                },
                labels: departmentNames,
                series: departmentCountData,
                legend: {
                    position: 'bottom',
                },
                title: {
                    text: 'Employee Count by Department',
                    align: 'center',
                    margin: 30,
                    offsetY: -15,
                    style: {
                        fontSize: '20px'
                    }
                },
                plotOptions: {
                    pie: {
                        startAngle: -90,
                        endAngle: 270,
                        donut: {
                            size: '70%',
                            background: 'transparent',
                            labels: {
                                show: true,
                                name: {
                                    show: true,
                                    fontSize: '22px',
                                },
                                value: {
                                    show: true,
                                    fontSize: '16px',
                                    formatter: function (val) {
                                        return val;
                                    }
                                },
                                total: {
                                    show: true,
                                    showAlways: true,
                                    label: 'Total',
                                    formatter: function (w) {
                                        return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                    }
                                }
                            }
                        }
                    }
                },
                fill: {
                    type: 'gradient',
                },
                dataLabels: {
                    enabled: false,
                },
                tooltip: {
                    enabled: true,
                },
            };

            const chart = new ApexCharts(document.querySelector('#departmentCountChart'), options);
            chart.render();
        }
    }
}
</script>
