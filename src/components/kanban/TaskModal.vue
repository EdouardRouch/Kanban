<script lang="ts">
import { defineComponent } from 'vue';
import TipTap from './TipTap.vue';
import { useUserStore } from '@/stores/user';
import { useKanbanStore } from '@/stores/kanban';

export default defineComponent({
    props: {
        create: {
            type: Boolean,
            default: false,
        },
        modelValue: {
            type: Object,
            default: {
                id: 0,
                title: '',
                description: '',
                assigned_user: '',
                creation_date: '',
                deadline: '',
                column_id: 0,
                kanban_id: 0,
            }
        },
        isOwner: {
            type: Boolean,
            default: false,
        }
    },

    emits: ['update:modelValue', 'modify', 'create', 'delete'],

    components: {
        TipTap,
    },

    methods: {
        save() {
            if (this.create) {
                this.$emit('create');
            } else {
                this.$emit('modify');
            }

            (this.$refs.closeModal as any).click();
        }
    },

    setup() {
        const userStore = useUserStore();
        const kanbanStore = useKanbanStore();
        return { userStore, kanbanStore };
    },

    computed: {
        canDelete() {
            return (!this.create && this.userStore.user === this.kanbanStore.kanban.owner);
        },

        isEditable() {
            let is_assigned = (this.modelValue.assigned_user !== '' &&
                this.userStore.user === this.modelValue.assigned_user);
            return is_assigned || this.isOwner;
        },

        canAssign() {
            return this.isEditable || this.kanbanStore.authorized_users.includes(this.userStore.user);
        },

        canEditAssign() {
            return this.isOwner ||
                (this.canAssign &&
                    (this.modelValue.assigned_user === '' ||
                        this.modelValue.assigned_user === this.userStore.user));
        }
    }

})
</script>

<template>
    <!-- Boite modale -->
    <div class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <input type="text" form="taskForm" class="form-control w-auto d-inline" maxlength="20"
                            aria-label="title" v-model="modelValue.title" :disabled="!isEditable">
                        <span class="badge text-bg-primary mt-2 ms-md-2 d-block d-md-inline">créée le {{
                                modelValue.creation_date
                        }}</span>
                    </div>

                    <button ref="closeModal" type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <!-- Corps de la boite modale -->
                <div class="modal-body">

                    <!-- Formulaire de tache -->
                    <div class="ps-2 pe-2 mb-2">
                        <form @submit.prevent="save()" :id="'task' + (create ? 'Create' : '') + 'Form'">
                            <div>
                                <h5 class="d-inline m-0">Deadline :</h5>
                                <input class="w-auto ms-2 d-inline form-control" type="date" name="deadline"
                                    id="deadline" v-model="modelValue.deadline" required :disabled="!isEditable">
                            </div>
                            <div class="my-3">
                                <h5>Description :</h5>
                                <TipTap v-model="modelValue.description" :editable="isEditable" class="form-control" />
                            </div>

                            <div class="my-3">
                                <h5>Utilisateur assigné :</h5>
                                <select name=" assigned_user" class="form-select" id="assigned_user"
                                    v-model="modelValue.assigned_user" aria-label="Default select example"
                                    :disabled="!canEditAssign">
                                    <option value="" selected="true">--sélectionner un utilisateur--</option>
                                    <template v-for="user in kanbanStore.authorized_users">
                                        <option
                                            v-if="isOwner || user === userStore.user || user == modelValue.assigned_user"
                                            :value="user">
                                            {{ user }}
                                        </option>
                                    </template>
                                </select>
                            </div>
                        </form>
                    </div>
                    <!-- Formulaire de tache -->

                </div>
                <div class="modal-footer d-flex">
                    <button v-if="canDelete" type="button" class="btn btn-outline-danger" data-bs-dismiss="modal"
                        @click="$emit('delete')">Supprimer</button>
                    <div class="flex-grow-1"></div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>

                    <button type="submit" :form="'task' + (create ? 'Create' : '') + 'Form'" class="btn btn-primary"
                        :disabled="!isEditable">Enregistrer</button>
                </div>

            </div>
        </div>
    </div>
    <!-- Boite modale -->
</template>
