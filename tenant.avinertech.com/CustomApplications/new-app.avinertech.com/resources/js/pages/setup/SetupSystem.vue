<template>
  <div class="space-y-8">
    <h1 class="text-3xl font-bold text-white mb-8">System Setup</h1>
    
    <!-- Setup Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- General Settings -->
      <GeneralSettings
        :app-name="appName"
        :timezone="timezone"
        :company-info="companyInfo"
        @edit="openEditModal"
      />

      <!-- Database Settings -->
      <DatabaseSettings />

      <!-- System Updates -->
      <SystemUpdates
        :current-version="currentVersion"
        :update-channel="updateChannel"
        @edit-channel="openEditModal('updateChannel')"
        @update-complete="handleUpdateComplete"
      />

      <!-- Configuration Management -->
      <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 border border-white/20">
        <h2 class="text-xl font-semibold text-white mb-4">Configuration Management</h2>
        <div class="space-y-4">
          <div class="flex items-center justify-between p-4 bg-white/5 rounded-lg">
            <div>
              <h3 class="text-white font-medium">System Configurations</h3>
              <p class="text-sm text-gray-400">Manage system-wide configurations</p>
            </div>
            <router-link 
              to="/configuration"
              class="px-4 py-2 bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 rounded-lg transition-colors"
            >
              Manage
            </router-link>
          </div>
        </div>
      </div>
    </div>

    <!-- Company Info Modal -->
    <CompanyInfoModal
      :show="showCompanyModal"
      :initial-data="companyInfo"
      @close="showCompanyModal = false"
      @save="handleCompanyInfoSave"
    />

    <!-- Edit Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
      <div class="bg-gray-800 rounded-xl p-6 w-full max-w-md border border-white/10">
        <h3 class="text-xl font-semibold text-white mb-4">{{ modalTitle }}</h3>
        <div class="space-y-4">
          <!-- App Name -->
          <div v-if="modalType === 'appName'">
            <label class="block text-sm font-medium text-gray-400 mb-2">App Name</label>
            <input 
              v-model="editValue"
              type="text"
              class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:border-blue-500"
            />
          </div>

          <!-- Timezone -->
          <div v-if="modalType === 'timezone'">
            <label class="block text-sm font-medium text-gray-400 mb-2">Time Zone</label>
            <select 
              v-model="editValue"
              class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:border-blue-500"
            >
              <option v-for="tz in timezones" :key="tz" :value="tz">{{ tz }}</option>
            </select>
          </div>

          <!-- Update Channel -->
          <div v-if="modalType === 'updateChannel'">
            <label class="block text-sm font-medium text-gray-400 mb-2">Update Channel</label>
            <select 
              v-model="editValue"
              class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:border-blue-500"
            >
              <option value="stable">Stable</option>
              <option value="beta">Beta</option>
              <option value="alpha">Alpha</option>
            </select>
          </div>
        </div>
        <div class="flex justify-end space-x-3 mt-6">
          <button 
            @click="closeModal"
            class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-lg transition-colors"
          >
            Cancel
          </button>
          <button 
            @click="saveSettings"
            class="px-4 py-2 bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 rounded-lg transition-colors"
          >
            Save Changes
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import GeneralSettings from './GeneralSettings.vue';
import DatabaseSettings from './DatabaseSettings.vue';
import SystemUpdates from './SystemUpdates.vue';
import CompanyInfoModal from './CompanyInfoModal.vue';
import DashboardLayout from './DashboardLayout.vue';

defineOptions({
  layout: DashboardLayout,
});

// State
const showModal = ref(false);
const modalType = ref('');
const modalTitle = ref('');
const editValue = ref('');
const showCompanyModal = ref(false);

// Settings
const appName = ref('My Application');
const timezone = ref('UTC');
const updateChannel = ref('stable');
const currentVersion = ref('v1.0.0');
const companyInfo = ref({
  name: 'Aviner Tech',
  host: 'demo',
  email: 'contact@avinertech.com',
  username: 'admin',
  phone: '+1 (555) 123-4567',
  address: '123 Tech Street, Silicon Valley, CA 94043'
});

// Timezone list
const timezones = [
  'UTC',
  'America/New_York',
  'America/Los_Angeles',
  'Europe/London',
  'Asia/Tokyo',
];

const openEditModal = (type) => {
  if (type === 'companyInfo') {
    showCompanyModal.value = true;
    return;
  }

  modalType.value = type;
  modalTitle.value = {
    appName: 'Edit App Name',
    timezone: 'Select Time Zone',
    updateChannel: 'Change Update Channel'
  }[type];
  
  editValue.value = {
    appName: appName.value,
    timezone: timezone.value,
    updateChannel: updateChannel.value
  }[type];
  
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  modalType.value = '';
  editValue.value = '';
};

const saveSettings = async () => {
  try {
    // Update the appropriate setting
    switch (modalType.value) {
      case 'appName':
        appName.value = editValue.value;
        break;
      case 'timezone':
        timezone.value = editValue.value;
        break;
      case 'updateChannel':
        updateChannel.value = editValue.value;
        break;
    }
    closeModal();
  } catch (error) {
    console.error('Failed to save settings:', error);
  }
};

const handleUpdateComplete = () => {
  // Update version after successful update
  currentVersion.value = 'v1.0.1'; // This would come from the API in a real app
};

const handleCompanyInfoSave = async (data) => {
  try {
    companyInfo.value = { ...data };
  } catch (error) {
    console.error('Failed to save company information:', error);
  }
};
</script> 