<script setup>
import InputError from '../../Components/InputError.vue'
import InputLabel from '../../Components/InputLabel.vue'
import PrimaryButton from '../../Components/PrimaryButton.vue'
import TextInput from '../../Components/TextInput.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
})

function submit() {
    form.post('/register', {
        onError: () => form.reset('password', 'password_confirmation'),
    })
}
</script>

<template>
    <Head title="Register" />

    <div class="min-h-screen bg-gray-50 flex flex-col items-center justify-center px-4">
        <Link href="/" class="text-2xl font-bold text-gray-900 tracking-tight mb-8">
            Inkwell
        </Link>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 w-full max-w-md">
            <h1 class="text-xl font-semibold text-gray-900 mb-1">Create an account</h1>
            <p class="text-sm text-gray-500 mb-6">Join Inkwell and start writing today</p>

            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <InputLabel for="name" value="Name" />
                    <TextInput
                        id="name"
                        v-model="form.name"
                        type="text"
                        autocomplete="name"
                        required
                        placeholder="Jane Smith"
                    />
                    <InputError :message="form.errors.name" />
                </div>

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
                        autocomplete="new-password"
                        required
                        placeholder="Min. 8 characters"
                    />
                    <InputError :message="form.errors.password" />
                </div>

                <div>
                    <InputLabel for="password_confirmation" value="Confirm password" />
                    <TextInput
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        type="password"
                        autocomplete="new-password"
                        required
                        placeholder="••••••••"
                    />
                    <InputError :message="form.errors.password_confirmation" />
                </div>

                <PrimaryButton class="w-full justify-center" :disabled="form.processing">
                    {{ form.processing ? 'Creating account…' : 'Create account' }}
                </PrimaryButton>
            </form>

            <p class="mt-6 text-center text-sm text-gray-500">
                Already have an account?
                <Link href="/login" class="text-gray-900 font-medium hover:underline">
                    Sign in
                </Link>
            </p>
        </div>
    </div>
</template>
