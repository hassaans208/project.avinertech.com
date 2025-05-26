<template>
  <div class="relative" ref="selectContainer">
    <!-- Selected Value Display -->
    <div 
      @click="toggleDropdown"
      class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:border-blue-500 cursor-pointer flex items-center justify-between"
      :class="{ 'border-blue-500': isOpen }"
    >
      <div class="flex items-center space-x-2">
        <span v-if="modelValue" class="text-white">{{ getSelectedLabel }}</span>
        <span v-else class="text-white/60">{{ placeholder }}</span>
      </div>
      <svg 
        class="w-5 h-5 text-white/60 transition-transform"
        :class="{ 'transform rotate-180': isOpen }"
        fill="none" 
        stroke="currentColor" 
        viewBox="0 0 24 24"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
      </svg>
    </div>

    <!-- Dropdown -->
    <div 
      v-if="isOpen"
      class="absolute z-50 w-full mt-1 bg-gray-800 border border-white/10 rounded-lg shadow-lg max-h-60 overflow-y-auto"
    >
      <!-- Search Input -->
      <div class="sticky top-0 p-2 bg-gray-800 border-b border-white/10">
        <input
          ref="searchInput"
          v-model="searchQuery"
          type="text"
          class="w-full px-3 py-1.5 bg-white/5 border border-white/10 rounded text-white text-sm focus:outline-none focus:border-blue-500 placeholder-white/40"
          placeholder="Search..."
          @input="filterOptions"
        />
      </div>

      <!-- Options List -->
      <div class="py-1">
        <template v-for="(group, groupLabel) in groupedOptions" :key="groupLabel">
          <!-- Group Label -->
          <div v-if="groupLabel !== 'default'" class="px-3 py-1.5 text-xs font-medium text-white/60 bg-white/5">
            {{ groupLabel }}
          </div>
          
          <!-- Group Options -->
          <div 
            v-for="option in group" 
            :key="option.value"
            @click="selectOption(option)"
            class="px-3 py-2 text-sm cursor-pointer hover:bg-white/5 text-white"
            :class="{ 'bg-blue-500/20 text-blue-300': option.value === modelValue }"
          >
            {{ option.label }}
          </div>
        </template>

        <!-- No Results -->
        <div v-if="filteredOptions.length === 0" class="px-3 py-2 text-sm text-white/60">
          No results found
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: ''
  },
  options: {
    type: Array,
    required: true,
    validator: (options) => options.every(opt => 
      typeof opt === 'object' && 
      'value' in opt && 
      'label' in opt
    )
  },
  placeholder: {
    type: String,
    default: 'Select an option'
  },
  searchable: {
    type: Boolean,
    default: true
  },
  groupBy: {
    type: String,
    default: 'group'
  }
});

const emit = defineEmits(['update:modelValue', 'change']);

const isOpen = ref(false);
const searchQuery = ref('');
const selectContainer = ref(null);
const searchInput = ref(null);

// Computed properties
const getSelectedLabel = computed(() => {
  const option = props.options.find(opt => opt.value === props.modelValue);
  return option ? option.label : '';
});

const filteredOptions = computed(() => {
  if (!searchQuery.value) return props.options;
  
  const query = searchQuery.value.toLowerCase();
  return props.options.filter(opt => 
    opt.label.toLowerCase().includes(query)
  );
});

const groupedOptions = computed(() => {
  const groups = {};
  
  filteredOptions.value.forEach(option => {
    const group = option[props.groupBy] || 'default';
    if (!groups[group]) {
      groups[group] = [];
    }
    groups[group].push(option);
  });
  
  return groups;
});

// Methods
const toggleDropdown = () => {
  isOpen.value = !isOpen.value;
  if (isOpen.value && props.searchable) {
    // Focus search input when dropdown opens
    setTimeout(() => {
      searchInput.value?.focus();
    }, 0);
  }
};

const selectOption = (option) => {
  emit('update:modelValue', option.value);
  emit('change', option);
  isOpen.value = false;
  searchQuery.value = '';
};

const filterOptions = () => {
  // Filtering is handled by computed property
};

// Click outside handler
const handleClickOutside = (event) => {
  if (selectContainer.value && !selectContainer.value.contains(event.target)) {
    isOpen.value = false;
  }
};

// Lifecycle hooks
onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});

// Watch for modelValue changes
watch(() => props.modelValue, (newValue) => {
  if (newValue) {
    searchQuery.value = '';
  }
});
</script>

<style scoped>
/* Custom scrollbar styles */
.overflow-y-auto {
  scrollbar-width: thin;
  scrollbar-color: rgba(255, 255, 255, 0.1) transparent;
}

.overflow-y-auto::-webkit-scrollbar {
  width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: transparent;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background-color: rgba(255, 255, 255, 0.1);
  border-radius: 3px;
}
</style> 