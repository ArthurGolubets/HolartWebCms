<template>
  <div v-if="email">
    <div class="flex items-center mb-6">
      <button
        @click="$router.push('/users-emails')"
        class="mr-4 p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition"
      >
        <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
      </button>
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Просмотр email</h1>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
      <div class="space-y-6">
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
          <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700 rounded-lg text-gray-900 dark:text-white">
            {{ email.email }}
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Статус</label>
          <div>
            <span
              class="px-3 py-2 rounded-lg text-sm font-medium inline-block"
              :class="{
                'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': email.status === 'active',
                'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200': email.status === 'unsubscribed',
                'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200': email.status === 'bounced'
              }"
            >
              {{ statusLabel(email.status) }}
            </span>
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Дата создания</label>
          <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700 rounded-lg text-gray-900 dark:text-white">
            {{ formatDate(email.created_at) }}
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Дата обновления</label>
          <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700 rounded-lg text-gray-900 dark:text-white">
            {{ formatDate(email.updated_at) }}
          </div>
        </div>
      </div>

      <div class="mt-6 flex space-x-3">
        <button
          v-if="canEdit"
          @click="$router.push(`/users-emails/${email.id}/edit`)"
          :style="buttonStyle"
          class="px-4 py-2 text-white rounded-lg transition-opacity hover:opacity-90"
        >
          Редактировать
        </button>
        <button
          @click="deleteEmail"
          class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition"
        >
          Удалить
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useModal } from '../composables/useModal';
import { useTheme } from '../composables/useTheme';

const router = useRouter();
const route = useRoute();
const { success, error, confirm } = useModal();
const { buttonStyle } = useTheme();

const email = ref(null);

const canEdit = computed(() => {
  const userData = JSON.parse(localStorage.getItem('holart-user') || '{}');
  return userData.role === 'super_admin' || userData.role === 'administrator';
});

const statusLabel = (status) => {
  const labels = {
    active: 'Активен',
    unsubscribed: 'Отписан',
    bounced: 'Ошибка'
  };
  return labels[status] || status;
};

const formatDate = (date) => {
  return new Date(date).toLocaleString('ru-RU');
};

const loadEmail = async () => {
  try {
    const response = await fetch(`/admin/api/users-emails/${route.params.id}`, {
      headers: { 'Accept': 'application/json' }
    });

    if (response.ok) {
      email.value = await response.json();
    } else {
      error('Ошибка загрузки данных');
      router.push('/users-emails');
    }
  } catch (err) {
    error('Ошибка загрузки данных');
    router.push('/users-emails');
  }
};

const deleteEmail = async () => {
  const confirmed = await confirm('Вы уверены, что хотите удалить этот email?');
  if (!confirmed) return;

  try {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const response = await fetch(`/admin/api/users-emails/${route.params.id}`, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': token,
        'Accept': 'application/json'
      }
    });

    if (response.ok) {
      success('Email успешно удален');
      router.push('/users-emails');
    } else {
      error('Ошибка при удалении');
    }
  } catch (err) {
    error('Ошибка при удалении');
  }
};

onMounted(() => {
  loadEmail();
});
</script>
