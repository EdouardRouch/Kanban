<script lang="ts">
import { useUserStore } from '@/stores/user';
import { defineComponent } from 'vue';
import LogInModal from './LogInModal.vue';
import SignUpModal from './SignUpModal.vue';
import LogOut from './LogOut.vue';

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
    },
})
</script>

<template>
    <nav class="navbar navbar-expand-md bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">NavBar</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Accueil</a>
                    </li>
                </ul>

                <div v-if="!userLoggedIn" class="d-flex ms-auto">
                    <button type="button" class="btn btn-primary me-2 w-100 w-md-auto" data-bs-toggle="modal"
                        data-bs-target="#logInModal">Se
                        connecter</button>

                    <button type="button" class="btn btn-primary w-100 w-md-auto" data-bs-toggle="modal"
                        data-bs-target="#signUpModal">S'inscrire</button>
                </div>

                <LogInModal id="logInModal" />
                <SignUpModal id="signUpModal" />
                <div v-if="userLoggedIn">
                    <LogOut />
                </div>
            </div>
        </div>
    </nav>
</template>