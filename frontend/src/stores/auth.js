import { defineStore } from 'pinia';
import api from '../api/axios';
import { canAccessPermission, canManagePermission, canDeletePermission } from '../lib/permissions';

function getStoredUser() {
    const rawUser = localStorage.getItem('user');

    if (!rawUser) {
        return null;
    }

    try {
        return JSON.parse(rawUser);
    } catch {
        localStorage.removeItem('user');
        return null;
    }
}

function getStoredToken() {
    return localStorage.getItem('token');
}

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: getStoredUser(),
        token: getStoredToken()
    }),

    getters: {
        sessionUser: (state) => state.user || getStoredUser(),
        sessionToken: (state) => state.token || getStoredToken(),
        role: (state) => state.user?.role || getStoredUser()?.role || null,
        isAdmin: (state) => (state.user?.role || getStoredUser()?.role || null) === 'admin',
        isAuthenticated: (state) => Boolean(state.token || getStoredToken()),
    },

    actions: {
        hydrateSession() {
            const storedToken = getStoredToken();
            const storedUser = getStoredUser();

            this.$patch({
                token: storedToken,
                user: storedUser,
            });
        },

        syncSessionFromStorage() {
            this.hydrateSession();
        },

        hasAccess(permission) {
            return canAccessPermission(this.role || getStoredUser()?.role || null, permission);
        },

        hasManage(permission) {
            return canManagePermission(this.role || getStoredUser()?.role || null, permission);
        },

        hasDelete(permission) {
            return canDeletePermission(this.role || getStoredUser()?.role || null, permission);
        },

        async login(data) {
            const res = await api.post('/login', data);

            this.token = res.data.token;
            this.user = res.data.user ?? null;
            localStorage.setItem('token', this.token);
            localStorage.setItem('user', JSON.stringify(this.user));

            if (!this.user) {
                await this.fetchUser();
            }
        },

        async fetchUser() {
            const res = await api.get('/me');
            this.user = res.data;
            this.token = this.token || getStoredToken();
            localStorage.setItem('user', JSON.stringify(this.user));
            return this.user;
        },

        async ensureUserLoaded() {
            this.hydrateSession();

            if (!this.token && getStoredToken()) {
                this.token = getStoredToken();
            }

            if (!this.user && getStoredUser()) {
                this.user = getStoredUser();
            }

            if (!this.token && !getStoredToken()) {
                return null;
            }

            if (this.user || getStoredUser()) {
                return this.user || getStoredUser();
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
            localStorage.removeItem('user');
            this.token = null;
            this.user = null;
        }
    }
});