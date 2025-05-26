<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-hidden">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="$emit('close')"></div>

    <!-- Modal Content -->
    <div class="fixed inset-0 flex flex-col bg-gray-800">
      <!-- Header -->
      <div class="flex-none flex items-center justify-between p-6 border-b border-white/10">
        <h3 class="text-xl font-semibold text-white">
          {{ isEditing ? 'Edit Model' : 'New Model' }}
        </h3>
        <button 
          @click="$emit('close')"
          class="text-gray-400 hover:text-white"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Scrollable Content -->
      <div class="flex-1 overflow-hidden">
        <div class="h-full overflow-y-auto">
          <form @submit.prevent="$emit('save')" class="p-6 space-y-6">
            <SchemaModelDetails
              v-model="model"
            />

            <!-- Fields List -->
            <div>
              <div class="flex items-center justify-between mb-4">
                <h4 class="text-lg font-medium text-white">Fields</h4>
                <button 
                  type="button"
                  @click="$emit('add-field')"
                  class="px-4 py-2 bg-green-500/20 hover:bg-green-500/30 text-green-300 rounded-lg transition-colors"
                >
                  Add Field
                </button>
              </div>

              <div class="space-y-4">
                <div 
                  v-for="(field, index) in model.fields" 
                  :key="field.name || index"
                  class="relative group"
                >
                  <SchemaField
                    v-model="model.fields[index]"
                    :index="index"
                    @remove="$emit('remove-field', index)"
                    @edit-logic="$emit('edit-logic', $event)"
                  />
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>

      <!-- Footer -->
      <div class="flex-none p-6 border-t border-white/10 bg-gray-800">
        <div class="flex justify-end space-x-3">
          <button 
            type="button"
            @click="$emit('close')"
            class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-lg transition-colors"
          >
            Cancel
          </button>
          <button 
            type="submit"
            @click="$emit('save')"
            :disabled="isSaving"
            class="px-4 py-2 bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ isSaving ? 'Saving...' : 'Save Model' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import SchemaModelDetails from './SchemaModelDetails.vue';
import SchemaField from './SchemaField.vue';

const props = defineProps({
  show: {
    type: Boolean,
    required: true
  },
  modelValue: {
    type: Object,
    required: true,
    default: () => ({
      name: '',
      tableType: 'regular',
      description: '',
      fields: []
    })
  },
  isEditing: {
    type: Boolean,
    default: false
  },
  isSaving: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits([
  'update:modelValue',
  'close',
  'save',
  'add-field',
  'remove-field',
  'edit-logic'
]);

const model = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
});
</script> 