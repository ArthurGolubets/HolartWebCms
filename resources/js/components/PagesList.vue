<template>
  <div>
    <div class="mb-6 flex items-center justify-between">
      <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Страницы</h2>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Управление страницами сайта</p>
      </div>
      <ThemeButton variant="primary" @click="$router.push('/pages/create')">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Создать страницу
      </ThemeButton>
    </div>

    <!-- Filters -->
    <div class="mb-4 flex gap-4">
      <input
        v-model="searchQuery"
        @input="loadPages"
        type="text"
        placeholder="Поиск по названию или slug..."
        class="flex-1 px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white"
      >
      <select
        v-model="typeFilter"
        @change="loadPages"
        class="px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white"
      >
        <option value="">Все типы</option>
        <option value="static">Статические</option>
        <option value="dynamic">Динамические</option>
      </select>
    </div>

    <!-- Pages List -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
      <div v-if="loading" class="p-8 text-center text-gray-500 dark:text-gray-400">Загрузка...</div>
      <div v-else-if="pages.length === 0" class="p-8 text-center text-gray-500 dark:text-gray-400">
        Страницы не созданы. Создайте первую страницу.
      </div>
      <table v-else class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-900">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Название</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Slug</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Тип</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Статус</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Действия</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
          <tr v-for="page in pages" :key="page.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
            <td class="px-6 py-4">
              <div class="font-medium text-gray-900 dark:text-white">{{ page.title }}</div>
            </td>
            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400 font-mono">{{ page.slug }}</td>
            <td class="px-6 py-4 text-sm">
              <span v-if="page.type === 'static'" class="px-2 py-1 text-xs bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 rounded">Статическая</span>
              <span v-else class="px-2 py-1 text-xs bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400 rounded">Динамическая</span>
            </td>
            <td class="px-6 py-4 text-sm">
              <span v-if="page.is_active" class="px-2 py-1 text-xs bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 rounded">Активна</span>
              <span v-else class="px-2 py-1 text-xs bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400 rounded">Неактивна</span>
            </td>
            <td class="px-6 py-4 text-right text-sm space-x-2">
              <button
                @click="previewPage(page)"
                class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300"
                title="Предпросмотр"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
              </button>
              <button
                @click="togglePublish(page)"
                :class="page.is_active ? 'text-orange-600 hover:text-orange-800 dark:text-orange-400 dark:hover:text-orange-300' : 'text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300'"
                :title="page.is_active ? 'Снять с публикации' : 'Опубликовать'"
              >
                <svg v-if="page.is_active" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
              </button>
              <button
                v-if="page.type === 'dynamic'"
                @click="$router.push(`/pages/${page.id}/builder`)"
                class="text-purple-600 hover:text-purple-800 dark:text-purple-400 dark:hover:text-purple-300"
                title="Конструктор"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1H5a1 1 0 01-1-1v-3zM14 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1h-4a1 1 0 01-1-1v-3z"/>
                </svg>
              </button>
              <button @click="$router.push(`/pages/${page.id}/edit`)" class="text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 dark:hover:text-yellow-300" title="Редактировать">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                </svg>
              </button>
              <button @click="duplicatePage(page)" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300" title="Дублировать">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
              </button>
              <button @click="confirmDeletePage(page)" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300" title="Удалить">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import ThemeButton from './ThemeButton.vue';
import { useModal } from '../composables/useModal';

const router = useRouter();
const { confirm } = useModal();

const pages = ref([]);
const loading = ref(false);
const searchQuery = ref('');
const typeFilter = ref('');

const loadPages = async () => {
  loading.value = true;
  try {
    const params = new URLSearchParams();
    if (searchQuery.value) params.append('search', searchQuery.value);
    if (typeFilter.value) params.append('type', typeFilter.value);

    const response = await fetch(`/admin/api/pages?${params}`, {
      headers: { 'Accept': 'application/json' }
    });

    if (response.ok) {
      const data = await response.json();
      pages.value = data.data || data;
    }
  } catch (error) {
    console.error('Failed to load pages:', error);
  } finally {
    loading.value = false;
  }
};

const duplicatePage = async (page) => {
  try {
    const response = await fetch(`/admin/api/pages/${page.id}/duplicate`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
        'Accept': 'application/json'
      }
    });

    if (response.ok) {
      loadPages();
    }
  } catch (error) {
    console.error('Failed to duplicate page:', error);
    alert('Ошибка при дублировании страницы');
  }
};

const confirmDeletePage = async (page) => {
  const result = await confirm(
    'Удаление страницы',
    `Вы уверены, что хотите удалить страницу "${page.title}"?`,
    'Удалить',
    'Отмена'
  );

  if (result) {
    deletePage(page);
  }
};

const deletePage = async (page) => {
  try {
    const response = await fetch(`/admin/api/pages/${page.id}`, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
        'Accept': 'application/json'
      }
    });

    if (response.ok) {
      loadPages();
    }
  } catch (error) {
    console.error('Failed to delete page:', error);
    alert('Ошибка при удалении страницы');
  }
};

const previewPage = (page) => {
  // Open page preview in new tab
  const url = `/admin/viewer/${page.slug}`;
  window.open(url, '_blank');
};

const togglePublish = async (page) => {
  try {
    const response = await fetch(`/admin/api/pages/${page.id}/toggle-publish`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
        'Accept': 'application/json'
      }
    });

    if (response.ok) {
      const data = await response.json();
      page.is_active = data.is_active;
    } else {
      alert('Ошибка при изменении статуса публикации');
    }
  } catch (error) {
    console.error('Failed to toggle publish:', error);
    alert('Ошибка при изменении статуса публикации');
  }
};

onMounted(() => {
  loadPages();
});
</script>
