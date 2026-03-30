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
const addedMovies = ref([]);
const scanning = ref(false);
const scanError = ref('');
const fetchError = ref('');
const fetching = ref(false);
const videoRef = ref(null);
let codeReader = null;
let controls = null;
let lastScanned = '';

const form = useForm({ titre: '', barcode: '', realisateur: '', status: '', location: '' });
const quickForm = useForm({ titre: '', barcode: '', realisateur: '', status: null, location: null });

const submit = () => form.post(route('movies.store'));

const submitQuick = () => {
    quickForm.post(route('movies.store'), {
        onSuccess: () => {
            addedMovies.value.unshift({ titre: quickForm.titre, realisateur: quickForm.realisateur });
            quickForm.reset();
            scanError.value = '';
            fetchError.value = '';
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
            (result) => {
                if (result) {
                    const barcode = result.getText();
                    if (barcode === lastScanned) return;
                    lastScanned = barcode;
                    if (quickMode.value) {
                        quickForm.barcode = barcode;
                    } else {
                        form.barcode = barcode;
                    }
                    stopScan();
                    fetchMovieData(barcode, quickMode.value);
                }
            }
        );
    } catch (e) {
        scanError.value = 'Impossible d\'accéder à la caméra. Vérifiez les permissions.';
        scanning.value = false;
    }
};

const stopScan = () => {
    if (controls) { controls.stop(); controls = null; }
    scanning.value = false;
};

const fetchMovieData = async (barcode, isQuick = false) => {
    fetching.value = true;
    fetchError.value = '';

    try {
        const response = await fetch(`https://api.upcitemdb.com/prod/trial/lookup?upc=${barcode}`);
        const data = await response.json();

        if (data.items && data.items.length > 0) {
            const item = data.items[0];
            const titre = item.title || '';
            if (isQuick) {
                quickForm.titre = titre;
            } else {
                form.titre = titre;
            }
        } else {
            fetchError.value = 'Film non trouvé. Remplissez les champs manuellement.';
        }
    } catch (e) {
        fetchError.value = 'Erreur lors de la recherche. Remplissez les champs manuellement.';
    } finally {
        fetching.value = false;
    }
};

const searchByBarcode = (isQuick = false) => {
    const barcode = isQuick ? quickForm.barcode : form.barcode;
    if (barcode) fetchMovieData(barcode, isQuick);
};

onUnmounted(() => stopScan());
</script>

<template>
    <Head title="Ajouter un film" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Ajouter un film</h2>
                <button
                    @click="quickMode = !quickMode; addedMovies = []; stopScan();"
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
                        <p class="text-orange-800 text-sm font-medium">⚡ Mode scan rapide — Scannez vos films à la suite. Status et localisation à compléter plus tard.</p>
                    </div>

                    <div v-if="scanning" class="mb-4">
                        <video ref="videoRef" class="w-full rounded-lg border-2 border-orange-400" style="max-height: 280px;" autoplay muted playsinline></video>
                        <button type="button" @click="stopScan" class="mt-2 w-full py-2 px-4 bg-red-500 text-white rounded-md text-sm">Annuler le scan</button>
                    </div>

                    <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                        <div class="mb-4">
                            <InputLabel for="quick-barcode" value="Code-barres" />
                            <div class="flex gap-2 mt-1">
                                <TextInput id="quick-barcode" v-model="quickForm.barcode" type="text" class="block w-full" placeholder="Ex: 3700173200125" />
                                <button type="button" @click="startScan" :disabled="scanning" class="px-3 py-2 bg-orange-500 text-white rounded-md text-sm whitespace-nowrap">📷 Scanner</button>
                                <button type="button" @click="searchByBarcode(true)" :disabled="fetching || !quickForm.barcode" class="px-3 py-2 bg-gray-600 text-white rounded-md text-sm whitespace-nowrap">{{ fetching ? '...' : 'Rechercher' }}</button>
                            </div>
                            <p v-if="scanError" class="mt-1 text-sm text-red-600">{{ scanError }}</p>
                            <p v-if="fetchError" class="mt-1 text-sm text-orange-600">{{ fetchError }}</p>
                            <p v-if="fetching" class="mt-1 text-sm text-indigo-600">Recherche en cours...</p>
                        </div>

                        <div class="mb-4">
                            <InputLabel for="quick-titre" value="Titre" />
                            <TextInput id="quick-titre" v-model="quickForm.titre" type="text" class="mt-1 block w-full" required />
                            <InputError :message="quickForm.errors.titre" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <InputLabel for="quick-realisateur" value="Réalisateur" />
                            <TextInput id="quick-realisateur" v-model="quickForm.realisateur" type="text" class="mt-1 block w-full" />
                        </div>

                        <button
                            @click="submitQuick"
                            :disabled="quickForm.processing || !quickForm.titre"
                            class="w-full py-3 bg-orange-500 hover:bg-orange-600 disabled:opacity-50 text-white rounded-md font-semibold"
                        >
                            ✅ Ajouter ce film → Scanner le suivant
                        </button>
                    </div>

                    <div v-if="addedMovies.length > 0" class="bg-white shadow-sm rounded-lg p-4">
                        <h3 class="font-medium text-gray-700 mb-3">Ajoutés cette session ({{ addedMovies.length }})</h3>
                        <ul class="space-y-2">
                            <li v-for="(m, i) in addedMovies" :key="i" class="flex items-center gap-2 text-sm text-gray-600">
                                <span class="text-green-500">✓</span>
                                <span class="font-medium">{{ m.titre }}</span>
                                <span v-if="m.realisateur" class="text-gray-400">— {{ m.realisateur }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- MODE NORMAL -->
                <div v-else class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">

                        <div v-if="scanning" class="mb-6">
                            <video ref="videoRef" class="w-full rounded-lg border-2 border-indigo-500" style="max-height: 300px;" autoplay muted playsinline></video>
                            <button type="button" @click="stopScan" class="mt-2 w-full py-2 px-4 bg-red-500 text-white rounded-md">Annuler le scan</button>
                        </div>

                        <form @submit.prevent="submit">
                            <div class="mb-4">
                                <InputLabel for="barcode" value="Code-barres" />
                                <div class="flex gap-2 mt-1">
                                    <TextInput id="barcode" v-model="form.barcode" type="text" class="block w-full" placeholder="Ex: 3700173200125" />
                                    <button type="button" @click="startScan" :disabled="scanning" class="px-3 py-2 bg-indigo-600 text-white rounded-md whitespace-nowrap">📷 Scanner</button>
                                    <button type="button" @click="searchByBarcode(false)" :disabled="fetching || !form.barcode" class="px-3 py-2 bg-gray-600 text-white rounded-md whitespace-nowrap">{{ fetching ? '...' : 'Rechercher' }}</button>
                                </div>
                                <p v-if="scanError" class="mt-1 text-sm text-red-600">{{ scanError }}</p>
                                <p v-if="fetchError" class="mt-1 text-sm text-orange-600">{{ fetchError }}</p>
                                <p v-if="fetching" class="mt-1 text-sm text-indigo-600">Recherche en cours...</p>
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

                            <PrimaryButton :disabled="form.processing">Enregistrer</PrimaryButton>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
