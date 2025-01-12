<template>
    <AuthenticatedLayout>
        <Head title="Images" />

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Images
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="flex justify-between mb-6">
                    <Pagination class="hidden sm:block" :links="images.links" />

                    <PrimaryButton @click="showModal = true">
                        <i class="fas fa-plus mr-2"></i> Generate Image
                    </PrimaryButton>
                </div>
                <!-- Tabla de imágenes -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <ProgressBar ref="progressBar" />
                        <table class="w-full min-w-max">
                            <thead>
                                <tr>
                                    <th class="text-white p-4 text-left">
                                        <i class="fas fa-image mr-2"></i>Prompt
                                    </th>
                                    <th class="text-white p-4 text-left">
                                        <i class="fas fa-expand mr-2"></i>Image
                                    </th>
                                    <th class="text-white p-4 text-left">
                                        <i class="fas fa-calendar-alt mr-2"></i>Generated At
                                    </th>
                                    <th class="text-white p-4">
                                        <i class="fas fa-cogs mr-2"></i>Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="!images || !images.data || images.data.length === 0">
                                    <td colspan="5" class="text-white px-4 py-8 text-center bg-gray-700 rounded-lg">
                                        No images found.
                                    </td>
                                </tr>
                                <tr v-else v-for="image in images.data" :key="image.id">
                                    <td class="text-white px-4 py-2">
                                        {{ truncate(image.prompt) }}
                                    </td>
                                    <td class="text-white px-4 py-2">
                                        {{ image.imageUrl }}
                                    </td>
                                    <td class="text-white px-4 py-2">
                                        {{ format(new Date(image.created_at), 'dd/MM/yyyy HH:mm:ss') }}
                                    </td>
                                    <td class="text-white px-4 py-2 space-x-2 text-center">
                                        <div class="flex space-x-2 justify-center">
                                            <DropdownMenu>
                                                <PrimaryButton @click="viewImage(image.id)" class="bg-blue-500 hover:bg-blue-700 text-white w-full text-left">
                                                    <i class="fas fa-eye mr-2"></i> View
                                                </PrimaryButton>
                                                <PrimaryButton @click="downloadImage(image.id)" class="bg-green-500 hover:bg-green-700 text-white w-full text-left">
                                                    <i class="fas fa-download mr-2"></i> Download
                                                </PrimaryButton>
                                                <PrimaryButton @click="deleteImage(image.id)" class="bg-red-500 hover:bg-red-700 text-white w-full text-left">
                                                    <i class="fas fa-trash mr-2"></i> Delete
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
                    <Pagination :links="images.links" />
                </div>
            </div>
        </div>

        <!-- Modal for Form -->
        <Modal :show="showModal" @close="showModal = false">
            <Form :submitAction="submitForm" buttonText="Generate Image" :errors="errors" />
        </Modal>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Pagination from "@/Components/Pagination.vue";
import DropdownMenu from "@/Components/DropdownMenu.vue";
import Modal from "@/Components/Modal.vue";
import Form from "@/Pages/Images/Form.vue";
import ProgressBar from '@/Components/ProgressBar.vue';
import { Head, router, usePage } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import { ref, onMounted } from "vue";
import { format } from 'date-fns';

defineProps({
    images: {
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

const deleteImage = (id) => {
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
            router.delete(route("images.destroy", id), {
                onSuccess: () => {
                    Swal.fire("Deleted!", "Image has been deleted.", "success");
                },
                onError: () => {
                    Swal.fire("Failed!", "Failed to delete image.", "error");
                },
            });
        }
    });
};

const downloadImage = (id) => {
    window.location.href = route("images.download", id);
};

const viewImage = (id) => {
    window.open(route("images.show", id), "_blank");
};

const submitForm = (formData) => {
    router.post(route("images.store"), formData, {
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
        localStorage.removeItem('isProgressing');
    }
};

onMounted(() => {
    if (localStorage.getItem('isProgressing') === 'true') {
        startProgress();
    }

    window.Echo.channel(`images.${page.props.auth.user.id}`)
        .listen("ImageUploadStarted", () => {
            startProgress();
        })
        .listen("ImageUploadCompleted", () => {
            stopProgress();
            updateImages();
        });
});

const updateImages = () => {
    router.visit(route("images.index"), {
        preserveScroll: true,
        only: ["images"],
        onError: () => {
            Swal.fire("Error!", "Failed to update images.", "error");
        },
    });
};
</script>
