<template>
  <div>
    <label v-if="label" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
      {{ label }}
      <span v-if="required" class="text-red-500">*</span>
    </label>

    <!-- Values List -->
    <div v-if="values.length > 0" class="space-y-2 mb-3">
      <div v-for="(value, index) in values" :key="index" class="flex items-center gap-2">
        <!-- Input based on type -->
        <input
          v-if="fieldType === 'string'"
          v-model="values[index]"
          type="text"
          :placeholder="`Значение ${index + 1}`"
          class="flex-1 px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white"
        >
        <textarea
          v-else-if="fieldType === 'text'"
          v-model="values[index]"
          :placeholder="`Значение ${index + 1}`"
          rows="2"
          class="flex-1 px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white"
        ></textarea>
        <input
          v-else-if="fieldType === 'number'"
          v-model.number="values[index]"
          type="number"
          :placeholder="`Значение ${index + 1}`"
          class="flex-1 px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white"
        >
        <input
          v-else-if="fieldType === 'double'"
          v-model.number="values[index]"
          type="number"
          step="0.01"
          :placeholder="`Значение ${index + 1}`"
          class="flex-1 px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white"
        >
        <input
          v-else-if="fieldType === 'date'"
          v-model="values[index]"
          type="date"
          class="flex-1 px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white"
        >
        <input
          v-else-if="fieldType === 'datetime'"
          v-model="values[index]"
          type="datetime-local"
          class="flex-1 px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white"
        >
        <input
          v-else
          v-model="values[index]"
          type="text"
          :placeholder="`Значение ${index + 1}`"
          class="flex-1 px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white"
        >

        <!-- Remove Button -->
        <button
          @click="removeValue(index)"
          type="button"
          class="p-2 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition"
          title="Удалить"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
          </svg>
        </button>
      </div>
    </div>

    <!-- Add Button -->
    <button
      @click="addValue"
      type="button"
      class="w-full flex items-center justify-center gap-2 px-4 py-2 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg text-gray-600 dark:text-gray-400 hover:border-blue-500 hover:text-blue-500 dark:hover:border-blue-400 dark:hover:text-blue-400 transition"
    >
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
      </svg>
      <span class="text-sm font-medium">Добавить значение</span>
    </button>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
  modelValue: [Array, String],
  label: String,
  required: Boolean,
  fieldType: {
    type: String,
    default: 'string'
  }
});

const emit = defineEmits(['update:modelValue']);

const values = ref([]);

// Initialize values from modelValue
watch(() => props.modelValue, (newVal) => {
  if (Array.isArray(newVal)) {
    values.value = [...newVal];
  } else if (newVal) {
    values.value = [newVal];
  } else {
    values.value = [];
  }
}, { immediate: true });

// Emit changes
watch(values, (newVals) => {
  emit('update:modelValue', newVals.filter(v => v !== '' && v !== null && v !== undefined));
}, { deep: true });

const addValue = () => {
  if (props.fieldType === 'number' || props.fieldType === 'double') {
    values.value.push(0);
  } else if (props.fieldType === 'bool') {
    values.value.push(false);
  } else {
    values.value.push('');
  }
};

const removeValue = (index) => {
  values.value.splice(index, 1);
};
</script>
