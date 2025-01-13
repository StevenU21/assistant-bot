<template>
  <transition name="slide-up">
    <div
      v-if="show"
      class="fixed bottom-4 left-4 w-80 rounded-lg bg-gray-800 p-4 shadow-xl dark:bg-gray-700"
    >
      <div class="mb-2 flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-200">Process Manager</h2>
        <button @click="toggle" class="text-sm text-gray-400">
          <i class="fas fa-minus"></i> Minimize
        </button>
      </div>

      <div v-if="processes.length === 0" class="text-gray-400">
        No processes running
      </div>
      <ul v-else>
        <li
          v-for="(process, index) in processes"
          :key="index"
          class="mb-1 rounded border p-2 text-gray-200 dark:border-gray-600 animate-pulse"
        >
          {{ process }}
        </li>
      </ul>
    </div>
    <button
      v-else
      @click="toggle"
      class="fixed bottom-4 left-4 flex items-center rounded-lg bg-gray-800 p-2 text-sm text-gray-400 shadow-xl dark:bg-gray-700"
    >
      <span>Show Process Manager</span>
      <span v-if="processes.length > 0" class="ml-2 rounded-full bg-red-600 px-2 py-1 text-xs text-white animate-blink">
        {{ processes.length }}
      </span>
    </button>
  </transition>
</template>

<script setup>
import { ref } from "vue";

const show = ref(false);
const processes = ref(["Process A", "Process B", "Process C", "Process D", "Process E", "Process F"]);

function toggle() {
  show.value = !show.value;
}
</script>

<style scoped>
.slide-up-enter-active, .slide-up-leave-active {
  transition: all 0.3s ease;
}
.slide-up-enter-from, .slide-up-leave-to {
  opacity: 0;
  transform: translateY(10px);
}

@keyframes blink {
  50% {
    opacity: 0;
  }
}

.animate-blink {
  animation: blink 1s infinite;
}

.animate-pulse {
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}
</style>
