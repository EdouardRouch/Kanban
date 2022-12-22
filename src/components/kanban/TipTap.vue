<script lang="ts">
import { defineComponent } from 'vue'
import { BubbleMenu, Editor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'

export default defineComponent({
    components: {
        EditorContent,
        BubbleMenu,
    },

    data() {
        return {
            editor: null as any,
        }
    },

    props: {
        modelValue: {
            type: String,
            default: '',
        },
        editable: {
            type: Boolean,
            default: true,
        }
    },

    emits: ['update:modelValue'],

    watch: {
        modelValue(value) {
            const isSame = this.editor.getHTML() === value

            if (isSame) {
                return
            }

            this.editor.commands.setContent(value, false);
        },

        editable(newValue) {
            this.editor.setEditable(newValue);
        },
    },

    mounted() {
        this.editor = new Editor({
            extensions: [
                StarterKit,
            ],
            content: this.modelValue,
            onUpdate: () => {
                this.$emit('update:modelValue', this.editor.getHTML());
            },
        });
    },

    beforeUnmount() {
        this.editor.destroy();
    },

})
</script>

<template>
    <div :class="{ 'bg-secondary bg-opacity-10': !editable }">
        <BubbleMenu :editor="editor" :tippy-options="{ duration: 100, placement: 'bottom' }" v-if="editor"
            class="bg-white rounded">
            <button @click.prevent="editor.chain().focus().toggleBold().run()"
                :class="{ 'active': editor.isActive('bold') }" class="btn btn-outline-primary p-1 me-1">
                gras
            </button>
            <button @click.prevent="editor.chain().focus().toggleItalic().run()"
                :class="{ 'active': editor.isActive('italic') }" class="btn btn-outline-primary p-1 me-1">
                italique
            </button>
            <button @click.prevent="editor.chain().focus().toggleStrike().run()"
                :class="{ 'active': editor.isActive('strike') }" class="btn btn-outline-primary p-1">
                barré
            </button>
        </BubbleMenu>
        <EditorContent :editor="editor" />
    </div>
</template>

<style>
.ProseMirror {
    height: 200px;
    overflow: scroll;
}

.ProseMirror:focus {
    outline: none;
}
</style>