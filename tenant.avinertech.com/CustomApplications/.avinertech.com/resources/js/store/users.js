import { defineStore } from 'pinia';
import axios from 'axios';

export const useUserStore = defineStore('users', {
    state: () => ({
        users: [],
        user: null,
        loading: false,
        error: null,
        pagination: {
            currentPage: 1,
            perPage: 10,
            total: 0
        },
        sortBy: 'name',
        sortDirection: 'asc'
    }),
    
    getters: {
        getUserById: (state) => (id) => {
            return state.users.find(user => user.id === id);
        }
    },
    
    actions: {
        async fetchUsers() {
            this.loading = true;
            try {
                const response = await axios.get('/api/users', {
                    params: {
                        per_page: this.pagination.perPage,
                        sort_by: this.sortBy,
                        sort_direction: this.sortDirection,
                        page: this.pagination.currentPage
                    }
                });
                
                if (response.data.success) {
                    this.users = response.data.data.data;
                    this.pagination = {
                        currentPage: response.data.data.current_page,
                        perPage: response.data.data.per_page,
                        total: response.data.data.total
                    };
                }
                this.error = null;
            } catch (error) {
                this.error = error.response?.data?.message || 'Failed to fetch users';
            } finally {
                this.loading = false;
            }
        },
        
        async createUser(userData) {
            this.loading = true;
            try {
                const response = await axios.post('/api/users', userData);
                
                if (response.data.success) {
                    // Refresh the list after creating
                    await this.fetchUsers();
                }
                return { success: true, data: response.data.data };
            } catch (error) {
                this.error = error.response?.data?.message || 'Failed to create user';
                return { 
                    success: false, 
                    errors: error.response?.data?.errors || { message: this.error } 
                };
            } finally {
                this.loading = false;
            }
        },
        
        async updateUser(id, userData) {
            this.loading = true;
            try {
                const response = await axios.put(`/api/users/${id}`, userData);
                
                if (response.data.success) {
                    // Refresh the list after updating
                    await this.fetchUsers();
                }
                return { success: true, data: response.data.data };
            } catch (error) {
                this.error = error.response?.data?.message || 'Failed to update user';
                return { 
                    success: false, 
                    errors: error.response?.data?.errors || { message: this.error } 
                };
            } finally {
                this.loading = false;
            }
        },
        
        async deleteUser(id) {
            this.loading = true;
            try {
                const response = await axios.delete(`/api/users/${id}`);
                
                if (response.data.success) {
                    // Refresh the list after deleting
                    await this.fetchUsers();
                }
                return { success: true };
            } catch (error) {
                this.error = error.response?.data?.message || 'Failed to delete user';
                return { success: false, error: this.error };
            } finally {
                this.loading = false;
            }
        },
        
        setSort(column) {
            // If clicking the same column, toggle direction
            if (this.sortBy === column) {
                this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortBy = column;
                this.sortDirection = 'asc';
            }
            
            // Fetch with new sorting
            this.fetchUsers();
        },
        
        setPage(page) {
            this.pagination.currentPage = page;
            this.fetchUsers();
        }
    }
}); 