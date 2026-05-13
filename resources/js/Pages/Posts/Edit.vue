<script setup>
import AppLayout from '../../Layout/AppLayout.vue'
import InputError from '../../Components/InputError.vue'
import InputLabel from '../../Components/InputLabel.vue'
import PrimaryButton from '../../Components/PrimaryButton.vue'
import SelectCategory from '../../Components/SelectCategory.vue'
import SelectInput from '../../Components/SelectInput.vue'
import SelectMediaFilesModal from '../../Components/SelectMediaFilesModal.vue'
import SlugInput from '../../Components/SlugInput.vue'
import TextArea from '../../Components/TextArea.vue'
import TextInput from '../../Components/TextInput.vue'
import { Link, setLayoutProps, useForm } from '@inertiajs/vue3'
import { computed, ref, watch } from 'vue'

defineOptions({ layout: AppLayout })
setLayoutProps({ title: 'Edit Post' })

const props = defineProps({
    post:       Object,
    categories: { type: Array, default: () => [] },
    userMedia:  { type: Array, default: () => [] },
})

const form = useForm({
    title:        props.post.title,
    slug:         props.post.slug,
    content:      props.post.content,
    excerpt:      props.post.excerpt ?? '',
    status:       props.post.status,
    published_at: props.post.published_at ? props.post.published_at.slice(0, 16) : '',
    category_ids: props.post.categories?.map(c => c.id) ?? [],
    media_ids:    props.post.media?.map(m => m.id) ?? [],
    media_files:  [],
    _method:      'PUT',
})

const slugManuallyEdited = ref(true)
const showMediaModal = ref(false)
const pendingFiles = ref([])

watch(() => form.title, (value) => {
    if (slugManuallyEdited.value) return
    form.slug = value
        .toLowerCase()
        .trim()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .replace(/^-|-$/g, '')
})

const selectedMedia = computed(() =>
    props.userMedia.filter(m => form.media_ids.includes(m.id))
)

function onMediaConfirm({ mediaIds, newFiles }) {
    form.media_ids = mediaIds
    pendingFiles.value = newFiles
    form.media_files = newFiles
}

function removeMedia(id) {
    form.media_ids = form.media_ids.filter(i => i !== id)
}

function removePending(index) {
    pendingFiles.value = pendingFiles.value.filter((_, i) => i !== index)
    form.media_files = [...pendingFiles.value]
}

function pendingPreview(file) {
    return URL.createObjectURL(file)
}

const submitLabel = computed(() =>
    form.processing ? 'Saving…' : 'Update'
)

function submit() {
    form.post(`/posts/${props.post.id}`)
}
</script>

<template>
    <div class="max-w-5xl mx-auto space-y-6">
        <!-- Top bar -->
        <div class="flex items-center justify-between">
            <Link
                href="/posts"
                class="flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-900 transition-colors"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Posts
            </Link>

            <PrimaryButton type="button" :disabled="form.processing" @click="submit">
                {{ submitLabel }}
            </PrimaryButton>
        </div>

        <!-- Body -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
            <!-- Main content -->
            <div class="lg:col-span-2 space-y-5">
                <!-- Title + Slug -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
                    <div>
                        <InputLabel for="title" value="Title" />
                        <TextInput
                            id="title"
                            v-model="form.title"
                            placeholder="Your post title…"
                        />
                        <InputError :message="form.errors.title" />
                    </div>

                    <div>
                        <InputLabel for="slug">Slug <span class="font-normal text-gray-400 text-xs ml-1">(auto-generated)</span></InputLabel>
                        <SlugInput
                            id="slug"
                            v-model="form.slug"
                            prefix="/posts/"
                            placeholder="post-slug"
                            @input="slugManuallyEdited = true"
                        />
                        <InputError :message="form.errors.slug" />
                    </div>
                </div>

                <!-- Content -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <InputLabel for="content" value="Content" />
                    <TextArea
                        id="content"
                        v-model="form.content"
                        rows="22"
                        placeholder="Write your post content here…"
                        class="font-mono leading-relaxed"
                    />
                    <InputError :message="form.errors.content" />
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-5">
                <!-- Media -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <p class="text-sm font-medium text-gray-700 mb-3">Media</p>

                    <div
                        v-if="selectedMedia.length || pendingFiles.length"
                        class="grid grid-cols-3 gap-2 mb-3"
                    >
                        <div
                            v-for="item in selectedMedia"
                            :key="item.id"
                            class="relative group"
                        >
                            <div class="aspect-square rounded-lg overflow-hidden bg-gray-100 flex items-center justify-center">
                                <img
                                    v-if="item.mime_type?.startsWith('image/')"
                                    :src="`/storage/${item.path}`"
                                    :alt="item.filename"
                                    class="w-full h-full object-cover"
                                />
                                <svg v-else class="h-6 w-6 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <button
                                type="button"
                                class="absolute -top-1 -right-1 p-0.5 bg-gray-900 rounded-full text-white opacity-0 group-hover:opacity-100 transition-opacity"
                                @click="removeMedia(item.id)"
                            >
                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div
                            v-for="(file, index) in pendingFiles"
                            :key="`pending-${index}`"
                            class="relative group"
                        >
                            <div class="aspect-square rounded-lg overflow-hidden bg-gray-100 flex items-center justify-center">
                                <img
                                    v-if="file.type.startsWith('image/')"
                                    :src="pendingPreview(file)"
                                    :alt="file.name"
                                    class="w-full h-full object-cover"
                                />
                                <svg v-else class="h-6 w-6 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <button
                                type="button"
                                class="absolute -top-1 -right-1 p-0.5 bg-gray-900 rounded-full text-white opacity-0 group-hover:opacity-100 transition-opacity"
                                @click="removePending(index)"
                            >
                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button
                        type="button"
                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 border-2 border-dashed border-gray-200 rounded-xl text-sm text-gray-500 hover:border-gray-400 hover:text-gray-700 transition-colors"
                        @click="showMediaModal = true"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Select / Upload Media
                    </button>
                    <InputError :message="form.errors.media_ids || form.errors.media_files" />
                </div>

                <!-- Categories -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <p class="text-sm font-medium text-gray-700 mb-3">Categories</p>
                    <SelectCategory
                        :categories="props.categories"
                        v-model="form.category_ids"
                    />
                    <InputError :message="form.errors.category_ids" />
                </div>

                <!-- Excerpt -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <InputLabel for="excerpt">Excerpt <span class="font-normal text-gray-400 text-xs ml-1">(optional)</span></InputLabel>
                    <TextArea
                        id="excerpt"
                        v-model="form.excerpt"
                        rows="3"
                        maxlength="500"
                        placeholder="A short summary of your post…"
                    />
                    <div class="flex items-start justify-between mt-1 gap-2">
                        <InputError :message="form.errors.excerpt" />
                        <p class="text-xs text-gray-400 ml-auto shrink-0">{{ form.excerpt.length }}/500</p>
                    </div>
                </div>

                <!-- Status & publish date -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-4">
                    <div>
                        <InputLabel for="status" value="Status" />
                        <SelectInput id="status" v-model="form.status">
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                        </SelectInput>
                        <InputError :message="form.errors.status" />
                    </div>

                    <div v-if="form.status === 'published'">
                        <InputLabel for="published_at">Publish date <span class="font-normal text-gray-400 text-xs ml-1">(defaults to now)</span></InputLabel>
                        <TextInput
                            id="published_at"
                            type="datetime-local"
                            v-model="form.published_at"
                        />
                        <InputError :message="form.errors.published_at" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <SelectMediaFilesModal
        :show="showMediaModal"
        :media="props.userMedia"
        :selected-ids="form.media_ids"
        @close="showMediaModal = false"
        @confirm="onMediaConfirm"
    />
</template>
