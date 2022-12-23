<script lang="ts">
import { defineComponent } from 'vue';
import { useKanbanStore } from '@/stores/kanban';
import { useUserStore } from '@/stores/user';

const API_URL = import.meta.env.VITE_API_URL;

export default defineComponent({
    props: {
        isOwner: Boolean,
    },

    data() {
        return {
            searchTerm: '',
            searchResults: ([] as Array<String>),
            pending: false,
        }
    },

    setup() {
        const userStore = useUserStore();
        const kanbanStore = useKanbanStore();
        return { userStore, kanbanStore }
    },

    watch: {
        searchTerm(newValue) {
            if (!newValue) {
                this.searchResults = [];
            } else {
                this.pending = true;
                fetch(API_URL + '/api/user/get.php?startswith=' + this.searchTerm, { credentials: 'include' })
                    .then(res => {
                        return res.json().then(json => {
                            return res.ok ? json : Promise.reject(json);
                        })
                    }).then(json => {
                        this.searchResults = json.records;
                        this.pending = false;
                    })
                    .catch(err => {
                        this.searchResults = [];
                        console.log(err.message);
                    })
            }
        }
    },

    methods: {
        addUser(user: string) {
            this.kanbanStore.authorized_users.push(user);
            this.searchTerm = '';
        },

        removeUser(user: string) {
            this.kanbanStore.authorized_users.splice(this.kanbanStore.authorized_users.indexOf(user), 1);
            this.kanbanStore.unauthorized_users.push(user);
        },

        save() {
            this.searchTerm = '';
            this.$emit('save');
        }
    },

    emits: ['save'],
})
</script>

<template>
    <!-- Boite modale -->
    <div class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Uilisateurs ajoutés</h5>

                    <button ref="closeModal" @click="save()" type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <!-- Corps de la boite modale -->
                <div class="modal-body">

                    <!-- Formulaire de tache -->
                    <div class="px-2 mb-2" v-if="isOwner">
                        <form @submit.prevent="" id="authUsersForm">
                            <label for="search">
                                Chercher le nom d'un utilisateur
                            </label>

                            <input type="text" class="form-control" id="search" placeholder="Taper ici..."
                                v-model="searchTerm" autocomplete="off">
                        </form>
                        <ul class="list-group mt-1">
                            <template v-for="user in searchResults">
                                <li v-if="!kanbanStore.authorized_users.includes(user)" class="list-group-item"
                                    role="button" @click="addUser(user as string)">{{ user }}</li>
                            </template>
                        </ul>
                    </div>

                    <div class="d-flex mt-2">
                        <span v-for="user in kanbanStore.authorized_users"
                            class="badge text-bg-primary rounded-pill p-2 fs-6 mx-1">
                            {{ user }}
                            <svg v-if="isOwner" xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                fill="currentColor" class="bi bi-x-circle-fill align-baseline ms-1" viewBox="0 0 16 16"
                                role="button" @click="removeUser(user as string)">
                                <path
                                    d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z" />
                            </svg>
                        </span>
                    </div>

                    <!-- Formulaire de tache -->

                </div>
                <div class="modal-footer d-flex" v-if="isOwner">
                    <button type="submit" form="authUsersForm" class="btn btn-primary" @click="save()"
                        data-bs-dismiss="modal">Enregistrer</button>
                </div>

            </div>
        </div>
    </div>
    <!-- Boite modale -->
</template>
