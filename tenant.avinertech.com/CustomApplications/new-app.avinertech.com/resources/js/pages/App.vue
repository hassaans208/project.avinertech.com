<template>
  <div class="min-h-screen bg-gray-900">
        <router-view />
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useAuthStore } from '../store/auth';

const authStore = useAuthStore();

onMounted(() => {
  // Initialize auth state on app load
  authStore.initialize();
});
</script> 