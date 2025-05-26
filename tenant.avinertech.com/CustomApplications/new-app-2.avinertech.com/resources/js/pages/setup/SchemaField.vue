<template>
  <div class="relative flex flex-col p-4 bg-white/5 rounded-lg border border-white/10">
    <!-- Main Field Properties -->
    <div class="flex items-start gap-4 mb-4">
      <!-- Field Name -->
      <div class="w-1/3">
        <label class="block text-sm font-medium text-gray-400 mb-2">Field Name</label>
        <input 
          v-model="field.name"
          type="text"
          class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:border-blue-500"
          placeholder="email"
          @input="updateField"
        />
      </div>

      <!-- Field Type -->
      <div class="flex-1">
        <label class="block text-sm font-medium text-gray-400 mb-2">
          Type
        </label>
        <Select2
          v-model="field.type"
          :options="fieldTypeOptions"
          placeholder="Select field type"
          class="w-full"
          @change="updateField"
        >
          <template #option="{ option }">
            <div class="flex items-center justify-between">
              <span>{{ formatTypeLabel(option.value) }}</span>
              <span class="text-xs text-gray-400">{{ option.description }}</span>
            </div>
          </template>
        </Select2>
      </div>

      <!-- Field Length/Precision/Scale -->
      <div class="w-1/3">
        <label class="block text-sm font-medium text-gray-400 mb-2">
          {{ getOptionsLabel(field.type) }}
        </label>
        <div class="h-[38px] flex items-center gap-2">
          <!-- Length Input -->
          <input 
            v-if="showLength"
            v-model="field.length"
            type="number"
            class="flex-1 px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:border-blue-500"
            :placeholder="getLengthPlaceholder(field.type)"
            @input="updateField"
          />

          <!-- Precision Input -->
          <input 
            v-if="showPrecision"
            v-model="field.precision"
            type="number"
            class="flex-1 px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:border-blue-500"
            placeholder="Precision"
            @input="updateField"
          />

          <!-- Scale Input -->
          <input 
            v-if="showScale"
            v-model="field.scale"
            type="number"
            class="flex-1 px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:border-blue-500"
            placeholder="Scale"
            @input="updateField"
          />

          <!-- Empty placeholder when no inputs needed -->
          <div v-if="!showLength && !showPrecision && !showScale" class="w-full h-full"></div>
        </div>
      </div>

      <!-- Field Actions -->
      <div class="flex items-center gap-2">
        <button
          v-for="action in fieldActions"
          :key="action.name"
          type="button"
          @click="action.handler"
          class="p-2 transition-colors"
          :class="[
            action.classes,
            action.isActive?.(field) ? action.activeClasses : action.inactiveClasses
          ]"
          :title="action.tooltip"
        >
          <svg 
            class="w-5 h-5" 
            fill="none" 
            stroke="currentColor" 
            viewBox="0 0 24 24"
          >
            <path 
              stroke-linecap="round" 
              stroke-linejoin="round" 
              stroke-width="2" 
              :d="action.icon"
            />
          </svg>
        </button>
      </div>
    </div>

    <!-- Field Options -->
    <div class="flex flex-wrap gap-4">
      <!-- Type-specific Options -->
      <div v-if="showTypeSpecificOptions" class="flex flex-wrap gap-4">
        <!-- Values Input for ENUM -->
        <div v-if="showValues" class="w-full">
          <label class="block text-sm font-medium text-gray-400 mb-2">
            Enum Values
          </label>
          <div class="flex gap-2">
            <input 
              v-model="newEnumValue"
              type="text"
              class="flex-1 px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:border-blue-500"
              placeholder="Enter enum value"
              @keyup.enter="addEnumValue"
            />
            <button 
              @click="addEnumValue"
              class="px-4 py-2 bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 rounded-lg transition-colors"
            >
              Add
            </button>
          </div>
          <!-- Enum Values List -->
          <div v-if="field.values?.length" class="mt-2 flex flex-wrap gap-2">
            <div 
              v-for="(value, index) in field.values" 
              :key="index"
              class="flex items-center gap-2 px-3 py-1 bg-slate-700/50 rounded-lg text-sm"
            >
              <span class="text-slate-200">{{ value }}</span>
              <button 
                @click="removeEnumValue(index)"
                class="text-slate-400 hover:text-red-400"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Field Flags -->
      <div class="flex flex-wrap gap-2">
        <button
          v-for="option in visibleFieldOptions"
          :key="option.name"
          type="button"
          @click="toggleFieldOption(option.name)"
          class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-200 flex items-center gap-2"
          :class="[
            field[option.name] 
              ? 'bg-slate-700/50 hover:bg-slate-700/70 text-slate-200' 
              : 'bg-slate-700/20 hover:bg-slate-700/30 text-slate-400'
          ]"
        >
          <svg 
            v-if="field[option.name]"
            class="w-4 h-4" 
            fill="none" 
            stroke="currentColor" 
            viewBox="0 0 24 24"
          >
            <path 
              stroke-linecap="round" 
              stroke-linejoin="round" 
              stroke-width="2" 
              d="M5 13l4 4L19 7"
            />
          </svg>
          <span>{{ option.label }}</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue';
import Select2 from '../common/Select2.vue';
import { hasLengthOrPrecision, getLengthOrPrecisionLabel, getLengthOrPrecisionPlaceholder } from '../../utils/schemaUtils';

const props = defineProps({
  modelValue: {
    type: Object,
    required: true
  },
  index: {
    type: Number,
    required: true
  }
});

const emit = defineEmits(['update:modelValue', 'remove', 'edit-logic']);

const field = ref({ ...props.modelValue });

const fieldTypes = [
  {
    mysql_type: 'CHAR',
    laravel_method: 'char',
    description: 'Fixed length string',
    requires_length: true,
    requires_precision: false,
    requires_scale: false,
    requires_values: false,
  },
  {
    mysql_type: 'VARCHAR',
    laravel_method: 'string',
    description: 'Variable length string',
    requires_length: true,
    requires_precision: false,
    requires_scale: false,
    requires_values: false,
  },
  {
    mysql_type: 'TEXT',
    laravel_method: 'text',
    description: 'Text up to 65,535 bytes',
    requires_length: false,
    requires_precision: false,
    requires_scale: false,
    requires_values: false,
  },
  {
    mysql_type: 'MEDIUMTEXT',
    laravel_method: 'mediumText',
    description: 'Medium text',
    requires_length: false,
    requires_precision: false,
    requires_scale: false,
    requires_values: false,
  },
  {
    mysql_type: 'LONGTEXT',
    laravel_method: 'longText',
    description: 'Long text',
    requires_length: false,
    requires_precision: false,
    requires_scale: false,
    requires_values: false,
  },
  {
    mysql_type: 'BLOB',
    laravel_method: 'binary',
    description: 'Binary data',
    requires_length: false,
    requires_precision: false,
    requires_scale: false,
    requires_values: false,
  },
  {
    mysql_type: 'ENUM',
    laravel_method: 'enum',
    description: 'Enumerated values',
    requires_length: false,
    requires_precision: false,
    requires_scale: false,
    requires_values: true,
  },
  {
    mysql_type: 'TINYINT',
    laravel_method: 'tinyInteger',
    description: 'Small integer',
    requires_length: false,
    requires_precision: false,
    requires_scale: false,
    requires_values: false,
  },
  {
    mysql_type: 'SMALLINT',
    laravel_method: 'smallInteger',
    description: 'Small integer',
    requires_length: false,
    requires_precision: false,
    requires_scale: false,
    requires_values: false,
  },
  {
    mysql_type: 'MEDIUMINT',
    laravel_method: 'mediumInteger',
    description: 'Medium integer',
    requires_length: false,
    requires_precision: false,
    requires_scale: false,
    requires_values: false,
  },
  {
    mysql_type: 'INT',
    laravel_method: 'integer',
    description: 'Standard integer',
    requires_length: false,
    requires_precision: false,
    requires_scale: false,
    requires_values: false,
  },
  {
    mysql_type: 'BIGINT',
    laravel_method: 'bigInteger',
    description: 'Large integer',
    requires_length: false,
    requires_precision: false,
    requires_scale: false,
    requires_values: false,
  },
  {
    mysql_type: 'FLOAT',
    laravel_method: 'float',
    description: 'Floating point',
    requires_length: false,
    requires_precision: true,
    requires_scale: true,
    requires_values: false,
  },
  {
    mysql_type: 'DOUBLE',
    laravel_method: 'double',
    description: 'Double precision float',
    requires_length: false,
    requires_precision: true,
    requires_scale: true,
    requires_values: false,
  },
  {
    mysql_type: 'DECIMAL',
    laravel_method: 'decimal',
    description: 'Fixed-point decimal',
    requires_length: false,
    requires_precision: true,
    requires_scale: true,
    requires_values: false,
  },
  {
    mysql_type: 'DATE',
    laravel_method: 'date',
    description: 'Date only',
    requires_length: false,
    requires_precision: false,
    requires_scale: false,
    requires_values: false,
  },
  {
    mysql_type: 'DATETIME',
    laravel_method: 'dateTime',
    description: 'Date and time',
    requires_length: false,
    requires_precision: false,
    requires_scale: false,
    requires_values: false,
  },
  {
    mysql_type: 'TIMESTAMP',
    laravel_method: 'timestamp',
    description: 'Timestamp',
    requires_length: false,
    requires_precision: false,
    requires_scale: false,
    requires_values: false,
  },
  {
    mysql_type: 'TIME',
    laravel_method: 'time',
    description: 'Time only',
    requires_length: false,
    requires_precision: false,
    requires_scale: false,
    requires_values: false,
  },
  {
    mysql_type: 'YEAR',
    laravel_method: 'year',
    description: 'Year only',
    requires_length: false,
    requires_precision: false,
    requires_scale: false,
    requires_values: false,
  },
  {
    mysql_type: 'JSON',
    laravel_method: 'json',
    description: 'JSON data',
    requires_length: false,
    requires_precision: false,
    requires_scale: false,
    requires_values: false,
  },
  {
    mysql_type: 'BOOLEAN',
    laravel_method: 'boolean',
    description: 'Boolean',
    requires_length: false,
    requires_precision: false,
    requires_scale: false,
    requires_values: false,
  },
];

// Format type label to be first letter capital and rest lowercase
const formatTypeLabel = (type) => {
  return type.charAt(0).toUpperCase() + type.slice(1).toLowerCase();
};

// Get field type info
const getFieldTypeInfo = (type) => {
  return fieldTypes.find(t => t.mysql_type === type) || null;
};

// Computed properties for field type requirements
const showLength = computed(() => {
  const typeInfo = getFieldTypeInfo(field.value.type);
  return typeInfo?.requires_length || false;
});

const showPrecision = computed(() => {
  const typeInfo = getFieldTypeInfo(field.value.type);
  return typeInfo?.requires_precision || false;
});

const showScale = computed(() => {
  const typeInfo = getFieldTypeInfo(field.value.type);
  return typeInfo?.requires_scale || false;
});

const showValues = computed(() => {
  const typeInfo = getFieldTypeInfo(field.value.type);
  return typeInfo?.requires_values || false;
});

const fieldOptions = [
  { 
    name: 'nullable',
    label: 'Nullable',
    description: 'Allow null values for this field'
  },
  { 
    name: 'unique',
    label: 'Unique',
    description: 'Ensure values are unique across records'
  },
  { 
    name: 'indexed',
    label: 'Indexed',
    description: 'Create an index for faster queries'
  },
  { 
    name: 'encrypted',
    label: 'Encrypted',
    description: 'Encrypt this field\'s values',
    showIf: (field) => field.type === 'string'
  }
];

const updateField = () => {
  emit('update:modelValue', { ...field.value });
};

const toggleFieldOption = (optionName) => {
  field.value[optionName] = !field.value[optionName];
  updateField();
};

// Filter options based on field type
const visibleFieldOptions = computed(() => {
  return fieldOptions.filter(option => {
    if (option.showIf) {
      return option.showIf(field.value);
    }
    return true;
  });
});

// Watch for external changes to the field
watch(() => props.modelValue, (newValue) => {
  field.value = { ...newValue };
}, { deep: true });

// New state for enum values
const newEnumValue = ref('');

// Computed to show type-specific options
const showTypeSpecificOptions = computed(() => {
  return showLength.value || showPrecision.value || showScale.value || showValues.value;
});

// Add enum value
const addEnumValue = () => {
  if (!newEnumValue.value.trim()) return;
  
  if (!field.value.values) {
    field.value.values = [];
  }
  
  if (!field.value.values.includes(newEnumValue.value.trim())) {
    field.value.values.push(newEnumValue.value.trim());
    updateField();
  }
  
  newEnumValue.value = '';
};

// Remove enum value
const removeEnumValue = (index) => {
  field.value.values.splice(index, 1);
  updateField();
};

// Update the default field structure
const defaultField = {
  name: '',
  type: 'VARCHAR',
  nullable: false,
  unique: false,
  indexed: false,
  encrypted: false,
  length: null,
  precision: null,
  scale: null,
  values: [],
  logic: ''
};

const fieldActions = [
  {
    name: 'logic',
    icon: 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z',
    tooltip: 'Edit field logic',
    classes: 'hover:text-blue-300',
    activeClasses: 'text-blue-500',
    inactiveClasses: 'text-blue-400',
    isActive: (field) => !!field.logic,
    handler: () => emit('edit-logic', { index: props.index, logic: field.value.logic })
  },
  {
    name: 'remove',
    icon: 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16',
    tooltip: 'Remove field',
    classes: 'hover:text-red-300',
    activeClasses: 'text-red-500',
    inactiveClasses: 'text-red-400',
    isActive: () => false,
    handler: () => emit('remove')
  }
];

// Transform fieldTypes into Select2 options format
const fieldTypeOptions = computed(() => {
  return fieldTypes.map(type => ({
    value: type.mysql_type,
    label: formatTypeLabel(type.mysql_type),
    description: type.description,
    group: getTypeGroup(type.mysql_type)
  }));
});

// Group types by category
const getTypeGroup = (type) => {
  if (['CHAR', 'VARCHAR', 'TEXT', 'MEDIUMTEXT', 'LONGTEXT'].includes(type)) {
    return 'Text Types';
  }
  if (['TINYINT', 'SMALLINT', 'MEDIUMINT', 'INT', 'BIGINT', 'FLOAT', 'DOUBLE', 'DECIMAL'].includes(type)) {
    return 'Numeric Types';
  }
  if (['DATE', 'DATETIME', 'TIMESTAMP', 'TIME', 'YEAR'].includes(type)) {
    return 'Date & Time Types';
  }
  if (['JSON', 'BOOLEAN', 'ENUM', 'BLOB'].includes(type)) {
    return 'Special Types';
  }
  return 'Other Types';
};

// Get the appropriate label for the options section
const getOptionsLabel = (type) => {
  const typeInfo = getFieldTypeInfo(type);
  if (!typeInfo) return 'Options';

  if (typeInfo.requires_length) return 'Length';
  if (typeInfo.requires_precision && typeInfo.requires_scale) return 'Precision & Scale';
  if (typeInfo.requires_precision) return 'Precision';
  if (typeInfo.requires_scale) return 'Scale';
  return 'Options';
};

// Get placeholder for length input
const getLengthPlaceholder = (type) => {
  switch (type) {
    case 'VARCHAR':
      return 'Max length';
    case 'CHAR':
      return 'Fixed length';
    default:
      return 'Length';
  }
};
</script> 