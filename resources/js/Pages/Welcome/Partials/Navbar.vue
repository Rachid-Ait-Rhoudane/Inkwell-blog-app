<script setup>
import { Link, usePage } from '@inertiajs/vue3'

const { auth } = usePage().props
</script>

<template>
    <header class="bg-white border-b border-gray-100 px-6 py-4">
        <div class="max-w-5xl mx-auto flex items-center justify-between">
            <Link href="/" class="text-xl font-bold text-gray-900 tracking-tight">
                Inkwell
            </Link>

            <nav class="flex items-center gap-3">
                <template v-if="auth.user">
                    <Link
                        href="/dashboard"
                        class="px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors"
                    >
                        Dashboard
                    </Link>
                    <Link
                        href="/logout"
                        method="post"
                        as="button"
                        class="text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors cursor-pointer"
                    >
                        Log out
                    </Link>
                </template>
                <template v-else>
                    <Link
                        href="/login"
                        class="text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors"
                    >
                        Log in
                    </Link>
                    <Link
                        href="/register"
                        class="px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors"
                    >
                        Get started
                    </Link>
                </template>
            </nav>
        </div>
    </header>
</template>
