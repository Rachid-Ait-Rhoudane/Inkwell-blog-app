<script setup>
import AppLayout from '../../Layout/AppLayout.vue'
import ConfirmationModal from '../../Components/ConfirmationModal.vue'
import DangerButton from '../../Components/DangerButton.vue'
import Paginator from '../../Components/Paginator.vue'
import SecondaryButton from '../../Components/SecondaryButton.vue'
import { Link, router, setLayoutProps } from '@inertiajs/vue3'
import { ref } from 'vue'

defineOptions({ layout: AppLayout })
setLayoutProps({ title: 'Comments' })

const props = defineProps({
    comments: Object,
})

const commentToDelete = ref(null)
const deleting = ref(false)

function openDeleteModal(comment) {
    commentToDelete.value = comment
}

function closeDeleteModal() {
    commentToDelete.value = null
}

function confirmDelete() {
    deleting.value = true
    router.delete(`/comments/${commentToDelete.value.id}`, {
        onSuccess: closeDeleteModal,
        onFinish: () => { deleting.value = false },
    })
}

function formatDate(comment) {
    return new Date(comment.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}
</script>

<template>
    <div class="space-y-5">
        <!-- Empty state -->
        <div
            v-if="comments.data.length === 0"
            class="bg-white rounded-2xl border border-gray-100 shadow-sm flex flex-col items-center justify-center py-20 text-center"
        >
            <div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center mb-4">
                <svg class="h-7 w-7 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-3 3v-3z" />
                </svg>
            </div>
            <h3 class="text-base font-semibold text-gray-900 mb-1">No comments yet</h3>
            <p class="text-sm text-gray-500">You haven't written any comments yet.</p>
        </div>

        <!-- Comments table -->
        <div
            v-else
            class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden"
        >
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-gray-400 uppercase tracking-wide border-b border-gray-100">
                        <th class="px-6 py-3 font-medium">Comment</th>
                        <th class="px-6 py-3 font-medium hidden md:table-cell">Post</th>
                        <th class="px-6 py-3 font-medium hidden lg:table-cell">Date</th>
                        <th class="px-6 py-3 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <tr
                        v-for="comment in comments.data"
                        :key="comment.id"
                        class="hover:bg-gray-50 transition-colors"
                    >
                        <!-- Comment body -->
                        <td class="px-6 py-4">
                            <p class="text-gray-900 line-clamp-2 max-w-sm">{{ comment.body }}</p>
                        </td>

                        <!-- Post -->
                        <td class="px-6 py-4 hidden md:table-cell">
                            <Link
                                v-if="comment.post"
                                :href="`/posts/${comment.post.id}/edit`"
                                class="text-indigo-600 hover:text-indigo-800 font-medium transition-colors line-clamp-1 max-w-xs block"
                            >
                                {{ comment.post.title }}
                            </Link>
                            <span v-else class="text-gray-400">—</span>
                        </td>

                        <!-- Date -->
                        <td class="px-6 py-4 text-gray-500 hidden lg:table-cell">
                            {{ formatDate(comment) }}
                        </td>

                        <!-- Actions -->
                        <td class="px-6 py-4 text-right">
                            <DangerButton @click="openDeleteModal(comment)">Delete</DangerButton>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Pagination -->
            <Paginator
                :links="comments.links"
                :from="comments.from"
                :to="comments.to"
                :total="comments.total"
            />
        </div>
    </div>

    <ConfirmationModal
        :show="!!commentToDelete"
        max-width="md"
        @close="closeDeleteModal"
    >
        <template #title>Delete Comment</template>

        <template #content>
            Are you sure you want to delete this comment? This action cannot be undone.
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
