<template>
  <div class="flex items-center justify-center min-h-[calc(100vh-8rem)]">
    <div class="text-center px-6">
      <div class="mb-8">
        <svg class="w-32 h-32 mx-auto text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
      </div>

      <h1 class="text-6xl font-bold text-gray-900 dark:text-white mb-4">404</h1>
      <h2 class="text-2xl font-semibold text-gray-700 dark:text-gray-300 mb-4">Страница не найдена</h2>
      <p class="text-gray-600 dark:text-gray-400 mb-8 max-w-md mx-auto">
        К сожалению, запрашиваемая страница не существует или была удалена.
      </p>

      <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
        <button
          @click="$router.push('/')"
          class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition inline-flex items-center"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
          </svg>
          На главную
        </button>
        <button
          @click="$router.back()"
          class="px-6 py-3 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-white rounded-lg font-medium transition inline-flex items-center"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
          </svg>
          Назад
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
</script>
