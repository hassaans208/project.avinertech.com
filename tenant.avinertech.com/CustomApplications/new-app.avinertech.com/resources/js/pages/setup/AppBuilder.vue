<template>
  <div class="space-y-8">
    <h1 class="text-3xl font-bold text-white mb-8">Application Builder</h1>

    <!-- Plugins Section -->
    <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 border border-white/20">
      <h2 class="text-xl font-semibold text-white mb-6">Available Plugins</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <!-- Authentication Plugin -->
        <div class="bg-white/5 rounded-lg p-4 border border-white/10">
          <div class="flex items-start justify-between">
            <div>
              <h3 class="text-white font-medium">Authentication</h3>
              <p class="text-sm text-gray-400 mt-1">User authentication and authorization system</p>
            </div>
            <div class="flex items-center space-x-2">
              <button 
                v-if="!plugins.auth.integrated"
                @click="integratePlugin('auth')"
                class="px-3 py-1.5 bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 rounded-lg transition-colors text-sm"
              >
                Integrate
              </button>
              <div v-else class="flex items-center text-green-400">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="text-sm">Integrated</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Mailer Plugin -->
        <div class="bg-white/5 rounded-lg p-4 border border-white/10">
          <div class="flex items-start justify-between">
            <div>
              <h3 class="text-white font-medium">Mailer</h3>
              <p class="text-sm text-gray-400 mt-1">Email sending and management system</p>
            </div>
            <div class="flex items-center space-x-2">
              <button 
                v-if="!plugins.mailer.integrated"
                @click="integratePlugin('mailer')"
                class="px-3 py-1.5 bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 rounded-lg transition-colors text-sm"
              >
                Integrate
              </button>
              <button 
                v-if="plugins.mailer.integrated && !plugins.mailer.configured"
                @click="showMailerSetup = true"
                class="px-3 py-1.5 bg-yellow-500/20 hover:bg-yellow-500/30 text-yellow-300 rounded-lg transition-colors text-sm"
              >
                Setup Required
              </button>
              <div v-if="plugins.mailer.configured" class="flex items-center text-green-400">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="text-sm">Configured</span>
              </div>
            </div>
          </div>
          <button 
            @click="showPluginInfo('mailer')"
            class="text-sm text-blue-400 hover:text-blue-300 mt-2 transition-colors"
          >
            Learn More
          </button>
        </div>

        <!-- Payments Plugin -->
        <div class="bg-white/5 rounded-lg p-4 border border-white/10">
          <div class="flex items-start justify-between">
            <div>
              <h3 class="text-white font-medium">Payments</h3>
              <p class="text-sm text-gray-400 mt-1">Payment processing and management</p>
            </div>
            <div class="flex items-center space-x-2">
              <button 
                v-if="!plugins.payments.integrated"
                @click="integratePlugin('payments')"
                class="px-3 py-1.5 bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 rounded-lg transition-colors text-sm"
              >
                Integrate
              </button>
              <div v-else class="flex items-center text-green-400">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="text-sm">Integrated</span>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white/5 rounded-lg p-4 border border-white/10">
          <div class="flex items-start justify-between">
            <div>
              <h3 class="text-white font-medium">Multi Language</h3>
              <p class="text-sm text-gray-400 mt-1">Multiple language support for your application</p>
            </div>
            <div class="flex items-center space-x-2">
              <button 
                v-if="!plugins.auth.integrated"
                @click="integratePlugin('auth')"
                class="px-3 py-1.5 bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 rounded-lg transition-colors text-sm"
              >
                Integrate
              </button>
              <div v-else class="flex items-center text-green-400">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="text-sm">Integrated</span>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white/5 rounded-lg p-4 border border-white/10">
          <div class="flex items-start justify-between">
            <div>
              <h3 class="text-white font-medium">Multi Language</h3>
              <p class="text-sm text-gray-400 mt-1">Multiple language support for your application</p>
            </div>
            <div class="flex items-center space-x-2">
              <button 
                v-if="!plugins.auth.integrated"
                @click="integratePlugin('auth')"
                class="px-3 py-1.5 bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 rounded-lg transition-colors text-sm"
              >
                Integrate
              </button>
              <div v-else class="flex items-center text-green-400">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="text-sm">Integrated</span>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white/5 rounded-lg p-4 border border-white/10">
          <div class="flex items-start justify-between">
            <div>
              <h3 class="text-white font-medium">Multi Currency</h3>
              <p class="text-sm text-gray-400 mt-1">Multiple currency support for your application</p>
            </div>
            <div class="flex items-center space-x-2">
              <button 
                v-if="!plugins.auth.integrated"
                @click="integratePlugin('auth')"
                class="px-3 py-1.5 bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 rounded-lg transition-colors text-sm"
              >
                Integrate
              </button>
              <div v-else class="flex items-center text-green-400">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="text-sm">Integrated</span>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white/5 rounded-lg p-4 border border-white/10">
          <div class="flex items-start justify-between">
            <div>
              <h3 class="text-white font-medium">Roles and Permissions</h3>
              <p class="text-sm text-gray-400 mt-1">Roles and permissions management for your application</p>
            </div>
            <div class="flex items-center space-x-2">
              <button 
                v-if="!plugins.auth.integrated"
                @click="integratePlugin('auth')"
                class="px-3 py-1.5 bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 rounded-lg transition-colors text-sm"
              >
                Integrate
              </button>
              <div v-else class="flex items-center text-green-400">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="text-sm">Integrated</span>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white/5 rounded-lg p-4 border border-white/10">
          <div class="flex items-start justify-between">
            <div>
              <h3 class="text-white font-medium">Notifications</h3>
              <p class="text-sm text-gray-400 mt-1">Notifications management for your application</p>
            </div>
            <div class="flex items-center space-x-2">
              <button 
                v-if="!plugins.auth.integrated"
                @click="integratePlugin('auth')"
                class="px-3 py-1.5 bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 rounded-lg transition-colors text-sm"
              >
                Integrate
              </button>
              <div v-else class="flex items-center text-green-400">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="text-sm">Integrated</span>
              </div>
            </div>
          </div>
        </div>


        <div class="bg-white/5 rounded-lg p-4 border border-white/10">
          <div class="flex items-start justify-between">
            <div>
              <h3 class="text-white font-medium">Real Time Analytics</h3>
              <p class="text-sm text-gray-400 mt-1">Real time analytics for your application</p>
            </div>
            <div class="flex items-center space-x-2">
              <button 
                v-if="!plugins.auth.integrated"
                @click="integratePlugin('auth')"
                class="px-3 py-1.5 bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 rounded-lg transition-colors text-sm"
              >
                Integrate
              </button>
              <div v-else class="flex items-center text-green-400">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="text-sm">Integrated</span>
              </div>
            </div>
          </div>
        </div>

        
      </div>
    </div>

    <!-- Theme Selection -->
    <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 border border-white/20">
      <h2 class="text-xl font-semibold text-white mb-6">Theme Selection</h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div 
          v-for="theme in themes" 
          :key="theme.id"
          class="relative bg-white/5 rounded-lg p-4 border cursor-pointer transition-all"
          :class="[
            selectedTheme === theme.id 
              ? 'border-blue-500 bg-blue-500/10' 
              : 'border-white/10 hover:border-white/20'
          ]"
          @click="selectedTheme = theme.id"
        >
          <div class="aspect-video bg-gray-800 rounded-lg mb-3 overflow-hidden">
            <img :src="theme.preview" :alt="theme.name" class="w-full h-full object-cover" />
          </div>
          <h3 class="text-white font-medium">{{ theme.name }}</h3>
          <p class="text-sm text-gray-400 mt-1">{{ theme.description }}</p>
          <div 
            v-if="selectedTheme === theme.id"
            class="absolute top-2 right-2 w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center"
          >
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Schema Overview -->
    <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 border border-white/20">
      <h2 class="text-xl font-semibold text-white mb-6">Schema Overview</h2>
      <div class="space-y-4">
        <div v-for="model in schemaModels" :key="model.name" class="bg-white/5 rounded-lg p-4">
          <h3 class="text-white font-medium">{{ model.name }}</h3>
          <div class="mt-2 space-y-2">
            <div v-for="field in model.fields" :key="field.name" class="flex items-center text-sm text-gray-400">
              <span class="w-32">{{ field.name }}</span>
              <span class="text-gray-500">→</span>
              <span class="text-blue-400">{{ field.type }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Build Button -->
    <div class="flex justify-end">
      <button 
        @click="startBuild"
        class="px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors flex items-center"
        :disabled="isBuilding"
      >
        <svg v-if="isBuilding" class="animate-spin -ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        {{ isBuilding ? `Building (${buildProgress}%)` : 'Build Application' }}
      </button>
    </div>

    <!-- Mailer Setup Modal -->
    <div v-if="showMailerSetup" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
      <div class="bg-gray-800 rounded-xl p-6 w-full max-w-md border border-white/10">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-xl font-semibold text-white">Mailer Configuration</h3>
          <button 
            @click="showMailerSetup = false"
            class="text-gray-400 hover:text-white transition-colors"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        <form @submit.prevent="configureMailer" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-400 mb-2">SMTP Host</label>
            <input 
              v-model="mailerConfig.host"
              type="text"
              class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:border-blue-500"
              required
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-400 mb-2">SMTP Port</label>
            <input 
              v-model="mailerConfig.port"
              type="number"
              class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:border-blue-500"
              required
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-400 mb-2">Username</label>
            <input 
              v-model="mailerConfig.username"
              type="text"
              class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:border-blue-500"
              required
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-400 mb-2">Password</label>
            <input 
              v-model="mailerConfig.password"
              type="password"
              class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:border-blue-500"
              required
            />
          </div>
          <div class="flex justify-end space-x-3 mt-6">
            <button 
              type="button"
              @click="showMailerSetup = false"
              class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-lg transition-colors"
            >
              Cancel
            </button>
            <button 
              type="submit"
              class="px-4 py-2 bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 rounded-lg transition-colors"
            >
              Save Configuration
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Plugin Info Modal -->
    <div v-if="showPluginInfoModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
      <div class="bg-gray-800 rounded-xl p-6 w-full max-w-2xl border border-white/10">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-xl font-semibold text-white">{{ selectedPluginInfo.name }} Plugin</h3>
          <button 
            @click="showPluginInfoModal = false"
            class="text-gray-400 hover:text-white transition-colors"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        <div class="prose prose-invert max-w-none">
          <h4 class="text-lg font-medium text-white mb-2">Overview</h4>
          <p class="text-gray-400">{{ selectedPluginInfo.overview }}</p>
          
          <h4 class="text-lg font-medium text-white mt-6 mb-2">Features</h4>
          <ul class="list-disc list-inside text-gray-400 space-y-2">
            <li v-for="feature in selectedPluginInfo.features" :key="feature">{{ feature }}</li>
          </ul>
          
          <h4 class="text-lg font-medium text-white mt-6 mb-2">Setup Instructions</h4>
          <div class="text-gray-400 space-y-4">
            <p v-for="(step, index) in selectedPluginInfo.setupSteps" :key="index">
              {{ index + 1 }}. {{ step }}
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Build Progress Modal -->
    <div v-if="isBuilding" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
      <div class="bg-gray-800 rounded-xl p-6 w-full max-w-md border border-white/10">
        <h3 class="text-xl font-semibold text-white mb-4">Building Application</h3>
        <div class="space-y-4">
          <div class="h-2 bg-white/10 rounded-full overflow-hidden">
            <div 
              class="h-full bg-blue-500 transition-all duration-300"
              :style="{ width: `${buildProgress}%` }"
            ></div>
          </div>
          <p class="text-center text-gray-400">{{ buildStatus }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import DashboardLayout from './DashboardLayout.vue';

defineOptions({
  layout: DashboardLayout,
});
// Plugin State
const plugins = ref({
  auth: { integrated: false },
  mailer: { integrated: false, configured: false },
  payments: { integrated: false },
  database: { integrated: false },
  notifications: { integrated: false },
  analytics: { integrated: false },
  billing: { integrated: false },
  marketing: { integrated: false },
  social: { integrated: false },
  payments: { integrated: false },
  notifications: { integrated: false },
  multiAuth: { integrated: false },
  multiLang: { integrated: false },
  multiCurrency: { integrated: false },
  multiTheme: { integrated: false },
  multiDatabase: { integrated: false },
  multiNotification: { integrated: false },
  multiAnalytics: { integrated: false },
});

// Theme State
const themes = [
  {
    id: 'modern',
    name: 'Modern Dark',
    description: 'Clean, modern dark theme with blue accents',
    preview: '/themes/modern-dark.jpg'
  },
  {
    id: 'light',
    name: 'Light Professional',
    description: 'Professional light theme with subtle shadows',
    preview: '/themes/light-pro.jpg'
  },
  {
    id: 'minimal',
    name: 'Minimal',
    description: 'Minimalist design with focus on content',
    preview: '/themes/minimal.jpg'
  }
];
const selectedTheme = ref('modern');

// Schema State
const schemaModels = [
  {
    name: 'User',
    fields: [
      { name: 'id', type: 'integer' },
      { name: 'name', type: 'string' },
      { name: 'email', type: 'string' },
      { name: 'created_at', type: 'datetime' }
    ]
  },
  {
    name: 'Post',
    fields: [
      { name: 'id', type: 'integer' },
      { name: 'title', type: 'string' },
      { name: 'content', type: 'text' },
      { name: 'user_id', type: 'integer' }
    ]
  }
];

// Modal States
const showMailerSetup = ref(false);
const showPluginInfoModal = ref(false);
const selectedPluginInfo = ref({});

// Build State
const isBuilding = ref(false);
const buildProgress = ref(0);
const buildStatus = ref('Initializing build process...');

// Mailer Configuration
const mailerConfig = ref({
  host: '',
  port: '',
  username: '',
  password: ''
});

// Plugin Info
const pluginInfo = {
  mailer: {
    name: 'Mailer',
    overview: 'A comprehensive email management system that allows you to send, receive, and manage emails within your application.',
    features: [
      'SMTP and API-based email sending',
      'Email templates and queue management',
      'Email tracking and analytics',
      'Bulk email sending capabilities'
    ],
    setupSteps: [
      'Configure your SMTP server details',
      'Set up email templates',
      'Configure email queue settings',
      'Test the email configuration'
    ]
  }
};

// Methods
const integratePlugin = (plugin) => {
  plugins.value[plugin].integrated = true;
  if (plugin === 'mailer') {
    showMailerSetup.value = true;
  }
};

const configureMailer = async () => {
  try {
    // Here you would typically make an API call to save the mailer configuration
    plugins.value.mailer.configured = true;
    showMailerSetup.value = false;
  } catch (error) {
    console.error('Failed to configure mailer:', error);
  }
};

const showPluginInfo = (plugin) => {
  selectedPluginInfo.value = pluginInfo[plugin];
  showPluginInfoModal.value = true;
};

const startBuild = async () => {
  isBuilding.value = true;
  buildProgress.value = 0;
  
  const steps = [
    { progress: 20, status: 'Installing plugins...' },
    { progress: 40, status: 'Configuring theme...' },
    { progress: 60, status: 'Setting up database...' },
    { progress: 80, status: 'Generating application files...' },
    { progress: 100, status: 'Finalizing build...' }
  ];

  for (const step of steps) {
    await new Promise(resolve => setTimeout(resolve, 1500));
    buildProgress.value = step.progress;
    buildStatus.value = step.status;
  }

  // Simulate final delay
  await new Promise(resolve => setTimeout(resolve, 1000));
  isBuilding.value = false;
  // Here you would typically redirect to the dashboard or show a success message
};
</script> 