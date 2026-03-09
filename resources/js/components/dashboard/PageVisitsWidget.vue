<template>
  <div class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 overflow-hidden">
    <!-- Header -->
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-white/50 dark:bg-gray-800/50">
      <h3 class="text-sm font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">{{ title }}</h3>
    </div>

    <!-- Content -->
    <div class="p-6">
      <!-- Loading State -->
      <div v-if="loading" class="text-center py-8">
        <div class="inline-block animate-spin rounded-full h-10 w-10 border-4 border-blue-200 border-t-blue-600"></div>
      </div>

      <!-- Empty State -->
      <div v-else-if="items.length === 0" class="text-center py-8">
        <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
        </svg>
        <p class="text-sm text-gray-500 dark:text-gray-400">Нет данных</p>
      </div>

      <!-- Stats List -->
      <div v-else class="space-y-3">
        <div v-for="(item, index) in items" :key="item.id"
             class="p-4 bg-gradient-to-r from-gray-50 to-white dark:from-gray-700/30 dark:to-gray-800/30 rounded-lg border border-gray-100 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-600 transition-colors duration-200">

          <div class="flex items-center gap-3">
            <!-- Rank Number -->
            <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
              <span class="text-sm font-bold text-gray-600 dark:text-gray-400">{{ index + 1 }}</span>
            </div>

            <!-- Page Info -->
            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ item.title }}</p>
              <p class="text-xs text-gray-500 dark:text-gray-400 truncate">/{{ item.subtitle }}</p>
            </div>

            <!-- Visit Count -->
            <div class="flex-shrink-0 text-right">
              <p class="text-sm font-bold text-blue-600 dark:text-blue-400">{{ formatNumber(item.visits) }}</p>
              <p class="text-xs text-gray-500 dark:text-gray-400">просм.</p>
            </div>
          </div>
        </div>

        <!-- Summary Stats -->
        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700 grid grid-cols-2 gap-3">
          <div class="text-center p-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
            <p class="text-lg font-bold text-gray-900 dark:text-white">{{ formatNumber(totalVisits) }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">Всего за 30 дней</p>
          </div>
          <div class="text-center p-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
            <p class="text-lg font-bold text-gray-900 dark:text-white">{{ formatNumber(todayVisits) }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">Сегодня</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    required: true,
  },
  items: {
    type: Array,
    default: () => [],
  },
  loading: {
    type: Boolean,
    default: false,
  },
  totalVisits: {
    type: Number,
    default: 0,
  },
  todayVisits: {
    type: Number,
    default: 0,
  },
  width: {
    type: Number,
    default: 6,
  },
});

function formatNumber(num) {
  return new Intl.NumberFormat('ru-RU').format(num);
}
</script>
