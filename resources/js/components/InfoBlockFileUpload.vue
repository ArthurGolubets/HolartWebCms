<template>
  <div>
    <label v-if="label" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
      {{ label }}
      <span v-if="required" class="text-red-500">*</span>
    </label>

    <!-- Multiple Files List -->
    <div v-if="isMultiple && files.length > 0" class="mb-3 space-y-2">
      <div v-for="(file, index) in files" :key="index" class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-300 dark:border-gray-600">
        <div class="flex items-center space-x-3 flex-1 min-w-0">
          <svg class="w-8 h-8 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
          </svg>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ getFileName(file) }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">{{ getFileExtension(file) }}</p>
          </div>
        </div>
        <button
          @click="removeFile(index)"
          type="button"
          class="ml-3 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 flex-shrink-0"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>
    </div>

    <!-- Single File Preview -->
    <div v-if="!isMultiple && currentFile" class="mb-3">
      <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-300 dark:border-gray-600">
        <div class="flex items-center space-x-3 flex-1 min-w-0">
          <svg class="w-10 h-10 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ getFileName(currentFile) }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">{{ getFileExtension(currentFile) }}</p>
          </div>
        </div>
        <button
          @click="removeFile()"
          type="button"
          class="ml-3 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 flex-shrink-0"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>
    </div>

    <!-- Upload Button -->
    <div v-if="!currentFile || isMultiple" class="flex items-center justify-center w-full">
      <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 transition">
        <div class="flex flex-col items-center justify-center pt-5 pb-6">
          <svg class="w-8 h-8 mb-2 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
          </svg>
          <p class="mb-1 text-sm text-gray-500 dark:text-gray-400">
            <span class="font-semibold">{{ isMultiple ? 'Добавить файлы' : 'Нажмите для загрузки' }}</span>
          </p>
          <p class="text-xs text-gray-500 dark:text-gray-400">Любые файлы (MAX. 10MB)</p>
        </div>
        <input
          ref="fileInput"
          type="file"
          class="hidden"
          :multiple="isMultiple"
          @change="handleFileChange"
        >
      </label>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue';

const props = defineProps({
  modelValue: [String, Array],
  label: String,
  required: Boolean,
  isMultiple: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['update:modelValue']);

const fileInput = ref(null);
const files = ref([]);

const currentFile = computed(() => {
  if (props.isMultiple) return null;
  return props.modelValue || null;
});

// Initialize files array for multiple mode
watch(() => props.modelValue, (newVal) => {
  if (props.isMultiple) {
    files.value = Array.isArray(newVal) ? newVal : (newVal ? [newVal] : []);
  }
}, { immediate: true });

const getFileName = (path) => {
  if (!path) return '';
  const parts = path.split('/');
  return parts[parts.length - 1] || 'Файл';
};

const getFileExtension = (path) => {
  if (!path) return '';
  const parts = path.split('.');
  return parts.length > 1 ? `.${parts[parts.length - 1].toUpperCase()}` : '';
};

const handleFileChange = async (event) => {
  const uploadedFiles = Array.from(event.target.files);

  if (uploadedFiles.length === 0) return;

  // Validate file sizes
  for (const file of uploadedFiles) {
    if (file.size > 10 * 1024 * 1024) {
      alert(`Файл ${file.name} превышает максимальный размер 10MB`);
      continue;
    }

    // Convert to base64
    const reader = new FileReader();
    reader.onload = (e) => {
      const base64 = e.target.result;
      // Store as data URL with filename in comment
      const fileData = `${base64}#filename=${file.name}`;

      if (props.isMultiple) {
        files.value.push(fileData);
        emit('update:modelValue', [...files.value]);
      } else {
        emit('update:modelValue', fileData);
      }
    };
    reader.readAsDataURL(file);
  }

  // Reset input
  fileInput.value.value = '';
};

const removeFile = (index) => {
  if (props.isMultiple) {
    files.value.splice(index, 1);
    emit('update:modelValue', [...files.value]);
  } else {
    emit('update:modelValue', '');
  }
};
</script>
