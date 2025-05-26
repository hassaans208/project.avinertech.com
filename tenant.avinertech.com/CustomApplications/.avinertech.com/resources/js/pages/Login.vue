<template>
  <div class="h-screen min-w-screen flex flex-col items-center bg-gradient-to-br from-indigo-900 via-purple-800 to-pink-700 relative overflow-hidden">
    <!-- Floating elements -->
    <div class="absolute -right-24 -top-24 w-96 h-96 rounded-full bg-gradient-to-r from-pink-400 to-purple-400 opacity-20 blur-3xl animate-pulse-slow"></div>
    <div class="absolute -left-24 bottom-0 w-80 h-80 rounded-full bg-gradient-to-r from-blue-500 to-teal-500 opacity-20 blur-3xl animate-pulse-slow"></div>
    
    <!-- Floating curved header -->
    <div class="relative mt-32 mb-16 z-10">
      <div class="absolute inset-0 bg-gradient-to-r from-pink-500/20 to-purple-500/20 blur-xl rounded-full transform -translate-y-4 scale-110"></div>
      <div class="backdrop-blur-xl bg-white/10 border border-white/20 rounded-full px-20 py-6 shadow-2xl transform hover:scale-105 transition-all duration-300">
        <h1 class="text-5xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-pink-400 to-purple-400 drop-shadow-lg">AvinerTech</h1>
      </div>
    </div>
    
    <div class="w-full max-w-md px-4">
      <div class="text-center mb-8">
        <h2 class="text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-pink-400 to-purple-400 drop-shadow-lg">
          Welcome Back
        </h2>
        <p class="mt-2 text-white/80">
          Or
          <router-link to="/register" class="font-medium text-teal-400 hover:text-teal-300 transition-colors">
            create a new account
          </router-link>
        </p>
      </div>
      
      <!-- Error alert -->
      <div v-if="error" class="bg-red-900/30 backdrop-blur-sm border border-red-500/20 text-white p-4 mb-6 rounded-xl w-full" role="alert">
        <p>{{ error }}</p>
      </div>

      <form class="backdrop-blur-xl bg-white/5 rounded-2xl shadow-2xl p-8 border border-white/20 w-full" @submit.prevent="login">
        <div class="space-y-5">
          <div>
            <label for="email-address" class="sr-only">Email address</label>
            <input
              id="email-address"
              name="email"
              type="email"
              autocomplete="email"
              required
              v-model="formData.email"
              class="appearance-none relative block w-full px-4 py-3 bg-white/5 backdrop-blur-sm border border-white/20 text-white placeholder-white/60 rounded-xl focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent"
              placeholder="Email address"
              :class="{ 'border-red-500': formErrors.email }"
            />
            <p v-if="formErrors.email" class="mt-1 text-sm text-red-300">{{ formErrors.email[0] }}</p>
          </div>
          <div>
            <label for="password" class="sr-only">Password</label>
            <input
              id="password"
              name="password"
              type="password"
              autocomplete="current-password"
              required
              v-model="formData.password"
              class="appearance-none relative block w-full px-4 py-3 bg-white/5 backdrop-blur-sm border border-white/20 text-white placeholder-white/60 rounded-xl focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent"
              placeholder="Password"
              :class="{ 'border-red-500': formErrors.password }"
            />
            <p v-if="formErrors.password" class="mt-1 text-sm text-red-300">{{ formErrors.password[0] }}</p>
          </div>
        </div>

        <div class="flex items-center justify-between mt-5">
          <div class="flex items-center">
            <input
              id="remember-me"
              name="remember-me"
              type="checkbox"
              v-model="formData.remember"
              class="h-4 w-4 text-purple-600 focus:ring-pink-500 bg-white/5 rounded"
            />
            <label for="remember-me" class="ml-2 block text-sm text-white/80">
              Remember me
            </label>
          </div>
        </div>

        <div class="mt-6">
          <button
            type="submit"
            class="group relative w-full flex justify-center px-8 py-3 rounded-full bg-gradient-to-r from-pink-500 to-purple-600 text-white shadow-lg hover:shadow-pink-500/30 transition-all hover:-translate-y-1 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500"
            :disabled="authStore.loading"
          >
            <span v-if="authStore.loading" class="absolute left-4 inset-y-0 flex items-center">
              <div class="animate-spin h-5 w-5 border-t-2 border-b-2 border-white rounded-full"></div>
            </span>
            <span v-else class="absolute left-4 inset-y-0 flex items-center">
              <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
              </svg>
            </span>
            Sign in
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../store/auth';

const router = useRouter();
const authStore = useAuthStore();

const formData = ref({
  email: '',
  password: '',
  remember: false
});

const error = ref('');
const formErrors = ref({});

async function login() {
  error.value = '';
  formErrors.value = {};
  
  const result = await authStore.login(formData.value);
  
  if (result.success) {
    // Redirect to dashboard/home after successful login
    router.push('/');
  } else {
    if (result.errors) {
      formErrors.value = result.errors;
    } else if (result.error) {
      error.value = result.error;
    } else {
      error.value = 'An unexpected error occurred. Please try again.';
    }
  }
}
</script>

<style scoped>
@keyframes pulse-slow {
  0%, 100% {
    opacity: 0.2;
  }
  50% {
    opacity: 0.3;
  }
}

.animate-pulse-slow {
  animation: pulse-slow 4s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style> 