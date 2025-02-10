<template>
    <v-container-fluid>
        <v-row>
            <div class="d-flex justify-center flex-wrap ml-3">
                <v-span @click="fetchTasks" color="secondary" variant="flat" class="m-1">
                    Total: {{ totalTasks }}
                </v-span>

                <v-span @click="filterCompleted" color="success" variant="flat" class="m-1">
                    Completed: {{ completedTasks }}
                </v-span>
                <v-span @click="filterNew" color="info" variant="flat" class="m-1">
                    In Progress: {{ inProgressTasks }}
                </v-span>
                <v-span @click="filterPending" color="warning" variant="flat" class="m-1">
                    New: {{ newTasks }}
                </v-span>
            </div>
        </v-row>

        <v-row>
            <v-col cols="12" md="8">
                <v-text-field v-model="search" label="Search" clearable @clear="clearSearch"
                    prepend-inner-icon="mdi-magnify">
                </v-text-field>
            </v-col>
            <v-col cols="auto">
                <v-btn @click="openNewTaskDialog" icon>
                    <v-tooltip activator="parent" location="top">Add new task
                    </v-tooltip>
                    <v-icon color="info" x-small>mdi-tooltip-plus</v-icon>
                </v-btn>
            </v-col>

            <v-col cols="auto">
                <v-btn icon @click="refreshTasks">
                    <v-tooltip activator="parent" location="top">Refresh tasks
                    </v-tooltip>
                    <v-icon color="danger" x-small>mdi-refresh</v-icon>
                </v-btn>
            </v-col>
        </v-row>

        <v-data-table :headers="tableHeaders" :items="filteredTasks" item-key="id" class="elevation-1">
            <template v-slot:item="{ item, index }">
                <tr>
                    <td>{{ index + 1 }}</td>
                    <td><strong>{{ item.name }}</strong></td>
                    <td>{{ item.department.name }}</td>
                    <td>{{ item.leader.firstname }} {{ item.leader.lastname }}</td>
                    <td>{{ item.status }}</td>
                    <td>{{ item.description }}</td>
                    <td>{{ item.start_date }}</td>
                    <td>{{ item.end_date }}</td>
                    <td>
                        <span title="View Task" class="mx-2" @click="openViewModal(item.id)">
                            <v-icon size="18" color="primary">mdi-eye</v-icon>
                        </span>

                        <span title="Edit Task" class="mx-2" @click="openEditModal(item.id)">
                            <v-icon size="18" color="info">mdi-pen</v-icon>
                        </span>

                        <a :href="'/tasks/pdf_form/' + item.id" title="Print issuance form" class="mx-2">
                            <v-icon size="18">mdi-printer</v-icon>
                        </a>
                        <span title="Edit Task" @click="openEditModal(item.id)">
                            <v-icon size="18" color="danger">mdi-delete</v-icon>
                        </span>
                    </td>

                </tr>
            </template>
        </v-data-table>

        <v-dialog v-model="newTaskDialog" max-width="600px" persistent>
            <v-card>
                <v-card-title class="headline">
                    <v-icon>mdi-plus</v-icon>
                    New Task
                </v-card-title>
                <v-card-text>
                    <v-form ref="newTaskForm" @submit.prevent="submitNewTaskForm">
                        <v-text-field v-model="newTask.name" label="Task Name">
                        </v-text-field>
                        <v-text-field v-model="newTask.start_date" label="Start Date" type="date">
                        </v-text-field>
                        <v-text-field v-model="newTask.end_date" label="End Date" type="date">
                        </v-text-field>
                        <v-select v-model="newTask.user_id" :items="fullName" label="Leader" item-value="id"
                            item-title="fullName">
                        </v-select>
                        <v-select v-model="newTask.department_id" :items="departments" label="Department"
                            item-value="id" item-title="name">
                        </v-select>
                        <v-textarea v-model="newTask.description" label="Description"></v-textarea>
                        <!-- Add other form fields (files, progress, status) as needed -->
                    </v-form>
                </v-card-text>
                <v-card-actions>
                    <v-row class="d-flex align-center justify-space-between">
                        <v-col>
                            <v-btn @click="closeNewTaskDialog" color="error">Close</v-btn>
                        </v-col>
                        <v-col cols="auto" class="text-right">
                            <v-btn @click="createTask" icon color="success">
                                <v-icon>mdi-check-circle</v-icon>
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <v-dialog v-model="viewTaskDialog" max-width="600px">
            <v-card>
                <v-card-title class="headline">
                    <v-icon>mdi-eye</v-icon>
                    View Task
                </v-card-title>
                <v-card-text>
                    <!-- Display task details here -->
                </v-card-text>
                <v-card-actions>
                    <v-btn @click="viewTaskDialog = false" color="primary">Close</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- Edit Task Modal -->
        <v-dialog v-model="editTaskDialog" max-width="600px">
            <v-card>
                <v-card-title class="headline">
                    <v-icon>mdi-pencil</v-icon>
                    Edit Task
                </v-card-title>
                <v-card-text>
                    <!-- Edit task form goes here -->
                </v-card-text>
                <v-card-actions>
                    <v-btn @click="viewTaskDialog = false" color="primary">close</v-btn>
                    <v-btn @click="saveEditedTask" color="success">Save</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

    </v-container-fluid>
</template>

<script>
export default {
    data() {
        return {
            base_url: '/',
            users: [],
            totalTasks: 0,
            search: '',
            completedTasks: 0,
            newTasks: 0,
            inProgressTasks: 0,
            viewTaskDialog: false,
            editTaskDialog: false,
            departments: [],
            tasks: [],
            tableHeaders: [
                { title: '#', key: 'index' },
                { title: 'Name', key: 'name' },
                { title: 'Department', key: 'department.name' },
                { title: 'Leader', key: 'leader' },
                { title: 'Status', key: 'status' },
                { title: 'Description', key: 'description' },
                { title: 'Start Date', key: 'start_date' },
                { title: 'End Date', key: 'end_date' },
                { title: 'Action', key: 'actions', sortable: false },
            ],
            newTask: {
                name: '',
                department_id: '',
                start_date: null,
                end_date: null,
                user_id: '',
                description: '',
            },
            datePickerMenu: false,
            newTaskDialog: false,
            newTaskStartDatePickerMenu: false,
            newTaskEndDatePickerMenu: false,
        };
    },
    mounted() {
        this.fetchUsers();
        this.fetchDepartments();
        this.fetchTasks();
    },

    methods: {

        fetchUsers() {
            const apiUrl = this.base_url + 'api/v1/users';

            axios.get(apiUrl)
                .then(response => {
                    this.users = response.data.users.map(user => ({
                        id: user.id,
                        first_name: user.firstname,
                        last_name: user.lastname,
                        email: user.email,
                        phone: user.phone,
                        unit: user.unit,
                        department: user.department,
                        designation: user.designation,
                        is_enabled: user.is_enabled,
                    }));
                })
                .catch(error => {
                    console.error('Error fetching users:', error);
                });
        },

        fetchTasks() {
            const apiUrl = this.base_url + 'api/v1/tasks';

            axios.get(apiUrl)
                .then(response => {
                    this.tasks = response.data.tasks;
                    this.updateTaskCounts();
                })
                .catch(error => {
                    console.error('Error fetching tasks:', error);
                });
        },

        fetchDepartments() {
            const apiUrl = this.base_url + 'api/v1/departments';

            axios.get(apiUrl)
                .then(response => {
                    this.departments = response.data.departments;
                })
                .catch(error => {
                    console.error('Error fetching departments:', error);
                });
        },
        submitFilterForm() {
        },
        openNewTaskDialog() {
            this.newTaskDialog = true;
            this.resetNewTaskForm();
        },
        closeNewTaskDialog() {
            this.newTaskDialog = false;
        },
        submitNewTaskForm() {
            if (this.$refs.newTaskForm.validate()) {
                this.createTask();
            }
        },
        createTask() {
            const taskData = {
                name: this.newTask.name,
                department_id: this.newTask.department_id,
                start_date: this.newTask.start_date,
                end_date: this.newTask.end_date,
                user_id: this.newTask.user_id,
                description: this.newTask.description,
            };
            axios.post('/api/v1/tasks', taskData)
                .then(response => {
                    console.log('Task created successfully:', response.data);
                    this.$toastr.success('Task created successfully!', 'Success');
                    this.newTaskDialog = false;
                    this.fetchTasks();
                })
                .catch(error => {
                    console.error('Error creating task:', error);
                    toastr.error('Error creating task. Please try again.', 'Error');
                });
        },
        resetNewTaskForm() {
            this.newTask = {
                name: '',
                department_id: '',
                start_date: null,
                end_date: null,
                leader: '',
                description: '',
            };
            this.newTaskDatePickerMenu = false;
        },
        updateTaskCounts() {
            this.totalTasks = this.tasks.length;
            this.completedTasks = this.tasks.filter(task => task.status === 'completed').length;
            this.newTasks = this.tasks.filter(task => task.status === 'new').length;
            this.inProgressTasks = this.tasks.filter(task => task.status === 'in_progress').length;
        },
        openViewModal(taskId) {
            this.fetchTaskById(taskId)
                .then(task => {
                    this.selectedTask = task;
                    this.viewTaskDialog = true;
                })
                .catch(error => {
                    console.error('Error fetching task:', error);
                });
        },
        openEditModal(taskId) {
            this.fetchTaskById(taskId)
                .then(task => {
                    this.selectedTask = task;
                    this.editTaskDialog = true;
                })
                .catch(error => {
                    console.error('Error fetching task:', error);
                });
        },
    },
    computed: {
        fullName() {
            return this.users.map(user => ({
                ...user,
                fullName: `${user.first_name} ${user.last_name}`,
            }));
        },
        filteredTasks() {
            // Filter tasks based on search query for task name or leader
            return this.tasks.filter(task => {
                const taskNameMatches = task.name.toLowerCase().includes(this.search.toLowerCase());
                const leaderMatches = task.leader && typeof task.leader === 'string' && task.leader.toLowerCase().includes(this.search.toLowerCase());

                // Return true if either task name or leader matches the search query
                return taskNameMatches || leaderMatches;
            });
        },
    },
};
</script>
