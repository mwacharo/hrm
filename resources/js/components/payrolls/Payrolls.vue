<template>
    <div class="row align-items-center">
        <div class="col">
            <h3 class="page-title">Payroll</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
                <li class="breadcrumb-item active">Payrolls</li>
            </ul>
        </div>
        <div class="col-auto float-right ml-auto">
            <a href="/payrolls.create" class="btn add-btn"><i
                    class="fa fa-plus"></i>
                Update Payrolls</a>

            <!-- Upload Excel and PDF buttons -->
            <a href="#" class="btn btn-success mx-1" data-toggle="modal" data-target="#import_announcement">
                <i class="fa fa-upload"></i> Import Excel
            </a>
            <a href="#" class="btn btn-primary mx-1" data-toggle="modal" data-target="#download_excel">
                <i class="fa fa-download"></i> Download Excel
            </a>
            <a href="#" class="btn btn-info mx-1" data-toggle="modal" data-target="#download_pdf">
                <i class="fa fa-download"></i> Download PDF
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-striped custom-table datatable mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Employee Name</th>
                            <th>Basic Pay</th>
                            <th>Allowances</th>
                            <td>Gross Salary</td>
                            <th>Deductions</th>
                            <th>Net Salary</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-for="(salary, index) in salaries" :key="index">
                            <td>{{ index + 1 }}</td>
                            <td>{{ salary.user.firstname }} {{ salary.user.lastname }}</td>
                            <td>{{ salary.basic_pay ? salary.basic_pay || 'N/A' : 'N/A' }}</td>
                            <td>{{ salary.deductions }}
                            </td>
                            <td>{{ salary.cal }}</td>
                            <td>{{salary.bonuses }}</td>
                            <td>{{ salary.overtime }}</td>

                            <td>
                                <a title="print payslip" :href="'/print_payslip/' + salary.id">
                                    <i class="la la-print text-danger h4 mx-1"></i>
                                </a>

                                <a title="email payslip" :href="'/email-payslip/' + salary.id">
                                    <i class="la la-envelope text-danger h4 mx-1"></i>
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
export default {

    data() {
        return {
            base_url: '/',
            salaries: [],
        };
    },
    mounted() {
        this.fetchSalaries();
    },
    methods: {
        fetchSalaries() {
            axios
                .get(this.base_url + "api/v1/salaries")
                .then((response) => {
                    this.salaries = response.data.salaries;
                    console.log("Fetched salaries:", response.data.salaries);

                })
                .catch((error) => {
                    console.error("Error fetching salaries:", error);
                });
        },
    },

};
</script>
