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
            this.userStore.logOut();
        }
    }
})
</script>

<template>
    <button type="button" class="btn btn-outline-danger " @click.prevent="logOut">Se déconnecter</button>
</template>