<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'

const form = useForm({
    email: '',
    password: '',
    remember: false,
})

function submit() {
    form.post('/login', {
        onError: () => form.reset('password'),
    })
}
</script>

<template>
    <Head title="Login" />

    <div class="min-h-screen bg-gray-50 flex flex-col items-center justify-center px-4">
        <Link href="/" class="text-2xl font-bold text-gray-900 tracking-tight mb-8">
            Inkwell
        </Link>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 w-full max-w-md">
            <h1 class="text-xl font-semibold text-gray-900 mb-1">Welcome back</h1>
            <p class="text-sm text-gray-500 mb-6">Sign in to your account to continue</p>

            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        Email
                    </label>
                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        autocomplete="email"
                        required
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition"
                        placeholder="you@example.com"
                    />
                    <p v-if="form.errors.email" class="mt-1 text-red-500 text-xs">{{ form.errors.email }}</p>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        Password
                    </label>
                    <input
                        id="password"
                        v-model="form.password"
                        type="password"
                        autocomplete="off"
                        required
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition"
                        placeholder="••••••••"
                    />
                    <p v-if="form.errors.password" class="mt-1 text-red-500 text-xs">{{ form.errors.password }}</p>
                </div>

                <div class="flex items-center gap-2">
                    <input
                        id="remember"
                        v-model="form.remember"
                        type="checkbox"
                        class="h-4 w-4 rounded border-gray-300 text-gray-900 focus:ring-gray-900"
                    />
                    <label for="remember" class="text-sm text-gray-600">Remember me</label>
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-700 disabled:opacity-50 transition-colors cursor-pointer"
                >
                    {{ form.processing ? 'Signing in…' : 'Sign in' }}
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-gray-500">
                Don't have an account?
                <Link href="/register" class="text-gray-900 font-medium hover:underline">
                    Sign up
                </Link>
            </p>
        </div>
    </div>
</template>
