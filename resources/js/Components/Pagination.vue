<template>
    <div v-if="links.length > 3" class="flex flex-wrap -mb-1">
        <template v-for="(link, key) in links" :key="key">
            <span
                v-if="link.url === null"
                class="mr-1 mb-1 px-4 py-3 text-sm leading-4 text-gray-400 border rounded"
                v-html="link.label"
            >
            </span>
            <Link
                v-else
                :href="link.url"
                class="mr-1 mb-1 px-4 py-3 text-sm leading-4 border rounded hover:bg-gray-700 focus:border-indigo-500 focus:text-indigo-500"
                :class="{
                    'bg-gray-800 text-white': link.active,
                    'text-gray-400': !link.active,
                }"
                v-html="link.label"
            >
            </Link>
        </template>
    </div>
</template>

<script>
import { Link } from "@inertiajs/vue3";

export default {
    props: {
        links: {
            type: Array,
            required: true,
        },
    },
    components: {
        Link,
    },
};
</script>
