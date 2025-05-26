<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
      <div>
        <h2 class="text-2xl font-bold text-white">AI Assistant</h2>
        <p class="mt-1 text-sm text-gray-400">Your intelligent AI-powered assistant</p>
      </div>
      <div class="flex space-x-4">
        <button 
          @click="toggleAIMode"
          class="px-4 py-2 rounded-lg bg-white/5 border border-white/10 text-white hover:bg-white/10 transition-all"
        >
          <span class="inline-flex items-center">
            <svg class="w-5 h-5 mr-2" :class="aiMode ? 'text-pink-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            </svg>
            {{ aiMode ? 'AI Mode: Active' : 'AI Mode: Inactive' }}
          </span>
        </button>
      </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Chat Interface -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Chat Messages -->
        <div class="bg-white/5 backdrop-blur-lg rounded-xl border border-white/10 p-6 h-[600px] flex flex-col">
          <div class="flex-1 overflow-y-auto space-y-4 mb-4">
            <!-- Welcome Message -->
            <div class="flex items-start space-x-4">
              <div class="flex-shrink-0">
                <div class="w-10 h-10 rounded-lg bg-pink-500/10 flex items-center justify-center">
                  <svg class="w-6 h-6 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                  </svg>
                </div>
              </div>
              <div class="flex-1">
                <div class="bg-white/5 rounded-lg p-4">
                  <p class="text-white">Hello! I'm your AI assistant. How can I help you today?</p>
                </div>
                <p class="mt-1 text-xs text-gray-400">Just now</p>
              </div>
            </div>

            <!-- User Message -->
            <div class="flex items-start space-x-4 justify-end">
              <div class="flex-1 text-right">
                <div class="bg-pink-500/10 rounded-lg p-4 inline-block">
                  <p class="text-white">Can you help me analyze my client data?</p>
                </div>
                <p class="mt-1 text-xs text-gray-400">Just now</p>
              </div>
              <div class="flex-shrink-0">
                <div class="w-10 h-10 rounded-lg bg-gradient-to-r from-pink-500 to-purple-600 flex items-center justify-center text-white font-medium">
                  U
                </div>
              </div>
            </div>

            <!-- AI Response -->
            <div class="flex items-start space-x-4">
              <div class="flex-shrink-0">
                <div class="w-10 h-10 rounded-lg bg-pink-500/10 flex items-center justify-center">
                  <svg class="w-6 h-6 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                  </svg>
                </div>
              </div>
              <div class="flex-1">
                <div class="bg-white/5 rounded-lg p-4">
                  <p class="text-white">I'd be happy to help analyze your client data! I can assist with:</p>
                  <ul class="mt-2 list-disc list-inside text-gray-300 space-y-1">
                    <li>Client behavior patterns</li>
                    <li>Revenue trends</li>
                    <li>Engagement metrics</li>
                    <li>Predictive analytics</li>
                  </ul>
                  <p class="mt-2 text-white">What specific aspect would you like to explore?</p>
                </div>
                <p class="mt-1 text-xs text-gray-400">Just now</p>
              </div>
            </div>
          </div>

          <!-- Chat Input -->
          <div class="flex space-x-4">
            <input
              type="text"
              v-model="message"
              placeholder="Type your message..."
              class="flex-1 px-4 py-2 rounded-lg bg-white/5 border border-white/10 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-pink-500/50"
              @keyup.enter="sendMessage"
            />
            <button
              @click="sendMessage"
              class="px-4 py-2 rounded-lg bg-gradient-to-r from-pink-500 to-purple-600 text-white hover:shadow-lg hover:shadow-pink-500/30 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
              :disabled="!message.trim()"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- AI Features Panel -->
      <div class="space-y-6">
        <!-- Quick Actions -->
        <div class="bg-white/5 backdrop-blur-lg rounded-xl border border-white/10 p-6">
          <h3 class="text-lg font-medium text-white mb-4">Quick Actions</h3>
          <div class="space-y-3">
            <button 
              v-for="action in quickActions" 
              :key="action.name"
              @click="executeAction(action)"
              class="w-full px-4 py-3 rounded-lg bg-white/5 hover:bg-white/10 transition-all text-left"
            >
              <div class="flex items-center">
                <div class="p-2 rounded-lg" :class="action.bgColor">
                  <component :is="action.icon" class="w-5 h-5" :class="action.iconColor" />
                </div>
                <div class="ml-3">
                  <p class="text-sm font-medium text-white">{{ action.name }}</p>
                  <p class="text-xs text-gray-400">{{ action.description }}</p>
                </div>
              </div>
            </button>
          </div>
        </div>

        <!-- AI Insights -->
        <div class="bg-white/5 backdrop-blur-lg rounded-xl border border-white/10 p-6">
          <h3 class="text-lg font-medium text-white mb-4">AI Insights</h3>
          <div class="space-y-4">
            <div 
              v-for="insight in aiInsights" 
              :key="insight.id"
              class="p-4 rounded-lg bg-white/5"
            >
              <div class="flex items-start">
                <div class="p-2 rounded-lg" :class="insight.bgColor">
                  <component :is="insight.icon" class="w-5 h-5" :class="insight.iconColor" />
                </div>
                <div class="ml-3">
                  <p class="text-sm font-medium text-white">{{ insight.title }}</p>
                  <p class="mt-1 text-sm text-gray-400">{{ insight.description }}</p>
                </div>
              </div>
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

const aiMode = ref(true);
const message = ref('');

// Quick Actions
const quickActions = [
  {
    name: 'Analyze Client Data',
    description: 'Get insights from client behavior',
    icon: 'ChartIcon',
    bgColor: 'bg-pink-500/10',
    iconColor: 'text-pink-500'
  },
  {
    name: 'Generate Report',
    description: 'Create a detailed client report',
    icon: 'DocumentIcon',
    bgColor: 'bg-blue-500/10',
    iconColor: 'text-blue-500'
  },
  {
    name: 'Predict Trends',
    description: 'Forecast future client behavior',
    icon: 'TrendingUpIcon',
    bgColor: 'bg-purple-500/10',
    iconColor: 'text-purple-500'
  },
  {
    name: 'Optimize Strategy',
    description: 'Get AI-powered recommendations',
    icon: 'LightBulbIcon',
    bgColor: 'bg-green-500/10',
    iconColor: 'text-green-500'
  }
];

// AI Insights
const aiInsights = [
  {
    id: 1,
    title: 'Client Engagement Peak',
    description: 'Most active users between 2-4 PM EST',
    icon: 'ClockIcon',
    bgColor: 'bg-pink-500/10',
    iconColor: 'text-pink-500'
  },
  {
    id: 2,
    title: 'Conversion Opportunity',
    description: 'High potential clients identified in enterprise segment',
    icon: 'UserGroupIcon',
    bgColor: 'bg-blue-500/10',
    iconColor: 'text-blue-500'
  },
  {
    id: 3,
    title: 'Revenue Forecast',
    description: 'Expected 15% growth in Q2 based on current trends',
    icon: 'CurrencyDollarIcon',
    bgColor: 'bg-purple-500/10',
    iconColor: 'text-purple-500'
  }
];

// Methods
const toggleAIMode = () => {
  aiMode.value = !aiMode.value;
};

const sendMessage = () => {
  if (!message.value.trim()) return;
  
  // Add message handling logic here
  console.log('Sending message:', message.value);
  
  // Clear input
  message.value = '';
};

const executeAction = (action) => {
  console.log('Executing action:', action.name);
  // Add action handling logic here
};

// Icon Components
const ChartIcon = {
  template: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
  </svg>`
};

const DocumentIcon = {
  template: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
  </svg>`
};

const TrendingUpIcon = {
  template: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
  </svg>`
};

const LightBulbIcon = {
  template: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
  </svg>`
};

const ClockIcon = {
  template: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
  </svg>`
};

const UserGroupIcon = {
  template: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
  </svg>`
};

const CurrencyDollarIcon = {
  template: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
  </svg>`
};
</script>

<style scoped>
/* Custom scrollbar for chat messages */
.overflow-y-auto {
  scrollbar-width: thin;
  scrollbar-color: rgba(255, 255, 255, 0.2) transparent;
}

.overflow-y-auto::-webkit-scrollbar {
  width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: transparent;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background-color: rgba(255, 255, 255, 0.2);
  border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background-color: rgba(255, 255, 255, 0.3);
}
</style> 