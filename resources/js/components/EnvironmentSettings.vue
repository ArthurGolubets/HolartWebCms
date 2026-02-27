<template>
  <div>
    <div class="mb-6">
      <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Настройки окружения</h2>
      <p class="text-gray-600 dark:text-gray-400 mt-1">Конфигурация переменных окружения (.env)</p>
    </div>

    <!-- SMTP Settings -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 mb-6">
      <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Настройки SMTP</h3>

      <div class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              SMTP Хост
            </label>
            <input
              v-model="form.mail_host"
              type="text"
              placeholder="smtp.example.com"
              class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white"
            >
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              SMTP Порт
            </label>
            <input
              v-model="form.mail_port"
              type="number"
              placeholder="587"
              class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white"
            >
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Шифрование
          </label>
          <select
            v-model="form.mail_encryption"
            class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white"
          >
            <option value="">Нет</option>
            <option value="tls">TLS</option>
            <option value="ssl">SSL</option>
          </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Имя пользователя
            </label>
            <input
              v-model="form.mail_username"
              type="text"
              placeholder="user@example.com"
              class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white"
            >
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Пароль
            </label>
            <input
              v-model="form.mail_password"
              type="password"
              placeholder="••••••••"
              class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white"
            >
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Email отправителя
            </label>
            <input
              v-model="form.mail_from_address"
              type="email"
              placeholder="noreply@example.com"
              class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white"
            >
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Имя отправителя
            </label>
            <input
              v-model="form.mail_from_name"
              type="text"
              placeholder="HolartCMS"
              class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white"
            >
          </div>
        </div>
      </div>
    </div>

    <!-- Save Button -->
    <div class="flex justify-end space-x-3">
      <button
        @click="testConnection"
        :disabled="loading || testing"
        class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition disabled:opacity-50"
      >
        {{ testing ? 'Проверка...' : 'Проверить подключение' }}
      </button>
      <button
        @click="saveSettings"
        :disabled="loading"
        class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition disabled:opacity-50"
      >
        {{ loading ? 'Сохранение...' : 'Сохранить' }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useModal } from '../composables/useModal';

const { success, error } = useModal();

const loading = ref(false);
const testing = ref(false);

const form = ref({
  mail_host: '',
  mail_port: '587',
  mail_encryption: 'tls',
  mail_username: '',
  mail_password: '',
  mail_from_address: '',
  mail_from_name: '',
});

const loadSettings = async () => {
  try {
    const response = await fetch('/admin/api/environment', {
      headers: { 'Accept': 'application/json' }
    });

    if (response.ok) {
      const data = await response.json();
      form.value = { ...form.value, ...data };
    }
  } catch (err) {
    console.error('Error loading environment settings:', err);
  }
};

const saveSettings = async () => {
  loading.value = true;
  try {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const response = await fetch('/admin/api/environment', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': token,
        'Accept': 'application/json',
      },
      body: JSON.stringify(form.value),
    });

    if (!response.ok) {
      const data = await response.json();
      throw new Error(data.message || 'Failed to save settings');
    }

    await success('Настройки успешно сохранены. Перезапустите приложение для применения изменений.');
  } catch (err) {
    console.error('Error saving settings:', err);
    await error('Ошибка при сохранении настроек: ' + err.message);
  } finally {
    loading.value = false;
  }
};

const testConnection = async () => {
  testing.value = true;
  try {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const response = await fetch('/admin/api/environment/test-smtp', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': token,
        'Accept': 'application/json',
      },
      body: JSON.stringify(form.value),
    });

    const data = await response.json();

    if (response.ok && data.success) {
      await success('SMTP соединение успешно установлено!');
    } else {
      throw new Error(data.message || 'Connection test failed');
    }
  } catch (err) {
    console.error('Error testing connection:', err);
    await error('Не удалось подключиться к SMTP серверу: ' + err.message);
  } finally {
    testing.value = false;
  }
};

onMounted(() => {
  loadSettings();
});
</script>
