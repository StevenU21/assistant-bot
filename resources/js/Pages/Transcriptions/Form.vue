<template>
    <form @submit.prevent="submit" class="space-y-6 bg-gray-800 p-6 rounded-lg shadow-md">
        <div>
            <InputLabel for="audio" value="Audio File" class="text-white" />
            <div class="flex items-center mt-1">
                <i class="fas fa-file-audio text-white mr-2"></i>
                <input
                    id="audio"
                    ref="audioInput"
                    @change="handleFileChange"
                    type="file"
                    class="block w-full text-white bg-gray-700 border border-gray-600 rounded-md"
                    accept=".mp3,.m4a"
                />
            </div>
            <InputError :message="form.errors.audio" class="mt-2 text-red-500" />
        </div>

        <div>
            <InputLabel for="language" value="Language" class="text-white" />
            <div class="flex items-center mt-1">
                <i class="fas fa-language text-white mr-2"></i>
                <select
                    id="language"
                    v-model="form.language"
                    class="block w-full mt-1 text-white bg-gray-700 border border-gray-600 rounded-md"
                >
                    <option value="auto">Auto Detect</option>
                    <option value="en">English</option>
                    <option value="es">Spanish</option>
                    <option value="ja">Japanese</option>
                    <option value="fr">French</option>
                    <option value="pt">Portuguese</option>
                </select>
            </div>
            <InputError :message="form.errors.language" class="mt-2 text-red-500" />
        </div>

        <div class="mt-8 flex justify-between">
            <Link :href="route('transcriptions.index')">
                <PrimaryButton class="bg-gray-500 hover:bg-gray-700 text-white">
                    <i class="fas fa-arrow-left mr-2"></i> Back
                </PrimaryButton>
            </Link>
            <PrimaryButton type="submit" class="bg-blue-500 hover:bg-blue-700 text-white">
                <i class="fas fa-save mr-2"></i> {{ buttonText }}
            </PrimaryButton>
        </div>
    </form>
</template>

<script>
import { Head, Link } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import { useForm } from "@inertiajs/vue3";

export default {
    components: {
        Head,
        Link,
        PrimaryButton,
        InputError,
        InputLabel,
    },
    props: {
        buttonText: {
            type: String,
            default: 'Submit'
        },
        submitAction: {
            type: Function,
            required: true
        }
    },
    setup(props) {
        const form = useForm({
            audio: null,
            language: 'auto',
        });

        const handleFileChange = (event) => {
            form.audio = event.target.files[0];
        };

        const submit = () => {
            const formData = new FormData();
            formData.append('audio', form.audio);
            formData.append('language', form.language);

            props.submitAction(formData);
        };

        return {
            form,
            handleFileChange,
            submit
        };
    }
};
</script>
