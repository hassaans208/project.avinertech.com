import { createRouter, createWebHistory } from 'vue-router';
import UserList from '../pages/UserList.vue';
import Login from '../pages/Login.vue';
import Register from '../pages/Register.vue';
import { useAuthStore } from '../store/auth';

const routes = [
    {
        path: '/',
        name: 'home',
        component: UserList,
        meta: { 
            requiresAuth: true,
            roles: ['admin', 'manager', 'user']
        }
    },
    {
        path: '/users',
        name: 'users',
        component: UserList,
        meta: { 
            requiresAuth: true,
            roles: ['admin', 'manager']
        }
    },
    {
        path: '/login',
        name: 'login',
        component: Login,
        meta: { 
            guest: true 
        }
    },
    {
        path: '/register',
        name: 'register',
        component: Register,
        meta: { 
            guest: true 
        }
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

// Navigation guards
router.beforeEach((to, from, next) => {
    const authStore = useAuthStore();
    const requiresAuth = to.matched.some(record => record.meta.requiresAuth);
    const isGuest = to.matched.some(record => record.meta.guest);
    const userRole = authStore.userRole;
    
    // Check if the route requires specific roles
    const requiredRoles = to.meta.roles || [];
    
    // If route requires auth and user is not authenticated
    if (requiresAuth && !authStore.isAuthenticated) {
        next('/login');
    } 
    // If route is for guests only and user is authenticated
    else if (isGuest && authStore.isAuthenticated) {
        next('/');
    } 
    // If route requires specific roles and user doesn't have any of them
    else if (requiresAuth && requiredRoles.length > 0 && !requiredRoles.includes(userRole)) {
        next('/'); // Redirect to home or access denied page
    } 
    // Otherwise proceed as normal
    else {
        next();
    }
});

export default router; 