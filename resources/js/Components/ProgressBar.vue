<template>
    <div v-if="isVisible" class="progress-bar-container">
        <div class="progress-bar">
            <div
                class="progress-bar-inner"
                :style="{
                    width: `${progress}%`,
                    transitionDuration: `${transitionTime}ms`,
                }"
            ></div>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";

// Estado de la barra de progreso
const progress = ref(0);
const isVisible = ref(false);
const transitionTime = ref(700); // Tiempo de transición inicial
let intervalId = null;

// Función para iniciar el progreso
const start = () => {
    isVisible.value = true;
    progress.value = 0;
    transitionTime.value = 700; // Animación suave
    intervalId = setInterval(() => {
        if (progress.value < 100) {
            progress.value += 10; // Incremento simulado
        } else {
            clearInterval(intervalId);
        }
    }, 700);
};

// Función para detener el progreso
const stop = () => {
    clearInterval(intervalId);
    transitionTime.value = 200; // Acelerar la transición al final
    progress.value = 100; // Completar la barra
    setTimeout(() => {
        isVisible.value = false;
        progress.value = 0; // Reiniciar el progreso
    }, 200); // Sincronizar con el tiempo de transición
};

// Exponer las funciones para usarlas desde el componente padre
defineExpose({ start, stop });
</script>

<style scoped>
.progress-bar-container {
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 20px 0;
    position: relative;
    width: 100%;
}

.progress-bar {
    background-color: rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    height: 14px;
    overflow: hidden;
    width: 100%;
    max-width: 600px; /* Aumenta la longitud de la barra */
    position: relative;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}

.progress-bar-inner {
    background: linear-gradient(90deg, #4caf50, #8bc34a);
    height: 100%;
    width: 0;
    transition: width 0.5s ease; /* Animación inicial */
}
</style>
