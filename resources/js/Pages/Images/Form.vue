<template>
    <form @submit.prevent="handleSubmit" class="space-y-6 bg-gray-800 p-6 rounded-lg shadow-md">
        <div>
            <InputLabel for="prompt" value="Prompt" class="text-white" />
            <div class="flex items-center mt-1">
                <i class="fas fa-keyboard text-white mr-2"></i>
                <textarea
                    id="prompt"
                    v-model="formData.prompt"
                    class="block w-full text-white bg-gray-700 border border-gray-600 rounded-md"
                    rows="3"
                ></textarea>
            </div>
            <InputError :message="errors.prompt" class="mt-2 text-red-500" />
        </div>

        <div class="flex space-x-4">
            <div class="w-1/2">
                <InputLabel for="model" value="Model" class="text-white" />
                <div class="flex items-center mt-1">
                    <i class="fas fa-robot text-white mr-2"></i>
                    <select
                        id="model"
                        v-model="formData.model"
                        class="block w-full mt-1 text-white bg-gray-700 border border-gray-600 rounded-md"
                    >
                        <option value="" disabled selected>Select Image IA Model</option>
                        <option value="dall-e-2">DALL·E 2 (Basic Model Recommended)</option>
                        <option value="dall-e-3">DALL·E 3 (Enhanced Model)</option>
                    </select>
                </div>
                <InputError :message="errors.model" class="mt-2 text-red-500" />
            </div>

            <div class="w-1/2">
                <InputLabel for="size" value="Size" class="text-white" />
                <div class="flex items-center mt-1">
                    <i class="fas fa-expand-arrows-alt text-white mr-2"></i>
                    <select
                        id="size"
                        v-model="formData.size"
                        class="block w-full mt-1 text-white bg-gray-700 border border-gray-600 rounded-md"
                    >
                        <option value="" disabled selected>Select a Size</option>
                        <option v-for="size in availableSizes" :key="size" :value="size">{{ size }}</option>
                    </select>
                </div>
                <InputError :message="errors.size" class="mt-2 text-red-500" />
            </div>
        </div>

        <div class="flex space-x-4">
            <div class="w-1/2">
                <InputLabel for="quality" value="Quality" class="text-white" />
                <div class="flex items-center mt-1">
                    <i class="fas fa-highlighter text-white mr-2"></i>
                    <select
                        id="quality"
                        v-model="formData.quality"
                        class="block w-full mt-1 text-white bg-gray-700 border border-gray-600 rounded-md"
                    >
                        <option value="" disabled selected>Select Image Quality</option>
                        <option value="standard">Standard</option>
                        <option v-if="formData.model === 'dall-e-3'" value="hd">High Definition (Only for DALL·E 3)</option>
                    </select>
                </div>
                <InputError :message="errors.quality" class="mt-2 text-red-500" />
            </div>

            <div class="w-1/2">
                <InputLabel for="style" value="Style" class="text-white" />
                <div class="flex items-center mt-1">
                    <i class="fas fa-palette text-white mr-2"></i>
                    <select
                        id="style"
                        v-model="formData.style"
                        class="block w-full mt-1 text-white bg-gray-700 border border-gray-600 rounded-md"
                    >
                        <option value="" disabled selected>Select a Style</option>
                        <option value="realistic">Realistic</option>
                        <option value="anime">Anime</option>
                        <option value="cartoon">Cartoon</option>
                        <option value="futuristic">Futuristic</option>
                        <option value="abstract">Abstract</option>
                    </select>
                </div>
                <InputError :message="errors.style" class="mt-2 text-red-500" />
            </div>
        </div>

        <div class="mt-8 flex justify-between">
            <PrimaryButton type="submit" class="bg-blue-500 hover:bg-blue-700 text-white">
                <i class="fas fa-save mr-2"></i> {{ buttonText }}
            </PrimaryButton>
        </div>
    </form>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";

const props = defineProps({
    submitAction: {
        type: Function,
        required: true,
    },
    buttonText: {
        type: String,
        default: 'Submit',
    },
    errors: {
        type: Object,
        default: () => ({}),
    },
});

const formData = ref({
    prompt: '',
    size: '',
    style: '',
    quality: '',
    model: '',
});

const availableSizes = computed(() => {
    if (formData.value.model === 'dall-e-2') {
        return ['256x256', '512x512', '1024x1024'];
    } else if (formData.value.model === 'dall-e-3') {
        return ['1024x1024', '1024x1792', '1792x1024'];
    }
    return [];
});

watch(() => formData.value.model, (newModel) => {
    if (newModel === 'dall-e-2' && formData.value.quality === 'hd') {
        formData.value.quality = '';
    }
    formData.value.size = '';
});

const handleSubmit = () => {
    const data = new FormData();
    data.append('prompt', formData.value.prompt);
    data.append('size', formData.value.size);
    data.append('style', formData.value.style);
    data.append('quality', formData.value.quality);
    data.append('model', formData.value.model);

    props.submitAction(data);
};
</script>
