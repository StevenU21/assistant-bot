<template>
    <AuthenticatedLayout>
        <Head title="Speech Audio" />

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Speech Audios
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="flex justify-between mb-6">
                    <Pagination class="hidden sm:block" :links="speechAudios.links"/>

                    <PrimaryButton @click="showModal = true">
                        <i class="fas fa-plus mr-2"></i> Create Speech Audio
                    </PrimaryButton>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                    <table class="w-full min-w-max">
                        <thead>
                            <tr>
                                <th class="text-white p-4 text-left">
                                    <i class="fas fa-align-left mr-2"></i>
                                    <span class="hidden sm:inline">Text</span>
                                </th>
                                <th class="text-white p-4 text-left hidden sm:table-cell">
                                    <i class="fas fa-calendar-alt mr-2"></i>
                                    <span class="hidden sm:inline">Recorded At</span>
                                </th>
                                <th class="text-white p-4">
                                    <i class="fas fa-cogs mr-2"></i>
                                    <span class="hidden sm:inline">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="!speechAudios || !speechAudios.data || speechAudios.data.length === 0">
                                <td colspan="4" class="text-white px-4 py-8 text-center bg-gray-700 rounded-lg">
                                    No speech audios found.
                                </td>
                            </tr>
                            <tr v-else v-for="speechAudio in speechAudios.data" :key="speechAudio.id">
                                <td class="text-white px-4 py-2">
                                    <span class="sm:hidden">{{ truncate(speechAudio.text, 24) }}</span>
                                    <span class="hidden sm:inline">{{ truncate(speechAudio.text, 60) }}</span>
                                </td>
                                <td class="text-white px-4 py-2 hidden sm:table-cell">
                                    {{ format(new Date(speechAudio.created_at),"dd/MM/yyyy HH:mm:ss") }}
                                </td>
                                <td class="text-white px-2 py-2 space-x-2 text-center">
                                    <div class="flex space-x-1 justify-center">
                                        <AudioPlayer :audioUrl="speechAudio.audioUrl" v-model:isPlaying="speechAudio.isPlaying"/>
                                        <DropdownMenu>
                                            <PrimaryButton @click="downloadText(speechAudio.id)" class="bg-green-500 hover:bg-green-700 text-white w-full text-left px-2 py-1 text-xs sm:text-sm">
                                                <i class="fas fa-file-alt mr-1 sm:mr-2"></i>
                                                <span class="hidden sm:inline">Download TXT</span>
                                            </PrimaryButton>
                                            <PrimaryButton @click="downloadSpeechAudio(speechAudio.id)" class="bg-green-500 hover:bg-green-700 text-white w-full text-left px-2 py-1 text-xs sm:text-sm">
                                                <i class="fas fa-file-audio mr-1 sm:mr-2"></i>
                                                <span class="hidden sm:inline">Download Audio</span>
                                            </PrimaryButton>
                                            <PrimaryButton @click="deleteSpeechAudio(speechAudio.id)" class="bg-red-500 hover:bg-red-700 text-white w-full text-left px-2 py-1 text-xs sm:text-sm">
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
                    <Pagination :links="speechAudios.links" />
                </div>
            </div>
        </div>

        <!-- Modal for Form -->
        <Modal :show="showModal" @close="showModal = false">
            <Form :submitAction="submitForm" buttonText="Generate Speech Audio" :errors="errors"/>
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
import Form from "@/Pages/SpeechAudio/Form.vue";
import { Head, router, usePage } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import { ref, onMounted, watch } from "vue";
import { format } from "date-fns";
import eventBus from "@/Components/eventBus.js";

defineProps({
    speechAudios: {
        type: Object,
        default: () => ({
            data: [],
            links: [],
        }),
    },
});

const showModal = ref(false);
const errors = ref({});
const page = usePage();

const truncate = (text, length) => {
    if (text.length <= length) {
        return text;
    }
    return text.substring(0, length) + "...";
};

const deleteSpeechAudio = (id) => {
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
            router.delete(route("speech_audios.destroy", id), {
                onSuccess: () => {
                    Swal.fire(
                        "Deleted!",
                        "Speech audio has been deleted.",
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
                            "Failed to delete speech audio.",
                            "error"
                        );
                    }
                },
            });
        }
    });
};

const downloadSpeechAudio = (id) => {
    window.location.href = route("speech_audios.download_audio", id);
};

const downloadText = (id) => {
    window.location.href = route("speech_audios.download_text", id);
};

const submitForm = (formData) => {
    router.post(route("speech_audios.store"), formData, {
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

onMounted(() => {
    window.Echo.channel(`processes.${page.props.auth.user.id}`).listen(
        "ProcessStatusCompleted",
        () => {
            updateSpeechAudios();
        }
    );
});

const updateSpeechAudios = () => {
    router.reload({
        only: ["speechAudios"],
        onError: () => {
            Swal.fire("Error!", "Failed to update speech audios.", "error");
        },
    });
};

// Watch for session messages and display them using SweetAlert
watch(() => page.props.flash, (flash) => {
    if (flash?.message) {
        Swal.fire("Error!", flash.message, "error");
    }
});
</script>
