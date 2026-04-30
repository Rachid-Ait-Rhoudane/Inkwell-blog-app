<script setup>
import { ref } from 'vue'

const open = ref(false)
const toggle = () => { open.value = !open.value }
const close = () => { open.value = false }
</script>

<template>
    <div class="relative">
        <!-- Backdrop teleported to body to avoid z-index stacking context issues -->
        <Teleport to="body">
            <div
                v-if="open"
                class="fixed inset-0 z-40"
                @click="close"
            />
        </Teleport>

        <!-- Trigger -->
        <slot name="trigger" :open="open" :toggle="toggle" />

        <!-- Panel -->
        <Transition
            enter-active-class="transition ease-out duration-100 origin-top-right"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75 origin-top-right"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
            <div
                v-if="open"
                class="absolute right-0 mt-1.5 w-52 bg-white rounded-xl border border-gray-100 shadow-lg py-1 overflow-hidden z-50"
            >
                <slot :close="close" />
            </div>
        </Transition>
    </div>
</template>
