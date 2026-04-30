<script setup>
import AppLayout from '../../Layout/AppLayout.vue'
import ConfirmationModal from '../../Components/ConfirmationModal.vue'
import DangerButton from '../../Components/DangerButton.vue'
import Paginator from '../../Components/Paginator.vue'
import SecondaryButton from '../../Components/SecondaryButton.vue'
import UploadNewFilesModal from '../../Components/UploadNewFilesModal.vue'
import { router, setLayoutProps } from '@inertiajs/vue3'
import { ref } from 'vue'

defineOptions({ layout: AppLayout })
setLayoutProps({ title: 'Media' })

const props = defineProps({
    media: Object,
})

const showUploadModal = ref(false)
const mediaToDelete = ref(null)
const deleting = ref(false)

function openDeleteModal(item) {
    mediaToDelete.value = item
}

function closeDeleteModal() {
    mediaToDelete.value = null
}

function confirmDelete() {
    deleting.value = true
    router.delete(`/media/${mediaToDelete.value.id}`, {
        onSuccess: closeDeleteModal,
        onFinish: () => { deleting.value = false },
    })
}

function isImage(mimeType) {
    return mimeType?.startsWith('image/')
}

function formatSize(bytes) {
    if (bytes < 1024) return `${bytes} B`
    if (bytes < 1048576) return `${(bytes / 1024).toFixed(1)} KB`
    return `${(bytes / 1048576).toFixed(1)} MB`
}
</script>

<template>
    <div class="space-y-5">
        <!-- Toolbar -->
        <div class="flex items-center justify-between">
            <h1 class="text-lg font-semibold text-gray-900">Media Library</h1>
            <button
                class="flex items-center gap-2 px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors"
                @click="showUploadModal = true"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Add New Files
            </button>
        </div>

        <!-- Empty state -->
        <div
            v-if="media.data.length === 0"
            class="bg-white rounded-2xl border border-gray-100 shadow-sm flex flex-col items-center justify-center py-20 text-center"
        >
            <div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center mb-4">
                <svg class="h-7 w-7 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <h3 class="text-base font-semibold text-gray-900 mb-1">No media yet</h3>
            <p class="text-sm text-gray-500">Upload files when adding media to a post.</p>
        </div>

        <!-- Grid -->
        <div v-else class="space-y-5">
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
                <div
                    v-for="item in media.data"
                    :key="item.id"
                    class="group relative bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden"
                >
                    <!-- Thumbnail -->
                    <div class="aspect-square bg-gray-50 flex items-center justify-center overflow-hidden">
                        <img
                            v-if="isImage(item.mime_type)"
                            :src="`/storage/${item.path}`"
                            :alt="item.filename"
                            class="w-full h-full object-cover"
                        />
                        <svg
                            v-else
                            class="h-10 w-10 text-gray-300"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.5"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>

                    <!-- Delete button (visible on hover) -->
                    <button
                        type="button"
                        class="absolute top-2 right-2 p-1 bg-gray-900/60 rounded-lg text-white opacity-0 group-hover:opacity-100 hover:bg-gray-900 transition-all"
                        @click="openDeleteModal(item)"
                    >
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <!-- Meta -->
                    <div class="p-2.5">
                        <p class="text-xs font-medium text-gray-800 truncate" :title="item.filename">{{ item.filename }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ formatSize(item.size) }}</p>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <Paginator
                    :links="media.links"
                    :from="media.from"
                    :to="media.to"
                    :total="media.total"
                />
            </div>
        </div>
    </div>

    <UploadNewFilesModal :show="showUploadModal" @close="showUploadModal = false" />

    <ConfirmationModal
        :show="!!mediaToDelete"
        max-width="md"
        @close="closeDeleteModal"
    >
        <template #title>Delete File</template>

        <template #content>
            Are you sure you want to delete
            <span class="font-semibold">{{ mediaToDelete?.filename }}</span>?
            This will permanently remove the file from storage.
        </template>

        <template #footer>
            <SecondaryButton @click="closeDeleteModal">Cancel</SecondaryButton>
            <DangerButton
                class="ms-3"
                :disabled="deleting"
                @click="confirmDelete"
            >
                {{ deleting ? 'Deleting…' : 'Delete' }}
            </DangerButton>
        </template>
    </ConfirmationModal>
</template>
