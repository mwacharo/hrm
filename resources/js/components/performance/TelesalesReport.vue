<template>
    <v-layout>
        <v-navigation-drawer v-model="drawer" width="500" location="right" temporary>
            <v-row class="pa-4">
                <v-col>
                    <h4>Filters</h4>
                </v-col>
                <v-col class="text-right">
                    <v-btn icon @click="drawer = false">
                        <v-icon color="grey darken-3">mdi-close</v-icon>
                    </v-btn>
                </v-col>
            </v-row>

            <!-- Report Selector -->
            <v-row class="pa-4">
                <v-col cols="12">
                    <v-select v-model="selectedReport" :items="reports" label="Report" dense outlined></v-select>
                </v-col>
            </v-row>

            <!-- Agent Selector -->
            <v-row class="pa-4">
                <v-col cols="12">
                    <v-select v-model="selectedAgent" :items="agents" label="Agent" dense outlined></v-select>
                </v-col>
            </v-row>

            <!-- Date Pickers -->
            <v-row class="pa-4">
                <!-- Start Date -->
                <v-col cols="12">
                    <v-menu ref="startDateMenu" v-model="startDateMenu" :close-on-content-click="false"
                        transition="scale-transition" offset-y min-width="auto">
                        <template v-slot:activator="{ on, attrs }">
                            <v-text-field v-model="startDate" label="Start Date" prepend-icon="mdi-calendar" readonly
                                v-bind="attrs" v-on="on" dense outlined></v-text-field>
                        </template>
                        <v-date-picker v-model="startDate" no-title scrollable>
                            <v-spacer></v-spacer>
                            <v-btn text color="primary" @click="startDateMenu = false">
                                Cancel
                            </v-btn>
                            <v-btn text color="primary" @click="startDateMenu = false">
                                OK
                            </v-btn>
                        </v-date-picker>
                    </v-menu>
                </v-col>

                <!-- End Date -->
                <v-col cols="12">
                    <v-menu ref="endDateMenu" v-model="endDateMenu" :close-on-content-click="false"
                        transition="scale-transition" offset-y min-width="auto">
                        <template v-slot:activator="{ on, attrs }">
                            <v-text-field v-model="endDate" label="End Date" prepend-icon="mdi-calendar" readonly
                                v-bind="attrs" v-on="on" dense outlined></v-text-field>
                        </template>
                        <v-date-picker v-model="endDate" no-title scrollable>
                            <v-spacer></v-spacer>
                            <v-btn text color="primary" @click="endDateMenu = false">
                                Cancel
                            </v-btn>
                            <v-btn text color="primary" @click="endDateMenu = false">
                                OK
                            </v-btn>
                        </v-date-picker>
                    </v-menu>
                </v-col>
            </v-row>

            <!-- Filter Button -->
            <v-row class="pa-4">
                <v-col cols="12">
                    <v-btn @click="filter" color="primary" dark large block>Apply Filters</v-btn>
                </v-col>
            </v-row>

        </v-navigation-drawer>
        <v-main>
            <v-row class="mb-4">
                <v-col v-for="card in cards" cols="12" md="3">
                    <v-card>
                        <v-card-title>{{ card.title }}</v-card-title>
                        <v-card-text>{{ card.value }}</v-card-text>
                    </v-card>
                </v-col>
            </v-row>
            <v-row justify="end" class="text-right">

                <v-col cols="auto">
                    <v-icon size="26" color="success mx-1" @click="generateExcel" small>mdi-file-excel
                    </v-icon>
                    <v-icon size="26" color="primary mx-1" @click.stop="drawer = !drawer" small>mdi-filter</v-icon>

                </v-col>
            </v-row>

            <v-data-table :headers="headers" :items="items" class="elevation-1">
                <template v-slot:top>
                    <v-text-field v-model="search" label="Search" class="mx-4" dense></v-text-field>
                </template>
                <template v-slot:item="{ item }">
                    <tr>
                        <td>{{ item.agentName }}</td>
                        <td>{{ item.totalOrders }}</td>
                        <td>{{ item.scheduled }}</td>
                        <td>{{ item.awaitingDispatch }}</td>
                        <td>{{ item.cancelled }}</td>
                        <td>{{ item.pending }}</td>
                        <td>{{ item.inTransit }}</td>
                        <td>{{ item.delivered }}</td>
                        <td>{{ item.returned }}</td>
                        <td>{{ item.undispatched }}</td>
                        <td>{{ item.reschedule }}</td>
                        <td>{{ item.outOfStock }}</td>
                    </tr>
                </template>
            </v-data-table>
            <v-row>
                <v-col cols="12" md="8">
                    <v-card>
                        <AgentPerformance />
                    </v-card>
                </v-col>
                <v-col cols="12" md="4">
                    <v-card>
                        <OrderPerformance />
                    </v-card>
                </v-col>
            </v-row>

        </v-main>
    </v-layout>
</template>

<script>
import AgentPerformance from '@/components/charts/telesales/AgentPerformance.vue';
import OrderPerformance from '@/components/charts/telesales/OrderPerformance.vue';

export default {
    components: {
        AgentPerformance,
        OrderPerformance,
    },
    data() {
        return {
            cards: [
                { title: "Avg Scheduling Rate", value: '55%' },
                { title: "Avg Buyout Rate", value: '40%' },
                { title: "Avg Pending Rate", value: '8%' },
                { title: "Avg Return Rate", value: '7%' },
            ],
            drawer: false,
            selectedReport: null,
            reports: ["Agent Performance Report"],
            selectedAgent: null,
            agents: [],
            startDate: null,
            endDate: null,
            startDateMenu: false,
            endDateMenu: false,
            search: "",
            headers: [
                { title: "Agent Name", value: "agentName" },
                { title: "Total Orders", value: "totalOrders" },
                { title: "Scheduled", value: "scheduled" },
                { title: "Cancelled", value: "cancelled" },
                { title: "Pending", value: "pending" },
                { title: "Delivered", value: "delivered" },
                { title: "Buyout", value: "delivered" },
                { title: "Returned", value: "returned" },
            ],
            items: [],
        };
    },
    mounted() {
        this.fetchAgents();
        this.fetchOrderAanalysis();
    },

    methods: {
        fetchAgents() {
            axios.get('/api/v1/telesale-agents')
                .then(response => {
                    this.agents = response.data;
                })
                .catch(error => {
                    console.error("There was an error fetching the agents: ", error);
                    this.agents = [];
                });
        },
        fetchOrderAanalysis() {
            axios.get('/api/v1/agent-performance')
                .then(response => {
                    this.items = response.data;
                })
                .catch(error => {
                    console.error("There was an error fetching the telesales performance: ", error);
                    this.agents = [];
                });
        },

    },

};
</script>
