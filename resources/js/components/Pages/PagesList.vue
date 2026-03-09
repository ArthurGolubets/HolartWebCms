<template>
  <div class="p-6">
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Страницы</h1>
      <p class="text-gray-600 dark:text-gray-400 mt-1">Управление страницами сайта</p>
    </div>

    <!-- Actions Bar -->
    <div class="mb-6 flex flex-col sm:flex-row gap-4">
      <div class="flex-1">
        <input
          v-model="search"
          type="text"
          placeholder="Поиск по названию или slug..."
          class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
          @input="debouncedSearch"
        />
      </div>

      <select
        v-model="filters.type"
        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
        @change="loadPages"
      >
        <option value="all">Все типы</option>
        <option value="static">Статические</option>
        <option value="dynamic">Динамические</option>
      </select>

      <select
        v-model="filters.is_active"
        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
        @change="loadPages"
      >
        <option value="">Все статусы</option>
        <option value="true">Активные</option>
        <option value="false">Неактивные</option>
      </select>

      <button
        @click="scanRoutes"
        class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition whitespace-nowrap"
        :disabled="scanning"
      >
        {{ scanning ? 'Сканирование...' : 'Сканировать маршруты' }}
      </button>

      <router-link
        to="/pages/create"
        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition whitespace-nowrap text-center"
      >
        Создать страницу
      </router-link>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-12">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
      <p class="mt-2 text-gray-600 dark:text-gray-400">Загрузка...</p>
    </div>

    <!-- Pages Table -->
    <div v-else-if="pages.length > 0" class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-900">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
              Название
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
              Slug
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
              Тип
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
              Всего
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
              Сегодня
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
              Статус
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
              Действия
            </th>
          </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
          <tr v-for="page in pages" :key="page.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
            <td class="px-6 py-4">
              <div class="text-sm font-medium text-gray-900 dark:text-white">{{ page.title }}</div>
            </td>
            <td class="px-6 py-4">
              <div class="text-sm text-gray-600 dark:text-gray-400">{{ page.slug }}</div>
            </td>
            <td class="px-6 py-4">
              <span
                class="px-2 py-1 text-xs rounded-full"
                :class="page.type === 'static' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' : 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300'"
              >
                {{ page.type === 'static' ? 'Статическая' : 'Динамическая' }}
              </span>
            </td>
            <td class="px-6 py-4">
              <div class="text-sm text-gray-600 dark:text-gray-400">{{ page.views_count || 0 }}</div>
            </td>
            <td class="px-6 py-4">
              <div class="text-sm text-green-600 dark:text-green-400 font-medium">{{ page.today_views || 0 }}</div>
            </td>
            <td class="px-6 py-4">
              <button
                @click="toggleStatus(page)"
                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors"
                :class="page.is_active ? 'bg-green-600' : 'bg-gray-300 dark:bg-gray-600'"
              >
                <span
                  class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                  :class="page.is_active ? 'translate-x-6' : 'translate-x-1'"
                ></span>
              </button>
            </td>
            <td class="px-6 py-4 text-right text-sm font-medium space-x-2">
              <router-link
                :to="`/pages/${page.id}/edit`"
                class="text-blue-600 hover:text-blue-900 dark:text-blue-400"
              >
                Редактировать
              </router-link>
              <button
                @click="confirmDelete(page)"
                class="text-red-600 hover:text-red-900 dark:text-red-400"
              >
                Удалить
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Pagination -->
      <div v-if="pagination.last_page > 1" class="px-6 py-4 flex items-center justify-between border-t border-gray-200 dark:border-gray-700">
        <div class="text-sm text-gray-700 dark:text-gray-400">
          Показано {{ pagination.from }} - {{ pagination.to }} из {{ pagination.total }}
        </div>
        <div class="flex gap-2">
          <button
            @click="changePage(pagination.current_page - 1)"
            :disabled="pagination.current_page === 1"
            class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-md disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50 dark:hover:bg-gray-700"
          >
            Предыдущая
          </button>
          <button
            @click="changePage(pagination.current_page + 1)"
            :disabled="pagination.current_page === pagination.last_page"
            class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-md disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50 dark:hover:bg-gray-700"
          >
            Следующая
          </button>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="text-center py-12 bg-white dark:bg-gray-800 rounded-lg shadow">
      <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
      </svg>
      <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Нет страниц</h3>
      <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Начните с создания новой страницы</p>
      <div class="mt-6">
        <router-link
          to="/pages/create"
          class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
        >
          Создать страницу
        </router-link>
      </div>
    </div>

    <!-- Scan Results Modal -->
    <Modal
      v-model="showScanModal"
      type="success"
      title="Результаты сканирования маршрутов"
      size="large"
      confirmText="Закрыть"
      @confirm="showScanModal = false"
    >
      <div class="space-y-6">
        <!-- Summary -->
        <div class="grid grid-cols-3 gap-4">
          <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ scanResults.created }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Создано</div>
          </div>
          <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ scanResults.updated }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Обновлено</div>
          </div>
          <div class="bg-gray-50 dark:bg-gray-900/20 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-gray-600 dark:text-gray-400">{{ scanResults.skipped }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Пропущено</div>
          </div>
        </div>

        <!-- Routes List -->
        <div v-if="scanResults.routes && scanResults.routes.length > 0" class="max-h-96 overflow-y-auto">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900 sticky top-0">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">URI</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Название маршрута</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Статус</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Примечание</th>
              </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
              <tr v-for="(route, index) in scanResults.routes" :key="index">
                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white font-mono">{{ route.uri }}</td>
                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ route.name || '-' }}</td>
                <td class="px-4 py-3 text-sm">
                  <span
                    class="px-2 py-1 text-xs rounded-full"
                    :class="{
                      'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300': route.status === 'created',
                      'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300': route.status === 'updated',
                      'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300': route.status === 'skipped'
                    }"
                  >
                    {{ route.status === 'created' ? 'Создан' : route.status === 'updated' ? 'Обновлен' : 'Пропущен' }}
                  </span>
                </td>
                <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ route.reason || '-' }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </Modal>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import Modal from '../Modal.vue';

const router = useRouter();
const pages = ref([]);
const loading = ref(false);
const scanning = ref(false);
const search = ref('');
const filters = ref({
  type: 'all',
  is_active: ''
});

const pagination = ref({
  current_page: 1,
  last_page: 1,
  from: 0,
  to: 0,
  total: 0
});

const showScanModal = ref(false);
const scanResults = ref({
  created: 0,
  updated: 0,
  skipped: 0,
  routes: []
});

let searchTimeout = null;

const debouncedSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    loadPages();
  }, 500);
};

const loadPages = async () => {
  loading.value = true;
  try {
    const params = new URLSearchParams({
      page: pagination.value.current_page,
      search: search.value,
      type: filters.value.type,
      is_active: filters.value.is_active
    });

    const response = await fetch(`/admin/api/pages?${params}`, {
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
      }
    });

    if (response.ok) {
      const data = await response.json();
      pages.value = data.data;
      pagination.value = {
        current_page: data.current_page,
        last_page: data.last_page,
        from: data.from,
        to: data.to,
        total: data.total
      };
    }
  } catch (error) {
    console.error('Error loading pages:', error);
  } finally {
    loading.value = false;
  }
};

const changePage = (page) => {
  pagination.value.current_page = page;
  loadPages();
};

const toggleStatus = async (page) => {
  try {
    const response = await fetch(`/admin/api/pages/${page.id}/toggle-status`, {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
      }
    });

    if (response.ok) {
      const data = await response.json();
      page.is_active = data.is_active;
    }
  } catch (error) {
    console.error('Error toggling status:', error);
  }
};

const confirmDelete = (page) => {
  if (confirm(`Вы уверены, что хотите удалить страницу "${page.title}"?`)) {
    deletePage(page.id);
  }
};

const deletePage = async (id) => {
  try {
    const response = await fetch(`/admin/api/pages/${id}`, {
      method: 'DELETE',
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
      }
    });

    if (response.ok) {
      await loadPages();
    }
  } catch (error) {
    console.error('Error deleting page:', error);
  }
};

const scanRoutes = async () => {
  scanning.value = true;
  try {
    const response = await fetch('/admin/api/pages/scan-routes', {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
      }
    });

    if (response.ok) {
      const result = await response.json();

      if (result.success && result.data) {
        scanResults.value = {
          created: result.data.created || 0,
          updated: result.data.updated || 0,
          skipped: result.data.skipped || 0,
          routes: result.data.routes || []
        };
        showScanModal.value = true;
        await loadPages();
      } else {
        alert(result.message || 'Ошибка при сканировании маршрутов');
      }
    } else {
      alert('Ошибка при сканировании маршрутов');
    }
  } catch (error) {
    console.error('Error scanning routes:', error);
    alert('Ошибка при сканировании маршрутов');
  } finally {
    scanning.value = false;
  }
};

onMounted(() => {
  loadPages();
});
</script>
