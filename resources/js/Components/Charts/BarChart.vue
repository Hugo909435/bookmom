<script setup>
import { ref, onMounted, watch } from 'vue';
import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);

const props = defineProps({
    data: Object,
    title: String,
});

const chartRef = ref(null);
let chartInstance = null;

const createChart = () => {
    if (chartInstance) {
        chartInstance.destroy();
    }

    const ctx = chartRef.value.getContext('2d');
    chartInstance = new Chart(ctx, {
        type: 'bar',
        data: props.data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false,
                },
                title: {
                    display: true,
                    text: props.title,
                    font: {
                        size: 16,
                        weight: 'bold',
                    },
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0,
                    },
                },
            },
        },
    });
};

onMounted(() => {
    createChart();
});

watch(() => props.data, () => {
    createChart();
}, { deep: true });
</script>

<template>
    <div class="relative h-64 sm:h-80">
        <canvas ref="chartRef"></canvas>
    </div>
</template>
