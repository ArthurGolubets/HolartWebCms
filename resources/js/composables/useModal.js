import { ref } from 'vue';

const modalState = ref({
  show: false,
  type: 'info',
  title: '',
  message: '',
  confirmText: 'OK',
  cancelText: 'Отмена',
  showCancel: false,
  onConfirm: null,
  onCancel: null
});

export function useModal() {
  const showModal = (options) => {
    return new Promise((resolve) => {
      modalState.value = {
        show: true,
        type: options.type || 'info',
        title: options.title || '',
        message: options.message || '',
        confirmText: options.confirmText || 'OK',
        cancelText: options.cancelText || 'Отмена',
        showCancel: options.showCancel !== undefined ? options.showCancel : false,
        onConfirm: () => {
          if (options.onConfirm) options.onConfirm();
          resolve(true);
        },
        onCancel: () => {
          if (options.onCancel) options.onCancel();
          resolve(false);
        }
      };
    });
  };

  const confirm = (title, message) => {
    return showModal({
      type: 'confirm',
      title: title || 'Подтверждение',
      message: message || 'Вы уверены?',
      confirmText: 'Да',
      cancelText: 'Отмена',
      showCancel: true
    });
  };

  const alert = (message, type = 'info', title = '') => {
    return showModal({
      type,
      title: title || getDefaultTitle(type),
      message,
      confirmText: 'OK',
      showCancel: false
    });
  };

  const success = (message, title = 'Успешно') => {
    return alert(message, 'success', title);
  };

  const error = (message, title = 'Ошибка') => {
    return alert(message, 'error', title);
  };

  const warning = (message, title = 'Внимание') => {
    return alert(message, 'warning', title);
  };

  const info = (message, title = 'Информация') => {
    return alert(message, 'info', title);
  };

  const getDefaultTitle = (type) => {
    const titles = {
      success: 'Успешно',
      error: 'Ошибка',
      warning: 'Внимание',
      info: 'Информация',
      confirm: 'Подтверждение'
    };
    return titles[type] || 'Уведомление';
  };

  const closeModal = () => {
    modalState.value.show = false;
  };

  return {
    modalState,
    showModal,
    confirm,
    alert,
    success,
    error,
    warning,
    info,
    closeModal
  };
}
