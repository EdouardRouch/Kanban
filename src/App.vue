<script lang="ts">
import { useUserStore } from '@/stores/user';
import NavBar from '@/components/NavBar.vue';
import { defineComponent } from 'vue';

export default defineComponent({
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
})
</script>


<template>
  <NavBar />
</template>