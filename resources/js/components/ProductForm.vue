<template>
  <div>
    <div class="mb-6 flex items-center space-x-3">
      <button @click="$router.back()" class="text-gray-600 dark:text-gray-400">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
      </button>
      <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ isEdit ? 'Редактировать товар' : 'Создать товар' }}</h2>
    </div>

    <form @submit.prevent="handleSubmit" class="space-y-6">
      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Основная информация</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Категория *</label>
            <input
              v-model="categorySearch"
              @input="filterCategories"
              type="text"
              placeholder="Поиск категории..."
              class="w-full px-4 py-2 mb-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white"
            >
            <select v-model="form.catalog_id" required class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white">
              <option v-for="cat in filteredCategories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Название *</label>
            <input v-model="form.name" @input="generateSlug" type="text" required class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Артикул (SKU) *</label>
            <input v-model="form.sku" type="text" required class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Цена *</label>
            <input v-model.number="form.price" type="number" step="0.01" required class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Старая цена</label>
            <input v-model.number="form.old_price" type="number" step="0.01" class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white">
          </div>
          <div class="md:col-span-2">
            <ImageUpload v-model="form.main_image" label="Главное изображение" />
          </div>
          <div class="md:col-span-2">
            <GalleryUpload v-model="form.gallery" label="Галерея изображений" />
          </div>
          <div class="md:col-span-2 flex items-center space-x-6">
            <ToggleSwitch v-model="form.is_new" label="Новинка" />
            <ToggleSwitch v-model="form.is_hot" label="Хит" />
            <ToggleSwitch v-model="form.is_recommended" label="Рекомендуем" />
          </div>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">SEO</h3>
        <div class="space-y-4">
          <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Title</label><input v-model="form.title" type="text" class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white"></div>
          <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label><textarea v-model="form.description" rows="3" class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white"></textarea></div>
          <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Keywords</label><input v-model="form.keywords" type="text" class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white"></div>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Варианты товара</h3>
        <div v-for="(variant, index) in form.variants" :key="index" class="mb-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
          <div class="flex justify-between items-center mb-2">
            <span class="font-medium text-gray-900 dark:text-white">Вариант {{ index + 1 }}</span>
            <button type="button" @click="removeVariant(index)" class="text-red-600 hover:text-red-800"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <input v-model="variant.name" placeholder="Название" required class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white">
            <input v-model="variant.sku" placeholder="SKU" required class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white">
            <input v-model.number="variant.price" placeholder="Цена" type="number" step="0.01" required class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white">
            <input v-model.number="variant.old_price" placeholder="Старая цена" type="number" step="0.01" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white">
          </div>
        </div>
        <button type="button" @click="addVariant" :style="buttonStyle" class="px-4 py-2 text-white rounded-lg transition-opacity hover:opacity-90 text-sm">+ Добавить вариант</button>
      </div>

      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Контент</h3>
        <textarea v-model="form.content" rows="10" class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white font-mono text-sm"></textarea>
      </div>

      <div class="flex justify-end space-x-3">
        <button type="button" @click="$router.back()" class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium">Отмена</button>
        <button type="submit" :disabled="loading" :style="buttonStyle" class="px-6 py-3 text-white rounded-lg font-medium transition-opacity hover:opacity-90 disabled:opacity-50">{{ loading ? 'Сохранение...' : (isEdit ? 'Сохранить' : 'Создать') }}</button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useModal } from '../composables/useModal';
import { useTheme } from '../composables/useTheme';
import ImageUpload from './ImageUpload.vue';
import GalleryUpload from './GalleryUpload.vue';
import ToggleSwitch from './ToggleSwitch.vue';

const { success, error } = useModal();
const { buttonStyle } = useTheme();
const route = useRoute();
const router = useRouter();

const loading = ref(false);
const isEdit = computed(() => !!route.params.id);
const categories = ref([]);
const categorySearch = ref('');
const filteredCategories = ref([]);

const form = ref({
  catalog_id: null,
  name: '',
  slug: '',
  title: '',
  description: '',
  keywords: '',
  price: 0,
  old_price: null,
  sku: '',
  main_image: '',
  tags: [],
  is_new: false,
  is_hot: false,
  is_recommended: false,
  content: '',
  gallery: [],
  variants: [],
});

const generateSlug = () => {
  if (!isEdit.value) {
    const slug = form.value.name
      .toLowerCase()
      .replace(/[^\w\s-]/g, '')
      .replace(/\s+/g, '-')
      .replace(/-+/g, '-')
      .trim();
    form.value.slug = slug;
  }
};

const addVariant = () => {
  form.value.variants.push({ name: '', sku: '', price: 0, old_price: null, attributes: {} });
};

const removeVariant = (index) => {
  form.value.variants.splice(index, 1);
};

const loadCategories = async () => {
  try {
    const response = await fetch('/admin/api/catalogs/tree');
    const data = await response.json();
    const flatten = (items) => {
      let result = [];
      for (const item of items) {
        result.push(item);
        if (item.children?.length) result = result.concat(flatten(item.children));
      }
      return result;
    };
    categories.value = flatten(data);
    filteredCategories.value = categories.value;
  } catch (err) {
    console.error('Error loading categories:', err);
  }
};

const filterCategories = () => {
  if (!categorySearch.value) {
    filteredCategories.value = categories.value;
  } else {
    const search = categorySearch.value.toLowerCase();
    filteredCategories.value = categories.value.filter(cat =>
      cat.name.toLowerCase().includes(search)
    );
  }
};

const loadProduct = async () => {
  try {
    const response = await fetch(`/admin/api/products/${route.params.id}`);
    const data = await response.json();
    form.value = { ...data, variants: data.variants || [] };
  } catch (err) {
    console.error('Error loading product:', err);
    await error('Ошибка при загрузке товара');
  }
};

const handleSubmit = async () => {
  loading.value = true;
  try {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const url = isEdit.value ? `/admin/api/products/${route.params.id}` : '/admin/api/products';
    const method = isEdit.value ? 'PUT' : 'POST';

    const response = await fetch(url, {
      method,
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
      body: JSON.stringify(form.value),
    });

    if (!response.ok) {
      const data = await response.json();
      throw new Error(data.message || 'Failed to save product');
    }

    await success(isEdit.value ? 'Товар обновлен' : 'Товар создан');
    router.push('/catalog');
  } catch (err) {
    console.error('Error saving product:', err);
    await error(err.message || 'Ошибка при сохранении товара');
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  loadCategories();
  if (isEdit.value) loadProduct();
  if (route.query.catalog_id) form.value.catalog_id = parseInt(route.query.catalog_id);
});
</script>
