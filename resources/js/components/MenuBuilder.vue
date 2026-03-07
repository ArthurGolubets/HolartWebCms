<template>
  <div>
    <div v-if="loading" class="flex justify-center items-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
    </div>

    <div v-else-if="menu">
      <!-- Header -->
      <div class="flex justify-between items-center mb-6">
        <div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ menu.name }}</h1>
          <div class="flex items-center gap-2 mt-2">
            <span
              :class="[
                'px-2 py-1 text-xs font-medium rounded',
                menu.location === 'header'
                  ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400'
                  : 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400'
              ]"
            >
              {{ menu.location === 'header' ? 'Шапка' : 'Подвал' }}
            </span>
            <code class="px-2 py-1 text-xs bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded font-mono">{{ menu.code }}</code>
          </div>
        </div>
        <div class="flex gap-3">
          <ThemeButton @click="addItem(null)" variant="primary">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Добавить пункт
          </ThemeButton>
          <ThemeButton @click="$router.push('/menus')" variant="secondary">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Назад
          </ThemeButton>
        </div>
      </div>

      <!-- Menu Items -->
      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg">
        <div class="p-6">
          <div v-if="items.length === 0" class="text-center py-12">
            <svg class="mx-auto h-16 w-16 text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
            <p class="text-gray-600 dark:text-gray-400 mb-4">Пункты меню не созданы</p>
            <ThemeButton @click="addItem(null)" variant="primary">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
              </svg>
              Добавить первый пункт
            </ThemeButton>
          </div>

          <div v-else class="space-y-3">
            <draggable
              v-model="items"
              :options="{ group: 'menu-items', handle: '.drag-handle' }"
              @end="onDragEnd"
              item-key="id"
            >
              <template #item="{ element: item }">
              <div class="mb-3">
                <div class="bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition-shadow">
                  <div class="flex items-center gap-4">
                    <span class="drag-handle cursor-move text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/>
                      </svg>
                    </span>
                    <div class="flex-1">
                      <div class="font-semibold text-gray-900 dark:text-white">{{ item.title }}</div>
                      <div class="text-sm text-gray-600 dark:text-gray-400 mt-1 flex items-center gap-2">
                        <span>{{ item.url || item.route || '—' }}</span>
                        <span v-if="item.target === '_blank'" class="px-2 py-0.5 text-xs bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded">
                          Новая вкладка
                        </span>
                      </div>
                    </div>
                    <div class="flex items-center gap-2">
                      <button
                        @click="toggleItemActive(item)"
                        :class="[
                          'p-2 rounded-lg transition-colors',
                          item.is_active
                            ? 'text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20'
                            : 'text-gray-400 dark:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700'
                        ]"
                        :title="item.is_active ? 'Активно' : 'Неактивно'"
                      >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path v-if="item.is_active" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                          <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                      </button>
                      <button @click="addItem(item)" class="p-2 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors" title="Добавить подпункт">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                      </button>
                      <button @click="editItem(item)" class="p-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors" title="Редактировать">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                      </button>
                      <button @click="confirmDeleteItem(item)" class="p-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors" title="Удалить">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Child Items -->
                <div v-if="item.children && item.children.length > 0" class="ml-12 mt-2 pl-4 border-l-2 border-gray-300 dark:border-gray-600 space-y-2">
                  <draggable
                    v-model="item.children"
                    :options="{ group: 'menu-items', handle: '.drag-handle' }"
                    @end="onDragEnd"
                    item-key="id"
                  >
                    <template #item="{ element: child }">
                    <div class="mb-2">
                      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-3 hover:shadow-sm transition-shadow">
                        <div class="flex items-center gap-3">
                          <span class="drag-handle cursor-move text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/>
                            </svg>
                          </span>
                          <div class="flex-1">
                            <div class="font-medium text-sm text-gray-900 dark:text-white">{{ child.title }}</div>
                            <div class="text-xs text-gray-600 dark:text-gray-400 mt-0.5 flex items-center gap-2">
                              <span>{{ child.url || child.route || '—' }}</span>
                              <span v-if="child.target === '_blank'" class="px-1.5 py-0.5 text-xs bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded">
                                Новая вкладка
                              </span>
                            </div>
                          </div>
                          <div class="flex items-center gap-1">
                            <button
                              @click="toggleItemActive(child)"
                              :class="[
                                'p-1.5 rounded transition-colors',
                                child.is_active
                                  ? 'text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20'
                                  : 'text-gray-400 dark:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700'
                              ]"
                              :title="child.is_active ? 'Активно' : 'Неактивно'"
                            >
                              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path v-if="child.is_active" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                              </svg>
                            </button>
                            <button @click="editItem(child)" class="p-1.5 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded transition-colors" title="Редактировать">
                              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                              </svg>
                            </button>
                            <button @click="confirmDeleteItem(child)" class="p-1.5 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded transition-colors" title="Удалить">
                              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                              </svg>
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                    </template>
                  </draggable>
                </div>
              </div>
              </template>
            </draggable>
          </div>
        </div>
      </div>
    </div>

    <!-- Create/Edit Item Modal -->
    <Modal v-if="showItemModal" @close="closeItemModal" size="large">
      <template #header>
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
          {{ itemFormData.id ? 'Редактировать пункт' : 'Создать пункт' }}
        </h3>
      </template>
      <template #body>
        <form @submit.prevent="saveItem" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Название *</label>
            <input v-model="itemFormData.title" type="text" required class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white" placeholder="Главная" />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">URL</label>
            <input v-model="itemFormData.url" type="text" class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white" placeholder="/catalog" />
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Абсолютный или относительный URL</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Route (Laravel)</label>
            <input v-model="itemFormData.route" type="text" class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white" placeholder="catalog.index" />
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Имя роута Laravel (если указано, URL игнорируется)</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Открывать в</label>
            <select v-model="itemFormData.target" class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white">
              <option value="_self">Той же вкладке</option>
              <option value="_blank">Новой вкладке</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Порядок сортировки</label>
            <input v-model.number="itemFormData.sort" type="number" class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white" />
          </div>

          <div class="flex items-center">
            <input v-model="itemFormData.is_active" type="checkbox" id="itemIsActive" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" />
            <label for="itemIsActive" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Активно</label>
          </div>
        </form>
      </template>
      <template #footer>
        <ThemeButton @click="closeItemModal" variant="secondary">Отмена</ThemeButton>
        <ThemeButton @click="saveItem" variant="primary">Сохранить</ThemeButton>
      </template>
    </Modal>

    <!-- Delete Confirmation Modal -->
    <Modal v-if="deleteItemModal.show" @close="deleteItemModal.show = false">
      <template #header>
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Подтверждение удаления</h3>
      </template>
      <template #body>
        <p class="text-gray-700 dark:text-gray-300">Вы действительно хотите удалить пункт <strong>{{ deleteItemModal.item?.title }}</strong>?</p>
        <p v-if="deleteItemModal.item?.children?.length > 0" class="mt-2 text-sm text-red-600 dark:text-red-400">
          Все подпункты также будут удалены.
        </p>
      </template>
      <template #footer>
        <ThemeButton @click="deleteItemModal.show = false" variant="secondary">Отмена</ThemeButton>
        <ThemeButton @click="confirmDeleteMenuItem" variant="danger">Удалить</ThemeButton>
      </template>
    </Modal>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import draggable from 'vuedraggable';
import Modal from './Modal.vue';
import ThemeButton from './ThemeButton.vue';

const route = useRoute();
const menuId = route.params.id;

const menu = ref(null);
const items = ref([]);
const loading = ref(true);
const showItemModal = ref(false);
const deleteItemModal = ref({ show: false, item: null });

const itemFormData = ref({
  id: null,
  parent_id: null,
  title: '',
  url: '',
  route: '',
  target: '_self',
  sort: 500,
  is_active: true,
});

const loadMenu = async () => {
  loading.value = true;
  try {
    const response = await fetch(`/admin/api/menus/${menuId}`, {
      headers: {
        'Accept': 'application/json',
      },
    });

    if (response.ok) {
      const data = await response.json();
      menu.value = data.menu;
      items.value = data.items || [];
    }
  } catch (error) {
    console.error('Failed to load menu:', error);
  } finally {
    loading.value = false;
  }
};

const addItem = (parentItem) => {
  itemFormData.value = {
    id: null,
    parent_id: parentItem?.id || null,
    title: '',
    url: '',
    route: '',
    target: '_self',
    sort: 500,
    is_active: true,
  };
  showItemModal.value = true;
};

const editItem = (item) => {
  itemFormData.value = {
    id: item.id,
    parent_id: item.parent_id,
    title: item.title,
    url: item.url || '',
    route: item.route || '',
    target: item.target || '_self',
    sort: item.sort || 500,
    is_active: item.is_active,
  };
  showItemModal.value = true;
};

const closeItemModal = () => {
  showItemModal.value = false;
  itemFormData.value = {
    id: null,
    parent_id: null,
    title: '',
    url: '',
    route: '',
    target: '_self',
    sort: 500,
    is_active: true,
  };
};

const saveItem = async () => {
  try {
    const url = itemFormData.value.id
      ? `/admin/api/menu-items/${itemFormData.value.id}`
      : '/admin/api/menu-items';

    const method = itemFormData.value.id ? 'PUT' : 'POST';

    const payload = {
      ...itemFormData.value,
      menu_id: menuId,
    };

    const response = await fetch(url, {
      method,
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
        'Accept': 'application/json',
      },
      body: JSON.stringify(payload),
    });

    if (response.ok) {
      closeItemModal();
      loadMenu();
    } else {
      const error = await response.json();
      alert(error.message || 'Ошибка при сохранении пункта меню');
    }
  } catch (error) {
    console.error('Failed to save menu item:', error);
    alert('Ошибка при сохранении пункта меню');
  }
};

const confirmDeleteItem = (item) => {
  deleteItemModal.value = {
    show: true,
    item,
  };
};

const confirmDeleteMenuItem = async () => {
  try {
    const response = await fetch(`/admin/api/menu-items/${deleteItemModal.value.item.id}`, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
        'Accept': 'application/json',
      },
    });

    if (response.ok) {
      deleteItemModal.value.show = false;
      loadMenu();
    } else {
      const error = await response.json();
      alert(error.message || 'Ошибка при удалении пункта меню');
    }
  } catch (error) {
    console.error('Failed to delete menu item:', error);
    alert('Ошибка при удалении пункта меню');
  }
};

const toggleItemActive = async (item) => {
  try {
    const response = await fetch(`/admin/api/menu-items/${item.id}/toggle-active`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
        'Accept': 'application/json',
      },
    });

    if (response.ok) {
      const data = await response.json();
      item.is_active = data.is_active;
    }
  } catch (error) {
    console.error('Failed to toggle item active:', error);
  }
};

const onDragEnd = async () => {
  // Flatten all items with their new positions and parents
  const allItems = [];

  items.value.forEach((item, index) => {
    allItems.push({
      id: item.id,
      sort: index * 10,
      parent_id: null,
    });

    if (item.children) {
      item.children.forEach((child, childIndex) => {
        allItems.push({
          id: child.id,
          sort: childIndex * 10,
          parent_id: item.id,
        });
      });
    }
  });

  try {
    await fetch('/admin/api/menu-items/reorder', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
        'Accept': 'application/json',
      },
      body: JSON.stringify({ items: allItems }),
    });
  } catch (error) {
    console.error('Failed to reorder items:', error);
  }
};

onMounted(() => {
  loadMenu();
});
</script>
