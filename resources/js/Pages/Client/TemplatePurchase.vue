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
import { Head, router } from '@inertiajs/vue3'
import { onMounted, ref } from 'vue'
import axios from 'axios'
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

// Save payment ID before redirect
const savePaymentId = async (payment) => {
    try {
        await axios.post(route('templates.save-payment-id', props.template.id), {
            payment_id: payment.id
        });
        console.log('Payment ID saved successfully for template:', payment.id);
    } catch (error) {
        console.error('Failed to save payment ID for template:', error);
    }
};

// Initialize Moyasar payment form
onMounted(() => {
    // Initialize Moyasar form when component is mounted
    if (typeof window.Moyasar !== 'undefined') {
        try {
            console.log('Template Purchase - Initializing Moyasar with config:', props.paymentConfig);
            window.Moyasar.init({
                element: '#mysr-form',
                ...props.paymentConfig,
                on_completed: async function (payment) {
                    console.log('Template payment completed:', payment);
                    // Save payment ID before redirect
                    await savePaymentId(payment);
                },
            });
            loading.value = false;
            console.log('Template Purchase - Moyasar initialized successfully');
        } catch (error) {
            console.error('Template Purchase - Moyasar initialization error:', error);
            errorMessage.value = `خطأ في تحميل نموذج الدفع: ${error.message || 'خطأ غير معروف'}`;
            loading.value = false;
            
            // Show user-friendly error
            const formElement = document.getElementById('mysr-form');
            if (formElement) {
                formElement.innerHTML = `
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center">
                        <i class="fas fa-exclamation-triangle text-red-600 mb-2"></i>
                        <p class="text-red-800 font-medium">خطأ في تحميل نموذج الدفع</p>
                        <p class="text-red-600 text-sm">يرجى إعادة تحميل الصفحة والمحاولة مرة أخرى</p>
                        <button onclick="window.location.reload()" class="mt-3 bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                            إعادة التحميل
                        </button>
                    </div>
                `;
            }
        }
    } else {
        console.error('Template Purchase - Moyasar library not loaded');
        errorMessage.value = 'مكتبة Moyasar غير محملة';
        loading.value = false;
        
        // Show loading error
        const formElement = document.getElementById('mysr-form');
        if (formElement) {
            formElement.innerHTML = `
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center">
                    <i class="fas fa-exclamation-triangle text-yellow-600 mb-2"></i>
                    <p class="text-yellow-800 font-medium">جاري تحميل نموذج الدفع...</p>
                    <p class="text-yellow-600 text-sm">إذا لم يتم التحميل، يرجى التحقق من الاتصال بالإنترنت</p>
                </div>
            `;
        }
    }
})
</script>
