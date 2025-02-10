<template>
    <v-container fluid>
        <v-responsive>
            <v-data-table :items="timelines" :headers="headers" item-key="id" class="elevation-10" responsive>
                <template v-slot:top>
                    <v-toolbar flat color="grey">
                        <v-toolbar-title>Timeline & Updates</v-toolbar-title>
                    </v-toolbar>
                </template>
            </v-data-table>
        </v-responsive>

    </v-container>

</template>

<script>
export default {

    data() {
        return {
            headers: [

                ],
                timelines: []
        };
    },
    methods: {

    }
};
</script>
