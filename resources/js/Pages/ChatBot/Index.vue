<template>
    <AuthenticatedLayout>
        <Head title="ChatBot"/>

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                ChatBot
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white dark:bg-gray-800 p-6 rounded-md shadow-sm">
                <!-- Model, Temperature, Prompt -->
                <div class="flex flex-col md:flex-row gap-4 mb-6">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                            Model
                        </label>
                        <select v-model="selectedModel"
                                class="mt-1 w-full p-2 border rounded-md bg-white dark:bg-gray-700
                                       text-gray-900 dark:text-gray-200 border-gray-300 dark:border-gray-600">
                            <option value="gpt-3.5-turbo">GPT-3.5 Turbo</option>
                            <option value="text-davinci-003">Davinci</option>
                        </select>
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                            Temperature
                        </label>
                        <input type="number" step="0.1" min="0" max="1" v-model="temperature"
                               class="mt-1 w-full p-2 border rounded-md bg-white dark:bg-gray-700
                                      text-gray-900 dark:text-gray-200 border-gray-300 dark:border-gray-600"/>
                    </div>
                    <!-- Prompt as Select -->
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                            Prompt
                        </label>
                        <select v-model="prompt"
                                class="mt-1 w-full p-2 border rounded-md bg-white dark:bg-gray-700
                                       text-gray-900 dark:text-gray-200 border-gray-300 dark:border-gray-600">
                            <option value="translation">Traducción</option>
                            <option value="summary">Resumen</option>
                            <option value="qa">Preguntas y Respuestas</option>
                        </select>
                    </div>
                </div>

                <!-- Chat Messages -->
                <div ref="chatContainer"
                     class="border border-gray-300 dark:border-gray-600 rounded-md p-4 h-96 overflow-y-auto mb-4">
                    <div v-for="(msg, index) in messages" :key="index"
                         :class="['mb-2 flex', msg.sender === 'bot' ? 'justify-start' : 'justify-end']">
                         <div :class="[
                                'message',
                                msg.sender === 'bot'
                                ? 'bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200'
                                : 'bg-blue-500 text-white'
                            ]"
                            class="p-3 rounded-md max-w-xs">
                            {{ msg.text }}
                        </div>
                    </div>
                </div>

                <!-- Send Message -->
                <div class="flex space-x-2">
                    <textarea rows="2" v-model="userInput"
                              :disabled="isGenerating"
                              @keydown.enter.prevent="sendMessage"
                              class="flex-1 p-2 border rounded-md bg-white dark:bg-gray-700
                                     text-gray-900 dark:text-gray-200 border-gray-300 dark:border-gray-600
                                     resize-none"
                              placeholder="Write your Message..."></textarea>
                    <button @click="sendMessage"
                            :disabled="isGenerating"
                            class="px-4 py-2 bg-indigo-500 text-white rounded-md hover:bg-indigo-600 focus:outline-none
                                   disabled:opacity-50 disabled:cursor-not-allowed">
                        Send
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref, watch, nextTick, onMounted } from "vue";

const selectedModel = ref("gpt-3.5-turbo");
const temperature = ref(0.7);
const prompt = ref("translation");
const userInput = ref("");
const messages = ref([
    { sender: "bot", text: "Hola, soy el bot." },
    { sender: "user", text: "Hola, Bot." },
]);
const isGenerating = ref(false);
const chatContainer = ref(null);

const scrollToBottom = async () => {
    await nextTick();
    if (chatContainer.value) {
        chatContainer.value.scrollTop = chatContainer.value.scrollHeight;
    }
};

// Aseguramos que el scroll siempre esté al fondo al montar y al actualizar mensajes
onMounted(scrollToBottom);
watch(messages, scrollToBottom);

const sendMessage = async () => {
    if (!userInput.value.trim() || isGenerating.value) return;

    isGenerating.value = true;

    // Agregar mensaje del usuario
    messages.value.push({ sender: "user", text: userInput.value });
        await nextTick();
        scrollToBottom();
    // Agregar mensaje del bot como marcador de posición
    const placeholderMsg = {
        sender: "bot",
        text: "El bot está escribiendo...",
    };
    messages.value.push(placeholderMsg);

    // Simular respuesta del bot
    setTimeout(async () => {
    placeholderMsg.text = "Esto es una respuesta simulada.";
    await nextTick();
    scrollToBottom();
    isGenerating.value = false;
}, 1500);

    userInput.value = "";
};
</script>

<style scoped>
h2 {
    margin-bottom: 1rem;
}

.message {
    word-wrap: break-word;
    word-break: break-all;
    overflow-wrap: break-word;
    white-space: pre-wrap;
}
</style>
