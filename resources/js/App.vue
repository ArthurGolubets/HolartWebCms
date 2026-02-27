<template>
  <div class="flex min-h-screen bg-gray-50 dark:bg-gray-900" :class="{ 'dark': isDark }">
    <!-- Mobile Overlay -->
    <div
      v-if="isMobileMenuOpen"
      @click="isMobileMenuOpen = false"
      class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden"
    ></div>

    <!-- Sidebar -->
    <aside
      class="fixed lg:static inset-y-0 left-0 z-50 bg-gray-900 dark:bg-gray-950 text-gray-200 flex flex-col border-r border-gray-800 transition-all duration-300"
      :class="[
        isCollapsed ? 'w-20' : 'w-64',
        isMobileMenuOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
      ]"
    >
      <!-- Logo -->
      <div class="h-16 flex items-center justify-between px-6 border-b border-gray-800">
        <h2 v-if="!isCollapsed" class="text-lg font-semibold text-white transition-opacity">{{ panelName }}</h2>
        <h2 v-else class="text-lg font-semibold text-white">HC</h2>
        <button
          @click="toggleSidebar"
          class="hidden lg:block p-1.5 rounded hover:bg-gray-800 transition"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="isCollapsed ? 'M13 5l7 7-7 7M5 5l7 7-7 7' : 'M11 19l-7-7 7-7m8 14l-7-7 7-7'"/>
          </svg>
        </button>
      </div>

      <!-- Navigation -->
      <nav class="flex-1 py-4 px-3">
        <router-link
          to="/"
          v-slot="{ isActive }"
          custom
        >
          <a
            @click="$router.push('/'); isMobileMenuOpen = false"
            class="flex items-center px-3 py-2.5 mb-1 rounded-md transition-colors cursor-pointer"
            :class="[
              isActive ? 'text-white font-medium' : 'text-gray-300 hover:bg-gray-800 hover:text-white',
              isCollapsed ? 'justify-center' : ''
            ]"
            :style="isActive ? `background-color: ${themeColor}` : ''"
            :title="isCollapsed ? 'Главная' : ''"
          >
            <svg class="w-5 h-5" :class="isCollapsed ? '' : 'mr-3'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span v-if="!isCollapsed">Главная</span>
          </a>
        </router-link>

        <router-link
          to="/administrators"
          v-slot="{ isActive }"
          custom
        >
          <a
            @click="$router.push('/administrators'); isMobileMenuOpen = false"
            class="flex items-center px-3 py-2.5 mb-1 rounded-md transition-colors cursor-pointer"
            :class="[
              isActive ? 'text-white font-medium' : 'text-gray-300 hover:bg-gray-800 hover:text-white',
              isCollapsed ? 'justify-center' : ''
            ]"
            :style="isActive ? `background-color: ${themeColor}` : ''"
            :title="isCollapsed ? 'Администраторы' : ''"
          >
            <svg class="w-5 h-5" :class="isCollapsed ? '' : 'mr-3'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <span v-if="!isCollapsed">Администраторы</span>
          </a>
        </router-link>

        <router-link
          to="/settings"
          v-slot="{ isActive }"
          custom
        >
          <a
            @click="$router.push('/settings'); isMobileMenuOpen = false"
            class="flex items-center px-3 py-2.5 mb-1 rounded-md transition-colors cursor-pointer"
            :class="[
              isActive ? 'text-white font-medium' : 'text-gray-300 hover:bg-gray-800 hover:text-white',
              isCollapsed ? 'justify-center' : ''
            ]"
            :style="isActive ? `background-color: ${themeColor}` : ''"
            :title="isCollapsed ? 'Настройки' : ''"
          >
            <svg class="w-5 h-5" :class="isCollapsed ? '' : 'mr-3'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span v-if="!isCollapsed">Настройки</span>
          </a>
        </router-link>
      </nav>

      <!-- User Menu -->
      <div class="p-3 border-t border-gray-800">
        <div v-if="!isCollapsed" class="flex items-center p-3 rounded-md bg-gray-800 mb-2">
          <div class="w-9 h-9 rounded-full bg-gray-700 flex items-center justify-center font-semibold text-sm mr-3">
            {{ userInitials }}
          </div>
          <div class="flex-1 min-w-0">
            <p class="font-medium text-sm text-white truncate">{{ adminUser.name }}</p>
            <p class="text-xs text-gray-400 truncate">{{ roleLabel }}</p>
          </div>
        </div>
        <div v-else class="flex justify-center mb-2">
          <div class="w-9 h-9 rounded-full bg-gray-700 flex items-center justify-center font-semibold text-sm">
            {{ userInitials }}
          </div>
        </div>

        <button
          @click="logout"
          class="w-full flex items-center px-3 py-2 bg-gray-800 hover:bg-gray-700 rounded-md transition text-sm font-medium"
          :class="isCollapsed ? 'justify-center' : 'justify-center'"
          :title="isCollapsed ? 'Выйти' : ''"
        >
          <svg class="w-4 h-4" :class="isCollapsed ? '' : 'mr-2'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
          </svg>
          <span v-if="!isCollapsed">Выйти</span>
        </button>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col">
      <!-- Header -->
      <header class="h-16 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-4 lg:px-6 flex items-center justify-between">
        <!-- Mobile Menu Button -->
        <button
          @click="isMobileMenuOpen = !isMobileMenuOpen"
          class="lg:hidden p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition"
        >
          <svg class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
        </button>

        <h1 class="text-xl font-semibold text-gray-900 dark:text-white hidden lg:block">Dashboard</h1>
        <div class="lg:hidden"></div>

        <!-- Actions -->
        <div class="flex items-center space-x-3">
          <!-- Settings Button -->
          <router-link to="/settings">
            <button class="p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition" title="Настройки">
              <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
              </svg>
            </button>
          </router-link>

          <!-- Theme Toggle -->
          <button
            @click="toggleTheme"
            class="p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition"
            title="Сменить тему"
          >
            <svg v-if="!isDark" class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
            </svg>
            <svg v-else class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
          </button>

          <!-- User Menu -->
          <div class="relative">
            <button
              @click="showUserMenu = !showUserMenu"
              class="flex items-center space-x-2 p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition"
            >
              <div class="w-8 h-8 rounded-full bg-gray-700 flex items-center justify-center font-semibold text-sm text-white">
                {{ userInitials }}
              </div>
              <span class="hidden md:block text-sm font-medium text-gray-900 dark:text-white">{{ adminUser.name }}</span>
              <svg class="w-4 h-4 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </button>

            <!-- Dropdown -->
            <div
              v-if="showUserMenu"
              @click.away="showUserMenu = false"
              class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-2 z-50"
            >
              <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ adminUser.name }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ adminUser.email }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ roleLabel }}</p>
              </div>
              <button
                @click="editProfile"
                class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition"
              >
                Редактировать профиль
              </button>
              <button
                @click="logout"
                class="w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition"
              >
                Выйти
              </button>
            </div>
          </div>
        </div>
      </header>

      <!-- Content Area -->
      <div class="flex-1 p-6 overflow-auto">
        <router-view></router-view>
      </div>
    </main>

    <!-- Global Modal -->
    <Modal
      v-model="modalState.show"
      :type="modalState.type"
      :title="modalState.title"
      :message="modalState.message"
      :confirmText="modalState.confirmText"
      :cancelText="modalState.cancelText"
      :showCancel="modalState.showCancel"
      @confirm="modalState.onConfirm"
      @cancel="modalState.onCancel"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import Modal from './components/Modal.vue';
import { useModal } from './composables/useModal';

const { modalState, success, error } = useModal();

const isDark = ref(false);
const isCollapsed = ref(false);
const isMobileMenuOpen = ref(false);
const showUserMenu = ref(false);
const panelName = ref('HolartCMS');
const themeColor = ref('#ef4444'); // Default red

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

const toggleSidebar = () => {
  isCollapsed.value = !isCollapsed.value;
  localStorage.setItem('holart-cms-sidebar', isCollapsed.value ? 'collapsed' : 'expanded');
};

const toggleTheme = () => {
  isDark.value = !isDark.value;
  localStorage.setItem('holart-cms-theme', isDark.value ? 'dark' : 'light');

  if (isDark.value) {
    document.documentElement.classList.add('dark');
  } else {
    document.documentElement.classList.remove('dark');
  }
};

const editProfile = () => {
  showUserMenu.value = false;
  // TODO: Implement profile editing modal or page
  success('Функция редактирования профиля будет реализована позже');
};

const logout = async () => {
  showUserMenu.value = false;
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

const loadSettings = async () => {
  try {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const response = await fetch('/admin/api/settings', {
      headers: {
        'X-CSRF-TOKEN': token,
        'Accept': 'application/json'
      }
    });

    if (response.ok) {
      const settings = await response.json();
      panelName.value = settings.panel_name || 'HolartCMS';

      if (settings.theme_color) {
        const colorMap = {
          red: '#ef4444',
          blue: '#3b82f6',
          green: '#10b981',
          purple: '#a855f7',
          orange: '#f97316',
          pink: '#ec4899'
        };
        themeColor.value = colorMap[settings.theme_color] || '#ef4444';
      }
    }
  } catch (error) {
    console.error('Failed to load settings:', error);
  }
};

onMounted(() => {
  const savedTheme = localStorage.getItem('holart-cms-theme');
  isDark.value = savedTheme === 'dark';

  if (isDark.value) {
    document.documentElement.classList.add('dark');
  }

  const savedSidebar = localStorage.getItem('holart-cms-sidebar');
  isCollapsed.value = savedSidebar === 'collapsed';

  loadSettings();
});
</script>

<style scoped>
/* Nova-inspired dark sidebar */
</style>
