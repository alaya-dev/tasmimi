<template>
    <Link
        :href="href"
        :class="[
            'block px-3 py-2 rounded-md text-base font-medium transition-colors duration-200',
            active
                ? 'bg-blue-50 text-blue-700 border-r-4 border-blue-700'
                : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600'
        ]"
    >
        <slot />
    </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
        default: false,
    },
});
</script>
