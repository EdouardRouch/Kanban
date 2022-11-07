<script lang="ts">
import { useUserStore } from '@/stores/user';
import { defineComponent } from 'vue';

export default defineComponent({
    setup() {
        const userStore = useUserStore();
        return { userStore };
    },
    data() {
        return {
            username: '',
            password: '',
            validation: false,
            status: '',
        }
    },
    methods: {
        alert(message: string, type: string): void {
            const alertPlaceholder = this.$refs.alertPlaceholder as HTMLElement;
            const wrapper = document.createElement('div')
            wrapper.innerHTML = [
                `<div class="alert alert-${type} alert-dismissible" role="alert">`,
                `   <div>${message}</div>`,
                '   <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>',
                '</div>'
            ].join('')

            alertPlaceholder.append(wrapper)
        },
        logIn(): void {
            this.status = 'pending';
            this.userStore.logIn(this.username, this.password)
                .then(() => (this.$refs.closeModal as HTMLElement).click(), (err) => this.alert(err.message, "danger"))
                .then(() => this.status = '');
        },
        clearForm(): void {
            this.username = '';
            this.password = '';
            this.validation = false;
            (this.$refs.alertPlaceholder as HTMLElement).innerHTML = '';
        }
    }
})
</script>

<template>
    <!-- Boite modale -->
    <div class="modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Se connecter</h5>
                    <button ref="closeModal" @click="clearForm()" type="button" class="btn-close"
                        data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Corps de la boite modale -->
                <div class="modal-body">

                    <!-- Formulaire de connexion -->
                    <div class="ps-2 pe-2 mb-5">
                        <div ref="alertPlaceholder" id="alertPlaceholder"></div>
                        <form :class="{ 'was-validated': validation }" @submit.prevent="logIn()">

                            <div class="form-floating mb-2">
                                <input type="text" class="form-control" name="username" id="username" v-model="username"
                                    placeholder="" required>
                                <label for="username" class="form-label">Nom d'utilisateur</label>
                            </div>

                            <div class="form-floating mb-2">
                                <input type="password" class="form-control" name="password" id="password"
                                    v-model="password" placeholder="" required>
                                <label for="password" class="form-label">Mot de passe</label>
                            </div>
                            <button @click="validation = true" type="submit" class="btn btn-outline-primary w-100">
                                <div v-if="status == 'pending'" class="spinner-border" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <span v-if="status == ''">Se connecter</span>
                            </button>
                        </form>
                    </div>
                    <!-- Formulaire de connexion -->

                </div>
            </div>
        </div>
    </div>
    <!-- Boite modale -->
</template>