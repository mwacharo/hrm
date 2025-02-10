<template>
    <div>
        <div id="agentPerformance"></div>
    </div>
</template>

<script>
import ApexCharts from 'apexcharts';

export default {
    data() {
        return {
            apiData: null,
        };
    },
    mounted() {
        this.fetchData();
    },
    methods: {
        fetchData() {
            axios.get('/api/v1/agent-performance')
                .then(response => {
                    this.apiData = response.data;
                    this.renderChart();
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                });
        },
        renderChart() {
            if (!this.apiData) return; // Ensure data is available

            const options = {
                chart: {
                    height: 350,
                    type: 'line',
                    stacked: false
                },
                series: [
                    {
                        name: 'Total Orders',
                        type: 'column',
                        data: this.apiData.data.map(item => item.total) // Use 'total' from each item in apiData.data
                    },
                    {
                        name: 'Buyout Rate',
                        type: 'line',
                        data: this.apiData.data.map(item => parseFloat(item.sales_count_by_status.Buyout)) // Parse Buyout rate as float
                    },
                    {
                        name: 'Delivery Rate',
                        type: 'line',
                        data: this.apiData.data.map(item => parseFloat(item.sales_count_by_status.Delivered)) // Parse Delivery rate as float
                    }
                ],
                stroke: {
                    width: [0, 3, 3],
                    curve: 'smooth'
                },
                plotOptions: {
                    bar: {
                        columnWidth: '50%',
                        opacity: 0.8
                    }
                },
                fill: {
                    opacity: [0.9, 0.3, 0.3],
                    gradient: {
                        inverseColors: false,
                        shade: 'light',
                        type: "vertical",
                        opacityFrom: 0.9,
                        opacityTo: 0.9,
                        stops: [0, 100, 100]
                    }
                },
                labels: this.apiData.delivery_chart.labels, // Use delivery chart labels
                markers: {
                    size: 0
                },
                xaxis: {
                    type: 'category'
                },
                yaxis: {
                    title: {
                        text: 'Orders'
                    },
                    min: 0
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    y: {
                        formatter: function (y) {
                            if (typeof y !== "undefined") {
                                return y.toFixed(0) + " orders";
                            }
                            return y;
                        }
                    }
                }
            };

            var chart = new ApexCharts(document.querySelector("#agentPerformance"), options);
            chart.render();
        }
    }
};
</script>
