<template>
  <div class="min-h-screen bg-gray-100">
    <NavBar />
    <main>
      <div>
        <router-view />
      </div>
    </main>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import NavBar from './components/NavBar.vue';
import { useAuthStore } from './store/auth';

const authStore = useAuthStore();

onMounted(() => {
  // Initialize auth state on app load
  authStore.initialize();
});
</script> 