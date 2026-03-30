<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { ref, onUnmounted } from 'vue';
import { BrowserMultiFormatReader } from '@zxing/browser';

const form = useForm({
    name: '',
    isbn: '',
    auteur: '',
    status: '',
    location: '',
});

const submit = () => {
    form.post(route('books.store'));
};

// Scanner
const scanning = ref(false);
const scanError = ref('');
const fetchError = ref('');
const fetching = ref(false);
const videoRef = ref(null);
let codeReader = null;
let controls = null;

const startScan = async () => {
    scanError.value = '';
    scanning.value = true;

    await new Promise(resolve => setTimeout(resolve, 100));

    try {
        codeReader = new BrowserMultiFormatReader();
        const devices = await BrowserMultiFormatReader.listVideoInputDevices();

        if (devices.length === 0) {
            scanError.value = 'Aucune caméra détectée.';
            scanning.value = false;
            return;
        }

        // Préférer la caméra arrière sur mobile
        const backCamera = devices.find(d =>
            d.label.toLowerCase().includes('back') ||
            d.label.toLowerCase().includes('rear') ||
            d.label.toLowerCase().includes('environment')
        ) || devices[devices.length - 1];

        controls = await codeReader.decodeFromVideoDevice(
            backCamera.deviceId,
            videoRef.value,
            (result, error) => {
                if (result) {
                    const isbn = result.getText();
                    form.isbn = isbn;
                    stopScan();
                    fetchBookData(isbn);
                }
            }
        );
    } catch (e) {
        scanError.value = 'Impossible d\'accéder à la caméra. Vérifiez les permissions.';
        scanning.value = false;
    }
};

const stopScan = () => {
    if (controls) {
        controls.stop();
        controls = null;
    }
    scanning.value = false;
};

const fetchBookData = async (isbn) => {
    fetching.value = true;
    fetchError.value = '';

    try {
        const response = await fetch(`https://openlibrary.org/api/books?bibkeys=ISBN:${isbn}&format=json&jscmd=data`);
        const data = await response.json();
        const key = `ISBN:${isbn}`;

        if (data[key]) {
            const book = data[key];
            form.name = book.title || '';
            if (book.authors && book.authors.length > 0) {
                form.auteur = book.authors.map(a => a.name).join(', ');
            }
        } else {
            fetchError.value = 'Livre non trouvé. Remplissez les champs manuellement.';
        }
    } catch (e) {
        fetchError.value = 'Erreur lors de la recherche. Remplissez les champs manuellement.';
    } finally {
        fetching.value = false;
    }
};

const searchByIsbn = () => {
    if (form.isbn) {
        fetchBookData(form.isbn);
    }
};

onUnmounted(() => {
    stopScan();
});
</script>

<template>
    <Head title="Ajouter un livre" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Ajouter un livre</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">

                        <!-- Scanner caméra -->
                        <div v-if="scanning" class="mb-6">
                            <video ref="videoRef" class="w-full rounded-lg border-2 border-indigo-500" style="max-height: 300px;" autoplay muted playsinline></video>
                            <button type="button" @click="stopScan" class="mt-2 w-full py-2 px-4 bg-red-500 text-white rounded-md">
                                Annuler le scan
                            </button>
                        </div>

                        <form @submit.prevent="submit">
                            <!-- ISBN -->
                            <div class="mb-4">
                                <InputLabel for="isbn" value="ISBN (code-barres)" />
                                <div class="flex gap-2 mt-1">
                                    <TextInput
                                        id="isbn"
                                        v-model="form.isbn"
                                        type="text"
                                        class="block w-full"
                                        placeholder="Ex: 9782070360024"
                                    />
                                    <button
                                        type="button"
                                        @click="startScan"
                                        :disabled="scanning"
                                        class="px-3 py-2 bg-indigo-600 text-white rounded-md flex items-center gap-1 whitespace-nowrap"
                                    >
                                        📷 Scanner
                                    </button>
                                    <button
                                        type="button"
                                        @click="searchByIsbn"
                                        :disabled="fetching || !form.isbn"
                                        class="px-3 py-2 bg-gray-600 text-white rounded-md whitespace-nowrap"
                                    >
                                        {{ fetching ? '...' : 'Rechercher' }}
                                    </button>
                                </div>
                                <p v-if="scanError" class="mt-1 text-sm text-red-600">{{ scanError }}</p>
                                <p v-if="fetchError" class="mt-1 text-sm text-orange-600">{{ fetchError }}</p>
                                <p v-if="fetching" class="mt-1 text-sm text-indigo-600">Recherche du livre en cours...</p>
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
                                <PrimaryButton :disabled="form.processing">Enregistrer</PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
