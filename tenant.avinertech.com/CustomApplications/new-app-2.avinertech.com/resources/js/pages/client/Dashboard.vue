<template>
  <!-- <ClientLayout> -->
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <!-- Active Projects -->
      <div class="bg-white/5 backdrop-blur-lg rounded-xl p-6 border border-white/10 hover:border-pink-500/50 transition-all group">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-400">Active Projects</p>
            <p class="mt-2 text-3xl font-semibold text-white">12</p>
          </div>
          <div class="w-12 h-12 bg-pink-500/20 rounded-lg flex items-center justify-center group-hover:bg-pink-500/30 transition-all">
            <svg class="w-6 h-6 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
          </div>
        </div>
        <div class="mt-4">
          <div class="flex items-center text-sm text-green-400">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
            </svg>
            <span>+2 from last month</span>
          </div>
        </div>
      </div>

      <!-- Total Revenue -->
      <div class="bg-white/5 backdrop-blur-lg rounded-xl p-6 border border-white/10 hover:border-purple-500/50 transition-all group">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-400">Total Revenue</p>
            <p class="mt-2 text-3xl font-semibold text-white">$24,500</p>
          </div>
          <div class="w-12 h-12 bg-purple-500/20 rounded-lg flex items-center justify-center group-hover:bg-purple-500/30 transition-all">
            <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>
        <div class="mt-4">
          <div class="flex items-center text-sm text-green-400">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
            </svg>
            <span>+12% from last month</span>
          </div>
        </div>
      </div>

      <!-- Active Users -->
      <div class="bg-white/5 backdrop-blur-lg rounded-xl p-6 border border-white/10 hover:border-blue-500/50 transition-all group">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-400">Active Users</p>
            <p class="mt-2 text-3xl font-semibold text-white">1,234</p>
          </div>
          <div class="w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center group-hover:bg-blue-500/30 transition-all">
            <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
          </div>
        </div>
        <div class="mt-4">
          <div class="flex items-center text-sm text-green-400">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
            </svg>
            <span>+8% from last month</span>
          </div>
        </div>
      </div>

      <!-- System Health -->
      <div class="bg-white/5 backdrop-blur-lg rounded-xl p-6 border border-white/10 hover:border-green-500/50 transition-all group">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-400">System Health</p>
            <p class="mt-2 text-3xl font-semibold text-white">98.9%</p>
          </div>
          <div class="w-12 h-12 bg-green-500/20 rounded-lg flex items-center justify-center group-hover:bg-green-500/30 transition-all">
            <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>
        <div class="mt-4">
          <div class="flex items-center text-sm text-green-400">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
            </svg>
            <span>All systems operational</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Activity and Projects -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Recent Projects -->
      <div class="bg-white/5 backdrop-blur-lg rounded-xl border border-white/10">
        <div class="p-6 border-b border-white/10">
          <h2 class="text-lg font-semibold text-white">Recent Projects</h2>
        </div>
        <div class="p-6">
          <div class="space-y-6">
            <div v-for="project in recentProjects" :key="project.id" class="flex items-start space-x-4 group">
              <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-gradient-to-r from-pink-500/20 to-purple-500/20 flex items-center justify-center group-hover:from-pink-500/30 group-hover:to-purple-500/30 transition-all">
                <svg class="w-6 h-6 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-white truncate">{{ project.name }}</p>
                <p class="text-sm text-gray-400">{{ project.description }}</p>
                <div class="mt-2 flex items-center space-x-4">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="project.statusClass">
                    {{ project.status }}
                  </span>
                  <span class="text-sm text-gray-400">{{ project.lastUpdated }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Activity -->
      <div class="bg-white/5 backdrop-blur-lg rounded-xl border border-white/10">
        <div class="p-6 border-b border-white/10">
          <h2 class="text-lg font-semibold text-white">Recent Activity</h2>
        </div>
        <div class="p-6">
          <div class="flow-root">
            <ul class="-mb-8">
              <li v-for="(activity, index) in recentActivity" :key="activity.id" class="relative pb-8">
                <span v-if="index !== recentActivity.length - 1" class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-white/10" aria-hidden="true"></span>
                <div class="relative flex space-x-3">
                  <div>
                    <span class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-gray-900" :class="activity.iconBackground">
                      <component :is="activity.icon" class="h-5 w-5 text-white" aria-hidden="true" />
                    </span>
                  </div>
                  <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                    <div>
                      <p class="text-sm text-white">{{ activity.content }}</p>
                      <p class="mt-0.5 text-sm text-gray-400">{{ activity.time }}</p>
                    </div>
                  </div>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  <!-- </ClientLayout> -->
</template>

<script setup>
import { ref } from 'vue';
import ClientLayout from './ClientLayout.vue';

defineOptions({
  layout: ClientLayout,
});

// Mock data for recent projects
const recentProjects = ref([
  {
    id: 1,
    name: 'Website Redesign',
    description: 'Modernizing the company website with new features and design',
    status: 'In Progress',
    statusClass: 'bg-blue-500/20 text-blue-400',
    lastUpdated: '2 hours ago'
  },
  {
    id: 2,
    name: 'Mobile App Development',
    description: 'Building a new mobile application for iOS and Android',
    status: 'Planning',
    statusClass: 'bg-yellow-500/20 text-yellow-400',
    lastUpdated: '1 day ago'
  },
  {
    id: 3,
    name: 'API Integration',
    description: 'Integrating third-party services with our platform',
    status: 'Completed',
    statusClass: 'bg-green-500/20 text-green-400',
    lastUpdated: '3 days ago'
  }
]);

// Mock data for recent activity
const recentActivity = ref([
  {
    id: 1,
    content: 'Created new project "Website Redesign"',
    time: '2 hours ago',
    icon: 'DocumentIcon',
    iconBackground: 'bg-blue-500'
  },
  {
    id: 2,
    content: 'Updated project "Mobile App Development" status',
    time: '1 day ago',
    icon: 'PencilIcon',
    iconBackground: 'bg-yellow-500'
  },
  {
    id: 3,
    content: 'Completed project "API Integration"',
    time: '3 days ago',
    icon: 'CheckIcon',
    iconBackground: 'bg-green-500'
  }
]);

// Icons components
const DocumentIcon = {
  template: `
    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
    </svg>
  `
};

const PencilIcon = {
  template: `
    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
    </svg>
  `
};

const CheckIcon = {
  template: `
    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
    </svg>
  `
};
</script>

<style scoped>
/* Add any component-specific styles here */
</style> 