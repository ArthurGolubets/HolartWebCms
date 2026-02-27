<template>
  <div>
    <div class="mb-6">
      <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Настройки системы</h2>
      <p class="text-gray-600 dark:text-gray-400 mt-1">Управление параметрами административной панели</p>
    </div>

    <form @submit.prevent="saveSettings" class="space-y-6">
      <!-- Основные настройки -->
      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Основные настройки</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Название системы</label>
            <input v-model="settings.panel_name" type="text" class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white">
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Цвет темы</label>
            <select v-model="settings.theme_color" class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white">
              <option value="red">Красный</option>
              <option value="blue">Синий</option>
              <option value="green">Зеленый</option>
              <option value="purple">Фиолетовый</option>
              <option value="orange">Оранжевый</option>
              <option value="pink">Розовый</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Компания -->
      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Информация о компании</h3>
        
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Название компании</label>
            <input v-model="settings.company_name" type="text" class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white">
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Время работы</label>
            <input v-model="settings.work_hours" type="text" placeholder="Пн-Пт: 9:00-18:00" class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white">
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Телефоны</label>
            <div v-for="(phone, index) in settings.phones" :key="index" class="flex mb-2">
              <input v-model="settings.phones[index]" type="text" class="flex-1 px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-l-lg text-gray-900 dark:text-white">
              <button @click.prevent="removePhone(index)" type="button" class="px-4 py-2 bg-red-600 text-white rounded-r-lg hover:bg-red-700">×</button>
            </div>
            <button @click.prevent="addPhone" type="button" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">+ Добавить телефон</button>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Адреса</label>
            <div v-for="(address, index) in settings.addresses" :key="index" class="flex mb-2">
              <input v-model="settings.addresses[index]" type="text" class="flex-1 px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-l-lg text-gray-900 dark:text-white">
              <button @click.prevent="removeAddress(index)" type="button" class="px-4 py-2 bg-red-600 text-white rounded-r-lg hover:bg-red-700">×</button>
            </div>
            <button @click.prevent="addAddress" type="button" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">+ Добавить адрес</button>
          </div>
        </div>
      </div>

      <!-- Интеграции -->
      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Интеграции</h3>

        <!-- Tabs -->
        <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
          <nav class="flex space-x-4">
            <button
              @click="activeTab = 'bitrix24'"
              type="button"
              class="pb-3 px-1 border-b-2 font-medium text-sm transition"
              :class="activeTab === 'bitrix24'
                ? 'border-blue-600 text-blue-600 dark:border-blue-400 dark:text-blue-400'
                : 'border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200'"
            >
              Bitrix24
            </button>
            <button
              @click="activeTab = 'payment'"
              type="button"
              class="pb-3 px-1 border-b-2 font-medium text-sm transition"
              :class="activeTab === 'payment'
                ? 'border-blue-600 text-blue-600 dark:border-blue-400 dark:text-blue-400'
                : 'border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200'"
            >
              Юкасса
            </button>
            <button
              @click="activeTab = 'telegram'"
              type="button"
              class="pb-3 px-1 border-b-2 font-medium text-sm transition"
              :class="activeTab === 'telegram'
                ? 'border-blue-600 text-blue-600 dark:border-blue-400 dark:text-blue-400'
                : 'border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200'"
            >
              Telegram
            </button>
          </nav>
        </div>

        <!-- Tab Content -->
        <div class="space-y-4">
          <!-- Bitrix24 Tab -->
          <div v-if="activeTab === 'bitrix24'">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Webhook URL</label>
              <input
                v-model="settings.bitrix24_webhook"
                type="text"
                placeholder="https://your-domain.bitrix24.ru/rest/1/..."
                class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white"
              >
              <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                Вебхук для интеграции с CRM Bitrix24
              </p>
            </div>
          </div>

          <!-- Payment Tab -->
          <div v-if="activeTab === 'payment'">
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Shop ID</label>
                <input
                  v-model="settings.payment_shop_id"
                  type="text"
                  placeholder="12345"
                  class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white"
                >
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Secret Key</label>
                <input
                  v-model="settings.payment_secret"
                  type="password"
                  placeholder="••••••••••••••••"
                  class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white"
                >
              </div>
              <p class="text-xs text-gray-500 dark:text-gray-400">
                Настройки для интеграции с Юкассой
              </p>
            </div>
          </div>

          <!-- Telegram Tab -->
          <div v-if="activeTab === 'telegram'">
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bot Token</label>
                <input
                  v-model="settings.telegram_token"
                  type="password"
                  placeholder="1234567890:ABCdefGHIjklMNOpqrsTUVwxyz"
                  class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white"
                >
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Chat ID</label>
                <input
                  v-model="settings.telegram_chat_id"
                  type="text"
                  placeholder="-1001234567890"
                  class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white"
                >
              </div>
              <p class="text-xs text-gray-500 dark:text-gray-400">
                Настройки для отправки уведомлений в Telegram
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Код для вставки -->
      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Код для вставки</h3>
        
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Код в &lt;head&gt;</label>
            <textarea v-model="settings.header_code" rows="4" class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white font-mono text-sm"></textarea>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Код перед &lt;/body&gt;</label>
            <textarea v-model="settings.footer_code" rows="4" class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white font-mono text-sm"></textarea>
          </div>
        </div>
      </div>

      <!-- Кнопка сохранения -->
      <div class="flex justify-end">
        <button type="submit" :disabled="loading" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition disabled:opacity-50">
          {{ loading ? 'Сохранение...' : 'Сохранить изменения' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useModal } from '../composables/useModal';

const { success, error } = useModal();

const loading = ref(false);
const activeTab = ref('bitrix24');
const settings = ref({
  panel_name: '',
  theme_color: 'red',
  company_name: '',
  phones: [],
  work_hours: '',
  addresses: [],
  bitrix24_webhook: '',
  payment_shop_id: '',
  payment_secret: '',
  telegram_chat_id: '',
  telegram_token: '',
  header_code: '',
  footer_code: ''
});

const fetchSettings = async () => {
  try {
    const response = await fetch('/admin/api/settings');
    const data = await response.json();
    settings.value = {
      ...settings.value,
      ...data,
      phones: data.phones || [],
      addresses: data.addresses || []
    };
  } catch (err) {
    console.error('Error fetching settings:', err);
    await error('Ошибка при загрузке настроек');
  }
};

const saveSettings = async () => {
  loading.value = true;
  try {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const response = await fetch('/admin/api/settings', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': token
      },
      body: JSON.stringify(settings.value)
    });

    if (!response.ok) {
      throw new Error('Failed to save settings');
    }

    await success('Настройки успешно сохранены!');

    // Reload page to apply theme color changes
    setTimeout(() => {
      window.location.reload();
    }, 1500);
  } catch (err) {
    console.error('Error saving settings:', err);
    await error('Ошибка при сохранении настроек');
  } finally {
    loading.value = false;
  }
};

const addPhone = () => {
  settings.value.phones.push('');
};

const removePhone = (index) => {
  settings.value.phones.splice(index, 1);
};

const addAddress = () => {
  settings.value.addresses.push('');
};

const removeAddress = (index) => {
  settings.value.addresses.splice(index, 1);
};

onMounted(() => {
  fetchSettings();
});
</script>
