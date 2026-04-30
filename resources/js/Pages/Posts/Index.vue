<script setup>
import AppLayout from '../../Layout/AppLayout.vue'
import ConfirmationModal from '../../Components/ConfirmationModal.vue'
import DangerButton from '../../Components/DangerButton.vue'
import Paginator from '../../Components/Paginator.vue'
import { Link, router, setLayoutProps } from '@inertiajs/vue3'
import { ref } from 'vue'

defineOptions({ layout: AppLayout })
setLayoutProps({ title: 'Posts' })

const props = defineProps({
    posts: Object,
    filters: Object,
})

const tabs = [
    { label: 'All', value: '' },
    { label: 'Published', value: 'published' },
    { label: 'Draft', value: 'draft' },
]

function filterByStatus(status) {
    router.get('/posts', { status }, { preserveState: true, replace: true })
}

const postToDelete = ref(null)
const deleting = ref(false)

function openDeleteModal(post) {
    postToDelete.value = post
}

function closeDeleteModal() {
    postToDelete.value = null
}

function confirmDelete() {
    deleting.value = true
    router.delete(`/posts/${postToDelete.value.id}`, {
        onSuccess: closeDeleteModal,
        onFinish: () => { deleting.value = false },
    })
}

function formatDate(post) {
    const raw = post.status === 'published' && post.published_at
        ? post.published_at
        : post.created_at
    return new Date(raw).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}
</script>

<template>
    <div class="space-y-5">
        <!-- Toolbar -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-1 bg-gray-100 rounded-xl p-1">
                <button
                    v-for="tab in tabs"
                    :key="tab.value"
                    class="px-4 py-1.5 text-sm font-medium rounded-lg transition-colors"
                    :class="(filters.status ?? '') === tab.value
                        ? 'bg-white text-gray-900 shadow-sm'
                        : 'text-gray-500 hover:text-gray-700'"
                    @click="filterByStatus(tab.value)"
                >
                    {{ tab.label }}
                </button>
            </div>

            <Link
                href="/posts/create"
                class="flex items-center gap-2 px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors uppercase"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                New Post
            </Link>
        </div>

        <!-- Empty state -->
        <div
            v-if="posts.data.length === 0"
            class="bg-white rounded-2xl border border-gray-100 shadow-sm flex flex-col items-center justify-center py-20 text-center"
        >
            <div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center mb-4">
                <svg class="h-7 w-7 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <h3 class="text-base font-semibold text-gray-900 mb-1">No posts yet</h3>
            <p class="text-sm text-gray-500 mb-6">
                {{ (filters.status ?? '') !== '' ? `You have no ${filters.status} posts.` : 'Start writing your first blog post.' }}
            </p>
            <Link
                href="/posts/create"
                class="px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors uppercase"
            >
                Write your first post
            </Link>
        </div>

        <!-- Posts table -->
        <div
            v-else
            class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden"
        >
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-gray-400 uppercase tracking-wide border-b border-gray-100">
                        <th class="px-6 py-3 font-medium">Post</th>
                        <th class="px-6 py-3 font-medium">Status</th>
                        <th class="px-6 py-3 font-medium hidden md:table-cell">Date</th>
                        <th class="px-6 py-3 font-medium hidden lg:table-cell text-right">Views</th>
                        <th class="px-6 py-3 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <tr
                        v-for="post in posts.data"
                        :key="post.id"
                        class="hover:bg-gray-50 transition-colors"
                    >
                        <!-- Post: thumbnail + title + excerpt -->
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div
                                    class="h-12 w-16 rounded-lg bg-gray-100 shrink-0 overflow-hidden"
                                >
                                    <img
                                        v-if="post.first_image_path"
                                        :src="`/storage/${post.first_image_path}`"
                                        :alt="post.title"
                                        class="h-full w-full object-cover"
                                    />
                                    <div
                                        v-else
                                        class="h-full w-full flex items-center justify-center"
                                    >
                                        <svg class="h-5 w-5 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="min-w-0">
                                    <p class="font-medium text-gray-900 truncate max-w-xs">{{ post.title }}</p>
                                    <p v-if="post.excerpt" class="text-xs text-gray-400 truncate max-w-xs mt-0.5">{{ post.excerpt }}</p>
                                    <div v-if="post.categories?.length" class="flex flex-wrap gap-1 mt-1.5">
                                        <span
                                            v-for="cat in post.categories"
                                            :key="cat.id"
                                            class="inline-flex items-center px-1.5 py-0.5 bg-gray-100 text-gray-500 text-xs rounded"
                                        >
                                            {{ cat.name }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Status badge -->
                        <td class="px-6 py-4">
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                :class="post.status === 'published'
                                    ? 'bg-green-100 text-green-700'
                                    : 'bg-amber-100 text-amber-700'"
                            >
                                {{ post.status === 'published' ? 'Published' : 'Draft' }}
                            </span>
                        </td>

                        <!-- Date -->
                        <td class="px-6 py-4 text-gray-500 hidden md:table-cell">
                            {{ formatDate(post) }}
                        </td>

                        <!-- Views -->
                        <td class="px-6 py-4 text-gray-500 hidden lg:table-cell text-right">
                            {{ post.views > 0 ? post.views.toLocaleString() : '—' }}
                        </td>

                        <!-- Actions -->
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <Link
                                    :href="`/posts/${post.slug}`"
                                    class="text-gray-400 hover:text-gray-700 transition-colors"
                                    title="View post"
                                >
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </Link>
                                <Link
                                    :href="`/posts/${post.id}/edit`"
                                    class="text-xs font-medium text-indigo-600 hover:text-indigo-800 transition-colors"
                                >
                                    Edit
                                </Link>
                                <DangerButton @click="openDeleteModal(post)">Delete</DangerButton>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Pagination -->
            <Paginator
                :links="posts.links"
                :from="posts.from"
                :to="posts.to"
                :total="posts.total"
            />
        </div>
    </div>

    <ConfirmationModal
        :show="!!postToDelete"
        max-width="md"
        @close="closeDeleteModal"
    >
        <template #title>Delete Post</template>

        <template #content>
            Are you sure you want to delete
            <span class="font-semibold">{{ postToDelete?.title }}</span>?
            This action cannot be undone.
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
