<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({ book: Object });
</script>

<template>
    <Head title="Détails du livre" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <Link :href="route('books.index')" class="text-slate-400 hover:text-blue-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </Link>
                <h2 class="font-semibold text-xl text-slate-800">Détails du livre</h2>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-6 py-4 bg-blue-600">
                        <h3 class="text-white font-semibold text-lg">{{ book.name }}</h3>
                        <p class="text-blue-100 text-sm mt-0.5">{{ book.auteur }}</p>
                    </div>
                    <div class="p-6 space-y-4">
                        <div v-if="book.isbn" class="flex items-center justify-between py-3 border-b border-slate-100">
                            <span class="text-sm font-medium text-slate-500">ISBN</span>
                            <span class="text-sm text-slate-800 font-mono">{{ book.isbn }}</span>
                        </div>
                        <div class="flex items-center justify-between py-3 border-b border-slate-100">
                            <span class="text-sm font-medium text-slate-500">Status</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">{{ book.status || '—' }}</span>
                        </div>
                        <div class="flex items-center justify-between py-3">
                            <span class="text-sm font-medium text-slate-500">Localisation</span>
                            <span class="text-sm text-slate-800">{{ book.location || '—' }}</span>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex gap-3">
                        <Link :href="route('books.edit', book.id)" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">Modifier</Link>
                        <Link :href="route('books.index')" class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-medium rounded-lg transition-colors">Retour</Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
