<template>
  <div class="space-y-8">
    <!-- Message Box -->
    <MessageBox
      :show="!!message"
      :message="message"
      :type="messageType"
      @close="message = null"
    />

    <h1 class="text-3xl font-bold text-white mb-8">Configuration Management</h1>
    
    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
      <!-- Sidebar -->
      <div class="lg:col-span-1 space-y-6">
        <!-- Configuration Types -->
        <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 border border-white/20">
          <h2 class="text-xl font-semibold text-white mb-4">Configuration Types</h2>
          <div class="space-y-2">
            <button 
              v-for="type in configTypes" 
              :key="type.value"
              @click="selectedType = type.value; loadConfigurations()"
              class="w-full text-left px-4 py-3 rounded-lg transition-colors"
              :class="[
                selectedType === type.value 
                  ? 'bg-blue-500/20 text-blue-300' 
                  : 'text-gray-400 hover:bg-white/5 hover:text-white'
              ]"
            >
              {{ type.label }}
            </button>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 border border-white/20">
          <h2 class="text-xl font-semibold text-white mb-4">Quick Actions</h2>
          <div class="space-y-2">
            <button 
              @click="openModal()"
              class="w-full px-4 py-3 bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 rounded-lg transition-colors flex items-center justify-center"
            >
              <span class="mr-2">+</span> Add New Configuration
            </button>
            <button 
              @click="clearCache"
              class="w-full px-4 py-3 bg-red-500/20 hover:bg-red-500/30 text-red-300 rounded-lg transition-colors flex items-center justify-center"
            >
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
              Clear Cache
            </button>
          </div>
        </div>
      </div>

      <!-- Main Content -->
      <div class="lg:col-span-3">
        <!-- Configuration List -->
        <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 border border-white/20">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-white">
              {{ getSelectedTypeLabel }} Configurations
            </h2>
            <div class="flex items-center space-x-4">
              <div class="relative">
                <input
                  v-model="searchQuery"
                  type="text"
                  placeholder="Search configurations..."
                  class="px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:border-blue-500 w-64"
                />
                <span class="absolute right-3 top-2.5 text-gray-400">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                  </svg>
                </span>
              </div>
            </div>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full">
              <thead>
                <tr class="border-b border-white/10">
                  <th class="text-left py-3 px-4 text-sm font-medium text-gray-400">Name</th>
                  <th class="text-left py-3 px-4 text-sm font-medium text-gray-400">Value</th>
                  <th class="text-left py-3 px-4 text-sm font-medium text-gray-400">Type</th>
                  <th class="text-left py-3 px-4 text-sm font-medium text-gray-400">Host</th>
                  <th class="text-left py-3 px-4 text-sm font-medium text-gray-400">Group</th>
                  <th class="text-right py-3 px-4 text-sm font-medium text-gray-400">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="config in filteredConfigurations" :key="config.name" class="border-b border-white/10 hover:bg-white/5">
                  <td class="py-3 px-4 text-white">{{ config.name }}</td>
                  <td class="py-3 px-4 text-gray-300">{{ config.value }}</td>
                  <td class="py-3 px-4 text-gray-300">{{ config.type }}</td>
                  <td class="py-3 px-4 text-gray-300">{{ config.host }}</td>
                  <td class="py-3 px-4 text-gray-300">{{ config.group }}</td>
                  <td class="py-3 px-4 text-right">
                    <button 
                      @click="openModal(config)"
                      class="text-blue-300 hover:text-blue-400 transition-colors"
                    >
                      Edit
                    </button>
                  </td>
                </tr>
                <tr v-if="filteredConfigurations.length === 0">
                  <td colspan="6" class="py-4 text-center text-gray-400">
                    {{ searchQuery ? 'No matching configurations found' : 'No configurations found' }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Configuration Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
      <div class="bg-gray-900 rounded-xl p-6 w-[32rem] border border-white/20">
        <h3 class="text-xl font-semibold text-white mb-4">
          {{ editingConfig ? 'Edit Configuration' : 'Add Configuration' }}
        </h3>
        <form @submit.prevent="saveConfiguration" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-400 mb-2" for="name">
              Name
            </label>
            <input
              id="name"
              v-model="form.name"
              type="text"
              class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:border-blue-500"
              required
            >
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-400 mb-2" for="value">
              Value
            </label>
            <input
              id="value"
              v-model="form.value"
              type="text"
              class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:border-blue-500"
              required
            >
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-400 mb-2" for="type">
              Type
            </label>
            <select
              id="type"
              v-model="form.type"
              class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:border-blue-500"
              required
            >
              <option v-for="type in configTypes" :key="type.value" :value="type.value">
                {{ type.label }}
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-400 mb-2" for="host">
              Host
            </label>
            <input
              id="host"
              v-model="form.host"
              type="text"
              class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:border-blue-500"
              required
            >
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-400 mb-2" for="group">
              Group
            </label>
            <input
              id="group"
              v-model="form.group"
              type="text"
              class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:border-blue-500"
            >
          </div>
          <div class="flex justify-end space-x-3 pt-4">
            <button
              type="button"
              @click="closeModal"
              class="px-4 py-2 bg-white/5 hover:bg-white/10 text-gray-300 rounded-lg transition-colors"
            >
              Cancel
            </button>
            <button
              type="submit"
              class="px-4 py-2 bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 rounded-lg transition-colors"
            >
              Save
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import MessageBox from '../common/MessageBox.vue';

const configurations = ref([]);
const selectedType = ref('app');
const showModal = ref(false);

const configTypes = [
  { value: 'app', label: 'Application' },
  { value: 'database', label: 'Database' },
  { value: 'mail', label: 'Mail' },
  { value: 'cache', label: 'Cache' },
  { value: 'queue', label: 'Queue' },
  { value: 'security', label: 'Security' }
];

const editingConfig = ref(null);
const message = ref(null);
const messageType = ref('info');
const searchQuery = ref('');

const form = ref({
  name: '',
  value: '',
  type: 'app',
  host: 'localhost',
  group: ''
});

const getSelectedTypeLabel = computed(() => {
  const type = configTypes.find(t => t.value === selectedType.value);
  return type ? type.label : selectedType.value.charAt(0).toUpperCase() + selectedType.value.slice(1);
});

const filteredConfigurations = computed(() => {
  if (!searchQuery.value) return configurations.value;
  
  const query = searchQuery.value.toLowerCase();
  return configurations.value.filter(config => 
    config.name.toLowerCase().includes(query) ||
    config.value.toLowerCase().includes(query) ||
    config.type.toLowerCase().includes(query) ||
    config.host.toLowerCase().includes(query) ||
    (config.group && config.group.toLowerCase().includes(query))
  );
});

const showMessage = (msg, type = 'info') => {
  message.value = msg;
  messageType.value = type;
};

const loadConfigurations = async () => {
  try {
    const response = await axios.get(`/api/configuration/${selectedType.value}`);
    configurations.value = response.data.data;
  } catch (error) {
    console.error('Error loading configurations:', error);
    showMessage('Failed to load configurations', 'error');
  }
};

const clearCache = async () => {
  try {
    await axios.post('/api/configuration/clear-cache');
    showMessage('Cache cleared successfully', 'success');
    loadConfigurations();
  } catch (error) {
    console.error('Error clearing cache:', error);
    showMessage('Failed to clear cache', 'error');
  }
};

const openModal = (config = null) => {
  editingConfig.value = config;
  if (config) {
    form.value = { 
      name: config.name,
      value: config.value,
      type: config.type,
      host: config.host,
      group: config.group || ''
    };
  } else {
    form.value = {
      name: '',
      value: '',
      type: selectedType.value,
      host: '',
      group: ''
    };
  }
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  editingConfig.value = null;
  form.value = {
    name: '',
    value: '',
    type: selectedType.value,
    host: '',
    group: ''
  };
};

const saveConfiguration = async () => {
  try {
    const configs = [{
      name: form.value.name,
      value: form.value.value,
      type: form.value.type,
      host: form.value.host,
      group: form.value.group
    }];

    await axios.post(`/api/configuration/${form.value.type}`, {
      configurations: configs
    });

    closeModal();
    loadConfigurations();
    showMessage('Configuration saved successfully', 'success');
  } catch (error) {
    console.error('Error saving configuration:', error);
    showMessage('Failed to save configuration', 'error');
  }
};

onMounted(() => {
  loadConfigurations();
});
</script>