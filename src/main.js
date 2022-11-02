import { createApp } from 'vue'
import App from './App.vue'

import { createPinia } from "pinia";
const pinia = createPinia();

import './assets/main.css'

createApp(App).use(pinia).mount('#app')
