<script setup>
import { Link } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const props = defineProps({
    categories: { type: Array, default: () => [] },
    modelValue: { type: Array, default: () => [] },
})

const emit = defineEmits(['update:modelValue'])

const open = ref(false)
const search = ref('')

const filtered = computed(() =>
    props.categories.filter(c =>
        c.name.toLowerCase().includes(search.value.toLowerCase().trim())
    )
)

const selected = computed(() =>
    props.categories.filter(c => props.modelValue.includes(c.id))
)

const noResults = computed(() => search.value.trim() !== '' && filtered.value.length === 0)

function toggle(id) {
    const ids = props.modelValue.includes(id)
        ? props.modelValue.filter(i => i !== id)
        : [...props.modelValue, id]
    emit('update:modelValue', ids)
}

function remove(id) {
    emit('update:modelValue', props.modelValue.filter(i => i !== id))
}

function close() {
    open.value = false
    search.value = ''
}
</script>

<template>
    <div class="relative">
        <!-- Selected chips -->
        <div v-if="selected.length > 0" class="flex flex-wrap gap-1.5 mb-2">
            <span
                v-for="cat in selected"
                :key="cat.id"
                class="inline-flex items-center gap-1 px-2 py-0.5 bg-gray-100 text-gray-700 text-xs font-medium rounded-md"
            >
                {{ cat.name }}
                <button
                    type="button"
                    class="text-gray-400 hover:text-gray-700 transition-colors"
                    @click="remove(cat.id)"
                >
                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </span>
        </div>

        <!-- Trigger -->
        <button
            type="button"
            class="flex items-center gap-1.5 text-xs text-gray-500 hover:text-gray-900 transition-colors"
            @click="open = !open"
        >
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            Select categories…
        </button>

        <!-- Backdrop -->
        <Teleport to="body">
            <div v-if="open" class="fixed inset-0 z-40" @click="close" />
        </Teleport>

        <!-- Dropdown panel -->
        <div
            v-if="open"
            class="absolute left-0 top-full mt-1 w-full min-w-56 bg-white border border-gray-200 rounded-xl shadow-lg z-50 overflow-hidden"
        >
            <!-- Search -->
            <div class="p-2 border-b border-gray-100">
                <div class="flex items-center gap-2 px-2 py-1.5 bg-gray-50 rounded-lg">
                    <svg class="h-3.5 w-3.5 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search categories…"
                        class="flex-1 bg-transparent text-xs text-gray-900 placeholder-gray-400 focus:outline-none"
                    />
                </div>
            </div>

            <!-- Options list -->
            <ul class="max-h-52 overflow-y-auto py-1">
                <li
                    v-for="cat in filtered"
                    :key="cat.id"
                >
                    <button
                        type="button"
                        class="w-full flex items-center gap-2.5 px-3 py-2 text-xs text-gray-700 hover:bg-gray-50 transition-colors text-left"
                        @click="toggle(cat.id)"
                    >
                        <span
                            class="h-3.5 w-3.5 shrink-0 rounded border flex items-center justify-center transition-colors"
                            :class="modelValue.includes(cat.id) ? 'bg-gray-900 border-gray-900' : 'border-gray-300'"
                        >
                            <svg v-if="modelValue.includes(cat.id)" class="h-2.5 w-2.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </span>
                        {{ cat.name }}
                    </button>
                </li>

                <!-- No results -->
                <li v-if="noResults" class="px-3 py-3">
                    <p class="text-xs text-gray-400 mb-2">No results for "{{ search }}"</p>
                    <Link
                        href="/categories/create"
                        class="flex items-center gap-1.5 text-xs font-medium text-indigo-600 hover:text-indigo-800 transition-colors"
                        @click="close"
                    >
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Add "{{ search }}" as category
                    </Link>
                </li>

                <!-- Empty state (no categories at all) -->
                <li v-if="!noResults && filtered.length === 0 && search.trim() === ''" class="px-3 py-3">
                    <p class="text-xs text-gray-400 mb-2">No categories yet.</p>
                    <Link
                        href="/categories/create"
                        class="flex items-center gap-1.5 text-xs font-medium text-indigo-600 hover:text-indigo-800 transition-colors"
                        @click="close"
                    >
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Create your first category
                    </Link>
                </li>
            </ul>
        </div>
    </div>
</template>
