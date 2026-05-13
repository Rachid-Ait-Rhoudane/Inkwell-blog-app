<script setup>
import InputError from '../../Components/InputError.vue'
import InputLabel from '../../Components/InputLabel.vue'
import PrimaryButton from '../../Components/PrimaryButton.vue'
import TextInput from '../../Components/TextInput.vue'
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
                    <InputLabel for="email" value="Email" />
                    <TextInput
                        id="email"
                        v-model="form.email"
                        type="email"
                        autocomplete="email"
                        required
                        placeholder="you@example.com"
                    />
                    <InputError :message="form.errors.email" />
                </div>

                <div>
                    <InputLabel for="password" value="Password" />
                    <TextInput
                        id="password"
                        v-model="form.password"
                        type="password"
                        autocomplete="off"
                        required
                        placeholder="••••••••"
                    />
                    <InputError :message="form.errors.password" />
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

                <PrimaryButton class="w-full justify-center" :disabled="form.processing">
                    {{ form.processing ? 'Signing in…' : 'Sign in' }}
                </PrimaryButton>
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
