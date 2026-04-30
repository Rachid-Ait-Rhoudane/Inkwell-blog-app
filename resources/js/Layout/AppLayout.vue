<script setup>
import { computed, ref } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import Dropdown from '../Components/Dropdown.vue'
import DropdownLink from '../Components/DropdownLink.vue'
import FlashBanner from '../Components/FlashBanner.vue'

defineProps({
    title: {
        type: String,
        default: 'Dashboard',
    },
})

const page = usePage()
const user = computed(() => page.props.auth?.user)

const userInitials = computed(() => {
    if (!user.value?.name) return '?'
    return user.value.name
        .split(' ')
        .map((n) => n[0])
        .slice(0, 2)
        .join('')
        .toUpperCase()
})

const sidebarOpen = ref(true)

const isActive = (href) => href !== '#' && page.url.startsWith(href)

const navLinks = [
    {
        label: 'Dashboard',
        href: '/dashboard',
        iconPaths: [
            'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
        ],
    },
    {
        label: 'Posts',
        href: '/posts',
        iconPaths: [
            'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        ],
    },
    {
        label: 'Categories',
        href: '/categories',
        iconPaths: [
            'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z',
        ],
    },
    {
        label: 'Comments',
        href: '/comments',
        iconPaths: [
            'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z',
        ],
    },
    {
        label: 'Media',
        href: '/media',
        iconPaths: [
            'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z',
        ],
    }
]
</script>

<template>

    <Head :title="title" />

    <div class="flex h-screen bg-gray-100 overflow-hidden">
        <!-- Sidebar -->
        <aside
            class="flex flex-col bg-gray-900 text-white shrink-0 transition-all duration-300 ease-in-out"
            :class="sidebarOpen ? 'w-64' : 'w-16'"
        >
            <!-- Logo -->
            <div class="flex items-center h-16 px-4 border-b border-gray-800 overflow-hidden shrink-0">
                <Link href="/" class="text-xl font-bold tracking-tight text-white whitespace-nowrap">
                    {{ sidebarOpen ? 'Inkwell' : 'I' }}
                </Link>
            </div>

            <!-- Nav -->
            <nav class="flex-1 py-3 overflow-y-auto overflow-x-hidden">
                <Link
                    v-for="link in navLinks"
                    :key="link.label"
                    :href="link.href"
                    class="flex items-center h-10 px-3 mx-2 mb-0.5 rounded-lg transition-colors"
                    :class="isActive(link.href)
                        ? 'bg-gray-800 text-white'
                        : 'text-gray-400 hover:bg-gray-800 hover:text-white'"
                >
                    <svg
                        class="h-5 w-5 shrink-0"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.5"
                    >
                        <path
                            v-for="d in link.iconPaths"
                            :key="d"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            :d="d"
                        />
                    </svg>
                    <span
                        class="ml-3 text-sm font-medium whitespace-nowrap transition-all duration-200 overflow-hidden"
                        :class="sidebarOpen ? 'opacity-100 max-w-xs' : 'opacity-0 max-w-0'"
                    >
                        {{ link.label }}
                    </span>
                </Link>
            </nav>
        </aside>

        <!-- Right panel -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Header -->
            <header class="flex items-center justify-between h-16 px-6 bg-white border-b border-gray-200 shrink-0">
                <!-- Left: toggle + title -->
                <div class="flex items-center gap-4">
                    <button
                        class="p-1.5 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-900 transition-colors"
                        @click="sidebarOpen = !sidebarOpen"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <h1 class="text-lg font-semibold text-gray-900">{{ title }}</h1>
                </div>

                <!-- Right: user dropdown -->
                <Dropdown>
                    <template #trigger="{ open, toggle }">
                        <button
                            class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl hover:bg-gray-100 transition-colors"
                            @click="toggle"
                        >
                            <div class="h-8 w-8 rounded-full bg-indigo-600 flex items-center justify-center text-white text-xs font-bold shrink-0">
                                {{ userInitials }}
                            </div>
                            <div class="hidden sm:block text-left">
                                <p class="text-sm font-medium text-gray-900 leading-tight">{{ user?.name }}</p>
                                <p class="text-xs text-gray-500">{{ user?.email }}</p>
                            </div>
                            <svg
                                class="h-4 w-4 text-gray-400 transition-transform duration-200"
                                :class="{ 'rotate-180': open }"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                    </template>

                    <template #default="{ close }">
                        <DropdownLink href="#" @click="close">
                            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Profile
                        </DropdownLink>
                        <DropdownLink href="#" @click="close">
                            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                            </svg>
                            Settings
                        </DropdownLink>
                        <div class="my-1 border-t border-gray-100" />
                        <DropdownLink href="/logout" method="post" as="button" variant="danger" @click="close">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Log out
                        </DropdownLink>
                    </template>
                </Dropdown>
            </header>

            <!-- Main content -->
            <main class="flex-1 overflow-auto p-6">
                <FlashBanner />
                <slot />
            </main>
        </div>
    </div>
</template>
