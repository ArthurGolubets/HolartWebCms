<template>
  <div>
    <div class="mb-6 flex items-center space-x-3">
      <button @click="$router.back()" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
      </button>
      <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
        {{ isEdit ? 'Редактировать категорию' : 'Создать категорию' }}
      </h2>
    </div>

    <form @submit.prevent="handleSubmit" class="space-y-6">
      <!-- Main Info -->
      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Основная информация</h3>

        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Родительская категория</label>
            <input
              v-model="categorySearch"
              @input="filterCategories"
              type="text"
              placeholder="Поиск категории..."
              class="w-full px-4 py-2 mb-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white"
            >
            <select
              v-model="form.parent_id"
              class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white"
            >
              <option :value="null">Корневая категория</option>
              <option v-for="cat in filteredCategories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Название *</label>
            <input
              v-model="form.name"
              @input="generateSlug"
              type="text"
              required
              class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white"
            >
          </div>

          <div>
            <ImageUpload v-model="form.image" label="Изображение категории" />
          </div>
        </div>
      </div>

      <!-- SEO -->
      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">SEO</h3>

        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Title</label>
            <input
              v-model="form.title"
              type="text"
              class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white"
            >
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
            <textarea
              v-model="form.description"
              rows="3"
              class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white"
            ></textarea>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Keywords</label>
            <input
              v-model="form.keywords"
              type="text"
              placeholder="Через запятую"
              class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white"
            >
          </div>
        </div>
      </div>

      <!-- Content -->
      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Контент</h3>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Описание категории</label>
          <textarea
            v-model="form.content"
            rows="10"
            class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white font-mono text-sm"
          ></textarea>
        </div>
      </div>

      <!-- Actions -->
      <div class="flex justify-end space-x-3">
        <button
          type="button"
          @click="$router.back()"
          class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition"
        >
          Отмена
        </button>
        <button
          type="submit"
          :disabled="loading"
          class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition disabled:opacity-50"
        >
          {{ loading ? 'Сохранение...' : (isEdit ? 'Сохранить' : 'Создать') }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useModal } from '../composables/useModal';
import ImageUpload from './ImageUpload.vue';

const { success, error } = useModal();
const route = useRoute();
const router = useRouter();

const loading = ref(false);
const isEdit = computed(() => !!route.params.id);
const availableCategories = ref([]);
const categorySearch = ref('');
const filteredCategories = ref([]);

const form = ref({
  parent_id: null,
  name: '',
  slug: '',
  title: '',
  description: '',
  keywords: '',
  image: '',
  content: '',
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

const loadCategories = async () => {
  try {
    const response = await fetch('/admin/api/catalogs/list');
    const data = await response.json();
    availableCategories.value = data;
    filteredCategories.value = data;
  } catch (err) {
    console.error('Error loading categories:', err);
  }
};

const filterCategories = () => {
  if (!categorySearch.value) {
    filteredCategories.value = availableCategories.value;
  } else {
    const search = categorySearch.value.toLowerCase();
    filteredCategories.value = availableCategories.value.filter(cat =>
      cat.name.toLowerCase().includes(search)
    );
  }
};

const loadCatalog = async () => {
  try {
    const response = await fetch(`/admin/api/catalogs/${route.params.id}`);
    const data = await response.json();
    form.value = {
      parent_id: data.catalog.parent_id,
      name: data.catalog.name,
      slug: data.catalog.slug || '',
      title: data.catalog.title || '',
      description: data.catalog.description || '',
      keywords: data.catalog.keywords || '',
      image: data.catalog.image || '',
      content: data.catalog.content || '',
    };
  } catch (err) {
    console.error('Error loading catalog:', err);
    await error('Ошибка при загрузке категории');
  }
};

const handleSubmit = async () => {
  loading.value = true;
  try {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const url = isEdit.value ? `/admin/api/catalogs/${route.params.id}` : '/admin/api/catalogs';
    const method = isEdit.value ? 'PUT' : 'POST';

    const response = await fetch(url, {
      method,
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': token,
      },
      body: JSON.stringify(form.value),
    });

    if (!response.ok) {
      const data = await response.json();
      throw new Error(data.message || 'Failed to save catalog');
    }

    await success(isEdit.value ? 'Категория обновлена' : 'Категория создана');
    router.push('/catalog');
  } catch (err) {
    console.error('Error saving catalog:', err);
    await error(err.message || 'Ошибка при сохранении категории');
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  loadCategories();

  if (isEdit.value) {
    loadCatalog();
  }

  // Set parent_id from query parameter
  if (route.query.parent_id) {
    form.value.parent_id = parseInt(route.query.parent_id);
  }
});
</script>
