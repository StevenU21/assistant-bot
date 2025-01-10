<template>
    <AuthenticatedLayout>
        <Head title="Tasks" />

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Tasks
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Tabla de tareas en segundo plano -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-max">
                            <thead>
                                <tr>
                                    <th class="text-white p-4 text-left">
                                        <i class="fas fa-id-badge mr-2"></i>ID
                                    </th>
                                    <th class="text-white p-4 text-left">
                                        <i class="fas fa-tasks mr-2"></i>Status
                                    </th>
                                    <th class="text-white p-4 text-left">
                                        <i class="fas fa-spinner mr-2"></i>Progress
                                    </th>
                                    <th class="text-white p-4 text-left">
                                        <i class="fas fa-comment mr-2"></i>Message
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="!tasks || tasks.length === 0">
                                    <td
                                        colspan="4"
                                        class="text-white px-4 py-8 text-center bg-gray-700 rounded-lg">
                                        No tasks found.
                                    </td>
                                </tr>
                                <tr v-else v-for="task in tasks" :key="task.id">
                                    <td class="text-white px-4 py-2">
                                        {{ task.id }}
                                    </td>
                                    <td class="text-white px-4 py-2">
                                        {{ task.status }}
                                    </td>
                                    <td class="text-white px-4 py-2">
                                        {{ task.progress }}%
                                    </td>
                                    <td class="text-white px-4 py-2">
                                        {{ task.message }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref, onMounted } from 'vue';

defineProps({
    tasks: {
        type: Array,
        default: () => []
    }
});

// Real-time updates with Laravel Echo
onMounted(() => {
    window.Echo.channel('tasks')
        .listen('TaskProgressUpdated', (e) => {
            const taskIndex = tasks.findIndex(task => task.id === e.task.id);
            if (taskIndex !== -1) {
                tasks[taskIndex] = e.task;
            } else {
                tasks.push(e.task);
            }
        });
});
</script>
