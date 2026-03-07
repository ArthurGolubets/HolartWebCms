<template>
  <div v-if="loading" class="text-center py-12">
    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
    <p class="text-gray-600 dark:text-gray-400 mt-4">Загрузка меню...</p>
  </div>

  <div v-else-if="menu">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
      <div class="flex items-center gap-4">
        <button @click="$router.push('/menus')" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
          </svg>
        </button>
        <div>
          <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ menu.name }}</h2>
          <p class="text-gray-600 dark:text-gray-400 mt-1">Информация о меню</p>
        </div>
      </div>
      <div class="flex gap-3">
        <ThemeButton variant="secondary" @click="$router.push(`/menus/${menu.id}/edit`)">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
          </svg>
          Редактировать
        </ThemeButton>
        <ThemeButton variant="primary" @click="$router.push(`/menus/${menu.id}/builder`)">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
          Настроить пункты меню
        </ThemeButton>
      </div>
    </div>

    <!-- Info Cards -->
    <div class="mb-6">
      <!-- Main Info -->
      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Основная информация</h3>
        <dl class="space-y-3">
          <div>
            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Название</dt>
            <dd class="text-base text-gray-900 dark:text-white mt-1">{{ menu.name }}</dd>
          </div>
          <div>
            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Символьный код</dt>
            <dd class="text-base font-mono text-gray-900 dark:text-white mt-1 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded inline-block">
              {{ menu.code }}
            </dd>
          </div>
          <div>
            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Расположение</dt>
            <dd class="mt-1">
              <span
                :class="[
                  'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium',
                  menu.location === 'header'
                    ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400'
                    : 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400'
                ]"
              >
                <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                  <path v-if="menu.location === 'header'" d="M3 3a1 1 0 000 2h11a1 1 0 100-2H3zM3 7a1 1 0 000 2h7a1 1 0 100-2H3zM3 11a1 1 0 100 2h4a1 1 0 100-2H3zM15 8a1 1 0 10-2 0v5.586l-1.293-1.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L15 13.586V8z"/>
                  <path v-else d="M3 3a1 1 0 000 2h11a1 1 0 100-2H3zM3 7a1 1 0 000 2h5a1 1 0 000-2H3zM3 11a1 1 0 100 2h4a1 1 0 100-2H3zM13 16a1 1 0 102 0v-5.586l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 101.414 1.414L13 10.414V16z"/>
                </svg>
                {{ menu.location === 'header' ? 'Шапка сайта' : 'Футер сайта' }}
              </span>
            </dd>
          </div>
          <div>
            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Статус</dt>
            <dd class="mt-1">
              <span
                :class="[
                  'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium',
                  menu.is_active
                    ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400'
                    : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400'
                ]"
              >
                <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                  <path v-if="menu.is_active" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                  <path v-else d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/>
                </svg>
                {{ menu.is_active ? 'Активно' : 'Неактивно' }}
              </span>
            </dd>
          </div>
          <div v-if="menu.description">
            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Описание</dt>
            <dd class="text-base text-gray-900 dark:text-white mt-1">{{ menu.description }}</dd>
          </div>
          <div>
            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Создано</dt>
            <dd class="text-base text-gray-900 dark:text-white mt-1">{{ formatDate(menu.created_at) }}</dd>
          </div>
          <div>
            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Обновлено</dt>
            <dd class="text-base text-gray-900 dark:text-white mt-1">{{ formatDate(menu.updated_at) }}</dd>
          </div>
        </dl>
      </div>
    </div>

    <!-- Menu Items Preview -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Структура меню</h3>
        <ThemeButton variant="primary" size="sm" @click="$router.push(`/menus/${menu.id}/builder`)">
          <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
          </svg>
          Добавить пункт
        </ThemeButton>
      </div>

      <div v-if="menuItems.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
        <p>Пункты меню отсутствуют</p>
        <p class="text-sm mt-2">Нажмите "Добавить пункт" чтобы начать</p>
      </div>

      <div v-else class="space-y-2">
        <MenuItemView v-for="item in menuItems" :key="item.id" :item="item" :level="0" />
      </div>
    </div>
  </div>

  <div v-else class="text-center py-12">
    <p class="text-red-600 dark:text-red-400">Меню не найдено</p>
    <ThemeButton variant="secondary" @click="$router.push('/menus')" class="mt-4">
      Вернуться к списку
    </ThemeButton>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import ThemeButton from './ThemeButton.vue';
import MenuItemView from './MenuItemView.vue';

const route = useRoute();
const menu = ref(null);
const menuItems = ref([]);
const loading = ref(true);

const loadMenu = async () => {
  loading.value = true;
  try {
    const response = await fetch(`/admin/api/menus/${route.params.id}`, {
      headers: { 'Accept': 'application/json' },
    });

    if (response.ok) {
      const data = await response.json();
      console.log('Menu view data:', data);
      menu.value = data.menu;
      menuItems.value = data.items || [];
      console.log('Menu items:', menuItems.value);
    }
  } catch (error) {
    console.error('Failed to load menu:', error);
  } finally {
    loading.value = false;
  }
};

const formatDate = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  return date.toLocaleString('ru-RU', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

onMounted(() => {
  loadMenu();
});
</script>
