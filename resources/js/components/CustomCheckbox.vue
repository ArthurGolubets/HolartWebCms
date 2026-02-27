<template>
  <label class="inline-flex items-center cursor-pointer">
    <input
      type="checkbox"
      :checked="modelValue"
      @change="$emit('update:modelValue', $event.target.checked)"
      class="w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600 cursor-pointer transition"
      :style="modelValue && themeColor ? { accentColor: themeColor } : {}"
    >
    <span v-if="label" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">{{ label }}</span>
  </label>
</template>

<script setup>
defineProps({
  modelValue: Boolean,
  label: String,
  themeColor: String,
});

defineEmits(['update:modelValue']);
</script>
