<script setup>
import AppLayout from '../../Layout/AppLayout.vue'
import ConfirmationModal from '../../Components/ConfirmationModal.vue'
import DangerButton from '../../Components/DangerButton.vue'
import Paginator from '../../Components/Paginator.vue'
import SecondaryButton from '../../Components/SecondaryButton.vue'
import { Link, router, setLayoutProps } from '@inertiajs/vue3'
import { ref } from 'vue'

defineOptions({ layout: AppLayout })
setLayoutProps({ title: 'Categories' })

const props = defineProps({
    categories: Object,
})

const categoryToDelete = ref(null)
const deleting = ref(false)

function openDeleteModal(category) {
    categoryToDelete.value = category
}

function closeDeleteModal() {
    categoryToDelete.value = null
}

function confirmDelete() {
    deleting.value = true
    router.delete(`/categories/${categoryToDelete.value.id}`, {
        onSuccess: closeDeleteModal,
        onFinish: () => { deleting.value = false },
    })
}
</script>

<template>
    <div class="space-y-5">
        <!-- Toolbar -->
        <div class="flex items-center justify-end">
            <Link
                href="/categories/create"
                class="flex items-center gap-2 px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                New Category
            </Link>
        </div>

        <!-- Empty state -->
        <div
            v-if="categories.data.length === 0"
            class="bg-white rounded-2xl border border-gray-100 shadow-sm flex flex-col items-center justify-center py-20 text-center"
        >
            <div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center mb-4">
                <svg class="h-7 w-7 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                </svg>
            </div>
            <h3 class="text-base font-semibold text-gray-900 mb-1">No categories yet</h3>
            <p class="text-sm text-gray-500 mb-6">Organise your posts by adding your first category.</p>
            <Link
                href="/categories/create"
                class="px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors"
            >
                Add your first category
            </Link>
        </div>

        <!-- Categories table -->
        <div
            v-else
            class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden"
        >
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-gray-400 uppercase tracking-wide border-b border-gray-100">
                        <th class="px-6 py-3 font-medium">Name</th>
                        <th class="px-6 py-3 font-medium hidden md:table-cell">Slug</th>
                        <th class="px-6 py-3 font-medium hidden lg:table-cell">Description</th>
                        <th class="px-6 py-3 font-medium text-right">Posts</th>
                        <th class="px-6 py-3 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <tr
                        v-for="category in categories.data"
                        :key="category.id"
                        class="hover:bg-gray-50 transition-colors"
                    >
                        <!-- Name -->
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-900">{{ category.name }}</p>
                        </td>

                        <!-- Slug -->
                        <td class="px-6 py-4 hidden md:table-cell">
                            <span class="font-mono text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-md">
                                {{ category.slug }}
                            </span>
                        </td>

                        <!-- Description -->
                        <td class="px-6 py-4 text-gray-500 hidden lg:table-cell max-w-xs truncate">
                            {{ category.description ?? '—' }}
                        </td>

                        <!-- Posts count -->
                        <td class="px-6 py-4 text-gray-500 text-right">
                            {{ category.posts_count > 0 ? category.posts_count.toLocaleString() : '—' }}
                        </td>

                        <!-- Actions -->
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <Link
                                    :href="`/categories/${category.id}/edit`"
                                    class="text-xs font-medium text-indigo-600 hover:text-indigo-800 transition-colors"
                                >
                                    Edit
                                </Link>
                                <DangerButton @click="openDeleteModal(category)">Delete</DangerButton>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Pagination -->
            <Paginator
                :links="categories.links"
                :from="categories.from"
                :to="categories.to"
                :total="categories.total"
            />
        </div>
    </div>

    <ConfirmationModal
        :show="!!categoryToDelete"
        max-width="md"
        @close="closeDeleteModal"
    >
        <template #title>Delete Category</template>

        <template #content>
            Are you sure you want to delete
            <span class="font-semibold">{{ categoryToDelete?.name }}</span>?
            Posts in this category will not be deleted but will lose this category association.
        </template>

        <template #footer>
            <SecondaryButton @click="closeDeleteModal">Cancel</SecondaryButton>
            <DangerButton
                data-test="delete"
                class="ms-3"
                :disabled="deleting"
                @click="confirmDelete"
            >
                {{ deleting ? 'Deleting…' : 'Delete' }}
            </DangerButton>
        </template>
    </ConfirmationModal>
</template>
