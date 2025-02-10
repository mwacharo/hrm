<template>
    <v-container fluid>
        <!-- Header -->
        <v-row>
            <v-col>
                <v-btn depressed>This Month</v-btn>
                <v-btn outlined>Show Company Average</v-btn>
            </v-col>
            <v-col>
                <v-menu>
                    <template v-slot:activator="{ on, attrs }">
                        <v-btn v-bind="attrs" v-on="on" icon>
                            <v-icon>mdi-calendar</v-icon>
                        </v-btn>
                    </template>
                    <v-date-picker></v-date-picker>
                </v-menu>
            </v-col>
        </v-row>
        <!-- Graph -->
        <v-row>
            <v-col>
                <v-card>
                    <v-card-title>{{ currentMonth }}</v-card-title>
                    <v-card-text>
                        <AttendanceAnalyticsGraph />
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
        <!-- Tables -->
        <v-row>
            <v-col cols="4">
                <v-card>
                    <v-card-title>Early Birds</v-card-title>
                    <v-card-subtitle>Ranking of early check-ins in this period</v-card-subtitle>
                    <v-data-table :headers="earlyBirdHeaders" :items="earlyBirds"></v-data-table>
                </v-card>
            </v-col>
            <v-col cols="4">
                <v-card>
                    <v-card-title>Lates</v-card-title>
                    <v-card-subtitle>People were recorded late on this period</v-card-subtitle>
                    <v-data-table :headers="latesHeaders" :items="lates"></v-data-table>
                </v-card>
            </v-col>
            <v-col cols="4">
                <v-card>
                    <v-card-title>Perfect Attendance</v-card-title>
                    <v-card-subtitle>Longest streak in Perfect Attendance on this Period</v-card-subtitle>
                    <v-data-table :headers="perfectAttendanceHeaders" :items="perfectAttendance"></v-data-table>
                </v-card>
            </v-col>

        </v-row>
    </v-container>
</template>


<script>
import AttendanceAnalyticsGraph from '@/components/charts/AttendanceAnalyticsGraph.vue';

export default {
    components: {
        AttendanceAnalyticsGraph,
    },
    data() {
        return {
            currentMonth: '',
            earlyBirdHeaders: [
                { title: 'Name', value: 'name' },
                { title: 'Days', value: 'days' },
                { title: 'Avg. Time', value: 'avgTime' }
            ],
            earlyBirds: [],
            latesHeaders: [
                { title: 'Name', value: 'name' },
                { title: 'Days', value: 'days' },
                { title: 'Avg. Time', value: 'avgTime' }
            ],
            lates: [],
            perfectAttendanceHeaders: [
                { title: 'Name', value: 'name' },
                { title: 'Streak', value: 'streak' }
            ],
            perfectAttendance: [],
            absencesHeaders: [
                { title: 'Name', value: 'name' },
                { title: 'Days', value: 'days' }
            ],
            absences: [],
        };
    },
    mounted() {
        this.updateCurrentMonth();
    },
    methods: {
        updateCurrentMonth() {
            const date = new Date();
            const options = { month: 'long', year: 'numeric' };
            this.currentMonth = date.toLocaleDateString('en-US', options);
        }
    }
};
</script>
