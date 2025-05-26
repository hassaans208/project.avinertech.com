<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
      <div>
        <h2 class="text-2xl font-bold text-white">Client Statistics</h2>
        <p class="mt-1 text-sm text-gray-400">Overview of client performance and metrics</p>
      </div>
      <div class="flex space-x-4">
        <select 
          v-model="timeRange" 
          class="px-4 py-2 rounded-lg bg-white/5 border border-white/10 text-white focus:outline-none focus:ring-2 focus:ring-pink-500/50"
        >
          <option value="7d">Last 7 Days</option>
          <option value="30d">Last 30 Days</option>
          <option value="90d">Last 90 Days</option>
          <option value="1y">Last Year</option>
        </select>
        <button class="px-4 py-2 rounded-lg bg-gradient-to-r from-pink-500 to-purple-600 text-white hover:shadow-lg hover:shadow-pink-500/30 transition-all">
          Export Report
        </button>
      </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <!-- Total Clients -->
      <div class="bg-white/5 backdrop-blur-lg rounded-xl border border-white/10 p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-400">Total Clients</p>
            <p class="mt-2 text-3xl font-bold text-white">1,234</p>
            <p class="mt-2 text-sm text-green-400">
              <span class="inline-flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
                12.5%
              </span>
              <span class="text-gray-400 ml-2">vs last period</span>
            </p>
          </div>
          <div class="p-3 rounded-lg bg-pink-500/10">
            <svg class="w-6 h-6 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
          </div>
        </div>
      </div>

      <!-- Active Clients -->
      <div class="bg-white/5 backdrop-blur-lg rounded-xl border border-white/10 p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-400">Active Clients</p>
            <p class="mt-2 text-3xl font-bold text-white">856</p>
            <p class="mt-2 text-sm text-green-400">
              <span class="inline-flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
                8.2%
              </span>
              <span class="text-gray-400 ml-2">vs last period</span>
            </p>
          </div>
          <div class="p-3 rounded-lg bg-green-500/10">
            <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
        </div>
      </div>

      <!-- Revenue -->
      <div class="bg-white/5 backdrop-blur-lg rounded-xl border border-white/10 p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-400">Total Revenue</p>
            <p class="mt-2 text-3xl font-bold text-white">$45,678</p>
            <p class="mt-2 text-sm text-green-400">
              <span class="inline-flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
                23.1%
              </span>
              <span class="text-gray-400 ml-2">vs last period</span>
            </p>
          </div>
          <div class="p-3 rounded-lg bg-purple-500/10">
            <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
        </div>
      </div>

      <!-- Growth Rate -->
      <div class="bg-white/5 backdrop-blur-lg rounded-xl border border-white/10 p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-400">Growth Rate</p>
            <p class="mt-2 text-3xl font-bold text-white">15.3%</p>
            <p class="mt-2 text-sm text-green-400">
              <span class="inline-flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
                4.2%
              </span>
              <span class="text-gray-400 ml-2">vs last period</span>
            </p>
          </div>
          <div class="p-3 rounded-lg bg-blue-500/10">
            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Client Growth Chart -->
      <div class="bg-white/5 backdrop-blur-lg rounded-xl border border-white/10 p-6">
        <h3 class="text-lg font-medium text-white mb-4">Client Growth</h3>
        <div class="h-80">
          <!-- Add your chart component here -->
          <div class="w-full h-full flex items-center justify-center text-gray-400">
            Chart Component Placeholder
          </div>
        </div>
      </div>

      <!-- Revenue Distribution -->
      <div class="bg-white/5 backdrop-blur-lg rounded-xl border border-white/10 p-6">
        <h3 class="text-lg font-medium text-white mb-4">Revenue Distribution</h3>
        <div class="h-80">
          <!-- Add your chart component here -->
          <div class="w-full h-full flex items-center justify-center text-gray-400">
            Chart Component Placeholder
          </div>
        </div>
      </div>

      <!-- Client Status -->
      <div class="bg-white/5 backdrop-blur-lg rounded-xl border border-white/10 p-6">
        <h3 class="text-lg font-medium text-white mb-4">Client Status</h3>
        <div class="h-80">
          <!-- Add your chart component here -->
          <div class="w-full h-full flex items-center justify-center text-gray-400">
            Chart Component Placeholder
          </div>
        </div>
      </div>

      <!-- Client Type Distribution -->
      <div class="bg-white/5 backdrop-blur-lg rounded-xl border border-white/10 p-6">
        <h3 class="text-lg font-medium text-white mb-4">Client Type Distribution</h3>
        <div class="h-80">
          <!-- Add your chart component here -->
          <div class="w-full h-full flex items-center justify-center text-gray-400">
            Chart Component Placeholder
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import ClientLayout from './ClientLayout.vue';

defineOptions({
  layout: ClientLayout,
});

const timeRange = ref('30d');

// Add chart initialization logic here
</script>

<style scoped>
/* Add any component-specific styles here */
</style> 