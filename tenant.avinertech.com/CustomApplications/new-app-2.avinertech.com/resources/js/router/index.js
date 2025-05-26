import { createRouter, createWebHistory } from 'vue-router';
import DashboardLayout from '../pages/setup/DashboardLayout.vue';
import Dashboard from '../pages/setup/Dashboard.vue';
import SetupSystem from '../pages/setup/SetupSystem.vue';
import Login from '../pages/Login.vue';
import Register from '../pages/Register.vue';
import DatabaseConfig from '../pages/setup/DatabaseConfig.vue';
import SchemaSetup from '../pages/setup/SchemaSetup.vue';
import ConfigurationList from '../pages/Configuration/ConfigurationList.vue';
import Plans from '../pages/setup/Plans.vue';
import AppBuilder from '../pages/setup/AppBuilder.vue';
import Documentation from '../pages/setup/Documentation.vue';
import ClientRoutes from '../pages/setup/ClientRoutes.vue';
import LandingPage from '../pages/client/LandingPage.vue';
import { useAuthStore } from '../store/auth';

const routes = [
    {
        path: '/',
        component: DashboardLayout,
        meta: { requiresAuth: true },
        children: [
            // {
            //     path: '',
            //     redirect: 'setup/dashboard'
            // },
            {
                path: 'setup/dashboard',
                name: 'dashboard',
                component: Dashboard,
                meta: {
                    requiresAuth: true,
                    roles: ['admin', 'manager', 'user']
                }
            },
            {
                path: 'setup',
                name: 'setup',
                component: SetupSystem,
                meta: {
                    requiresAuth: true,
                    roles: ['admin']
                }
            },
            {
                path: 'setup/database',
                component: DatabaseConfig,
                meta: {
                    requiresAuth: true,
                    roles: ['admin']
                }
            },
            {
                path: 'setup/schema',
                component: SchemaSetup,
                meta: {
                    requiresAuth: true,
                    roles: ['admin']
                }
            },
            {
                path: 'setup/configuration',
                component: ConfigurationList,
                meta: {
                    requiresAuth: true,
                    roles: ['admin']
                }
            },
            {
                path: 'setup/plans',
                name: 'plans',
                component: Plans,
        meta: { 
            requiresAuth: true,
            roles: ['admin', 'manager', 'user']
        }
    },
    {
                path: 'setup/app-builder',
                name: 'app-builder',
                component: AppBuilder,
        meta: { 
            requiresAuth: true,
                    title: 'Application Builder'
                }
        },
        {
            path: '/setup/docs',
            name: 'documentation',
            component: Documentation,
            meta: {
                requiresAuth: true,
                title: 'Documentation'
            }
        },
        {
            path: 'setup/client-routes',
            name: 'client-routes',
            component: ClientRoutes,
            meta: {
                requiresAuth: true,
                roles: ['admin'],
                title: 'Client Routes'
            }
        }
        ]
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
    },
    {
        path: '/landing',
        name: 'landing',
        component: LandingPage,
        meta: { 
            guest: true,
            title: 'Welcome to AvinerTech'
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
    
    // If user is authenticated and tries to access guest routes (login/register)
    if (authStore.isAuthenticated && isGuest) {
        next('/setup/dashboard');
    }
    // If route requires auth and user is not authenticated
    else if (requiresAuth && !authStore.isAuthenticated) {
        next('/login');
    } 
    // If route requires specific roles and user doesn't have any of them
    else if (requiresAuth && requiredRoles.length > 0 && !requiredRoles.includes(userRole)) {
        next('/setup/dashboard'); // Redirect to dashboard instead of home
    } 
    // Otherwise proceed as normal
    else {
        next();
    }
});

export default router; 