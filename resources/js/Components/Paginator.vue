<script setup>

import { Link } from '@inertiajs/vue3';

defineProps({
    links: {
        type: Object,
        required: true
    },
    from: {
        type: Number,
        required: true
    },
    to: {
        type: Number,
        required: true
    },
    total: {
        type: Number,
        required: true
    }
});

</script>

<template>

    <div
        v-if="links.length > 3"
        class="flex items-center justify-between px-6 py-4 border-t border-gray-100"
    >
        <p class="text-xs text-gray-500">
            Showing {{ from }}–{{ to }} of {{ total }} posts
        </p>
        <div class="flex items-center gap-1">
            <Link
                v-for="link in links"
                :key="link.label"
                :href="link.url ?? ''"
                class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors"
                :class="[
                    link.active
                        ? 'bg-gray-900 text-white'
                        : 'text-gray-600 hover:bg-gray-100',
                    !link.url
                        ? 'opacity-40 pointer-events-none'
                        : '',
                ]"
                v-html="link.label"
            />
        </div>
    </div>

</template>