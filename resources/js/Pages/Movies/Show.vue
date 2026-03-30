<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({ movie: Object });
</script>

<template>
    <Head :title="movie.titre" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ movie.titre }}</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 space-y-4">
                        <div v-if="movie.barcode">
                            <div class="text-xs text-gray-500 uppercase font-medium">Code-barres</div>
                            <div class="text-gray-900 mt-1">{{ movie.barcode }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 uppercase font-medium">Titre</div>
                            <div class="text-gray-900 font-semibold mt-1">{{ movie.titre }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 uppercase font-medium">Réalisateur</div>
                            <div class="text-gray-900 mt-1">{{ movie.realisateur || '—' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 uppercase font-medium">Status</div>
                            <div class="text-gray-900 mt-1">{{ movie.status || '—' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 uppercase font-medium">Location</div>
                            <div class="text-gray-900 mt-1">{{ movie.location || '—' }}</div>
                        </div>

                        <div class="flex gap-3 pt-4 border-t border-gray-200">
                            <Link :href="route('movies.edit', movie.id)" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700">Modifier</Link>
                            <Link :href="route('movies.index')" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm hover:bg-gray-200">Retour</Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
