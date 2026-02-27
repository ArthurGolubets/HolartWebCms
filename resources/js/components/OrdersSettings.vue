<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Настройки заказов</h1>
      <button @click="saveSettings" :disabled="saving" :style="buttonStyle" class="px-4 py-2 text-white rounded-md transition-opacity hover:opacity-90 disabled:opacity-50">
        {{ saving ? 'Сохранение...' : 'Сохранить' }}
      </button>
    </div>

    <div v-if="loading" class="text-center py-12">
      <div class="text-gray-500 dark:text-gray-400">Загрузка настроек...</div>
    </div>

    <div v-else class="space-y-6">
      <!-- General Settings -->
      <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Общие настройки</h2>

        <div class="space-y-4">
          <div>
            <label class="flex items-center space-x-3 cursor-pointer">
              <input v-model="settings.order_notifications_enabled" type="checkbox" class="w-4 h-4 text-blue-600 rounded">
              <span class="text-sm text-gray-700 dark:text-gray-300">Включить уведомления о новых заказах</span>
            </label>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email для уведомлений</label>
            <input v-model="settings.notification_email" type="email" class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md" placeholder="orders@example.com">
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Email, на который будут приходить уведомления о новых заказах</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Минимальная сумма заказа (₽)</label>
            <input v-model="settings.min_order_amount" type="number" step="0.01" class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md" placeholder="0">
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Минимальная сумма для оформления заказа (0 = без ограничений)</p>
          </div>
        </div>
      </div>

      <!-- Delivery Settings -->
      <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Настройки доставки</h2>

        <div class="space-y-4">
          <div>
            <label class="flex items-center space-x-3 cursor-pointer">
              <input v-model="settings.delivery_pickup_enabled" type="checkbox" class="w-4 h-4 text-blue-600 rounded">
              <span class="text-sm text-gray-700 dark:text-gray-300">Включить самовывоз</span>
            </label>
          </div>

          <div>
            <label class="flex items-center space-x-3 cursor-pointer">
              <input v-model="settings.delivery_courier_enabled" type="checkbox" class="w-4 h-4 text-blue-600 rounded">
              <span class="text-sm text-gray-700 dark:text-gray-300">Включить доставку курьером</span>
            </label>
          </div>

          <div v-if="settings.delivery_courier_enabled">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Стоимость доставки курьером (₽)</label>
            <input v-model="settings.delivery_courier_price" type="number" step="0.01" class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md" placeholder="300">
          </div>

          <div>
            <label class="flex items-center space-x-3 cursor-pointer">
              <input v-model="settings.delivery_post_enabled" type="checkbox" class="w-4 h-4 text-blue-600 rounded">
              <span class="text-sm text-gray-700 dark:text-gray-300">Включить доставку почтой</span>
            </label>
          </div>

          <div v-if="settings.delivery_post_enabled">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Стоимость доставки почтой (₽)</label>
            <input v-model="settings.delivery_post_price" type="number" step="0.01" class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md" placeholder="500">
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Бесплатная доставка от (₽)</label>
            <input v-model="settings.free_delivery_from" type="number" step="0.01" class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md" placeholder="3000">
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Сумма заказа, от которой доставка становится бесплатной (0 = нет бесплатной доставки)</p>
          </div>
        </div>
      </div>

      <!-- Payment Settings -->
      <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Настройки оплаты</h2>

        <div class="space-y-4">
          <div>
            <label class="flex items-center space-x-3 cursor-pointer">
              <input v-model="settings.payment_online_enabled" type="checkbox" class="w-4 h-4 text-blue-600 rounded">
              <span class="text-sm text-gray-700 dark:text-gray-300">Включить онлайн оплату</span>
            </label>
          </div>

          <div>
            <label class="flex items-center space-x-3 cursor-pointer">
              <input v-model="settings.payment_cash_enabled" type="checkbox" class="w-4 h-4 text-blue-600 rounded">
              <span class="text-sm text-gray-700 dark:text-gray-300">Включить оплату наличными</span>
            </label>
          </div>
        </div>
      </div>

      <!-- Order Form Fields -->
      <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Поля формы заказа</h2>

        <div class="space-y-4">
          <div>
            <label class="flex items-center space-x-3 cursor-pointer">
              <input v-model="settings.require_phone" type="checkbox" class="w-4 h-4 text-blue-600 rounded">
              <span class="text-sm text-gray-700 dark:text-gray-300">Телефон обязателен</span>
            </label>
          </div>

          <div>
            <label class="flex items-center space-x-3 cursor-pointer">
              <input v-model="settings.require_email" type="checkbox" class="w-4 h-4 text-blue-600 rounded">
              <span class="text-sm text-gray-700 dark:text-gray-300">Email обязателен</span>
            </label>
          </div>

          <div>
            <label class="flex items-center space-x-3 cursor-pointer">
              <input v-model="settings.show_comments_field" type="checkbox" class="w-4 h-4 text-blue-600 rounded">
              <span class="text-sm text-gray-700 dark:text-gray-300">Показывать поле для комментариев</span>
            </label>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useModal } from '../composables/useModal';
import { useTheme } from '../composables/useTheme';

const { success, error } = useModal();
const { buttonStyle } = useTheme();

const loading = ref(false);
const saving = ref(false);

const settings = ref({
  // General
  order_notifications_enabled: true,
  notification_email: '',
  min_order_amount: 0,

  // Delivery
  delivery_pickup_enabled: true,
  delivery_courier_enabled: true,
  delivery_courier_price: 0,
  delivery_post_enabled: false,
  delivery_post_price: 0,
  free_delivery_from: 0,

  // Payment
  payment_online_enabled: true,
  payment_cash_enabled: true,

  // Form fields
  require_phone: true,
  require_email: true,
  show_comments_field: true
});

const loadSettings = async () => {
  loading.value = true;
  try {
    const response = await fetch('/admin/api/orders-settings', {
      headers: { 'Accept': 'application/json' }
    });

    if (response.ok) {
      const data = await response.json();
      settings.value = { ...settings.value, ...data };
    }
  } catch (err) {
    error('Ошибка при загрузке настроек');
  } finally {
    loading.value = false;
  }
};

const saveSettings = async () => {
  saving.value = true;
  try {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const response = await fetch('/admin/api/orders-settings', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': token,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({ settings: settings.value })
    });

    if (response.ok) {
      success('Настройки успешно сохранены');
    } else {
      error('Ошибка при сохранении настроек');
    }
  } catch (err) {
    error('Ошибка при сохранении настроек');
  } finally {
    saving.value = false;
  }
};

onMounted(() => {
  loadSettings();
});
</script>
