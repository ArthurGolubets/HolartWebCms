<template>
  <div>
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
      <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Администраторы</h2>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Управление пользователями административной панели</p>
      </div>
      <button
        @click="showCreateModal = true"
        :style="buttonStyle"
        class="px-4 py-2 text-white rounded-lg font-medium transition-opacity hover:opacity-90 flex items-center"
      >
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Добавить
      </button>
    </div>

    <!-- Search -->
    <div class="mb-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
      <input
        v-model="searchQuery"
        @input="handleSearch"
        type="text"
        placeholder="Поиск по имени, email..."
        class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white"
      >
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-900">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Имя</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Email</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Роль</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Статус</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Действия</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
          <tr v-for="admin in administrators" :key="admin.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div class="w-10 h-10 rounded-full bg-gray-700 flex items-center justify-center font-semibold text-white text-sm mr-3">
                  {{ getInitials(admin.name) }}
                </div>
                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ admin.name }}</span>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">{{ admin.email }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span class="px-2 py-1 text-xs font-medium rounded-full" :class="getRoleBadgeClass(admin.role)">
                {{ getRoleLabel(admin.role) }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span class="px-2 py-1 text-xs font-medium rounded-full" :class="admin.is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'">
                {{ admin.is_active ? 'Активен' : 'Неактивен' }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
              <button
                @click="editAdministrator(admin)"
                class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 mr-3"
              >
                Редактировать
              </button>
              <button
                @click="deleteAdministrator(admin.id)"
                class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
              >
                Удалить
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <div v-if="administrators.length === 0" class="p-8 text-center text-gray-500 dark:text-gray-400">
        Администраторы не найдены
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showCreateModal || showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click.self="closeModal">
      <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md">
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
          {{ showEditModal ? 'Редактировать администратора' : 'Новый администратор' }}
        </h3>

        <form @submit.prevent="submitForm" class="space-y-4">
          <!-- Name -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Имя</label>
            <input
              v-model="form.name"
              type="text"
              required
              class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white"
            >
          </div>

          <!-- Email -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
            <input
              v-model="form.email"
              type="email"
              required
              class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white"
            >
          </div>

          <!-- Password -->
          <div v-if="!showEditModal">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Пароль</label>
            <input
              v-model="form.password"
              type="password"
              :required="!showEditModal"
              class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white"
            >
          </div>

          <!-- Role -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Роль</label>
            <select
              v-model="form.role"
              required
              class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white"
            >
              <option value="manager">Менеджер</option>
              <option value="administrator">Администратор</option>
              <option value="super_admin">Супер администратор</option>
            </select>
          </div>

          <!-- Active -->
          <div>
            <ToggleSwitch v-model="form.is_active" label="Активен" />
          </div>

          <!-- Actions -->
          <div class="flex justify-end space-x-3 mt-6">
            <button
              type="button"
              @click="closeModal"
              class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-white rounded-lg transition"
            >
              Отмена
            </button>
            <button
              type="submit"
              :style="buttonStyle"
              class="px-4 py-2 text-white rounded-lg transition-opacity hover:opacity-90"
            >
              {{ showEditModal ? 'Сохранить' : 'Создать' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useModal } from '../composables/useModal';
import { useTheme } from '../composables/useTheme';
import ToggleSwitch from './ToggleSwitch.vue';

const { confirm, success, error } = useModal();
const { buttonStyle } = useTheme();

const administrators = ref([]);
const allAdministrators = ref([]);
const searchQuery = ref('');
const showCreateModal = ref(false);
const showEditModal = ref(false);
const form = ref({
  id: null,
  name: '',
  email: '',
  password: '',
  role: 'manager',
  is_active: true
});

const handleSearch = () => {
  if (!searchQuery.value) {
    administrators.value = allAdministrators.value;
    return;
  }

  const query = searchQuery.value.toLowerCase();
  administrators.value = allAdministrators.value.filter(admin =>
    admin.name.toLowerCase().includes(query) ||
    admin.email.toLowerCase().includes(query)
  );
};

const getInitials = (name) => {
  return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
};

const getRoleLabel = (role) => {
  const labels = {
    super_admin: 'Супер администратор',
    administrator: 'Администратор',
    manager: 'Менеджер'
  };
  return labels[role] || role;
};

const getRoleBadgeClass = (role) => {
  const classes = {
    super_admin: 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
    administrator: 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
    manager: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
  };
  return classes[role] || classes.manager;
};

const fetchAdministrators = async () => {
  try {
    const response = await fetch('/admin/api/administrators');
    const data = await response.json();
    allAdministrators.value = data;
    administrators.value = data;
  } catch (err) {
    console.error('Error fetching administrators:', err);
    await error('Ошибка при загрузке списка администраторов');
  }
};

const submitForm = async () => {
  const url = showEditModal.value ? `/admin/api/administrators/${form.value.id}` : '/admin/api/administrators';
  const method = showEditModal.value ? 'PUT' : 'POST';

  try {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const response = await fetch(url, {
      method,
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': token
      },
      body: JSON.stringify(form.value)
    });

    if (!response.ok) {
      throw new Error('Failed to save administrator');
    }

    closeModal();
    await fetchAdministrators();
    await success(showEditModal.value ? 'Администратор успешно обновлен' : 'Администратор успешно создан');
  } catch (err) {
    console.error('Error saving administrator:', err);
    await error('Ошибка при сохранении администратора');
  }
};

const editAdministrator = (admin) => {
  form.value = { ...admin };
  showEditModal.value = true;
};

const deleteAdministrator = async (id) => {
  const confirmed = await confirm(
    'Удалить администратора?',
    'Вы уверены, что хотите удалить этого администратора? Это действие нельзя отменить.'
  );

  if (!confirmed) return;

  try {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const response = await fetch(`/admin/api/administrators/${id}`, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': token
      }
    });

    if (!response.ok) {
      const data = await response.json();
      throw new Error(data.message || 'Failed to delete administrator');
    }

    await fetchAdministrators();
    await success('Администратор успешно удален');
  } catch (err) {
    console.error('Error deleting administrator:', err);
    await error(err.message || 'Ошибка при удалении администратора');
  }
};

const closeModal = () => {
  showCreateModal.value = false;
  showEditModal.value = false;
  form.value = {
    id: null,
    name: '',
    email: '',
    password: '',
    role: 'manager',
    is_active: true
  };
};

onMounted(() => {
  fetchAdministrators();
});
</script>
