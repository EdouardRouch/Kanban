<script>
  import { useUserStore } from './store/user';
  import LogIn from './components/LogIn.vue';
  import SignUp from './components/SignUp.vue';
  import LogOut from './components/LogOut.vue';
  export default {
    setup() {
      const userStore = useUserStore();
      return { userStore }
    },
    data() {
      return {
      }
    },
    components: {
      LogIn,
      SignUp,
      LogOut
    },
    computed: {
      userLoggedIn() {
        return this.userStore.user != '';
      }
    },
    created() {
      this.userStore.isLoggedIn();
    }
  }
</script>


<template>
    <LogIn v-if="!userLoggedIn" />
    <SignUp v-if="!userLoggedIn" />
    <LogOut v-if="userLoggedIn" />
</template>