<template>
  <div :class="`bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-5 col-span-${width}`">
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-sm font-semibold text-gray-900 dark:text-white">{{ title }}</h3>
      <slot name="actions"></slot>
    </div>
    <slot></slot>
  </div>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    required: true,
  },
  width: {
    type: Number,
    default: 6,
    validator: (value) => value >= 1 && value <= 12,
  },
});
</script>
