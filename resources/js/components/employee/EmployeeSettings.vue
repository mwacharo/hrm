<template>
    <v-container fluid class="d-flex justify-center">
        <v-col cols="12" md="9" class="pa-5">
            <v-card class="pa-4 elevation-2">
                <v-row>
                    <!-- Search Section -->
                    <v-col cols="12" class="mb-5">
                        <h3 class="text-h5 font-weight-medium mb-4">Find the setting that you need</h3>
                        <v-text-field placeholder="Search settings" append-icon="mdi-magnify" hide-details
                            class="py-2"></v-text-field>
                    </v-col>

                    <!-- Most Visited Settings Section -->
                    <v-col cols="12">
                        <h3 class="text-h6 font-weight-medium mb-4">Most visited settings</h3>
                        <v-row>
                            <v-col cols="12" md="4" class="mb-4 d-flex">
                                <v-card class="pa-4 flex-grow-1">
                                    <h3>Account</h3>
                                    <p>Add/Update account settings.
                                    </p>
                                </v-card>
                            </v-col>
                            <v-col cols="12" md="4" class="mb-4 d-flex">
                                <v-card class="pa-4 flex-grow-1">
                                    <h3>Activity log</h3>
                                    <p>View and manage your activity on Facebook.</p>
                                </v-card>
                            </v-col>
                            <v-col cols="12" md="4" class="mb-4 d-flex">
                                <v-card class="pa-4 flex-grow-1">
                                    <h3>Dark mode</h3>
                                    <p>Choose if you want to use dark mode.</p>
                                </v-card>
                            </v-col>
                        </v-row>
                    </v-col>

                    <!-- Additional Links Section -->
                    <v-col cols="12" class="mt-4">
                        <h3 class="text-h6 font-weight-medium mb-4">Looking for something else?</h3>
                        <v-row>
                            <v-col cols="12" class="mb-4">
                                <v-card class="d-flex align-center pa-4">
                                    <v-icon class="mr-4">mdi-lock</v-icon>
                                    <v-card-title class="pa-0">Privacy Centre</v-card-title>
                                    <v-card-text class="pa-0">Learn how to manage and control your privacy across your account
                                        products.</v-card-text>
                                    <v-spacer></v-spacer>
                                    <v-icon>mdi-arrow-right</v-icon>
                                </v-card>
                            </v-col>
                            <v-col cols="12" class="mb-4">
                                <v-card class="d-flex align-center pa-4">
                                    <v-icon class="mr-4">mdi-help-circle</v-icon>
                                    <v-card-title class="pa-0">Facebook Help Centre</v-card-title>
                                    <v-card-text class="pa-0">Learn more about our updated settings experience on
                                        your account.</v-card-text>
                                    <v-spacer></v-spacer>
                                    <v-icon>mdi-arrow-right</v-icon>
                                </v-card>
                            </v-col>
                        </v-row>
                    </v-col>
                </v-row>
            </v-card>
        </v-col>
    </v-container>
</template>

<script>
export default {
    data() {
        return {};
    },
    created() { },
    methods: {}
};
</script>
