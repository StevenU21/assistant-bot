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
                    <Pagination
                        class="hidden sm:block"
                        :links="transcriptions.links"
                    />

                    <PrimaryButton @click="showModal = true">
                        <i class="fas fa-plus mr-2"></i> Create transcription
                    </PrimaryButton>
                </div>
                <!-- Tabla de géneros -->
                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
                >
                    <div class="overflow-x-auto">
                        <ProgressBar ref="progressBar" />
                        <table class="w-full min-w-max">
                            <thead>
                                <tr>
                                    <th class="text-white p-4 text-left">
                                        <i class="fas fa-font mr-2"></i>Title
                                    </th>
                                    <th class="text-white p-4 text-left">
                                        <i class="fas fa-align-left mr-2"></i
                                        >Content
                                    </th>
                                    <th class="text-white p-4 text-left">
                                        <i class="fas fa-calendar-alt mr-2"></i
                                        >Language
                                    </th>
                                    <th class="text-white p-4">
                                        <i class="fas fa-cogs mr-2"></i>Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-if="
                                        !transcriptions ||
                                        !transcriptions.data ||
                                        transcriptions.data.length === 0
                                    "
                                >
                                    <td
                                        colspan="5"
                                        class="text-white px-4 py-8 text-center bg-gray-700 rounded-lg"
                                    >
                                        No transcriptions found.
                                    </td>
                                </tr>
                                <tr
                                    v-else
                                    v-for="transcription in transcriptions.data"
                                    :key="transcription.id"
                                >
                                    <td class="text-white px-4 py-2">
                                        {{ transcription.title }}
                                    </td>
                                    <td class="text-white px-4 py-2">
                                        {{
                                            truncate(transcription.content, 30)
                                        }}
                                    </td>
                                    <td class="text-white px-4 py-2">
                                        <span
                                            v-if="
                                                transcription.language === 'en'
                                            "
                                        >
                                            🇺🇸
                                        </span>
                                        <span
                                            v-else-if="
                                                transcription.language === 'es'
                                            "
                                        >
                                            🇪🇸
                                        </span>
                                        {{ transcription.language }}
                                    </td>
                                    <td
                                        class="text-white px-4 py-2 space-x-2 text-center"
                                    >
                                        <div
                                            class="flex space-x-2 justify-center"
                                        >
                                            <AudioPlayer
                                                :audioUrl="
                                                    transcription.audioUrl
                                                "
                                                v-model:isPlaying="
                                                    transcription.isPlaying
                                                "
                                            />
                                            <DropdownMenu>
                                                <PrimaryButton
                                                    @click="
                                                        viewTranscription(
                                                            transcription.slug
                                                        )
                                                    "
                                                    class="bg-blue-500 hover:bg-blue-700 text-white w-full text-left"
                                                >
                                                    <i
                                                        class="fas fa-eye mr-2"
                                                    ></i>
                                                    View PDF
                                                </PrimaryButton>
                                                <PrimaryButton
                                                    @click="
                                                        downloadTranscription(
                                                            transcription.slug
                                                        )
                                                    "
                                                    class="bg-green-500 hover:bg-green-700 text-white w-full text-left"
                                                >
                                                    <i
                                                        class="fas fa-download mr-2"
                                                    ></i>
                                                    Download PDF
                                                </PrimaryButton>
                                                <PrimaryButton
                                                    @click="
                                                        deletetranscription(
                                                            transcription.slug
                                                        )
                                                    "
                                                    class="bg-red-500 hover:bg-red-700 text-white w-full text-left"
                                                >
                                                    <i
                                                        class="fas fa-trash mr-2"
                                                    ></i>
                                                    Delete
                                                </PrimaryButton>
                                            </DropdownMenu>
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

        <!-- Modal for Form -->
        <Modal :show="showModal" @close="showModal = false">
            <Form
                :submitAction="submitForm"
                buttonText="Generate Transcription"
                :errors="errors"
            />
        </Modal>

        <!-- Transcription Progress Animation -->
        <!-- <TranscriptionProgress ref="transcriptionProgress" id="transcription-progress" /> -->
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
import ProgressBar from '@/Components/ProgressBar.vue';
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import { ref, onMounted, watch } from "vue";

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
const isProgressing = ref(false);

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
                onError: () => {
                    Swal.fire(
                        "Failed!",
                        "Failed to delete transcription.",
                        "error"
                    );
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
        },
        onError: (newErrors) => {
            errors.value = newErrors;
        },
    });
};

const progressBar = ref(null);
const page = usePage();

const startProgress = () => {
    if (progressBar.value) {
        progressBar.value.start();
        isProgressing.value = true;
        localStorage.setItem('isProgressing', 'true');
    }
};

const stopProgress = () => {
    if (progressBar.value) {
        progressBar.value.stop();
        isProgressing.value = false;
        localStorage.removeItem('isProgressing'); // Eliminar el item de localStorage
    }
};

onMounted(() => {
    // Recuperar el estado de la barra de progreso desde localStorage
    if (localStorage.getItem('isProgressing') === 'true') {
        startProgress();
    }

    // Suscribirse al canal de Echo
    window.Echo.channel(`transcriptions.${page.props.auth.user.id}`)
    .listen("TranscriptionStarted", () => {
            startProgress();
        })
        .listen("TranscriptionCompleted", () => {
            stopProgress();
            updateTranscriptions();
        });
});

const updateTranscriptions = () => {
    router.visit(route("transcriptions.index"), {
        preserveScroll: true, // Mantener la posición de la página
        only: ["transcriptions"], // Solo recargar las transcripciones,
        onError: () => {
            Swal.fire("Error!", "Failed to update transcriptions.", "error");
        },
    });
};

// Limpiar el estado de la barra de progreso al desmontar el componente
watch(isProgressing, (newValue) => {
    if (!newValue) {
        localStorage.removeItem('isProgressing');
    }
});
</script>
