<template>
  <div class="space-y-8">
    <div class="flex justify-between items-center">
      <h1 class="text-2xl font-bold text-white">Client Routes Management</h1>
      <button 
        @click="addNewRoute"
        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center"
      >
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Add New Route
      </button>
    </div>

    <!-- Routes Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <!-- Landing Page Route -->
      <div class="bg-white/5 backdrop-blur-lg rounded-xl p-6 border border-white/10">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-white">Landing Page</h3>
          <span class="px-2 py-1 text-xs font-medium bg-green-500/20 text-green-400 rounded-full">Active</span>
        </div>
        <div class="space-y-3">
          <div class="flex items-center text-gray-300">
            <svg class="w-5 h-5 text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
            </svg>
            <span class="text-sm">/landing</span>
          </div>
          <p class="text-sm text-gray-400">Public landing page with company information and features</p>
          <div class="flex space-x-2">
            <button class="px-3 py-1 text-sm bg-blue-500/20 text-blue-400 rounded hover:bg-blue-500/30 transition-colors">
              Edit
            </button>
            <button 
              @click="previewRoute('/landing')"
              class="px-3 py-1 text-sm bg-purple-500/20 text-purple-400 rounded hover:bg-purple-500/30 transition-colors flex items-center"
            >
              Preview
              <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- About Page Route -->
      <div class="bg-white/5 backdrop-blur-lg rounded-xl p-6 border border-white/10">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-white">About Us</h3>
          <span class="px-2 py-1 text-xs font-medium bg-yellow-500/20 text-yellow-400 rounded-full">Draft</span>
        </div>
        <div class="space-y-3">
          <div class="flex items-center text-gray-300">
            <svg class="w-5 h-5 text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
            </svg>
            <span class="text-sm">/about</span>
          </div>
          <p class="text-sm text-gray-400">Company information, mission, and team details</p>
          <div class="flex space-x-2">
            <button class="px-3 py-1 text-sm bg-blue-500/20 text-blue-400 rounded hover:bg-blue-500/30 transition-colors">
              Edit
            </button>
            <button 
              @click="previewRoute('/about')"
              class="px-3 py-1 text-sm bg-purple-500/20 text-purple-400 rounded hover:bg-purple-500/30 transition-colors flex items-center"
            >
              Preview
              <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Services Page Route -->
      <div class="bg-white/5 backdrop-blur-lg rounded-xl p-6 border border-white/10">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-white">Services</h3>
          <span class="px-2 py-1 text-xs font-medium bg-yellow-500/20 text-yellow-400 rounded-full">Draft</span>
        </div>
        <div class="space-y-3">
          <div class="flex items-center text-gray-300">
            <svg class="w-5 h-5 text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
            </svg>
            <span class="text-sm">/services</span>
          </div>
          <p class="text-sm text-gray-400">Detailed service offerings and solutions</p>
          <div class="flex space-x-2">
            <button class="px-3 py-1 text-sm bg-blue-500/20 text-blue-400 rounded hover:bg-blue-500/30 transition-colors">
              Edit
            </button>
            <button 
              @click="previewRoute('/services')"
              class="px-3 py-1 text-sm bg-purple-500/20 text-purple-400 rounded hover:bg-purple-500/30 transition-colors flex items-center"
            >
              Preview
              <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Contact Page Route -->
      <div class="bg-white/5 backdrop-blur-lg rounded-xl p-6 border border-white/10">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-white">Contact</h3>
          <span class="px-2 py-1 text-xs font-medium bg-yellow-500/20 text-yellow-400 rounded-full">Draft</span>
        </div>
        <div class="space-y-3">
          <div class="flex items-center text-gray-300">
            <svg class="w-5 h-5 text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
            </svg>
            <span class="text-sm">/contact</span>
          </div>
          <p class="text-sm text-gray-400">Contact form and company contact information</p>
          <div class="flex space-x-2">
            <button class="px-3 py-1 text-sm bg-blue-500/20 text-blue-400 rounded hover:bg-blue-500/30 transition-colors">
              Edit
            </button>
            <button 
              @click="previewRoute('/contact')"
              class="px-3 py-1 text-sm bg-purple-500/20 text-purple-400 rounded hover:bg-purple-500/30 transition-colors flex items-center"
            >
              Preview
              <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Blog Page Route -->
      <div class="bg-white/5 backdrop-blur-lg rounded-xl p-6 border border-white/10">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-white">Blog</h3>
          <span class="px-2 py-1 text-xs font-medium bg-yellow-500/20 text-yellow-400 rounded-full">Draft</span>
        </div>
        <div class="space-y-3">
          <div class="flex items-center text-gray-300">
            <svg class="w-5 h-5 text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
            </svg>
            <span class="text-sm">/blog</span>
          </div>
          <p class="text-sm text-gray-400">Company blog and news articles</p>
          <div class="flex space-x-2">
            <button class="px-3 py-1 text-sm bg-blue-500/20 text-blue-400 rounded hover:bg-blue-500/30 transition-colors">
              Edit
            </button>
            <button 
              @click="previewRoute('/blog')"
              class="px-3 py-1 text-sm bg-purple-500/20 text-purple-400 rounded hover:bg-purple-500/30 transition-colors flex items-center"
            >
              Preview
              <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Route Settings -->
    <div class="bg-white/5 backdrop-blur-lg rounded-xl p-6 border border-white/10">
      <h3 class="text-lg font-semibold text-white mb-4">Route Settings</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <h4 class="text-white font-medium mb-3">Default Route</h4>
          <select class="w-full px-4 py-2 rounded-lg bg-white/10 border border-white/20 text-white focus:outline-none focus:border-blue-500">
            <option value="/landing">Landing Page (/landing)</option>
            <option value="/about">About Page (/about)</option>
            <option value="/services">Services Page (/services)</option>
            <option value="/contact">Contact Page (/contact)</option>
            <option value="/blog">Blog Page (/blog)</option>
          </select>
        </div>
        <div>
          <h4 class="text-white font-medium mb-3">404 Page</h4>
          <select class="w-full px-4 py-2 rounded-lg bg-white/10 border border-white/20 text-white focus:outline-none focus:border-blue-500">
            <option value="default">Default 404 Page</option>
            <option value="custom">Custom 404 Page</option>
          </select>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router';
// import { router } from '@inertiajs/vue3';
import DashboardLayout from './DashboardLayout.vue';

defineOptions({
  layout: DashboardLayout,
});
// const router = useRouter();

const addNewRoute = () => {
  // Add new route logic here
};

const previewRoute = (path) => {
  // Get the route name from the path
  const routeName = path.replace('/', '');
  
  // Visit the route in a new tab using Inertia
  router.visit(route(routeName), {
    preserveState: true,
    preserveScroll: true,
    openInNewTab: true
  });
};
</script>

<style scoped>
/* Add any component-specific styles here */
</style> 