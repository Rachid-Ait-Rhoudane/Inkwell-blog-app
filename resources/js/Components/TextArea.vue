<script setup>
defineProps({
    modelValue: { type: String, default: '' },
})
defineEmits(['update:modelValue'])
</script>

<template>
    <textarea
        :value="modelValue"
        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition resize-none"
        @input="$emit('update:modelValue', $event.target.value)"
    />
</template>
