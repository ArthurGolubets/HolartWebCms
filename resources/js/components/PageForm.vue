<template>
  <div>
    <div class="mb-6 flex items-center justify-between">
      <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ isEdit ? 'Редактирование страницы' : 'Создание страницы' }}</h2>
        <p class="text-gray-600 dark:text-gray-400 mt-1">{{ isEdit ? 'Обновите данные страницы' : 'Заполните информацию о странице' }}</p>
      </div>
      <ThemeButton variant="secondary" @click="$router.push('/pages')">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Назад к списку
      </ThemeButton>
    </div>

    <form @submit.prevent="savePage" class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
      <!-- Tabs -->
      <div class="border-b border-gray-200 dark:border-gray-700">
        <div class="flex">
          <button
            type="button"
            @click="activeTab = 'basic'"
            :class="[
              'px-6 py-3 text-sm font-medium border-b-2 transition-colors',
              activeTab === 'basic'
                ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'
            ]"
          >
            Основная информация
          </button>
          <button
            type="button"
            @click="activeTab = 'seo'"
            :class="[
              'px-6 py-3 text-sm font-medium border-b-2 transition-colors',
              activeTab === 'seo'
                ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'
            ]"
          >
            SEO
          </button>
        </div>
      </div>

      <div class="p-6">
        <!-- Basic Information Tab -->
        <div v-if="activeTab === 'basic'" class="space-y-6">
          <!-- Title -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Название <span class="text-red-500">*</span>
              <span v-if="isEdit" class="ml-2 px-2 py-1 text-xs font-medium rounded-full" :class="form.type === 'static' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300' : 'bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300'">
                {{ form.type === 'static' ? 'Статическая' : 'Динамическая' }}
              </span>
            </label>
            <input
              v-model="form.title"
              @input="onTitleChange"
              type="text"
              required
              class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white"
              placeholder="Введите название страницы"
            >
          </div>

          <!-- Slug -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Символьный код (slug) <span class="text-red-500">*</span>
            </label>
            <div class="flex gap-2">
              <input
                v-model="form.slug"
                type="text"
                required
                pattern="[a-z0-9_]+"
                class="flex-1 px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white font-mono"
                placeholder="nazvanie_stranicy"
              >
              <ThemeButton type="button" variant="secondary" @click="generateSlug">
                Сгенерировать
              </ThemeButton>
            </div>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Только латинские буквы, цифры и подчеркивание</p>
          </div>

          <!-- Type (only for new pages) -->
          <div v-if="!isEdit">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Тип страницы <span class="text-red-500">*</span>
            </label>
            <div class="flex gap-4">
              <label class="flex items-center cursor-pointer">
                <input
                  v-model="form.type"
                  type="radio"
                  value="static"
                  class="mr-2"
                >
                <span class="text-gray-900 dark:text-white">Статическая</span>
              </label>
              <label class="flex items-center cursor-pointer">
                <input
                  v-model="form.type"
                  type="radio"
                  value="dynamic"
                  class="mr-2"
                >
                <span class="text-gray-900 dark:text-white">Динамическая</span>
              </label>
            </div>
          </div>

          <!-- Content (only for static pages) -->
          <div v-if="form.type === 'static'">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Содержимое
            </label>
            <div id="editor-container" class="quill-editor" style="min-height: 400px;"></div>
          </div>

          <!-- Active Status -->
          <div class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
            <label for="is_active" class="text-sm font-medium text-gray-700 dark:text-gray-300 cursor-pointer">
              Активна
            </label>
            <button
              @click="form.is_active = !form.is_active"
              type="button"
              :class="form.is_active ? 'bg-blue-600' : 'bg-gray-300 dark:bg-gray-600'"
              class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors"
            >
              <span
                :class="form.is_active ? 'translate-x-6' : 'translate-x-1'"
                class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
              />
            </button>
          </div>

          <!-- Sort -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Сортировка
            </label>
            <input
              v-model.number="form.sort"
              type="number"
              class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white"
            >
          </div>
        </div>

        <!-- SEO Tab -->
        <div v-if="activeTab === 'seo'" class="space-y-6">
          <!-- Meta Title -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Meta заголовок
            </label>
            <input
              v-model="form.meta_title"
              type="text"
              class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white"
              placeholder="Заголовок для поисковиков"
            >
          </div>

          <!-- Meta Description -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Meta описание
            </label>
            <textarea
              v-model="form.meta_description"
              rows="3"
              class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white"
              placeholder="Описание для поисковиков"
            ></textarea>
          </div>

          <!-- Meta Keywords -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Meta ключевые слова
            </label>
            <input
              v-model="form.meta_keywords"
              type="text"
              class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white"
              placeholder="ключевое слово, другое слово"
            >
          </div>
        </div>
      </div>

      <!-- Error Message -->
      <div v-if="errorMessage" class="mx-6 mb-4 p-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg">
        <p class="text-red-800 dark:text-red-400">{{ errorMessage }}</p>
      </div>

      <!-- Actions -->
      <div class="border-t border-gray-200 dark:border-gray-700 p-6 flex gap-3">
        <ThemeButton type="submit" variant="primary" :disabled="saving">
          {{ saving ? 'Сохранение...' : 'Сохранить' }}
        </ThemeButton>

        <ThemeButton
          v-if="isEdit && form.type === 'dynamic'"
          type="button"
          variant="secondary"
          @click="$router.push(`/pages/${pageId}/builder`)"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1H5a1 1 0 01-1-1v-3zM14 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1h-4a1 1 0 01-1-1v-3z"/>
          </svg>
          Конструктор страницы
        </ThemeButton>

        <ThemeButton type="button" variant="secondary" @click="$router.push('/pages')">
          Отмена
        </ThemeButton>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch, nextTick } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import ThemeButton from './ThemeButton.vue';
import Quill from 'quill';
import 'quill/dist/quill.snow.css';

const router = useRouter();
const route = useRoute();

const pageId = computed(() => route.params.id);
const isEdit = computed(() => !!pageId.value);

const activeTab = ref('basic');
let quillEditor = null;

const form = ref({
  title: '',
  slug: '',
  type: 'static',
  content: '',
  meta_title: '',
  meta_description: '',
  meta_keywords: '',
  is_active: true,
  sort: 500,
});

const saving = ref(false);
const errorMessage = ref('');
const slugManuallyChanged = ref(false);

// Initialize Quill editor when type is static
watch(() => form.value.type, async (newType) => {
  if (newType === 'static') {
    await nextTick();
    initQuillEditor();
  }
}, { immediate: false });

const initQuillEditor = () => {
  if (quillEditor || form.value.type !== 'static') return;

  const container = document.getElementById('editor-container');
  if (!container) return;

  quillEditor = new Quill('#editor-container', {
    theme: 'snow',
    placeholder: 'Введите содержимое страницы...',
    modules: {
      toolbar: [
        [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
        ['bold', 'italic', 'underline', 'strike'],
        [{ 'color': [] }, { 'background': [] }],
        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
        [{ 'align': [] }],
        ['link', 'image', 'video'],
        ['clean']
      ]
    }
  });

  // Set initial content
  if (form.value.content) {
    quillEditor.root.innerHTML = form.value.content;
  }

  // Update form on content change
  quillEditor.on('text-change', () => {
    form.value.content = quillEditor.root.innerHTML;
  });
};

// Transliteration map for Russian to Latin
const transliterationMap = {
  'а': 'a', 'б': 'b', 'в': 'v', 'г': 'g', 'д': 'd', 'е': 'e', 'ё': 'yo', 'ж': 'zh',
  'з': 'z', 'и': 'i', 'й': 'y', 'к': 'k', 'л': 'l', 'м': 'm', 'н': 'n', 'о': 'o',
  'п': 'p', 'р': 'r', 'с': 's', 'т': 't', 'у': 'u', 'ф': 'f', 'х': 'h', 'ц': 'ts',
  'ч': 'ch', 'ш': 'sh', 'щ': 'sch', 'ъ': '', 'ы': 'y', 'ь': '', 'э': 'e', 'ю': 'yu', 'я': 'ya',
  'А': 'A', 'Б': 'B', 'В': 'V', 'Г': 'G', 'Д': 'D', 'Е': 'E', 'Ё': 'Yo', 'Ж': 'Zh',
  'З': 'Z', 'И': 'I', 'Й': 'Y', 'К': 'K', 'Л': 'L', 'М': 'M', 'Н': 'N', 'О': 'O',
  'П': 'P', 'Р': 'R', 'С': 'S', 'Т': 'T', 'У': 'U', 'Ф': 'F', 'Х': 'H', 'Ц': 'Ts',
  'Ч': 'Ch', 'Ш': 'Sh', 'Щ': 'Sch', 'Ъ': '', 'Ы': 'Y', 'Ь': '', 'Э': 'E', 'Ю': 'Yu', 'Я': 'Ya'
};

const transliterate = (text) => {
  return text.split('').map(char => transliterationMap[char] || char).join('');
};

const makeSlug = (text) => {
  let slug = transliterate(text);
  slug = slug.toLowerCase();
  slug = slug.replace(/[^a-z0-9]+/g, '_');
  slug = slug.replace(/^_+|_+$/g, '');
  return slug;
};

const onTitleChange = () => {
  if (!slugManuallyChanged.value && !isEdit.value) {
    form.value.slug = makeSlug(form.value.title);
  }
};

const generateSlug = async () => {
  if (!form.value.title) {
    errorMessage.value = 'Сначала введите название';
    return;
  }

  try {
    const response = await fetch('/admin/api/pages/generate-slug', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        title: form.value.title,
        exclude_id: isEdit.value ? pageId.value : null
      })
    });

    if (response.ok) {
      const data = await response.json();
      form.value.slug = data.slug;
      slugManuallyChanged.value = true;
      errorMessage.value = '';
    }
  } catch (error) {
    console.error('Failed to generate slug:', error);
    errorMessage.value = 'Ошибка при генерации slug';
  }
};

const loadPage = async () => {
  try {
    const response = await fetch(`/admin/api/pages/${pageId.value}`, {
      headers: { 'Accept': 'application/json' }
    });

    if (response.ok) {
      const data = await response.json();
      form.value = {
        title: data.title,
        slug: data.slug,
        type: data.type,
        content: data.content || '',
        meta_title: data.meta_title || '',
        meta_description: data.meta_description || '',
        meta_keywords: data.meta_keywords || '',
        is_active: data.is_active,
        sort: data.sort,
      };
      slugManuallyChanged.value = true;

      // Initialize editor after loading data
      if (data.type === 'static') {
        await nextTick();
        initQuillEditor();
      }
    }
  } catch (error) {
    console.error('Failed to load page:', error);
    errorMessage.value = 'Ошибка при загрузке страницы';
  }
};

const savePage = async () => {
  saving.value = true;
  errorMessage.value = '';

  try {
    const url = isEdit.value ? `/admin/api/pages/${pageId.value}` : '/admin/api/pages';
    const method = isEdit.value ? 'PUT' : 'POST';

    const response = await fetch(url, {
      method,
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
        'Accept': 'application/json'
      },
      body: JSON.stringify(form.value)
    });

    if (response.ok) {
      const data = await response.json();

      if (isEdit.value) {
        router.push('/pages');
      } else if (data.type === 'dynamic') {
        // For new dynamic pages, redirect to builder
        router.push(`/pages/${data.id}/builder`);
      } else {
        router.push('/pages');
      }
    } else {
      const error = await response.json();
      errorMessage.value = error.message || 'Ошибка при сохранении страницы';
    }
  } catch (error) {
    console.error('Failed to save page:', error);
    errorMessage.value = 'Ошибка при сохранении страницы';
  } finally {
    saving.value = false;
  }
};

onMounted(async () => {
  if (isEdit.value) {
    await loadPage();
  } else if (form.value.type === 'static') {
    await nextTick();
    initQuillEditor();
  }
});
</script>

<style>
/* Quill Editor Styles - Light Mode */
.quill-editor .ql-toolbar {
  background-color: #f3f4f6 !important;
  border-color: #d1d5db !important;
  border-top-left-radius: 0.5rem;
  border-top-right-radius: 0.5rem;
}

.quill-editor .ql-toolbar .ql-stroke {
  stroke: #374151 !important;
}

.quill-editor .ql-toolbar .ql-fill {
  fill: #374151 !important;
}

.quill-editor .ql-toolbar .ql-picker-label {
  color: #374151 !important;
}

.quill-editor .ql-toolbar button:hover,
.quill-editor .ql-toolbar button.ql-active {
  background-color: #e5e7eb !important;
}

.quill-editor .ql-toolbar button:hover .ql-stroke,
.quill-editor .ql-toolbar button.ql-active .ql-stroke {
  stroke: #2563eb !important;
}

.quill-editor .ql-toolbar button:hover .ql-fill,
.quill-editor .ql-toolbar button.ql-active .ql-fill {
  fill: #2563eb !important;
}

.quill-editor .ql-container {
  background-color: #ffffff !important;
  border-color: #d1d5db !important;
  border-bottom-left-radius: 0.5rem;
  border-bottom-right-radius: 0.5rem;
}

.quill-editor .ql-editor {
  color: #111827 !important;
  min-height: 350px;
}

.quill-editor .ql-editor * {
  color: #111827 !important;
}

.quill-editor .ql-editor.ql-blank::before {
  color: #9ca3af !important;
}

.quill-editor .ql-picker-options {
  background-color: #ffffff !important;
  border-color: #d1d5db !important;
}

.quill-editor .ql-picker-item {
  color: #374151 !important;
}

.quill-editor .ql-picker-item:hover {
  background-color: #f3f4f6 !important;
}

/* Dark Mode */
.dark .quill-editor .ql-toolbar {
  background-color: #1f2937 !important;
  border-color: #4b5563 !important;
}

.dark .quill-editor .ql-toolbar .ql-stroke {
  stroke: #d1d5db !important;
}

.dark .quill-editor .ql-toolbar .ql-fill {
  fill: #d1d5db !important;
}

.dark .quill-editor .ql-toolbar .ql-picker-label {
  color: #d1d5db !important;
}

.dark .quill-editor .ql-toolbar button:hover,
.dark .quill-editor .ql-toolbar button.ql-active {
  background-color: #374151 !important;
}

.dark .quill-editor .ql-toolbar button:hover .ql-stroke,
.dark .quill-editor .ql-toolbar button.ql-active .ql-stroke {
  stroke: #60a5fa !important;
}

.dark .quill-editor .ql-toolbar button:hover .ql-fill,
.dark .quill-editor .ql-toolbar button.ql-active .ql-fill {
  fill: #60a5fa !important;
}

.dark .quill-editor .ql-container {
  background-color: #111827 !important;
  border-color: #4b5563 !important;
}

.dark .quill-editor .ql-editor {
  color: #f3f4f6 !important;
}

.dark .quill-editor .ql-editor * {
  color: #f3f4f6 !important;
}

.dark .quill-editor .ql-editor.ql-blank::before {
  color: #6b7280 !important;
}

.dark .quill-editor .ql-picker-options {
  background-color: #1f2937 !important;
  border-color: #4b5563 !important;
}

.dark .quill-editor .ql-picker-item {
  color: #d1d5db !important;
}

.dark .quill-editor .ql-picker-item:hover {
  background-color: #374151 !important;
}
</style>
