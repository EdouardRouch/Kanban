<script>
    import { useUserStore } from '../store/user';
    export default {
        setup() {
            const userStore = useUserStore();
            return { userStore };
        },
        methods: {
            logOut() {
                this.userStore.logOut();
            }
        }
    }
</script>

<template>
    <button @click.prevent="logOut">Se déconnecter</button>
</template>