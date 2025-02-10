<template>
    <div>
        <div class="mb-3">
            <h5 class="modal-title">Assign Leave</h5>

        </div>
        <div class="border p-5 col-md-6">
            <form @submit.prevent="submitForm" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Employee <span class="text-danger">*</span></label>
                            <select v-model="employee_id" class="select form-control">
                                <option value=''>Select Employee</option>
                                <option v-for="employee in employees" :key="employee.id" :value="employee.id">
                                    {{ employee.firstname }} {{ employee.lastname }}
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Leave Type <span class="text-danger">*</span></label>
                            <select v-model="leaveType_id" class="select form-control">
                                <option value=''>Select Leave Type</option>
                                <option v-for="leaveType in leaveTypes" :key="leaveType.id" :value="leaveType.id">
                                    {{ leaveType.name }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Phone <span class="text-danger">*</span></label>
                            <input name="from" v-model="phone" class="form-control" type="tel">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Document <span class="text-muted">(If any)</span></label>
                            <input @change="handleFileChange" name="document" id="document" class="form-control"
                                type="file">
                        </div>
                    </div>
                </div>
                <div class="row  mt-2">
                    <div class="form-group">
                        <label>Application Date <span class="text-danger">*</span></label>
                        <div>
                            <input name="application_date" v-model="application_date" class="form-control" type="date">
                        </div>
                    </div>
                </div>
                <div class="row  mt-2">
                    <div class="form-group col-md-6">
                        <label>From <span class="text-danger">*</span></label>
                        <div>
                            <input name="from" v-model="from" class="form-control" type="date">
                        </div>
                    </div>
                    <div class="form-group col-md-6">
                        <label>To <span class="text-danger">*</span></label>
                        <div>
                            <input name="to" v-model="to" class="form-control" type="date">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Comment <span class="text-muted">(optional)</span></label>
                    <textarea name="comment" v-model="comment" rows="4" class="form-control"></textarea>
                </div>

                <div class="form-group  mt-2">
                    <label>Status </label>
                    <select v-model="status" class="select form-control">
                        <option value="">Select Status</option>
                        <option>Approved</option>
                        <option>Pending</option>
                        <option>Declined</option>
                    </select>
                </div>
                <div class="submit-section">
                    <button class="btn-lg btn-success w-100 submit-btn">Submit</button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>

export default {
    data() {
        return {
            base_url: "/",
            alert_success: false,
            alert_error: false,
            employee_id: '',
            leaveType_id: '',
            application_date: '',
            phone:'',
            from: '',
            to: '',
            comment: '',
            status: '',
            selectedFile: null,
            employees: [],
            departments: [],
            leaveTypes: []
        };
    },
    mounted() {
        this.fetchEmployees();
        this.fetchDepartments();
        this.fetchLeaveTypes();
    },
    methods: {
        fetchEmployees() {
            axios
                .get(this.base_url + "api/v1/employees")
                .then((response) => {
                    this.employees = response.data.employees;
                    console.log("Fetched employees:", response.data.employees);
                })
                .catch((error) => {
                    console.error("Error fetching employees:", error);
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
        fetchLeaveTypes() {
            axios
                .get(this.base_url + "api/v1/leave-types")
                .then((response) => {
                    this.leaveTypes = response.data.leaveTypes;
                    console.log("Fetched Leave Types:", response.data.leaveTypes);
                })
                .catch((error) => {
                    console.error("Error fetching leave types:", error);
                });
        },
        handleFileChange(event) {
            this.selectedFile = event.target.files[0];
        },
        submitForm() {
            const formData = new FormData();
            formData.append('employee_id', this.employee_id);
            formData.append('leaveType_id', this.leaveType_id);
            formData.append('application_date', this.application_date);
            formData.append('from', this.from);
            formData.append('to', this.to);
            formData.append('comment', this.comment);
            formData.append('phone', this.phone);
            formData.append('status', this.status);
            if (this.selectedFile) {
                formData.append('document', this.selectedFile);
            }

            let uri = this.base_url + `api/v1/leaves`;

            axios.post(uri, formData)
                .then((response) => {

                    console.log(response)
                })
                .catch((error) => {

                    console.log(error);
                });

        },
    },
};
</script>

