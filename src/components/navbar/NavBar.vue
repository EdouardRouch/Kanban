<script lang="ts">
import { useUserStore } from '@/stores/user';
import { defineComponent } from 'vue';
import LogInModal from './LogInModal.vue';
import SignUpModal from './SignUpModal.vue';
import LogOut from './LogOut.vue';
import CreateKanbanModal from './CreateKanbanModal.vue';

export default defineComponent({
    setup() {
        const userStore = useUserStore();
        return { userStore }
    },
    data() {
        return {
        }
    },
    computed: {
        userLoggedIn(): boolean {
            return this.userStore.user != '';
        }
    },
    components: {
        LogInModal,
        SignUpModal,
        LogOut,
        CreateKanbanModal
    },
})
</script>

<template>
    <nav class="navbar navbar-expand-md bg-light border-bottom shadow-sm">
        <div class="container-fluid">
            <router-link class="navbar-brand" to="/home">Kabane</router-link>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mb-2 mb-md-0" :class="{ 'me-auto': !userStore.user }">
                    <li class="nav-item">
                        <router-link class="nav-link active" aria-current="page" to="/home">Accueil</router-link>
                    </li>
                    <li>
                        <router-link to="/dashboard" class="nav-link active" aria-current="page">Dashboard</router-link>
                    </li>
                </ul>

                <h5 v-if="userStore.user" class="m-auto text-center text-primary mb-2">Bienvenue {{ userStore.user }}
                </h5>

                <div v-if="!userLoggedIn" class="d-flex ms-auto">
                    <button type="button" class="btn btn-primary me-2 w-100 w-md-auto" data-bs-toggle="modal"
                        data-bs-target="#logInModal">Se connecter</button>

                    <button type="button" class="btn btn-primary me-2 w-100 w-md-auto" data-bs-toggle="modal"
                        data-bs-target="#signUpModal">S'inscrire</button>
                </div>

                <div v-if="userLoggedIn">
                    <button type="button" class="btn btn-primary me-2 w-100 w-md-auto" data-bs-toggle="modal"
                        data-bs-target="#createKanbanModal">
                        Créer un Kanban
                    </button>
                    <LogOut />
                </div>

                <LogInModal id="logInModal" />
                <SignUpModal id="signUpModal" />
                <CreateKanbanModal id="createKanbanModal" />
            </div>
        </div>
    </nav>
</template>