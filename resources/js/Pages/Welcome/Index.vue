<script setup>
import Navbar from './Partials/Navbar.vue'
import { Head, Link, usePage } from '@inertiajs/vue3'

const { auth } = usePage().props
</script>

<template>
    <Head title="Welcome" />

    <div class="min-h-screen bg-white flex flex-col">
        <Navbar />

        <!-- Hero -->
        <main class="flex-1 flex flex-col items-center justify-center px-6 text-center">
            <span class="inline-block mb-6 px-3 py-1 bg-indigo-50 text-indigo-600 text-xs font-semibold tracking-wide rounded-full uppercase">
                A place for ideas
            </span>

            <h1 class="text-5xl font-extrabold text-gray-900 leading-tight max-w-2xl mb-5">
                Write, share, and discover great stories
            </h1>

            <p class="text-lg text-gray-500 max-w-xl mb-10">
                Inkwell is a clean, minimal blogging platform where writers focus on what matters — their words.
            </p>

            <div class="flex items-center gap-4">
                <template v-if="auth.user">
                    <Link
                        href="/dashboard"
                        class="px-6 py-3 bg-gray-900 text-white font-semibold rounded-xl hover:bg-gray-700 transition-colors"
                    >
                        Go to Dashboard
                    </Link>
                </template>
                <template v-else>
                    <Link
                        href="/register"
                        class="px-6 py-3 bg-gray-900 text-white font-semibold rounded-xl hover:bg-gray-700 transition-colors"
                    >
                        Start writing for free
                    </Link>
                    <Link
                        href="#"
                        class="px-6 py-3 border border-gray-200 text-gray-700 font-semibold rounded-xl hover:border-gray-400 transition-colors"
                    >
                        Explore posts
                    </Link>
                </template>
            </div>
        </main>

        <!-- Features -->
        <section class="py-16 px-6 bg-gray-50">
            <div class="max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                    <div class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center mb-4 text-xl">
                        ✍️
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Distraction-free writing</h3>
                    <p class="text-sm text-gray-500">A minimal editor that keeps you focused on your ideas, nothing else.</p>
                </div>

                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                    <div class="w-10 h-10 bg-green-100 text-green-600 rounded-xl flex items-center justify-center mb-4 text-xl">
                        🌍
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Built-in audience</h3>
                    <p class="text-sm text-gray-500">Publish and instantly reach readers who care about what you write.</p>
                </div>

                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                    <div class="w-10 h-10 bg-yellow-100 text-yellow-600 rounded-xl flex items-center justify-center mb-4 text-xl">
                        ⚡
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Lightning fast</h3>
                    <p class="text-sm text-gray-500">Pages load instantly so readers never wait and writers never lose flow.</p>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="border-t border-gray-100 px-6 py-6">
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
