<template>
  <div>
    <label v-if="label" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
      {{ label }}
    </label>
    <QuillEditor
      :content="modelValue"
      @update:content="$emit('update:modelValue', $event)"
      content-type="html"
      theme="snow"
      :toolbar="toolbarOptions"
      :style="{ height: height + 'px' }"
    />
  </div>
</template>

<script setup>
import { QuillEditor } from '@vueup/vue-quill';
import '@vueup/vue-quill/dist/vue-quill.snow.css';

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  label: {
    type: String,
    default: ''
  },
  height: {
    type: Number,
    default: 400
  }
});

defineEmits(['update:modelValue']);

const toolbarOptions = [
  ['bold', 'italic', 'underline', 'strike'],
  ['blockquote', 'code-block'],
  [{ 'header': 1 }, { 'header': 2 }],
  [{ 'list': 'ordered'}, { 'list': 'bullet' }],
  [{ 'script': 'sub'}, { 'script': 'super' }],
  [{ 'indent': '-1'}, { 'indent': '+1' }],
  [{ 'direction': 'rtl' }],
  [{ 'size': ['small', false, 'large', 'huge'] }],
  [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
  [{ 'color': [] }, { 'background': [] }],
  [{ 'font': [] }],
  [{ 'align': [] }],
  ['link', 'image', 'video'],
  ['clean']
];
</script>

<style scoped>
:deep(.ql-container) {
  font-size: 14px;
  background-color: white;
}

:deep(.ql-editor) {
  min-height: 300px;
  background-color: white;
  color: #1f2937;
}

:deep(.ql-toolbar) {
  background-color: #f9fafb;
  border-color: #d1d5db;
}

/* Dark theme support */
:deep(.dark .ql-container),
:deep(.dark .ql-editor) {
  background-color: #374151;
  color: #f9fafb;
  border-color: #4b5563;
}

:deep(.dark .ql-toolbar) {
  background-color: #1f2937;
  border-color: #4b5563;
}

:deep(.dark .ql-stroke) {
  stroke: #9ca3af;
}

:deep(.dark .ql-fill) {
  fill: #9ca3af;
}

:deep(.dark .ql-picker-label) {
  color: #9ca3af;
}
</style>
