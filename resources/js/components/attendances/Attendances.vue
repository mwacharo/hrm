<template>
  <v-layout>
    <v-navigation-drawer location="right" width="500" v-model="drawer" temporary>
      <v-container>
        <v-row justify="space-between" class="drawer-header">
          <v-col>
            <v-list-item-title>Filter</v-list-item-title>
          </v-col>
          <v-col class="text-right">
            <v-icon @click="drawer = false">mdi-close</v-icon>
          </v-col>
        </v-row>
        <v-divider></v-divider>
        <v-row align="center" justify="center">
          <v-col cols="12">
            <v-list dense nav>
              <v-list-item>
                <v-col cols="12">
                  <v-label>Country:</v-label>
                  <v-select v-model="filters.country_id" item-value="id" item-title="name" :items="countries" clearable
                    dense>
                  </v-select>
                </v-col>
              </v-list-item>
              <v-list-item>
                <v-col cols="12">
                  <v-label>Attendance Date:</v-label>
                  <v-text-field v-model="filters.attendance_date" type="date"></v-text-field>
                </v-col>
              </v-list-item>
              <v-list-item>
                <v-col cols="12">
                  <v-label>Employee:</v-label>
                  <v-autocomplete v-model="filters.user_id" :items="employees" item-title="fullName" item-value="id"
                    clearable dense>
                  </v-autocomplete>
                </v-col>
              </v-list-item>

              <v-list-item>
                <v-col cols="12">
                  <v-label>Status:</v-label>
                  <v-select v-model="filters.status" :items="['In Time', 'Late', 'Absent']" clearable dense>
                  </v-select>
                </v-col>
              </v-list-item>
            </v-list>
          </v-col>
        </v-row>
        <v-row align="center" justify="center" class="drawer-footer">
          <v-col cols="12">
            <v-btn @click.prevent="filterAttendances">
              <v-icon>mdi-filter</v-icon>
            </v-btn>
          </v-col>
        </v-row>
      </v-container>
    </v-navigation-drawer>
    <v-main>
      <v-col>
        <v-row>
          <v-col cols="12" sm="3">
            <v-card class="pa-4" variant="flat">
              <v-row align="center">
                <v-icon color="purple lighten-1" size="48">mdi-briefcase-clock</v-icon>
                <v-col>
                  <div class="text-h6">{{ averageWorkingHours }}</div>
                  <div class="subtitle-2">Average Working Hours</div>
                </v-col>
              </v-row>
            </v-card>
          </v-col>
          <v-col cols="12" sm="3">
            <v-card class="pa-4" variant="flat">
              <v-row align="center">
                <v-icon color="blue lighten-1" size="48">mdi-clock-time-four</v-icon>
                <v-col>
                  <div class="text-h6">{{ averageClockInTime }}</div>
                  <div class="subtitle-2">Average In Time</div>
                </v-col>
              </v-row>
            </v-card>
          </v-col>
          <v-col cols="12" sm="3">
            <v-card class="pa-4" variant="flat">
              <v-row align="center">
                <v-icon color="green lighten-1" size="48">mdi-clock-out</v-icon>
                <v-col>
                  <div class="text-h6">{{ averageClockOutTime }}</div>
                  <div class="subtitle-2">Average Out Time</div>
                </v-col>
              </v-row>
            </v-card>
          </v-col>
          <v-col cols="12" sm="3">
            <v-card class="pa-4" variant="flat">
              <v-row align="center">
                <v-icon color="orange lighten-1" size="48">mdi-account-multiple-outline</v-icon>
                <v-col>
                  <div class="text-h6">{{ averagePresencePercentage }}%</div>
                  <div class="subtitle-2">Average Presence(Today)</div>
                </v-col>
              </v-row>
            </v-card>
          </v-col>
        </v-row>
      </v-col>
      <v-divider></v-divider>
      <v-row justify="end" class="text-right">

        <v-col cols="auto">
          <v-icon size="16" color="primary mx-1" @click.stop="drawer = !drawer" small>mdi-filter</v-icon>
          <v-btn @click="addAttendanceDialog = true" icon>
            <v-tooltip activator="parent" location="top">Add Attendance
            </v-tooltip>
            <v-icon color="primary">mdi-plus</v-icon>
          </v-btn>
        </v-col>
      </v-row>
      <v-row no-gutters>
        <v-col>
          <v-responsive>

            <v-progress-linear v-if="loading" color="green" indeterminate></v-progress-linear>
            <v-data-table v-model="selected" :headers="headers" :items="attendances" item-key="id" class="elevation-10"
              dense show-select :search="search">
              <template v-slot:top>
                <v-row>
                  <v-col cols="12">
                    <v-text-field v-model="search" prepend-inner-icon="mdi-magnify" label="Search Attendance" clearable
                      dense />
                  </v-col>
                </v-row>
              </template>

              <template v-slot:item.clock_in_time="{ item }">

                <span :style="{ color: item.status === 'In Time' ? 'green' : 'red' }">
                  {{ item.clock_in_time ? item.clock_in_time : '00:00:00' }}
                </span>

              </template>

              <template v-slot:item.status="{ item }">

                <span :style="{ color: item.status === 'In Time' ? 'green' : 'red' }">
                  <v-icon left class="mr-2">
                    {{ item.status === 'In Time' ? 'mdi-clock-check-outline' : 'mdi-clock-alert-outline' }}
                  </v-icon>
                  {{ item.status }}
                </span>

              </template>

              <template v-slot:item.clock_out_time="{ item }">

                <span :style="{ color: item.clock_out_time ? 'green' : 'red' }">
                  {{ item.clock_out_time ? item.clock_out_time : '00:00' }}
                </span>

              </template>

              <template v-slot:item.actions="{ item }">

                <v-tooltip bottom>
                  <template v-slot:activator="{ on }">
                    <v-icon @click="viewAttendance(item)" class="mx-1" title="View Attendance" color="black" v-on="on">
                      mdi-information
                    </v-icon>

                    <!-- 
                    <v-icon @click="deleteAttendance(item)" class="mx-1" title="Delete Attendance" color="red"
                      v-on="on">
                      mdi-delete
                    </v-icon> -->

                    <v-icon @click="confirmDelete(item)" class="mx-1" title="Delete Attendance" color="red">
                      mdi-delete
                    </v-icon>

                  </template>
                  <span>View Attendance</span>
                </v-tooltip>

              </template>
            </v-data-table>

          </v-responsive>
        </v-col>
      </v-row>




      <!-- Delete Confirmation Modal -->
      <v-dialog v-model="deleteDialog" max-width="400">
        <v-card>
          <v-card-title class="headline">Confirm Deletion</v-card-title>
          <v-card-text>
            Are you sure you want to delete this attendance record?
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn @click="deleteDialog = false" color="grey">Cancel</v-btn>
            <v-btn @click="deleteAttendance" color="red">Delete</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>

      <v-dialog v-model="addAttendanceDialog" max-width="500">
        <v-card prepend-icon="mdi-update" title="Add Attendance">
          <v-card-text>
            <v-text-field v-model="newAttendance.attendance_date" label="Attendance Date" type="date">
            </v-text-field>
            <v-autocomplete v-model="newAttendance.user_id" :items="employees" item-title="fullName" item-value="id"
              label="Employee" clearable dense>
            </v-autocomplete>
            <v-select v-model="newAttendance.attendance_type" :items="['Clock In', 'Clock Out']" label="Attendance Type"
              clearable dense>
            </v-select>
            <v-text-field v-model="newAttendance.time" placeholder="08:00:00" label="Time" type="text">
            </v-text-field>
          </v-card-text>
          <v-card-actions class="justify-content-end">
            <v-btn @click="addAttendanceDialog = false" color="error"><v-icon>mdi-cancel</v-icon>Cancel</v-btn>
            <v-btn @click="addAttendance" color="primary">submit</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
      <v-dialog v-model="viewAttendanceModal" max-width="600">
        <v-card>
          <v-card-title>
            <v-row class="justify-space-between align-center">
              <v-col cols="auto" class="d-flex align-center">
                <v-icon>mdi-clock-check-outline</v-icon>
                <span class="ml-2">Attendance Information</span>
              </v-col>

              <v-col cols="auto" class="d-flex justify-end">
                <v-btn icon @click="viewAttendanceModal = false">
                  <v-icon color="red">mdi-close</v-icon>
                </v-btn>
              </v-col>
            </v-row>
          </v-card-title>

          <v-divider></v-divider>
          <v-card-text>
            <v-timeline align="start" density="compact">
              <v-timeline-item dot-color="indigo" size="x-small">
                <div class="mb-4">
                  <div class="font-weight-normal">
                    <strong>Attendance Date: </strong>
                    {{ selectedAttendance.attendance_date }}
                  </div>
                </div>
              </v-timeline-item>
            </v-timeline>
            <v-timeline align="start" density="compact">
              <v-timeline-item dot-color="green" size="x-small">
                <div class="mb-4">
                  <div class="font-weight-normal">
                    <strong>Employee: </strong>
                    {{ selectedAttendance.employeeName }}
                  </div>
                </div>
              </v-timeline-item>
            </v-timeline>

            <v-timeline align="start" density="compact">
              <v-timeline-item dot-color="red" size="x-small">
                <div class="mb-4">
                  <div class="font-weight-normal">
                    <strong>Time in: </strong>
                    {{ selectedAttendance.clock_in_time }}
                  </div>
                </div>
              </v-timeline-item>
            </v-timeline>
            <v-timeline align="start" density="compact">
              <v-timeline-item dot-color="yellow" size="x-small">
                <div class="mb-4">
                  <div>
                    <strong>Attendance status: </strong>
                    <span :color="selectedAttendance.clock_out_time ? 'green' : 'red'">
                      {{ selectedAttendance.status }}
                    </span>
                  </div>
                </div>
              </v-timeline-item>
            </v-timeline>
            <v-timeline align="start" density="compact">
              <v-timeline-item dot-color="indigo" size="x-small">
                <div class="mb-4">
                  <div class="font-weight-normal">
                    <strong>Notes: </strong>
                    {{ selectedAttendance.notes ?? '---' }}
                  </div>
                </div>
              </v-timeline-item>
            </v-timeline>
          </v-card-text>
        </v-card>
      </v-dialog>
    </v-main>
  </v-layout>
</template>

<script>

export default {
  data() {
    return {
      deleteDialog: false,
      drawer: null,
      selected: [],
      base_url: '/',
      countries: [],
      headers: [
        { title: 'Employee', key: 'user.fullName' },
        { title: 'Attendance Date', key: 'attendance_date' },
        { title: 'Time in ', key: 'clock_in_time' },
        { title: 'Status', key: 'status' },
        { title: 'Time Out ', key: 'clock_out_time' },
        { title: 'Action', key: 'actions' },
      ],

      averagePresencePercentage: '0.00',
      averageClockInTime: '00:00',
      averageClockOutTime: '00:00',
      averageWorkingHours: 9.45,
      newAttendance: {
        user_id: null,
        attendance_date: null,
        time: null,
        attendance_type: '',
        notes: '',
      },
      viewAttendanceModal: false,
      editAttendance: {
        user_id: null,
        attendance_date: null,
        time: null,
        attendance_type: null,
        notes: null
      },
      search: '',
      loading: false,
      notes: '',
      selectedStatus: null,
      selectedAttendance: {
        attendance_date: '',
        employeeName: '',
        clock_in_time: '',
        clock_out_time: '',
        status: '',
      },
      employees: [],
      attendances: [],
      addAttendanceDialog: false,
      editAttendanceDialog: false,
      confirmDeleteDialog: false,
      selectedAttendanceId: null,
      updateStatusesModal: false,
      addNotesModal: false,
      filters: {
        country_id: null,
        attendance_date: null,
        user_id: null,
        attendance_type: null,
        status: null
      },
      updateStatusesModal: false,
      bulkDeleteAttendances: false,
      selectedStatus: null,
      note: '',
      viewAttendanceModal: false,
      selectedAttendance: {
        attendance_date: '',
        employeeName: '',
        clock_in_time: '',
        clock_out_time: '',
        status: '',
      },
    };
  },

  async created() {
    await this.fetchAttendances();
    await this.fetchEmployees();
    this.fetchUnits()
  },

  methods: {


    // Open the delete confirmation modal
    confirmDelete(item) {
      this.selectedAttendance = item;
      this.deleteDialog = true;
    },

    // Delete the attendance after confirmation
    async deleteAttendance() {
      if (!this.selectedAttendance) return;

      try {
        await axios.delete(`/api/v1/attendances/${this.selectedAttendance.id}`);
        this.$toastr.success("Attendance deleted successfully!");

        // Refresh attendance list after deletion
        this.fetchAttendances();
      } catch (error) {
        console.error("Error deleting attendance:", error);
        this.$toastr.error("Failed to delete attendance.");
      } finally {
        // Close the modal
        this.deleteDialog = false;
        this.selectedAttendance = null;
      }
    },
    async fetchEmployees() {
      const apiUrl = this.base_url + 'api/v1/users';

      try {
        const response = await axios.get(apiUrl);
        this.employees = response.data.users.filter(employee => !employee.super_admin)
          .map(employee => ({
            id: employee.id,
            fullName: `${employee.firstname} ${employee.lastname}`,
          }));
      } catch (error) {
        console.error('Error fetching employees:', error);
      }
    },

    async fetchAttendances() {
      this.loading = true;
      const apiUrl = this.base_url + 'api/v1/attendances';

      try {
        const response = await axios.get(apiUrl);
        const data = response.data;

        this.attendances = data.attendances.map(attendance => ({
          ...attendance,
          user: {
            ...attendance.user,
            fullName: `${attendance.user.firstname} ${attendance.user.lastname}`
          }
        }));

        // Extract and assign additional data
        this.averagePresencePercentage = data.average_presence_percentage;
        this.averageClockInTime = data.average_clock_in_time;
        this.averageClockOutTime = data.average_clock_out_time;
        // this.averageWorkingHours = data.average_working_hours;

      } catch (error) {
        console.error('Error fetching data:', error);
      } finally {
        this.loading = false;
      }
    },

    fetchUnits() {
      const apiUrl = this.base_url + 'api/v1/branches';
      axios.get(apiUrl)
        .then(response => {
          this.countries = response.data.branches;
        })
        .catch(error => {
          console.error('Error fetching units:', error);
        });

    },

    filterAttendances() {
      this.loading = true;
      const apiUrl = this.base_url + 'api/v1/attendances';
      const params = {
        unit_id: this.filters.country_id,
        attendance_date: this.filters.attendance_date,
        user_id: this.filters.user_id,
        attendance_type: this.filters.attendance_date,
        status: this.filters.status
      };

      axios.get(apiUrl, { params })
        .then(response => {
          this.drawer = false
          this.attendances = response.data.attendances.map(attendance => ({
            ...attendance,
            user: {
              ...attendance.user,
              fullName: `${attendance.user.firstname} ${attendance.user.lastname}`
            }
          }));

          this.loading = false;
        })
        .catch(error => {
          this.loading = false;
          console.error('Error filtering attendances:', error);
        });
    },

    addAttendance() {
      this.newAttendance.attendance_type = this.newAttendance.attendance_type.toLowerCase().replace(/\s+/g, '_');

      const apiUrl = this.base_url + 'api/v1/attendances';

      axios.post(apiUrl, this.newAttendance)
        .then(response => {
          const addedAttendance = response.data.attendance;
          this.fetchAttendances();
          this.$toastr.success('Attendance added successfully!');
          this.addAttendanceDialog = false;
        })
        .catch(error => {
          const errorMessage = error.response && error.response.data.message ? error.response.data.message : 'Error adding attendance. Please try again.';
          this.$toastr.error(errorMessage);
          console.error('Error adding attendance:', error);
        });
    },

    viewAttendance(attendance) {
      this.viewAttendanceModal = true;
      console.log("the attendance is" + attendance.user.firstname)
      this.selectedAttendance = {
        attendance_date: attendance.attendance_date,
        employeeName: `${attendance.user.firstname} ${attendance.user.lastname}`,
        clock_in_time: attendance.clock_in_time,
        clock_out_time: attendance.clock_out_time,
        status: attendance.status,
        notes: attendance.notes,

      };
    },
  },
};
</script>
