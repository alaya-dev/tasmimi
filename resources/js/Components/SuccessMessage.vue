<template>
    <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0 transform translate-y-2"
        enter-to-class="opacity-100 transform translate-y-0"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100 transform translate-y-0"
        leave-to-class="opacity-0 transform translate-y-2"
    >
        <div
            v-if="show && message"
            class="fixed top-4 right-4 z-50 max-w-sm w-full"
        >
            <div class="bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg flex items-center">
                <svg class="w-6 h-6 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <div class="flex-1">
                    <p class="font-medium">{{ message }}</p>
                </div>
                <button
                    @click="close"
                    class="mr-2 text-green-200 hover:text-white transition-colors"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </Transition>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const show = ref(false)
const message = ref('')

const close = () => {
    show.value = false
}

const showMessage = (msg) => {
    message.value = msg
    show.value = true
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        show.value = false
    }, 5000)
}

// Watch for flash messages
watch(() => page.props.flash, (flash) => {
    if (flash?.success) {
        showMessage(flash.success)
    }
}, { immediate: true })

onMounted(() => {
    if (page.props.flash?.success) {
        showMessage(page.props.flash.success)
    }
})
</script>
