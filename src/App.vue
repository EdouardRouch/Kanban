<script>
import { useUserStore } from './store/user';
import NavBar from './components/NavBar.vue';
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
    NavBar
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
  <NavBar />
</template>