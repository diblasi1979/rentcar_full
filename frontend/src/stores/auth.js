import { defineStore } from 'pinia';
import api from '../api/axios';
import { canAccessPermission, canManagePermission, canDeletePermission } from '../lib/permissions';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        token: localStorage.getItem('token')
    }),

    getters: {
        role: (state) => state.user?.role || null,
        isAdmin: (state) => state.user?.role === 'admin',
        isAuthenticated: (state) => Boolean(state.token),
        canAccess: (state) => (permission) => canAccessPermission(state.user?.role || null, permission),
        canManage: (state) => (permission) => canManagePermission(state.user?.role || null, permission),
        canDelete: (state) => (permission) => canDeletePermission(state.user?.role || null, permission),
    },

    actions: {

        async login(data) {
            const res = await api.post('/login', data);

            this.token = res.data.token;
            localStorage.setItem('token', this.token);

            await this.fetchUser();
        },

        async fetchUser() {
            const res = await api.get('/me');
            this.user = res.data;
            return this.user;
        },

        async ensureUserLoaded() {
            if (!this.token) {
                return null;
            }

            if (this.user) {
                return this.user;
            }

            try {
                return await this.fetchUser();
            } catch (error) {
                this.logout();
                throw error;
            }
        },

        logout() {
            localStorage.removeItem('token');
            this.token = null;
            this.user = null;
        }
    }
});