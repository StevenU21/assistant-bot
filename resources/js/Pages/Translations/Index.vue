<template>
    <AuthenticatedLayout>
        <Head title="Translations" />

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Translations
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex flex-col md:flex-row justify-between mb-6 space-y-4 md:space-y-0 md:space-x-4">
                        <div class="w-full md:w-1/2">
                            <label for="sourceLanguage" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Source Language</label>
                            <select v-model="sourceLanguage" id="sourceLanguage" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200">
                                <option value="en">English</option>
                                <option value="es">Spanish</option>
                                <!-- Add more languages as needed -->
                            </select>
                        </div>
                        <div class="flex items-center justify-center md:w-auto">
                            <button @click="swapLanguages" :class="{'rotate-180': isSwapped}" class="p-2 bg-gray-300 dark:bg-gray-700 rounded-full hover:bg-gray-400 dark:hover:bg-gray-600 focus:outline-none transition-transform duration-300">
                                <i class="fas fa-exchange-alt text-gray-700 dark:text-gray-200"></i>
                            </button>
                        </div>
                        <div class="w-full md:w-1/2">
                            <label for="targetLanguage" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Target Language</label>
                            <select v-model="targetLanguage" id="targetLanguage" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200">
                                <option value="en">English</option>
                                <option value="es">Spanish</option>
                                <!-- Add more languages as needed -->
                            </select>
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row justify-between mb-6 space-y-4 md:space-y-0 md:space-x-4">
                        <textarea v-model="sourceText" class="w-full md:w-1/2 p-4 border rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 border-gray-300 dark:border-gray-600" rows="10" placeholder="Enter text to translate"></textarea>
                        <textarea v-model="translatedText" class="w-full md:w-1/2 p-4 border rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 border-gray-300 dark:border-gray-600" rows="10" placeholder="Translated text" readonly></textarea>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref } from "vue";

const sourceLanguage = ref("en");
const targetLanguage = ref("es");
const sourceText = ref("");
const translatedText = ref("");
const isSwapped = ref(false);

const swapLanguages = () => {
    isSwapped.value = !isSwapped.value;
    const temp = sourceLanguage.value;
    sourceLanguage.value = targetLanguage.value;
    targetLanguage.value = temp;
};
</script>

<style scoped>
.rotate-180 {
    transform: rotate(180deg);
}
</style>
