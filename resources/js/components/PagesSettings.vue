<template>
  <div>
    <div class="mb-6">
      <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Настройки страниц</h2>
      <p class="text-gray-600 dark:text-gray-400 mt-1">Конфигурация модуля страниц</p>
    </div>

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
      <div class="space-y-6">
        <!-- Module Info -->
        <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Информация о модуле</h3>
          <div class="space-y-2 text-sm">
            <div class="flex justify-between">
              <span class="text-gray-600 dark:text-gray-400">Название модуля:</span>
              <span class="font-medium text-gray-900 dark:text-white">Pages & SEO</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600 dark:text-gray-400">Версия:</span>
              <span class="font-medium text-gray-900 dark:text-white">1.0.0</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600 dark:text-gray-400">Статус:</span>
              <span class="px-2 py-1 text-xs bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 rounded">Установлен</span>
            </div>
          </div>
        </div>

        <!-- Statistics -->
        <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Статистика</h3>
          <div v-if="loadingStats" class="text-center text-gray-500 dark:text-gray-400 py-4">
            Загрузка...
          </div>
          <div v-else class="grid grid-cols-3 gap-4">
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
              <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.total_pages || 0 }}</div>
              <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Всего страниц</div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
              <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.active_pages || 0 }}</div>
              <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Активных страниц</div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
              <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.block_types || 0 }}</div>
              <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Типов блоков</div>
            </div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Быстрые действия</h3>
          <div class="space-y-3">
            <ThemeButton variant="secondary" @click="$router.push('/pages')" class="w-full justify-center">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
              Управление страницами
            </ThemeButton>

            <ThemeButton variant="secondary" @click="$router.push('/page-block-types')" class="w-full justify-center">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1H5a1 1 0 01-1-1v-3zM14 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1h-4a1 1 0 01-1-1v-3z"/>
              </svg>
              Управление типами блоков
            </ThemeButton>

            <ThemeButton variant="secondary" @click="$router.push('/pages/create')" class="w-full justify-center">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
              </svg>
              Создать новую страницу
            </ThemeButton>
          </div>
        </div>

        <!-- Documentation -->
        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Справка</h3>
          <div class="text-sm text-gray-600 dark:text-gray-400 space-y-2">
            <p><strong>Статические страницы:</strong> Простые страницы с HTML-контентом и SEO-метаданными.</p>
            <p><strong>Динамические страницы:</strong> Страницы, построенные с помощью конструктора блоков.</p>
            <p><strong>Типы блоков:</strong> Переиспользуемые компоненты для конструктора страниц (hero, текст, слайдер и т.д.).</p>
            <p class="mt-4">
              <strong>Примечание:</strong> Системные типы блоков защищены от изменений и удаления.
              Вы можете создавать собственные типы блоков для расширения функционала конструктора.
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import ThemeButton from './ThemeButton.vue';

const loadingStats = ref(false);
const stats = ref({
  total_pages: 0,
  active_pages: 0,
  block_types: 0
});

const loadStats = async () => {
  loadingStats.value = true;
  try {
    // Load pages count
    const pagesResponse = await fetch('/admin/api/pages', {
      headers: { 'Accept': 'application/json' }
    });
    if (pagesResponse.ok) {
      const pagesData = await pagesResponse.json();
      const pages = pagesData.data || pagesData;
      stats.value.total_pages = pages.length;
      stats.value.active_pages = pages.filter(p => p.is_active).length;
    }

    // Load block types count
    const blockTypesResponse = await fetch('/admin/api/page-block-types', {
      headers: { 'Accept': 'application/json' }
    });
    if (blockTypesResponse.ok) {
      const blockTypesData = await blockTypesResponse.json();
      const blockTypes = blockTypesData.data || blockTypesData;
      stats.value.block_types = blockTypes.length;
    }
  } catch (error) {
    console.error('Failed to load stats:', error);
  } finally {
    loadingStats.value = false;
  }
};

onMounted(() => {
  loadStats();
});
</script>
