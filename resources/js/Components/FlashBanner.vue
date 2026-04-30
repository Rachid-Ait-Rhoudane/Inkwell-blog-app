<script setup>
import { computed, ref, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const flash = computed(() => page.props.flash)
const dismissed = ref(false)

watch(flash, () => { dismissed.value = false })

const show = computed(() => !dismissed.value && !!flash.value?.message)
</script>

<template>
    <Transition
        enter-active-class="transition ease-out duration-200"
        enter-from-class="opacity-0 -translate-y-2"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition ease-in duration-150"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 -translate-y-2"
    >
        <div
            v-if="show"
            class="flex items-center gap-3 px-4 py-3 mb-5 rounded-xl text-sm font-medium border"
            :class="{
                'bg-green-50 text-green-800 border-green-200': flash.status === 'success',
                'bg-amber-50 text-amber-800 border-amber-200': flash.status === 'warning',
                'bg-red-50 text-red-800 border-red-200': flash.status === 'error',
            }"
        >
            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path v-if="flash.status === 'success'" stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                <path v-else-if="flash.status === 'warning'" stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                <path v-else stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>

            <span class="flex-1">{{ flash.message }}</span>

            <button class="p-0.5 rounded hover:opacity-70 transition-opacity" @click="dismissed = true">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </Transition>
</template>
