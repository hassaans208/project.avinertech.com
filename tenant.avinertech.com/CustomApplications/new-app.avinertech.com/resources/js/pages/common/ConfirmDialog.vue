<template>
  <Transition
    enter-active-class="transition duration-300 ease-out"
    enter-from-class="transform scale-95 opacity-0"
    enter-to-class="transform scale-100 opacity-100"
    leave-active-class="transition duration-200 ease-in"
    leave-from-class="transform scale-100 opacity-100"
    leave-to-class="transform scale-95 opacity-0"
  >
    <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex min-h-screen items-center justify-center p-4 text-center">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="onCancel"></div>

        <!-- Dialog -->
        <div class="relative transform overflow-hidden rounded-lg bg-gray-800 border border-white/10 p-6 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
          <!-- Icon -->
          <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full" :class="iconClasses[type]">
            <svg v-if="type === 'warning'" class="h-6 w-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <svg v-else-if="type === 'danger'" class="h-6 w-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <svg v-else class="h-6 w-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>

          <!-- Title -->
          <div class="mt-3 text-center sm:mt-5">
            <h3 class="text-lg font-medium leading-6 text-white">
              {{ title }}
            </h3>
            <div class="mt-2">
              <p class="text-sm text-gray-400">
                {{ message }}
              </p>
            </div>
          </div>

          <!-- Buttons -->
          <div class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
            <button
              type="button"
              class="inline-flex w-full justify-center rounded-lg px-4 py-2 text-base font-medium text-white shadow-sm focus:outline-none sm:col-start-2"
              :class="confirmButtonClasses[type]"
              @click="onConfirm"
            >
              {{ confirmText }}
            </button>
            <button
              type="button"
              class="mt-3 inline-flex w-full justify-center rounded-lg px-4 py-2 text-base font-medium text-gray-300 shadow-sm hover:bg-white/5 focus:outline-none sm:col-start-1 sm:mt-0"
              @click="onCancel"
            >
              {{ cancelText }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  title: {
    type: String,
    required: true
  },
  message: {
    type: String,
    required: true
  },
  type: {
    type: String,
    default: 'warning',
    validator: (value) => ['warning', 'danger', 'info'].includes(value)
  },
  confirmText: {
    type: String,
    default: 'Confirm'
  },
  cancelText: {
    type: String,
    default: 'Cancel'
  }
});

const emit = defineEmits(['confirm', 'cancel']);

const iconClasses = {
  warning: 'bg-yellow-500/20',
  danger: 'bg-red-500/20',
  info: 'bg-blue-500/20'
};

const confirmButtonClasses = {
  warning: 'bg-yellow-500/20 hover:bg-yellow-500/30 text-yellow-300',
  danger: 'bg-red-500/20 hover:bg-red-500/30 text-red-300',
  info: 'bg-blue-500/20 hover:bg-blue-500/30 text-blue-300'
};

const onConfirm = () => {
  emit('confirm');
};

const onCancel = () => {
  emit('cancel');
};
</script> 