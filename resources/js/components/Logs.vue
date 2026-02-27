<template>
  <div>
    <div class="mb-6">
      <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Логи действий</h2>
      <p class="text-gray-600 dark:text-gray-400 mt-1">История всех действий администраторов</p>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Action Filter -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Действие</label>
          <select v-model="filters.action" @change="loadLogs" class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white">
            <option value="">Все действия</option>
            <option v-for="action in availableActions" :key="action" :value="action">{{ action }}</option>
          </select>
        </div>

        <!-- Entity Type Filter -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Тип сущности</label>
          <select v-model="filters.entity_type" @change="loadLogs" class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white">
            <option value="">Все типы</option>
            <option v-for="entityType in availableEntityTypes" :key="entityType" :value="entityType">{{ entityType }}</option>
          </select>
        </div>

        <!-- Date From -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Дата от</label>
          <input v-model="filters.date_from" @change="loadLogs" type="date" class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white">
        </div>

        <!-- Date To -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Дата до</label>
          <input v-model="filters.date_to" @change="loadLogs" type="date" class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white">
        </div>
      </div>

      <!-- Search -->
      <div class="mt-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Поиск по описанию</label>
        <input v-model="filters.search" @input="debounceSearch" type="text" placeholder="Введите текст для поиска..." class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white">
      </div>

      <!-- Clear Filters -->
      <div class="mt-4 flex justify-end">
        <button @click="clearFilters" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-white rounded-lg transition">
          Сбросить фильтры
        </button>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
    </div>

    <!-- Logs Table -->
    <div v-else class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Время</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Администратор</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Действие</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Сущность</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Описание</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">IP</th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            <tr v-if="logs.length === 0">
              <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                Логи не найдены
              </td>
            </tr>
            <tr v-for="log in logs" :key="log.id" class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                {{ formatDate(log.created_at) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                {{ log.admin?.name || 'N/A' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="getActionBadgeClass(log.action)" class="px-2 py-1 text-xs font-medium rounded">
                  {{ log.action }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                <div v-if="log.entity_type">
                  <div class="font-medium">{{ log.entity_type }}</div>
                  <div v-if="log.entity_id" class="text-xs text-gray-500 dark:text-gray-400">ID: {{ log.entity_id }}</div>
                </div>
                <span v-else class="text-gray-400">—</span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                {{ log.description || '—' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                {{ log.ip_address || '—' }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="pagination.total > 0" class="bg-gray-50 dark:bg-gray-700 px-6 py-4 border-t border-gray-200 dark:border-gray-600">
        <div class="flex items-center justify-between">
          <div class="text-sm text-gray-700 dark:text-gray-300">
            Показано <span class="font-medium">{{ pagination.from }}</span> - <span class="font-medium">{{ pagination.to }}</span> из <span class="font-medium">{{ pagination.total }}</span> записей
          </div>
          <div class="flex space-x-2">
            <button
              v-for="page in visiblePages"
              :key="page"
              @click="goToPage(page)"
              :disabled="page === pagination.current_page"
              :class="{
                [`${bgClass} text-white`]: page === pagination.current_page,
                'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600': page !== pagination.current_page
              }"
              class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium transition disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {{ page }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useModal } from '../composables/useModal';
import { useTheme } from '../composables/useTheme';

const { error } = useModal();
const { bgClass } = useTheme();

const loading = ref(false);
const logs = ref([]);
const availableActions = ref([]);
const availableEntityTypes = ref([]);
const searchTimeout = ref(null);

const filters = ref({
  action: '',
  entity_type: '',
  admin_id: '',
  date_from: '',
  date_to: '',
  search: ''
});

const pagination = ref({
  current_page: 1,
  last_page: 1,
  per_page: 50,
  total: 0,
  from: 0,
  to: 0
});

const visiblePages = computed(() => {
  const pages = [];
  const current = pagination.value.current_page;
  const last = pagination.value.last_page;

  // Always show first page
  if (current > 3) pages.push(1);
  if (current > 4) pages.push('...');

  // Show pages around current
  for (let i = Math.max(1, current - 2); i <= Math.min(last, current + 2); i++) {
    pages.push(i);
  }

  // Always show last page
  if (current < last - 3) pages.push('...');
  if (current < last - 2) pages.push(last);

  return pages;
});

const loadLogs = async (page = 1) => {
  loading.value = true;
  try {
    const params = new URLSearchParams({
      page: page.toString(),
      ...filters.value
    });

    const response = await fetch(`/admin/api/logs?${params}`);
    const data = await response.json();

    logs.value = data.data;
    pagination.value = {
      current_page: data.current_page,
      last_page: data.last_page,
      per_page: data.per_page,
      total: data.total,
      from: data.from || 0,
      to: data.to || 0
    };
  } catch (err) {
    console.error('Error loading logs:', err);
    await error('Ошибка при загрузке логов');
  } finally {
    loading.value = false;
  }
};

const loadFilterOptions = async () => {
  try {
    const [actionsResponse, entityTypesResponse] = await Promise.all([
      fetch('/admin/api/logs/actions'),
      fetch('/admin/api/logs/entity-types')
    ]);

    availableActions.value = await actionsResponse.json();
    availableEntityTypes.value = await entityTypesResponse.json();
  } catch (err) {
    console.error('Error loading filter options:', err);
  }
};

const debounceSearch = () => {
  clearTimeout(searchTimeout.value);
  searchTimeout.value = setTimeout(() => {
    loadLogs();
  }, 500);
};

const clearFilters = () => {
  filters.value = {
    action: '',
    entity_type: '',
    admin_id: '',
    date_from: '',
    date_to: '',
    search: ''
  };
  loadLogs();
};

const goToPage = (page) => {
  if (page !== '...' && page !== pagination.value.current_page) {
    loadLogs(page);
  }
};

const formatDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleString('ru-RU', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit'
  });
};

const getActionBadgeClass = (action) => {
  const actionLower = action.toLowerCase();

  if (actionLower.includes('create') || actionLower.includes('add') || actionLower === 'login') {
    return 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400';
  }

  if (actionLower.includes('delete') || actionLower.includes('remove') || actionLower === 'logout') {
    return 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400';
  }

  if (actionLower.includes('update') || actionLower.includes('edit') || actionLower.includes('modify')) {
    return 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400';
  }

  return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
};

onMounted(() => {
  loadLogs();
  loadFilterOptions();
});
</script>
