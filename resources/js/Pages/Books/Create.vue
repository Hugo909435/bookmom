<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { ref, onUnmounted } from 'vue';
import { BrowserMultiFormatReader } from '@zxing/browser';

const quickMode = ref(false);
const addedBooks = ref([]);
const scanning = ref(false);
const scanError = ref('');
const fetchError = ref('');
const fetching = ref(false);
const videoRef = ref(null);
let codeReader = null;
let controls = null;
let lastScanned = '';

const form = useForm({
    name: '',
    isbn: '',
    auteur: '',
    status: '',
    location: '',
});

const quickForm = useForm({
    name: '',
    isbn: '',
    auteur: '',
    status: null,
    location: null,
});

const submit = () => {
    form.post(route('books.store'));
};

const submitQuick = () => {
    quickForm.post(route('books.store'), {
        onSuccess: () => {
            addedBooks.value.unshift({ name: quickForm.name, auteur: quickForm.auteur, isbn: quickForm.isbn });
            quickForm.reset();
            scanError.value = '';
            fetchError.value = '';
            // Relancer le scan automatiquement
            startScan();
        },
    });
};

const startScan = async () => {
    scanError.value = '';
    scanning.value = true;
    lastScanned = '';

    await new Promise(resolve => setTimeout(resolve, 100));

    try {
        codeReader = new BrowserMultiFormatReader();
        const devices = await BrowserMultiFormatReader.listVideoInputDevices();

        if (devices.length === 0) {
            scanError.value = 'Aucune caméra détectée.';
            scanning.value = false;
            return;
        }

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
                    if (isbn === lastScanned) return;
                    lastScanned = isbn;
                    if (quickMode.value) {
                        quickForm.isbn = isbn;
                    } else {
                        form.isbn = isbn;
                    }
                    stopScan();
                    fetchBookData(isbn, quickMode.value);
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

const fetchBookData = async (isbn, isQuick = false) => {
    fetching.value = true;
    fetchError.value = '';

    try {
        const response = await fetch(`https://openlibrary.org/api/books?bibkeys=ISBN:${isbn}&format=json&jscmd=data`);
        const data = await response.json();
        const key = `ISBN:${isbn}`;

        if (data[key]) {
            const book = data[key];
            const title = book.title || '';
            const auteur = book.authors ? book.authors.map(a => a.name).join(', ') : '';
            if (isQuick) {
                quickForm.name = title;
                quickForm.auteur = auteur;
            } else {
                form.name = title;
                form.auteur = auteur;
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

const searchByIsbn = (isQuick = false) => {
    const isbn = isQuick ? quickForm.isbn : form.isbn;
    if (isbn) fetchBookData(isbn, isQuick);
};

onUnmounted(() => stopScan());
</script>

<template>
    <Head title="Ajouter un livre" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Ajouter un livre</h2>
                <button
                    @click="quickMode = !quickMode; addedBooks = []; stopScan();"
                    :class="quickMode ? 'bg-orange-500 hover:bg-orange-600' : 'bg-gray-600 hover:bg-gray-700'"
                    class="px-4 py-2 text-white text-sm rounded-md font-medium"
                >
                    {{ quickMode ? '⚡ Mode scan rapide (actif)' : '⚡ Mode scan rapide' }}
                </button>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <!-- MODE SCAN RAPIDE -->
                <div v-if="quickMode">
                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-6">
                        <p class="text-orange-800 text-sm font-medium">⚡ Mode scan rapide — Scannez vos livres à la suite. Status et localisation à compléter plus tard depuis l'accueil.</p>
                    </div>

                    <!-- Caméra -->
                    <div v-if="scanning" class="mb-4">
                        <video ref="videoRef" class="w-full rounded-lg border-2 border-orange-400" style="max-height: 280px;" autoplay muted playsinline></video>
                        <button type="button" @click="stopScan" class="mt-2 w-full py-2 px-4 bg-red-500 text-white rounded-md text-sm">
                            Annuler le scan
                        </button>
                    </div>

                    <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                        <!-- ISBN -->
                        <div class="mb-4">
                            <InputLabel for="quick-isbn" value="ISBN" />
                            <div class="flex gap-2 mt-1">
                                <TextInput id="quick-isbn" v-model="quickForm.isbn" type="text" class="block w-full" placeholder="Ex: 9782070360024" />
                                <button type="button" @click="startScan" :disabled="scanning" class="px-3 py-2 bg-orange-500 text-white rounded-md text-sm whitespace-nowrap">
                                    📷 Scanner
                                </button>
                                <button type="button" @click="searchByIsbn(true)" :disabled="fetching || !quickForm.isbn" class="px-3 py-2 bg-gray-600 text-white rounded-md text-sm whitespace-nowrap">
                                    {{ fetching ? '...' : 'Rechercher' }}
                                </button>
                            </div>
                            <p v-if="scanError" class="mt-1 text-sm text-red-600">{{ scanError }}</p>
                            <p v-if="fetchError" class="mt-1 text-sm text-orange-600">{{ fetchError }}</p>
                            <p v-if="fetching" class="mt-1 text-sm text-indigo-600">Recherche en cours...</p>
                        </div>

                        <!-- Titre -->
                        <div class="mb-4">
                            <InputLabel for="quick-name" value="Nom du livre" />
                            <TextInput id="quick-name" v-model="quickForm.name" type="text" class="mt-1 block w-full" required />
                            <InputError :message="quickForm.errors.name" class="mt-2" />
                        </div>

                        <!-- Auteur -->
                        <div class="mb-4">
                            <InputLabel for="quick-auteur" value="Auteur" />
                            <TextInput id="quick-auteur" v-model="quickForm.auteur" type="text" class="mt-1 block w-full" required />
                            <InputError :message="quickForm.errors.auteur" class="mt-2" />
                        </div>

                        <button
                            @click="submitQuick"
                            :disabled="quickForm.processing || !quickForm.name || !quickForm.auteur"
                            class="w-full py-3 bg-orange-500 hover:bg-orange-600 disabled:opacity-50 text-white rounded-md font-semibold"
                        >
                            ✅ Ajouter ce livre → Scanner le suivant
                        </button>
                    </div>

                    <!-- Liste des livres ajoutés -->
                    <div v-if="addedBooks.length > 0" class="bg-white shadow-sm rounded-lg p-4">
                        <h3 class="font-medium text-gray-700 mb-3">Ajoutés cette session ({{ addedBooks.length }})</h3>
                        <ul class="space-y-2">
                            <li v-for="(b, i) in addedBooks" :key="i" class="flex items-center gap-2 text-sm text-gray-600">
                                <span class="text-green-500">✓</span>
                                <span class="font-medium">{{ b.name }}</span>
                                <span v-if="b.auteur" class="text-gray-400">— {{ b.auteur }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- MODE NORMAL -->
                <div v-else class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">

                        <!-- Caméra -->
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
                                    <TextInput id="isbn" v-model="form.isbn" type="text" class="block w-full" placeholder="Ex: 9782070360024" />
                                    <button type="button" @click="startScan" :disabled="scanning" class="px-3 py-2 bg-indigo-600 text-white rounded-md flex items-center gap-1 whitespace-nowrap">
                                        📷 Scanner
                                    </button>
                                    <button type="button" @click="searchByIsbn(false)" :disabled="fetching || !form.isbn" class="px-3 py-2 bg-gray-600 text-white rounded-md whitespace-nowrap">
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
                                <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" required />
                                <InputError :message="form.errors.name" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <InputLabel for="auteur" value="Auteur" />
                                <textarea id="auteur" v-model="form.auteur" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="4" required></textarea>
                                <InputError :message="form.errors.auteur" class="mt-2" />
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
