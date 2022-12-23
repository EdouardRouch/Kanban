<script lang="ts">
import { defineComponent } from 'vue';
import { useUserStore } from '@/stores/user';
import draggable from 'vuedraggable';

const API_URL = import.meta.env.VITE_API_URL;

export default defineComponent({
    components: {
        draggable,
    },
    setup() {
        const userStore = useUserStore();
        return { userStore };
    },
    data() {
        return {
            title: '',
            public: false,
            validation: false,
            pending: false,
            newColumnTitle: '',
            columnList: ([] as any),
            drag: false,
        }
    },
    methods: {
        alert(message: string, type: string): void {
            const alertPlaceholder = this.$refs.alertPlaceholder as HTMLElement;
            const wrapper = document.createElement('div')
            wrapper.innerHTML = [
                `<div class="alert alert-${type} alert-dismissible" role="alert">`,
                `   <div>${message}</div>`,
                '   <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>',
                '</div>'
            ].join('')

            alertPlaceholder.append(wrapper)
        },
        clearForm(): void {
            this.title = '';
            this.public = false;
            this.validation = false;
            (this.$refs.alertPlaceholder as HTMLElement).innerHTML = '';
            this.columnList = [];
            this.newColumnTitle = '';
        },
        create() {
            const body = {
                title: this.title,
                public: this.public,
                owner: this.userStore.user,
                columns: this.columnList
            };
            fetch(API_URL + "/api/kanban/post.php", {
                method: "POST",
                headers: {
                    'Content-Type': "application/json; charset=UTF-8",
                },
                credentials: 'include',
                body: JSON.stringify(body),
            }).then(res => {
                return res.json().then(json => {
                    return res.ok ? json : Promise.reject(json);
                })
            }).then(json => {
                this.alert(json.message, "success");
                (this.$refs.closeModal as any).click();
                this.$router.push('/kanban/' + json.records.kanban_id);
            })
                .catch(err => { this.alert(err.message, "danger") });
        },
        addColumn() {
            if (this.newColumnTitle) {
                let newColumn = { title: this.newColumnTitle };
                this.columnList.push(newColumn);
                this.newColumnTitle = '';
            }
        },
        removeColumn(column: any) {
            this.columnList.splice(this.columnList.indexOf(column), 1);
        }
    },
})
</script>

<template>
    <!-- Boite modale -->
    <div class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Créer un Kanban</h5>
                    <button ref="closeModal" @click="clearForm()" type="button" class="btn-close"
                        data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Corps de la boite modale -->
                <div class="modal-body">

                    <!-- Formulaire de création de Kanban -->
                    <div class="ps-2 pe-2 mb-2">
                        <div ref="alertPlaceholder" id="alertPlaceholder"></div>
                        <form :class="{ 'was-validated': validation }" @submit.prevent="create()" id="createForm">

                            <div class="form-floating mb-2 mt-2">
                                <input type="text" class="form-control" name="username" id="username" v-model="title"
                                    placeholder="" required>
                                <label for="username" class="form-label">Titre du Kanban</label>
                            </div>

                            <div class="form-check form-switch mt-4 w-5">
                                <label class="form-check-label" for="flexSwitchCheckDefault">Public</label>
                                <input class="form-check-input" type="checkbox" role="switch"
                                    id="flexSwitchCheckDefault" v-model="public" aria-describedby="publicHelp">
                            </div>
                            <div id="publicHelp" class="form-text">Un Kanban public peut être vu par tout le monde.
                            </div>

                            <div class="my-4">
                                <h5>Colonnes :</h5>
                                <draggable v-model="columnList" item-key="title" class="list-group" @start="drag = true"
                                    @end="drag = false" ghost-class="ghost">
                                    <template #item="{ element }">
                                        <div class="d-flex justify-content-between list-group-item" role="button">
                                            <div class="align-self-center">{{ element.title }}</div>
                                            <button class="btn btn-outline-danger d-flex align-items-center p-2"
                                                @click.prevent="removeColumn(element)" type="button">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                                    <path
                                                        d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854Z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                    <template #footer>
                                        <div class="d-flex list-group-item" role="group">
                                            <input type="text" class="form-control flex-grow-1" name="newRowTitle"
                                                v-model="newColumnTitle" placeholder="Titre de la colonne"
                                                aria-label="Titre de la colonne" maxlength="20"
                                                on-keyup.enter="addColumn()">
                                            <button class="btn btn-outline-secondary ms-2"
                                                @click.prevent="addColumn()">Ajouter</button>
                                        </div>
                                    </template>
                                </draggable>
                            </div>
                        </form>
                    </div>
                    <!-- Formulaire de création de Kanban -->

                </div>
                <div class="modal-footer">
                    <button @click="validation = true" type="submit" form="createForm"
                        class="btn btn-outline-primary w-100">
                        <div v-if="pending" class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <span v-if="!pending">
                            Créer
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Boite modale -->
</template>

<style>
.ghost {
    opacity: 0.5;
    background: #c8ebfb;
}
</style>