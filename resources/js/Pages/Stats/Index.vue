<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import BarChart from '@/Components/Charts/BarChart.vue';
import PieChart from '@/Components/Charts/PieChart.vue';
import { computed } from 'vue';

const props = defineProps({
    stats: Object,
});

// Graphique des status (Pie Chart)
const statusChartData = computed(() => ({
    labels: Object.keys(props.stats.booksByStatus),
    datasets: [{
        label: 'Nombre de livres',
        data: Object.values(props.stats.booksByStatus),
        backgroundColor: [
            'rgba(59, 130, 246, 0.8)',
            'rgba(16, 185, 129, 0.8)',
            'rgba(251, 146, 60, 0.8)',
            'rgba(239, 68, 68, 0.8)',
            'rgba(168, 85, 247, 0.8)',
            'rgba(236, 72, 153, 0.8)',
        ],
        borderColor: [
            'rgb(59, 130, 246)',
            'rgb(16, 185, 129)',
            'rgb(251, 146, 60)',
            'rgb(239, 68, 68)',
            'rgb(168, 85, 247)',
            'rgb(236, 72, 153)',
        ],
        borderWidth: 2,
    }],
}));

// Graphique des lieux (Pie Chart)
const locationChartData = computed(() => ({
    labels: Object.keys(props.stats.booksByLocation),
    datasets: [{
        label: 'Nombre de livres',
        data: Object.values(props.stats.booksByLocation),
        backgroundColor: [
            'rgba(239, 68, 68, 0.8)',
            'rgba(251, 146, 60, 0.8)',
            'rgba(234, 179, 8, 0.8)',
            'rgba(16, 185, 129, 0.8)',
            'rgba(59, 130, 246, 0.8)',
            'rgba(99, 102, 241, 0.8)',
            'rgba(168, 85, 247, 0.8)',
            'rgba(236, 72, 153, 0.8)',
        ],
        borderColor: [
            'rgb(239, 68, 68)',
            'rgb(251, 146, 60)',
            'rgb(234, 179, 8)',
            'rgb(16, 185, 129)',
            'rgb(59, 130, 246)',
            'rgb(99, 102, 241)',
            'rgb(168, 85, 247)',
            'rgb(236, 72, 153)',
        ],
        borderWidth: 2,
    }],
}));

// Top auteurs (Bar Chart)
const authorsChartData = computed(() => ({
    labels: props.stats.topAuthors.map(a => a.auteur),
    datasets: [{
        label: 'Nombre de livres',
        data: props.stats.topAuthors.map(a => a.count),
        backgroundColor: 'rgba(99, 102, 241, 0.8)',
        borderColor: 'rgb(99, 102, 241)',
        borderWidth: 2,
    }],
}));

// Auteurs par lieu (Bar Chart)
const authorsLocationChartData = computed(() => ({
    labels: Object.keys(props.stats.authorsByLocation),
    datasets: [{
        label: 'Nombre d\'auteurs différents',
        data: Object.values(props.stats.authorsByLocation),
        backgroundColor: 'rgba(16, 185, 129, 0.8)',
        borderColor: 'rgb(16, 185, 129)',
        borderWidth: 2,
    }],
}));

// Top combinaisons auteur + status
const authorStatusChartData = computed(() => ({
    labels: props.stats.topAuthorStatus.map(item => item.label),
    datasets: [{
        label: 'Nombre de livres',
        data: props.stats.topAuthorStatus.map(item => item.count),
        backgroundColor: 'rgba(236, 72, 153, 0.8)',
        borderColor: 'rgb(236, 72, 153)',
        borderWidth: 2,
    }],
}));
</script>

<template>
    <Head title="Statistiques" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Statistiques Complètes</h2>
        </template>

        <div class="py-6 sm:py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

                <!-- Section 1 : Statistiques générales -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">📊 Vue d'ensemble</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                        <!-- Total de livres -->
                        <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600">Total livres</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ stats.total }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Auteurs uniques -->
                        <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600">Auteurs</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ stats.uniqueAuthors }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Titres uniques -->
                        <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600">Titres uniques</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ stats.uniqueNames }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Lieux différents -->
                        <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-orange-500 rounded-md p-3">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600">Lieux</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ stats.uniqueLieux }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Doublons -->
                        <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600">Doublons</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ stats.duplicateBooks.length }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2 : Graphiques principaux -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">📈 Répartitions principales</h3>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Livres par lieu -->
                        <div class="bg-white overflow-hidden shadow-sm rounded-lg p-4 sm:p-6">
                            <PieChart
                                v-if="Object.keys(stats.booksByLocation).length > 0"
                                :data="locationChartData"
                                title="📍 Où sont les livres ?"
                            />
                            <p v-else class="text-center text-gray-500 py-8">Aucune donnée</p>
                        </div>

                        <!-- Livres par status -->
                        <div class="bg-white overflow-hidden shadow-sm rounded-lg p-4 sm:p-6">
                            <PieChart
                                v-if="Object.keys(stats.booksByStatus).length > 0"
                                :data="statusChartData"
                                title="📚 Répartition par status"
                            />
                            <p v-else class="text-center text-gray-500 py-8">Aucune donnée</p>
                        </div>
                    </div>
                </div>

                <!-- Section 3 : Pourcentages détaillés -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">💯 Pourcentages</h3>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Pourcentages par lieu -->
                        <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                            <h4 class="font-semibold text-gray-800 mb-4">Par lieu</h4>
                            <div class="space-y-3">
                                <div v-for="(percentage, location) in stats.locationPercentages" :key="location">
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="font-medium text-gray-700">{{ location }}</span>
                                        <span class="text-gray-600">{{ percentage }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div
                                            class="bg-blue-600 h-2 rounded-full transition-all duration-500"
                                            :style="{ width: percentage + '%' }"
                                        ></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pourcentages par status -->
                        <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                            <h4 class="font-semibold text-gray-800 mb-4">Par status</h4>
                            <div class="space-y-3">
                                <div v-for="(percentage, status) in stats.statusPercentages" :key="status">
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="font-medium text-gray-700">{{ status }}</span>
                                        <span class="text-gray-600">{{ percentage }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div
                                            class="bg-green-600 h-2 rounded-full transition-all duration-500"
                                            :style="{ width: percentage + '%' }"
                                        ></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 4 : Top auteurs -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">🏆 Top 10 Auteurs</h3>
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg p-4 sm:p-6">
                        <BarChart
                            v-if="stats.topAuthors.length > 0"
                            :data="authorsChartData"
                            title="Auteurs les plus présents"
                        />
                        <p v-else class="text-center text-gray-500 py-8">Aucune donnée</p>
                    </div>
                </div>

                <!-- Section 5 : Statistiques avancées -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">🔍 Analyses avancées</h3>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Diversité des auteurs par lieu -->
                        <div class="bg-white overflow-hidden shadow-sm rounded-lg p-4 sm:p-6">
                            <BarChart
                                v-if="Object.keys(stats.authorsByLocation).length > 0"
                                :data="authorsLocationChartData"
                                title="Diversité des auteurs par lieu"
                            />
                            <p v-else class="text-center text-gray-500 py-8">Aucune donnée</p>
                        </div>

                        <!-- Top combinaisons auteur + status -->
                        <div class="bg-white overflow-hidden shadow-sm rounded-lg p-4 sm:p-6">
                            <BarChart
                                v-if="stats.topAuthorStatus.length > 0"
                                :data="authorStatusChartData"
                                title="Top 5 - Auteur + Status"
                            />
                            <p v-else class="text-center text-gray-500 py-8">Aucune donnée</p>
                        </div>
                    </div>
                </div>

                <!-- Section 6 : Tableau détaillé des auteurs -->
                <div v-if="stats.authorsStats.length > 0">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">👤 Détails par auteur</h3>
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Auteur</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total livres</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status différents</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lieux différents</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="author in stats.authorsStats" :key="author.auteur">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ author.auteur }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ author.total_books }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ author.different_status }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ author.different_locations }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Section 7 : Doublons -->
                <div v-if="stats.duplicateBooks.length > 0">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">⚠️ Livres en doublon</h3>
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom du livre</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Occurrences</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="book in stats.duplicateBooks" :key="book.name">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ book.name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                {{ book.count }}x
                                            </span>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
