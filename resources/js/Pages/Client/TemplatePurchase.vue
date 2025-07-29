<template>
    <Head title="شراء القالب" />
    
    <ClientLayout>
        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="mb-6">
                            <h1 class="text-2xl font-bold text-gray-900 mb-2">شراء القالب</h1>
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                                <h3 class="font-semibold text-blue-900 mb-2">{{ template.name }}</h3>
                                <p class="text-blue-700 mb-2">{{ template.description }}</p>
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-bold text-blue-900">{{ template.price }} ريال سعودي</span>
                                    <span class="text-sm text-blue-600">الفئة: {{ template.category?.name }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Moyasar Payment Form -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h2 class="text-lg font-semibold mb-4">معلومات الدفع</h2>
                            <div id="mysr-form" class="min-h-[400px]"></div>
                        </div>

                        <!-- Error Display -->
                        <div v-if="errorMessage" class="mt-4 bg-red-50 border border-red-200 rounded-lg p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">
                                        خطأ في تحميل نموذج الدفع
                                    </h3>
                                    <div class="mt-2 text-sm text-red-700">
                                        <p>{{ errorMessage }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Loading State -->
                        <div v-if="loading" class="mt-4 text-center">
                            <div class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm text-blue-600">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                جاري تحميل نموذج الدفع...
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </ClientLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import { onMounted, ref } from 'vue'
import ClientLayout from '@/Layouts/ClientLayout.vue'

// Props
const props = defineProps({
    template: {
        type: Object,
        required: true
    },
    paymentConfig: {
        type: Object,
        required: true
    }
})

// Reactive data
const loading = ref(true)
const errorMessage = ref('')

// Initialize Moyasar payment form
onMounted(async () => {
    try {
        console.log('Template Purchase - Payment config:', props.paymentConfig)
        
        // Initialize Moyasar payment form
        const mysr = Moyasar.init(props.paymentConfig)
        console.log('Template Purchase - Moyasar initialized:', mysr)
        
        loading.value = false
    } catch (error) {
        console.error('Template Purchase - Error initializing Moyasar:', error)
        errorMessage.value = `خطأ في تحميل نموذج الدفع: ${error.message || 'خطأ غير معروف'}`
        loading.value = false
    }
})
</script>
