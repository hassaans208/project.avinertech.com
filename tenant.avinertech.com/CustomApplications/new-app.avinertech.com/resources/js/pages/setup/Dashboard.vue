<template>
  <div class="space-y-8">
    <h1 class="text-3xl font-bold text-white mb-8">Welcome Back, {{ authStore.user?.name }}!</h1>
    
    <!-- Statistics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <!-- Total Users Card -->
      <div class="relative group">
        <div class="absolute -inset-0.5 bg-gradient-to-r from-pink-600 to-purple-600 rounded-2xl blur opacity-30 group-hover:opacity-100 transition duration-1000"></div>
        <div class="relative bg-white/10 backdrop-blur-lg rounded-xl p-6 border border-white/20">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-400 text-sm">Total Users</p>
              <h3 class="text-3xl font-bold text-white mt-2">{{ stats.totalUsers }}</h3>
            </div>
            <div class="p-3 bg-pink-500/20 rounded-lg">
              <svg class="w-6 h-6 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
            </div>
          </div>
          <div class="mt-4">
            <span class="text-green-400 text-sm flex items-center">
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
              </svg>
              +12% from last month
            </span>
          </div>
        </div>
      </div>

      <!-- Active Sessions Card -->
      <div class="relative group">
        <div class="absolute -inset-0.5 bg-gradient-to-r from-blue-600 to-cyan-600 rounded-2xl blur opacity-30 group-hover:opacity-100 transition duration-1000"></div>
        <div class="relative bg-white/10 backdrop-blur-lg rounded-xl p-6 border border-white/20">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-400 text-sm">Active Sessions</p>
              <h3 class="text-3xl font-bold text-white mt-2">{{ stats.activeSessions }}</h3>
            </div>
            <div class="p-3 bg-blue-500/20 rounded-lg">
              <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
          <div class="mt-4">
            <span class="text-green-400 text-sm flex items-center">
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
              </svg>
              +8% from last hour
            </span>
          </div>
        </div>
      </div>

      <!-- System Health Card -->
      <div class="relative group">
        <div class="absolute -inset-0.5 bg-gradient-to-r from-green-600 to-emerald-600 rounded-2xl blur opacity-30 group-hover:opacity-100 transition duration-1000"></div>
        <div class="relative bg-white/10 backdrop-blur-lg rounded-xl p-6 border border-white/20">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-400 text-sm">System Health</p>
              <h3 class="text-3xl font-bold text-white mt-2">{{ stats.systemHealth }}%</h3>
            </div>
            <div class="p-3 bg-green-500/20 rounded-lg">
              <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
              </svg>
            </div>
          </div>
          <div class="mt-4">
            <div class="w-full bg-gray-700 rounded-full h-2">
              <div class="bg-green-500 h-2 rounded-full" :style="{ width: stats.systemHealth + '%' }"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Response Time Card -->
      <div class="relative group">
        <div class="absolute -inset-0.5 bg-gradient-to-r from-orange-600 to-red-600 rounded-2xl blur opacity-30 group-hover:opacity-100 transition duration-1000"></div>
        <div class="relative bg-white/10 backdrop-blur-lg rounded-xl p-6 border border-white/20">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-400 text-sm">Avg. Response Time</p>
              <h3 class="text-3xl font-bold text-white mt-2">{{ stats.avgResponseTime }}ms</h3>
            </div>
            <div class="p-3 bg-orange-500/20 rounded-lg">
              <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
          <div class="mt-4">
            <span class="text-red-400 text-sm flex items-center">
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
              </svg>
              +5ms from last check
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white/5 backdrop-blur-lg rounded-xl p-6 border border-white/10">
      <h2 class="text-xl font-semibold text-white mb-4">Recent Activity</h2>
      <div class="space-y-4">
        <div v-for="(activity, index) in recentActivity" :key="index" 
             class="flex items-center p-4 bg-white/5 rounded-lg border border-white/10">
          <div class="p-2 rounded-lg" :class="activity.iconBg">
            <component :is="activity.icon" class="w-5 h-5" :class="activity.iconColor" />
          </div>
          <div class="ml-4">
            <p class="text-white">{{ activity.description }}</p>
            <p class="text-sm text-gray-400">{{ activity.time }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useAuthStore } from '../../store/auth';
const authStore = useAuthStore();
import DashboardLayout from './DashboardLayout.vue';

defineOptions({
  layout: DashboardLayout,
});

// Mock data - Replace with actual API calls
const stats = ref({
  totalUsers: 1234,
  activeSessions: 89,
  systemHealth: 98,
  avgResponseTime: 45
});

const recentActivity = ref([
  {
    description: 'New user registration',
    time: '2 minutes ago',
    icon: 'UserIcon',
    iconBg: 'bg-blue-500/20',
    iconColor: 'text-blue-400'
  },
  {
    description: 'System update completed',
    time: '15 minutes ago',
    icon: 'UpdateIcon',
    iconBg: 'bg-green-500/20',
    iconColor: 'text-green-400'
  },
  {
    description: 'Database backup',
    time: '1 hour ago',
    icon: 'DatabaseIcon',
    iconBg: 'bg-purple-500/20',
    iconColor: 'text-purple-400'
  }
]);

// Icons components
const UserIcon = {
  render() {
    return h('svg', {
      fill: 'none',
      stroke: 'currentColor',
      viewBox: '0 0 24 24'
    }, [
      h('path', {
        'stroke-linecap': 'round',
        'stroke-linejoin': 'round',
        'stroke-width': '2',
        d: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'
      })
    ]);
  }
};

const UpdateIcon = {
  render() {
    return h('svg', {
      fill: 'none',
      stroke: 'currentColor',
      viewBox: '0 0 24 24'
    }, [
      h('path', {
        'stroke-linecap': 'round',
        'stroke-linejoin': 'round',
        'stroke-width': '2',
        d: 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15'
      })
    ]);
  }
};

const DatabaseIcon = {
  render() {
    return h('svg', {
      fill: 'none',
      stroke: 'currentColor',
      viewBox: '0 0 24 24'
    }, [
      h('path', {
        'stroke-linecap': 'round',
        'stroke-linejoin': 'round',
        'stroke-width': '2',
        d: 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4'
      })
    ]);
  }
};

onMounted(() => {
  // Here you would typically fetch real data from your API
  // For example:
  // fetchDashboardStats();
  // fetchRecentActivity();
});
</script> 