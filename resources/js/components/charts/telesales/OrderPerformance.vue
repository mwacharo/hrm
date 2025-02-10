<template>
    <div>
        <div id="orderPerformanceChart">
            <v-progress-circular v-if="loading" :size="70" :width="7" color="purple"
                indeterminate></v-progress-circular>
        </div>
    </div>
</template>

<script>
import ApexCharts from 'apexcharts';
export default {
    name: 'OrderPerformanceChart',
    data() {
        return {
            chartData: {
                deliveryRate: 75,
                buyoutRate: 15,
                pendingRate: 10
            },
            loading: true
        };
    },
    mounted() {
        this.fetchOrderData();
    },
    methods: {
        fetchOrderData() {
            setTimeout(() => {
                this.loading = false;
                this.renderChart();
            }, 1000);
        },
        renderChart() {
            const options = {
                chart: {
                    type: 'donut',
                    height: 380,
                },
                series: [this.chartData.deliveryRate, this.chartData.buyoutRate, this.chartData.pendingRate],
                labels: ['Delivery Rate', 'Buyout Rate', 'Pending Rate'],
                colors: ['#4CAF50', '#FFC107', '#FF5722'],
                legend: {
                    position: 'bottom',
                },
                dataLabels: {
                    enabled: true
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 300
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }],
                title: {
                    text: 'Order Performance',
                    align: 'center',
                    margin: 20,
                    offsetX: 0,
                    offsetY: 0,
                    floating: false,
                    style: {
                        fontSize: '18px',
                        fontWeight: 'bold'
                    }
                }
            };

            const chart = new ApexCharts(document.querySelector('#orderPerformanceChart'), options);
            chart.render();
        }
    }
}
</script>

<style scoped>
/* Additional styles if needed */
</style>
