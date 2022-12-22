<script lang="ts">
import NavBar from './navbar/NavBar.vue';
import { defineComponent } from 'vue';

export default defineComponent({
    components: {
        NavBar,
    },
    data() {
        return {
            kanbans: {
                public: [{
                    id: 0,
                    title: 'Premier Kanban',
                    public: true,
                    owner: '',
                    creation_date: '2022-12-17'
                }]
            },
            pending: false
        }
    },
    created() {
        this.kanbans.public = [];
        this.pending = true;
        fetch("/api/kanban/get.php")
            .then(res => {
                return res.json().then(json => {
                    return res.ok ? json : Promise.reject(json);
                })
            }).then(json => { this.kanbans = json.records })
            .catch(err => { console.log(err.message) })
            .then(res => this.pending = false);
    }
})
</script>

<template>
    <NavBar />
    <div class="w-100 text-center text-bg-primary py-5 px-4">
        <h1 class="py-5 rounded m-3">Bienvenue sur Kabane</h1>
        <h5>Connectez-vous ou créez un compte pour gérer vos kanbans et vos tâches.</h5>
        <h6>N'hésitez pas regarder le code source de cette application sur <a class="link-light" target="_blank"
                href="https://github.com/EdouardRouch/kanban">GitHub</a>
        </h6>
    </div>
    <div class="w-75 container p-5">
        <h1 class="text-primary mb-5 text-center">Kanbans publics</h1>
        <div class="row g-5">
            <div class="col-md-3" v-for="kanban in kanbans.public" :key="kanban.id">
                <router-link :to="'/kanban/' + kanban.id"
                    class="position-relative text-break m-auto py-4 px-2 border rounded shadow-sm d-flex align-items-center justify-content-center text-center h-100">
                    <h5 class="my-4">{{ kanban.title }}</h5>
                    <div class="position-absolute bottom-0 w-100 p-1 mt-5">
                        <span class="badge text-bg-primary"> {{ kanban.creation_date }} </span>
                    </div>
                </router-link>
            </div>
        </div>
    </div>
</template>

<style>
.col-3 {
    height: 150px;
}
</style>