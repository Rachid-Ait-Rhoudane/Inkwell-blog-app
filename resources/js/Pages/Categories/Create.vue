<script setup>
import AppLayout from '../../Layout/AppLayout.vue'
import SlugInput from '../../Components/SlugInput.vue'
import TextArea from '../../Components/TextArea.vue'
import TextInput from '../../Components/TextInput.vue'
import { Link, setLayoutProps, useForm } from '@inertiajs/vue3'
import { ref, watch } from 'vue'

defineOptions({ layout: AppLayout })
setLayoutProps({ title: 'New Category' })

const form = useForm({
    name:        '',
    slug:        '',
    description: '',
})

const slugManuallyEdited = ref(false)

watch(() => form.name, (value) => {
    if (slugManuallyEdited.value) return
    form.slug = value
        .toLowerCase()
        .trim()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .replace(/^-|-$/g, '')
})

function submit() {
    form.post('/categories')
}
</script>

<template>
    <div class="max-w-5xl mx-auto space-y-6">
        <!-- Top bar -->
        <div class="flex items-center justify-between">
            <Link
                href="/categories"
                class="flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-900 transition-colors"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Categories
            </Link>

            <button
                :disabled="form.processing"
                class="px-5 py-2 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-700 disabled:opacity-50 transition-colors"
                @click="submit"
            >
                {{ form.processing ? 'Saving…' : 'Save Category' }}
            </button>
        </div>

        <!-- Body -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
            <!-- Main content -->
            <div class="lg:col-span-2">
                <!-- Name + Slug -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <TextInput
                            id="name"
                            v-model="form.name"
                            placeholder="Category name…"
                        />
                        <p v-if="form.errors.name" class="mt-1 text-red-500 text-xs">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">
                            Slug
                            <span class="font-normal text-gray-400 text-xs ml-1">(auto-generated)</span>
                        </label>
                        <SlugInput
                            id="slug"
                            v-model="form.slug"
                            prefix="/categories/"
                            placeholder="category-slug"
                            @input="slugManuallyEdited = true"
                        />
                        <p v-if="form.errors.slug" class="mt-1 text-red-500 text-xs">{{ form.errors.slug }}</p>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div>
                <!-- Description -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                        Description
                        <span class="font-normal text-gray-400 text-xs ml-1">(optional)</span>
                    </label>
                    <TextArea
                        id="description"
                        v-model="form.description"
                        rows="5"
                        maxlength="1000"
                        placeholder="A short description of this category…"
                    />
                    <div class="flex items-start justify-between mt-1 gap-2">
                        <p v-if="form.errors.description" class="text-red-500 text-xs">{{ form.errors.description }}</p>
                        <p class="text-xs text-gray-400 ml-auto shrink-0">{{ form.description.length }}/1000</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
