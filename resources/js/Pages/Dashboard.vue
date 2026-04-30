<script setup>
import AppLayout from '../Layout/AppLayout.vue'
import { Link, setLayoutProps } from '@inertiajs/vue3'

defineOptions({ layout: AppLayout })
setLayoutProps({ title: 'Dashboard' })

const props = defineProps({
    stats: Object,
    recentPosts: Array,
    recentComments: Array,
})

function formatDate(raw) {
    return new Date(raw).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}

function postDate(post) {
    return formatDate(post.status === 'published' && post.published_at ? post.published_at : post.created_at)
}

function initials(name) {
    return name
        .split(' ')
        .slice(0, 2)
        .map(w => w[0])
        .join('')
        .toUpperCase()
}
</script>

<template>
    <div class="space-y-6">
        <!-- Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <p class="text-sm text-gray-500">Total Posts</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ stats.totalPosts }}</p>
                <p class="text-xs mt-2 font-medium text-green-600">
                    {{ stats.postsThisMonth > 0 ? `+${stats.postsThisMonth} this month` : 'No new posts this month' }}
                </p>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <p class="text-sm text-gray-500">Published</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ stats.publishedPosts }}</p>
                <p class="text-xs mt-2 font-medium text-gray-400">
                    {{ stats.draftPosts }} draft{{ stats.draftPosts !== 1 ? 's' : '' }}
                </p>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <p class="text-sm text-gray-500">Total Views</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ stats.totalViews.toLocaleString() }}</p>
                <p class="text-xs mt-2 font-medium text-gray-400">across all posts</p>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <p class="text-sm text-gray-500">Comments</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ stats.totalComments }}</p>
                <p class="text-xs mt-2 font-medium text-gray-400">on your posts</p>
            </div>
        </div>

        <!-- Recent posts + Quick actions -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <!-- Recent posts table -->
            <div class="xl:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-900">Recent Posts</h2>
                    <Link href="/posts" class="text-xs text-indigo-600 font-medium hover:underline">View all</Link>
                </div>

                <div v-if="recentPosts.length === 0" class="px-6 py-10 text-center text-sm text-gray-400">
                    No posts yet. <Link href="/posts/create" class="text-indigo-600 hover:underline">Write your first post.</Link>
                </div>

                <table v-else class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs text-gray-400 uppercase tracking-wide border-b border-gray-100">
                            <th class="px-6 py-3 font-medium">Title</th>
                            <th class="px-6 py-3 font-medium">Status</th>
                            <th class="px-6 py-3 font-medium hidden md:table-cell">Date</th>
                            <th class="px-6 py-3 font-medium hidden md:table-cell text-right">Views</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr
                            v-for="post in recentPosts"
                            :key="post.id"
                            class="hover:bg-gray-50 transition-colors"
                        >
                            <td class="px-6 py-4 font-medium text-gray-900 max-w-xs">
                                <Link :href="`/posts/${post.slug}`" class="hover:text-indigo-600 transition-colors truncate block max-w-xs">
                                    {{ post.title }}
                                </Link>
                            </td>
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
                            <td class="px-6 py-4 text-gray-500 hidden md:table-cell">{{ postDate(post) }}</td>
                            <td class="px-6 py-4 text-gray-500 hidden md:table-cell text-right">
                                {{ post.views > 0 ? post.views.toLocaleString() : '—' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Quick actions -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex flex-col gap-4">
                <h2 class="text-sm font-semibold text-gray-900">Quick Actions</h2>

                <Link
                    href="/posts/create"
                    class="flex items-center gap-3 px-4 py-3 bg-gray-900 text-white rounded-xl hover:bg-gray-700 transition-colors"
                >
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    <span class="text-sm font-medium">Write new post</span>
                </Link>

                <Link
                    href="/categories/create"
                    class="flex items-center gap-3 px-4 py-3 border border-gray-200 text-gray-700 rounded-xl hover:border-gray-400 transition-colors"
                >
                    <svg class="h-5 w-5 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                    </svg>
                    <span class="text-sm font-medium">Add category</span>
                </Link>

                <Link
                    href="/media"
                    class="flex items-center gap-3 px-4 py-3 border border-gray-200 text-gray-700 rounded-xl hover:border-gray-400 transition-colors"
                >
                    <svg class="h-5 w-5 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="text-sm font-medium">Upload media</span>
                </Link>

                <Link
                    href="/comments"
                    class="flex items-center gap-3 px-4 py-3 border border-gray-200 text-gray-700 rounded-xl hover:border-gray-400 transition-colors"
                >
                    <svg class="h-5 w-5 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <span class="text-sm font-medium">Review comments</span>
                </Link>
            </div>
        </div>

        <!-- Recent comments -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-900">Recent Comments</h2>
                <Link href="/comments" class="text-xs text-indigo-600 font-medium hover:underline">View all</Link>
            </div>

            <div v-if="recentComments.length === 0" class="px-6 py-10 text-center text-sm text-gray-400">
                No comments on your posts yet.
            </div>

            <ul v-else class="divide-y divide-gray-50">
                <li
                    v-for="comment in recentComments"
                    :key="comment.id"
                    class="flex items-start gap-4 px-6 py-4 hover:bg-gray-50 transition-colors"
                >
                    <!-- Avatar -->
                    <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center shrink-0 mt-0.5">
                        <span class="text-xs font-medium text-gray-600">{{ initials(comment.user?.name ?? '?') }}</span>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-baseline gap-2 flex-wrap">
                            <span class="text-sm font-medium text-gray-900">{{ comment.user?.name ?? 'Unknown' }}</span>
                            <span class="text-xs text-gray-400">on</span>
                            <Link
                                :href="`/posts/${comment.post?.slug}`"
                                class="text-xs text-indigo-600 hover:underline truncate"
                            >
                                {{ comment.post?.title }}
                            </Link>
                            <span class="text-xs text-gray-400 ml-auto shrink-0">{{ formatDate(comment.created_at) }}</span>
                        </div>
                        <p class="text-sm text-gray-600 mt-1 line-clamp-2">{{ comment.body }}</p>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</template>
