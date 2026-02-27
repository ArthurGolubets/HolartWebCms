<template>
  <div class="relative" ref="dropdown">
    <button
      @click="isOpen = !isOpen"
      class="px-4 py-2 rounded-lg font-medium transition flex items-center"
      :style="{ backgroundColor: themeColor, borderColor: themeColor, color: '#fff' }"
    >
      <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
      </svg>
      Экспорт/Импорт
      <svg class="w-4 h-4 ml-2 transition-transform" :class="{ 'rotate-180': isOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
      </svg>
    </button>

    <div
      v-if="isOpen"
      class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg z-50"
    >
      <a
        :href="downloadTemplateUrl"
        class="block px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition flex items-center"
        @click="isOpen = false"
      >
        <svg class="w-5 h-5 mr-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        Скачать шаблон импорта
      </a>

      <label class="block px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition cursor-pointer flex items-center">
        <svg class="w-5 h-5 mr-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
        </svg>
        Загрузить файл импорта
        <input
          type="file"
          ref="fileInput"
          @change="handleFileUpload"
          accept=".xlsx,.xls,.csv"
          class="hidden"
        >
      </label>

      <a
        :href="exportUrl"
        class="block px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition border-t border-gray-200 dark:border-gray-700 flex items-center"
        @click="isOpen = false"
      >
        <svg class="w-5 h-5 mr-3 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
        </svg>
        Экспортировать {{ entityName }}
      </a>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useTheme } from '../composables/useTheme';

const props = defineProps({
  entityType: {
    type: String,
    required: true,
    validator: (value) => ['catalogs', 'products'].includes(value)
  },
  entityName: {
    type: String,
    required: true
  }
});

const emit = defineEmits(['file-selected']);

const { themeColor } = useTheme();
const isOpen = ref(false);
const dropdown = ref(null);
const fileInput = ref(null);

const downloadTemplateUrl = `/admin/api/${props.entityType}/import-template`;
const exportUrl = `/admin/api/${props.entityType}/export`;

const handleFileUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    emit('file-selected', file);
    isOpen.value = false;
    // Reset input
    event.target.value = '';
  }
};

const handleClickOutside = (event) => {
  if (dropdown.value && !dropdown.value.contains(event.target)) {
    isOpen.value = false;
  }
};

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});
</script>
