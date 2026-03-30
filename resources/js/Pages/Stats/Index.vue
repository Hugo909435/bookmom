<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import BarChart from '@/Components/Charts/BarChart.vue';
import PieChart from '@/Components/Charts/PieChart.vue';
import { computed, ref } from 'vue';

const props = defineProps({
    stats: Object,
    movieStats: Object,
});

const activeTab = ref('livres');

// ---- Livres ----
const statusChartData = computed(() => ({
    labels: Object.keys(props.stats.booksByStatus),
    datasets: [{ label: 'Nombre de livres', data: Object.values(props.stats.booksByStatus), backgroundColor: ['rgba(59,130,246,0.8)','rgba(16,185,129,0.8)','rgba(251,146,60,0.8)','rgba(239,68,68,0.8)','rgba(168,85,247,0.8)','rgba(236,72,153,0.8)'], borderColor: ['rgb(59,130,246)','rgb(16,185,129)','rgb(251,146,60)','rgb(239,68,68)','rgb(168,85,247)','rgb(236,72,153)'], borderWidth: 2 }],
}));

const locationChartData = computed(() => ({
    labels: Object.keys(props.stats.booksByLocation),
    datasets: [{ label: 'Nombre de livres', data: Object.values(props.stats.booksByLocation), backgroundColor: ['rgba(239,68,68,0.8)','rgba(251,146,60,0.8)','rgba(234,179,8,0.8)','rgba(16,185,129,0.8)','rgba(59,130,246,0.8)','rgba(99,102,241,0.8)','rgba(168,85,247,0.8)','rgba(236,72,153,0.8)'], borderColor: ['rgb(239,68,68)','rgb(251,146,60)','rgb(234,179,8)','rgb(16,185,129)','rgb(59,130,246)','rgb(99,102,241)','rgb(168,85,247)','rgb(236,72,153)'], borderWidth: 2 }],
}));

const authorsChartData = computed(() => ({
    labels: props.stats.topAuthors.map(a => a.auteur),
    datasets: [{ label: 'Nombre de livres', data: props.stats.topAuthors.map(a => a.count), backgroundColor: 'rgba(99,102,241,0.8)', borderColor: 'rgb(99,102,241)', borderWidth: 2 }],
}));

const authorsLocationChartData = computed(() => ({
    labels: Object.keys(props.stats.authorsByLocation),
    datasets: [{ label: 'Nombre d\'auteurs différents', data: Object.values(props.stats.authorsByLocation), backgroundColor: 'rgba(16,185,129,0.8)', borderColor: 'rgb(16,185,129)', borderWidth: 2 }],
}));

const authorStatusChartData = computed(() => ({
    labels: props.stats.topAuthorStatus.map(item => item.label),
    datasets: [{ label: 'Nombre de livres', data: props.stats.topAuthorStatus.map(item => item.count), backgroundColor: 'rgba(236,72,153,0.8)', borderColor: 'rgb(236,72,153)', borderWidth: 2 }],
}));

// ---- Films ----
const movieStatusChartData = computed(() => ({
    labels: Object.keys(props.movieStats.moviesByStatus),
    datasets: [{ label: 'Nombre de films', data: Object.values(props.movieStats.moviesByStatus), backgroundColor: ['rgba(59,130,246,0.8)','rgba(16,185,129,0.8)','rgba(251,146,60,0.8)','rgba(239,68,68,0.8)','rgba(168,85,247,0.8)','rgba(236,72,153,0.8)'], borderColor: ['rgb(59,130,246)','rgb(16,185,129)','rgb(251,146,60)','rgb(239,68,68)','rgb(168,85,247)','rgb(236,72,153)'], borderWidth: 2 }],
}));

const movieLocationChartData = computed(() => ({
    labels: Object.keys(props.movieStats.moviesByLocation),
    datasets: [{ label: 'Nombre de films', data: Object.values(props.movieStats.moviesByLocation), backgroundColor: ['rgba(239,68,68,0.8)','rgba(251,146,60,0.8)','rgba(234,179,8,0.8)','rgba(16,185,129,0.8)','rgba(59,130,246,0.8)','rgba(99,102,241,0.8)','rgba(168,85,247,0.8)','rgba(236,72,153,0.8)'], borderColor: ['rgb(239,68,68)','rgb(251,146,60)','rgb(234,179,8)','rgb(16,185,129)','rgb(59,130,246)','rgb(99,102,241)','rgb(168,85,247)','rgb(236,72,153)'], borderWidth: 2 }],
}));

const realisateursChartData = computed(() => ({
    labels: props.movieStats.topRealisateurs.map(r => r.realisateur),
    datasets: [{ label: 'Nombre de films', data: props.movieStats.topRealisateurs.map(r => r.count), backgroundColor: 'rgba(251,146,60,0.8)', borderColor: 'rgb(251,146,60)', borderWidth: 2 }],
}));
</script>

<template>
    <Head title="Statistiques" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Statistiques</h2>
        </template>

        <div class="py-6 sm:py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

                <!-- Onglets -->
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8">
                        <button
                            @click="activeTab = 'livres'"
                            :class="activeTab === 'livres'
                                ? 'border-indigo-500 text-indigo-600'
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap"
                        >
                            📚 Livres
                            <span class="ml-2 py-0.5 px-2 rounded-full text-xs bg-gray-100 text-gray-600">{{ stats.total }}</span>
                        </button>
                        <button
                            @click="activeTab = 'films'"
                            :class="activeTab === 'films'
                                ? 'border-indigo-500 text-indigo-600'
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap"
                        >
                            🎬 Films
                            <span class="ml-2 py-0.5 px-2 rounded-full text-xs bg-gray-100 text-gray-600">{{ movieStats.total }}</span>
                        </button>
                    </nav>
                </div>

                <!-- ===== ONGLET LIVRES ===== -->
                <div v-if="activeTab === 'livres'" class="space-y-6">

                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">📊 Vue d'ensemble</h3>
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                            <div class="bg-white shadow-sm rounded-lg p-6 flex items-center gap-4">
                                <div class="bg-blue-500 rounded-md p-3"><svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg></div>
                                <div><p class="text-sm text-gray-600">Total</p><p class="text-2xl font-bold text-gray-900">{{ stats.total }}</p></div>
                            </div>
                            <div class="bg-white shadow-sm rounded-lg p-6 flex items-center gap-4">
                                <div class="bg-green-500 rounded-md p-3"><svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></div>
                                <div><p class="text-sm text-gray-600">Auteurs</p><p class="text-2xl font-bold text-gray-900">{{ stats.uniqueAuthors }}</p></div>
                            </div>
                            <div class="bg-white shadow-sm rounded-lg p-6 flex items-center gap-4">
                                <div class="bg-purple-500 rounded-md p-3"><svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg></div>
                                <div><p class="text-sm text-gray-600">Titres uniques</p><p class="text-2xl font-bold text-gray-900">{{ stats.uniqueNames }}</p></div>
                            </div>
                            <div class="bg-white shadow-sm rounded-lg p-6 flex items-center gap-4">
                                <div class="bg-orange-500 rounded-md p-3"><svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
                                <div><p class="text-sm text-gray-600">Lieux</p><p class="text-2xl font-bold text-gray-900">{{ stats.uniqueLieux }}</p></div>
                            </div>
                            <div class="bg-white shadow-sm rounded-lg p-6 flex items-center gap-4">
                                <div class="bg-yellow-500 rounded-md p-3"><svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg></div>
                                <div><p class="text-sm text-gray-600">Doublons</p><p class="text-2xl font-bold text-gray-900">{{ stats.duplicateBooks.length }}</p></div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">📈 Répartitions</h3>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="bg-white shadow-sm rounded-lg p-4 sm:p-6">
                                <PieChart v-if="Object.keys(stats.booksByLocation).length > 0" :data="locationChartData" title="📍 Où sont les livres ?" />
                                <p v-else class="text-center text-gray-500 py-8">Aucune donnée</p>
                            </div>
                            <div class="bg-white shadow-sm rounded-lg p-4 sm:p-6">
                                <PieChart v-if="Object.keys(stats.booksByStatus).length > 0" :data="statusChartData" title="📚 Répartition par status" />
                                <p v-else class="text-center text-gray-500 py-8">Aucune donnée</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">💯 Pourcentages</h3>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="bg-white shadow-sm rounded-lg p-6">
                                <h4 class="font-semibold text-gray-800 mb-4">Par lieu</h4>
                                <div class="space-y-3">
                                    <div v-for="(percentage, location) in stats.locationPercentages" :key="location">
                                        <div class="flex justify-between text-sm mb-1"><span class="font-medium text-gray-700">{{ location }}</span><span class="text-gray-600">{{ percentage }}%</span></div>
                                        <div class="w-full bg-gray-200 rounded-full h-2"><div class="bg-blue-600 h-2 rounded-full" :style="{ width: percentage + '%' }"></div></div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white shadow-sm rounded-lg p-6">
                                <h4 class="font-semibold text-gray-800 mb-4">Par status</h4>
                                <div class="space-y-3">
                                    <div v-for="(percentage, status) in stats.statusPercentages" :key="status">
                                        <div class="flex justify-between text-sm mb-1"><span class="font-medium text-gray-700">{{ status }}</span><span class="text-gray-600">{{ percentage }}%</span></div>
                                        <div class="w-full bg-gray-200 rounded-full h-2"><div class="bg-green-600 h-2 rounded-full" :style="{ width: percentage + '%' }"></div></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">🏆 Top 10 Auteurs</h3>
                        <div class="bg-white shadow-sm rounded-lg p-4 sm:p-6">
                            <BarChart v-if="stats.topAuthors.length > 0" :data="authorsChartData" title="Auteurs les plus présents" />
                            <p v-else class="text-center text-gray-500 py-8">Aucune donnée</p>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">🔍 Analyses avancées</h3>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="bg-white shadow-sm rounded-lg p-4 sm:p-6">
                                <BarChart v-if="Object.keys(stats.authorsByLocation).length > 0" :data="authorsLocationChartData" title="Diversité des auteurs par lieu" />
                                <p v-else class="text-center text-gray-500 py-8">Aucune donnée</p>
                            </div>
                            <div class="bg-white shadow-sm rounded-lg p-4 sm:p-6">
                                <BarChart v-if="stats.topAuthorStatus.length > 0" :data="authorStatusChartData" title="Top 5 - Auteur + Status" />
                                <p v-else class="text-center text-gray-500 py-8">Aucune donnée</p>
                            </div>
                        </div>
                    </div>

                    <div v-if="stats.authorsStats.length > 0">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">👤 Détails par auteur</h3>
                        <div class="bg-white shadow-sm rounded-lg overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50"><tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Auteur</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status diff.</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lieux diff.</th>
                                </tr></thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="author in stats.authorsStats" :key="author.auteur">
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ author.auteur }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-700">{{ author.total_books }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-700">{{ author.different_status }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-700">{{ author.different_locations }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div v-if="stats.duplicateBooks.length > 0">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">⚠️ Livres en doublon</h3>
                        <div class="bg-white shadow-sm rounded-lg overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50"><tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Occurrences</th>
                                </tr></thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="book in stats.duplicateBooks" :key="book.name">
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ book.name }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-700"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">{{ book.count }}x</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

                <!-- ===== ONGLET FILMS ===== -->
                <div v-if="activeTab === 'films'" class="space-y-6">

                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">📊 Vue d'ensemble</h3>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            <div class="bg-white shadow-sm rounded-lg p-6 flex items-center gap-4">
                                <div class="bg-blue-500 rounded-md p-3"><span class="text-white text-xl">🎬</span></div>
                                <div><p class="text-sm text-gray-600">Total films</p><p class="text-2xl font-bold text-gray-900">{{ movieStats.total }}</p></div>
                            </div>
                            <div class="bg-white shadow-sm rounded-lg p-6 flex items-center gap-4">
                                <div class="bg-green-500 rounded-md p-3"><span class="text-white text-xl">🎥</span></div>
                                <div><p class="text-sm text-gray-600">Réalisateurs</p><p class="text-2xl font-bold text-gray-900">{{ movieStats.uniqueRealisateurs }}</p></div>
                            </div>
                            <div class="bg-white shadow-sm rounded-lg p-6 flex items-center gap-4">
                                <div class="bg-orange-500 rounded-md p-3"><span class="text-white text-xl">📍</span></div>
                                <div><p class="text-sm text-gray-600">Lieux</p><p class="text-2xl font-bold text-gray-900">{{ movieStats.uniqueLieux }}</p></div>
                            </div>
                            <div class="bg-white shadow-sm rounded-lg p-6 flex items-center gap-4">
                                <div class="bg-yellow-500 rounded-md p-3"><span class="text-white text-xl">⚠️</span></div>
                                <div><p class="text-sm text-gray-600">Doublons</p><p class="text-2xl font-bold text-gray-900">{{ movieStats.duplicateMovies.length }}</p></div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">📈 Répartitions</h3>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="bg-white shadow-sm rounded-lg p-4 sm:p-6">
                                <PieChart v-if="Object.keys(movieStats.moviesByLocation).length > 0" :data="movieLocationChartData" title="📍 Où sont les films ?" />
                                <p v-else class="text-center text-gray-500 py-8">Aucune donnée</p>
                            </div>
                            <div class="bg-white shadow-sm rounded-lg p-4 sm:p-6">
                                <PieChart v-if="Object.keys(movieStats.moviesByStatus).length > 0" :data="movieStatusChartData" title="🎬 Répartition par status" />
                                <p v-else class="text-center text-gray-500 py-8">Aucune donnée</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">💯 Pourcentages</h3>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="bg-white shadow-sm rounded-lg p-6">
                                <h4 class="font-semibold text-gray-800 mb-4">Par lieu</h4>
                                <div class="space-y-3">
                                    <div v-for="(percentage, location) in movieStats.locationPercentages" :key="location">
                                        <div class="flex justify-between text-sm mb-1"><span class="font-medium text-gray-700">{{ location }}</span><span class="text-gray-600">{{ percentage }}%</span></div>
                                        <div class="w-full bg-gray-200 rounded-full h-2"><div class="bg-blue-600 h-2 rounded-full" :style="{ width: percentage + '%' }"></div></div>
                                    </div>
                                    <p v-if="Object.keys(movieStats.locationPercentages).length === 0" class="text-gray-500 text-sm">Aucune donnée</p>
                                </div>
                            </div>
                            <div class="bg-white shadow-sm rounded-lg p-6">
                                <h4 class="font-semibold text-gray-800 mb-4">Par status</h4>
                                <div class="space-y-3">
                                    <div v-for="(percentage, status) in movieStats.statusPercentages" :key="status">
                                        <div class="flex justify-between text-sm mb-1"><span class="font-medium text-gray-700">{{ status }}</span><span class="text-gray-600">{{ percentage }}%</span></div>
                                        <div class="w-full bg-gray-200 rounded-full h-2"><div class="bg-orange-500 h-2 rounded-full" :style="{ width: percentage + '%' }"></div></div>
                                    </div>
                                    <p v-if="Object.keys(movieStats.statusPercentages).length === 0" class="text-gray-500 text-sm">Aucune donnée</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="movieStats.topRealisateurs.length > 0">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">🏆 Top Réalisateurs</h3>
                        <div class="bg-white shadow-sm rounded-lg p-4 sm:p-6">
                            <BarChart :data="realisateursChartData" title="Réalisateurs les plus présents" />
                        </div>
                    </div>

                    <div v-if="movieStats.duplicateMovies.length > 0">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">⚠️ Films en doublon</h3>
                        <div class="bg-white shadow-sm rounded-lg overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50"><tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Titre</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Occurrences</th>
                                </tr></thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="movie in movieStats.duplicateMovies" :key="movie.titre">
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ movie.titre }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-700"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">{{ movie.count }}x</span></td>
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
