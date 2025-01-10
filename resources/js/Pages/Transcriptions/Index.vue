<template>
    <AuthenticatedLayout>
        <Head title="Transcription" />

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Transcriptions
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="flex justify-between mb-6">
                    <Pagination class="hidden sm:block" :links="transcriptions.links"/>

                    <Link :href="route('transcriptions.create')">
                        <PrimaryButton>
                            <i class="fas fa-plus mr-2"></i> Create transcription
                        </PrimaryButton>
                    </Link>
                </div>
                <!-- Tabla de géneros -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-max">
                            <thead>
                                <tr>
                                    <th class="text-white p-4 text-left">
                                        <i class="fas fa-id-badge mr-2"></i>ID
                                    </th>
                                    <th class="text-white p-4 text-left">
                                        <i class="fas fa-font mr-2"></i>Title
                                    </th>
                                    <th class="text-white p-4 text-left">
                                        <i class="fas fa-align-left mr-2"></i>Content
                                    </th>
                                    <th class="text-white p-4 text-left">
                                        <i class="fas fa-calendar-alt mr-2"></i>Language
                                    </th>
                                    <th class="text-white p-4">
                                        <i class="fas fa-cogs mr-2"></i>Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="!transcriptions || !transcriptions.data || transcriptions.data.length === 0">
                                    <td
                                        colspan="5"
                                        class="text-white px-4 py-8 text-center bg-gray-700 rounded-lg">
                                        No transcriptions found.
                                    </td>
                                </tr>
                                <tr v-else v-for="transcription in transcriptions.data" :key="transcription.id">
                                    <td class="text-white px-4 py-2">
                                        {{ transcription.id }}
                                    </td>
                                    <td class="text-white px-4 py-2">
                                        {{ transcription.title }}
                                    </td>
                                    <td class="text-white px-4 py-2">
                                        {{ truncate(transcription.content, 30) }}
                                    </td>
                                    <td class="text-white px-4 py-2">
                                        <span v-if="transcription.language === 'en'">
                                            🇺🇸
                                        </span>
                                        <span v-else-if="transcription.language === 'es'">
                                            🇪🇸
                                        </span>
                                        {{ transcription.language }}
                                    </td>
                                    <td class="text-white px-4 py-2 space-x-2 text-center">
                                        <div class="flex space-x-2 justify-center">
                                            <PrimaryButton @click="viewTranscription(transcription.slug)" class="bg-blue-500 hover:bg-blue-700 text-white">
                                                <i class="fas fa-eye mr-2"></i>
                                                View PDF
                                            </PrimaryButton>
                                            <PrimaryButton @click="downloadTranscription(transcription.slug)" class="bg-green-500 hover:bg-green-700 text-white">
                                                <i class="fas fa-download mr-2"></i>
                                                Download PDF
                                            </PrimaryButton>
                                            <PrimaryButton @click="deletetranscription(transcription.slug)" class="bg-red-500 hover:bg-red-700 text-white">
                                                <i class="fas fa-trash mr-2"></i>
                                                Delete
                                            </PrimaryButton>
                                            <PrimaryButton @click="toggleAudio(transcription)" class="bg-yellow-500 hover:bg-yellow-700 text-white">
                                                <i :class="{'fas fa-play': !transcription.isPlaying, 'fas fa-pause': transcription.isPlaying}" class="mr-2"></i>
                                                {{ transcription.isPlaying ? 'Pause' : 'Play' }} Audio
                                            </PrimaryButton>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="flex justify-left mb-6 mt-6">
                    <Pagination :links="transcriptions.links" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Pagination from "@/Components/Pagination.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import { ref } from 'vue';

defineProps({
    transcriptions: {
        type: Object,
        default: () => ({
            data: [],
            links: []
        })
    },
});

const truncate = (text, length) => {
    if (text.length <= length) {
        return text;
    }
    return text.substring(0, length) + '...';
};

const deletetranscription = (slug) => {
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("transcriptions.destroy", slug), {
                onSuccess: () => {
                    Swal.fire("Deleted!", "Transcription has been deleted.", "success");
                },
                onError: () => {
                    Swal.fire("Failed!", "Failed to delete transcription.", "error");
                },
            });
        }
    });
};

const downloadTranscription = (slug) => {
    window.location.href = route('transcriptions.download', slug);
};

const viewTranscription = (slug) => {
    window.open(route('transcriptions.show', slug), '_blank');
};

const currentPlaying = ref(null);

const toggleAudio = (transcription) => {
    if (currentPlaying.value && currentPlaying.value !== transcription) {
        currentPlaying.value.audio.pause();
        currentPlaying.value.isPlaying = false;
    }

    if (!transcription.audio || !(transcription.audio instanceof Audio)) {
        transcription.audio = new Audio(transcription.audioUrl);
        transcription.audio.addEventListener('ended', () => {
            transcription.isPlaying = false;
        });
    }

    if (transcription.isPlaying) {
        transcription.audio.pause();
    } else {
        transcription.audio.play();
    }

    transcription.isPlaying = !transcription.isPlaying;
    currentPlaying.value = transcription.isPlaying ? transcription : null;
};
</script>
