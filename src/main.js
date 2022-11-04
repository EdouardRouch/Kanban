import { createApp } from 'vue'
import App from './App.vue'
// import './assets/main.css'
import "bootstrap/dist/css/bootstrap.min.css"
import "./assets/custom.scss"

import { createPinia } from "pinia";
const pinia = createPinia();

// import VueRouter from 'vue-router';
// const routes = [

// ];
// const router = VueRouter.createRouter({
//     history: VueRouter.createWebHashHistory(),
//     routes: routes,
// });


createApp(App).use(pinia).mount('#app')

import "bootstrap/dist/js/bootstrap.js"
