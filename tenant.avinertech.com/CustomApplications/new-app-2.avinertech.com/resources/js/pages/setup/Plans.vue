<template>
  <div class="h-screen flex flex-col overflow-hidden">
    <!-- Header Section -->
    <div class="flex-none space-y-8 p-6">
      <!-- Breadcrumbs -->
      <Breadcrumbs :items="[
        { name: 'Setup', path: 'setup.index' },
        { name: 'Subscription Plans' }
      ]" />

      <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-white">Subscription Plans</h1>
        <button 
          @click="openBillingPortal"
          class="px-4 py-2 bg-purple-500/20 hover:bg-purple-500/30 text-purple-300 rounded-lg transition-colors"
        >
          Manage Subscription
        </button>
      </div>
    </div>

    <!-- Plans Grid -->
    <div class="flex-1 overflow-hidden px-6 pb-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 h-full">
        <!-- Free Plan -->
        <div class="bg-white/5 backdrop-blur-lg rounded-xl border border-white/10 p-6 flex flex-col">
          <div class="flex-1">
            <h3 class="text-xl font-semibold text-white mb-2">Free</h3>
            <p class="text-gray-400 text-sm mb-4">Perfect for getting started</p>
            <div class="text-3xl font-bold text-white mb-6">
              $0<span class="text-lg text-gray-400">/month</span>
            </div>
            <ul class="space-y-3 mb-6">
              <li class="flex items-center text-gray-300">
                <svg class="w-5 h-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Basic Schema Management
              </li>
              <li class="flex items-center text-gray-300">
                <svg class="w-5 h-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Up to 5 Models
              </li>
              <li class="flex items-center text-gray-300">
                <svg class="w-5 h-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Basic Relationships
              </li>
              <li class="flex items-center text-gray-300">
                <svg class="w-5 h-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Standard Support
              </li>
            </ul>
          </div>
          <button 
            @click="selectPlan('free')"
            class="w-full px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-lg transition-colors"
            :class="{ 'opacity-50 cursor-not-allowed': currentPlan === 'free' }"
            :disabled="currentPlan === 'free'"
          >
            {{ currentPlan === 'free' ? 'Current Plan' : 'Select Plan' }}
          </button>
        </div>

        <!-- Pro Plan -->
        <div class="bg-white/5 backdrop-blur-lg rounded-xl border border-white/10 p-6 flex flex-col relative">
          <div class="absolute -top-3 left-1/2 transform -translate-x-1/2">
            <span class="bg-blue-500 text-white text-xs px-3 py-1 rounded-full">Popular</span>
          </div>
          <div class="flex-1">
            <h3 class="text-xl font-semibold text-white mb-2">Pro</h3>
            <p class="text-gray-400 text-sm mb-4">For growing businesses</p>
            <div class="text-3xl font-bold text-white mb-6">
              $5<span class="text-lg text-gray-400">/month</span>
            </div>
            <ul class="space-y-3 mb-6">
              <li class="flex items-center text-gray-300">
                <svg class="w-5 h-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Everything in Free
              </li>
              <li class="flex items-center text-gray-300">
                <svg class="w-5 h-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Unlimited Models
              </li>
              <li class="flex items-center text-gray-300">
                <svg class="w-5 h-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Advanced Relationships
              </li>
              <li class="flex items-center text-gray-300">
                <svg class="w-5 h-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Priority Support
              </li>
              <li class="flex items-center text-gray-300">
                <svg class="w-5 h-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Custom Validators
              </li>
            </ul>
          </div>
          <button 
            @click="selectPlan('pro')"
            class="w-full px-4 py-2 bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 rounded-lg transition-colors"
            :class="{ 'opacity-50 cursor-not-allowed': currentPlan === 'pro' }"
            :disabled="currentPlan === 'pro'"
          >
            {{ currentPlan === 'pro' ? 'Current Plan' : 'Select Plan' }}
          </button>
        </div>

        <!-- Premium Plan -->
        <div class="bg-white/5 backdrop-blur-lg rounded-xl border border-white/10 p-6 flex flex-col">
          <div class="flex-1">
            <h3 class="text-xl font-semibold text-white mb-2">Premium</h3>
            <p class="text-gray-400 text-sm mb-4">For enterprise needs</p>
            <div class="text-3xl font-bold text-white mb-6">
              $20<span class="text-lg text-gray-400">/month</span>
            </div>
            <ul class="space-y-3 mb-6">
              <li class="flex items-center text-gray-300">
                <svg class="w-5 h-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Everything in Pro
              </li>
              <li class="flex items-center text-gray-300">
                <svg class="w-5 h-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Column Logic
              </li>
              <li class="flex items-center text-gray-300">
                <svg class="w-5 h-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Custom Migrations
              </li>
              <li class="flex items-center text-gray-300">
                <svg class="w-5 h-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                24/7 Support
              </li>
              <li class="flex items-center text-gray-300">
                <svg class="w-5 h-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                API Access
              </li>
              <li class="flex items-center text-gray-300">
                <svg class="w-5 h-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Custom Integrations
              </li>
            </ul>
          </div>
          <button 
            @click="selectPlan('premium')"
            class="w-full px-4 py-2 bg-purple-500/20 hover:bg-purple-500/30 text-purple-300 rounded-lg transition-colors"
            :class="{ 'opacity-50 cursor-not-allowed': currentPlan === 'premium' }"
            :disabled="currentPlan === 'premium'"
          >
            {{ currentPlan === 'premium' ? 'Current Plan' : 'Select Plan' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Subscription Modal -->
    <div v-if="showSubscriptionModal" class="fixed inset-0 z-50 overflow-hidden">
      <!-- Backdrop -->
      <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="closeSubscriptionModal"></div>

      <!-- Modal Content -->
      <div class="fixed inset-0 flex items-center justify-center p-6">
        <div class="bg-gray-800 rounded-xl border border-white/10 w-full max-w-md">
          <div class="p-6">
            <h3 class="text-xl font-semibold text-white mb-4">Subscribe to {{ selectedPlanName }}</h3>
            <p class="text-gray-400 mb-6">Complete your subscription to access premium features.</p>
            
            <!-- Payment Form -->
            <form @submit.prevent="processSubscription" class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-400 mb-2">Card Number</label>
                <input 
                  type="text"
                  class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:border-blue-500"
                  placeholder="1234 5678 9012 3456"
                />
              </div>
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-400 mb-2">Expiry Date</label>
                  <input 
                    type="text"
                    class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:border-blue-500"
                    placeholder="MM/YY"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-400 mb-2">CVC</label>
                  <input 
                    type="text"
                    class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:border-blue-500"
                    placeholder="123"
                  />
                </div>
              </div>
              <div class="flex justify-end space-x-3 pt-4">
                <button
                  type="button"
                  @click="closeSubscriptionModal"
                  class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-lg transition-colors"
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  class="px-4 py-2 bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 rounded-lg transition-colors"
                >
                  Subscribe
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import Breadcrumbs from '../common/Breadcrumbs.vue';
import DashboardLayout from './DashboardLayout.vue';

defineOptions({
  layout: DashboardLayout,
});

const currentPlan = ref('free');
const showSubscriptionModal = ref(false);
const selectedPlan = ref(null);

const selectedPlanName = computed(() => {
  switch (selectedPlan.value) {
    case 'pro':
      return 'Pro Plan';
    case 'premium':
      return 'Premium Plan';
    default:
      return 'Free Plan';
  }
});

const selectPlan = (plan) => {
  if (plan === currentPlan.value) return;
  selectedPlan.value = plan;
  showSubscriptionModal.value = true;
};

const closeSubscriptionModal = () => {
  showSubscriptionModal.value = false;
  selectedPlan.value = null;
};

const processSubscription = async () => {
  // Implement subscription logic here
  try {
    // Process payment and update subscription
    currentPlan.value = selectedPlan.value;
    showMessage('Subscription updated successfully', 'success');
    closeSubscriptionModal();
  } catch (error) {
    showMessage('Failed to process subscription', 'error');
  }
};

const openBillingPortal = () => {
  // Implement billing portal logic
};

const showMessage = (msg, type = 'info') => {
  // Implement message display logic
};
</script>

<style scoped>
.backdrop-blur-lg {
  backdrop-filter: blur(12px);
}

.bg-white\/5 {
  background-color: rgba(255, 255, 255, 0.05);
}

.border-white\/10 {
  border-color: rgba(255, 255, 255, 0.1);
}

.hover\:bg-white\/20:hover {
  background-color: rgba(255, 255, 255, 0.1);
}

.hover\:bg-blue-500\/30:hover {
  background-color: rgba(59, 130, 246, 0.3);
}

.hover\:bg-purple-500\/30:hover {
  background-color: rgba(168, 85, 247, 0.3);
}
</style> 