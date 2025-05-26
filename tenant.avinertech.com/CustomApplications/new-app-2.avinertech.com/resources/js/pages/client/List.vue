<template>

    <!-- List Header -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h2 class="text-2xl font-bold text-white">Client List</h2>
        <p class="mt-1 text-sm text-gray-400">Manage and view all your clients</p>
      </div>
      <div class="mt-4 sm:mt-0 flex items-center space-x-4">
        <!-- Search -->
        <div class="relative">
          <input
            type="text"
            v-model="search"
            placeholder="Search clients..."
            class="w-full sm:w-64 px-4 py-2 rounded-lg bg-white/5 border border-white/10 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-pink-500/50 focus:border-transparent"
          />
          <svg class="absolute right-3 top-2.5 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </div>
        <!-- Add New Button -->
        <button class="px-4 py-2 rounded-lg bg-gradient-to-r from-pink-500 to-purple-600 text-white hover:shadow-lg hover:shadow-pink-500/30 transition-all flex items-center space-x-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
          </svg>
          <span>Add Client</span>
        </button>
      </div>
    </div>

    <!-- List Table -->
    <div class="bg-white/5 backdrop-blur-lg rounded-xl border border-white/10 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-white/10">
          <thead class="bg-white/5">
            <tr>
              <th 
                v-for="column in columns" 
                :key="column.key"
                class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider cursor-pointer hover:text-white transition-colors"
                @click="column.sortable ? sort(column.key) : null"
              >
                <div class="flex items-center space-x-1">
                  <span>{{ column.label }}</span>
                  <span v-if="column.sortable" class="flex flex-col">
                    <svg 
                      class="w-3 h-3" 
                      :class="{ 'text-pink-400': sortKey === column.key && sortOrder === 'asc' }"
                      fill="none" 
                      viewBox="0 0 24 24" 
                      stroke="currentColor"
                    >
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                    </svg>
                    <svg 
                      class="w-3 h-3" 
                      :class="{ 'text-pink-400': sortKey === column.key && sortOrder === 'desc' }"
                      fill="none" 
                      viewBox="0 0 24 24" 
                      stroke="currentColor"
                    >
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                  </span>
                </div>
              </th>
              <th class="px-6 py-4 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-white/10">
            <tr v-for="item in paginatedData" :key="item.id" class="hover:bg-white/5 transition-colors">
              <td v-for="column in columns" :key="column.key" class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm" :class="column.class">
                  <template v-if="column.type === 'badge'">
                    <span 
                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                      :class="getBadgeClass(item[column.key])"
                    >
                      {{ item[column.key] }}
                    </span>
                  </template>
                  <template v-else-if="column.type === 'date'">
                    {{ formatDate(item[column.key]) }}
                  </template>
                  <template v-else>
                    {{ item[column.key] }}
                  </template>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <div class="flex items-center justify-end space-x-3">
                  <button class="text-gray-400 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                  </button>
                  <button class="text-gray-400 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                  </button>
                  <button class="text-gray-400 hover:text-red-400 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="px-6 py-4 bg-white/5 border-t border-white/10 flex items-center justify-between">
        <div class="flex-1 flex justify-between sm:hidden">
          <button 
            @click="currentPage--" 
            :disabled="currentPage === 1"
            class="relative inline-flex items-center px-4 py-2 text-sm font-medium rounded-md text-white bg-white/10 hover:bg-white/20 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Previous
          </button>
          <button 
            @click="currentPage++" 
            :disabled="currentPage === totalPages"
            class="ml-3 relative inline-flex items-center px-4 py-2 text-sm font-medium rounded-md text-white bg-white/10 hover:bg-white/20 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Next
          </button>
        </div>
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
          <div>
            <p class="text-sm text-gray-400">
              Showing
              <span class="font-medium text-white">{{ paginationStart }}</span>
              to
              <span class="font-medium text-white">{{ paginationEnd }}</span>
              of
              <span class="font-medium text-white">{{ totalItems }}</span>
              results
            </p>
          </div>
          <div>
            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
              <button 
                @click="currentPage--" 
                :disabled="currentPage === 1"
                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-white/10 bg-white/5 text-sm font-medium text-gray-400 hover:bg-white/10 hover:text-white disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <span class="sr-only">Previous</span>
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
              </button>
              <button 
                v-for="page in displayedPages" 
                :key="page"
                @click="currentPage = page"
                :class="[
                  'relative inline-flex items-center px-4 py-2 border border-white/10 text-sm font-medium',
                  currentPage === page 
                    ? 'z-10 bg-pink-500/20 border-pink-500/50 text-pink-400' 
                    : 'bg-white/5 text-gray-400 hover:bg-white/10 hover:text-white'
                ]"
              >
                {{ page }}
              </button>
              <button 
                @click="currentPage++" 
                :disabled="currentPage === totalPages"
                class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-white/10 bg-white/5 text-sm font-medium text-gray-400 hover:bg-white/10 hover:text-white disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <span class="sr-only">Next</span>
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
              </button>
            </nav>
          </div>
        </div>
      </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import ClientLayout from './ClientLayout.vue';

defineOptions({
  layout: ClientLayout,
});

// Column definitions
const columns = [
  { 
    key: 'id', 
    label: 'ID', 
    sortable: true, 
    searchable: true,
    class: 'text-gray-400'
  },
  { 
    key: 'name', 
    label: 'Name', 
    sortable: true, 
    searchable: true,
    class: 'text-white font-medium'
  },
  { 
    key: 'email', 
    label: 'Email', 
    sortable: true, 
    searchable: true,
    class: 'text-gray-400'
  },
  { 
    key: 'status', 
    label: 'Status', 
    sortable: true, 
    searchable: true,
    type: 'badge',
    class: ''
  },
  { 
    key: 'created_at', 
    label: 'Created At', 
    sortable: true, 
    searchable: false,
    type: 'date',
    class: 'text-gray-400'
  }
];

// Mock data
const mockData = Array.from({ length: 100 }, (_, i) => ({
  id: i + 1,
  name: `Client ${i + 1}`,
  email: `client${i + 1}@example.com`,
  status: ['Active', 'Inactive', 'Pending'][Math.floor(Math.random() * 3)],
  created_at: new Date(Date.now() - Math.floor(Math.random() * 10000000000)).toISOString()
}));

// State
const search = ref('');
const currentPage = ref(1);
const itemsPerPage = 10;
const sortKey = ref('id');
const sortOrder = ref('asc');

// Computed
const filteredData = computed(() => {
  let result = [...mockData];
  
  // Apply search
  if (search.value) {
    const searchLower = search.value.toLowerCase();
    result = result.filter(item => 
      columns.some(column => 
        column.searchable && 
        String(item[column.key]).toLowerCase().includes(searchLower)
      )
    );
  }
  
  // Apply sorting
  result.sort((a, b) => {
    const aValue = a[sortKey.value];
    const bValue = b[sortKey.value];
    
    if (typeof aValue === 'string' && typeof bValue === 'string') {
      return sortOrder.value === 'asc' 
        ? aValue.localeCompare(bValue)
        : bValue.localeCompare(aValue);
    }
    
    return sortOrder.value === 'asc' 
      ? aValue - bValue
      : bValue - aValue;
  });
  
  return result;
});

const totalItems = computed(() => filteredData.value.length);
const totalPages = computed(() => Math.ceil(totalItems.value / itemsPerPage));

const paginatedData = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  const end = start + itemsPerPage;
  return filteredData.value.slice(start, end);
});

const paginationStart = computed(() => (currentPage.value - 1) * itemsPerPage + 1);
const paginationEnd = computed(() => Math.min(currentPage.value * itemsPerPage, totalItems.value));

const displayedPages = computed(() => {
  const pages = [];
  const maxPages = 5;
  
  if (totalPages.value <= maxPages) {
    for (let i = 1; i <= totalPages.value; i++) {
      pages.push(i);
    }
  } else {
    let start = Math.max(1, currentPage.value - Math.floor(maxPages / 2));
    let end = start + maxPages - 1;
    
    if (end > totalPages.value) {
      end = totalPages.value;
      start = Math.max(1, end - maxPages + 1);
    }
    
    for (let i = start; i <= end; i++) {
      pages.push(i);
    }
  }
  
  return pages;
});

// Methods
const sort = (key) => {
  if (sortKey.value === key) {
    sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';
  } else {
    sortKey.value = key;
    sortOrder.value = 'asc';
  }
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
};

const getBadgeClass = (status) => {
  const classes = {
    'Active': 'bg-green-500/20 text-green-400',
    'Inactive': 'bg-gray-500/20 text-gray-400',
    'Pending': 'bg-yellow-500/20 text-yellow-400'
  };
  return classes[status] || 'bg-gray-500/20 text-gray-400';
};
</script>

<style scoped>
/* Add any component-specific styles here */
</style> 