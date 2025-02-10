<template>
  <div>
    <v-container>
      <v-row v-if="loading">
        <v-col cols="12" class="text-center">
          <v-progress-circular indeterminate color="primary" size="50"></v-progress-circular>
        </v-col>
      </v-row>

      <v-row v-else>
        <v-col v-for="user in paginatedUsers" :key="user.id" cols="12" sm="6" md="4">
          <v-card class="user-card">
            <v-row align="center" justify="start">
              <v-col cols="auto">
                <v-avatar size="40">
                  <v-img :src="getAvatar(user)" alt="Avatar"></v-img>
                </v-avatar>
              </v-col>
              <v-col cols="auto" class="ml-2">
                <div class="headline">{{ user.firstname }} {{ user.lastname }}</div>
                <div class="caption">{{ user.email }}</div>
              </v-col>
            </v-row>

            <v-divider class="mt-2"></v-divider>

            <v-card-text>
              <div class="user-info-item">
                <v-icon color="primary">mdi-phone</v-icon>
                <span class="info-label">Phone:</span> {{ user.phone }}
              </div>
              <div class="user-info-item">
                <v-icon color="primary">mdi-office-building</v-icon>
                <span class="info-label">Branch:</span> {{ user.unit.name }}
              </div>
              <div class="user-info-item">
                <v-icon color="primary">mdi-domain</v-icon>
                <span class="info-label">Office:</span> {{ user.office.name }}
              </div>
              <div class="user-info-item">
                <v-icon color="primary">mdi-account-group</v-icon>
                <span class="info-label">Department:</span> {{ user.department.name }}
              </div>
              <div class="user-info-item">
                <v-icon color="primary">mdi-badge-account-horizontal</v-icon>
                <span class="info-label">Designation:</span> {{ user.designation.name }}
              </div>
              <div class="user-info-item">
                <v-icon color="primary">mdi-calendar-clock</v-icon>
                <span class="info-label">Employment Date:</span> {{ user.employment_date }}
              </div>
            </v-card-text>

            <v-card-actions>
              <v-tooltip bottom>
                <template v-slot:activator="{ on }">
                  <v-btn icon v-on="on" @click="startChat(user)">
                    <v-icon color="blue lighten-1">mdi-chat</v-icon>
                  </v-btn>
                </template>
                <span>Chat</span>
              </v-tooltip>

              <v-tooltip bottom>
                <template v-slot:activator="{ on }">
                  <v-btn icon v-on="on" @click="sendSMS(user)">
                    <v-icon color="green lighten-1">mdi-message-text</v-icon>
                  </v-btn>
                </template>
                <span>SMS</span>
              </v-tooltip>

              <v-tooltip bottom>
                <template v-slot:activator="{ on }">
                  <v-btn icon v-on="on" @click="sendEmail(user)">
                    <v-icon color="orange lighten-1">mdi-email</v-icon>
                  </v-btn>
                </template>
                <span>Email</span>
              </v-tooltip>

              <v-tooltip bottom>
                <template v-slot:activator="{ on }">
                  <v-btn icon v-on="on" @click="makeCall(user)">
                    <v-icon color="red lighten-1">mdi-phone</v-icon>
                  </v-btn>
                </template>
                <span>Call</span>
              </v-tooltip>

              <v-tooltip bottom>
                <template v-slot:activator="{ on }">
                  <v-btn icon v-on="on" @click="toggleUserStatus(user)">
                    <v-icon :color="user.is_enabled ? 'green' : 'red'">
                      {{ user.is_enabled ? 'mdi-check-circle' : 'mdi-alert-circle' }}
                    </v-icon>
                  </v-btn>
                </template>
                <span>{{ user.is_enabled ? 'Disable' : 'Enable' }}</span>
              </v-tooltip>
            </v-card-actions>
          </v-card>
        </v-col>
      </v-row>

      <v-row justify="center" class="mt-4">
        <v-pagination
          v-model="currentPage"
          :length="totalPages"
          @input="updatePagination"
        ></v-pagination>
      </v-row>
    </v-container>
  </div>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      users: [],
      loading: true,
      currentPage: 1,
      perPage: 6,
    };
  },
  computed: {
    totalPages() {
      return Math.ceil(this.users.length / this.perPage);
    },
    paginatedUsers() {
      const start = (this.currentPage - 1) * this.perPage;
      const end = start + this.perPage;
      return this.users.slice(start, end);
    },
  },
  mounted() {
    this.fetchUsers();
  },
  methods: {
    fetchUsers() {
      this.loading = true;
      axios
        .get("/api/v1/users")
        .then((response) => {
          this.users = response.data.users;
        })
        .catch((error) => {
          console.error("Error fetching users:", error);
        })
        .finally(() => {
          this.loading = false;
        });
    },
    updatePagination() {
      window.scrollTo({ top: 0, behavior: "smooth" });
    },
    getAvatar(user) {
      if (user.gender === "Male") {
        return "/assets/img/male-avatar.svg";
      } else if (user.gender === "Female") {
        return "/assets/img/female-avatar.png";
      } else {
        return "/assets/img/user.jpg";
      }
    },
    startChat(user) {
      console.log("Starting chat with user:", user);
    },
    sendSMS(user) {
      console.log("Sending SMS to user:", user);
    },
    sendEmail(user) {
      console.log("Sending email to user:", user);
    },
    makeCall(user) {
      console.log("Making call to user:", user);
    },
    toggleUserStatus(user) {
      user.is_enabled = !user.is_enabled;
      axios
        .put(`/api/v1/users/${user.id}`, { is_enabled: user.is_enabled })
        .then((response) => {
          console.log("User status updated successfully:", response.data);
        })
        .catch((error) => {
          console.error("Error updating user status:", error);
        });
    },
  },
};
</script>

<style scoped>
.user-card {
  margin-bottom: 20px;
}

.info-label {
  margin-left: 5px;
}

.user-info-item {
  margin-top: 8px;
}
</style>
