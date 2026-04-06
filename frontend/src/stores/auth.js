import { defineStore } from 'pinia'
import axios, { getAuthToken, setAuthToken } from '@/utils/axios.js'

export const useAuthStore = defineStore('auth', {
    state: () => ({
        token: getAuthToken() || null,
        user: (() => {
            try {
                const raw = localStorage.getItem('user')
                return raw ? JSON.parse(raw) : null
            } catch {
                return null
            }
        })(),
    }),

    getters: {
        isAuthenticated: (state) => !!state.token,
        userName: (state) => state.user?.name || '',
        userEmail: (state) => state.user?.email || '',
    },

    actions: {
        initialize() {
            this.token = getAuthToken() || null

            try {
                const rawUser = localStorage.getItem('user')
                this.user = rawUser ? JSON.parse(rawUser) : null
            } catch {
                this.user = null
            }
        },

        async login({ email, password }) {
            const response = await axios.post('/auth/login', {
                email,
                password,
            })

            const token = response.data?.token ?? null
            const user = response.data?.user ?? null

            if (!token) {
                throw new Error('No token received from server')
            }

            setAuthToken(token)

            this.token = token
            this.user = user

            if (user) {
                localStorage.setItem('user', JSON.stringify(user))
            } else {
                localStorage.removeItem('user')
            }

            return response.data
        },

        async logout() {
            try {
                await axios.post('/auth/logout')
            } catch (err) {
                // ignore logout API errors, still clear local auth state
                console.error('Logout request failed:', err)
            }

            setAuthToken(null)
            localStorage.removeItem('user')

            this.token = null
            this.user = null
        },
    },
})