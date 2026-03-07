<template>
  <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-5">
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-sm font-semibold text-gray-900 dark:text-white">{{ title }}</h3>
    </div>
    <div class="space-y-3">
      <div v-if="loading" class="text-center py-4">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
      </div>

      <div v-else-if="items.length === 0" class="text-center py-4 text-gray-500 dark:text-gray-400">
        Нет данных
      </div>

      <div v-else class="space-y-2">
        <slot name="item" v-for="(item, index) in items" :item="item" :index="index">
          <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
            <pre class="text-xs">{{ item }}</pre>
          </div>
        </slot>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    required: true,
  },
  items: {
    type: Array,
    default: () => [],
  },
  loading: {
    type: Boolean,
    default: false,
  },
  width: {
    type: Number,
    default: 6,
  },
});
</script>
