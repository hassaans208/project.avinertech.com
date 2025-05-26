<template>
  <div class="h-screen flex flex-col overflow-hidden">
    <!-- Header Section -->
    <div class="flex-none space-y-8 p-6">
      <!-- Breadcrumbs -->
      <Breadcrumbs :items="[
        { name: 'Setup', path: 'setup.index' },
        { name: 'Database Schema' }
      ]" />

      <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-white">Database Schema</h1>
        <button 
          @click="openNewModelModal"
          class="px-4 py-2 bg-purple-500/20 hover:bg-purple-500/30 text-purple-300 rounded-lg transition-colors"
        >
          Add New Model
        </button>
      </div>
    </div>

    <!-- Models List - Full Height Scrollable -->
    <div class="flex-1 overflow-hidden px-6 pb-6">
      <div class="bg-white/10 backdrop-blur-lg rounded-xl border border-white/20 h-full flex flex-col">
        <div class="flex-1 overflow-y-auto p-6">
          <div class="space-y-4">
            <SchemaModelListItem
              v-for="model in models"
              :key="model.name"
              :model="model"
              @edit="openEditModal"
              @delete="confirmDelete"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- Model Modal -->
    <SchemaModelModal
      v-model="currentModel"
      :show="showModal"
      :is-editing="!!editingModel"
      :is-saving="isSaving"
      @close="closeModal"
      @save="saveModel"
      @add-field="addField"
      @remove-field="removeField"
      @edit-logic="openLogicModal"
    />

    <!-- Logic Modal -->
    <div v-if="showLogicModal" class="fixed inset-0 z-50 overflow-hidden">
      <!-- Backdrop -->
      <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="closeLogicModal"></div>

      <!-- Modal Content -->
      <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="w-full max-w-2xl bg-gray-800 rounded-xl border border-white/10 shadow-xl">
          <!-- Header -->
          <div class="flex items-center justify-between p-4 border-b border-white/10">
            <h3 class="text-lg font-medium text-white">
              Field Logic: {{ currentField?.name || 'Untitled Field' }}
            </h3>
            <button 
              @click="closeLogicModal"
              class="text-gray-400 hover:text-white"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Content -->
          <div class="p-4">
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-400 mb-2">
                  Logic Expression
                </label>
                <textarea
                  v-model="currentFieldLogic"
                  rows="6"
                  class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:border-blue-500 font-mono text-sm"
                  placeholder="Enter field logic expression..."
                ></textarea>
              </div>
              <div class="text-sm text-gray-400">
                <p>Available variables:</p>
                <ul class="list-disc list-inside mt-2 space-y-1">
                  <li><code class="text-blue-400">$value</code> - Current field value</li>
                  <li><code class="text-blue-400">$model</code> - Current model data</li>
                  <li><code class="text-blue-400">$user</code> - Current user data</li>
                </ul>
              </div>
            </div>
          </div>

          <!-- Footer -->
          <div class="flex justify-end gap-3 p-4 border-t border-white/10">
            <button 
              @click="closeLogicModal"
              class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-lg transition-colors"
            >
              Cancel
            </button>
            <button 
              @click="saveFieldLogic"
              class="px-4 py-2 bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 rounded-lg transition-colors"
            >
              Save Logic
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Message Box -->
    <MessageBox
      :show="!!message"
      :message="message"
      :type="messageType"
      @close="message = null"
    />

    <!-- Confirm Dialog -->
    <ConfirmDialog
      :show="showConfirmDialog"
      title="Delete Model"
      :message="confirmDialogMessage"
      type="danger"
      confirm-text="Delete"
      @confirm="handleDeleteConfirm"
      @cancel="showConfirmDialog = false"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import Breadcrumbs from '../common/Breadcrumbs.vue';
import MessageBox from '../common/MessageBox.vue';
import ConfirmDialog from '../common/ConfirmDialog.vue';
import SchemaField from './SchemaField.vue';
import SchemaModelDetails from './SchemaModelDetails.vue';
import SchemaModelListItem from './SchemaModelListItem.vue';
import SchemaModelModal from './SchemaModelModal.vue';
import { generateMigration, generateModel, generateFactory } from '../../utils/schemaUtils';
import DashboardLayout from './DashboardLayout.vue';

defineOptions({
  layout: DashboardLayout,
});

// State
const showModal = ref(false);
const showConfirmDialog = ref(false);
const editingModel = ref(null);
const modelToDelete = ref(null);
const message = ref(null);
const messageType = ref('info');
const isSaving = ref(false);

const currentModel = ref({
  name: '',
  tableType: 'regular',
  description: '',
  fields: []
});

// Mock data for development
const models = ref([
  {
    name: 'User',
    tableType: 'regular',
    description: 'User management and authentication',
    fields: [
      { name: 'id', type: 'integer', nullable: false, unique: true, indexed: true },
      { name: 'name', type: 'string', length: 255, nullable: false, unique: false, indexed: true },
      { name: 'email', type: 'string', length: 255, nullable: false, unique: true, indexed: true, encrypted: true },
      { name: 'password', type: 'string', length: 255, nullable: false, unique: false, indexed: false, encrypted: true },
      { name: 'created_at', type: 'datetime', nullable: false, unique: false, indexed: false }
    ]
  },
  {
    name: 'Post',
    tableType: 'regular',
    description: 'Blog posts and articles',
    fields: [
      { name: 'id', type: 'integer', nullable: false, unique: true, indexed: true },
      { name: 'title', type: 'string', length: 255, nullable: false, unique: false, indexed: true },
      { name: 'content', type: 'text', nullable: false, unique: false, indexed: false },
      { name: 'user_id', type: 'integer', nullable: false, unique: false, indexed: true },
      { name: 'status', type: 'string', length: 20, nullable: false, unique: false, indexed: true },
      { name: 'published_at', type: 'datetime', nullable: true, unique: false, indexed: true }
    ]
  },
  {
    name: 'UserRole',
    tableType: 'pivot',
    description: 'User role assignments',
    fields: [
      { name: 'user_id', type: 'integer', nullable: false, unique: false, indexed: true },
      { name: 'role_id', type: 'integer', nullable: false, unique: false, indexed: true },
      { name: 'assigned_at', type: 'datetime', nullable: false, unique: false, indexed: false }
    ]
  }
]);

// Logic Modal State
const showLogicModal = ref(false);
const currentFieldLogic = ref('');
const currentField = ref(null);

const openNewModelModal = () => {
  editingModel.value = null;
  currentModel.value = {
    name: '',
    fields: []
  };
  showModal.value = true;
};

const openEditModal = (model) => {
  editingModel.value = model;
  currentModel.value = JSON.parse(JSON.stringify(model)); // Deep copy
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  editingModel.value = null;
  currentModel.value = {
    name: '',
    fields: []
  };
};

const addField = () => {
  currentModel.value.fields.push({
    name: '',
    type: 'string',
    nullable: false,
    unique: false,
    indexed: false,
    encrypted: false,
    length: null,
    logic: '' // Add logic field
  });
};

const removeField = (index) => {
  currentModel.value.fields.splice(index, 1);
};

const confirmDelete = (model) => {
  modelToDelete.value = model;
  confirmDialogMessage.value = `Are you sure you want to delete the "${model.name}" model? This action cannot be undone.`;
  showConfirmDialog.value = true;
};

const handleDeleteConfirm = async () => {
  try {
    const index = models.value.findIndex(m => m.name === modelToDelete.value.name);
    if (index !== -1) {
      models.value.splice(index, 1);
      showMessage('Model deleted successfully', 'success');
    }
  } catch (error) {
    showMessage('Failed to delete model', 'error');
  } finally {
    showConfirmDialog.value = false;
    modelToDelete.value = null;
  }
};

const showMessage = (msg, type = 'info') => {
  message.value = msg;
  messageType.value = type;
};

const saveModel = async () => {
  isSaving.value = true;
  try {
    if (editingModel.value) {
      // Update existing model
      const index = models.value.findIndex(m => m.name === editingModel.value.name);
      if (index !== -1) {
        models.value[index] = { ...currentModel.value };
      }
    } else {
      // Add new model
      models.value.push({ ...currentModel.value });
    }
    showMessage('Model saved successfully', 'success');
    closeModal();
  } catch (error) {
    console.error('Error saving model:', error);
    showMessage('Failed to save model', 'error');
  } finally {
    isSaving.value = false;
  }
};

const openLogicModal = ({ index, logic }) => {
  console.log('Opening logic modal:', { index, logic }); // Add debug log
  if (!currentModel.value?.fields?.[index]) {
    console.error('Field not found:', index);
    return;
  }
  currentField.value = currentModel.value.fields[index];
  currentFieldLogic.value = logic || '';
  showLogicModal.value = true;
};

const closeLogicModal = () => {
  showLogicModal.value = false;
  currentFieldLogic.value = '';
  currentField.value = null;
};

const saveFieldLogic = () => {
  if (currentField.value) {
    const index = currentModel.value.fields.findIndex(f => f === currentField.value);
    if (index !== -1) {
      currentModel.value.fields[index].logic = currentFieldLogic.value;
    }
  }
  closeLogicModal();
};
</script>