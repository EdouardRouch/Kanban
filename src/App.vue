<script>
  import Kanban from './components/Kanban.vue';
  export default {
    data() {
      return {
        count: 2,
        display: true,
        colors: [{id:1, value:'red'}, 'blue', 'green', 'cyan'],
        cliqued: ''
      }
    },
    methods: {
      increment() {
        this.count++;
      }
    },
    mounted() {
      this.increment();
    },
    computed: {
      canDisplay() {
        return this.count > 10;
      }
    },
    components: {
      Kanban
    }
  }
</script>


<template>
  <Kanban :can-display="true" @cliqued="(n) => cliqued = n"/>

</template>