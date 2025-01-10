<template>
    <div class="relative">
        <button @click="toggleDropdown" class="bg-gray-500 hover:bg-gray-700 text-white px-2 py-1 rounded">
            <i :class="isOpen ? 'fas fa-chevron-up' : 'fas fa-chevron-down'"></i>
        </button>
        <div v-if="isOpen" class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded shadow-lg z-50">
            <slot></slot>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const isOpen = ref(false);

const toggleDropdown = () => {
    isOpen.value = !isOpen.value;
};
</script>

<style scoped>
.z-50 {
    z-index: 50;
}
</style>
