<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    book: Object,
});

const form = useForm({
    name: props.book.name,
    isbn: props.book.isbn ?? '',
    auteur: props.book.auteur,
    status: props.book.status,
    location: props.book.location,
});

const submit = () => {
    form.put(route('books.update', props.book.id));
};
</script>

<template>
    <Head title="Modifier le livre" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Modifier le livre</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-slate-200">
                    <div class="p-6 text-gray-900">
                        <form @submit.prevent="submit">
                            <div class="mb-4">
                                <InputLabel for="isbn" value="ISBN" />
                                <TextInput
                                    id="isbn"
                                    v-model="form.isbn"
                                    type="text"
                                    class="mt-1 block w-full"
                                    placeholder="Ex: 9782070360024"
                                />
                                <InputError :message="form.errors.isbn" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <InputLabel for="name" value="Nom du livre" />
                                <TextInput
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.name" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <InputLabel for="auteur" value="Auteur" />
                                <textarea
                                    id="auteur"
                                    v-model="form.auteur"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    rows="4"
                                    required
                                ></textarea>
                                <InputError :message="form.errors.auteur" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <InputLabel for="status" value="Status" />
                                <TextInput
                                    id="status"
                                    v-model="form.status"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.status" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <InputLabel for="location" value="Location" />
                                <TextInput
                                    id="location"
                                    v-model="form.location"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.location" class="mt-2" />
                            </div>

                            <div class="flex items-center gap-4">
                                <PrimaryButton :disabled="form.processing">Mettre à jour</PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
