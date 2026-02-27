<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Список транзакций</h1>
    </div>

    <!-- Transactions Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">ID транзакции</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">ID заказа</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Ссылка</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Статус</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Дата создания</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Действия</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
          <tr v-if="loading">
            <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">Загрузка...</td>
          </tr>
          <tr v-else-if="transactions.length === 0">
            <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">Нет транзакций</td>
          </tr>
          <tr v-else v-for="transaction in transactions" :key="transaction.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white font-mono">{{ transaction.transaction_id }}</td>
            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">#{{ transaction.order_id }}</td>
            <td class="px-6 py-4 text-sm">
              <a v-if="transaction.link" :href="transaction.link" target="_blank" class="text-blue-600 hover:text-blue-800">Открыть</a>
              <span v-else class="text-gray-400">—</span>
            </td>
            <td class="px-6 py-4">
              <span class="px-2 py-1 text-xs rounded-full" :class="getStatusClass(transaction.status)">
                {{ getStatusLabel(transaction.status) }}
              </span>
            </td>
            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ formatDate(transaction.created_at) }}</td>
            <td class="px-6 py-4 text-sm text-right space-x-2">
              <button @click="updateStatus(transaction)" class="text-blue-600 hover:text-blue-800">Изменить статус</button>
              <button @click="deleteTransaction(transaction.id)" class="text-red-600 hover:text-red-800">Удалить</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Update Status Modal -->
    <div v-if="showStatusModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Изменить статус транзакции</h3>
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Статус</label>
          <select v-model="selectedStatus" class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md">
            <option value="pending">Ожидает</option>
            <option value="success">Успешно</option>
            <option value="cancel">Отменена</option>
          </select>
        </div>
        <div class="flex justify-end space-x-3">
          <button @click="showStatusModal = false" class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md">Отмена</button>
          <button @click="saveStatus" :style="buttonStyle" class="px-4 py-2 text-white rounded-md transition-opacity hover:opacity-90">Сохранить</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useModal } from '../composables/useModal';
import { useTheme } from '../composables/useTheme';

const { success, error, confirm } = useModal();
const { buttonStyle } = useTheme();

const transactions = ref([]);
const loading = ref(false);
const showStatusModal = ref(false);
const selectedTransaction = ref(null);
const selectedStatus = ref('');

const loadTransactions = async () => {
  loading.value = true;
  try {
    const response = await fetch('/admin/api/transactions', {
      headers: { 'Accept': 'application/json' }
    });

    if (response.ok) {
      const data = await response.json();
      transactions.value = data.data || [];
    }
  } catch (err) {
    error('Ошибка при загрузке транзакций');
  } finally {
    loading.value = false;
  }
};

const updateStatus = (transaction) => {
  selectedTransaction.value = transaction;
  selectedStatus.value = transaction.status;
  showStatusModal.value = true;
};

const saveStatus = async () => {
  try {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const response = await fetch(`/admin/api/transactions/${selectedTransaction.value.id}`, {
      method: 'PUT',
      headers: {
        'X-CSRF-TOKEN': token,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({ status: selectedStatus.value })
    });

    if (response.ok) {
      success('Статус транзакции обновлен');
      showStatusModal.value = false;
      loadTransactions();
    } else {
      error('Ошибка при обновлении статуса');
    }
  } catch (err) {
    error('Ошибка при обновлении статуса');
  }
};

const deleteTransaction = async (id) => {
  const confirmed = await confirm('Вы уверены, что хотите удалить эту транзакцию?');
  if (!confirmed) return;

  try {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const response = await fetch(`/admin/api/transactions/${id}`, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': token,
        'Accept': 'application/json'
      }
    });

    if (response.ok) {
      success('Транзакция успешно удалена');
      loadTransactions();
    } else {
      error('Ошибка при удалении транзакции');
    }
  } catch (err) {
    error('Ошибка при удалении транзакции');
  }
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('ru-RU', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const getStatusLabel = (status) => {
  const labels = {
    pending: 'Ожидает',
    success: 'Успешно',
    cancel: 'Отменена'
  };
  return labels[status] || status;
};

const getStatusClass = (status) => {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800',
    success: 'bg-green-100 text-green-800',
    cancel: 'bg-red-100 text-red-800'
  };
  return classes[status] || 'bg-gray-100 text-gray-800';
};

onMounted(() => {
  loadTransactions();
});
</script>
