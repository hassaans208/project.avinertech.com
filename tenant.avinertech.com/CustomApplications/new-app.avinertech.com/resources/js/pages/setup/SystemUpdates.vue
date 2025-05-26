<template>
  <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 border border-white/20">
    <h2 class="text-xl font-semibold text-white mb-4">System Updates</h2>
    <div class="space-y-4">
      <div class="flex items-center justify-between p-4 bg-white/5 rounded-lg">
        <div>
          <h3 class="text-white font-medium">Current Version</h3>
          <p class="text-sm text-gray-400">{{ currentVersion }}</p>
        </div>
        <button 
          v-if="!isChecking && !updateAvailable"
          @click="checkForUpdates"
          class="px-4 py-2 bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 rounded-lg transition-colors"
        >
          Check for Updates
        </button>
        <button 
          v-else-if="isChecking"
          disabled
          class="px-4 py-2 bg-gray-500/20 text-gray-400 rounded-lg cursor-not-allowed flex items-center"
        >
          <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          Checking...
        </button>
        <button 
          v-else-if="updateAvailable"
          @click="performUpdate"
          class="px-4 py-2 bg-green-500/20 hover:bg-green-500/30 text-green-300 rounded-lg transition-colors"
        >
          Update Now
        </button>
        <span 
          v-else
          class="px-3 py-1 bg-green-500/20 text-green-300 rounded-full text-sm"
        >
          Up to date
        </span>
      </div>
      
      <div class="flex items-center justify-between p-4 bg-white/5 rounded-lg">
        <div>
          <h3 class="text-white font-medium">Update Channel</h3>
          <p class="text-sm text-gray-400">{{ updateChannel }}</p>
        </div>
        <button 
          @click="$emit('edit-channel')"
          class="px-4 py-2 bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 rounded-lg transition-colors"
        >
          Change
        </button>
      </div>

      <!-- Update Progress -->
      <div v-if="isUpdating" class="mt-4 p-4 bg-white/5 rounded-lg">
        <div class="flex items-center justify-between mb-2">
          <span class="text-sm text-white">Updating system...</span>
          <span class="text-sm text-gray-400">{{ updateProgress }}%</span>
        </div>
        <div class="w-full bg-white/10 rounded-full h-2">
          <div 
            class="bg-blue-500 h-2 rounded-full transition-all duration-300"
            :style="{ width: `${updateProgress}%` }"
          ></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
  currentVersion: {
    type: String,
    default: 'v1.0.0'
  },
  updateChannel: {
    type: String,
    default: 'stable'
  }
});

const emit = defineEmits(['edit-channel']);

const isChecking = ref(false);
const isUpdating = ref(false);
const updateAvailable = ref(false);
const updateProgress = ref(0);

const checkForUpdates = async () => {
  isChecking.value = true;
  try {
    // Simulate API call to check for updates
    await new Promise(resolve => setTimeout(resolve, 2000));
    // Randomly determine if update is available (for demo)
    updateAvailable.value = Math.random() > 0.5;
  } catch (error) {
    console.error('Failed to check for updates:', error);
  } finally {
    isChecking.value = false;
  }
};

const performUpdate = async () => {
  isUpdating.value = true;
  updateProgress.value = 0;
  
  try {
    // Simulate update progress
    for (let i = 0; i <= 100; i += 10) {
      await new Promise(resolve => setTimeout(resolve, 500));
      updateProgress.value = i;
    }
    // Update complete
    updateAvailable.value = false;
    // Emit event to parent to refresh version
    emit('update-complete');
  } catch (error) {
    console.error('Failed to update:', error);
  } finally {
    isUpdating.value = false;
    updateProgress.value = 0;
  }
};
</script> 