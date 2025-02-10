<template>
    <v-container fluid>
        <v-row>
            <v-col cols="12">
                <v-card class="mx-auto" max-width="100%">
                    <v-card-title class="headline text-center">HRM System Settings</v-card-title>
                    <v-divider></v-divider>
                    <v-card-text>
                        <v-form ref="form" v-model="isFormValid" lazy-validation>
                            <!-- Clock In Time -->
                            <v-row>
                                <v-col cols="12" md="6">
                                    <v-dialog ref="clockInDialog" v-model="clockInDialog" persistent max-width="290px">
                                        <template v-slot:activator="{ on, attrs }">
                                            <v-text-field v-model="settings.clockInTime"
                                                label="Clock In Time (Weekdays)" prepend-icon="mdi-clock" readonly
                                                v-bind="attrs" v-on="on" v-on:click="clockInDialog = true"
                                                aria-label="Clock In Time (Weekdays)"
                                                :rules="[v => !!v || 'Clock In Time is required']"
                                                hint="Set the clock-in time for weekdays"
                                                persistent-hint></v-text-field>
                                        </template>
                                        <v-card>
                                            <v-card-title class="headline">Clock In Time</v-card-title>
                                            <v-card-text>
                                                <v-text-field v-model="tempClockInTime" label="Clock In Time"
                                                    prepend-icon="mdi-clock" aria-label="Clock In Time"
                                                    @input="updateClockInTime"></v-text-field>
                                                <v-time-picker v-model="tempClockInTime" full-width format="24hr"
                                                    @change="updateClockInTimeFromPicker"></v-time-picker>
                                            </v-card-text>
                                            <v-card-actions>
                                                <v-spacer></v-spacer>
                                                <v-btn text color="primary"
                                                    @click="clockInDialog = false">Cancel</v-btn>
                                                <v-btn text color="primary" @click="confirmClockInTime">OK</v-btn>
                                            </v-card-actions>
                                        </v-card>
                                    </v-dialog>
                                </v-col>
                                <!-- Clock Out Time -->
                                <v-col cols="12" md="6">
                                    <v-dialog ref="clockOutDialog" v-model="clockOutDialog" persistent
                                        max-width="290px">
                                        <template v-slot:activator="{ on, attrs }">
                                            <v-text-field v-model="settings.clockOutTime"
                                                label="Clock Out Time (Weekdays)" prepend-icon="mdi-clock" readonly
                                                v-bind="attrs" v-on="on" v-on:click="clockOutDialog = true"
                                                aria-label="Clock Out Time (Weekdays)"
                                                :rules="[v => !!v || 'Clock Out Time is required']"
                                                hint="Set the clock-out time for weekdays"
                                                persistent-hint></v-text-field>
                                        </template>
                                        <v-card>
                                            <v-card-title class="headline">Clock Out Time</v-card-title>
                                            <v-card-text>
                                                <v-text-field v-model="tempClockOutTime" label="Clock Out Time"
                                                    prepend-icon="mdi-clock" aria-label="Clock Out Time"
                                                    @input="updateClockOutTime"></v-text-field>
                                                <v-time-picker v-model="tempClockOutTime" full-width format="24hr"
                                                    @change="updateClockOutTimeFromPicker"></v-time-picker>
                                            </v-card-text>
                                            <v-card-actions>
                                                <v-spacer></v-spacer>
                                                <v-btn text color="primary"
                                                    @click="clockOutDialog = false">Cancel</v-btn>
                                                <v-btn text color="primary" @click="confirmClockOutTime">OK</v-btn>
                                            </v-card-actions>
                                        </v-card>
                                    </v-dialog>
                                </v-col>
                            </v-row>
                            <!-- Thresholds for Weekdays and Weekends -->
                            <v-row>
                                <v-col cols="12" md="6">
                                    <v-dialog ref="weekendClockInDialog" v-model="weekendClockInDialog" persistent
                                        max-width="290px">
                                        <template v-slot:activator="{ on, attrs }">
                                            <v-text-field v-model="settings.weekendClockInTime"
                                                label="Clock In Time (Weekends)" prepend-icon="mdi-clock" readonly
                                                v-bind="attrs" v-on="on" v-on:click="weekendClockInDialog = true"
                                                aria-label="Clock In Time (Weekends)"
                                                :rules="[v => !!v || 'Clock In Time (Weekends) is required']"
                                                hint="Set the clock-in time for weekends"
                                                persistent-hint></v-text-field>
                                        </template>
                                        <v-card>
                                            <v-card-title class="headline">Clock In Time (Weekends)</v-card-title>
                                            <v-card-text>
                                                <v-text-field v-model="tempWeekendClockInTime" label="Clock In Time"
                                                    prepend-icon="mdi-clock" aria-label="Clock In Time"
                                                    @input="updateWeekendClockInTime"></v-text-field>
                                                <v-time-picker v-model="tempWeekendClockInTime" full-width format="24hr"
                                                    @change="updateWeekendClockInTimeFromPicker"></v-time-picker>
                                            </v-card-text>
                                            <v-card-actions>
                                                <v-spacer></v-spacer>
                                                <v-btn text color="primary"
                                                    @click="weekendClockInDialog = false">Cancel</v-btn>
                                                <v-btn text color="primary"
                                                    @click="confirmWeekendClockInTime">OK</v-btn>
                                            </v-card-actions>
                                        </v-card>
                                    </v-dialog>
                                </v-col>
                                <v-col cols="12" md="6">
                                    <v-dialog ref="weekendClockOutDialog" v-model="weekendClockOutDialog" persistent
                                        max-width="290px">
                                        <template v-slot:activator="{ on, attrs }">
                                            <v-text-field v-model="settings.weekendClockOutTime"
                                                label="Clock Out Time (Weekends)" prepend-icon="mdi-clock" readonly
                                                v-bind="attrs" v-on="on" v-on:click="weekendClockOutDialog = true"
                                                aria-label="Clock Out Time (Weekends)"
                                                :rules="[v => !!v || 'Clock Out Time (Weekends) is required']"
                                                hint="Set the clock-out time for weekends"
                                                persistent-hint></v-text-field>
                                        </template>
                                        <v-card>
                                            <v-card-title class="headline">Clock Out Time (Weekends)</v-card-title>
                                            <v-card-text>
                                                <v-text-field v-model="tempWeekendClockOutTime" label="Clock Out Time"
                                                    prepend-icon="mdi-clock" aria-label="Clock Out Time"
                                                    @input="updateWeekendClockOutTime"></v-text-field>
                                                <v-time-picker v-model="tempWeekendClockOutTime" full-width
                                                    format="24hr"
                                                    @change="updateWeekendClockOutTimeFromPicker"></v-time-picker>
                                            </v-card-text>
                                            <v-card-actions>
                                                <v-spacer></v-spacer>
                                                <v-btn text color="primary"
                                                    @click="weekendClockOutDialog = false">Cancel</v-btn>
                                                <v-btn text color="primary"
                                                    @click="confirmWeekendClockOutTime">OK</v-btn>
                                            </v-card-actions>
                                        </v-card>
                                    </v-dialog>
                                </v-col>
                            </v-row>
                            <!-- Company Email -->
                            <v-row>
                                <v-col cols="12">
                                    <v-text-field v-model="settings.companyEmail" label="Notification Email"
                                        prepend-icon="mdi-email" type="email" aria-label="Notification Email"
                                        :rules="[v => !!v || 'Email is required', v => /.+@.+/.test(v) || 'E-mail must be valid']"
                                        hint="Enter the email to receive notifications" persistent-hint></v-text-field>
                                </v-col>
                            </v-row>
                            <!-- Company Geo-location Coordinates -->
                            <v-row>
                                <v-col cols="12" md="6">
                                    <v-dialog ref="locationDialog" v-model="locationDialog" persistent
                                        max-width="800px">
                                        <template v-slot:activator="{ on, attrs }">
                                            <v-text-field v-model="settings.companyLatitude" label="Latitude"
                                                prepend-icon="mdi-map-marker" readonly v-bind="attrs" v-on="on"
                                                v-on:click="locationDialog = true" aria-label="Company Latitude"
                                                :rules="[v => !!v || 'Latitude is required']"
                                                hint="Select the company location on the map"
                                                persistent-hint></v-text-field>
                                        </template>
                                        <v-card>
                                            <v-card-title class="headline">Select Company Location</v-card-title>
                                            <v-card-text>
                                                <l-map :zoom="13" :center="mapCenter" style="height: 400px;">
                                                    <l-tile-layer :url="tileUrl"
                                                        :attribution="mapAttribution"></l-tile-layer>
                                                    <l-marker :lat-lng="markerPosition"
                                                        @update:lat-lng="updateLocation">
                                                        <l-popup>{{ markerPopup }}</l-popup>
                                                    </l-marker>
                                                </l-map>
                                            </v-card-text>
                                            <v-card-actions>
                                                <v-spacer></v-spacer>
                                                <v-btn text color="primary"
                                                    @click="locationDialog = false">Cancel</v-btn>
                                                <v-btn text color="primary" @click="confirmLocation">OK</v-btn>
                                            </v-card-actions>
                                        </v-card>
                                    </v-dialog>
                                </v-col>
                                <v-col cols="12" md="6">
                                    <v-text-field v-model="settings.companyLongitude" label="Longitude"
                                        prepend-icon="mdi-map-marker" readonly v-bind="attrs" v-on="on"
                                        v-on:click="locationDialog = true" aria-label="Company Longitude"
                                        :rules="[v => !!v || 'Longitude is required']"
                                        hint="Select the company location on the map" persistent-hint></v-text-field>
                                </v-col>
                            </v-row>
                        </v-form>
                    </v-card-text>
                    <v-divider></v-divider>
                    <v-card-actions class="justify-center">
                        <v-btn :disabled="!isFormValid" color="primary" @click="saveSettings" class="mx-2">Save</v-btn>
                        <v-btn color="secondary" @click="resetSettings" class="mx-2">Reset</v-btn>
                        <v-btn color="error" @click="deleteSettings" class="mx-2">Delete</v-btn>
                    </v-card-actions>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>

export default {

    data() {
        return {
            clockInDialog: false,
            clockOutDialog: false,
            weekendClockInDialog: false,
            weekendClockOutDialog: false,
            locationDialog: false,
            isFormValid: false,
            settings: {
                clockInTime: null,
                clockOutTime: null,
                weekendClockInTime: null,
                weekendClockOutTime: null,
                companyEmail: "",
                companyLatitude: null,
                companyLongitude: null,
            },
            tempClockInTime: null,
            tempClockOutTime: null,
            tempWeekendClockInTime: null,
            tempWeekendClockOutTime: null,
            markerPosition: [0, 0], // Default marker position
            mapCenter: [0, 0], // Default map center
            tileUrl: "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
            mapAttribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            markerPopup: "Office Location",
        };
    },
    methods: {
        updateClockInTime() {
            this.tempClockInTime = this.formatTime(this.settings.clockInTime);
        },
        updateClockInTimeFromPicker(time) {
            this.tempClockInTime = this.formatTime(time);
        },
        confirmClockInTime() {
            this.settings.clockInTime = this.tempClockInTime;
            this.clockInDialog = false;
        },
        updateClockOutTime() {
            this.tempClockOutTime = this.formatTime(this.settings.clockOutTime);
        },
        updateClockOutTimeFromPicker(time) {
            this.tempClockOutTime = this.formatTime(time);
        },
        confirmClockOutTime() {
            this.settings.clockOutTime = this.tempClockOutTime;
            this.clockOutDialog = false;
        },
        updateWeekendClockInTime() {
            this.tempWeekendClockInTime = this.formatTime(this.settings.weekendClockInTime);
        },
        updateWeekendClockInTimeFromPicker(time) {
            this.tempWeekendClockInTime = this.formatTime(time);
        },
        confirmWeekendClockInTime() {
            this.settings.weekendClockInTime = this.tempWeekendClockInTime;
            this.weekendClockInDialog = false;
        },
        updateWeekendClockOutTime() {
            this.tempWeekendClockOutTime = this.formatTime(this.settings.weekendClockOutTime);
        },
        updateWeekendClockOutTimeFromPicker(time) {
            this.tempWeekendClockOutTime = this.formatTime(time);
        },
        confirmWeekendClockOutTime() {
            this.settings.weekendClockOutTime = this.tempWeekendClockOutTime;
            this.weekendClockOutDialog = false;
        },
        formatTime(time) {
            if (!time) return null;
            const date = new Date(`1970-01-01T${time}:00`);
            const hours = String(date.getHours()).padStart(2, "0");
            const minutes = String(date.getMinutes()).padStart(2, "0");
            const seconds = "00";
            return `${hours}:${minutes}:${seconds}`;
        },
        updateLocation(event) {
            this.markerPosition = [event.latlng.lat, event.latlng.lng];
        },
        confirmLocation() {
            this.settings.companyLatitude = this.markerPosition[0];
            this.settings.companyLongitude = this.markerPosition[1];
            this.locationDialog = false;
        },
        async saveSettings() {
            if (this.$refs.form.validate()) {
                try {
                    const response = await axios.post("/api/v1/settings", this.settings);
                    console.log("Settings saved:", response.data);
                    this.$emit("update:settings", response.data);
                } catch (error) {
                    console.error("Error saving settings:", error);
                }
            }
        },
        async deleteSettings() {
            try {
                const response = await axios.delete("/api/v1/settings");
                console.log("Settings deleted:", response.data);
                this.resetSettings();
                this.$emit("delete:settings");
            } catch (error) {
                console.error("Error deleting settings:", error);
            }
        },
        resetSettings() {
            this.settings = {
                clockInTime: null,
                clockOutTime: null,
                weekendClockInTime: null,
                weekendClockOutTime: null,
                companyEmail: "",
                companyLatitude: null,
                companyLongitude: null,
            };
        },
    },
};
</script>
