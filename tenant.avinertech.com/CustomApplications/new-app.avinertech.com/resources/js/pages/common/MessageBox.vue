<template>
  <Transition
    enter-active-class="transition duration-300 ease-out"
    enter-from-class="transform -translate-y-2 opacity-0"
    enter-to-class="transform translate-y-0 opacity-100"
    leave-active-class="transition duration-200 ease-in"
    leave-from-class="transform translate-y-0 opacity-100"
    leave-to-class="transform -translate-y-2 opacity-0"
  >
    <div v-if="show" 
         :class="[
           'fixed top-4 right-4 z-50 max-w-md w-full shadow-lg rounded-lg px-4 py-3',
           typeClasses[type]
         ]"
    >
      <div class="flex items-center">
        <!-- Icon -->
        <div class="flex-shrink-0">
          <svg v-if="type === 'success'" class="h-6 w-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <svg v-else-if="type === 'error'" class="h-6 w-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <svg v-else-if="type === 'warning'" class="h-6 w-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
          <svg v-else class="h-6 w-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>

        <!-- Message -->
        <div class="ml-3 flex-1">
          <p class="text-sm font-medium" :class="textColorClasses[type]">
            {{ message }}
          </p>
        </div>

        <!-- Close Button -->
        <div class="ml-4 flex-shrink-0 flex">
          <button
            @click="close"
            class="inline-flex text-gray-400 hover:text-gray-500 focus:outline-none"
          >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { ref, onUnmounted } from 'vue';

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  message: {
    type: String,
    required: true
  },
  type: {
    type: String,
    default: 'info',
    validator: (value) => ['success', 'error', 'warning', 'info'].includes(value)
  },
  duration: {
    type: Number,
    default: 5000
  }
});

const emit = defineEmits(['close']);

const typeClasses = {
  success: 'bg-green-500/20 border border-green-500/30',
  error: 'bg-red-500/20 border border-red-500/30',
  warning: 'bg-yellow-500/20 border border-yellow-500/30',
  info: 'bg-blue-500/20 border border-blue-500/30'
};

const textColorClasses = {
  success: 'text-green-300',
  error: 'text-red-300',
  warning: 'text-yellow-300',
  info: 'text-blue-300'
};

let timeout;

const close = () => {
  emit('close');
};

// Auto close after duration
if (props.duration > 0) {
  timeout = setTimeout(() => {
    close();
  }, props.duration);
}

// Cleanup timeout on component unmount
onUnmounted(() => {
  if (timeout) clearTimeout(timeout);
});
</script> 