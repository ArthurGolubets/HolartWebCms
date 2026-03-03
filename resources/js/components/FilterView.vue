<template>
  <div>
    <div class="mb-6 flex items-center justify-between">
      <div class="flex items-center gap-4">
        <button @click="$router.push('/filters')" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
          </svg>
        </button>
        <div>
          <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
            {{ filter?.name }}
          </h2>
          <p class="text-gray-600 dark:text-gray-400 mt-1">
            Просмотр фильтра
          </p>
        </div>
      </div>
      <div class="flex gap-2">
        <ThemeButton variant="primary" @click="$router.push(`/filters/${filterId}/edit`)">
          <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
          </svg>
          Редактировать
        </ThemeButton>
        <ThemeButton variant="danger" @click="confirmDelete">
          <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
          </svg>
          Удалить
        </ThemeButton>
      </div>
    </div>

    <div v-if="loading" class="text-center py-12">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
      <p class="text-gray-600 dark:text-gray-400 mt-4">Загрузка...</p>
    </div>

    <div v-else-if="filter" class="space-y-6">
      <!-- Basic Info -->
      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Основная информация</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Название</label>
            <p class="text-gray-900 dark:text-white">{{ filter.name }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Символьный код</label>
            <p class="text-gray-900 dark:text-white font-mono">{{ filter.code }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Тип фильтра</label>
            <div class="flex items-center gap-2">
              <span class="px-2 py-1 text-xs font-medium rounded"
                :class="{
                  'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200': filter.type === 'select',
                  'bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200': filter.type === 'checkbox',
                  'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200': filter.type === 'range'
                }">
                {{ typeLabels[filter.type] }}
              </span>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Порядок сортировки</label>
            <p class="text-gray-900 dark:text-white">{{ filter.sort }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Категория</label>
            <div class="flex items-center gap-2">
              <span v-if="filter.catalog_id === null" class="px-2 py-1 text-xs font-medium rounded bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                Глобальный фильтр
              </span>
              <span v-else class="px-2 py-1 text-xs font-medium rounded bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200">
                {{ filter.catalog?.name || `ID: ${filter.catalog_id}` }}
              </span>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Статус</label>
            <div class="flex items-center gap-2">
              <span class="px-2 py-1 text-xs font-medium rounded"
                :class="filter.is_active
                  ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200'
                  : 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200'">
                {{ filter.is_active ? 'Активен' : 'Неактивен' }}
              </span>
            </div>
          </div>

          <div v-if="filter.description" class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Описание</label>
            <p class="text-gray-900 dark:text-white">{{ filter.description }}</p>
          </div>
        </div>
      </div>

      <!-- Values -->
      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
          Значения фильтра ({{ filter.values?.length || 0 }})
        </h3>

        <div v-if="!filter.values || filter.values.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
          <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
          </svg>
          <p>Нет значений</p>
        </div>

        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Значение
                </th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Код
                </th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Сортировка
                </th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Статус
                </th>
              </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
              <tr v-for="value in sortedValues" :key="value.id">
                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                  {{ value.value }}
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400 font-mono">
                  {{ value.code || '—' }}
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                  {{ value.sort }}
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-sm">
                  <span class="px-2 py-1 text-xs font-medium rounded"
                    :class="value.is_active
                      ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200'
                      : 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200'">
                    {{ value.is_active ? 'Активно' : 'Неактивно' }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Statistics -->
      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Статистика</h3>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
            <p class="text-sm text-blue-600 dark:text-blue-400 mb-1">Всего значений</p>
            <p class="text-2xl font-bold text-blue-900 dark:text-blue-100">{{ filter.values?.length || 0 }}</p>
          </div>

          <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
            <p class="text-sm text-green-600 dark:text-green-400 mb-1">Активных значений</p>
            <p class="text-2xl font-bold text-green-900 dark:text-green-100">{{ activeValuesCount }}</p>
          </div>

          <div class="p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
            <p class="text-sm text-purple-600 dark:text-purple-400 mb-1">Создан</p>
            <p class="text-2xl font-bold text-purple-900 dark:text-purple-100">{{ formatDate(filter.created_at) }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <Modal v-if="showDeleteModal" @close="showDeleteModal = false">
      <template #header>
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Подтверждение удаления</h3>
      </template>
      <template #body>
        <p class="text-gray-700 dark:text-gray-300">
          Вы действительно хотите удалить фильтр <strong>{{ filter?.name }}</strong>?
        </p>
        <p class="text-sm text-red-600 dark:text-red-400 mt-2">
          Все значения фильтра и связи с товарами также будут удалены. Это действие необратимо.
        </p>
      </template>
      <template #footer>
        <ThemeButton variant="secondary" @click="showDeleteModal = false">
          Отмена
        </ThemeButton>
        <ThemeButton variant="danger" @click="deleteFilter" :disabled="deleting">
          <span v-if="deleting">Удаление...</span>
          <span v-else>Удалить</span>
        </ThemeButton>
      </template>
    </Modal>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import ThemeButton from './ThemeButton.vue';
import Modal from './Modal.vue';

const route = useRoute();
const router = useRouter();

const filterId = route.params.id;
const filter = ref(null);
const loading = ref(false);
const showDeleteModal = ref(false);
const deleting = ref(false);

const typeLabels = {
  select: 'Выбор (dropdown)',
  checkbox: 'Флажки (multiple)',
  range: 'Диапазон (min-max)'
};

const sortedValues = computed(() => {
  if (!filter.value?.values) return [];
  return [...filter.value.values].sort((a, b) => a.sort - b.sort);
});

const activeValuesCount = computed(() => {
  if (!filter.value?.values) return 0;
  return filter.value.values.filter(v => v.is_active).length;
});

const loadFilter = async () => {
  loading.value = true;
  try {
    const response = await fetch(`/admin/api/filters/${filterId}`, {
      headers: { 'Accept': 'application/json' },
    });

    if (response.ok) {
      filter.value = await response.json();
    } else {
      alert('Фильтр не найден');
      router.push('/filters');
    }
  } catch (error) {
    console.error('Failed to load filter:', error);
    alert('Ошибка при загрузке фильтра');
  } finally {
    loading.value = false;
  }
};

const confirmDelete = () => {
  showDeleteModal.value = true;
};

const deleteFilter = async () => {
  deleting.value = true;
  try {
    const response = await fetch(`/admin/api/filters/${filterId}`, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
        'Accept': 'application/json',
      },
    });

    if (response.ok) {
      router.push('/filters');
    } else {
      const error = await response.json();
      alert(error.message || 'Ошибка при удалении фильтра');
    }
  } catch (error) {
    console.error('Failed to delete filter:', error);
    alert('Ошибка при удалении фильтра');
  } finally {
    deleting.value = false;
    showDeleteModal.value = false;
  }
};

const formatDate = (dateString) => {
  if (!dateString) return '—';
  const date = new Date(dateString);
  return date.toLocaleDateString('ru-RU', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  });
};

onMounted(() => {
  loadFilter();
});
</script>
