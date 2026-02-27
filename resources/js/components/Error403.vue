<template>
  <div class="flex items-center justify-center min-h-[calc(100vh-8rem)]">
    <div class="text-center px-6">
      <div class="mb-8">
        <svg class="w-32 h-32 mx-auto text-red-500 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
      </div>

      <h1 class="text-6xl font-bold text-gray-900 dark:text-white mb-4">403</h1>
      <h2 class="text-2xl font-semibold text-gray-700 dark:text-gray-300 mb-4">Доступ запрещен</h2>
      <p class="text-gray-600 dark:text-gray-400 mb-8 max-w-md mx-auto">
        У вас нет прав для доступа к этой странице. Если вы считаете, что это ошибка, обратитесь к администратору.
      </p>

      <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
        <button
          @click="$router.push('/')"
          :style="buttonStyle"
          class="px-6 py-3 text-white rounded-lg font-medium transition-opacity hover:opacity-90 inline-flex items-center"
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
import { useTheme } from '../composables/useTheme';

const { buttonStyle } = useTheme();
</script>
