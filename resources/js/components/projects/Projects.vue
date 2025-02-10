<template>
    <div>
        <div class="modal-header mb-3">
            <h5 class="modal-title">Employee Attendance list</h5>
        </div>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Checkin</th>
                    <th>Checkout</th>
                    <td>Status</td>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(employee, index) in employees" :key="employee.id">
                    <td>{{ index + 1 }}</td>
                    <td>{{ employee.firstname }} {{ employee.lastname }}</td>
                    <td>
                        {{ employee.phone }}
                        <a @click="editEmployee(employee)" title="Call" href="javascript:void(0)"><i
                                class="mx-1 la la-phone-square text-danger"></i></a>
                        <a @click="editEmployee(employee)" title="SMS" href="javascript:void(0)"><i
                                class="mx-1 la la-envelope-o text-info"></i></a>
                    </td>
                    <td>{{ employee.email }}</td>
                    <td>{{ employee.department.name }}</td>
                    <td>{{ employee.designation.name }}</td>
                    <td>
                        <a @click="editEmployee(employee)" title="view" href="javascript:void(0)"><i
                                class="la la-eye text-success"></i></a>
                        |
                        <a @click="editEmployee(employee)" title="edit" href="javascript:void(0)"><i
                                class="la la-pencil text-warning"></i></a>
                        |
                        <a @click="deleteEmployee(employee)" title="delete" href="javascript:void(0)"><i
                                class="la la-trash text-danger"></i></a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>

export default {
    data() {
        return {
            base_url: "/",
            alert_success: false,
            alert_error: false,
            employee: {
                unit_id: '',
                office_id: '',
                first_name: '',
                last_name: '',
                email: '',
                phone: '',
                department_id: '',
                designation_id: '',
                avatar: null,
                employment_date: '',
            },
            branches: [],
            offices: [],
            departments: [],
            designations: [],
        };
    },
    mounted() {
        this.fetchUnits();
        this.fetchOffices();
        this.fetchDepartments();
        this.fetchDesignations();
    },
    methods: {
        fetchUnits() {
            axios
                .get(this.base_url + "api/v1/branches")
                .then((response) => {
                    this.branches = response.data.branches;
                    console.log("Fetched branches:", response.data.branches);
                })
                .catch((error) => {
                    console.error("Error fetching branches:", error);
                });
        },
        fetchOffices() {
            axios
                .get(this.base_url + "api/v1/offices")
                .then((response) => {
                    this.offices = response.data.offices;
                    console.log("Fetched offices:", response.data.offices);
                })
                .catch((error) => {
                    console.error("Error fetching offices:", error);
                });
        },
        fetchDepartments() {
            axios
                .get(this.base_url + "api/v1/departments")
                .then((response) => {
                    this.departments = response.data.departments;
                    console.log("Fetched departments:", response.data.departments);
                })
                .catch((error) => {
                    console.error("Error fetching departments:", error);
                });
        },
        fetchDesignations() {
            axios
                .get(this.base_url + "api/v1/designations")
                .then((response) => {
                    this.designations = response.data.designations;
                    console.log("Fetched designations:", response.data.designations);
                })
                .catch((error) => {
                    console.error("Error fetching designations:", error);
                });
        },

        submitForm() {


            const formData = new FormData();
            const avatar = document.querySelector('#avatar');
            formData.append('avatar', avatar.files[0]);
            formData.append('first_name', this.employee.first_name);
            formData.append('last_name', this.employee.last_name);
            formData.append('email', this.employee.email);
            formData.append('phone', this.employee.phone);
            formData.append('unit_id', this.employee.unit_id);
            formData.append('office_id', this.employee.office_id);
            formData.append('department_id', this.employee.department_id);
            formData.append('designation_id', this.employee.designation_id);
            formData.append('employment_date', this.employee.employment_date);

            let uri = this.base_url + `api/v1/employees`;

            axios.post(uri, formData)
                .then((response) => {

                    console.log(response)
                })
                .catch((error) => {

                    console.log(error);
                });

        },
        handleAvatarUpload(event) {
            // Handle the avatar file upload here and update this.employee.avatar
            this.employee.avatar = event.target.files[0];
        },


    },
};
</script>

