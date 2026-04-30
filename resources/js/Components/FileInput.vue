<script setup>
import { ref } from 'vue'

const props = defineProps({
    modelValue: { type: Array, default: () => [] },
    multiple: { type: Boolean, default: true },
})

const emit = defineEmits(['update:modelValue'])

const isDragging = ref(false)
const fileInput = ref(null)

function addFiles(fileList) {
    emit('update:modelValue', [...props.modelValue, ...Array.from(fileList)])
}

function removeFile(index) {
    emit('update:modelValue', props.modelValue.filter((_, i) => i !== index))
}

function onDrop(e) {
    isDragging.value = false
    addFiles(e.dataTransfer.files)
}

function isImage(file) {
    return file.type.startsWith('image/')
}

function previewUrl(file) {
    return URL.createObjectURL(file)
}

function formatSize(bytes) {
    if (bytes < 1024) return `${bytes} B`
    if (bytes < 1048576) return `${(bytes / 1024).toFixed(1)} KB`
    return `${(bytes / 1048576).toFixed(1)} MB`
}
</script>

<template>
    <div class="space-y-3">
        <!-- Drop zone -->
        <div
            class="flex flex-col items-center justify-center gap-2 w-full h-36 border-2 border-dashed rounded-xl cursor-pointer transition-colors"
            :class="isDragging
                ? 'border-gray-900 bg-gray-50'
                : 'border-gray-200 hover:border-gray-400 hover:bg-gray-50'"
            @click="fileInput.click()"
            @dragover.prevent="isDragging = true"
            @dragleave.prevent="isDragging = false"
            @drop.prevent="onDrop"
        >
            <svg class="h-9 w-9 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
            </svg>
            <p class="text-sm text-gray-500">
                <span class="font-medium text-gray-700">Click to browse</span>
                &nbsp;or drag &amp; drop files here
            </p>
            <p class="text-xs text-gray-400">Max 10 MB per file</p>
            <input
                ref="fileInput"
                type="file"
                :multiple="multiple"
                class="hidden"
                @change="addFiles($event.target.files)"
            />
        </div>

        <!-- File list -->
        <ul v-if="modelValue.length" class="space-y-2 max-h-52 overflow-y-auto">
            <li
                v-for="(file, index) in modelValue"
                :key="index"
                class="flex items-center gap-3 p-2.5 bg-gray-50 rounded-xl"
            >
                <!-- Thumbnail or icon -->
                <div class="h-10 w-10 shrink-0 rounded-lg overflow-hidden bg-gray-200 flex items-center justify-center">
                    <img
                        v-if="isImage(file)"
                        :src="previewUrl(file)"
                        :alt="file.name"
                        class="h-full w-full object-cover"
                    />
                    <svg
                        v-else
                        class="h-5 w-5 text-gray-400"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.5"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>

                <!-- Name + size -->
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-800 truncate">{{ file.name }}</p>
                    <p class="text-xs text-gray-400">{{ formatSize(file.size) }}</p>
                </div>

                <!-- Remove -->
                <button
                    type="button"
                    class="p-1 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-200 transition-colors shrink-0"
                    @click="removeFile(index)"
                >
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </li>
        </ul>
    </div>
</template>
