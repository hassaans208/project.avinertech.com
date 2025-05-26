<template>
  <header class="fixed top-0 left-0 w-full z-50 px-4 py-4">
    <nav class="container mx-auto max-w-6xl rounded-full transition-all duration-300 bg-white/10 backdrop-blur-sm border border-white/20 flex justify-between items-center">
      <div class="max-w-7xl flex justify-between items-center px-2 sm:px-6 lg:px-8">
        <div class="relative flex justify-between h-16">
          <div class="absolute inset-y-0 left-0 flex items-center sm:hidden">
            <!-- Mobile menu button -->
            <button 
              @click="isMobileMenuOpen = !isMobileMenuOpen" 
              type="button" 
              class="inline-flex items-center justify-center p-2 rounded-full text-white/80 hover:text-white hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-pink-500"
            >
              <span class="sr-only">Open main menu</span>
              <!-- Icon when menu is closed -->
              <svg 
                v-if="!isMobileMenuOpen" 
                class="block h-6 w-6" 
                xmlns="http://www.w3.org/2000/svg" 
                fill="none" 
                viewBox="0 0 24 24" 
                stroke="currentColor" 
                aria-hidden="true"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              </svg>
              <!-- Icon when menu is open -->
              <svg 
                v-else 
                class="block h-6 w-6" 
                xmlns="http://www.w3.org/2000/svg" 
                fill="none" 
                viewBox="0 0 24 24" 
                stroke="currentColor" 
                aria-hidden="true"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <div class="flex-1 flex items-center justify-center sm:items-center sm:justify-start">
            <div class="flex-shrink-0 flex items-center">
              <router-link to="/" class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-pink-400 to-purple-400">
                AvinerTech
              </router-link>
            </div>
            <div class="hidden sm:ml-6 sm:flex sm:space-x-4">
              <!-- Desktop menu -->
              <router-link 
                to="/" 
                class="px-4 py-1 text-white/80 hover:text-white transition-colors border-b-2 border-transparent hover:border-pink-500"
                :class="{ 'text-white border-purple-500': $route.path === '/' }"
              >
                Dashboard
              </router-link>
              
              <router-link 
                v-if="authStore.isAdmin || authStore.isManager" 
                to="/users" 
                class="px-4 py-1 text-white/80 hover:text-white transition-colors border-b-2 border-transparent hover:border-pink-500"
                :class="{ 'text-white border-purple-500': $route.path === '/users' }"
              >
                Users
              </router-link>
            </div>
          </div>

          <div class="absolute inset-y-0 right-0 flex items-center pr-2 sm:static sm:inset-auto sm:ml-6 sm:pr-0">
            <!-- Authentication Links -->
            <div v-if="!authStore.isAuthenticated" class="flex space-x-2">
              <router-link 
                to="/login" 
                class="text-white/80 hover:text-white transition-colors px-4 py-1 rounded-full bg-white/10 backdrop-blur-sm border border-white/20 hover:bg-white/20"
              >
                Login
              </router-link>
              <router-link 
                to="/register" 
                class="text-white px-4 py-1 rounded-full bg-gradient-to-r from-pink-500 to-purple-600 hover:shadow-lg hover:shadow-pink-500/30 transition-all hover:-translate-y-1"
              >
                Register
              </router-link>
            </div>

            <!-- Profile dropdown -->
            <div v-else class="ml-3 relative">
              <div>
                <button 
                  @click="isProfileDropdownOpen = !isProfileDropdownOpen" 
                  type="button" 
                  class="rounded-full flex text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500"
                >
                  <span class="sr-only">Open user menu</span>
                  <div class="h-8 w-8 rounded-full bg-gradient-to-r from-pink-500 to-purple-600 flex items-center justify-center text-white shadow-lg">
                    {{ authStore.user?.name.charAt(0).toUpperCase() }}
                  </div>
                </button>
              </div>

              <!-- Dropdown menu -->
              <div 
                v-if="isProfileDropdownOpen" 
                class="origin-top-right absolute right-0 mt-2 w-48 rounded-xl shadow-xl py-1 bg-white/10 backdrop-blur-md border border-white/20 focus:outline-none z-10"
              >
                <div class="px-4 py-2 text-xs text-white/60">
                  Signed in as <span class="font-medium text-white">{{ authStore.user?.email }}</span>
                </div>
                <div class="border-t border-white/10"></div>
                <router-link 
                  to="/profile" 
                  class="block px-4 py-2 text-sm text-white hover:bg-white/10 transition-colors"
                >
                  Your Profile
                </router-link>
                <button 
                  @click="logout" 
                  class="w-full text-left block px-4 py-2 text-sm text-white hover:bg-white/10 transition-colors"
                >
                  Sign out
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Mobile menu -->
      <div v-if="isMobileMenuOpen" class="sm:hidden bg-white/5 backdrop-blur-sm">
        <div class="pt-2 pb-3 space-y-1">
          <router-link 
            to="/" 
            class="text-white/80 hover:text-white block px-3 py-2 hover:bg-white/10 transition-colors"
            :class="{ 'text-white bg-white/10': $route.path === '/' }"
          >
            Dashboard
          </router-link>
          
          <router-link 
            v-if="authStore.isAdmin || authStore.isManager" 
            to="/users" 
            class="text-white/80 hover:text-white block px-3 py-2 hover:bg-white/10 transition-colors"
            :class="{ 'text-white bg-white/10': $route.path === '/users' }"
          >
            Users
          </router-link>
          
          <div v-if="!authStore.isAuthenticated" class="pt-4 pb-3 border-t border-white/10">
            <router-link 
              to="/login" 
              class="text-white/80 hover:text-white block px-3 py-2 hover:bg-white/10 transition-colors"
            >
              Login
            </router-link>
            <router-link 
              to="/register" 
              class="text-white/80 hover:text-white block px-3 py-2 hover:bg-white/10 transition-colors"
            >
              Register
            </router-link>
          </div>
          
          <div v-else class="pt-4 pb-3 border-t border-white/10">
            <div class="flex items-center px-4">
              <div class="flex-shrink-0">
                <div class="h-10 w-10 rounded-full bg-gradient-to-r from-pink-500 to-purple-600 flex items-center justify-center text-white">
                  {{ authStore.user?.name.charAt(0).toUpperCase() }}
                </div>
              </div>
              <div class="ml-3">
                <div class="text-base font-medium text-white">{{ authStore.user?.name }}</div>
                <div class="text-sm font-medium text-white/60">{{ authStore.user?.email }}</div>
              </div>
            </div>
            <div class="mt-3 space-y-1">
              <router-link 
                to="/profile" 
                class="text-white/80 hover:text-white block px-3 py-2 hover:bg-white/10 transition-colors"
              >
                Your Profile
              </router-link>
              <button 
                @click="logout" 
                class="w-full text-left text-white/80 hover:text-white block px-3 py-2 hover:bg-white/10 transition-colors"
              >
                Sign out
              </button>
            </div>
          </div>
        </div>
      </div>
    </nav>
  </header>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../store/auth';

const router = useRouter();
const authStore = useAuthStore();

const isMobileMenuOpen = ref(false);
const isProfileDropdownOpen = ref(false);

async function logout() {
  await authStore.logout();
  isProfileDropdownOpen.value = false;
  router.push('/login');
}
</script> 