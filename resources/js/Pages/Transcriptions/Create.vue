<template>
    <AuthenticatedLayout>
        <!-- Title -->
        <Head title="Generate Transcription" />

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Generate Transcription
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-8">
                    <Form :submitAction="submit" buttonText="Generate Transcription" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import Form from "@/Pages/Transcriptions/Form.vue";
import Swal from "sweetalert2";

export default {
    components: {
        AuthenticatedLayout,
        Head,
        Form,
    },
    methods: {
        submit(formData) {
            console.log("Form data submitted:", formData);
            this.$inertia.post(route("transcriptions.store"), formData, {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire("Success!", "Transcription has been created.", "success");
                },
                onError: (errors) => {
                    console.error("Error submitting form:", errors);
                },
            });
        },
    },
};
</script>
