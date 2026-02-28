<template>
  <div>
    <label v-if="label" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
      {{ label }}
      <span v-if="required" class="text-red-500">*</span>
    </label>

    <!-- Search Input -->
    <div class="relative mb-2">
      <input
        v-model="searchQuery"
        @input="searchUsers"
        type="text"
        placeholder="Поиск пользователя..."
        class="w-full px-4 py-2 pr-10 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white"
      >
      <svg class="absolute right-3 top-3 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
      </svg>
    </div>

    <!-- Selected User Display -->
    <div v-if="selectedUser" class="mb-3 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg flex items-center justify-between">
      <div class="flex items-center space-x-3">
        <div class="w-10 h-10 rounded-full bg-green-600 flex items-center justify-center text-white font-semibold">
          {{ getUserInitials(selectedUser) }}
        </div>
        <div>
          <p class="text-sm font-medium text-green-900 dark:text-green-100">{{ selectedUser.name }}</p>
          <p class="text-xs text-green-700 dark:text-green-300">{{ selectedUser.email }}</p>
        </div>
      </div>
      <button
        @click="clearSelection"
        type="button"
        class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    <!-- Users List -->
    <div v-if="!selectedUser && users.length > 0" class="border border-gray-300 dark:border-gray-600 rounded-lg max-h-64 overflow-y-auto">
      <button
        v-for="user in users"
        :key="user.id"
        @click="selectUser(user)"
        type="button"
        class="w-full text-left px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 border-b border-gray-200 dark:border-gray-600 last:border-b-0 transition flex items-center space-x-3"
      >
        <div class="w-10 h-10 rounded-full bg-gray-600 flex items-center justify-center text-white font-semibold flex-shrink-0">
          {{ getUserInitials(user) }}
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ user.name }}</p>
          <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ user.email }}</p>
        </div>
      </button>
    </div>

    <div v-if="!selectedUser && users.length === 0 && !loading" class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">
      {{ searchQuery ? 'Пользователи не найдены' : 'Начните вводить для поиска' }}
    </div>

    <div v-if="loading" class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">
      Загрузка...
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';

const props = defineProps({
  modelValue: [Number, String],
  label: String,
  required: Boolean
});

const emit = defineEmits(['update:modelValue']);

const selectedUser = ref(null);
const searchQuery = ref('');
const users = ref([]);
const loading = ref(false);
const searchTimeout = ref(null);

const getUserInitials = (user) => {
  if (!user || !user.name) return 'U';
  const parts = user.name.split(' ');
  if (parts.length >= 2) {
    return (parts[0][0] + parts[1][0]).toUpperCase();
  }
  return user.name.substring(0, 2).toUpperCase();
};

const loadUsers = async (query = '') => {
  loading.value = true;
  try {
    const params = new URLSearchParams();
    if (query) {
      params.append('search', query);
    }
    params.append('per_page', '20');

    const response = await fetch(`/admin/api/users?${params}`, {
      headers: { 'Accept': 'application/json' }
    });

    if (response.ok) {
      const data = await response.json();
      users.value = data.data || data;
    }
  } catch (error) {
    console.error('Failed to load users:', error);
    users.value = [];
  } finally {
    loading.value = false;
  }
};

const searchUsers = () => {
  clearTimeout(searchTimeout.value);
  searchTimeout.value = setTimeout(() => {
    if (searchQuery.value.length >= 2) {
      loadUsers(searchQuery.value);
    } else if (searchQuery.value.length === 0) {
      loadUsers();
    } else {
      users.value = [];
    }
  }, 300);
};

const selectUser = (user) => {
  selectedUser.value = user;
  emit('update:modelValue', user.id);
};

const clearSelection = () => {
  selectedUser.value = null;
  emit('update:modelValue', null);
  searchQuery.value = '';
  users.value = [];
};

// Load selected user if modelValue is set
watch(() => props.modelValue, async (newVal) => {
  if (newVal && !selectedUser.value) {
    loading.value = true;
    try {
      const response = await fetch(`/admin/api/users/${newVal}`, {
        headers: { 'Accept': 'application/json' }
      });
      if (response.ok) {
        selectedUser.value = await response.json();
      }
    } catch (error) {
      console.error('Failed to load user:', error);
    } finally {
      loading.value = false;
    }
  }
}, { immediate: true });

onMounted(() => {
  loadUsers();
});
</script>
