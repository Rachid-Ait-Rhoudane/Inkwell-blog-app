<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    href: { type: String, default: '#' },
    method: { type: String, default: 'get' },
    as: { type: String, default: 'a' },
    variant: { type: String, default: 'default' },
})
</script>

<template>
    <Link
        :href="href"
        :method="method"
        :as="as"
        class="flex items-center gap-3 px-4 py-2.5 text-sm transition-colors w-full"
        :class="variant === 'danger'
            ? 'text-red-600 hover:bg-red-50'
            : 'text-gray-700 hover:bg-gray-50'"
    >
        <slot />
    </Link>
</template>
