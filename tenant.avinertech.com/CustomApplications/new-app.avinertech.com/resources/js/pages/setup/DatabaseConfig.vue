<template>
  <div class="space-y-8">
    <!-- Message Box -->
    <MessageBox
      :show="!!message"
      :message="message"
      :type="messageType"
      @close="message = null"
    />

    <div class="flex items-center justify-between">
      <h1 class="text-3xl font-bold text-white">Database Configuration</h1>
      <!-- <button 
        @click="saveConfig"
        class="px-4 py-2 bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 rounded-lg transition-colors"
        :disabled="isSaving || isTesting"
      >
        {{ isSaving ? 'Saving...' : 'Save Configuration' }}
      </button> -->
    </div>

    <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 border border-white/20">
      <h2 class="text-xl font-semibold text-white mb-4">Database Configuration</h2>
      <div class="space-y-4">
        <div class="flex items-center justify-between p-4 bg-white/5 rounded-lg">
          <div>
            <h3 class="text-white font-medium">Database Settings</h3>
            <p class="text-sm text-gray-400">Configure database connection</p>
          </div>
          <router-link 
            to="/setup/database"
            class="px-4 py-2 bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 rounded-lg transition-colors"
          >
            Configure
          </router-link>
        </div>

        <div class="flex items-center justify-between p-4 bg-white/5 rounded-lg">
          <div>
            <h3 class="text-white font-medium">Connection Status</h3>
            <p class="text-sm" :class="connectionStatusClass">{{ connectionStatus }}</p>
          </div>
          <button 
            @click="testConnection"
            :disabled="isTesting"
            class="px-4 py-2 bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center"
          >
            <svg v-if="isTesting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ isTesting ? 'Testing...' : 'Test Connection' }}
          </button>
        </div>
      </div>
    </div>

    <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 border border-white/20">
      <form @submit.prevent="saveConfig" class="space-y-6">
        <!-- Database Host -->
        <div>
          <label class="block text-sm font-medium text-gray-400 mb-2">Database Host</label>
          <input 
            v-model="config.DATABASE_HOST"
            type="text"
            class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:border-blue-500"
            placeholder="localhost"
          />
        </div>

        <!-- Database Port -->
        <div>
          <label class="block text-sm font-medium text-gray-400 mb-2">Database Port</label>
          <input 
            v-model="config.DATABASE_PORT"
            type="number"
            class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:border-blue-500"
            placeholder="3306"
          />
        </div>

        <!-- Database Name -->
        <div>
          <label class="block text-sm font-medium text-gray-400 mb-2">Database Name</label>
          <input 
            v-model="config.DATABASE_NAME"
            type="text"
            class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:border-blue-500"
            placeholder="my_database"
          />
        </div>

        <!-- Database Username -->
        <div>
          <label class="block text-sm font-medium text-gray-400 mb-2">Database Username</label>
          <input 
            v-model="config.DATABASE_USERNAME"
            type="text"
            class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:border-blue-500"
            placeholder="db_user"
          />
        </div>

        <!-- Database Password -->
        <div>
          <label class="block text-sm font-medium text-gray-400 mb-2">Database Password</label>
          <div class="relative">
            <input 
              v-model="config.DATABASE_PASSWORD"
              :type="showPassword ? 'text' : 'password'"
              class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:border-blue-500"
              placeholder="••••••••"
            />
            <button 
              type="button"
              @click="showPassword = !showPassword"
              class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white"
            >
              <svg v-if="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
              </svg>
              <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Test Connection Button -->
        <div class="pt-4">
          <button 
            type="button"
            @click="testConnection"
            class="w-full px-4 py-2 bg-green-500/20 hover:bg-green-500/30 text-green-300 rounded-lg transition-colors"
            :disabled="isTesting || isSaving"
          >
            {{ isTesting ? 'Testing Connection...' : 'Test Connection' }}
          </button>
        </div>
      </form>
    </div>

    <!-- Connection Status -->
    <div v-if="connectionStatus" 
         :class="[
           'p-4 rounded-lg border',
           connectionStatus.success 
             ? 'bg-green-500/20 border-green-500/30 text-green-300' 
             : 'bg-red-500/20 border-red-500/30 text-red-300'
         ]"
    >
      {{ connectionStatus.message }}
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import SchemaService from '../../services/SchemaService';
import MessageBox from '../common/MessageBox.vue';

const router = useRouter();
const isSaving = ref(false);
const isTesting = ref(false);
const showPassword = ref(false);
const connectionStatus = ref(null);
const message = ref(null);
const messageType = ref('info');

const config = ref({
  DATABASE_HOST: '',
  DATABASE_PORT: '',
  DATABASE_NAME: '',
  DATABASE_USERNAME: '',
  DATABASE_PASSWORD: ''
});

const connectionStatusClass = computed(() => ({
  'text-red-400': connectionStatus.value === 'Not Connected',
  'text-yellow-400': connectionStatus.value === 'Testing Connection',
  'text-green-400': connectionStatus.value === 'Connected'
}));

// Load configuration on mount
onMounted(async () => {
  try {
    const host = window.location.host;
    const response = await SchemaService.getTenantConfig(host);
    if (response.status === 'success' && response.data) {
      // Map the response data to our config object
      for (const data of response.data) {
        config.value[data?.name] = data?.value || '';
      }
    }
  } catch (error) {
    showMessage('Failed to load database configuration', 'error');
  }
});

const showMessage = (msg, type = 'info') => {
  message.value = msg;
  messageType.value = type;
};

const testConnection = async () => {
  isTesting.value = true;
  connectionStatus.value = 'Testing Connection';
  
  try {
    // Simulate API call to test connection
    await new Promise(resolve => setTimeout(resolve, 2000));
    // Randomly determine connection status (for demo)
    connectionStatus.value = Math.random() > 0.3 ? 'Connected' : 'Connection Failed';
  } catch (error) {
    console.error('Failed to test connection:', error);
    connectionStatus.value = 'Connection Failed';
  } finally {
    isTesting.value = false;
  }
};

const saveConfig = async () => {
  isSaving.value = true;
  
  try {
    // Test connection before saving
    await testConnection();
    
    if (connectionStatus.value?.success) {
      // Save configuration
      const host = window.location.host;
      await SchemaService.storeSchema(host, []);
      showMessage('Database configuration saved successfully', 'success');
      router.push('/setup');
    } else {
      throw new Error('Please ensure the database connection is valid before saving');
    }
  } catch (error) {
    showMessage(error.message || 'Failed to save database configuration', 'error');
  } finally {
    isSaving.value = false;
  }
};
</script> 