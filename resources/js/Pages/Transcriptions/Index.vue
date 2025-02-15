<template>
    <AuthenticatedLayout>
        <Head title="Transcription" />

        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Transcriptions
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="flex justify-between mb-6">
                    <Pagination class="hidden sm:block" :links="transcriptions.links"/>

                    <PrimaryButton @click="showModal = true">
                        <i class="fas fa-plus mr-2"></i> Create transcription
                    </PrimaryButton>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                    <table class="w-full min-w-max">
                        <thead>
                            <tr>
                                <th class="text-white p-4 text-left">
                                    <i class="fas fa-font mr-2"></i>
                                    <span class="hidden sm:inline">Title</span>
                                </th>
                                <th class="text-white p-4 text-left hidden sm:table-cell">
                                    <i class="fas fa-align-left mr-2"></i>
                                    <span class="hidden sm:inline">Content</span>
                                </th>
                                    <th class="text-white p-4 text-left">
                                    <i class="fas fa-globe mr-2"></i>
                                    <span class="hidden sm:inline">Language</span>
                                </th>
                                <th class="text-white p-4">
                                    <i class="fas fa-cogs mr-2"></i>
                                    <span class="hidden sm:inline">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="!transcriptions || !transcriptions.data || transcriptions.data.length === 0">
                                <td colspan="4" class="text-white px-4 py-8 text-center bg-gray-700 rounded-lg">
                                    No transcriptions found.
                                </td>
                            </tr>
                            <tr v-else v-for="transcription in transcriptions.data" :key="transcription.id">
                                <td class="text-white px-4 py-2">
                                    <span class="hidden md:inline">{{ truncate(transcription.title, 40) }}</span>
                                    <span class="inline md:hidden">{{ truncate(transcription.title, 14) }}</span>
                                </td>
                                <td class="text-white px-4 py-2 hidden sm:table-cell">
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
                                    <td class="text-white px-2 py-2 space-x-2 text-center">
                                        <div class="flex space-x-1 justify-center">
                                            <AudioPlayer :audioUrl="transcription.audioUrl" v-model:isPlaying="transcription.isPlaying"/>
                                            <DropdownMenu>
                                                <PrimaryButton @click="viewTranscription(transcription.slug)" class="bg-blue-500 hover:bg-blue-700 text-white w-full text-left px-2 py-1 text-xs sm:text-sm">
                                                    <i class="fas fa-eye mr-1 sm:mr-2"></i>
                                                    <span class="hidden sm:inline">View PDF</span>
                                                </PrimaryButton>
                                                <PrimaryButton @click="downloadTranscription(transcription.slug)" class="bg-green-500 hover:bg-green-700 text-white w-full text-left px-2 py-1 text-xs sm:text-sm">
                                                    <i class="fas fa-download mr-1 sm:mr-2"></i>
                                                    <span class="hidden sm:inline">Download PDF</span>
                                                </PrimaryButton>
                                                <PrimaryButton @click="deletetranscription(transcription.slug)" class="bg-red-500 hover:bg-red-700 text-white w-full text-left px-2 py-1 text-xs sm:text-sm">
                                                    <i class="fas fa-trash mr-1 sm:mr-2"></i>
                                                    <span class="hidden sm:inline">Delete</span>
                                                </PrimaryButton>
                                            </DropdownMenu>
                                        </div>
                                    </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-left mb-6 mt-6">
                    <Pagination :links="transcriptions.links" />
                </div>
            </div>
        </div>

        <!-- Modal for Form -->
        <Modal :show="showModal" @close="showModal = false">
            <Form :submitAction="submitForm" buttonText="Generate Transcription" :errors="errors"/>
        </Modal>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Pagination from "@/Components/Pagination.vue";
import AudioPlayer from "@/Components/AudioPlayer.vue";
import DropdownMenu from "@/Components/DropdownMenu.vue";
import Modal from "@/Components/Modal.vue";
import Form from "@/Pages/Transcriptions/Form.vue";
import { Head, router, usePage } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import { ref, onMounted, watch } from "vue";
import eventBus from "@/Components/eventBus.js";

defineProps({
    transcriptions: {
        type: Object,
        default: () => ({
            data: [],
            links: [],
        }),
    },
});

const showModal = ref(false);
const errors = ref({});

const truncate = (text, length) => {
    if (text.length <= length) {
        return text;
    }
    return text.substring(0, length) + "...";
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
                    Swal.fire(
                        "Deleted!",
                        "Transcription has been deleted.",
                        "success"
                    );
                },
                onError: (error) => {
                    if (error.response.status === 429) {
                        Swal.fire(
                            "Error!",
                            error.response.data.message,
                            "error"
                        );
                    } else {
                        Swal.fire(
                            "Failed!",
                            "Failed to delete transcription.",
                            "error"
                        );
                    }
                },
            });
        }
    });
};

const downloadTranscription = (slug) => {
    window.location.href = route("transcriptions.download", slug);
};

const viewTranscription = (slug) => {
    window.open(route("transcriptions.show", slug), "_blank");
};

const submitForm = (formData) => {
    router.post(route("transcriptions.store"), formData, {
        preserveScroll: true,
        onSuccess: () => {
            showModal.value = false;
            getRequestCount(); // Update request count after form submission
        },
        onError: (error) => {
            if (error.response.status === 429) {
                Swal.fire(
                    "Error!",
                    error.response.data.message,
                    "error"
                );
            } else {
                errors.value = error.response.data.errors;
            }
        },
    });
};

const getRequestCount = async () => {
    try {
        const response = await axios.get('/user/request_count');
        eventBus.emit('requestCountUpdated', response.data.request_count); // Emit the event
    } catch (error) {
        console.error("Error fetching request count:", error);
    }
};

const page = usePage();

onMounted(() => {
    window.Echo.channel(`processes.${page.props.auth.user.id}`).listen(
        "ProcessStatusCompleted",

        () => {
            updateTranscriptions();
        }
    );
});

const updateTranscriptions = () => {
    router.reload({
        only: ["transcriptions"],
        onError: () => {
            Swal.fire("Error!", "Failed to update transcriptions.", "error");
        },
    });
};

// Watch for session messages and display them using SweetAlert
watch(() => page.props.flash, (flash) => {
    if (flash && flash.message) {
        Swal.fire("Error!", flash.message, "error");
    }
});
</script>
