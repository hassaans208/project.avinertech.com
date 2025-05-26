<template>
  <div class="flex items-center justify-between p-3 bg-white/5 rounded-lg border border-white/10">
    <div>
      <h3 class="text-white font-medium">{{ model.name }}</h3>
      <p class="text-xs text-gray-400">{{ model.fields.length }} fields</p>
    </div>
    <div class="flex items-center space-x-2">
      <button 
        @click="$emit('edit', model)"
        class="px-3 py-1.5 bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 rounded-lg transition-colors text-sm"
      >
        Edit
      </button>
      <button 
        @click="$emit('delete', model)"
        class="px-3 py-1.5 bg-red-500/20 hover:bg-red-500/30 text-red-300 rounded-lg transition-colors text-sm"
      >
        Delete
      </button>
    </div>
  </div>
</template>

<script setup>
defineProps({
  model: {
    type: Object,
    required: true,
    default: () => ({
      name: '',
      fields: []
    })
  }
});

defineEmits(['edit', 'delete']);
</script> 