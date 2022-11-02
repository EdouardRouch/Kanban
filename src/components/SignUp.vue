<script>
    import { useUserStore } from "../store/user"
    export default {
        setup() {
            const userStore = useUserStore();
            return { userStore };
        },
        data() {
            return {
                username: '',
                password: '',
                password_verify: ''
            }
        },
        methods: {
            signUp() {
                this.userStore.signUp( this.username,
                                       this.password,
                                       this.password_verify ) 
                .then(res => alert(res.message))
                .catch(err => alert(err.message));;
            }
        }
    }
</script>

<template>
    <form @submit.prevent="signUp">
    <label for="username">Nom d'utilisateur :</label> <br>
    <input type="text" name="username" id="username" v-model="username" required> <br>

    <label for="password">Mot de passe :</label> <br>
    <input type="password" name="password" id="password" v-model="password" required>  <br>

    <label for="password_verify">Vérifiez votre mot de passe :</label> <br>
    <input type="password" name="password_verify" id="password_verify" v-model="password_verify" required> <br>

    <button>S'inscrire</button>
  </form> 
</template>