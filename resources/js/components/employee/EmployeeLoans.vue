<template>
    <v-container fluid>
        <v-responsive>
            <v-data-table :items="loans" :headers="headers" item-key="id" class="elevation-10" responsive>
                <template v-slot:top>
                    <v-toolbar flat color="grey">
                        <v-toolbar-title>My Loans</v-toolbar-title>
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
                { title: 'Loan ID', value: 'id' },
                { title: 'Book Title', value: 'book.title' },
                { title: 'Borrow Date', value: 'borrow_date' },
                { title: 'Return Date', value: 'return_date' },
                { title: 'Status', value: 'status' },
                { title: 'Actions', value: 'actions' },
            ],
            loans: []
        };
    },
    methods: {

    }
};
</script>
