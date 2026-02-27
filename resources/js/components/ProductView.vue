<template>
  <div v-if="product">
    <div class="mb-6 flex items-center justify-between">
      <div class="flex-1">
        <div class="flex items-center space-x-3 mb-2">
          <button @click="$router.back()" class="text-gray-600 dark:text-gray-400">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
          </button>
          <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ product.name }}</h2>
          <span v-if="product.is_new" class="px-3 py-1 text-sm bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 rounded-full">Новинка</span>
          <span v-if="product.is_hot" class="px-3 py-1 text-sm bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 rounded-full">Хит</span>
          <span v-if="product.is_recommended" class="px-3 py-1 text-sm bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 rounded-full">Рекомендуем</span>
        </div>
      </div>
      <button @click="$router.push(`/products/${product.id}/edit`)" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium">Редактировать</button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <div class="lg:col-span-2 space-y-6">
        <!-- Main Image and Gallery -->
        <div v-if="product.image || (product.gallery && product.gallery.length > 0)" class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Изображения</h3>
          <div v-if="product.image" class="mb-4">
            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Главное изображение</h4>
            <img :src="getImageUrl(product.image)" :alt="product.name" class="w-full max-w-md rounded-lg border border-gray-200 dark:border-gray-700">
          </div>
          <div v-if="product.gallery && product.gallery.length > 0">
            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Галерея</h4>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
              <img v-for="(img, index) in product.gallery" :key="index" :src="getImageUrl(img)" :alt="`${product.name} ${index + 1}`" class="w-full h-32 object-cover rounded-lg border border-gray-200 dark:border-gray-700">
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
          <div class="grid grid-cols-2 gap-6">
            <div><h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Категория</h3><p class="text-gray-900 dark:text-white">{{ product.catalog?.name }}</p></div>
            <div><h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">SKU</h3><p class="text-gray-900 dark:text-white">{{ product.sku }}</p></div>
            <div><h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Цена</h3><div class="flex items-center space-x-2"><span class="text-2xl font-bold text-gray-900 dark:text-white">{{ product.price }} ₽</span><span v-if="product.old_price" class="text-lg text-gray-500 line-through">{{ product.old_price }} ₽</span></div></div>
            <div><h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Slug</h3><p class="text-gray-900 dark:text-white">{{ product.slug }}</p></div>
          </div>
        </div>

        <div v-if="product.variants && product.variants.length > 0" class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Варианты товара</h3>
          <div class="space-y-3">
            <div v-for="variant in product.variants" :key="variant.id" class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
              <div class="flex items-center justify-between">
                <div><h4 class="font-medium text-gray-900 dark:text-white">{{ variant.name }}</h4><p class="text-sm text-gray-600 dark:text-gray-400">{{ variant.sku }}</p></div>
                <div class="text-right"><span class="text-lg font-bold text-gray-900 dark:text-white">{{ variant.price }} ₽</span><span v-if="variant.old_price" class="block text-sm text-gray-500 line-through">{{ variant.old_price }} ₽</span></div>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Описание</h3>
          <div class="prose dark:prose-invert max-w-none" v-html="product.content || 'Нет описания'"></div>
        </div>
      </div>

      <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">SEO</h3>
          <div class="space-y-3">
            <div><h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Title</h4><p class="text-sm text-gray-900 dark:text-white">{{ product.title || '—' }}</p></div>
            <div><h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Description</h4><p class="text-sm text-gray-900 dark:text-white">{{ product.description || '—' }}</p></div>
            <div><h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Keywords</h4><p class="text-sm text-gray-900 dark:text-white">{{ product.keywords || '—' }}</p></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { useModal } from '../composables/useModal';

const { error } = useModal();
const route = useRoute();
const product = ref(null);

const getImageUrl = (imageString) => {
  if (!imageString) return '';

  // If it's a base64 string or full URL, return as is
  if (imageString.startsWith('data:') || imageString.startsWith('http')) {
    return imageString;
  }

  // If it's a storage path, prepend the storage URL
  if (imageString.startsWith('/storage/') || imageString.startsWith('storage/')) {
    return imageString.startsWith('/') ? imageString : '/' + imageString;
  }

  return imageString;
};

const loadProduct = async () => {
  try {
    const response = await fetch(`/admin/api/products/${route.params.id}`);
    product.value = await response.json();
  } catch (err) {
    console.error('Error loading product:', err);
    await error('Ошибка при загрузке товара');
  }
};

onMounted(() => {
  loadProduct();
});
</script>
