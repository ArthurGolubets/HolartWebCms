<template>
  <div>
    <div class="mb-6 flex items-center justify-between">
      <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Глобальные фильтры</h2>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Управление фильтрами для товаров</p>
      </div>
      <ThemeButton variant="primary" @click="$router.push('/filters/create')">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Создать фильтр
      </ThemeButton>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Поиск</label>
          <input
            v-model="filters.search"
            @input="loadFilters"
            type="text"
            class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white"
            placeholder="Название или код..."
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Тип</label>
          <select v-model="filters.type" @change="loadFilters" class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white">
            <option value="">Все типы</option>
            <option value="select">Выбор</option>
            <option value="checkbox">Флажки</option>
            <option value="range">Диапазон</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Статус</label>
          <select v-model="filters.is_active" @change="loadFilters" class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white">
            <option value="">Все</option>
            <option value="1">Активные</option>
            <option value="0">Неактивные</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="text-center py-12">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
      <p class="text-gray-600 dark:text-gray-400 mt-4">Загрузка фильтров...</p>
    </div>

    <!-- Empty State -->
    <div v-else-if="filtersList.length === 0" class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-12 text-center">
      <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
      </svg>
      <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">Фильтры не найдены</h3>
      <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Начните с создания первого фильтра</p>
      <ThemeButton variant="primary" @click="$router.push('/filters/create')" class="mt-6">
        Создать первый фильтр
      </ThemeButton>
    </div>

    <!-- Filters List -->
    <div v-else class="grid grid-cols-1 gap-4">
      <div
        v-for="filter in filtersList"
        :key="filter.id"
        class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 hover:shadow-lg transition-shadow"
      >
        <div class="flex items-start justify-between">
          <div class="flex-1">
            <div class="flex items-center gap-3 mb-2">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ filter.name }}</h3>
              <span
                :class="[
                  'px-2 py-1 text-xs font-medium rounded-full',
                  filter.is_active
                    ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400'
                    : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400'
                ]"
              >
                {{ filter.is_active ? 'Активен' : 'Неактивен' }}
              </span>
              <span
                :class="[
                  'px-2 py-1 text-xs font-medium rounded-full',
                  getTypeColor(filter.type)
                ]"
              >
                {{ getTypeLabel(filter.type) }}
              </span>
              <span v-if="!filter.catalog_id" class="px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400">
                Глобальный
              </span>
              <span v-else class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                {{ filter.catalog?.name }}
              </span>
            </div>

            <code class="text-sm text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">
              {{ filter.code }}
            </code>

            <p v-if="filter.description" class="text-sm text-gray-600 dark:text-gray-400 mt-2">
              {{ filter.description }}
            </p>

            <!-- Filter Values -->
            <div v-if="filter.values && filter.values.length > 0" class="mt-4">
              <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Значения ({{ filter.values.length }}):
              </p>
              <div class="flex flex-wrap gap-2">
                <span
                  v-for="value in filter.values.slice(0, 10)"
                  :key="value.id"
                  class="px-3 py-1 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full"
                >
                  {{ value.value }}
                </span>
                <span v-if="filter.values.length > 10" class="px-3 py-1 text-sm text-gray-500 dark:text-gray-400">
                  +{{ filter.values.length - 10 }} еще
                </span>
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex gap-2 ml-4">
            <ThemeButton variant="secondary" size="sm" @click="$router.push(`/filters/${filter.id}`)">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
              </svg>
            </ThemeButton>

            <ThemeButton variant="primary" size="sm" @click="$router.push(`/filters/${filter.id}/edit`)">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
              </svg>
            </ThemeButton>

            <button
              @click="confirmDelete(filter)"
              class="px-3 py-2 text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <Modal v-if="deleteModal.show" @close="deleteModal.show = false">
      <template #header>
        <h5 class="text-xl font-semibold text-gray-900 dark:text-white">Подтверждение удаления</h5>
      </template>
      <template #body>
        <div class="text-center py-4">
          <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/30 mb-4">
            <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
          </div>
          <p class="text-gray-900 dark:text-white mb-2">
            Вы действительно хотите удалить фильтр <strong>{{ deleteModal.filter?.name }}</strong>?
          </p>
          <p class="text-sm text-red-600 dark:text-red-400">
            Все значения фильтра также будут удалены. Это действие нельзя отменить.
          </p>
        </div>
      </template>
      <template #footer>
        <div class="flex gap-3">
          <ThemeButton variant="secondary" @click="deleteModal.show = false" class="flex-1">
            Отмена
          </ThemeButton>
          <button
            @click="deleteFilter"
            class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium"
          >
            Удалить
          </button>
        </div>
      </template>
    </Modal>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import Modal from './Modal.vue';
import ThemeButton from './ThemeButton.vue';

const route = useRoute();
const filtersList = ref([]);
const loading = ref(false);
const deleteModal = ref({ show: false, filter: null });

const filters = ref({
  search: '',
  type: '',
  is_active: '',
  catalog_id: 'global', // только глобальные фильтры
});

const loadFilters = async () => {
  loading.value = true;
  try {
    // Build params only with non-empty values
    const params = new URLSearchParams();
    Object.keys(filters.value).forEach(key => {
      const value = filters.value[key];
      if (value !== '' && value !== null && value !== undefined) {
        params.append(key, value);
      }
    });

    const response = await fetch(`/admin/api/filters?${params}`, {
      headers: { 'Accept': 'application/json' },
    });

    if (response.ok) {
      const data = await response.json();
      filtersList.value = data;
    }
  } catch (error) {
    console.error('Failed to load filters:', error);
  } finally {
    loading.value = false;
  }
};

const getTypeLabel = (type) => {
  const labels = {
    'select': 'Выбор',
    'checkbox': 'Флажки',
    'range': 'Диапазон',
  };
  return labels[type] || type;
};

const getTypeColor = (type) => {
  const colors = {
    'select': 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
    'checkbox': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
    'range': 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400',
  };
  return colors[type] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400';
};

const confirmDelete = (filter) => {
  deleteModal.value = {
    show: true,
    filter,
  };
};

const deleteFilter = async () => {
  try {
    const response = await fetch(`/admin/api/filters/${deleteModal.value.filter.id}`, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
        'Accept': 'application/json',
      },
    });

    if (response.ok) {
      deleteModal.value.show = false;
      loadFilters();
    } else {
      const error = await response.json();
      alert(error.message || 'Ошибка при удалении фильтра');
    }
  } catch (error) {
    console.error('Failed to delete filter:', error);
    alert('Ошибка при удалении фильтра');
  }
};

// Watch for route changes to reload filters
watch(() => route.path, () => {
  if (route.path === '/filters') {
    loadFilters();
  }
});

onMounted(() => {
  loadFilters();
});
</script>
