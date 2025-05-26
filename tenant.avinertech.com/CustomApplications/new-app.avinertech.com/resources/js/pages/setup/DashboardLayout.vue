<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900">
    <!-- Sidebar -->
    <aside 
      class="fixed top-0 left-0 h-screen w-64 bg-white/10 backdrop-blur-lg border-r border-white/20 transition-all duration-300 flex flex-col"
      :class="{ '-translate-x-full': !sidebarOpen }"
    >
      <div class="p-6 flex-1 flex flex-col">
        <div>
          <h1 class="text-2xl font-bold text-white mb-8">Dashboard</h1>
          <nav class="space-y-2">
            <Link 
              :href="route('setup.dashboard')" 
              class="flex items-center px-4 py-3 text-gray-300 hover:bg-white/10 rounded-lg transition-colors"
              :class="{ 'bg-white/20': page.url.startsWith('/setup/dashboard') }"
            >
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
              </svg>
              Dashboard
            </Link>
            <Link 
              :href="route('setup.index')" 
              class="flex items-center px-4 py-3 text-gray-300 hover:bg-white/10 rounded-lg transition-colors"
              :class="{ 'bg-white/20': page.url == '/setup' }"
            >
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              Setup System
            </Link>
            <Link 
              :href="route('setup.app-builder')" 
              class="flex items-center px-4 py-3 text-gray-300 hover:bg-white/10 rounded-lg transition-colors"
              :class="{ 'bg-white/20': page.url.startsWith('/setup/app-builder') && !page.url.startsWith('/setup/dashboard') }"
            >
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              Build Application
            </Link>
            <Link 
              :href="route('setup.plans')" 
              class="flex items-center px-4 py-3 text-gray-300 hover:bg-white/10 rounded-lg transition-colors"
              :class="{ 'bg-white/20': page.url.startsWith('/setup/plans') }"
            >
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
              </svg>
              Subscription Plans
            </Link>
            <Link 
              :href="route('setup.documentation')" 
              class="flex items-center px-4 py-3 text-gray-300 hover:bg-white/10 rounded-lg transition-colors"
              :class="{ 'bg-white/20': page.url.startsWith('/setup/docs') && !page.url.startsWith('/setup/dashboard') }"
            >
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
              </svg>
              Documentation
            </Link>
            <Link 
              :href="route('setup.client-routes')" 
              class="flex items-center px-4 py-3 text-gray-300 hover:bg-white/10 rounded-lg transition-colors"
              :class="{ 'bg-white/20': page.url.startsWith('/setup/client-routes') && !page.url.startsWith('/setup/dashboard') }"
            >
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
              </svg>
              Client Routes
            </Link>
          </nav>
        </div>

        <!-- Contact Support Link -->
        <div class="mt-auto pt-6 border-t border-white/10">
          <a 
            href="mailto:support@avinertech.com" 
            class="flex items-center px-4 py-3 bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 rounded-lg transition-colors group"
          >
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            Contact Support
            <svg class="w-4 h-4 ml-2 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </a>
        </div>
      </div>
    </aside>

    <!-- Main Content -->
    <div class="ml-64 p-8">
      <div class="flex justify-between items-center mb-8">
        <div class="flex items-center space-x-4">
          <button 
            @click="toggleSidebar"
            class="lg:hidden p-2 rounded-lg bg-white/10 hover:bg-white/20 transition-colors"
          >
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>
          <Link
            :href="route('client.dashboard')"
            class="px-4 py-2 rounded-lg bg-gradient-to-r from-pink-500 to-purple-600 text-white hover:shadow-lg hover:shadow-pink-500/30 transition-all flex items-center space-x-2"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <span>Go To POS</span>
          </Link>
        </div>
        <div class="flex items-center space-x-4">
          <span class="text-white">{{ authStore.user?.name }}</span>
          <button 
            @click="logout"
            class="px-4 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-300 rounded-lg transition-colors"
          >
            Logout
          </button>
        </div>
      </div>
      
      <!-- Page Content -->
      <div class="bg-white/5 backdrop-blur-lg rounded-2xl p-6 border border-white/10">
        <slot />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { useAuthStore } from '@/store/auth';

const authStore = useAuthStore();
const sidebarOpen = ref(true);
const page = usePage();

const toggleSidebar = () => {
  sidebarOpen.value = !sidebarOpen.value;
};

const logout = () => {
  router.post('/logout');
};
</script>

<style scoped>
.link-active {
  @reference bg-white/20 text-white;
}
</style> 