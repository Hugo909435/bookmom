<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    book: Object,
});
</script>

<template>
    <Head title="Détails du livre" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Détails du livre</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="mb-4">
                            <strong>Nom du livre:</strong> {{ book.name }}
                        </div>
                        <div class="mb-4">
                            <strong>Auteur:</strong> {{ book.auteur }}
                        </div>
                        <div class="mb-4">
                            <strong>Status:</strong> {{ book.status }}
                        </div>
                        <div class="mb-4">
                            <strong>Livres:</strong> {{ book.livres }}
                        </div>

                        <Link
                            :href="route('books.index')"
                            class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
                        >
                            Retour à la liste
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
