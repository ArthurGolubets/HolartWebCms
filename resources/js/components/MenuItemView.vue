<template>
  <div>
    <div :style="{ marginLeft: (level * 32) + 'px' }" class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg mb-2">
      <svg v-if="level > 0" class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
      </svg>
      <div class="flex-1">
        <div class="flex items-center gap-2">
          <span class="font-medium text-gray-900 dark:text-white">{{ item.title }}</span>
          <span v-if="!item.is_active" class="text-xs px-2 py-0.5 bg-gray-200 dark:bg-gray-600 text-gray-600 dark:text-gray-300 rounded">
            Неактивно
          </span>
        </div>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
          <span v-if="item.url">URL: {{ item.url }}</span>
          <span v-else-if="item.route">Route: {{ item.route }}</span>
          <span v-if="item.target === '_blank'" class="ml-2">(Новое окно)</span>
        </p>
      </div>
      <span class="text-sm text-gray-500 dark:text-gray-400">
        Сортировка: {{ item.sort }}
      </span>
    </div>

    <MenuItemView
      v-for="child in item.children"
      :key="child.id"
      :item="child"
      :level="level + 1"
    />
  </div>
</template>

<script setup>
defineProps({
  item: {
    type: Object,
    required: true
  },
  level: {
    type: Number,
    default: 0
  }
});
</script>
