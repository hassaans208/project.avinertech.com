<template>
  <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 border border-white/20">
    <h2 class="text-xl font-semibold text-white mb-4">General Settings</h2>
    
    <div class="space-y-4">
      <!-- Company Information Block -->
      <div class="flex items-center justify-between p-4 bg-white/5 rounded-lg">
        <div>
          <h3 class="text-white font-medium">Company Information</h3>
          <div class="mt-2 space-y-1">
            <p class="text-sm text-gray-400">
              <span class="text-white">Company Name:</span> {{ companyInfo.name || 'Not set' }}
            </p>
            <p class="text-sm text-gray-400">
              <span class="text-white">Host:</span> {{ companyInfo.host || 'Not set' }}.avinertech.com
            </p>
            <p class="text-sm text-gray-400">
              <span class="text-white">Email:</span> {{ companyInfo.email || 'Not set' }}
            </p>
            <p class="text-sm text-gray-400">
              <span class="text-white">Username:</span> {{ companyInfo.username || 'Not set' }}
            </p>
            <p class="text-sm text-gray-400">
              <span class="text-white">Phone:</span> {{ companyInfo.phone || 'Not set' }}
            </p>
            <p class="text-sm text-gray-400">
              <span class="text-white">Address:</span> {{ companyInfo.address || 'Not set' }}
            </p>
          </div>
        </div>
        <button 
          @click="$emit('edit', 'companyInfo')"
          class="px-4 py-2 bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 rounded-lg transition-colors"
        >
          Edit
        </button>
      </div>

      <!-- App Settings Block -->
      <div class="flex items-center justify-between p-4 bg-white/5 rounded-lg">
        <div>
          <h3 class="text-white font-medium">Application Settings</h3>
          <div class="mt-2 space-y-1">
            <p class="text-sm text-gray-400">
              <span class="text-white">App Name:</span> {{ appName }}
            </p>
            <p class="text-sm text-gray-400">
              <span class="text-white">Timezone:</span> {{ timezone }}
            </p>
          </div>
        </div>
        <div class="space-x-2">
          <button 
            @click="$emit('edit', 'appName')"
            class="px-4 py-2 bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 rounded-lg transition-colors"
          >
            Edit Name
          </button>
          <button 
            @click="$emit('edit', 'timezone')"
            class="px-4 py-2 bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 rounded-lg transition-colors"
          >
            Edit Timezone
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
  appName: {
    type: String,
    required: true
  },
  timezone: {
    type: String,
    required: true
  },
  companyInfo: {
    type: Object,
    required: true
  }
});

defineEmits(['edit']);
</script> 