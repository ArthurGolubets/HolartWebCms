<template>
  <div class="p-6">
    <div class="mb-6 flex items-center space-x-3">
      <button @click="$router.push('/pages')" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
      </button>
      <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
        {{ isEdit ? 'Редактировать страницу' : 'Создать страницу' }}
      </h2>
    </div>

    <form @submit.prevent="handleSubmit" class="space-y-6">
      <!-- Tabs -->
      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg">
        <div class="border-b border-gray-200 dark:border-gray-700">
          <nav class="flex -mb-px">
            <button
              type="button"
              @click="activeTab = 'main'"
              class="px-6 py-3 text-sm font-medium transition-colors"
              :class="activeTab === 'main'
                ? 'border-b-2 border-blue-500 text-blue-600 dark:text-blue-400'
                : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'"
            >
              Основное
            </button>
            <button
              type="button"
              @click="activeTab = 'content'"
              class="px-6 py-3 text-sm font-medium transition-colors"
              :class="activeTab === 'content'
                ? 'border-b-2 border-blue-500 text-blue-600 dark:text-blue-400'
                : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'"
            >
              Контент
            </button>
            <button
              type="button"
              @click="activeTab = 'seo'"
              class="px-6 py-3 text-sm font-medium transition-colors"
              :class="activeTab === 'seo'
                ? 'border-b-2 border-blue-500 text-blue-600 dark:text-blue-400'
                : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'"
            >
              SEO
            </button>
          </nav>
        </div>

        <!-- Tab Content -->
        <div class="p-6">
          <!-- Main Tab -->
          <div v-show="activeTab === 'main'" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Название страницы *</label>
              <input
                v-model="form.title"
                @input="onTitleChange"
                type="text"
                required
                placeholder="Например: О компании"
                class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white"
              >
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">URL (slug) *</label>
              <input
                v-model="form.slug"
                type="text"
                required
                placeholder="about-company"
                class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white font-mono"
              >
              <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Генерируется автоматически из названия</p>
            </div>


            <div>
              <label class="flex items-center space-x-2 cursor-pointer">
                <input
                  v-model="form.is_active"
                  type="checkbox"
                  class="w-4 h-4 rounded border-gray-300 dark:border-gray-600"
                >
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Активна</span>
              </label>
            </div>
          </div>

          <!-- Content Tab -->
          <div v-show="activeTab === 'content'" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Контент страницы</label>
              <QuillEditor
                v-model:content="form.content"
                content-type="html"
                theme="snow"
                :toolbar="toolbarOptions"
                style="min-height: 400px;"
              />
            </div>
          </div>

          <!-- SEO Tab -->
          <div v-show="activeTab === 'seo'" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Meta Title</label>
              <input
                v-model="form.meta_title"
                type="text"
                placeholder="SEO заголовок"
                maxlength="255"
                class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white"
              >
              <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ form.meta_title?.length || 0 }} / 255 символов</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Meta Description</label>
              <textarea
                v-model="form.meta_description"
                rows="3"
                placeholder="SEO описание"
                maxlength="500"
                class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white"
              ></textarea>
              <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ form.meta_description?.length || 0 }} / 500 символов</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Meta Keywords</label>
              <input
                v-model="form.meta_keywords"
                type="text"
                placeholder="ключевые, слова, через, запятую"
                class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white"
              >
            </div>
          </div>
        </div>
      </div>

      <!-- Actions -->
      <div class="flex justify-end space-x-3">
        <button
          type="button"
          @click="$router.push('/pages')"
          class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition"
        >
          Отмена
        </button>
        <button
          type="submit"
          :disabled="saving"
          class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:opacity-50"
        >
          {{ saving ? 'Сохранение...' : (isEdit ? 'Сохранить' : 'Создать') }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import { QuillEditor } from '@vueup/vue-quill';
import '@vueup/vue-quill/dist/vue-quill.snow.css';

const route = useRoute();
const router = useRouter();

const isEdit = computed(() => !!route.params.id);
const saving = ref(false);
const activeTab = ref('main');
const isSlugManuallyEdited = ref(false);

const form = ref({
  title: '',
  slug: '',
  type: 'dynamic',
  route_name: '',
  content: '',
  meta_title: '',
  meta_description: '',
  meta_keywords: '',
  is_active: true
});

const toolbarOptions = [
  [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
  ['bold', 'italic', 'underline', 'strike'],
  [{ 'list': 'ordered'}, { 'list': 'bullet' }],
  [{ 'indent': '-1'}, { 'indent': '+1' }],
  [{ 'color': [] }, { 'background': [] }],
  [{ 'align': [] }],
  ['link', 'image', 'video'],
  ['clean'],
  ['code-block']
];

const slugify = (text) => {
  const translitMap = {
    'а': 'a', 'б': 'b', 'в': 'v', 'г': 'g', 'д': 'd', 'е': 'e', 'ё': 'yo',
    'ж': 'zh', 'з': 'z', 'и': 'i', 'й': 'y', 'к': 'k', 'л': 'l', 'м': 'm',
    'н': 'n', 'о': 'o', 'п': 'p', 'р': 'r', 'с': 's', 'т': 't', 'у': 'u',
    'ф': 'f', 'х': 'h', 'ц': 'ts', 'ч': 'ch', 'ш': 'sh', 'щ': 'sch', 'ъ': '',
    'ы': 'y', 'ь': '', 'э': 'e', 'ю': 'yu', 'я': 'ya'
  };

  return text
    .toLowerCase()
    .split('')
    .map(char => translitMap[char] || char)
    .join('')
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-+|-+$/g, '');
};

const onTitleChange = () => {
  if (!isEdit.value && !isSlugManuallyEdited.value) {
    form.value.slug = slugify(form.value.title);
  }
};

onMounted(() => {
  if (isEdit.value) {
    loadPage();
  }
});

const loadPage = async () => {
  try {
    const response = await axios.get(`/admin/api/pages/${route.params.id}`);
    Object.assign(form.value, response.data);
    isSlugManuallyEdited.value = true;
  } catch (error) {
    console.error('Error loading page:', error);
    alert('Ошибка загрузки страницы');
  }
};

const handleSubmit = async () => {
  saving.value = true;

  try {
    if (isEdit.value) {
      await axios.put(`/admin/api/pages/${route.params.id}`, form.value);
    } else {
      await axios.post('/admin/api/pages', form.value);
    }

    router.push('/pages');
  } catch (error) {
    console.error('Error saving page:', error);
    alert(error.response?.data?.message || 'Ошибка сохранения страницы');
  } finally {
    saving.value = false;
  }
};
</script>
