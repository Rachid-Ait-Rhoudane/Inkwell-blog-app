<script setup>
import AppLayout from '../../Layout/AppLayout.vue'
import InputError from '../../Components/InputError.vue'
import InputLabel from '../../Components/InputLabel.vue'
import PrimaryButton from '../../Components/PrimaryButton.vue'
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

            <PrimaryButton type="button" :disabled="form.processing" @click="submit">
                {{ form.processing ? 'Saving…' : 'Save Category' }}
            </PrimaryButton>
        </div>

        <!-- Body -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
            <!-- Main content -->
            <div class="lg:col-span-2">
                <!-- Name + Slug -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
                    <div>
                        <InputLabel for="name" value="Name" />
                        <TextInput
                            id="name"
                            v-model="form.name"
                            placeholder="Category name…"
                        />
                        <InputError :message="form.errors.name" />
                    </div>

                    <div>
                        <InputLabel for="slug">Slug <span class="font-normal text-gray-400 text-xs ml-1">(auto-generated)</span></InputLabel>
                        <SlugInput
                            id="slug"
                            v-model="form.slug"
                            prefix="/categories/"
                            placeholder="category-slug"
                            @input="slugManuallyEdited = true"
                        />
                        <InputError :message="form.errors.slug" />
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div>
                <!-- Description -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <InputLabel for="description">Description <span class="font-normal text-gray-400 text-xs ml-1">(optional)</span></InputLabel>
                    <TextArea
                        id="description"
                        v-model="form.description"
                        rows="5"
                        maxlength="1000"
                        placeholder="A short description of this category…"
                    />
                    <div class="flex items-start justify-between mt-1 gap-2">
                        <InputError :message="form.errors.description" />
                        <p class="text-xs text-gray-400 ml-auto shrink-0">{{ form.description.length }}/1000</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
