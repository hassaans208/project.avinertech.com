import { defineStore } from 'pinia';
import axios from 'axios';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        token: localStorage.getItem('token') || null,
        loading: false,
        error: null
    }),

    getters: {
        isAuthenticated: (state) => !!state.user && !!state.token,
        isAdmin: (state) => state.user?.role === 'admin',
        isManager: (state) => state.user?.role === 'manager',
        isUser: (state) => state.user?.role === 'user',
        userRole: (state) => state.user?.role
    },

    actions: {
        async initialize() {
            if (this.token) {
                try {
                    await this.fetchUser();
                } catch (error) {
                    // Token is invalid or expired
                    this.logout();
                }
            }
        },

        async login(credentials) {
            this.loading = true;
            this.error = null;
            
            try {
                const response = await axios.post('/api/login', credentials);
                
                if (response.data.success) {
                    const { user, token } = response.data.data;
                    this.user = user;
                    this.token = token;
                    
                    // Save token to localStorage
                    localStorage.setItem('token', token);
                    
                    // Set the Authorization header for future requests
                    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
                    
                    return { success: true };
                }
                
                return { success: false, error: 'Login failed' };
            } catch (error) {
                this.error = error.response?.data?.message || 'Login failed. Please check your credentials.';
                
                if (error.response?.data?.errors) {
                    return { 
                        success: false, 
                        errors: error.response.data.errors 
                    };
                }
                
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        async register(userData) {
            this.loading = true;
            this.error = null;
            
            try {
                const response = await axios.post('/api/register', userData);
                
                if (response.data.success) {
                    const { user, token } = response.data.data;
                    this.user = user;
                    this.token = token;
                    
                    // Save token to localStorage
                    localStorage.setItem('token', token);
                    
                    // Set the Authorization header for future requests
                    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
                    
                    return { success: true };
                }
                
                return { success: false, error: 'Registration failed' };
            } catch (error) {
                this.error = error.response?.data?.message || 'Registration failed';
                
                if (error.response?.data?.errors) {
                    return { 
                        success: false, 
                        errors: error.response.data.errors 
                    };
                }
                
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        async fetchUser() {
            if (!this.token) return;
            
            this.loading = true;
            this.error = null;
            
            try {
                const response = await axios.get('/api/user');
                
                if (response.data.success) {
                    this.user = response.data.data;
                    return { success: true };
                }
                
                return { success: false };
            } catch (error) {
                this.error = error.response?.data?.message || 'Failed to fetch user data';
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },

        async logout() {
            this.loading = true;
            
            try {
                if (this.token) {
                    // Only call the API if we have a token
                    await axios.post('/api/logout');
                }
            } catch (error) {
                console.error('Error during logout:', error);
            } finally {
                // Always clear local state even if API call fails
                this.user = null;
                this.token = null;
                this.error = null;
                
                // Remove token from localStorage
                localStorage.removeItem('token');
                
                // Remove Authorization header
                delete axios.defaults.headers.common['Authorization'];
                
                this.loading = false;
            }
        }
    }
}); 