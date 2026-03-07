<template>
  <div class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-white/50 dark:bg-gray-800/50">
      <h3 class="text-sm font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">{{ title }}</h3>
    </div>

    <div class="p-6">
      <div v-if="loading" class="text-center py-8">
        <div class="inline-block animate-spin rounded-full h-10 w-10 border-4 border-blue-200 border-t-blue-600"></div>
      </div>

      <div v-else-if="!chartData || chartData.length === 0" class="text-center py-8">
        <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
        </svg>
        <p class="text-sm text-gray-500 dark:text-gray-400">Нет данных для графика</p>
      </div>

      <div v-else>
        <!-- Line Chart -->
        <div v-if="chartType === 'line'" class="relative h-64">
          <svg class="w-full h-full" viewBox="0 0 400 200" preserveAspectRatio="none">
            <!-- Grid lines -->
            <line v-for="i in 5" :key="`grid-${i}`" 
                  x1="0" :y1="i * 40" x2="400" :y2="i * 40" 
                  stroke="currentColor" class="text-gray-200 dark:text-gray-700" stroke-width="0.5" />
            
            <!-- Line path -->
            <polyline 
              :points="linePoints" 
              fill="none" 
              stroke="currentColor" 
              :class="`${lineColor}`"
              stroke-width="3"
              stroke-linecap="round"
              stroke-linejoin="round" />
            
            <!-- Area fill -->
            <polygon 
              :points="areaPoints" 
              :class="`${areaColor}`"
              opacity="0.2" />
            
            <!-- Data points -->
            <circle v-for="(point, index) in dataPoints" :key="`point-${index}`"
                    :cx="point.x" :cy="point.y" r="4"
                    fill="currentColor" :class="`${lineColor}`"
                    class="hover:r-6 transition-all cursor-pointer"
                    @mouseenter="showTooltip(index, $event)"
                    @mouseleave="hideTooltip">
            </circle>
          </svg>

          <!-- Tooltip -->
          <div v-if="tooltipVisible"
               class="absolute z-10 bg-gray-900 dark:bg-gray-700 text-white text-xs px-3 py-2 rounded shadow-lg pointer-events-none whitespace-nowrap"
               :style="{ left: `${tooltipX}px`, top: `${tooltipY}px`, transform: 'translate(-50%, -120%)' }">
            <div class="font-semibold">{{ tooltipData.label }}</div>
            <div>{{ formatTooltipValue(tooltipData.value) }}</div>
          </div>

          <!-- Labels -->
          <div class="flex justify-between mt-3 text-xs text-gray-500 dark:text-gray-400">
            <span>{{ chartData[0]?.label }}</span>
            <span>{{ chartData[Math.floor(chartData.length / 2)]?.label }}</span>
            <span>{{ chartData[chartData.length - 1]?.label }}</span>
          </div>
        </div>

        <!-- Bar Chart -->
        <div v-else-if="chartType === 'bar'" class="flex items-end justify-between gap-2 h-64">
          <div v-for="(item, index) in chartData" :key="`bar-${index}`" 
               class="flex-1 flex flex-col items-center group">
            <div class="w-full bg-gradient-to-t rounded-t-lg transition-all duration-300 hover:opacity-80 cursor-pointer relative"
                 :class="getBarColor(index)"
                 :style="{ height: `${(item.value / maxValue) * 100}%` }">
              <div class="absolute -top-6 left-1/2 transform -translate-x-1/2 opacity-0 group-hover:opacity-100 transition-opacity bg-gray-900 dark:bg-gray-700 text-white text-xs px-2 py-1 rounded whitespace-nowrap">
                {{ item.value }}
              </div>
            </div>
            <p class="text-xs text-gray-600 dark:text-gray-400 mt-2 text-center truncate w-full" :title="item.label">
              {{ item.label }}
            </p>
          </div>
        </div>

        <!-- Donut Chart -->
        <div v-else-if="chartType === 'donut'" class="flex items-center justify-center">
          <div class="relative w-64 h-64">
            <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
              <circle cx="50" cy="50" r="40" 
                      fill="none" 
                      stroke="currentColor" 
                      class="text-gray-200 dark:text-gray-700" 
                      stroke-width="12" />
              
              <circle v-for="(segment, index) in donutSegments" :key="`segment-${index}`"
                      cx="50" cy="50" r="40"
                      fill="none"
                      :stroke="segment.color"
                      stroke-width="12"
                      :stroke-dasharray="`${segment.length} ${circumference - segment.length}`"
                      :stroke-dashoffset="-segment.offset"
                      class="transition-all duration-300 hover:stroke-width-14 cursor-pointer">
                <title>{{ segment.label }}: {{ segment.value }} ({{ segment.percentage }}%)</title>
              </circle>
            </svg>
            
            <div class="absolute inset-0 flex items-center justify-center flex-col">
              <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ totalValue }}</p>
              <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Всего</p>
            </div>
          </div>
          
          <!-- Legend -->
          <div class="mt-4 grid grid-cols-2 gap-2">
            <div v-for="(item, index) in chartData" :key="`legend-${index}`" 
                 class="flex items-center gap-2">
              <div class="w-3 h-3 rounded-full" :style="{ backgroundColor: getDonutColor(index) }"></div>
              <span class="text-xs text-gray-600 dark:text-gray-400">{{ item.label }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue';

const props = defineProps({
  title: {
    type: String,
    required: true,
  },
  chartData: {
    type: Array,
    default: () => [],
  },
  chartType: {
    type: String,
    default: 'line', // line, bar, donut
  },
  loading: {
    type: Boolean,
    default: false,
  },
  lineColor: {
    type: String,
    default: 'text-blue-500',
  },
  areaColor: {
    type: String,
    default: 'fill-blue-500',
  },
});

const maxValue = computed(() => {
  if (!props.chartData || props.chartData.length === 0) return 1;
  return Math.max(...props.chartData.map(item => item.value || 0));
});

const dataPoints = computed(() => {
  if (!props.chartData || props.chartData.length === 0) return [];
  
  const width = 400;
  const height = 200;
  const padding = 20;
  const stepX = (width - padding * 2) / (props.chartData.length - 1);
  
  return props.chartData.map((item, index) => {
    const x = padding + (index * stepX);
    const y = height - padding - ((item.value / maxValue.value) * (height - padding * 2));
    return { x, y };
  });
});

const linePoints = computed(() => {
  return dataPoints.value.map(p => `${p.x},${p.y}`).join(' ');
});

const areaPoints = computed(() => {
  if (dataPoints.value.length === 0) return '';
  const points = [...dataPoints.value];
  const lastPoint = points[points.length - 1];
  const firstPoint = points[0];
  return `${linePoints.value} ${lastPoint.x},200 ${firstPoint.x},200`;
});

const barColors = [
  'from-blue-400 to-blue-600',
  'from-green-400 to-green-600',
  'from-purple-400 to-purple-600',
  'from-yellow-400 to-yellow-600',
  'from-red-400 to-red-600',
  'from-indigo-400 to-indigo-600',
  'from-pink-400 to-pink-600',
];

function getBarColor(index) {
  return barColors[index % barColors.length];
}

const donutColors = [
  '#3B82F6', '#10B981', '#8B5CF6', '#F59E0B', '#EF4444', '#6366F1', '#EC4899'
];

function getDonutColor(index) {
  return donutColors[index % donutColors.length];
}

const circumference = computed(() => 2 * Math.PI * 40);

const totalValue = computed(() => {
  if (!props.chartData || props.chartData.length === 0) return 0;
  return props.chartData.reduce((sum, item) => sum + (item.value || 0), 0);
});

const donutSegments = computed(() => {
  if (!props.chartData || props.chartData.length === 0) return [];

  let currentOffset = 0;
  return props.chartData.map((item, index) => {
    const percentage = totalValue.value > 0 ? (item.value / totalValue.value) * 100 : 0;
    const length = (percentage / 100) * circumference.value;
    const segment = {
      label: item.label,
      value: item.value,
      percentage: percentage.toFixed(1),
      length,
      offset: currentOffset,
      color: getDonutColor(index),
    };
    currentOffset += length;
    return segment;
  });
});

// Tooltip state
const tooltipVisible = ref(false);
const tooltipX = ref(0);
const tooltipY = ref(0);
const tooltipData = ref({ label: '', value: 0 });

function showTooltip(index, event) {
  const rect = event.target.getBoundingClientRect();
  const containerRect = event.target.closest('.relative').getBoundingClientRect();

  tooltipX.value = rect.left - containerRect.left + rect.width / 2;
  tooltipY.value = rect.top - containerRect.top;
  tooltipData.value = props.chartData[index];
  tooltipVisible.value = true;
}

function hideTooltip() {
  tooltipVisible.value = false;
}

function formatTooltipValue(value) {
  if (props.title.includes('выручки') || props.title.includes('Выручки')) {
    return new Intl.NumberFormat('ru-RU', { style: 'currency', currency: 'RUB' }).format(value);
  }
  return new Intl.NumberFormat('ru-RU').format(value);
}
</script>
