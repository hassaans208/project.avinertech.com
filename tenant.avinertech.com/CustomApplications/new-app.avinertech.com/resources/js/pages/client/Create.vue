<template>
    <div class="max-w-3xl mx-auto">
      <!-- Form Header -->
      <div class="mb-8">
        <h2 class="text-2xl font-bold text-white">Create New Client</h2>
        <p class="mt-1 text-sm text-gray-400">Add a new client to your system</p>
      </div>

      <!-- Form -->
      <form @submit.prevent="handleSubmit" class="space-y-6">
        <div class="bg-white/5 backdrop-blur-lg rounded-xl border border-white/10 p-6 space-y-6">
          <!-- Dynamic Form Fields -->
          <div class="grid grid-cols-12 gap-6">
            <div v-for="field in formFields" 
                 :key="field.name" 
                 :class="[
                   field.options?.col || 'col-span-12',
                   'space-y-2'
                 ]">
              <label 
                :for="field.name" 
                class="block text-sm font-medium text-gray-300"
              >
                {{ field.label }}
                <span v-if="field.required" class="text-pink-400 ml-1">*</span>
              </label>

              <!-- Text Input -->
              <input
                v-if="field.type === 'text' || field.type === 'email' || field.type === 'tel'"
                :type="field.type"
                :id="field.name"
                v-model="formData[field.name]"
                :placeholder="field.placeholder"
                :required="field.required"
                :disabled="field.disabled"
                :maxlength="field.maxLength"
                :minlength="field.minLength"
                :pattern="field.pattern"
                class="w-full px-4 py-2 rounded-lg bg-white/5 border border-white/10 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-pink-500/50 focus:border-transparent disabled:opacity-50 disabled:cursor-not-allowed"
                @input="field.onInput && field.onInput($event)"
                @change="field.onChange && field.onChange($event)"
                @keyup="field.onKeyup && field.onKeyup($event)"
                @keydown="field.onKeydown && field.onKeydown($event)"
                @blur="field.onBlur && field.onBlur($event)"
                @focus="field.onFocus && field.onFocus($event)"
              />

              <!-- Textarea -->
              <textarea
                v-else-if="field.type === 'textarea'"
                :id="field.name"
                v-model="formData[field.name]"
                :placeholder="field.placeholder"
                :required="field.required"
                :disabled="field.disabled"
                :rows="field.rows || 4"
                :maxlength="field.maxLength"
                class="w-full px-4 py-2 rounded-lg bg-white/5 border border-white/10 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-pink-500/50 focus:border-transparent disabled:opacity-50 disabled:cursor-not-allowed resize-none"
                @input="field.onInput && field.onInput($event)"
                @change="field.onChange && field.onChange($event)"
                @keyup="field.onKeyup && field.onKeyup($event)"
                @keydown="field.onKeydown && field.onKeydown($event)"
                @blur="field.onBlur && field.onBlur($event)"
                @focus="field.onFocus && field.onFocus($event)"
              ></textarea>

              <!-- Select -->
              <Select2
                v-else-if="field.type === 'select'"
                :id="field.name"
                v-model="formData[field.name]"
                :options="field.options?.items.map(option => ({
                  id: option.value,
                  text: option.label
                }))"
                :settings="{
                  placeholder: field.placeholder,
                  allowClear: true,
                  disabled: field.disabled,
                  required: field.required,
                  theme: 'light',
                  containerCssClass: 'w-full px-4 py-2 rounded-lg bg-white/5 border border-white/10 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-pink-500/50 focus:border-transparent disabled:opacity-50 disabled:cursor-not-allowed text-white'
                }"
                class="text-white"
                @change="(val) => field.onChange && field.onChange({ target: { value: val } })"
                @blur="field.onBlur && field.onBlur($event)" 
                @focus="field.onFocus && field.onFocus($event)"
              />

              <!-- Checkbox -->
              <div v-else-if="field.type === 'checkbox'" class="flex items-center">
                <input
                  type="checkbox"
                  :id="field.name"
                  v-model="formData[field.name]"
                  :required="field.required"
                  :disabled="field.disabled"
                  class="h-4 w-4 rounded border-white/10 bg-white/5 text-pink-500 focus:ring-pink-500/50 focus:ring-offset-gray-900"
                  @change="field.onChange && field.onChange($event)"
                />
                <label :for="field.name" class="ml-2 block text-sm text-gray-300">
                  {{ field.checkboxLabel }}
                </label>
              </div>

              <!-- Radio Group -->
              <div v-else-if="field.type === 'radio'" class="space-y-2">
                <div 
                  v-for="option in field.options?.items" 
                  :key="option.value" 
                  class="flex items-center"
                >
                  <input
                    type="radio"
                    :id="`${field.name}-${option.value}`"
                    :name="field.name"
                    :value="option.value"
                    v-model="formData[field.name]"
                    :required="field.required"
                    :disabled="field.disabled"
                    class="h-4 w-4 border-white/10 bg-white/5 text-pink-500 focus:ring-pink-500/50 focus:ring-offset-gray-900"
                    @change="field.onChange && field.onChange($event)"
                  />
                  <label 
                    :for="`${field.name}-${option.value}`" 
                    class="ml-2 block text-sm text-gray-300"
                  >
                    {{ option.label }}
                  </label>
                </div>
              </div>

              <!-- Date Input -->
              <input
                v-else-if="field.type === 'date'"
                type="date"
                :id="field.name"
                v-model="formData[field.name]"
                :required="field.required"
                :disabled="field.disabled"
                :min="field.min"
                :max="field.max"
                class="w-full px-4 py-2 rounded-lg bg-white/5 border border-white/10 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-pink-500/50 focus:border-transparent disabled:opacity-50 disabled:cursor-not-allowed"
                @change="field.onChange && field.onChange($event)"
                @blur="field.onBlur && field.onBlur($event)"
              />

              <!-- Error Message -->
              <p v-if="errors[field.name]" class="mt-1 text-sm text-pink-400">
                {{ errors[field.name] }}
              </p>

              <!-- Helper Text -->
              <p v-if="field.helper" class="mt-1 text-sm text-gray-400">
                {{ field.helper }}
              </p>
            </div>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-end space-x-4">
          <button
            type="button"
            @click="handleCancel"
            class="px-4 py-2 rounded-lg bg-white/10 text-white hover:bg-white/20 transition-all"
          >
            Cancel
          </button>
          <button
            type="submit"
            :disabled="isSubmitting"
            class="px-4 py-2 rounded-lg bg-gradient-to-r from-pink-500 to-purple-600 text-white hover:shadow-lg hover:shadow-pink-500/30 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2"
          >
            <svg 
              v-if="isSubmitting" 
              class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" 
              fill="none" 
              viewBox="0 0 24 24"
            >
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>{{ isSubmitting ? 'Creating...' : 'Create Client' }}</span>
          </button>
        </div>
      </form>
    </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { useRouter } from 'vue-router';
import ClientLayout from './ClientLayout.vue';
import Select2 from '../common/Select2.vue';

defineOptions({
  layout: ClientLayout,
});

const router = useRouter();
const isSubmitting = ref(false);
const errors = reactive({});

// Form fields configuration
const formFields = [
  {
    name: 'name',
    label: 'Client Name',
    type: 'text',
    placeholder: 'Enter client name',
    required: true,
    maxLength: 100,
    options: {
      col: 'col-span-6'
    },
    onInput: (e) => {
      // Example: Auto-capitalize first letter
      const value = e.target.value;
      if (value && value[0] !== value[0].toUpperCase()) {
        e.target.value = value.charAt(0).toUpperCase() + value.slice(1);
      }
    },
    onBlur: (e) => {
      // Example: Validate name
      if (e.target.value.length < 2) {
        errors.name = 'Name must be at least 2 characters long';
      } else {
        errors.name = '';
      }
    }
  },
  {
    name: 'email',
    label: 'Email Address',
    type: 'email',
    placeholder: 'Enter email address',
    required: true,
    pattern: '[a-z0-9._%+-]+@[a-z0-9.-]+\\.[a-z]{2,}$',
    options: {
      col: 'col-span-6'
    },
    onBlur: (e) => {
      // Example: Validate email
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(e.target.value)) {
        errors.email = 'Please enter a valid email address';
      } else {
        errors.email = '';
      }
    }
  },
  {
    name: 'phone',
    label: 'Phone Number',
    type: 'tel',
    placeholder: 'Enter phone number',
    required: false,
    pattern: '[0-9]{10}',
    helper: 'Enter a 10-digit phone number',
    options: {
      col: 'col-span-6'
    },
    onInput: (e) => {
      // Example: Format phone number
      let value = e.target.value.replace(/\D/g, '');
      if (value.length > 10) value = value.slice(0, 10);
      e.target.value = value;
    }
  },
  {
    name: 'status',
    label: 'Status',
    type: 'select',
    placeholder: 'Select status',
    required: true,
    options: {
      col: 'col-span-6',
      items: [
        { value: 'active', label: 'Active' },
        { value: 'inactive', label: 'Inactive' },
        { value: 'pending', label: 'Pending' }
      ]
    },
    onChange: (e) => {
      // Now e.target.value will be the selected value directly
      console.log('Status changed to:', e.target.value);
      // You can add additional validation or logic here
      if (!e.target.value) {
        errors.status = 'Status is required';
      } else {
        errors.status = '';
      }
    }
  },
  {
    name: 'notes',
    label: 'Notes',
    type: 'textarea',
    placeholder: 'Enter any additional notes',
    required: false,
    rows: 4,
    maxLength: 500,
    helper: 'Maximum 500 characters',
    options: {
      col: 'col-span-12'
    }
  },
  {
    name: 'startDate',
    label: 'Start Date',
    type: 'date',
    required: true,
    min: new Date().toISOString().split('T')[0],
    options: {
      col: 'col-span-6'
    },
    onChange: (e) => {
      // Example: Validate date
      const selectedDate = new Date(e.target.value);
      const today = new Date();
      if (selectedDate < today) {
        errors.startDate = 'Start date cannot be in the past';
      } else {
        errors.startDate = '';
      }
    }
  },
  {
    name: 'notifications',
    label: 'Enable Notifications',
    type: 'checkbox',
    checkboxLabel: 'Send email notifications for updates',
    required: false,
    options: {
      col: 'col-span-6'
    },
    onChange: (e) => {
      // Example: Handle notification preference change
      console.log('Notifications enabled:', e.target.checked);
    }
  },
  {
    name: 'type',
    label: 'Client Type',
    type: 'radio',
    required: true,
    options: {
      col: 'col-span-12',
      items: [
        { value: 'individual', label: 'Individual' },
        { value: 'business', label: 'Business' },
        { value: 'enterprise', label: 'Enterprise' }
      ]
    },
    onChange: (e) => {
      // Example: Handle client type change
      console.log('Client type changed to:', e.target.value);
    }
  }
];

// Form data
const formData = reactive(
  formFields.reduce((acc, field) => {
    acc[field.name] = field.type === 'checkbox' ? false : '';
    return acc;
  }, {})
);

// Form submission
const handleSubmit = async () => {
  // Reset errors
  Object.keys(errors).forEach(key => delete errors[key]);

  // Validate required fields
  formFields.forEach(field => {
    if (field.required && !formData[field.name]) {
      errors[field.name] = `${field.label} is required`;
    }
  });

  // If there are errors, don't submit
  if (Object.keys(errors).length > 0) {
    return;
  }

  isSubmitting.value = true;

  try {
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 1500));
    
    // Here you would typically make an API call
    console.log('Form submitted:', formData);
    
    // Redirect to list view
    router.push({ name: 'client.list' });
  } catch (error) {
    console.error('Error submitting form:', error);
    // Handle error (e.g., show error message)
  } finally {
    isSubmitting.value = false;
  }
};

// Cancel handler
const handleCancel = () => {
  router.push({ name: 'client.list' });
};
</script>

<style scoped>
/* Custom styles for form elements */
input[type="date"]::-webkit-calendar-picker-indicator {
  filter: invert(1);
  opacity: 0.5;
}

input[type="date"]::-webkit-calendar-picker-indicator:hover {
  opacity: 1;
}

/* Custom scrollbar for textarea */
textarea::-webkit-scrollbar {
  width: 8px;
}

textarea::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.1);
  border-radius: 4px;
}

textarea::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.2);
  border-radius: 4px;
}

textarea::-webkit-scrollbar-thumb:hover {
  background: rgba(255, 255, 255, 0.3);
}
</style> 