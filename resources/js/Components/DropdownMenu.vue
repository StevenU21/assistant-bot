<template>
    <div class="relative">
        <button @click="toggleDropdown" class="bg-gray-500 hover:bg-gray-700 text-white px-2 py-1 rounded text-xs sm:text-sm">
            <i :class="isOpen ? 'fas fa-chevron-up' : 'fas fa-chevron-down'"></i>
        </button>
        <transition name="dropdown">
            <div v-if="isOpen" class="absolute right-0 mt-2 w-auto sm:w-48 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded shadow-lg z-50">
                <div class="dropdown-content">
                    <slot></slot>
                </div>
            </div>
        </transition>
    </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import eventBus from '@/Components/eventBus.js';

const isOpen = ref(false);

const toggleDropdown = (event) => {
    event.stopPropagation();
    if (!isOpen.value) {
        eventBus.emit('closeAll');
    }
    isOpen.value = !isOpen.value;
};

const closeDropdown = () => {
    isOpen.value = false;
};

const handleClickOutside = (event) => {
    if (!event.target.closest('.relative')) {
        closeDropdown();
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
    eventBus.on('closeAll', closeDropdown);
});

onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside);
    eventBus.off('closeAll', closeDropdown);
});
</script>

<style scoped>
.z-50 {
    z-index: 50;
}

.dropdown-enter-active, .dropdown-leave-active {
    transition: opacity 0.3s ease, transform 0.3s ease;
}
.dropdown-enter-from, .dropdown-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}

.dropdown-content > *:not(:last-child) {
    margin-bottom: 0.2rem;
}
</style>
