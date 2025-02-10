<template>
    <v-col cols="12">
      <v-card class="mb-5">
        <v-card-text>
          <v-row align="center">
            <v-col cols="12" md="3">
              <div class="d-flex justify-center">
                <v-avatar size="150">
                  <img :src="user.avatar ? user.avatar : 'assets/img/user.jpg'" alt="Avatar">
                </v-avatar>
              </div>
            </v-col>
            <v-col cols="12" md="8">
              <div>
                <h2 class="mb-2">{{ user.firstname }} {{ user.lastname }}</h2>
                <v-row>
                  <v-col cols="12">
                    <v-icon class="mr-2">mdi-email</v-icon>
                    <span>{{ user.email }}</span>
                  </v-col>
                  <v-col cols="12">
                    <v-icon class="mr-2">mdi-phone</v-icon>
                    <span>{{ user.phone }}</span>
                  </v-col>
                  <v-col cols="12">
                    <v-icon class="mr-2">mdi-domain</v-icon>
                    <span>{{ user.unit.name }}</span>
                  </v-col>
                  <v-col cols="12">
                    <v-icon class="mr-2">mdi-briefcase</v-icon>
                    <span>{{ user.designation.name }}</span>
                  </v-col>
                </v-row>
              </div>
            </v-col>
          </v-row>
        </v-card-text>
        <v-card-actions class="d-flex justify-center">
          <v-dialog v-model="dialog" max-width="400px">
            <template v-slot:activator="{ on }">
              <v-tooltip bottom v-for="(action, index) in actions" :key="index" :title="action.title">
                <template v-slot:activator="{ on }">
                  <v-btn icon color="primary" v-bind="on" @click="openModal(action)">
                    <v-icon>{{ action.icon }}</v-icon>
                  </v-btn>
                </template>
              </v-tooltip>
            </template>
            <v-card>
              <v-card-title>{{ currentAction.title }}</v-card-title>
              <v-card-text>
                <!-- Content for the modal goes here -->
                <v-form v-if="currentAction.type === 'edit'" ref="editForm">
                  <v-text-field v-model="formData[currentAction.field]" :label="currentAction.label" :rules="currentAction.rules" outlined></v-text-field>
                </v-form>
              </v-card-text>
              <v-card-actions>
                <v-btn color="primary" @click="executeAction">Save</v-btn>
                <v-btn text @click="dialog = false">Cancel</v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>
        </v-card-actions>
      </v-card>
    </v-col>
  </template>

  <script>
  export default {
    props: {
      user: {
        type: Object,
        required: true
      }
    },
    data() {
      return {
        actions: [
          { title: 'Edit Profile', icon: 'mdi-pencil', type: 'edit', field: 'fullname', label: 'Full Name', rules: [(v) => !!v || 'Full name is required'] },
          { title: 'Change Password', icon: 'mdi-lock-reset', type: 'edit', field: 'password', label: 'New Password', rules: [(v) => !!v || 'Password is required'] },
          { title: 'Edit Email', icon: 'mdi-email', type: 'edit', field: 'email', label: 'Email', rules: [(v) => !!v || 'Email is required'] },
          { title: 'Edit Phone', icon: 'mdi-phone', type: 'edit', field: 'phone', label: 'Phone', rules: [(v) => !!v || 'Phone number is required'] }
        ],
        dialog: false,
        currentAction: null,
        formData: {
          fullname: '',
          password: '',
          email: '',
          phone: ''
        }
      };
    },
    methods: {
      openModal(action) {
        this.currentAction = action;
        this.dialog = true;
      },
      executeAction() {
        if (this.$refs.editForm && !this.$refs.editForm.validate()) return;

        // Update user information based on the current action
        switch (this.currentAction.field) {
          case 'fullname':
            this.user.fullname = this.formData.fullname;
            break;
          case 'password':
            // Perform action to change password
            break;
          case 'email':
            this.user.email = this.formData.email;
            break;
          case 'phone':
            this.user.phone = this.formData.phone;
            break;
          default:
            break;
        }

        this.dialog = false;
      }
    }
  };
  </script>
