<script setup>
import Navbar from '../Welcome/Partials/Navbar.vue'
import TextArea from '../../Components/TextArea.vue'
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const { auth } = usePage().props

const props = defineProps({
    post: Object,
})

const form = useForm({ body: '' })

function submitComment() {
    form.post(`/posts/${props.post.slug}/comments`, {
        onSuccess: () => form.reset(),
    })
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}

const publishedDate = computed(() =>
    formatDate(props.post.published_at ?? props.post.created_at)
)

function initials(name) {
    return name
        .split(' ')
        .slice(0, 2)
        .map(w => w[0])
        .join('')
        .toUpperCase()
}

function isImage(file) {
    return file.mime_type?.startsWith('image/')
}

function formatSize(bytes) {
    if (bytes < 1024) return `${bytes} B`
    if (bytes < 1048576) return `${(bytes / 1024).toFixed(1)} KB`
    return `${(bytes / 1048576).toFixed(1)} MB`
}

const images = computed(() => props.post.media?.filter(m => isImage(m)) ?? [])
const attachments = computed(() => props.post.media?.filter(m => !isImage(m)) ?? [])
</script>

<template>
    <Head :title="post.title" />

    <div class="min-h-screen bg-gray-50 flex flex-col">
        <Navbar />

        <main class="flex-1 py-10 px-6">
            <div class="max-w-3xl mx-auto space-y-6">
                <!-- Back link -->
                <Link
                    href="/"
                    class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-900 transition-colors"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back
                </Link>

                <!-- Header card -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
                    <!-- Categories -->
                    <div v-if="post.categories?.length" class="flex flex-wrap gap-1.5">
                        <span
                            v-for="cat in post.categories"
                            :key="cat.id"
                            class="inline-flex items-center px-2 py-0.5 bg-gray-100 text-gray-600 text-xs font-medium rounded-full"
                        >
                            {{ cat.name }}
                        </span>
                    </div>

                    <!-- Title -->
                    <h1 class="text-2xl font-bold text-gray-900 leading-tight">{{ post.title }}</h1>

                    <!-- Meta -->
                    <div class="flex items-center gap-2 text-sm text-gray-400">
                        <span v-if="post.user">{{ post.user.name }}</span>
                        <span>·</span>
                        <span>{{ publishedDate }}</span>
                    </div>
                </div>

                <!-- Content card -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <p class="text-gray-800 leading-relaxed whitespace-pre-wrap">{{ post.content }}</p>
                </div>

                <!-- Media section -->
                <div
                    v-if="post.media?.length"
                    class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4"
                >
                    <h2 class="text-base font-semibold text-gray-900">Media</h2>

                    <!-- Image grid -->
                    <div v-if="images.length" class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        <div
                            v-for="img in images"
                            :key="img.id"
                            class="aspect-square rounded-xl overflow-hidden bg-gray-100"
                        >
                            <img
                                :src="`/storage/${img.path}`"
                                :alt="img.filename"
                                class="w-full h-full object-cover"
                            />
                        </div>
                    </div>

                    <!-- Attachments -->
                    <div v-if="attachments.length" class="space-y-2">
                        <a
                            v-for="file in attachments"
                            :key="file.id"
                            :href="`/storage/${file.path}`"
                            target="_blank"
                            class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors"
                        >
                            <div class="h-9 w-9 shrink-0 rounded-lg bg-gray-200 flex items-center justify-center">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">{{ file.filename }}</p>
                                <p class="text-xs text-gray-400">{{ formatSize(file.size) }}</p>
                            </div>
                            <svg class="h-4 w-4 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Comments -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-5">
                    <div class="flex items-center gap-2">
                        <h2 class="text-base font-semibold text-gray-900">Comments</h2>
                        <span class="inline-flex items-center justify-center h-5 min-w-5 px-1.5 bg-gray-100 text-gray-500 text-xs font-medium rounded-full">
                            {{ post.comments.length }}
                        </span>
                    </div>

                    <p v-if="post.comments.length === 0" class="text-sm text-gray-400">
                        No comments yet. Be the first!
                    </p>

                    <div v-else class="space-y-4">
                        <div
                            v-for="comment in post.comments"
                            :key="comment.id"
                            class="flex gap-3"
                        >
                            <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center shrink-0">
                                <span class="text-xs font-medium text-gray-600">{{ initials(comment.user?.name ?? '?') }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-baseline gap-2 mb-1">
                                    <span class="text-sm font-medium text-gray-900">{{ comment.user?.name ?? 'Unknown' }}</span>
                                    <span class="text-xs text-gray-400">{{ formatDate(comment.created_at) }}</span>
                                </div>
                                <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ comment.body }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Comment form (authenticated) -->
                <div v-if="auth.user" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-3">
                    <h2 class="text-base font-semibold text-gray-900">Leave a comment</h2>

                    <TextArea
                        v-model="form.body"
                        rows="4"
                        maxlength="2000"
                        placeholder="Write your comment…"
                    />
                    <div class="flex items-center justify-between gap-2">
                        <p v-if="form.errors.body" class="text-red-500 text-xs">{{ form.errors.body }}</p>
                        <p class="text-xs text-gray-400 ml-auto shrink-0">{{ form.body.length }}/2000</p>
                    </div>

                    <div class="flex justify-end">
                        <button
                            :disabled="form.processing"
                            class="px-5 py-2 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-700 disabled:opacity-50 transition-colors"
                            @click="submitComment"
                        >
                            {{ form.processing ? 'Posting…' : 'Post Comment' }}
                        </button>
                    </div>
                </div>

                <!-- Guest prompt -->
                <div v-else class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 text-center space-y-3">
                    <p class="text-sm font-medium text-gray-700">Want to join the discussion?</p>
                    <p class="text-sm text-gray-400">Sign in or create an account to leave a comment.</p>
                    <div class="flex items-center justify-center gap-3 pt-1">
                        <Link
                            href="/login"
                            class="px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors"
                        >
                            Log in
                        </Link>
                        <Link
                            href="/register"
                            class="px-4 py-2 border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:border-gray-400 hover:bg-gray-50 transition-colors"
                        >
                            Create account
                        </Link>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="border-t border-gray-100 px-6 py-6 mt-10">
            <div class="max-w-5xl mx-auto flex items-center justify-between text-sm text-gray-400">
                <span>© 2026 Inkwell. All rights reserved.</span>
                <nav class="flex gap-6">
                    <a href="#" class="hover:text-gray-600 transition-colors">Privacy</a>
                    <a href="#" class="hover:text-gray-600 transition-colors">Terms</a>
                    <a href="#" class="hover:text-gray-600 transition-colors">Contact</a>
                </nav>
            </div>
        </footer>
    </div>
</template>
