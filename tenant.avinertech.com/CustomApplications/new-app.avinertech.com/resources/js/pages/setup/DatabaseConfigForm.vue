<template>
  <form @submit.prevent="handleSubmit" class="space-y-6">
    <!-- Database Host -->
    <div>
      <label class="block text-sm font-medium text-gray-400 mb-2">Database Host</label>
      <input 
        v-model="formData.DATABASE_HOST"
        type="text"
        class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:border-blue-500"
        placeholder="localhost"
        required
      />
    </div>

    <!-- Database Port -->
    <div>
      <label class="block text-sm font-medium text-gray-400 mb-2">Database Port</label>
      <input 
        v-model="formData.DATABASE_PORT"
        type="number"
        class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:border-blue-500"
        placeholder="3306"
        required
      />
    </div>

    <!-- Database Name -->
    <div>
      <label class="block text-sm font-medium text-gray-400 mb-2">Database Name</label>
      <input 
        v-model="formData.DATABASE_NAME"
        type="text"
        class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:border-blue-500"
        placeholder="my_database"
        required
      />
    </div>

    <!-- Database Username -->
    <div>
      <label class="block text-sm font-medium text-gray-400 mb-2">Database Username</label>
      <input 
        v-model="formData.DATABASE_USERNAME"
        type="text"
        class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:border-blue-500"
        placeholder="db_user"
        required
      />
    </div>

    <!-- Database Password -->
    <div>
      <label class="block text-sm font-medium text-gray-400 mb-2">Database Password</label>
      <div class="relative">
        <input 
          v-model="formData.DATABASE_PASSWORD"
          :type="showPassword ? 'text' : 'password'"
          class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:border-blue-500"
          placeholder="••••••••"
          required
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

    <!-- Save Button -->
    <div class="pt-4">
      <button 
        type="submit"
        class="w-full px-4 py-2 bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 rounded-lg transition-colors"
        :disabled="isSaving"
      >
        {{ isSaving ? 'Saving...' : 'Save Configuration' }}
      </button>
    </div>
  </form>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
  initialData: {
    type: Object,
    required: true
  }
});

const emit = defineEmits(['save']);

const isSaving = ref(false);
const showPassword = ref(false);
const formData = ref({ ...props.initialData });

// Watch for changes in initialData
watch(() => props.initialData, (newData) => {
  formData.value = { ...newData };
}, { deep: true });

const handleSubmit = async () => {
  isSaving.value = true;
  try {
    await emit('save', { ...formData.value });
  } catch (error) {
    console.error('Failed to save database configuration:', error);
  } finally {
    isSaving.value = false;
  }
};
</script> 