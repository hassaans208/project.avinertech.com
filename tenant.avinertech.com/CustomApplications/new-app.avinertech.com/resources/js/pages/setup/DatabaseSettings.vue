<template>
    <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 border border-white/20">
        <h2 class="text-xl font-semibold text-white mb-4">Database Settings</h2>

        <!-- Status Block -->
        <div class="space-y-4">
            <div class="flex items-center justify-between p-4 bg-white/5 rounded-lg">
                <div>
                    <h3 class="text-white font-medium">Database Schema</h3>
                    <p class="text-sm text-gray-400">Manage your database models and relationships</p>
                </div>
                <Link :href="route('setup.schema')"
                    class="px-4 py-2 bg-purple-500/20 hover:bg-purple-500/30 text-purple-300 rounded-lg transition-colors">
                    Manage Schema
                </Link>
            </div>
            <div class="flex items-center justify-between p-4 bg-white/5 rounded-lg">
                <div>
                    <h3 class="text-white font-medium">Connection Status</h3>
                    <p class="text-sm" :class="connectionStatusClass">{{ connectionStatus || 'Not Connected' }}</p>
                </div>
                <button @click="testConnection" :disabled="isTesting"
                    class="px-4 py-2 bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center">
                    <svg v-if="isTesting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    {{ isTesting ? 'Testing...' : 'Test Connection' }}
                </button>
            </div>

            <!-- Configuration Block -->
            <div class="flex items-center justify-between p-4 bg-white/5 rounded-lg">
                <div>
                    <h3 class="text-white font-medium">Database Configuration</h3>
                    <p class="text-sm text-gray-400">Configure database connection settings</p>
                </div>
                <button @click="showForm = !showForm"
                    class="px-4 py-2 bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 rounded-lg transition-colors">
                    {{ showForm ? 'Cancel' : 'Configure' }}
                </button>
            </div>
        </div>

        <!-- Configuration Form -->
        <div v-if="showForm" class="mt-6">
            <DatabaseConfigForm :initial-data="config" @save="handleSave" />
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import SchemaService from '../../services/SchemaService';
import DatabaseConfigForm from './DatabaseConfigForm.vue';

const showForm = ref(false);
const isSaving = ref(false);
const isTesting = ref(false);
const showPassword = ref(false);
const connectionStatus = ref(null);

const config = ref({
    DATABASE_HOST: 'localhost',
    DATABASE_PORT: '3306',
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
            for (const data of response.data) {
                config.value[data?.name] = data?.value || '';
            }
        }
    } catch (error) {
        console.error('Failed to load database configuration:', error);
    }
});

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

const handleSave = async (data) => {
    try {
        // Here you would typically make an API call to save the configuration
        config.value = { ...data };
        showForm.value = false;
    } catch (error) {
        console.error('Failed to save database configuration:', error);
    }
};
</script>