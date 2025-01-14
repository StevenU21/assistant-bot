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
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 flex items-center">
                            <i class="fas fa-robot mr-2"></i> Model
                        </label>
                        <select v-model="selectedModel" :disabled="isGenerating" class="mt-1 w-full p-2 border rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 border-gray-300 dark:border-gray-600">
                            <option v-for="model in modelOptions" :key="model.value" :value="model.value">
                                {{ model.text }}
                            </option>
                        </select>
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 flex items-center">
                            <i class="fas fa-thermometer-half mr-2"></i> Temperature
                        </label>
                        <input type="number" step="0.1" min="0" max="1.5" v-model="temperature" :disabled="isGenerating" class="mt-1 w-full p-2 border rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 border-gray-300 dark:border-gray-600"/>
                    </div>
                    <!-- Prompt as Select -->
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 flex items-center">
                            <i class="fas fa-keyboard mr-2"></i> Prompt
                        </label>
                        <select v-model="prompt" @change="sendPrompt" :disabled="isGenerating" class="mt-1 w-full p-2 border rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 border-gray-300 dark:border-gray-600">
                            <option v-for="option in promptOptions" :key="option.value" :value="option.value">
                                {{ option.text }}
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Chat Messages -->
                <div ref="chatContainer" :class="['border border-gray-300 dark:border-gray-600 rounded-md p-4 h-96 overflow-y-auto mb-4', { blinking: isGenerating }]">
                    <!-- Initial Prompt Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div v-for="(card, index) in initialPrompts" :key="index" @click="sendInitialPrompt(card)" class="cursor-pointer p-4 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-md shadow-sm hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200">
                            <i :class="card.icon" class="text-2xl mb-2"></i>
                            <div class="font-semibold">{{ card.title }}</div>
                        </div>
                    </div>

                    <div
                        v-for="(msg, index) in messages"
                        :key="index"
                        :class="['mb-2 flex', msg.sender === 'bot' ? 'justify-start' : 'justify-end']"
                    >
                        <div
                            v-if="msg.sender === 'bot'"
                            :class="[ 'message', 'bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200' ]"
                            class="p-3 rounded-md max-w-lg md:max-w-xl"
                        >
                            <div v-html="msg.text ? renderMarkdown(msg.text) : ''"></div>
                                <button v-if="msg.isComplete" @click="downloadResponse(msg.text)" class="mt-2 px-2 py-1 bg-red-600 text-white rounded-md hover:bg-red-700 flex items-center">
                                <i class="fas fa-download mr-2"></i> Descargar Respuesta
                            </button>
                        </div>
                        <div
                            v-else
                            :class="[ 'message', 'bg-indigo-600 hover:bg-indigo-700 text-white' ]"
                            class="p-3 rounded-md max-w-lg md:max-w-xl">
                            {{ msg.text }}
                        </div>
                    </div>
                </div>

                <div v-if="errorMessage" class="text-red-500 mb-2">{{ errorMessage }}</div>

                <div class="flex space-x-2 items-center">
                    <textarea ref="messageInput" rows="2" v-model="userInput"
                            :disabled="isGenerating"
                            @input="limitText"
                            @keydown.enter.prevent="sendMessage"
                            class="flex-1 p-2 border rounded-md bg-white dark:bg-gray-700
                                text-gray-900 dark:text-gray-200 border-gray-300 dark:border-gray-600
                                resize-none"
                            placeholder="Write your Message..."></textarea>
                    <button @click="sendMessage"
                            :disabled="isGenerating"
                            class="p-6 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none
                                disabled:opacity-50 disabled:cursor-not-allowed flex items-center">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                    <DropdownMenu>
                        <template #trigger>
                            <button :disabled="isGenerating"
                                    class="p-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none
                                        disabled:opacity-50 disabled:cursor-not-allowed flex items-center">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                        </template>
                        <template #default>
                            <button @click="clearChat"
                                    :disabled="isGenerating"
                                    class="block w-full text-left px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600">
                                <i class="fas fa-trash-alt"></i>
                                <span class="hidden sm:inline"> Clear Chat</span>
                            </button>
                        </template>
                    </DropdownMenu>
                </div>
                <div class="text-left text-sm text-gray-400">{{ userInput.length }} / 600</div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref, watch, nextTick, onMounted } from "vue";
import axios from "axios";
import { marked } from 'marked';
import eventBus from "@/Components/eventBus.js";
import DropdownMenu from "@/Components/DropdownMenu.vue";

function renderMarkdown(text) {
    return marked(text || '');
}

const downloadResponse = (text) => {
    const title = text.split(' ').slice(0, 5).join('_'); // Generate a dynamic title
    const blob = new Blob([text], { type: 'text/plain' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = `${title}.txt`;
    link.click();
};

const selectedModel = ref("gpt-4o-mini");
const temperature = ref(0.5);
const prompt = ref("assistant");
const userInput = ref("");
const messages = ref([]);
const isGenerating = ref(false);
const chatContainer = ref(null);

const errorMessage = ref("");

const modelOptions = [
    { value: "gpt-3.5-turbo", text: "GPT-3.5 Turbo" },
    { value: "gpt-4o-mini", text: "GPT-4o-mini" },
    { value: "gpt-4o", text: "GPT-4o" },
];

const initialPrompts = [
    { title: "Explicación de código PHP", text: "Explica cómo hacer una función recursiva en PHP." },
    { title: "Resumen de 'Cien años de soledad'", text: "Haz un resumen detallado de 'Cien años de soledad' destacando los puntos importantes, personajes, trama, giros argumentales, analogias." },
    { title: "Plan para una salida divertida", text: "Proporciona un plan para una salida divertida con amigos." },
    { title: "Consejos para mejorar hábitos", text: "Dame consejos solidos para mejorar mis hábitos." },
    { title: "Analisis de Mushoku Tensei", text: "Has un resumen de la obra japonesa Mushoku Tensei, destacando punto importante, el protagonista, los simbolismos y los personajes"},
    { title: "Guia de Estudio para Laravel", text: "Dame una guia de estudio completa paso a paso para aprender el framework laravel, desde lo mas basico hasta lo mas avanzado, con el fin de tenes los conocimientos necesarios para un entorno laboral" },
];

const promptOptions = [
    { value: "assistant", text: "Assistant" },
    { value: "grammar_correction", text: "Grammar Correction" },
    { value: "sarcastic_response", text: "Sarcastic" },
    { value: "code_explainer", text: "Code Explainer" },
    { value: "simplify_text", text: "Simplify Text" },
    { value: "code_interviewer", text: "Code Interviewer" },
    { value: "improve_code_efficiency", text: "Improve Code" },
    { value: "translator", text: "Translation" },
    { value: "psychologist", text: "Psychologist" },
];

const sendPrompt = async () => {
    const selectedPrompt = initialPrompts.find(p => p.title.toLowerCase().replace(/\s+/g, '_') === prompt.value);
    if (selectedPrompt) {
        userInput.value = selectedPrompt.text;
        await sendMessage();
    }
};

const scrollToBottom = async () => {
    await nextTick();
    if (chatContainer.value) {
        chatContainer.value.scrollTop = chatContainer.value.scrollHeight;
    }
};

onMounted(scrollToBottom);
watch(messages, scrollToBottom);

const limitText = () => {
    if (userInput.value.length > 600) {
        userInput.value = userInput.value.slice(0, 600);
    }
};

const messageInput = ref(null);

const getRequestCount = async () => {
    try {
        const response = await axios.get('/user/request_count');
        eventBus.emit('requestCountUpdated', response.data.request_count); // Emit the event
    } catch (error) {
        console.error("Error fetching request count:", error);
    }
};

const sendMessage = async () => {
    errorMessage.value = "";
    if (userInput.value.trim().length < 4) {
        errorMessage.value = "Minimum 4 characters.";
        return;
    }
    if (userInput.value.trim().length > 600) {
        errorMessage.value = "Maximum 600 characters.";
        return;
    }
    if (!userInput.value.trim() || isGenerating.value) return;

    // Send user message
    messages.value.push({ sender: "user", text: userInput.value });
    userInput.value = ""; // Clear input immediately
    await scrollToBottom(); // Scroll after adding user message

    isGenerating.value = true;

    // Temporary message while bot is responding
    const placeholderMsg = { sender: "bot", text: "The bot is typing", isComplete: false };
    messages.value.push(placeholderMsg);
    await scrollToBottom();

    try {
        const { data } = await axios.post("/chatbot", {
            text: messages.value[messages.value.length - 2].text,
            model: selectedModel.value,
            prompt: prompt.value,
            temperature: temperature.value,
        });
        placeholderMsg.text = data.bot_message;
        placeholderMsg.isComplete = true; // Mark the message as complete
        getRequestCount(); // Update request count after sending message
    } catch (err) {
        if (err.response?.status === 429) {
            placeholderMsg.text = "Error: " + (err.response?.data.message);
            placeholderMsg.isComplete = true; // Mark the message as complete even on error
        }
    }

    isGenerating.value = false;
    await nextTick();
    messageInput.value.focus(); // Set focus back to input
    scrollToBottom();
};

const sendInitialPrompt = async (card) => {
    userInput.value = card.text;
    await sendMessage();
};

const clearChat = () => {
    messages.value = [];
};
</script>

<style scoped>
    .message {
        word-wrap: break-word;
        word-break: break-all;
        overflow-wrap: break-word;
        white-space: pre-wrap;
    }

    .blinking {
    animation: blinkingText 1.2s infinite;
}

@keyframes blinkingText {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

    /* Ensure chat container scrolls to bottom */
    .border {
        overflow-y: auto;
    }
</style>
