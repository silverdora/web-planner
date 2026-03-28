import { createRouter, createWebHistory } from 'vue-router'
import { getAuthToken } from '@/utils/axios.js'

import HomePage from '../components/pages/HomePage/HomePage.vue'
import LoginPage from '../components/pages/LoginPage/LoginPage.vue'
import RegistrationPage from '../components/pages/RegistrationPage/RegistrationPage.vue'
import DashboardPage from '../components/pages/DashboardPage/DashboardPage.vue'

const routes = [
    {
        path: '/',
        name: 'home',
        component: HomePage,
    },
    {
        path: '/login',
        name: 'login',
        component: LoginPage,
    },
    {
        path: '/register',
        name: 'register',
        component: RegistrationPage,
    },
    {
        path: '/dashboard',
        name: 'dashboard',
        component: DashboardPage,
        meta: { requiresAuth: true },
    },
    {
        path: '/tasks',
        name: 'tasks',
        meta: { requiresAuth: true },
        component: {
            template: '<div class="p-8 text-2xl font-bold">Tasks page</div>',
        },
    },
    {
        path: '/tasks/create',
        name: 'task-create',
        meta: { requiresAuth: true },
        component: {
            template: '<div class="p-8 text-2xl font-bold">Create task page</div>',
        },
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

router.beforeEach((to, from, next) => {
    const isLoggedIn = !!getAuthToken()
    const requiresAuth = to.matched.some(record => record.meta.requiresAuth)

    if (requiresAuth && !isLoggedIn) {
        next('/')
        return
    }

    next()
})

export default router