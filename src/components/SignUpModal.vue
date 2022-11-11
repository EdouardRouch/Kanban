<script lang="ts">
import { useUserStore } from "@/stores/user"
import { defineComponent } from "vue";

export default defineComponent({
    setup() {
        const userStore = useUserStore();
        return { userStore };
    },
    data() {
        return {
            username: '',
            password: '',
            password_verify: '',
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
            ].join('');

            alertPlaceholder.append(wrapper);
        },
        signUp(): void {
            this.status = 'pending';
            this.userStore.signUp(this.username,
                this.password,
                this.password_verify)
                .then(() => (this.$refs.close as HTMLElement).click(), (err) => this.alert(err.message, "danger"))
                .then(() => this.status = '');
        },
        clearForm(): void {
            this.username = '';
            this.password = '';
            this.password_verify = '';
            this.validation = false;
            (this.$refs.alertPlaceholder as HTMLElement).innerHTML = '';
        }
    }
})
</script>

<template>
    <div class="modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">S'inscrire</h5>
                    <button ref="close" @click="clearForm()" type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div class="mb-5 ps-2 pe-2">
                        <div ref="alertPlaceholder"></div>
                        <form :class="{ 'was-validated': validation }" @submit.prevent="signUp">
                            <div class="form-floating mb-2">
                                <input maxlength="20" type="text" class="form-control" name="username" id="username"
                                    v-model="username" placeholder="" required>
                                <label class="form-label" for="username">Nom d'utilisateur</label>
                            </div>

                            <div class="form-floating mb-2">
                                <input type="password" class="form-control" name="password" id="password"
                                    v-model="password" placeholder="" required>
                                <label class="form-label" for="password">Mot de passe</label>
                            </div>

                            <div class="form-floating mb-2">
                                <input type="password" class="form-control" name="password_verify" id="password_verify"
                                    v-model="password_verify" placeholder="" required>
                                <label class="form-label" for="password_verify">Vérifiez votre mot de passe</label>
                            </div>

                            <button @click="validation = true" type="submit" class="btn btn-outline-primary w-100">
                                <div v-if="status == 'pending'" class="spinner-border" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <span v-if="status == ''">S'inscrire</span>
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>