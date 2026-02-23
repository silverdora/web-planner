import './assets/main.css'

import { createApp } from 'vue'
import App from './App.vue'


const routes = [
    { path: '/', component: Home },
    { path: '/about', component: About },
    ];

    

createApp(App).mount('#app')
