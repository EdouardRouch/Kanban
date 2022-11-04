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
    <button type="button" class="btn btn-outline-danger " @click.prevent="logOut">Se déconnecter</button>
</template>