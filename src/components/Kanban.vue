<script>
    export default {
        data() {
            return {
                name : 'Edouard'
            }
        },
        props: {
            canDisplay: Boolean
        },
        methods: {
            displayName() {
                console.log(this.name);
            },
            connect() {
                fetch('jdbz', {
                    username: this.name,
                })

            }
        }
    }   
</script>

<template>
    <form action="" @submit.prevent>
        <label for="name">Entrez votre nom:</label> <br>
        <input type="text" name="name" id="name" v-model="name" @input="displayName">
    </form>
</template>

<style>

</style>