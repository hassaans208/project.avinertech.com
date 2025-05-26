<template>
  <div v-if="show" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
    <div class="bg-gray-800 rounded-xl p-6 w-full max-w-md border border-white/10">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-xl font-semibold text-white">Edit Company Information</h3>
        <button 
          @click="$emit('close')"
          class="text-gray-400 hover:text-white transition-colors"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <form @submit.prevent="handleSubmit" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-400 mb-2">Company Name</label>
          <input 
            v-model="formData.name"
            type="text"
            class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:border-blue-500"
            required
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-400 mb-2">Email</label>
          <input 
            v-model="formData.email"
            type="email"
            class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:border-blue-500"
            required
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-400 mb-2">Username</label>
          <input 
            v-model="formData.username"
            type="text"
            class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:border-blue-500"
            required
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-400 mb-2">Phone</label>
          <input 
            v-model="formData.phone"
            type="tel"
            class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:border-blue-500"
            required
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-400 mb-2">Address</label>
          <textarea 
            v-model="formData.address"
            rows="2"
            class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:border-blue-500"
            required
          ></textarea>
        </div>

        <div class="flex justify-end space-x-3 mt-6">
          <button 
            type="button"
            @click="$emit('close')"
            class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-lg transition-colors"
          >
            Cancel
          </button>
          <button 
            type="submit"
            class="px-4 py-2 bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 rounded-lg transition-colors"
            :disabled="isSaving"
          >
            {{ isSaving ? 'Saving...' : 'Save Changes' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
  show: {
    type: Boolean,
    required: true
  },
  initialData: {
    type: Object,
    required: true
  }
});

const emit = defineEmits(['close', 'save']);

const isSaving = ref(false);
const formData = ref({ ...props.initialData });

// Watch for changes in initialData
watch(() => props.initialData, (newData) => {
  formData.value = { ...newData };
}, { deep: true });

const handleSubmit = async () => {
  isSaving.value = true;
  try {
    await emit('save', { ...formData.value });
    emit('close');
  } catch (error) {
    console.error('Failed to save company information:', error);
  } finally {
    isSaving.value = false;
  }
};
</script> 