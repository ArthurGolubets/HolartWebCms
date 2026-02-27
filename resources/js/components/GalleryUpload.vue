<template>
  <div>
    <label v-if="label" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ label }}</label>

    <!-- Gallery Preview -->
    <div v-if="images.length > 0" class="grid grid-cols-4 gap-3 mb-3">
      <div v-for="(image, index) in images" :key="index" class="relative group">
        <img :src="image" alt="Gallery image" class="w-full h-24 object-cover rounded-lg border-2 border-gray-300 dark:border-gray-600">
        <button
          @click="removeImage(index)"
          type="button"
          class="absolute -top-2 -right-2 bg-red-600 text-white rounded-full p-1 hover:bg-red-700 transition opacity-0 group-hover:opacity-100"
        >
          <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>
    </div>

    <!-- Upload Button -->
    <div class="flex items-center justify-center w-full">
      <label class="flex flex-col items-center justify-center w-full h-24 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 transition">
        <div class="flex flex-col items-center justify-center">
          <svg class="w-6 h-6 mb-1 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
          </svg>
          <p class="text-xs text-gray-500 dark:text-gray-400"><span class="font-semibold">Добавить изображения</span></p>
        </div>
        <input
          ref="fileInput"
          type="file"
          class="hidden"
          accept="image/*"
          multiple
          @change="handleFileChange"
        >
      </label>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
  modelValue: {
    type: Array,
    default: () => []
  },
  label: String,
});

const emit = defineEmits(['update:modelValue']);

const fileInput = ref(null);
const images = ref([...(props.modelValue || [])]);

watch(() => props.modelValue, (newValue) => {
  images.value = [...(newValue || [])];
});

const handleFileChange = (event) => {
  const files = Array.from(event.target.files);

  files.forEach(file => {
    if (file.size > 2 * 1024 * 1024) {
      alert('Файл слишком большой. Максимальный размер: 2MB');
      return;
    }

    const reader = new FileReader();
    reader.onload = (e) => {
      images.value.push(e.target.result);
      emit('update:modelValue', images.value);
    };
    reader.readAsDataURL(file);
  });

  if (fileInput.value) {
    fileInput.value.value = '';
  }
};

const removeImage = (index) => {
  images.value.splice(index, 1);
  emit('update:modelValue', images.value);
};
</script>
