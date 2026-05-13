<script setup>
import AppLayout from '../../Layout/AppLayout.vue'
import InputError from '../../Components/InputError.vue'
import InputLabel from '../../Components/InputLabel.vue'
import PrimaryButton from '../../Components/PrimaryButton.vue'
import TextArea from '../../Components/TextArea.vue'
import { Link, setLayoutProps, useForm } from '@inertiajs/vue3'

defineOptions({ layout: AppLayout })
setLayoutProps({ title: 'Edit Comment' })

const props = defineProps({ comment: Object })

const form = useForm({
    body: props.comment.body,
})

function submit() {
    form.put(`/comments/${props.comment.id}`)
}
</script>

<template>
    <div class="max-w-3xl mx-auto space-y-6">
        <!-- Top bar -->
        <div class="flex items-center justify-between">
            <Link
                href="/comments"
                class="flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-900 transition-colors"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Comments
            </Link>

            <PrimaryButton type="button" :disabled="form.processing" @click="submit">
                {{ form.processing ? 'Saving…' : 'Update Comment' }}
            </PrimaryButton>
        </div>

        <!-- Post context -->
        <div v-if="comment.post" class="bg-white rounded-2xl border border-gray-100 shadow-sm px-6 py-4">
            <p class="text-xs text-gray-400 uppercase tracking-wide font-medium mb-1">Commenting on</p>
            <a
                :href="`/posts/${comment.post.slug}`"
                class="text-sm font-medium text-indigo-600 hover:text-indigo-800 transition-colors"
            >
                {{ comment.post.title }}
            </a>
        </div>

        <!-- Body -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <InputLabel for="body" value="Comment" />
            <TextArea
                id="body"
                v-model="form.body"
                rows="6"
                maxlength="2000"
                placeholder="Write your comment…"
            />
            <div class="flex items-start justify-between mt-1 gap-2">
                <InputError :message="form.errors.body" />
                <p class="text-xs text-gray-400 ml-auto shrink-0">{{ form.body.length }}/2000</p>
            </div>
        </div>
    </div>
</template>
