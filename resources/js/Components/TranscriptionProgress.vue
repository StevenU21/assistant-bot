<template>
    <div v-if="show" class="transcription-progress">
        <div class="spinner"></div>
        <p>Transcription in progress...</p>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const show = ref(false);

const start = () => {
    show.value = true;
};

const stop = () => {
    show.value = false;
};

defineExpose({ start, stop });
</script>

<style scoped>
.transcription-progress {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 20px;
    border-radius: 10px;
    z-index: 1000;
}

.spinner {
    border: 4px solid rgba(255, 255, 255, 0.3);
    border-top: 4px solid white;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
