<template>
  <div class="mb-2">
    <!-- Category Node -->
    <div
      class="flex items-center p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition group"
      :style="{ paddingLeft: `${level * 24 + 12}px` }"
    >
      <!-- Expand/Collapse Button -->
      <button
        v-if="hasChildren"
        @click="toggleExpand"
        class="mr-2 p-1 hover:bg-gray-200 dark:hover:bg-gray-600 rounded transition"
      >
        <svg
          class="w-4 h-4 text-gray-600 dark:text-gray-400 transition-transform"
          :class="{ 'rotate-90': isExpanded }"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
      </button>
      <div v-else class="w-6 mr-2"></div>

      <!-- Folder Icon -->
      <svg class="w-5 h-5 mr-3 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
        <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"/>
      </svg>

      <!-- Category Name -->
      <div class="flex-1">
        <span class="font-medium text-gray-900 dark:text-white">{{ catalog.name }}</span>
        <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">
          ({{ catalog.children_count || 0 }} подкатегорий, {{ catalog.products_count || catalog.products?.length || 0 }} товаров)
        </span>
      </div>

      <!-- Toggle Active Status -->
      <button
        @click="$emit('toggle-active', catalog)"
        :class="catalog.is_active ? 'text-green-600' : 'text-gray-400'"
        class="p-2 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-lg transition mr-2"
        :title="catalog.is_active ? 'Активна' : 'Неактивна'"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path v-if="catalog.is_active" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
      </button>

      <!-- Actions - Always visible on mobile (md:opacity-0) -->
      <div class="md:opacity-0 md:group-hover:opacity-100 transition-opacity flex items-center space-x-1">
        <button
          @click="$emit('create-subcategory', catalog.id)"
          class="p-2 text-green-600 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-lg transition"
          title="Создать подкатегорию"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
          </svg>
        </button>
        <button
          @click="$emit('create-product', catalog.id)"
          class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition"
          title="Создать товар"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
          </svg>
        </button>
        <button
          @click="$emit('view', catalog)"
          class="p-2 text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-lg transition"
          title="Просмотр"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
          </svg>
        </button>
        <button
          @click="$emit('edit', catalog)"
          class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition"
          title="Редактировать"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
          </svg>
        </button>
        <button
          @click="$emit('delete', catalog)"
          class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition"
          title="Удалить"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
          </svg>
        </button>
      </div>
    </div>

    <!-- Children (Categories and Products) -->
    <div v-if="isExpanded">
      <!-- Child Categories -->
      <CatalogTreeNode
        v-for="child in catalog.children"
        :key="'cat-' + child.id"
        :catalog="child"
        :level="level + 1"
        @create-subcategory="$emit('create-subcategory', $event)"
        @create-product="$emit('create-product', $event)"
        @view="$emit('view', $event)"
        @edit="$emit('edit', $event)"
        @delete="$emit('delete', $event)"
        @toggle-active="$emit('toggle-active', $event)"
        @view-product="$emit('view-product', $event)"
        @edit-product="$emit('edit-product', $event)"
        @delete-product="$emit('delete-product', $event)"
        @toggle-product-active="$emit('toggle-product-active', $event)"
        @refresh="$emit('refresh')"
      />

      <!-- Products -->
      <div
        v-for="product in catalog.products"
        :key="'prod-' + product.id"
        class="flex items-center p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition group"
        :style="{ paddingLeft: `${(level + 1) * 24 + 12}px` }"
      >
        <div class="w-6 mr-2"></div>

        <!-- Product Icon -->
        <svg class="w-5 h-5 mr-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
        </svg>

        <!-- Product Name and Info -->
        <div class="flex-1">
          <span class="font-medium text-gray-900 dark:text-white">{{ product.name }}</span>
          <div class="flex items-center space-x-3 mt-1">
            <span class="text-sm text-gray-600 dark:text-gray-400">{{ product.sku }}</span>
            <span class="text-sm font-semibold text-green-600 dark:text-green-400">{{ product.price }} ₽</span>
            <span v-if="product.is_new" class="px-2 py-0.5 text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 rounded">Новинка</span>
            <span v-if="product.is_hot" class="px-2 py-0.5 text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 rounded">Хит</span>
            <span v-if="product.is_recommended" class="px-2 py-0.5 text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 rounded">Рекомендуем</span>
          </div>
        </div>

        <!-- Toggle Product Active Status -->
        <button
          @click="$emit('toggle-product-active', product)"
          :class="product.is_active ? 'text-green-600' : 'text-gray-400'"
          class="p-2 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-lg transition mr-2"
          :title="product.is_active ? 'Активен' : 'Неактивен'"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path v-if="product.is_active" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </button>

        <!-- Product Actions - Always visible on mobile -->
        <div class="md:opacity-0 md:group-hover:opacity-100 transition-opacity flex items-center space-x-1">
          <button
            @click="$emit('view-product', product)"
            class="p-2 text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-lg transition"
            title="Просмотр"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
          </button>
          <button
            @click="$emit('edit-product', product)"
            class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition"
            title="Редактировать"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
          </button>
          <button
            @click="$emit('delete-product', product)"
            class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition"
            title="Удалить"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
  catalog: {
    type: Object,
    required: true
  },
  level: {
    type: Number,
    default: 0
  }
});

defineEmits([
  'create-subcategory',
  'create-product',
  'view',
  'edit',
  'delete',
  'view-product',
  'edit-product',
  'delete-product',
  'refresh'
]);

const isExpanded = ref(false);

const hasChildren = computed(() => {
  return (props.catalog.children_count > 0) || (props.catalog.products_count > 0) ||
         (props.catalog.children && props.catalog.children.length > 0) ||
         (props.catalog.products && props.catalog.products.length > 0);
});

const toggleExpand = async () => {
  if (!isExpanded.value && (!props.catalog.children || props.catalog.children.length === 0)) {
    // Load children on demand
    try {
      const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      const response = await fetch(`/admin/api/catalogs/${props.catalog.id}/children`, {
        headers: {
          'X-CSRF-TOKEN': token,
          'Accept': 'application/json'
        }
      });
      const children = await response.json();
      props.catalog.children = children;

      // Load products if not already loaded
      if (!props.catalog.products) {
        const catalogResponse = await fetch(`/admin/api/catalogs/${props.catalog.id}`, {
          headers: {
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
          }
        });
        const data = await catalogResponse.json();
        props.catalog.products = data.catalog.products || [];
      }
    } catch (err) {
      console.error('Error loading children:', err);
    }
  }

  isExpanded.value = !isExpanded.value;
};
</script>
