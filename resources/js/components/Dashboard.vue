<template>
  <div>
    <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">Статистика</h2>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <!-- Stat Card 1 -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-md transition-all duration-300 hover:-translate-y-1 p-6 flex items-center">
        <div class="flex-shrink-0 w-14 h-14 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center mr-4">
          <svg class="w-7 h-7 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
          </svg>
        </div>
        <div>
          <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400">Пользователи</h3>
          <p class="text-3xl font-bold text-gray-900 dark:text-white">0</p>
        </div>
      </div>

      <!-- Stat Card 2 -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-md transition-all duration-300 hover:-translate-y-1 p-6 flex items-center">
        <div class="flex-shrink-0 w-14 h-14 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center mr-4">
          <svg class="w-7 h-7 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
        </div>
        <div>
          <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400">Записи</h3>
          <p class="text-3xl font-bold text-gray-900 dark:text-white">0</p>
        </div>
      </div>

      <!-- Stat Card 3 -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-md transition-all duration-300 hover:-translate-y-1 p-6 flex items-center">
        <div class="flex-shrink-0 w-14 h-14 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center mr-4">
          <svg class="w-7 h-7 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
          </svg>
        </div>
        <div>
          <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400">Комментарии</h3>
          <p class="text-3xl font-bold text-gray-900 dark:text-white">0</p>
        </div>
      </div>

      <!-- Stat Card 4 -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-md transition-all duration-300 hover:-translate-y-1 p-6 flex items-center">
        <div class="flex-shrink-0 w-14 h-14 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center mr-4">
          <svg class="w-7 h-7 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
          </svg>
        </div>
        <div>
          <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400">Просмотры</h3>
          <p class="text-3xl font-bold text-gray-900 dark:text-white">0</p>
        </div>
      </div>
    </div>

    <!-- Welcome Message -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-8">
      <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">
        Добро пожаловать в HolartCMS
      </h3>
      <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
        Это ваша панель администратора. Здесь вы можете управлять содержимым вашего сайта, просматривать статистику и выполнять другие административные задачи.
      </p>

      <!-- Quick Actions -->
      <div class="mt-6 flex flex-wrap gap-3">
        <button class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg font-medium transition">
          Создать запись
        </button>
        <button class="px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-white rounded-lg font-medium transition">
          Просмотреть отчёты
        </button>
        <button class="px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-white rounded-lg font-medium transition">
          Настройки
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
// Dashboard logic can be added here
</script>
