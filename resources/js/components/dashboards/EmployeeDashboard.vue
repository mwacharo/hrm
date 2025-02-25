<template>
  <v-container>
      <v-row>
        <v-col cols="12">
        <v-alert type="success" dense text>
          Hello {{ user.firstname }}, welcome to the employee self-service portal!
        </v-alert>
      </v-col>
        <v-col cols="12" lg="7">
          <v-card outlined>
            <v-row align="center" no-gutters>
              <v-col cols="auto" xs="auto" sm="auto">
                <v-avatar class="pa-1" size="100">
                  <v-img :src="user.gender === 'Male' ? '/assets/img/male-avatar.svg' : '/assets/img/female-avatar.png'"
                    class="v-img-blur" />
                </v-avatar>
              </v-col>
              <v-col xs="12" sm="8">
                <v-card-title class="elevation-0 transparent mb-0">
                  {{ user.firstname }} {{ user.lastname }}
                </v-card-title>
                <v-card-subtitle class="mt-0">
                  <v-span>
                    <v-icon>mdi-account-tie</v-icon>
                    {{ user.designation.name }}, {{ user.department.name }}
                  </v-span>
                  <br>

                  <v-span class="mb-3 mt-3">
                    <v-icon>mdi-map-marker</v-icon>{{ user.office.name }}, {{ user.unit.name }}
                  </v-span>
                </v-card-subtitle>
              </v-col>
            </v-row>

            <v-card-text>
              <v-list dense class="elevation-0 transparent">
                <v-list-item>
                  <v-list-item-icon>
                    <v-icon>mdi-email</v-icon>
                  </v-list-item-icon>
                  <v-list-item-content class="pl-2"> <!-- Add padding-left here -->
                    {{ user.email }}
                  </v-list-item-content>
                </v-list-item>
                <v-list-item>
                  <v-list-item-icon>
                    <v-icon>mdi-phone</v-icon>
                  </v-list-item-icon>
                  <v-list-item-content class="pl-2"> <!-- Add padding-left here -->
                    {{ user.phone }}
                  </v-list-item-content>
                </v-list-item>
                <v-list-item>
                  <v-list-item-icon>
                    <v-icon>mdi-calendar</v-icon>
                  </v-list-item-icon>
                  <v-list-item-content class="pl-2"> <!-- Add padding-left here -->
                    {{ user.work_anniversary ?? '--' }}
                  </v-list-item-content>
                </v-list-item>
              </v-list>

            </v-card-text>
          </v-card>

        </v-col>

        <v-col cols="12" lg="5">
          <v-card outlined>
            <v-card-title class="elevation-0 transparent">Leave Management</v-card-title>
            <v-card-text>
              <v-list dense class="elevation-0 transparent">
                <v-list-item>
                  <v-list-item-icon>
                    <v-icon>mdi-calendar-multiple</v-icon>
                  </v-list-item-icon>
                  <v-list-item-content>Total Leave Days: {{ statistics.annualLeaveDaysAssigned }}
                    days</v-list-item-content>
                </v-list-item>
                <v-list-item>
                  <v-list-item-icon>
                    <v-icon>mdi-check-circle</v-icon>
                  </v-list-item-icon>
                  <v-list-item-content>Leave Taken: {{ statistics.leaveTaken }} days</v-list-item-content>
                </v-list-item>
                <v-list-item>
                  <v-list-item-icon>
                    <v-icon>mdi-scale-balance</v-icon>
                  </v-list-item-icon>
                  <v-list-item-content>Leave Balance: {{ statistics.leaveBalance }}
                    days</v-list-item-content>
                </v-list-item>
                <v-list-item>
                  <v-list-item-icon>
                    <v-icon>mdi-clock-alert</v-icon>
                  </v-list-item-icon>
                  <v-list-item-content>Pending Requests: {{ statistics.leaveRequests
                    }}</v-list-item-content>
                </v-list-item>
              </v-list>
              <!-- <v-btn @click="applyLeaveModal = true" color="primary" block>Apply for Leave</v-btn> -->
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <v-row>
        <v-col cols="12" md="6" lg="4">
          <v-card outlined>
            <v-card-title class="elevation-0 transparent">Performance & Analytics</v-card-title>
            <v-card-text>
              <v-list dense class="elevation-0 transparent">
                <v-list-item>
                  <v-list-item-icon>
                    <v-icon>mdi-trophy</v-icon>
                  </v-list-item-icon>
                  <v-list-item-content>Recent Awards: <small>---</small></v-list-item-content>
                </v-list-item>
                <v-list-item>
                  <v-list-item-icon>
                    <v-icon>mdi-chart-line</v-icon>
                  </v-list-item-icon>
                  <v-list-item-content>Monthly Performance (Avg):-- %</v-list-item-content>
                </v-list-item>
                <v-list-item>
                  <v-list-item-icon>
                    <v-icon>mdi-calendar-check</v-icon>
                  </v-list-item-icon>
                  <v-list-item-content>AVG Presence --%</v-list-item-content>
                </v-list-item>
                <v-list-item>
                  <v-list-item-icon>
                    <v-icon>mdi-clock-check</v-icon>
                  </v-list-item-icon>
                  <v-list-item-content>Early Attendance -- %</v-list-item-content>
                </v-list-item>
              </v-list>
            </v-card-text>
          </v-card>
        </v-col>

        <v-col cols="12" md="6" lg="4">
          <v-card outlined>
            <v-card-title class="elevation-0 transparent">HR Contacts</v-card-title>
            <v-card-text>
              <v-list dense class="elevation-0 transparent">
                <v-list-item>
                  <v-list-item-icon>
                    <v-icon>mdi-account-tie</v-icon>
                  </v-list-item-icon>
                  <v-list-item-content>HR Manager: {{ hr.firstname }} {{ hr.lastname }}
                  </v-list-item-content>
                </v-list-item>
                <v-list-item>
                  <v-list-item-icon>
                    <v-icon>mdi-email</v-icon>
                  </v-list-item-icon>
                  <v-list-item-content>Email: {{ hr.email }}</v-list-item-content>
                </v-list-item>
                <v-list-item>
                  <v-list-item-icon>
                    <v-icon>mdi-phone</v-icon>
                  </v-list-item-icon>
                  <v-list-item-content>Phone: {{ hr.phone }}</v-list-item-content>
                </v-list-item>
              </v-list>
              <v-btn color="primary" block>Contact HR</v-btn>
            </v-card-text>
          </v-card>
        </v-col>

        <v-col cols="12" md="6" lg="4">
          <v-card outlined>
            <v-card-title class="elevation-0 transparent">Payslips</v-card-title>
            <v-card-text>
              <v-list dense class="elevation-0 transparent">
                <v-list-item>
                  <v-list-item-icon>
                    <v-icon>mdi-file-document</v-icon>
                  </v-list-item-icon>
                  <v-list-item-content>Latest Payslip: {{ getPreviousMonth() }}</v-list-item-content>
                </v-list-item>
              </v-list>
              <v-btn color="primary" block disabled>Download Payslip</v-btn>
              <v-btn color="secondary" block disabled>View Payslip History</v-btn>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <!-- Create Leave Modal -->
      <v-dialog v-model="applyLeaveModal" max-width="600px" persistent>
        <v-card>
          <v-toolbar color="primary" dark>
            <v-toolbar-title>Apply Leave</v-toolbar-title>
            <v-spacer></v-spacer>
            <v-btn icon @click="applyLeaveModal = false">
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </v-toolbar>

          <v-card-text>
            <v-form ref="createLeaveForm">
              <v-container>
                <v-row>
                  <v-col cols="12" md="6">
                    <v-select v-model="newLeave.leave_type_id" :items="leaveTypes" label="Leave Type" item-value="id"
                      item-title="name" outlined clearable :rules="[v => !!v || 'Leave Type is required']">
                    </v-select>
                  </v-col>

                  <v-col cols="12" md="6">
                    <v-text-field v-model="newLeave.days" label="Days" type="number" outlined
                      :rules="[v => !!v || 'Days are required', v => v > 0 || 'Days must be greater than 0']">
                    </v-text-field>
                  </v-col>

                  <v-col cols="12" md="6">
                    <v-text-field v-model="newLeave.from" label="From" type="date" outlined
                      :rules="[v => !!v || 'From date is required']">
                    </v-text-field>
                  </v-col>

                  <v-col cols="12" md="6">
                    <v-text-field v-model="newLeave.to" label="To" type="date" outlined
                      :rules="[v => !!v || 'To date is required']">
                    </v-text-field>
                  </v-col>

                  <v-col cols="12" md="6">
                    <v-select v-model="newLeave.manager" :items="managers" label="Line Manager" item-value="id"
                      item-title="fullname" outlined clearable :rules="[v => !!v || 'Line Manager is required']">
                    </v-select>
                  </v-col>

                  <v-col cols="12" md="6">
                    <v-select v-model="newLeave.hod" :items="hods" label="HOD" item-value="id" item-title="fullname"
                      outlined clearable :rules="[v => !!v || 'HOD is required']">
                    </v-select>
                  </v-col>

                  <v-col cols="12" md="6">
                    <v-text-field v-model="newLeave.phone" label="Phone" type="text" placeholder="254" outlined
                      :rules="[v => !!v || 'Phone number is required', v => /^[+254]\d{9,12}$/.test(v) || 'Invalid phone number']">
                    </v-text-field>
                  </v-col>

                  <v-col cols="12" md="6">
                    <v-file-input v-model="newLeave.document" label="Attach Document (Optional)"
                      accept=".pdf, .doc, .docx, .jpeg, .jpg, .png" outlined clearable></v-file-input>
                  </v-col>
                  <v-col cols="12">
                    <v-textarea v-model="newLeave.comment" label="Comment (Optional)" outlined clearable>
                    </v-textarea>
                  </v-col>
                </v-row>
              </v-container>
            </v-form>
          </v-card-text>
          <v-card-actions class="justify-end ">
            <!-- <v-btn color="red" @click="applyLeaveModal = false" outlined>Cancel</v-btn> -->
            <v-btn class="text-primary mr-2" @click="submitNewLeave">Apply</v-btn>
            <v-progress-circular v-if="isLoading" class="ml-2" color="primary" indeterminate>
            </v-progress-circular>
          </v-card-actions>
        </v-card>
      </v-dialog>
  </v-container>
</template>

<script>

export default {

  name: 'EmployeeDashboard',
  props: {
    user: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      base_url: '/',
      applyLeaveModal: false,
      statistics: {
        teamMembers: 0,
        earlyAttendances: 0,
        lateAttendances: 0,
        annualLeaveDaysAssigned: 0,
        leaveBalance: 0,
        leaveTaken: 0,
        leaveRequests: 0,
        notifications: 0,
        totalNotifications: 0,
        teamMembersOnLeave: 0,
        totalAppliedLeaves: 0,
        totalApprovedLeaves: 0,
        recentLeave: '',
      },
      hr: {
        firstname: '',
        lastname: '',
        email: '',
        phone: ''
      },
      users: [],
      managers: [],
      hods: [],
      leaveTypes: [],
      newLeave: {
        leave_type_id: null,
        from: null,
        to: null,
        phone: null,
        days: null,
        manager: null,
        hod: null,
        document: null,
        comment: null,
      },
      isLoading: false,
    };
  },
  mounted() {
    this.fetchDashboardStatistics();
    this.fetchUsers();
    this.fetchLeaveTypes();
  },
  methods: {
    getPreviousMonth() {
      const now = new Date();
      const previousMonth = new Date(now.getFullYear(), now.getMonth() - 1);

      // Format as "Month Year", e.g., "July 2024"
      const month = previousMonth.toLocaleString('default', { month: 'long' });
      const year = previousMonth.getFullYear();

      return `${month} ${year}`;
    },
    fetchUsers() {
      const apiUrl = this.base_url + `api/v1/users`;
      axios.get(apiUrl)
        .then(response => {
          this.users = response.data.users.map(user => ({
            ...user,
            fullname: `${user.firstname} ${user.lastname}`,
          }));

          this.hods = this.users.filter(user => user.is_hod === 1);
          this.managers = this.users.filter(user => user.designation_id === 1);

          console.log("HODs: ", this.hods);
          console.log("Managers: ", this.managers);
        })
        .catch(error => {
          console.error('Error fetching users:', error);
        });
    },
    fetchLeaveTypes() {
      const apiUrl = this.base_url + `api/v1/leave-types`;
      axios.get(apiUrl)
        .then(response => {
          this.leaveTypes = response.data.leaveTypes;
        })
        .catch(error => {
          console.error('Error fetching leave Types:', error);
        });
    },
    fetchDashboardStatistics() {
      axios
        .get(`/api/v1/dashboard/${this.user.id}`)
        .then((response) => {
          this.statistics = response.data;
          this.hr = response.data.hr;

          console.log('The api data', response.data);
        })
        .catch((error) => {
          console.error('Error fetching dashboard statistics:', error);
        });
    },
    submitNewLeave() {
      if (this.$refs.createLeaveForm.validate()) {
        this.isLoading = true;
        const formData = new FormData();
        formData.append('user_id', this.user.id);
        formData.append('leave_type_id', this.newLeave.leave_type_id);
        formData.append('from', this.newLeave.from);
        formData.append('to', this.newLeave.to);
        formData.append('phone', this.newLeave.phone);
        formData.append('days', this.newLeave.days);
        formData.append('manager', this.newLeave.manager);
        formData.append('hod', this.newLeave.hod);
        formData.append('comment', this.newLeave.comment);
        if (this.newLeave.document && this.newLeave.document[0]) {
          formData.append('document', this.newLeave.document[0]);
        }

        axios.post('/api/v1/leaves', formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
          },
        })
          .then(response => {

            this.isLoading = false;
            this.$toastr.success(response.data.message);
            this.applyLeaveModal = false;
          })
          .catch(error => {
            this.isLoading = false;
            if (error.response && error.response.data && error.response.data.error) {
              this.$toastr.error(error.response.data.error);
            } else {
              this.$toastr.error(error.message);
            }
          });
      }
    },
  },
};
</script>
