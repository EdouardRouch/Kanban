<script lang="ts">
import { useUserStore } from '@/stores/user';
import NavBar from '@/components/navbar/NavBar.vue';
import Dashboard from '@/components/Dashboard.vue';
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
    NavBar,
    Dashboard
  },
  created() {
    this.userStore.setSessionStatus();
  }
})
</script>


<template>
  <router-view></router-view>
</template>

