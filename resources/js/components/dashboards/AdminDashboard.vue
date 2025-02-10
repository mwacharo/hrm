<template>
  <v-card>
    <v-card-item>
      <v-card-title>
        Dashboard
      </v-card-title>

      <v-card-subtitle>
        Welcome Back, {{ user.firstname }}!
      </v-card-subtitle>
    </v-card-item>
    <v-card-text>
      <v-row>
        <v-col cols="12" md="6" lg="3" v-for="(stat, index) in statistics" :key="index">
          <v-card :subtitle="stat.value" :title="stat.label" link class="mb-4">
            <template v-slot:prepend>
              <v-avatar color="success">
                <v-icon :icon="stat.icon"></v-icon>
              </v-avatar>
            </template>
            <template v-slot:append>
              <v-avatar size="24">
                <v-icon color="primary" icon="mdi-chart-bar"></v-icon>
              </v-avatar>
            </template>
            <v-card-text>{{ stat.description }}</v-card-text>
          </v-card>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12" md="6" lg="3" v-for="(status, index) in employeeStatus" :key="index">
          <v-card link class="mb-4">
            <v-card-text>
              <div class="d-flex justify-space-between align-center">
                <div>
                  <span class="d-block">{{ status.label }}</span>
                </div>
              </div>
              <h3 class="mb-3">{{ status.value }}</h3>
              <v-progress-linear :value="status.percentage"
                :color="getProgressColor(status.percentage)"></v-progress-linear>
              <p class="mb-0">Percentage {{ status.percentage }} %</p>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12" lg="4" class="text-center">
          <v-card class="mb-4">
            <AttendanceGraph />
          </v-card>
        </v-col>
        <v-col cols="12" lg="4" class="text-center">
          <v-card class="mb-4">
            <DepartmentChart />
          </v-card>
        </v-col>
        <v-col cols="12" lg="4" class="text-center">
          <v-card class="mb-4">
            <v-progress-linear v-if="loading" color="green" height="2" indeterminate rounded>
            </v-progress-linear>
            <AttendancePieChart />
          </v-card>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12" lg="7">
          <v-card class="shadow-sm mb-4">
            <v-card-title class="text-left">Recent Leaves</v-card-title>
            <v-card-text>
              <v-row class="text-subtitle-1 font-weight-bold mb-2">
                <v-col cols="4">Employee</v-col>
                <v-col cols="4">Leave Type</v-col>
                <v-col cols="3">Status</v-col>
              </v-row>
              <v-divider></v-divider>
              <v-list class="elevation-0 transparent">
                <v-list-item v-for="(item, index) in recentLeaves" :key="item.id">
                  <v-row align="center">
                    <v-col cols="4">{{ item.user.firstname }} {{ item.user.lastname }}</v-col>
                    <v-col cols="4">{{ item.leave_type.name }}</v-col>
                    <v-col cols="3">
                      <v-chip :color="getStatusColor(item.status)">{{ item.status }}</v-chip>
                    </v-col>
                  </v-row>
                </v-list-item>
                <v-divider v-if="index < recentLeaves.length - 1"></v-divider>
              </v-list>
            </v-card-text>
          </v-card>
        </v-col>
        <v-col cols="12" lg="5" class="text-center">
          <v-card class="mb-4">
            <GenderChart />
          </v-card>
        </v-col>
      </v-row>
    </v-card-text>
  </v-card>
</template>

<script>
import DepartmentChart from '@/components/charts/DepartmentChart.vue';
import GenderChart from '@/components/charts/GenderChart.vue';
import AttendanceGraph from '@/components/charts/AttendanceGraph.vue';
import AttendancePieChart from '@/components/charts/AttendancePieChart.vue';

export default {
  name: 'Admin Dashboard',
  props: {
    user: {
      type: Object,
      required: true,
    },
  },
  components: {
    AttendanceGraph,
    DepartmentChart,
    GenderChart,
    AttendancePieChart
  },
  data() {
    return {
      base_url: '/',
      statistics: [],
      loading: false,
      employeeStatus: [],
      attendanceHeaders: [
        { title: '#', value: 'index' },
        { title: 'Date', value: 'clockin' },
        { title: 'Name', value: 'user' },
        { title: 'Status', value: 'status' },
      ],
      leaveHeaders: [
        { title: '#', value: 'index' },
        { title: 'Employee', value: 'user_id' },
        { title: 'Leave Type', value: 'leave_type_id' },
        { title: 'Status', value: 'status' },
      ],
      newEmployeeHeaders: [
        { title: '#', value: 'index' },
        { title: 'Employee', value: 'name' },
        { title: 'Department', value: 'department' },
        { title: 'Position', value: 'position' },
      ],
      recentAttendances: [],
      recentLeaves: [],
      newEmployees: [],
    };
  },
  mounted() {
    this.fetchStatistics();
    this.fetchLeaves();
    this.fetchUsers();
    this.fetchAttendaces();
  },
  methods: {
    getStatusColor(status) {
      switch (status) {
        case 'Approved':
          return 'success';
        case 'Pending':
          return 'warning';
        case 'Cancelled':
          return 'error';
        default:
          return 'primary';
      }
    },
    statusColor(status) {
      switch (status) {
        case 'In Time':
          return 'success';
        case 'Late':
          return 'danger';
        case 'Absent':
          return 'error';
        default:
          return 'primary';
      }
    },
    fetchAttendaces() {
      this.loading = true;
      const uri = `/api/v1/attendances`;
      axios.get(uri)
        .then(response => {
          this.recentAttendances = response.data.attendances.slice(0, 3);
        })
        .catch(error => {
          console.error('Error fetching attendances:', error);
        })
        .finally(() => {
          this.loading = false;
        });
    },
    fetchLeaves() {
      const uri = `/api/v1/leaves`;
      axios.get(uri)
        .then(response => {
          this.recentLeaves = response.data.leaves.slice(0, 3);
        })
        .catch(error => {
          console.error('Error fetching Leaves:', error);
        });
    },
    fetchUsers() {
      const uri = `/api/v1/users`;
      axios.get(uri)
        .then(response => {
          this.newEmployees = response.data.users.slice(-3);
        })
        .catch(error => {
          console.error('Error fetching users:', error);
        });
    },
    fetchStatistics() {
      axios.get('/api/v1/dashboard')
        .then(response => {
          this.statistics = response.data.statistics;
          this.employeeStatus = response.data.employeeStatus;
        })
        .catch(error => {
          console.error('Error fetching statistics:', error);
        });
    },
    getProgressColor(percentage) {
      if (percentage >= 80) {
        return 'success';
      } else if (percentage >= 50) {
        return 'warning';
      } else {
        return 'red';
      }
    }
  },
};
</script>

<style scoped>
.v-card {
  background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
  border-radius: 10px;
}

.transparent {
  background-color: transparent !important;
}
</style>
