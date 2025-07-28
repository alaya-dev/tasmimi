<template>
    <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                <div class="text-center">
                    <!-- Success Icon -->
                    <div v-if="success" class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    
                    <!-- Error Icon -->
                    <div v-else class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>

                    <!-- Title -->
                    <h2 class="text-lg font-medium text-gray-900 mb-2">
                        {{ success ? 'تم الدفع بنجاح' : 'فشل في الدفع' }}
                    </h2>

                    <!-- Message -->
                    <p class="text-sm text-gray-600 mb-6">
                        {{ message }}
                    </p>

                    <!-- Action Button -->
                    <button
                        @click="redirectToPage"
                        :class="[
                            'w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2',
                            success 
                                ? 'bg-green-600 hover:bg-green-700 focus:ring-green-500' 
                                : 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500'
                        ]"
                    >
                        {{ success ? 'إدارة الاشتراك' : 'العودة للاشتراكات' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3'

const props = defineProps({
    success: Boolean,
    message: String,
    redirectUrl: String
})

const redirectToPage = () => {
    router.visit(props.redirectUrl)
}

// Auto redirect after 5 seconds
setTimeout(() => {
    redirectToPage()
}, 5000)
</script>
