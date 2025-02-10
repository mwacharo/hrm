<template>
    <v-container-fluid>
        <v-responsive>
            <v-data-table :headers="headers" :items="users" item-key="id" class="elevation-10" :search="search"
                responsive>
                <template v-slot:item="{ item, index }">
                    <tr>
                        <td>{{ index + 1 }}</td>
                        <td style="padding: 0;">
                            <div class="d-flex align-items-center" style="margin-left: -10px;">
                                <v-avatar size="40">
                                    <v-img :src="getAvatar(item)" alt="Avatar"></v-img>
                                </v-avatar>
                                <div style="margin-left: 10px;">
                                    <div class="headline">{{ item.first_name }} {{ item.last_name }}</div>
                                    <v-span v-if="item.designation" class="caption text-secondary">
                                        {{ item.designation.name }}
                                    </v-span>
                                    <v-span v-else class="caption">Null</v-span>
                                </div>
                            </div>
                        </td>
                        <td>{{ item.email }}</td>
                        <td>{{ item.phone }}</td>
                        <td>{{ item.unit.name }}</td>
                        <td>{{ item.department.name }}</td>
                        <td class="account-status">
                            <v-icon :color="getStatusColor(item.is_enabled)" @click="toggleAccount(item)">
                                {{ getStatusIcon(item.is_enabled) }}
                            </v-icon>
                        </td>
                        <td class="d-flex justify-content-around align-items-center">
                            <v-icon class="toggle-account" title="Make Account" @click="makeCall(item)"
                                color="success">mdi-phone
                            </v-icon>
                            <v-icon class="edit-permissions" title="send email"
                                @click="sendEmail(item.id)" color="info">mdi-email
                            </v-icon>
                        </td>
                    </tr>
                </template>
            </v-data-table>
        </v-responsive>
    </v-container-fluid>
</template>

<script>
import axios from 'axios';

export default {
    props: {
        user: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            base_url: '/',
            search: '',
            headers: [
                { title: '#', value: 'index' },
                { title: 'Employee', value: 'user' },
                { title: 'Email', value: 'email' },
                { title: 'Phone', value: 'phone' },
                { title: 'Branch', value: 'unit.name' },
                { title: 'Department', value: 'department.name' },
                { title: 'Status', value: 'is_enabled' },
                { title: 'Action', value: 'actions' },
            ],
            users: [],
            department_id: null,
        };
    },
    created() {
        this.department_id = this.user.department_id;
        this.fetchUsers();
    },
    methods: {
        fetchUsers() {
            const apiUrl = `${this.base_url}api/v1/users`;
            axios.get(apiUrl, {
                params: {
                    department_id: this.department_id
                }
            })
                .then(response => {
                    this.users = response.data.users.map(user => ({
                        id: user.id,
                        first_name: user.firstname,
                        last_name: user.lastname,
                        super_admin: user.super_admin,
                        email: user.email,
                        phone: user.phone,
                        unit: user.unit,
                        department: user.department,
                        designation: user.designation,
                        is_enabled: user.is_enabled,
                        office: user.office,
                        gender: user.gender,
                        role: user.role
                    }));
                })
                .catch(error => {
                    console.error('Error fetching users:', error);
                });
        },
        getAvatar(user) {
            return user.avatar || 'path/to/default/avatar.jpg';
        },
        getStatusColor(is_enabled) {
            return is_enabled ? 'green' : 'red';
        },
        getStatusIcon(is_enabled) {
            return is_enabled ? 'mdi-check-circle' : 'mdi-cancel';
        },
        toggleAccount(user) {
            user.is_enabled = !user.is_enabled;
            axios.put(`${this.base_url}api/v1/users/${user.id}`, { is_enabled: user.is_enabled })
                .then(response => {
                    console.log('User status updated:', response.data);
                })
                .catch(error => {
                    console.error('Error updating user status:', error);
                });
        },
        editUser(user) {
            console.log('Editing user:', user);
        },
        openPermissionsModal(userId) {
            console.log('Opening permissions modal for user ID:', userId);
        },
        openRoleSwitchModal(user) {
            console.log('Opening role switch modal for user:', user);
        },
        deleteUser(user) {
            if (confirm('Are you sure you want to delete this user?')) {
                axios.delete(`${this.base_url}api/v1/users/${user.id}`)
                    .then(response => {
                        console.log('User deleted:', response.data);
                        this.fetchUsers();
                    })
                    .catch(error => {
                        console.error('Error deleting user:', error);
                    });
            }
        }
    }
};
</script>
