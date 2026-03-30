<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({ movie: Object });

const form = useForm({
    titre: props.movie.titre,
    barcode: props.movie.barcode ?? '',
    realisateur: props.movie.realisateur ?? '',
    status: props.movie.status ?? '',
    location: props.movie.location ?? '',
});

const submit = () => form.put(route('movies.update', props.movie.id));
</script>

<template>
    <Head title="Modifier le film" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Modifier le film</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form @submit.prevent="submit">
                            <div class="mb-4">
                                <InputLabel for="barcode" value="Code-barres" />
                                <TextInput id="barcode" v-model="form.barcode" type="text" class="mt-1 block w-full" />
                                <InputError :message="form.errors.barcode" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <InputLabel for="titre" value="Titre" />
                                <TextInput id="titre" v-model="form.titre" type="text" class="mt-1 block w-full" required />
                                <InputError :message="form.errors.titre" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <InputLabel for="realisateur" value="Réalisateur" />
                                <TextInput id="realisateur" v-model="form.realisateur" type="text" class="mt-1 block w-full" />
                                <InputError :message="form.errors.realisateur" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <InputLabel for="status" value="Status" />
                                <TextInput id="status" v-model="form.status" type="text" class="mt-1 block w-full" />
                                <InputError :message="form.errors.status" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <InputLabel for="location" value="Location" />
                                <TextInput id="location" v-model="form.location" type="text" class="mt-1 block w-full" />
                                <InputError :message="form.errors.location" class="mt-2" />
                            </div>

                            <PrimaryButton :disabled="form.processing">Mettre à jour</PrimaryButton>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
