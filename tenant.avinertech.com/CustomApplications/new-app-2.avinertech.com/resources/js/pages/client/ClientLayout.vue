<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900">
    <!-- Sidebar -->
    <aside 
      class="fixed inset-y-0 left-0 z-50 bg-white/5 backdrop-blur-lg border-r border-white/10 transform transition-all duration-300 ease-in-out flex flex-col"
      :class="[
        isSidebarCollapsed ? 'w-16' : 'w-64',
        isSidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
      ]"
    >
      <!-- Logo -->
      <div class="h-16 flex items-center justify-between border-b border-white/10 px-4">
        <Link :href="route('client.dashboard')" class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-pink-400 to-purple-400">
          <span :class="{ 'hidden': isSidebarCollapsed }">AvinerTech</span>
          <span :class="{ 'hidden': !isSidebarCollapsed }">A</span>
        </Link>
        <button 
          @click="isSidebarCollapsed = !isSidebarCollapsed"
          class="hidden lg:block text-gray-400 hover:text-white"
        >
          <svg 
            class="h-5 w-5 transform transition-transform"
            :class="{ 'rotate-180': isSidebarCollapsed }"
            fill="none" 
            viewBox="0 0 24 24" 
            stroke="currentColor"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
          </svg>
        </button>
      </div>

      <!-- Primary Navigation -->
      <nav class="mt-6 px-2 flex-1">
        <div class="space-y-1">
          <Link 
            v-for="item in navigation" 
            :key="item.name"
            :href="route(item.route)"
            class="group flex items-center px-2 py-3 text-sm font-medium rounded-lg transition-all"
            :class="[
              route().current(item.route)
                ? 'bg-white/10 text-white'
                : 'text-gray-400 hover:text-white hover:bg-white/5'
            ]"
          >
            <svg v-if="item.icon === 'HomeIcon'" class="h-5 w-5 flex-shrink-0" :class="[route().current(item.route) ? 'text-pink-400' : 'text-gray-400 group-hover:text-gray-300', isSidebarCollapsed ? '' : 'mr-3']" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <svg v-if="item.icon === 'FolderIcon'" class="h-5 w-5 flex-shrink-0" :class="[route().current(item.route) ? 'text-pink-400' : 'text-gray-400 group-hover:text-gray-300', isSidebarCollapsed ? '' : 'mr-3']" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
            </svg>
            <svg v-if="item.icon === 'ClipboardIcon'" class="h-5 w-5 flex-shrink-0" :class="[route().current(item.route) ? 'text-pink-400' : 'text-gray-400 group-hover:text-gray-300', isSidebarCollapsed ? '' : 'mr-3']" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <span :class="{ 'hidden': isSidebarCollapsed }">{{ item.name }}</span>
          </Link>
        </div>
      </nav>

      <!-- Secondary Navigation -->
      <nav class="mt-6 px-2 py-4 border-t border-white/10">
        <div class="space-y-1">
          <Link 
            v-for="item in defaultNavigation" 
            :key="item.name"
            :href="route(item.route)"
            class="group flex items-center px-2 py-3 text-sm font-medium rounded-lg transition-all text-gray-400 hover:text-white hover:bg-white/5"
          >
            <svg v-if="item.icon === 'HomeIcon'" class="h-5 w-5 flex-shrink-0" :class="['text-gray-400 group-hover:text-gray-300', isSidebarCollapsed ? '' : 'mr-3']" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <svg v-if="item.icon === 'FolderIcon'" class="h-5 w-5 flex-shrink-0" :class="['text-gray-400 group-hover:text-gray-300', isSidebarCollapsed ? '' : 'mr-3']" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
            </svg>
            <span :class="{ 'hidden': isSidebarCollapsed }">{{ item.name }}</span>
          </Link>
        </div>
      </nav>
    </aside>

    <!-- Mobile sidebar backdrop -->
    <div 
      v-if="isSidebarOpen" 
      class="fixed inset-0 z-40 bg-gray-900/50 backdrop-blur-sm lg:hidden"
      @click="isSidebarOpen = false"
    ></div>

    <div :class="[isSidebarCollapsed ? 'lg:pl-16' : 'lg:pl-64']">
      <!-- Dashboard Header -->
      <header class="bg-white/5 backdrop-blur-lg border-b border-white/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <!-- Mobile menu button -->
              <button 
                type="button" 
                class="lg:hidden -ml-0.5 -mt-0.5 inline-flex h-12 w-12 items-center justify-center rounded-lg text-gray-400 hover:text-white hover:bg-white/5 mr-4"
                @click="isSidebarOpen = !isSidebarOpen"
              >
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
              </button>
              <div>
                <h1 class="text-2xl font-bold text-white">Welcome back, {{ authStore.user?.name }}</h1>
                <p class="mt-1 text-sm text-gray-400">Here's what's happening with your account today.</p>
              </div>
            </div>
            <div class="flex items-center space-x-4">
              <button class="px-4 py-2 rounded-lg bg-white/10 text-white hover:bg-white/20 transition-all flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <span>Notifications</span>
              </button>
              <button class="px-4 py-2 rounded-lg bg-gradient-to-r from-pink-500 to-purple-600 text-white hover:shadow-lg hover:shadow-pink-500/30 transition-all flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                <span>New Project</span>
              </button>
            </div>
          </div>
        </div>
      </header>

      <!-- Main Content -->
      <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <slot></slot>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useAuthStore } from '@/store/auth';

const authStore = useAuthStore();
const isSidebarCollapsed = ref(true);
const isSidebarOpen = ref(false);

const navigation = [
  { name: 'Dashboard', route: 'client.dashboard', icon: 'HomeIcon' },
  { name: 'List', route: 'client.list', icon: 'FolderIcon' },
  { name: 'Create', route: 'client.create', icon: 'ClipboardIcon' },
  { name: 'Edit', route: 'client.edit', icon: 'ClipboardIcon' },
  { name: 'Web Editor', route: 'client.web-editor', icon: 'ClipboardIcon' },
  { name: 'AI Assistant', route: 'client.ai-assistant', icon: 'ClipboardIcon' },
  { name: 'Stats', route: 'client.stats', icon: 'ClipboardIcon' },
  { name: 'Analytics', route: 'client.analytics', icon: 'ClipboardIcon' },
  { name: 'Settings', route: 'client.settings', icon: 'ClipboardIcon' },
  { name: 'Visit Website', route: 'landing', icon: 'ClipboardIcon' },
];

const defaultNavigation = [
  { name: 'Contact Support', route: 'client.dashboard', icon: 'HomeIcon' },
  { name: 'Setup', route: 'setup.dashboard', icon: 'FolderIcon' },
  { name: 'Logout', route: 'logout', icon: 'FolderIcon' },
];
</script>