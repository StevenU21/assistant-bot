<template>
    <AuthenticatedLayout>
        <Head title="Speech Audio" />

        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Speech Audios
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="flex justify-between mb-6">
                    <Pagination
                        class="hidden sm:block"
                        :links="speechAudios.links"
                    />

                    <PrimaryButton @click="showModal = true">
                        <i class="fas fa-plus mr-2"></i> Create Speech Audio
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
                                        <i class="fas fa-font mr-2"></i>ID
                                    </th>
                                    <th class="text-white p-4 text-left">
                                        <i class="fas fa-align-left mr-2"></i
                                        >Text
                                    </th>
                                    <th class="text-white p-4 text-left">
                                        <i class="fas fa-calendar-alt mr-2"></i
                                        >Recorded At
                                    </th>
                                    <th class="text-white p-4">
                                        <i class="fas fa-cogs mr-2"></i>Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-if="
                                        !speechAudios ||
                                        !speechAudios.data ||
                                        speechAudios.data.length === 0
                                    "
                                >
                                    <td
                                        colspan="5"
                                        class="text-white px-4 py-8 text-center bg-gray-700 rounded-lg"
                                    >
                                        No speech audios found.
                                    </td>
                                </tr>
                                <tr
                                    v-else
                                    v-for="speechAudio in speechAudios.data"
                                    :key="speechAudio.id"
                                >
                                    <td class="text-white px-4 py-2">
                                        {{ speechAudio.id }}
                                    </td>

                                    <td class="text-white px-4 py-2">
                                        {{ truncate(speechAudio.text, 60) }}
                                    </td>

                                    <td class="text-white px-4 py-2">
                                        {{ format(new Date(speechAudio.created_at), 'dd/MM/yyyy HH:mm:ss') }}
                                    </td>

                                    <td
                                        class="text-white px-4 py-2 space-x-2 text-center"
                                    >
                                        <div
                                            class="flex space-x-2 justify-center"
                                        >
                                            <AudioPlayer
                                                :audioUrl="speechAudio.audioUrl"
                                                v-model:isPlaying="
                                                    speechAudio.isPlaying
                                                "
                                            />
                                            <DropdownMenu>
                                                <PrimaryButton
                                                    @click="
                                                        downloadText(
                                                            speechAudio.id
                                                        )
                                                    "
                                                    class="bg-green-500 hover:bg-green-700 text-white w-full text-left"
                                                >
                                                    <i
                                                        class="fas fa-download mr-2"
                                                    ></i>
                                                    Download Text
                                                </PrimaryButton>
                                                <PrimaryButton
                                                    @click="
                                                        downloadSpeechAudio(
                                                            speechAudio.id
                                                        )
                                                    "
                                                    class="bg-green-500 hover:bg-green-700 text-white w-full text-left"
                                                >
                                                    <i
                                                        class="fas fa-download mr-2"
                                                    ></i>
                                                    Download Audio
                                                </PrimaryButton>
                                                <PrimaryButton
                                                    @click="
                                                        deleteSpeechAudio(
                                                            speechAudio.id
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
                    <Pagination :links="speechAudios.links" />
                </div>
            </div>
        </div>

        <!-- Modal for Form -->
        <Modal :show="showModal" @close="showModal = false">
            <Form
                :submitAction="submitForm"
                buttonText="Generate Speech Audio"
                :errors="errors"
            />
        </Modal>

        <!-- Speech Audio Progress Animation -->
        <!-- <SpeechAudioProgress ref="speechAudioProgress" id="speech-audio-progress" /> -->
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
import ProgressBar from "@/Components/ProgressBar.vue";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import { ref, onMounted, watch } from "vue";
import { format } from 'date-fns';

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
const isProgressing = ref(false);

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
                onError: () => {
                    Swal.fire(
                        "Failed!",
                        "Failed to delete speech audio.",
                        "error"
                    );
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
        localStorage.setItem("isProgressing", "true");
    }
};

const stopProgress = () => {
    if (progressBar.value) {
        progressBar.value.stop();
        isProgressing.value = false;
        localStorage.removeItem("isProgressing");
    }
};

onMounted(() => {
    if (localStorage.getItem("isProgressing") === "true") {
        startProgress();
    }

    window.Echo.channel(`transcriptions.${page.props.auth.user.id}`)
        .listen("SpeechAudioStarted", () => {
            startProgress();
        })
        .listen("SpeechAudioCompleted", () => {
            stopProgress();
            updateSpeechAudios();
        });
});

const updateSpeechAudios = () => {
    router.visit(route("speech_audios.index"), {
        preserveScroll: true,
        only: ["speechAudios"],
        onError: () => {
            Swal.fire("Error!", "Failed to update speech audios.", "error");
        },
    });
};

watch(isProgressing, (newValue) => {
    if (!newValue) {
        localStorage.removeItem("isProgressing");
    }
});
</script>
