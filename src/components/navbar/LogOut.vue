<script lang="ts">
import { useUserStore } from '@/stores/user';
import { defineComponent } from 'vue';

export default defineComponent({
    setup() {
        const userStore = useUserStore();
        return { userStore };
    },
    methods: {
        logOut(): void {
            this.userStore.logOut().then(res => this.$router.go(0));
        }
    }
})
</script>

<template>
    <button type="button" class="btn btn-outline-danger w-100 w-md-auto mt-2 mt-md-0" @click.prevent="logOut">Se
        déconnecter</button>
</template>