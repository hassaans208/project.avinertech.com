<template>
  <div class="space-y-6">
    <!-- Model Name and Type -->
    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium text-gray-400 mb-2">Model Name</label>
        <input 
          v-model="model.name"
          type="text"
          class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:border-blue-500"
          placeholder="User"
          @input="updateModel"
        />
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-400 mb-2">Table Type</label>
        <Select2
          v-model="model.tableType"
          :options="tableTypeOptions"
          placeholder="Select table type"
          class="w-full"
          @change="updateModel"
        />
      </div>
    </div>

    <!-- Description -->
    <div>
      <label class="block text-sm font-medium text-gray-400 mb-2">Description</label>
      <textarea
        v-model="model.description"
        class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:border-blue-500"
        rows="3"
        placeholder="Describe the purpose of this model..."
        @input="updateModel"
      ></textarea>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import Select2 from '../common/Select2.vue';

const props = defineProps({
  modelValue: {
    type: Object,
    required: true,
    default: () => ({
      name: '',
      tableType: 'regular',
      description: ''
    })
  }
});

const emit = defineEmits(['update:modelValue']);

const model = ref({ ...props.modelValue });

const tableTypeOptions = [
  { value: 'regular', label: 'Regular Table' },
  { value: 'pivot', label: 'Pivot Table' },
  { value: 'enum', label: 'Enum Table' }
];

const updateModel = () => {
  emit('update:modelValue', { ...model.value });
};

// Watch for external changes to the model
watch(() => props.modelValue, (newValue) => {
  model.value = { ...newValue };
}, { deep: true });
</script> 