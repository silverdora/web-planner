import { createRouter, createWebHistory } from 'vue-router'
import HomePage from '../components/pages/HomePage/HomePage.vue'

const routes = [
    {
        path: '/',
        name: 'home',
        component: HomePage,
    },
    {
        path: '/tasks',
        name: 'tasks',
        component: {
            template: '<h1>Tasks page</h1>',
        },
    },
    {
        path: '/tasks/create',
        name: 'task-create',
        component: {
            template: '<h1>Create task page</h1>',
        },
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

export default router