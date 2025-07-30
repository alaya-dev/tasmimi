<template>
    <Head title="شراء القالب" />
    
    <ClientLayout>
        <div class="py-12" dir="rtl">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow-xl rounded-lg overflow-hidden">
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-8 text-center">
                        <h1 class="text-3xl font-bold text-white mb-2">شراء القالب</h1>
                        <p class="text-blue-100">{{ template.name }}</p>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Template Info -->
                            <div class="order-2 lg:order-1">
                                <h2 class="text-xl font-semibold text-gray-900 mb-4">تفاصيل القالب</h2>
                                
                                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="font-medium">{{ template.name }}</span>
                                        <div class="flex items-center space-x-2">
                                            <span class="font-bold">{{ template.price }}</span>
                                            <img src="/images/Saudi_Riyal.png" alt="ريال سعودي" class="w-5 h-5">
                                        </div>
                                    </div>
                                    <div v-if="template.category" class="flex justify-between items-center text-sm text-gray-600 mb-2">
                                        <span>الفئة</span>
                                        <span>{{ template.category.name }}</span>
                                    </div>
                                    <div v-if="template.description" class="mt-3">
                                        <p class="text-sm text-gray-600">{{ template.description }}</p>
                                    </div>
                                    
                                    <hr class="my-4">
                                    <div class="flex justify-between items-center font-bold text-lg">
                                        <span>المجموع</span>
                                        <div class="flex items-center space-x-2">
                                            <span>{{ template.price }}</span>
                                            <img src="/images/Saudi_Riyal.png" alt="ريال سعودي" class="w-6 h-6">
                                            <span class="text-sm font-normal text-gray-600">ريال سعودي</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Form -->
                            <div class="order-1 lg:order-2">
                                <h2 class="text-xl font-semibold text-gray-900 mb-4">معلومات الدفع</h2>
                                
                                <!-- Moyasar Payment Form -->
                                <div id="mysr-form" class="mb-6"></div>

                                <!-- Security Notice -->
                                <div class="mt-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-green-600 ml-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        <div>
                                            <p class="text-sm font-medium text-green-800">دفع آمن ومشفر</p>
                                            <p class="text-sm text-green-600">معلوماتك محمية بأعلى معايير الأمان</p>
                                        </div>
                                    </div>
                                </div>
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

// Save payment ID before redirect
const savePaymentId = async (payment) => {
    try {
        console.log('Attempting to save payment ID:', payment.id);
        console.log('Template ID:', props.template.id);
        console.log('Route:', route('client.templates.save-payment-id', props.template.id));
        
        const response = await window.axios.post(route('client.templates.save-payment-id', props.template.id), {
            payment_id: payment.id
        });
        console.log('Template payment ID saved successfully:', payment.id);
        console.log('Response:', response.data);
    } catch (error) {
        console.error('Failed to save template payment ID:', error);
        console.error('Error details:', error.response?.data);
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
        } catch (error) {
            console.error('Template Purchase - Moyasar initialization error:', error);
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
