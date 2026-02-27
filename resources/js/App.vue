<template>
  <div class="flex min-h-screen" :class="{ 'dark': isDark }">
    <!-- Sidebar -->
    <aside class="w-72 bg-gradient-to-b from-primary-500 to-secondary-600 text-white flex flex-col shadow-2xl">
      <!-- Logo -->
      <div class="p-6 border-b border-white/10">
        <h2 class="text-2xl font-bold">HolartCMS</h2>
        <p class="text-sm text-white/80 mt-1">Admin Panel</p>
      </div>

      <!-- Navigation -->
      <nav class="flex-1 py-6 px-3">
        <router-link
          to="/"
          class="flex items-center px-4 py-3 mb-1 rounded-lg text-white/90 hover:bg-white/15 hover:text-white transition-all duration-200"
          active-class="bg-white/20 text-white font-semibold"
        >
          <svg class="w-5 h-5 mr-3 stroke-2" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
          </svg>
          <span>Панель управления</span>
        </router-link>
      </nav>

      <!-- Footer -->
      <div class="p-4 border-t border-white/10 space-y-3">
        <!-- User Profile -->
        <div class="flex items-center p-3 bg-white/10 rounded-lg">
          <div class="w-11 h-11 rounded-full bg-white/30 flex items-center justify-center font-bold text-lg mr-3">
            {{ userInitials }}
          </div>
          <div class="flex-1 min-w-0">
            <p class="font-semibold text-sm truncate">{{ adminUser.name }}</p>
            <p class="text-xs text-white/80">{{ roleLabel }}</p>
          </div>
        </div>

        <!-- Logout Button -->
        <button
          @click="logout"
          class="w-full flex items-center justify-center px-4 py-2.5 bg-white/10 hover:bg-white/20 rounded-lg transition font-medium text-sm"
        >
          <svg class="w-5 h-5 mr-2 stroke-2" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
          </svg>
          <span>Выйти</span>
        </button>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col bg-gray-50 dark:bg-gray-900 transition-colors">
      <!-- Header -->
      <header class="bg-white dark:bg-gray-800 shadow-sm px-8 py-6 flex items-center justify-between transition-colors">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Добро пожаловать!</h1>

        <!-- Theme Toggle -->
        <button
          @click="toggleTheme"
          :title="isDark ? 'Светлая тема' : 'Тёмная тема'"
          class="w-11 h-11 rounded-lg border-2 border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition flex items-center justify-center"
        >
          <!-- Moon Icon (Light Mode) -->
          <svg v-if="!isDark" class="w-5 h-5 stroke-2 text-gray-700" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
          </svg>
          <!-- Sun Icon (Dark Mode) -->
          <svg v-else class="w-5 h-5 stroke-2 text-yellow-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
          </svg>
        </button>
      </header>

      <!-- Content Area -->
      <div class="flex-1 p-8">
        <router-view></router-view>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';

const isDark = ref(false);
const adminUser = ref({
  name: 'Администратор',
  email: 'admin@example.com',
  role: 'super_admin'
});

const roleLabel = computed(() => {
  const roles = {
    'super_admin': 'Супер администратор',
    'administrator': 'Администратор',
    'manager': 'Менеджер'
  };
  return roles[adminUser.value.role] || 'Пользователь';
});

const userInitials = computed(() => {
  const names = adminUser.value.name.split(' ');
  return names.map(n => n[0]).join('').toUpperCase().slice(0, 2);
});

const toggleTheme = () => {
  isDark.value = !isDark.value;
  localStorage.setItem('theme', isDark.value ? 'dark' : 'light');

  // Toggle dark class on HTML element for Tailwind
  if (isDark.value) {
    document.documentElement.classList.add('dark');
  } else {
    document.documentElement.classList.remove('dark');
  }
};

const logout = async () => {
  const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

  try {
    const response = await fetch('/admin/logout', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': token,
        'Accept': 'application/json'
      }
    });

    if (response.ok || response.redirected) {
      window.location.href = '/admin/login';
    }
  } catch (error) {
    console.error('Logout error:', error);
    window.location.href = '/admin/login';
  }
};

onMounted(() => {
  const savedTheme = localStorage.getItem('theme');
  isDark.value = savedTheme === 'dark';

  // Apply dark class on mount if needed
  if (isDark.value) {
    document.documentElement.classList.add('dark');
  }
});
</script>

<style scoped>
/* Tailwind handles all styling */
</style>
