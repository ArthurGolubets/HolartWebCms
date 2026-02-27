<template>
  <div class="relative" ref="searchContainer">
    <div class="relative">
      <input
        v-model="searchQuery"
        @input="handleSearch"
        @focus="showDropdown = true"
        type="text"
        placeholder="Поиск по всей системе..."
        class="w-full md:w-80 px-4 py-2 pl-10 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
      >
      <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
      </svg>
    </div>

    <!-- Dropdown Results -->
    <div
      v-if="showDropdown && (searchQuery.length >= 2 || results.length > 0)"
      class="absolute top-full left-0 right-0 mt-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg max-h-96 overflow-y-auto z-50"
    >
      <div v-if="loading" class="p-4 text-center text-gray-500 dark:text-gray-400">
        Поиск...
      </div>
      <div v-else-if="searchQuery.length >= 2 && results.length === 0" class="p-4 text-center text-gray-500 dark:text-gray-400">
        Ничего не найдено
      </div>
      <div v-else-if="results.length > 0">
        <div v-for="(group, entity) in groupedResults" :key="entity" class="border-b border-gray-200 dark:border-gray-700 last:border-0">
          <div class="px-4 py-2 bg-gray-50 dark:bg-gray-900 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">
            {{ getEntityName(entity) }}
          </div>
          <router-link
            v-for="item in group"
            :key="item.id"
            :to="item.url"
            @click="closeDropdown"
            class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition"
          >
            <div class="font-medium text-gray-900 dark:text-white">{{ item.name }}</div>
            <div v-if="item.subtitle" class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ item.subtitle }}</div>
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';

const searchQuery = ref('');
const showDropdown = ref(false);
const loading = ref(false);
const results = ref([]);
const searchContainer = ref(null);
let debounceTimeout = null;

const groupedResults = computed(() => {
  const grouped = {};
  results.value.forEach(item => {
    if (!grouped[item.entity]) {
      grouped[item.entity] = [];
    }
    grouped[item.entity].push(item);
  });
  return grouped;
});

const getEntityName = (entity) => {
  const names = {
    'administrators': 'Администраторы',
    'catalogs': 'Категории',
    'products': 'Товары',
    'settings': 'Настройки'
  };
  return names[entity] || entity;
};

const handleSearch = () => {
  if (debounceTimeout) clearTimeout(debounceTimeout);

  if (searchQuery.value.length < 2) {
    results.value = [];
    return;
  }

  debounceTimeout = setTimeout(async () => {
    loading.value = true;
    try {
      const response = await fetch(`/admin/api/search?q=${encodeURIComponent(searchQuery.value)}`);
      const data = await response.json();
      results.value = data;
    } catch (err) {
      console.error('Search error:', err);
      results.value = [];
    } finally {
      loading.value = false;
    }
  }, 300);
};

const closeDropdown = () => {
  showDropdown.value = false;
  searchQuery.value = '';
  results.value = [];
};

const handleClickOutside = (event) => {
  if (searchContainer.value && !searchContainer.value.contains(event.target)) {
    closeDropdown();
  }
};

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
  if (debounceTimeout) clearTimeout(debounceTimeout);
});
</script>
