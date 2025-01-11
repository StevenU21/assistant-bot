<template>
    <form @submit.prevent="submit" class="space-y-6 bg-gray-800 p-6 rounded-lg shadow-md">
        <div>
            <InputLabel for="text" value="Text" class="text-white" />
            <div class="flex items-center mt-1">
                <i class="fas fa-font text-white mr-2"></i>
                <input
                    id="text"
                    v-model="form.text"
                    type="text"
                    class="block w-full text-white bg-gray-700 border border-gray-600 rounded-md"
                />
            </div>
            <InputError :message="errors.text" class="mt-2 text-red-500" />
        </div>

        <div>
            <InputLabel for="voice" value="Voice" class="text-white" />
            <div class="flex items-center mt-1">
                <i class="fas fa-microphone text-white mr-2"></i>
                <select
                    id="voice"
                    v-model="form.voice"
                    class="block w-full mt-1 text-white bg-gray-700 border border-gray-600 rounded-md"
                >
                    <option value="" disabled selected>Select a Voice</option>
                    <option value="alloy">Alloy</option>
                    <option value="ash">Ash</option>
                    <option value="coral">Coral</option>
                    <option value="echo">Echo</option>
                    <option value="fable">Fable</option>
                    <option value="onyx">Onyx</option>
                    <option value="nova">Nova</option>
                    <option value="sage">Sage</option>
                    <option value="shimmer">Shimmer</option>
                </select>
                <button
                    type="button"
                    @click="togglePlay"
                    class="ml-2 bg-gray-700 text-white font-bold py-2 px-4 rounded flex items-center"
                >
                    <i :class="isPlaying ? 'fas fa-stop' : 'fas fa-play'"></i>
                    <span class="ml-2">{{ isPlaying ? 'Stop' : 'Play' }}</span>
                </button>
            </div>
            <InputError :message="errors.voice" class="mt-2 text-red-500" />
        </div>

        <div>
            <InputLabel for="model" value="Model" class="text-white" />
            <div class="flex items-center mt-1">
                <i class="fas fa-cogs text-white mr-2"></i>
                <select
                    id="model"
                    v-model="form.model"
                    class="block w-full mt-1 text-white bg-gray-700 border border-gray-600 rounded-md"
                >
                    <option value="" disabled selected>Select a Model</option>
                    <option value="tts-1">TTS-1</option>
                    <option value="tts-1-hd">TTS-1 HD</option>
                </select>
            </div>
            <InputError :message="errors.model" class="mt-2 text-red-500" />
        </div>

        <div class="mt-8 flex justify-between">
            <PrimaryButton type="submit" class="bg-blue-500 hover:bg-blue-700 text-white">
                <i class="fas fa-save mr-2"></i> {{ buttonText }}
            </PrimaryButton>
        </div>
    </form>
</template>

<script setup>
import { ref, watch, onBeforeUnmount } from "vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";

const props = defineProps({
    submitAction: {
        type: Function,
        required: true,
    },
    buttonText: {
        type: String,
        default: "Submit",
    },
    errors: {
        type: Object,
        default: () => ({}),
    },
});

const form = ref({
    text: "",
    voice: "alloy",
    model: "tts-1",
});

const isPlaying = ref(false);
let audio = null;

const togglePlay = () => {
    if (isPlaying.value) {
        audio.pause();
        audio.currentTime = 0;
        isPlaying.value = false;
    } else {
        audio = new Audio(`/audios/${form.value.voice}.wav`);
        audio.play();
        isPlaying.value = true;
        audio.onended = () => {
            isPlaying.value = false;
        };
    }
};

watch(() => form.value.voice, () => {
    if (isPlaying.value) {
        audio.pause();
        audio.currentTime = 0;
        isPlaying.value = false;
    }
});

onBeforeUnmount(() => {
    if (isPlaying.value) {
        audio.pause();
        audio.currentTime = 0;
        isPlaying.value = false;
    }
});

const submit = () => {
    props.submitAction(form.value);
};
</script>
