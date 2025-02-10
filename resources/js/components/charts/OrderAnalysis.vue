<template>
    <div>
        <div id="orderPerformance">
            <v-progress-circular v-if="loading" :size="70" :width="7" color="purple"
                indeterminate></v-progress-circular>
        </div>
    </div>
</template>

<script>
import ApexCharts from 'apexcharts';

export default {
    name: 'OrderAnalysis',
    data() {
        return {
            orderData: {
                scheduling: 25,
                buyout: 15,
                delivery: 30,
                cancelled: 10,
                pending: 20
            },
            loading: true
        };
    },
    mounted() {
        setTimeout(() => {
            this.loading = false;
            this.renderChart();
        }, 2000);
    },
    methods: {
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
                labels: ['Scheduling', 'Buyout', 'Delivery', 'Cancelled', 'Pending'],
                series: [this.orderData.scheduling, this.orderData.buyout, this.orderData.delivery, this.orderData.cancelled, this.orderData.pending],
                colors: ['#4CAF50', '#FFA500', '#2196F3', '#fc0345', '#9e9e9e'],
                legend: {
                    position: 'bottom',
                },
                title: {
                    text: "Order Performance",
                    align: 'center',
                    margin: 20,
                    offsetX: 0,
                    offsetY: -10,
                    style: {
                        fontSize: '22px'
                    }
                }
            };

            const chart = new ApexCharts(document.querySelector('#orderPerformance'), options);
            chart.render();
        }
    }
};
</script>
