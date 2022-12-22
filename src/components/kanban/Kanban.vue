<script lang="ts">
import { defineComponent } from 'vue';
import NavBar from '../navbar/NavBar.vue';
import draggable from 'vuedraggable';
import TaskModal from './TaskModal.vue';
import AuthUsersModal from './AuthUsersModal.vue';
import { useKanbanStore } from '@/stores/kanban';
import { useUserStore } from '@/stores/user';

export default defineComponent({
    components: {
        NavBar,
        draggable,
        TaskModal,
        AuthUsersModal,
    },

    data() {
        return {
            modified: false,
            currentTask: {
                id: 0,
                title: '',
                description: '',
                assigned_user: '',
                creation_date: '',
                deadline: '',
                column_id: 0,
                kanban_id: 0
            },
        }
    },

    watch: {
    },

    setup() {
        const userStore = useUserStore();
        const kanbanStore = useKanbanStore();
        return { userStore, kanbanStore }
    },

    created() {
        this.modified = true;
        this.kanbanStore.getKanban(this.$route.params.id as string)
            .then(json => {
                this.modified = false;
                if (!this.kanbanStore.kanban.public) {
                    if (!this.kanbanStore.canAccess(this.userStore.user)) {
                        this.$router.push('/dashboard');
                    }
                }
            })
            .catch(err => {
                console.log(err.message);
                this.$router.push('/dashboard');
                this.modified = false;
            })
    },

    methods: {
        setCurrentTask(task: any, column_id: number, create = false) {
            if (create) {
                let date = new Date();
                date.setHours(date.getHours() + 168);

                this.currentTask = {
                    id: -1,
                    title: 'Nouvelle tache',
                    description: '',
                    assigned_user: '',
                    creation_date: '',
                    deadline: date.toISOString().split('T')[0],
                    column_id: column_id,
                    kanban_id: this.kanbanStore.kanban.id,
                }
            } else {
                this.currentTask = Object.assign({}, task);
            }

        },

        putTask(task: any) {
            this.modified = true;
            this.kanbanStore.putTask(task)
                .then(json => this.modified = false)
                .catch(err => {
                    this.modified = false;
                    console.log(err.message);
                })
        },

        postTask(task: any) {
            this.modified = true;
            this.kanbanStore.postTask(task)
                .then(json => this.modified = false)
                .catch(err => {
                    this.modified = false;
                    console.log(err.message);
                })
        },

        deleteTask() {
            this.modified = true;
            this.kanbanStore.deleteTask(this.currentTask)
                .then(json => this.modified = false)
                .catch(err => {
                    this.modified = false;
                    console.log(err.message);
                })
        },

        updateTaskColumnId(evt: any, newColumn: any) {
            if ("added" in evt) {
                evt.added.element.column_id = newColumn.id;
                this.putTask(evt.added.element);
            }
        },

        checkMove(evt: any) {
            let task = evt.draggedContext.element;
            let is_assigned = (task.assigned_user !== '' &&
                this.userStore.user === task.assigned_user);
            return is_assigned || this.isOwner;
        },

        putKanban() {
            this.modified = true;
            this.kanbanStore.putKanban()
                .then(json => this.modified = false)
                .catch(err => {
                    this.modified = false;
                    console.log(err.message);
                })
        }
    },

    computed: {
        isOwner() {
            return this.userStore.user === this.kanbanStore.kanban.owner;
        }
    }
})

</script>

<template>
    <NavBar />
    <div class="text-primary px-3 py-2">
        <div class="d-flex align-items-center overflow-scroll">
            <div v-if="modified" class="fs-6 spinner-border text-primary me-3" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <input class="form-control w-auto fs-3 fw-semibold text-primary me-3" type="text" name="kanban_title"
                id="kanban_title" v-model="kanbanStore.kanban.title" :disabled="!isOwner" maxlength="40"
                @change="putKanban()">
            <div class="form-check form-switch w-5 me-3">
                <label class="form-check-label fw-semibold" for="flexSwitchCheckDefault">Public</label>
                <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault"
                    v-model="kanbanStore.kanban.public" aria-describedby="publicHelp" :disabled="!isOwner"
                    @change="putKanban()">
            </div>
            <button type="button" class="btn btn-primary me-2 w-100 w-md-auto text-nowrap" data-bs-toggle="modal"
                data-bs-target="#authUsersModal">Utilisateurs ajoutés ({{ kanbanStore.authorized_users.length
                }})</button>
        </div>
    </div>
    <AuthUsersModal id="authUsersModal" :is-owner="isOwner" @save="putKanban()" />
    <div class="w-100 h-100 ps-3 overflow-scroll d-flex">
        <div v-for="column in kanbanStore.columns" :key="column.id"
            class="flex-shrink-0 align-self-start me-3 border rounded shadow-sm column-container">
            <div class="text-bg-primary px-3 py-2 rounded-top shadow-sm fs-5">{{ column.title }}</div>
            <div class="w-100 flex-grow-1 p-1 d-flex flex-column">

                <draggable v-model="kanbanStore.tasks[column.id]" group="tasks" itemKey="id"
                    @change="updateTaskColumnId($event, column)" :move="checkMove" class="mb-1">
                    <template #item="{ element }">
                        <div class="p-1 rounded border shadow-sm mb-1" role="button"
                            @click="setCurrentTask(element, column.id)" data-bs-toggle="modal"
                            data-bs-target="#editTaskModal">
                            <div class="badge text-bg-danger w-100 text-center mb-1">
                                {{ element.deadline }}
                            </div>
                            <div>
                                {{ element.title }}
                            </div>
                            <div class="badge text-bg-primary">
                                {{ element.assigned_user }}
                            </div>
                        </div>
                    </template>
                </draggable>

                <TaskModal id="editTaskModal" v-model="currentTask" @modify="putTask(currentTask)"
                    @delete="deleteTask()" :is-owner="isOwner" />

                <TaskModal id="createTaskModal" :create="true" v-model="currentTask" @create="postTask(currentTask)"
                    :is-owner="isOwner" />

                <button v-if="isOwner" type="button" class="btn btn-outline-primary w-100 align-self-en p-0"
                    data-bs-toggle="modal" data-bs-target="#createTaskModal"
                    @click="setCurrentTask({}, column.id, true)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor"
                        viewBox="0 0 16 16">
                        <path
                            d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</template>

<style>
.column-container {
    width: 300px;
}
</style>