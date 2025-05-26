<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
      <div>
        <h2 class="text-2xl font-bold text-white">Settings</h2>
        <p class="mt-1 text-sm text-gray-400">Manage your client settings and preferences</p>
      </div>
      <div class="flex space-x-4">
        <button class="px-4 py-2 rounded-lg bg-white/5 border border-white/10 text-white hover:bg-white/10 transition-all">
          <span class="inline-flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            Reset to Defaults
          </span>
        </button>
        <button class="px-4 py-2 rounded-lg bg-gradient-to-r from-pink-500 to-purple-600 text-white hover:shadow-lg hover:shadow-pink-500/30 transition-all">
          <span class="inline-flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            Save Changes
          </span>
        </button>
      </div>
    </div>

    <!-- Settings Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Settings Navigation -->
      <div class="lg:col-span-1 space-y-6">
        <div class="bg-white/5 backdrop-blur-lg rounded-xl border border-white/10 p-6">
          <nav class="space-y-1">
            <button
              v-for="section in settingsSections"
              :key="section.id"
              @click="activeSection = section.id"
              class="w-full px-4 py-3 rounded-lg text-left transition-all"
              :class="[
                activeSection === section.id
                  ? 'bg-white/10 text-white'
                  : 'text-gray-400 hover:bg-white/5 hover:text-white'
              ]"
            >
              <div class="flex items-center">
                <div class="p-2 rounded-lg" :class="section.bgColor">
                  <component :is="section.icon" class="w-5 h-5" :class="section.iconColor" />
                </div>
                <span class="ml-3 text-sm font-medium">{{ section.name }}</span>
              </div>
            </button>
          </nav>
        </div>
      </div>

      <!-- Settings Content -->
      <div class="lg:col-span-2 space-y-6">
        <!-- General Settings -->
        <div v-if="activeSection === 'general'" class="bg-white/5 backdrop-blur-lg rounded-xl border border-white/10 p-6">
          <h3 class="text-lg font-medium text-white mb-6">General Settings</h3>
          <div class="space-y-6">
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-400">Client Name</label>
              <input
                type="text"
                v-model="settings.general.name"
                class="w-full px-4 py-2 rounded-lg bg-white/5 border border-white/10 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-pink-500/50"
              />
            </div>
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-400">Email Address</label>
              <input
                type="email"
                v-model="settings.general.email"
                class="w-full px-4 py-2 rounded-lg bg-white/5 border border-white/10 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-pink-500/50"
              />
            </div>
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-400">Time Zone</label>
              <select
                v-model="settings.general.timezone"
                class="w-full px-4 py-2 rounded-lg bg-white/5 border border-white/10 text-white focus:outline-none focus:ring-2 focus:ring-pink-500/50"
              >
                <option value="UTC">UTC</option>
                <option value="EST">Eastern Time (EST)</option>
                <option value="CST">Central Time (CST)</option>
                <option value="PST">Pacific Time (PST)</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Appearance Settings -->
        <div v-if="activeSection === 'appearance'" class="bg-white/5 backdrop-blur-lg rounded-xl border border-white/10 p-6">
          <h3 class="text-lg font-medium text-white mb-6">Appearance Settings</h3>
          <div class="space-y-6">
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-400">Theme</label>
              <div class="grid grid-cols-3 gap-4">
                <button
                  v-for="theme in themes"
                  :key="theme.id"
                  @click="settings.appearance.theme = theme.id"
                  class="p-4 rounded-lg border transition-all"
                  :class="[
                    settings.appearance.theme === theme.id
                      ? 'border-pink-500 bg-pink-500/10'
                      : 'border-white/10 hover:border-white/20'
                  ]"
                >
                  <div class="aspect-video rounded-lg mb-2" :class="theme.previewClass"></div>
                  <p class="text-sm font-medium text-white">{{ theme.name }}</p>
                </button>
              </div>
            </div>
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-400">Accent Color</label>
              <div class="grid grid-cols-6 gap-4">
                <button
                  v-for="color in accentColors"
                  :key="color.id"
                  @click="settings.appearance.accentColor = color.id"
                  class="w-10 h-10 rounded-full transition-all"
                  :class="[
                    color.bgClass,
                    settings.appearance.accentColor === color.id
                      ? 'ring-2 ring-offset-2 ring-offset-gray-900 ring-white'
                      : 'hover:ring-2 hover:ring-offset-2 hover:ring-offset-gray-900 hover:ring-white'
                  ]"
                ></button>
              </div>
            </div>
          </div>
        </div>

        <!-- Notification Settings -->
        <div v-if="activeSection === 'notifications'" class="bg-white/5 backdrop-blur-lg rounded-xl border border-white/10 p-6">
          <h3 class="text-lg font-medium text-white mb-6">Notification Settings</h3>
          <div class="space-y-6">
            <div v-for="notification in notificationSettings" :key="notification.id" class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-white">{{ notification.name }}</p>
                <p class="text-sm text-gray-400">{{ notification.description }}</p>
              </div>
              <label class="relative inline-flex items-center cursor-pointer">
                <input
                  type="checkbox"
                  v-model="settings.notifications[notification.id]"
                  class="sr-only peer"
                />
                <div class="w-11 h-6 bg-white/10 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-pink-500"></div>
              </label>
            </div>
          </div>
        </div>

        <!-- Security Settings -->
        <div v-if="activeSection === 'security'" class="bg-white/5 backdrop-blur-lg rounded-xl border border-white/10 p-6">
          <h3 class="text-lg font-medium text-white mb-6">Security Settings</h3>
          <div class="space-y-6">
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-400">Two-Factor Authentication</label>
              <div class="flex items-center justify-between">
                <p class="text-sm text-gray-400">Add an extra layer of security to your account</p>
                <button class="px-4 py-2 rounded-lg bg-white/5 border border-white/10 text-white hover:bg-white/10 transition-all">
                  Enable 2FA
                </button>
              </div>
            </div>
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-400">Session Timeout</label>
              <select
                v-model="settings.security.sessionTimeout"
                class="w-full px-4 py-2 rounded-lg bg-white/5 border border-white/10 text-white focus:outline-none focus:ring-2 focus:ring-pink-500/50"
              >
                <option value="15">15 minutes</option>
                <option value="30">30 minutes</option>
                <option value="60">1 hour</option>
                <option value="120">2 hours</option>
              </select>
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

// Settings Sections
const settingsSections = [
  {
    id: 'general',
    name: 'General',
    icon: 'CogIcon',
    bgColor: 'bg-blue-500/10',
    iconColor: 'text-blue-500'
  },
  {
    id: 'appearance',
    name: 'Appearance',
    icon: 'PaintBrushIcon',
    bgColor: 'bg-purple-500/10',
    iconColor: 'text-purple-500'
  },
  {
    id: 'notifications',
    name: 'Notifications',
    icon: 'BellIcon',
    bgColor: 'bg-pink-500/10',
    iconColor: 'text-pink-500'
  },
  {
    id: 'security',
    name: 'Security',
    icon: 'ShieldCheckIcon',
    bgColor: 'bg-green-500/10',
    iconColor: 'text-green-500'
  }
];

// Active Section
const activeSection = ref('general');

// Settings Data
const settings = ref({
  general: {
    name: 'Client Name',
    email: 'client@example.com',
    timezone: 'UTC'
  },
  appearance: {
    theme: 'dark',
    accentColor: 'pink'
  },
  notifications: {
    emailNotifications: true,
    pushNotifications: true,
    weeklyReports: false,
    securityAlerts: true
  },
  security: {
    sessionTimeout: '30'
  }
});

// Themes
const themes = [
  {
    id: 'light',
    name: 'Light',
    previewClass: 'bg-white'
  },
  {
    id: 'dark',
    name: 'Dark',
    previewClass: 'bg-gray-900'
  },
  {
    id: 'system',
    name: 'System',
    previewClass: 'bg-gradient-to-r from-white to-gray-900'
  }
];

// Accent Colors
const accentColors = [
  {
    id: 'pink',
    bgClass: 'bg-pink-500'
  },
  {
    id: 'purple',
    bgClass: 'bg-purple-500'
  },
  {
    id: 'blue',
    bgClass: 'bg-blue-500'
  },
  {
    id: 'green',
    bgClass: 'bg-green-500'
  },
  {
    id: 'yellow',
    bgClass: 'bg-yellow-500'
  },
  {
    id: 'red',
    bgClass: 'bg-red-500'
  }
];

// Notification Settings
const notificationSettings = [
  {
    id: 'emailNotifications',
    name: 'Email Notifications',
    description: 'Receive notifications via email'
  },
  {
    id: 'pushNotifications',
    name: 'Push Notifications',
    description: 'Receive push notifications in your browser'
  },
  {
    id: 'weeklyReports',
    name: 'Weekly Reports',
    description: 'Get weekly summary reports'
  },
  {
    id: 'securityAlerts',
    name: 'Security Alerts',
    description: 'Get notified about security-related events'
  }
];

// Icon Components
const CogIcon = {
  template: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
  </svg>`
};

const PaintBrushIcon = {
  template: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
  </svg>`
};

const BellIcon = {
  template: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
  </svg>`
};

const ShieldCheckIcon = {
  template: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
  </svg>`
};
</script>

<style scoped>
/* Add any component-specific styles here */
</style> 