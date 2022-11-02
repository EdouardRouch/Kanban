<script>
    import { useUserStore } from '../store/user';
    export default {
        setup() {
            const userStore = useUserStore();
            return { userStore };
        },
        data() {
            return {
                username: '',
                password: ''
            }
        },
        methods: {
            logIn() {
                this.userStore.logIn(this.username, this.password)
                .then()
                .catch(err => alert(err.message));
            }
        }
    }
</script>

<template>
    <form @submit.prevent="logIn">
    <label for="username">Nom d'utilisateur :</label> <br>
    <input type="text" name="username" id="username" v-model="username" required> <br>

    <label for="password">Mot de passe :</label> <br>
    <input type="password" name="password" id="password" v-model="password" required>  <br>

    <button>Se connecter</button>
  </form>
</template>