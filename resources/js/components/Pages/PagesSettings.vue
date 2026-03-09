<template>
  <div class="p-6">
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Настройки страниц</h1>
      <p class="text-gray-600 dark:text-gray-400 mt-1">Общие настройки модуля страниц и SEO</p>
    </div>

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
      <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Информация о модуле</h3>

      <div class="space-y-4">
        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
          <div>
            <p class="font-medium text-gray-900 dark:text-white">Всего страниц</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ stats.total || 0 }}</p>
          </div>
          <div>
            <p class="font-medium text-gray-900 dark:text-white">Активных</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ stats.active || 0 }}</p>
          </div>
          <div>
            <p class="font-medium text-gray-900 dark:text-white">Статических</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ stats.static || 0 }}</p>
          </div>
          <div>
            <p class="font-medium text-gray-900 dark:text-white">Динамических</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ stats.dynamic || 0 }}</p>
          </div>
        </div>

        <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
          <h4 class="font-medium text-gray-900 dark:text-white mb-2">Полезные команды</h4>
          <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
            <p><code class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded">php artisan holartcms:scan-routes</code> - Сканировать роуты приложения</p>
            <p><code class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded">php artisan holartcms:scan-routes --update</code> - Обновить существующие страницы</p>
          </div>
        </div>

        <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
          <h4 class="font-medium text-gray-900 dark:text-white mb-2">Информация</h4>
          <p class="text-sm text-gray-600 dark:text-gray-400">
            Модуль "Страницы и SEO" позволяет создавать статические и динамические страницы,
            управлять SEO-оптимизацией и отслеживать статистику посещений.
          </p>
          <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
            Статические страницы содержат HTML-контент напрямую, а динамические страницы
            привязаны к роутам Laravel и используются для автоматической генерации мета-данных.
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const stats = ref({
  total: 0,
  active: 0,
  static: 0,
  dynamic: 0
});

onMounted(() => {
  loadStats();
});

const loadStats = async () => {
  try {
    const response = await axios.get('/admin/api/pages', {
      params: { per_page: 1000 }
    });

    const pages = response.data.data || [];
    stats.value.total = pages.length;
    stats.value.active = pages.filter(p => p.is_active).length;
    stats.value.static = pages.filter(p => p.type === 'static').length;
    stats.value.dynamic = pages.filter(p => p.type === 'dynamic').length;
  } catch (error) {
    console.error('Error loading stats:', error);
  }
};
</script>
