<template>
  <div class="flex">
    <!-- Main content - User Table -->
    <div class="flex-1 overflow-x-auto">
      <div class="mb-4 flex justify-between items-center">
        <h2 class="text-xl font-semibold">Users</h2>
        <button 
          @click="openSidebar('create')" 
          class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
        >
          Add User
        </button>
      </div>

      <!-- Loading state -->
      <div v-if="userStore.loading" class="py-8 text-center">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500"></div>
        <p class="mt-2 text-gray-500">Loading...</p>
      </div>

      <!-- Error state -->
      <div v-else-if="userStore.error" class="py-8 text-center">
        <p class="text-red-500">{{ userStore.error }}</p>
        <button 
          @click="userStore.fetchUsers()" 
          class="mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
        >
          Retry
        </button>
      </div>

      <!-- User table -->
      <div v-else class="overflow-x-auto shadow-md rounded-lg">
        <table class="min-w-full bg-white">
          <thead class="bg-gray-50">
            <tr>
              <th 
                v-for="column in columns" 
                :key="column.key" 
                @click="userStore.setSort(column.key)" 
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
              >
                {{ column.label }}
                <span v-if="userStore.sortBy === column.key">
                  {{ userStore.sortDirection === 'asc' ? '↑' : '↓' }}
                </span>
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="user in userStore.users" :key="user.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap">
                {{ user.name }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                {{ user.email }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span 
                  class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                  :class="{
                    'bg-green-100 text-green-800': user.role === 'admin',
                    'bg-blue-100 text-blue-800': user.role === 'manager',
                    'bg-gray-100 text-gray-800': user.role === 'user'
                  }"
                >
                  {{ user.role }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span 
                  class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                  :class="{
                    'bg-green-100 text-green-800': user.status === 'active',
                    'bg-yellow-100 text-yellow-800': user.status === 'pending',
                    'bg-red-100 text-red-800': user.status === 'inactive'
                  }"
                >
                  {{ user.status }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <button 
                  @click="openSidebar('edit', user)"
                  class="text-indigo-600 hover:text-indigo-900 mr-3"
                >
                  Edit
                </button>
                <button 
                  @click="confirmDelete(user)"
                  class="text-red-600 hover:text-red-900"
                >
                  Delete
                </button>
              </td>
            </tr>
            <tr v-if="userStore.users.length === 0">
              <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                No users found
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="userStore.pagination.total > 0" class="flex justify-between items-center mt-4">
        <div class="text-sm text-gray-500">
          Showing {{ (userStore.pagination.currentPage - 1) * userStore.pagination.perPage + 1 }} to 
          {{ Math.min(userStore.pagination.currentPage * userStore.pagination.perPage, userStore.pagination.total) }} of 
          {{ userStore.pagination.total }} users
        </div>
        <div class="flex space-x-1">
          <button 
            v-for="page in totalPages" 
            :key="page" 
            @click="userStore.setPage(page)"
            class="px-3 py-1 rounded"
            :class="userStore.pagination.currentPage === page ? 'bg-blue-600 text-white' : 'bg-gray-200 hover:bg-gray-300'"
          >
            {{ page }}
          </button>
        </div>
      </div>
    </div>

    <!-- Sidebar Form -->
    <div 
      v-if="sidebarOpen" 
      class="w-96 border-l border-gray-200 bg-white p-4 fixed right-0 top-0 h-full shadow-xl transform transition-transform duration-300"
      :class="{ 'translate-x-0': sidebarOpen, 'translate-x-full': !sidebarOpen }"
    >
      <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-medium">{{ sidebarMode === 'create' ? 'Add New User' : 'Edit User' }}</h3>
        <button @click="sidebarOpen = false" class="text-gray-500 hover:text-gray-700">
          <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <form @submit.prevent="saveUser">
        <!-- Name -->
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700">Name</label>
          <input 
            v-model="formData.name" 
            type="text" 
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            :class="{ 'border-red-500': formErrors.name }"
          >
          <p v-if="formErrors.name" class="mt-1 text-sm text-red-600">{{ formErrors.name[0] }}</p>
        </div>

        <!-- Email -->
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700">Email</label>
          <input 
            v-model="formData.email" 
            type="email" 
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            :class="{ 'border-red-500': formErrors.email }"
          >
          <p v-if="formErrors.email" class="mt-1 text-sm text-red-600">{{ formErrors.email[0] }}</p>
        </div>

        <!-- Password -->
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700">Password</label>
          <input 
            v-model="formData.password" 
            type="password" 
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            :class="{ 'border-red-500': formErrors.password }"
          >
          <p v-if="formErrors.password" class="mt-1 text-sm text-red-600">{{ formErrors.password[0] }}</p>
        </div>

        <!-- Role -->
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700">Role</label>
          <select 
            v-model="formData.role" 
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            :class="{ 'border-red-500': formErrors.role }"
          >
            <option value="admin">Admin</option>
            <option value="manager">Manager</option>
            <option value="user">User</option>
          </select>
          <p v-if="formErrors.role" class="mt-1 text-sm text-red-600">{{ formErrors.role[0] }}</p>
        </div>

        <!-- Status -->
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700">Status</label>
          <select 
            v-model="formData.status" 
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            :class="{ 'border-red-500': formErrors.status }"
          >
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
            <option value="pending">Pending</option>
          </select>
          <p v-if="formErrors.status" class="mt-1 text-sm text-red-600">{{ formErrors.status[0] }}</p>
        </div>

        <div class="mt-6">
          <button 
            type="submit" 
            class="w-full px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            :disabled="isSubmitting"
          >
            <span v-if="isSubmitting" class="inline-block animate-spin rounded-full h-4 w-4 border-t-2 border-b-2 border-white mr-2"></span>
            {{ sidebarMode === 'create' ? 'Create User' : 'Update User' }}
          </button>
        </div>
      </form>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white p-6 rounded-lg shadow-xl max-w-md w-full">
        <h3 class="text-lg font-medium mb-4">Confirm Deletion</h3>
        <p class="mb-6">Are you sure you want to delete user "{{ userToDelete?.name }}"? This action cannot be undone.</p>
        <div class="flex justify-end space-x-3">
          <button 
            @click="showDeleteModal = false" 
            class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-100"
          >
            Cancel
          </button>
          <button 
            @click="deleteUser" 
            class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
            :disabled="isDeleting"
          >
            <span v-if="isDeleting" class="inline-block animate-spin rounded-full h-4 w-4 border-t-2 border-b-2 border-white mr-2"></span>
            Delete
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useUserStore } from '../store/users';

const userStore = useUserStore();

// Table Columns
const columns = [
  { key: 'name', label: 'Name' },
  { key: 'email', label: 'Email' },
  { key: 'role', label: 'Role' },
  { key: 'status', label: 'Status' },
];

// Sidebar State
const sidebarOpen = ref(false);
const sidebarMode = ref('create'); // 'create' or 'edit'
const formData = ref({
  name: '',
  email: '',
  password: '',
  role: 'user',
  status: 'pending'
});
const formErrors = ref({});
const isSubmitting = ref(false);

// Delete Modal State
const showDeleteModal = ref(false);
const userToDelete = ref(null);
const isDeleting = ref(false);

// Computed
const totalPages = computed(() => {
  return Math.ceil(userStore.pagination.total / userStore.pagination.perPage);
});

// Lifecycle
onMounted(() => {
  userStore.fetchUsers();
});

// Methods
function openSidebar(mode, user = null) {
  sidebarMode.value = mode;
  sidebarOpen.value = true;
  formErrors.value = {};
  
  if (mode === 'edit' && user) {
    formData.value = { ...user };
    // Don't show current password in edit mode
    formData.value.password = '';
  } else {
    // Reset form for create mode
    formData.value = {
      name: '',
      email: '',
      password: '',
      role: 'user',
      status: 'pending'
    };
  }
}

async function saveUser() {
  isSubmitting.value = true;
  formErrors.value = {};
  
  try {
    let result;
    
    if (sidebarMode.value === 'create') {
      result = await userStore.createUser(formData.value);
    } else {
      result = await userStore.updateUser(formData.value.id, formData.value);
    }
    
    if (result.success) {
      sidebarOpen.value = false;
    } else {
      formErrors.value = result.errors || {};
    }
  } catch (error) {
    console.error('Failed to save user:', error);
  } finally {
    isSubmitting.value = false;
  }
}

function confirmDelete(user) {
  userToDelete.value = user;
  showDeleteModal.value = true;
}

async function deleteUser() {
  if (!userToDelete.value) return;
  
  isDeleting.value = true;
  
  try {
    const result = await userStore.deleteUser(userToDelete.value.id);
    
    if (result.success) {
      showDeleteModal.value = false;
      userToDelete.value = null;
    }
  } catch (error) {
    console.error('Failed to delete user:', error);
  } finally {
    isDeleting.value = false;
  }
}
</script> 