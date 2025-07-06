<template>
    <div class="relative inline-block text-left">
        <div>
            <button
                @click="toggleDropdown"
                type="button"
                class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                id="language-menu"
                aria-expanded="true"
                aria-haspopup="true"
            >
                <span class="flex items-center">
                    <span class="mr-2">{{ currentLanguageFlag }}</span>
                    {{ currentLanguageName }}
                </span>
                <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>

        <div
            v-show="isOpen"
            class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
            role="menu"
            aria-orientation="vertical"
            aria-labelledby="language-menu"
        >
            <div class="py-1" role="none">
                <a
                    v-for="language in languages"
                    :key="language.code"
                    @click="switchLanguage(language.code)"
                    href="#"
                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900"
                    :class="{ 'bg-gray-100 text-gray-900': currentLocale === language.code }"
                    role="menuitem"
                >
                    <span class="mr-3">{{ language.flag }}</span>
                    {{ language.name }}
                    <svg
                        v-if="currentLocale === language.code"
                        class="ml-auto h-5 w-5 text-indigo-600"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                </a>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
    currentLocale: {
        type: String,
        default: 'en'
    }
})

const isOpen = ref(false)

const languages = [
    { code: 'en', name: 'English', flag: '🇺🇸' },
    { code: 'ar', name: 'العربية', flag: '🇸🇦' }
]

const currentLanguageFlag = computed(() => {
    const language = languages.find(lang => lang.code === props.currentLocale)
    return language ? language.flag : '🇺🇸'
})

const currentLanguageName = computed(() => {
    const language = languages.find(lang => lang.code === props.currentLocale)
    return language ? language.name : 'English'
})

const toggleDropdown = () => {
    isOpen.value = !isOpen.value
}

const switchLanguage = (locale) => {
    if (locale !== props.currentLocale) {
        router.visit(route('language.switch', locale), {
            method: 'post',
            preserveState: true,
            preserveScroll: true,
        })
    }
    isOpen.value = false
}

const closeDropdown = (event) => {
    if (!event.target.closest('#language-menu')) {
        isOpen.value = false
    }
}

onMounted(() => {
    document.addEventListener('click', closeDropdown)
})

onUnmounted(() => {
    document.removeEventListener('click', closeDropdown)
})
</script>

<style scoped>
/* RTL support for Arabic */
[dir="rtl"] .origin-top-right {
    @apply origin-top-left;
}

[dir="rtl"] .right-0 {
    @apply left-0 right-auto;
}
</style>