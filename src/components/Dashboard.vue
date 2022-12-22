<script lang="ts">
import NavBar from '@/components/navbar/NavBar.vue';
import { Tooltip } from 'bootstrap';
import { defineComponent } from 'vue';
import API_URL from '@/apiUrl';

export default defineComponent({
    components: {
        NavBar
    },
    data() {
        return {
            pending: false,
            kanbans: {
                private: [{
                    id: 0,
                    title: '',
                    public: false,
                    owner: '',
                    creation_date: ''
                }],
                shared: [{
                    id: 0,
                    title: '',
                    public: false,
                    owner: '',
                    creation_date: ''
                }],
                public: [{
                    id: 0,
                    title: '',
                    public: false,
                    owner: '',
                    creation_date: ''
                }]
            },
            tasks: [
                {
                    id: 0,
                    title: 'Ma première tache',
                    description: 'sqnqjskbfqhfbq;jfvqjsfgvq',
                    creation_date: '',
                    deadline: '2022-12-17',
                    assigned_user: '',
                    column_id: 0,
                    kanban_id: 1
                },
            ]
        }
    },
    created() {
        this.kanbans = { private: [], shared: [], public: [] };
        this.tasks = [];
        this.pending = true;

        fetch(API_URL + "/api/kanban/get.php", { credentials: 'include' })
            .then(res => {
                return res.json().then(json => {
                    return res.ok ? json : Promise.reject(json);
                })
            }).then(json => { this.kanbans = json.records })
            .catch(err => { console.log(err.message) })
            .then(res => this.pending = false);

        fetch(API_URL + "/api/task/get.php", { credentials: 'include' })
            .then(res => {
                return res.json().then(json => {
                    return res.ok ? json : Promise.reject(json);
                })
            }).then(json => { this.tasks = json.records })
            .catch(err => { console.log(err.message) })
            .then(res => this.pending = false);
    },
    mounted() {
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        [...tooltipTriggerList].map(tooltipTriggerEl => new Tooltip(tooltipTriggerEl));
    }
})
</script>

<template>
    <NavBar />

    <div class="container-fluid h-100 w-100 p-3 ">
        <div class="row h-100 g-3 align-items-center">
            <div class="col-md-3 h-md-100">
                <div class="h-100 w-100 bg-light border rounded shadow overflow-hidden">
                    <div class="text-bg-primary px-3 py-2 rounded-top shadow-sm fs-5">Mes taches</div>
                    <div class="h-100 w-100 p-2 d-flex flex-column overflow-scroll">
                        <router-link v-for="task in tasks" :key="task.id" :to="'/kanban/' + task.kanban_id"
                            class="w-100 py-1 px-1 mb-1 rounded border shadow-sm">
                            {{ task.title }} <br>
                            <span class="badge text-bg-danger"> {{
                                    task.deadline
                            }} </span>
                        </router-link>
                    </div>
                </div>
            </div>
            <div class="col-md-9 h-100">
                <div class="row h-100 d-flex flex-column">
                    <div class="col pb-2">
                        <div class="bg-light h-100 border rounded shadow d-flex flex-column">
                            <div class="text-bg-primary px-3 py-2 fs-5 rounded-top shadow-sm">Mes kanbans</div>
                            <div class="h-100 px-3 d-inline-flex align-items-center overflow-scroll">
                                <router-link v-for="kanban in kanbans.private" :key="kanban.id"
                                    :to="'/kanban/' + kanban.id"
                                    class="h-75 w-50 w-md-25 position-relative text-break text-center me-3 p-3 border rounded shadow-sm d-flex flex-column flex-shrink-0 align-items-center justify-content-center ">
                                    <h5>{{ kanban.title }}</h5>
                                    <div class="position-absolute bottom-0 w-100 p-1">
                                        <span class="badge text-bg-primary"> {{ kanban.creation_date
                                        }} </span>
                                        <span v-if="kanban.public" class="ms-1 badge text-bg-primary">public</span>
                                    </div>
                                </router-link>
                            </div>
                        </div>
                    </div>
                    <div class="col pb-2">
                        <div class="bg-light h-100 borde rounded shadow d-flex flex-column">
                            <div class="text-bg-primary px-3 py-2 fs-5 rounded-top shadow-sm">Kanbans partagés</div>
                            <div class="h-100 px-3 d-inline-flex align-items-center overflow-scroll">
                                <router-link v-for="kanban in kanbans.shared" :key="kanban.id"
                                    :to="'/kanban/' + kanban.id"
                                    class="position-relative flex-shrink-0 text-break me-3 p-3 border rounded shadow-sm h-75 w-50 w-md-25 d-flex align-items-center justify-content-center text-center">
                                    <h5>{{ kanban.title }}</h5>
                                    <div class="position-absolute bottom-0 w-100 p-1">
                                        <span class="badge text-bg-primary"> {{ kanban.creation_date }} </span>
                                        <span v-if="kanban.public" class="ms-1 badge text-bg-primary">public</span>
                                    </div>
                                </router-link>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="bg-light h-100 border rounded shadow d-flex flex-column">
                            <div class="text-bg-primary px-3 py-2 fs-5 rounded-top shadow-sm">Kanban publics</div>
                            <div class="h-100 px-3 d-inline-flex align-items-center overflow-scroll">
                                <router-link v-for="kanban in kanbans.public" :key="kanban.id"
                                    :to="'/kanban/' + kanban.id"
                                    class="position-relative flex-shrink-0 text-break me-3 p-3 border rounded shadow-sm h-75 w-50 w-md-25 d-flex align-items-center justify-content-center text-center">
                                    <h5>{{ kanban.title }}</h5>
                                    <div class="position-absolute bottom-0 w-100 p-1">
                                        <span class="badge text-bg-primary"> {{ kanban.creation_date }} </span>
                                    </div>
                                </router-link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div v-if="pending" class="position-fixed w-100 h-100 d-flex align-items-center justify-content-center">
        <div
            class="position-fixed p-5 rounded-3 bg-secondary bg-opacity-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary m-5" role="status" style="width: 4rem; height: 4rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>
</template>

<style>
.container>a {
    text-decoration: none !important;
    color: inherit !important;
}
</style>