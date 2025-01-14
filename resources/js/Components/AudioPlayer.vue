<template>
    <div>
        <PrimaryButton @click="toggleAudio" :class="buttonClass">
            <i :class="iconClass" class="mr-0 sm:mr-2"></i>
            <span class="hidden sm:inline">{{ isPlaying ? 'Pause' : 'Play' }} Audio</span>
        </PrimaryButton>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue';
import PrimaryButton from './PrimaryButton.vue';
import { currentPlaying } from './audioState.js';

const props = defineProps({
    audioUrl: {
        type: String,
        required: true,
    },
    isPlaying: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['update:isPlaying']);

const audio = ref(null);
const isPlaying = ref(props.isPlaying);

const buttonClass = ref(`${isPlaying.value ? 'bg-red-500' : 'bg-green-500'} `);
const iconClass = ref(isPlaying.value ? 'fas fa-pause' : 'fas fa-play');

const toggleAudio = () => {
    if (currentPlaying.value && currentPlaying.value !== audio.value) {
        currentPlaying.value.pause();
        currentPlaying.value = null;
    }

    if (isPlaying.value) {
        audio.value.pause();
    } else {
        audio.value.play();
        currentPlaying.value = audio.value;
    }
    isPlaying.value = !isPlaying.value;
    emit('update:isPlaying', isPlaying.value);
};

onMounted(() => {
    audio.value = new Audio(props.audioUrl);
    audio.value.addEventListener('ended', () => {
        isPlaying.value = false;
        emit('update:isPlaying', false);
        currentPlaying.value = null;
    });

    watch(currentPlaying, (newVal) => {
        if (newVal !== audio.value) {
            isPlaying.value = false;
            emit('update:isPlaying', false);
        }
    });
});

onUnmounted(() => {
    if (audio.value) {
        audio.value.pause();
        audio.value = null;
    }
});

watch(isPlaying, (newVal) => {
    iconClass.value = newVal ? 'fas fa-pause' : 'fas fa-play';
    buttonClass.value = `${newVal ? 'bg-red-500' : 'bg-green-500'}`;
});

watch(currentPlaying, (newVal) => {
    if (newVal !== audio.value) {
        isPlaying.value = false;
        iconClass.value = 'fas fa-play';
        buttonClass.value = 'bg-green-500';
    }
});
</script>
