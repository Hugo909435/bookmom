<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    movies: Array,
    pendingMovies: Array,
    totalMovies: Number,
    filteredCount: Number,
    filters: Object,
});

const search = ref(props.filters?.search || '');

let searchTimeout = null;
watch(search, (value) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('movies.index'), { search: value }, {
            preserveState: true,
            replace: true,
        });
    }, 300);
});

const deleteMovie = (id) => {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce film ?')) {
        useForm({}).delete(route('movies.destroy', id));
    }
};
</script>

<template>
    <Head title="Films" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Films</h2>
                    <p class="text-sm text-gray-600 mt-1">
                        <span v-if="search && filteredCount !== totalMovies">
                            {{ filteredCount }} résultat{{ filteredCount > 1 ? 's' : '' }} sur {{ totalMovies }} film{{ totalMovies > 1 ? 's' : '' }}
                        </span>
                        <span v-else>
                            {{ totalMovies }} film{{ totalMovies > 1 ? 's' : '' }} au total
                        </span>
                    </p>
                </div>
                <Link
                    :href="route('movies.create')"
                    class="inline-flex items-center px-3 py-2 sm:px-4 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700"
                >
                    <span class="hidden sm:inline">Ajouter un film</span>
                    <span class="sm:hidden">Ajouter</span>
                </Link>
            </div>
        </template>

        <div class="py-6 sm:py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Section En attente -->
                <div v-if="pendingMovies && pendingMovies.length > 0" class="mb-6 bg-orange-50 border border-orange-200 rounded-lg overflow-hidden">
                    <div class="px-4 py-3 bg-orange-100 border-b border-orange-200 flex items-center justify-between">
                        <h3 class="font-semibold text-orange-800">⏳ En attente de complétion ({{ pendingMovies.length }})</h3>
                        <span class="text-xs text-orange-600">Status ou localisation manquants</span>
                    </div>
                    <div class="divide-y divide-orange-100">
                        <div v-for="movie in pendingMovies" :key="movie.id" class="px-4 py-3 flex items-center justify-between gap-4">
                            <div class="min-w-0">
                                <div class="font-medium text-gray-900 truncate">{{ movie.titre }}</div>
                                <div class="text-sm text-gray-500 truncate">{{ movie.realisateur }}</div>
                            </div>
                            <Link
                                :href="route('movies.edit', movie.id)"
                                class="shrink-0 px-3 py-1.5 bg-orange-500 hover:bg-orange-600 text-white text-sm rounded-md font-medium"
                            >
                                Compléter
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Barre de recherche -->
                <div class="mb-6">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Rechercher un film, réalisateur..."
                            class="block w-full pl-10 pr-3 py-2 sm:py-3 border border-gray-300 rounded-lg bg-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        />
                        <button v-if="search" @click="search = ''" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <svg class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Vue Mobile : Cards -->
                <div class="md:hidden space-y-4">
                    <div v-for="movie in movies" :key="movie.id" class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                        <div class="space-y-3">
                            <div>
                                <div class="text-xs text-gray-500 uppercase font-medium">Titre</div>
                                <div class="text-base font-semibold text-gray-900 mt-1">{{ movie.titre }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500 uppercase font-medium">Réalisateur</div>
                                <div class="text-sm text-gray-700 mt-1">{{ movie.realisateur || '—' }}</div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <div class="text-xs text-gray-500 uppercase font-medium">Status</div>
                                    <div class="text-sm text-gray-700 mt-1">{{ movie.status }}</div>
                                </div>
                                <div>
                                    <div class="text-xs text-gray-500 uppercase font-medium">Location</div>
                                    <div class="text-sm text-gray-700 mt-1">{{ movie.location }}</div>
                                </div>
                            </div>
                            <div class="flex gap-2 pt-2 border-t border-gray-200">
                                <Link :href="route('movies.show', movie.id)" class="flex-1 text-center px-3 py-2 bg-blue-50 text-blue-600 rounded-md text-sm font-medium hover:bg-blue-100">Voir</Link>
                                <Link :href="route('movies.edit', movie.id)" class="flex-1 text-center px-3 py-2 bg-indigo-50 text-indigo-600 rounded-md text-sm font-medium hover:bg-indigo-100">Modifier</Link>
                                <button @click="deleteMovie(movie.id)" class="flex-1 px-3 py-2 bg-red-50 text-red-600 rounded-md text-sm font-medium hover:bg-red-100">Supprimer</button>
                            </div>
                        </div>
                    </div>
                    <div v-if="movies.length === 0" class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
                        <p class="text-gray-500">{{ search ? 'Aucun résultat trouvé' : 'Aucun film disponible' }}</p>
                    </div>
                </div>

                <!-- Vue Desktop : Table -->
                <div class="hidden md:block bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/3">Titre</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Réalisateur</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="movie in movies" :key="movie.id">
                                    <td class="px-4 py-4 max-w-xs">
                                        <div class="font-medium text-gray-900 truncate" :title="movie.titre">{{ movie.titre }}</div>
                                    </td>
                                    <td class="px-4 py-4 max-w-xs">
                                        <div class="text-gray-700 truncate" :title="movie.realisateur">{{ movie.realisateur || '—' }}</div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-gray-700 text-sm">{{ movie.status }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap text-gray-700 text-sm">{{ movie.location }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center gap-2">
                                            <Link :href="route('movies.show', movie.id)" class="px-2 py-1 bg-blue-50 text-blue-600 rounded hover:bg-blue-100 text-xs">Voir</Link>
                                            <Link :href="route('movies.edit', movie.id)" class="px-2 py-1 bg-indigo-50 text-indigo-600 rounded hover:bg-indigo-100 text-xs">Modifier</Link>
                                            <button @click="deleteMovie(movie.id)" class="px-2 py-1 bg-red-50 text-red-600 rounded hover:bg-red-100 text-xs">Supprimer</button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="movies.length === 0">
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                        {{ search ? 'Aucun résultat trouvé' : 'Aucun film disponible' }}
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
