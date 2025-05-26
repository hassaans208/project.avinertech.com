<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
      <div>
        <h2 class="text-2xl font-bold text-white">Website Editor</h2>
        <p class="mt-1 text-sm text-gray-400">Design and customize your website</p>
      </div>
      <div class="flex space-x-4">
        <button class="px-4 py-2 rounded-lg bg-white/5 border border-white/10 text-white hover:bg-white/10 transition-all">
          <span class="inline-flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            Preview
          </span>
        </button>
        <button class="px-4 py-2 rounded-lg bg-gradient-to-r from-pink-500 to-purple-600 text-white hover:shadow-lg hover:shadow-pink-500/30 transition-all">
          <span class="inline-flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            Save Changes
          </span>
        </button>
      </div>
    </div>

    <!-- Main Editor -->
    <div class="grid grid-cols-12 gap-6">
      <!-- Components Panel -->
      <div class="col-span-3 space-y-6">
        <!-- Search -->
        <div class="relative">
          <input
            type="text"
            placeholder="Search components..."
            class="w-full px-4 py-2 pl-10 rounded-lg bg-white/5 border border-white/10 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-pink-500/50"
          />
          <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
          </svg>
        </div>

        <!-- Component Categories -->
        <div class="space-y-4">
          <div v-for="category in componentCategories" :key="category.name" class="space-y-2">
            <h3 class="text-sm font-medium text-gray-400">{{ category.name }}</h3>
            <div class="space-y-2">
              <button
                v-for="component in category.components"
                :key="component.name"
                class="w-full px-4 py-3 rounded-lg bg-white/5 hover:bg-white/10 transition-all text-left group"
                draggable="true"
                @dragstart="onDragStart($event, component)"
              >
                <div class="flex items-center">
                  <div class="p-2 rounded-lg" :class="component.bgColor">
                    <component :is="component.icon" class="w-5 h-5" :class="component.iconColor" />
                  </div>
                  <div class="ml-3">
                    <p class="text-sm font-medium text-white">{{ component.name }}</p>
                    <p class="text-xs text-gray-400">{{ component.description }}</p>
                  </div>
                </div>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Canvas -->
      <div class="col-span-6">
        <div 
          class="bg-white/5 backdrop-blur-lg rounded-xl border border-white/10 p-6 min-h-[600px]"
          @dragover.prevent
          @drop="onDrop"
        >
          <!-- Drop Zone -->
          <div 
            v-if="!canvasComponents.length"
            class="h-full flex flex-col items-center justify-center text-center p-8 border-2 border-dashed border-white/10 rounded-lg"
          >
            <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
            </svg>
            <p class="text-lg font-medium text-white">Drag and drop components here</p>
            <p class="mt-1 text-sm text-gray-400">Start building your website by dragging components from the left panel</p>
          </div>

          <!-- Canvas Components -->
          <div v-else class="space-y-4">
            <div
              v-for="(component, index) in canvasComponents"
              :key="index"
              class="relative group"
            >
              <div class="p-4 rounded-lg bg-white/5 border border-white/10">
                <div class="flex items-center justify-between mb-2">
                  <div class="flex items-center">
                    <div class="p-2 rounded-lg" :class="component.bgColor">
                      <component :is="component.icon" class="w-5 h-5" :class="component.iconColor" />
                    </div>
                    <span class="ml-2 text-sm font-medium text-white">{{ component.name }}</span>
                  </div>
                  <div class="flex items-center space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button class="p-1 rounded hover:bg-white/10">
                      <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                      </svg>
                    </button>
                    <button class="p-1 rounded hover:bg-white/10">
                      <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                      </svg>
                    </button>
                  </div>
                </div>
                <div class="p-4 bg-white/5 rounded-lg">
                  <p class="text-sm text-gray-400">Component Preview</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Properties Panel -->
      <div class="col-span-3 space-y-6">
        <!-- Selected Component Properties -->
        <div v-if="selectedComponent" class="bg-white/5 backdrop-blur-lg rounded-xl border border-white/10 p-6">
          <h3 class="text-lg font-medium text-white mb-4">Properties</h3>
          <div class="space-y-4">
            <div v-for="prop in selectedComponent.properties" :key="prop.name" class="space-y-2">
              <label :for="prop.name" class="block text-sm font-medium text-gray-400">
                {{ prop.label }}
              </label>
              <input
                v-if="prop.type === 'text'"
                :type="prop.type"
                :id="prop.name"
                v-model="prop.value"
                class="w-full px-4 py-2 rounded-lg bg-white/5 border border-white/10 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-pink-500/50"
              />
              <select
                v-else-if="prop.type === 'select'"
                :id="prop.name"
                v-model="prop.value"
                class="w-full px-4 py-2 rounded-lg bg-white/5 border border-white/10 text-white focus:outline-none focus:ring-2 focus:ring-pink-500/50"
              >
                <option v-for="option in prop.options" :key="option.value" :value="option.value">
                  {{ option.label }}
                </option>
              </select>
            </div>
          </div>
        </div>

        <!-- No Selection -->
        <div v-else class="bg-white/5 backdrop-blur-lg rounded-xl border border-white/10 p-6">
          <div class="text-center">
            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            <p class="text-sm text-gray-400">Select a component to edit its properties</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import ClientLayout from './ClientLayout.vue';

defineOptions({
  layout: ClientLayout,
});

// Component Categories
const componentCategories = [
  {
    name: 'Layout',
    components: [
      {
        name: 'Container',
        description: 'A responsive container for content',
        icon: 'ContainerIcon',
        bgColor: 'bg-blue-500/10',
        iconColor: 'text-blue-500',
        properties: [
          {
            name: 'width',
            label: 'Width',
            type: 'select',
            value: 'full',
            options: [
              { value: 'full', label: 'Full Width' },
              { value: 'container', label: 'Container' },
              { value: 'narrow', label: 'Narrow' }
            ]
          },
          {
            name: 'padding',
            label: 'Padding',
            type: 'select',
            value: 'normal',
            options: [
              { value: 'none', label: 'None' },
              { value: 'small', label: 'Small' },
              { value: 'normal', label: 'Normal' },
              { value: 'large', label: 'Large' }
            ]
          }
        ]
      },
      {
        name: 'Grid',
        description: 'Responsive grid layout',
        icon: 'GridIcon',
        bgColor: 'bg-purple-500/10',
        iconColor: 'text-purple-500',
        properties: [
          {
            name: 'columns',
            label: 'Columns',
            type: 'select',
            value: '3',
            options: [
              { value: '2', label: '2 Columns' },
              { value: '3', label: '3 Columns' },
              { value: '4', label: '4 Columns' }
            ]
          }
        ]
      }
    ]
  },
  {
    name: 'Content',
    components: [
      {
        name: 'Heading',
        description: 'Section heading',
        icon: 'HeadingIcon',
        bgColor: 'bg-pink-500/10',
        iconColor: 'text-pink-500',
        properties: [
          {
            name: 'text',
            label: 'Text',
            type: 'text',
            value: 'Heading'
          },
          {
            name: 'size',
            label: 'Size',
            type: 'select',
            value: 'h2',
            options: [
              { value: 'h1', label: 'H1' },
              { value: 'h2', label: 'H2' },
              { value: 'h3', label: 'H3' }
            ]
          }
        ]
      },
      {
        name: 'Text',
        description: 'Rich text content',
        icon: 'TextIcon',
        bgColor: 'bg-green-500/10',
        iconColor: 'text-green-500',
        properties: [
          {
            name: 'content',
            label: 'Content',
            type: 'text',
            value: 'Enter your text here'
          }
        ]
      }
    ]
  }
];

// Canvas State
const canvasComponents = ref([]);
const selectedComponent = ref(null);

// Drag and Drop Handlers
const onDragStart = (event, component) => {
  event.dataTransfer.setData('component', JSON.stringify(component));
};

const onDrop = (event) => {
  const component = JSON.parse(event.dataTransfer.getData('component'));
  canvasComponents.value.push({ ...component });
};

// Icon Components
const ContainerIcon = {
  template: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path>
  </svg>`
};

const GridIcon = {
  template: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
  </svg>`
};

const HeadingIcon = {
  template: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
  </svg>`
};

const TextIcon = {
  template: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
  </svg>`
};
</script>

<style scoped>
/* Add any component-specific styles here */
</style> 