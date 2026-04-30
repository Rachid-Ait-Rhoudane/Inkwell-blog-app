<script setup>
import FileInput from './FileInput.vue'
import Modal from './Modal.vue'
import { ref, watch } from 'vue'

const props = defineProps({
    show: { type: Boolean, default: false },
    media: { type: Array, default: () => [] },
    selectedIds: { type: Array, default: () => [] },
})

const emit = defineEmits(['close', 'confirm'])

const activeTab = ref('library')
const localIds = ref([...props.selectedIds])
const localFiles = ref([])

watch(() => props.show, (val) => {
    if (val) {
        localIds.value = [...props.selectedIds]
        localFiles.value = []
        activeTab.value = 'library'
    }
})

function toggleId(id) {
    if (localIds.value.includes(id)) {
        localIds.value = localIds.value.filter(i => i !== id)
    } else {
        localIds.value = [...localIds.value, id]
    }
}

function isSelected(id) {
    return localIds.value.includes(id)
}

function isImage(mimeType) {
    return mimeType?.startsWith('image/')
}

function confirm() {
    emit('confirm', { mediaIds: localIds.value, newFiles: localFiles.value })
    emit('close')
}
</script>

<template>
    <Modal :show="show" max-width="xl" @close="$emit('close')">
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-900">Select Media</h2>
            <button
                type="button"
                class="p-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-colors"
                @click="$emit('close')"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Tab bar -->
        <div class="flex border-b border-gray-100 px-6">
            <button
                type="button"
                class="px-4 py-3 text-sm font-medium border-b-2 transition-colors"
                :class="activeTab === 'library'
                    ? 'border-gray-900 text-gray-900'
                    : 'border-transparent text-gray-500 hover:text-gray-700'"
                @click="activeTab = 'library'"
            >
                Library
            </button>
            <button
                type="button"
                class="px-4 py-3 text-sm font-medium border-b-2 transition-colors"
                :class="activeTab === 'upload'
                    ? 'border-gray-900 text-gray-900'
                    : 'border-transparent text-gray-500 hover:text-gray-700'"
                @click="activeTab = 'upload'"
            >
                Upload
                <span v-if="localFiles.length" class="ml-1.5 inline-flex items-center justify-center w-4 h-4 bg-gray-900 text-white text-xs rounded-full">{{ localFiles.length }}</span>
            </button>
        </div>

        <!-- Body -->
        <div class="p-6 min-h-64 max-h-[60vh] overflow-y-auto">
            <!-- Library tab -->
            <div v-show="activeTab === 'library'">
                <div v-if="media.length === 0" class="flex flex-col items-center justify-center py-12 text-center">
                    <svg class="h-10 w-10 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p class="text-sm text-gray-500">No media yet.</p>
                    <button type="button" class="mt-1 text-sm text-gray-700 font-medium hover:underline" @click="activeTab = 'upload'">
                        Switch to Upload tab to add files.
                    </button>
                </div>

                <div v-else class="grid grid-cols-3 lg:grid-cols-4 gap-3">
                    <button
                        v-for="item in media"
                        :key="item.id"
                        type="button"
                        class="relative group rounded-xl overflow-hidden border-2 transition-all focus:outline-none"
                        :class="isSelected(item.id)
                            ? 'border-gray-900'
                            : 'border-transparent hover:border-gray-300'"
                        @click="toggleId(item.id)"
                    >
                        <!-- Thumbnail -->
                        <div class="aspect-square bg-gray-100 flex items-center justify-center overflow-hidden">
                            <img
                                v-if="isImage(item.mime_type)"
                                :src="`/storage/${item.path}`"
                                :alt="item.filename"
                                class="w-full h-full object-cover"
                            />
                            <svg
                                v-else
                                class="h-8 w-8 text-gray-300"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.5"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>

                        <!-- Checkmark overlay -->
                        <div
                            v-if="isSelected(item.id)"
                            class="absolute inset-0 bg-gray-900/20 flex items-start justify-end p-1.5"
                        >
                            <span class="flex items-center justify-center w-5 h-5 bg-gray-900 rounded-full">
                                <svg class="h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            </span>
                        </div>

                        <!-- Filename -->
                        <p class="px-1.5 py-1 text-xs text-gray-700 truncate text-left">{{ item.filename }}</p>
                    </button>
                </div>
            </div>

            <!-- Upload tab -->
            <div v-show="activeTab === 'upload'">
                <FileInput v-model="localFiles" />
            </div>
        </div>

        <!-- Footer -->
        <div class="flex items-center justify-between gap-3 px-6 py-4 bg-gray-100">
            <p class="text-xs text-gray-500">
                <template v-if="localIds.length || localFiles.length">
                    {{ localIds.length }} from library<template v-if="localFiles.length">, {{ localFiles.length }} to upload</template>
                </template>
                <template v-else>Nothing selected</template>
            </p>
            <div class="flex gap-3">
                <button
                    type="button"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
                    @click="$emit('close')"
                >
                    Cancel
                </button>
                <button
                    type="button"
                    :disabled="localIds.length === 0 && localFiles.length === 0"
                    class="px-5 py-2 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-700 disabled:opacity-50 transition-colors"
                    @click="confirm"
                >
                    Confirm
                </button>
            </div>
        </div>
    </Modal>
</template>
