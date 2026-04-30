<script setup>
defineOptions({ inheritAttrs: false })
defineProps({
    modelValue: { type: String, default: '' },
    prefix: { type: String, required: true },
})
defineEmits(['update:modelValue'])
</script>

<template>
    <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-gray-900 transition">
        <span class="px-3 py-2 text-xs text-gray-400 bg-gray-50 border-r border-gray-200 shrink-0 select-none">{{ prefix }}</span>
        <input
            v-bind="$attrs"
            :value="modelValue"
            type="text"
            class="flex-1 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:outline-none"
            @input="$emit('update:modelValue', $event.target.value)"
        />
    </div>
</template>
