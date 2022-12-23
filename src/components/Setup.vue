<script lang="ts">
import { defineComponent } from 'vue';

const API_URL = import.meta.env.VITE_API_URL;

export default defineComponent({
    data() {
        return {
            status: '',
            config: {
                db_host: '',
                db_name: '',
                db_user: '',
                db_password: '',
            },
            pass_verify: '',
        }
    },

    methods: {
        async setup() {
            if (this.pass_verify === this.config.db_password) {
                this.status = 'Chargement...';
                const res = await fetch(API_URL + '/api/config/setup.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': "application/json; charset=UTF-8",
                    },
                    body: JSON.stringify(this.config),
                });
                this.status = await res.text();
            } else {
                this.status = 'Les mots de passe ne correspondent pas';
            }
        }
    }
})
</script>

<template>
    <div class="d-flex align-items-center bg-primary h-100 p-3 overflow-auto">
        <form @submit.prevent="setup()" class="w-md-50 m-auto p-3 bg-light rounded border shadow" id="setupForm">
            <h2>Configuration de la base de données</h2>
            <div class="mb-3">
                <label for="db_host" class="form-label">Adresse de la base de données :</label>
                <input type="text" v-model="config.db_host" class="form-control" id="db_host" required>
            </div>
            <div class="mb-3">
                <label for="db_name" class="form-label">Nom de la base de données :</label>
                <input type="text" v-model="config.db_name" class="form-control" id="db_name" required>
            </div>
            <div class="mb-3">
                <label for="db_user" class="form-label">Utilisateur de la base de données :</label>
                <input type="text" v-model="config.db_user" class="form-control" id="db_user" required>
            </div>
            <div class="mb-3">
                <label for="db_password" class="form-label">Mot de passe de la base de données :</label>
                <input type="password" v-model="config.db_password" class="form-control" id="db_password" required>
            </div>
            <div class="mb-3">
                <label for="pass_verify" class="form-label">Vérifiez le mot de passe :</label>
                <input type="password" v-model="pass_verify" class="form-control" id="pass_verify" required>
            </div>

            <button role="button" form="setupForm" class="btn btn-outline-primary mb-3">Valider</button>

            <div class="mt-3">
                <label for="status" class="form-label">Statut :</label> <br>
                <input type="text" name="status" id="status" v-model="status" class="form-control" disabled="true">
            </div>
        </form>
    </div>
</template>