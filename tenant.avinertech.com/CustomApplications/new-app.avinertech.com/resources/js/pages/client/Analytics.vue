<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
      <div>
        <h2 class="text-2xl font-bold text-white">Client Analytics</h2>
        <p class="mt-1 text-sm text-gray-400">Deep insights and performance analytics</p>
      </div>
      <div class="flex space-x-4">
        <button class="px-4 py-2 rounded-lg bg-white/5 border border-white/10 text-white hover:bg-white/10 transition-all">
          <span class="inline-flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            Refresh Data
          </span>
        </button>
        <button class="px-4 py-2 rounded-lg bg-gradient-to-r from-pink-500 to-purple-600 text-white hover:shadow-lg hover:shadow-pink-500/30 transition-all">
          <span class="inline-flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
            </svg>
            Export Report
          </span>
        </button>
      </div>
    </div>

    <!-- Analytics Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Key Metrics -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Engagement Metrics -->
        <div class="bg-white/5 backdrop-blur-lg rounded-xl border border-white/10 p-6">
          <h3 class="text-lg font-medium text-white mb-4">Engagement Metrics</h3>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="p-4 rounded-lg bg-white/5">
              <p class="text-sm font-medium text-gray-400">Active Sessions</p>
              <p class="mt-2 text-2xl font-bold text-white">2,345</p>
              <p class="mt-1 text-sm text-green-400">+12.3% from last week</p>
            </div>
            <div class="p-4 rounded-lg bg-white/5">
              <p class="text-sm font-medium text-gray-400">Avg. Session Duration</p>
              <p class="mt-2 text-2xl font-bold text-white">4m 32s</p>
              <p class="mt-1 text-sm text-green-400">+8.7% from last week</p>
            </div>
            <div class="p-4 rounded-lg bg-white/5">
              <p class="text-sm font-medium text-gray-400">Bounce Rate</p>
              <p class="mt-2 text-2xl font-bold text-white">32.4%</p>
              <p class="mt-1 text-sm text-red-400">+2.1% from last week</p>
            </div>
          </div>
        </div>

        <!-- User Behavior -->
        <div class="bg-white/5 backdrop-blur-lg rounded-xl border border-white/10 p-6">
          <h3 class="text-lg font-medium text-white mb-4">User Behavior</h3>
          <div class="h-80">
            <!-- Add your chart component here -->
            <div class="w-full h-full flex items-center justify-center text-gray-400">
              User Behavior Chart Placeholder
            </div>
          </div>
        </div>

        <!-- Conversion Funnel -->
        <div class="bg-white/5 backdrop-blur-lg rounded-xl border border-white/10 p-6">
          <h3 class="text-lg font-medium text-white mb-4">Conversion Funnel</h3>
          <div class="h-80">
            <!-- Add your chart component here -->
            <div class="w-full h-full flex items-center justify-center text-gray-400">
              Conversion Funnel Chart Placeholder
            </div>
          </div>
        </div>
      </div>

      <!-- Insights Panel -->
      <div class="space-y-6">
        <!-- AI Insights -->
        <div class="bg-white/5 backdrop-blur-lg rounded-xl border border-white/10 p-6">
          <h3 class="text-lg font-medium text-white mb-4">AI Insights</h3>
          <div class="space-y-4">
            <div class="p-4 rounded-lg bg-white/5">
              <div class="flex items-start">
                <div class="p-2 rounded-lg bg-pink-500/10">
                  <svg class="w-5 h-5 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                  </svg>
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-white">Peak Activity Time</p>
                  <p class="mt-1 text-sm text-gray-400">Most active users between 2-4 PM EST</p>
                </div>
              </div>
            </div>
            <div class="p-4 rounded-lg bg-white/5">
              <div class="flex items-start">
                <div class="p-2 rounded-lg bg-blue-500/10">
                  <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                  </svg>
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-white">Conversion Trend</p>
                  <p class="mt-1 text-sm text-gray-400">Conversion rate increased by 15% in the last week</p>
                </div>
              </div>
            </div>
            <div class="p-4 rounded-lg bg-white/5">
              <div class="flex items-start">
                <div class="p-2 rounded-lg bg-purple-500/10">
                  <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-white">Response Time</p>
                  <p class="mt-1 text-sm text-gray-400">Average response time decreased by 25%</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Top Performing Pages -->
        <div class="bg-white/5 backdrop-blur-lg rounded-xl border border-white/10 p-6">
          <h3 class="text-lg font-medium text-white mb-4">Top Performing Pages</h3>
          <div class="space-y-4">
            <div class="flex items-center justify-between p-3 rounded-lg bg-white/5">
              <div class="flex items-center">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-pink-500/10 text-pink-500 font-medium">1</span>
                <span class="ml-3 text-sm text-white">Dashboard</span>
              </div>
              <span class="text-sm text-gray-400">2.3k views</span>
            </div>
            <div class="flex items-center justify-between p-3 rounded-lg bg-white/5">
              <div class="flex items-center">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-purple-500/10 text-purple-500 font-medium">2</span>
                <span class="ml-3 text-sm text-white">Profile</span>
              </div>
              <span class="text-sm text-gray-400">1.8k views</span>
            </div>
            <div class="flex items-center justify-between p-3 rounded-lg bg-white/5">
              <div class="flex items-center">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-500/10 text-blue-500 font-medium">3</span>
                <span class="ml-3 text-sm text-white">Settings</span>
              </div>
              <span class="text-sm text-gray-400">1.2k views</span>
            </div>
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

// Add analytics initialization logic here
</script>

<style scoped>
/* Add any component-specific styles here */
</style> 