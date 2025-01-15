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

                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                    <table class="w-full min-w-max">
                        <thead>
                            <tr>
                                <th class="text-white p-4 text-left">
                                    <i class="fas fa-image mr-2"></i>
                                    <span class="hidden sm:inline">Prompt</span>
                                </th>
                                <th class="text-white p-4 text-left hidden sm:table-cell">
                                    <i class="fas fa-calendar-alt mr-2"></i>
                                    <span class="hidden sm:inline">Generated At</span>
                                </th>
                                <th class="text-white p-4 text-left">
                                    <i class="fas fa-expand mr-2"></i>
                                    <span class="hidden sm:inline">Image</span>
                                </th>
                                <th class="text-white p-4">
                                    <i class="fas fa-cogs mr-2"></i>
                                    <span class="hidden sm:inline">Actions</span>
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
                                    <span class="sm:hidden">{{ truncate(image.prompt, 8) }}</span>
                                    <span class="hidden sm:inline">{{ truncate(image.prompt, 40) }}</span>
                                </td>
                                <td class="text-white px-4 py-2 hidden sm:table-cell">
                                    {{ format(new Date(image.created_at), 'dd/MM/yyyy HH:mm:ss') }}
                                </td>
                                <td class="text-white px-4 py-2">
                                    <img :src="image.imageUrl" alt="Generated Image" class="w-16 h-16 sm:w-32 sm:h-32 object-cover rounded-md" />
                                </td>
                                <td class="text-white px-4 py-2 space-x-2 text-center">
                                    <div class="flex space-x-1 justify-center">
                                        <PrimaryButton @click="openImageModal(image)" class="bg-blue-500 hover:bg-blue-700 text-white text-left px-2 py-1 text-xs sm:text-sm">
                                            <i class="fas fa-eye mr-1"></i>
                                            <span class="hidden sm:inline">View</span>
                                        </PrimaryButton>
                                        <DropdownMenu>
                                            <PrimaryButton @click="downloadImage(image.id)" class="bg-green-500 hover:bg-green-700 text-white w-full text-left px-2 py-1 text-xs sm:text-sm">
                                                <i class="fas fa-download mr-1"></i>
                                                <span class="hidden sm:inline">Download</span>
                                            </PrimaryButton>
                                            <PrimaryButton @click="deleteImage(image.id)" class="bg-red-500 hover:bg-red-700 text-white w-full text-left px-2 py-1 text-xs sm:text-sm">
                                                <i class="fas fa-trash mr-1"></i>
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
                    <Pagination :links="images.links" />
                </div>
            </div>
        </div>

        <!-- Modal for Form -->
        <Modal :show="showModal" @close="showModal = false">
            <Form :submitAction="submitForm" buttonText="Generate Image" :errors="errors" :isProgressing="isProgressing" />
        </Modal>

        <!-- Modal for Viewing Image -->
        <Modal :show="showImageModal" @close="showImageModal = false">
            <template #default>
                <div class="p-4">
                    <img :src="selectedImage?.imageUrl" alt="Generated Image" class="w-full h-auto object-cover rounded-md" />
                </div>
            </template>
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
import { Head, router, usePage } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import { ref, onMounted, watch } from "vue";
import { format } from 'date-fns';
import eventBus from "@/Components/eventBus.js"; // Import the event bus

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
const showImageModal = ref(false);
const selectedImage = ref(null);
const errors = ref({});

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
                onError: (error) => {
                    if (error.response.status === 429) {
                        Swal.fire(
                            "Error!",
                            error.response.data.message,
                            "error"
                        );
                    } else {
                        Swal.fire("Failed!", "Failed to delete image.", "error");
                    }
                },
            });
        }
    });
};

const downloadImage = (id) => {
    window.location.href = route("images.download", id);
};

const openImageModal = (image) => {
    selectedImage.value = image;
    showImageModal.value = true;
};

const isProgressing = ref(false);

const submitForm = (formData) => {
    isProgressing.value = true;
    router.post(route("images.store"), formData, {
        preserveScroll: true,
        onSuccess: () => {
            showModal.value = false;
            isProgressing.value = false;
            getRequestCount();
        },
        onError: (error) => {
            isProgressing.value = false;
            if (error.response) {
                if (error.response.status === 429) {
                    Swal.fire("Error!", error.response.data.message, "error");
                } else {
                    errors.value = error.response.data.errors;
                }
            } else {
                Swal.fire("Error!", "Ocurrió un error inesperado. Por favor, intenta nuevamente.", "error");
                console.error("Error sin respuesta:", error);
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
            updateImages();
        }
    );
});

const updateImages = () => {
    router.reload({
        only: ["images"],
        onError: () => {
            Swal.fire("Error!", "Failed to update images.", "error");
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
