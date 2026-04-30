<script setup>
import FileInput from './FileInput.vue'
import Modal from './Modal.vue'
import { useForm } from '@inertiajs/vue3'

defineProps({
    show: { type: Boolean, default: false },
})

const emit = defineEmits(['close'])

const form = useForm({ files: [] })

function close() {
    form.reset()
    emit('close')
}

function submit() {
    form.post('/media', {
        onSuccess: close,
    })
}
</script>

<template>
    <Modal :show="show" max-width="lg" @close="close">
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-900">Upload Files</h2>
            <button
                type="button"
                class="p-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-colors"
                @click="close"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Body -->
        <div class="p-6 space-y-4">
            <FileInput v-model="form.files" />
            <p v-if="form.errors.files" class="text-red-500 text-xs">{{ form.errors.files }}</p>
        </div>

        <!-- Footer -->
        <div class="flex items-center justify-end gap-3 px-6 py-4 bg-gray-100">
            <button
                type="button"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
                @click="close"
            >
                Cancel
            </button>
            <button
                type="button"
                :disabled="form.processing || form.files.length === 0"
                class="px-5 py-2 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-700 disabled:opacity-50 transition-colors"
                @click="submit"
            >
                {{ form.processing ? 'Uploading…' : `Upload ${form.files.length > 0 ? form.files.length + ' file' + (form.files.length > 1 ? 's' : '') : ''}` }}
            </button>
        </div>
    </Modal>
</template>
