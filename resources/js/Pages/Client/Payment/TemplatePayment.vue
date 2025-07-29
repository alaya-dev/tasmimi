<template>
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    شراء القالب
                </h1>
                <p class="text-gray-600">
                    أكمل عملية الدفع لشراء القالب: {{ template.name }}
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Template Details -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">
                        تفاصيل القالب
                    </h2>
                    
                    <!-- Template Preview -->
                    <div class="mb-4">
                        <img 
                            v-if="template.thumbnail_url" 
                            :src="template.thumbnail_url" 
                            :alt="template.name"
                            class="w-full h-48 object-cover rounded-lg border"
                        >
                        <div v-else class="w-full h-48 bg-gray-200 rounded-lg flex items-center justify-center">
                            <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">اسم القالب:</span>
                            <span class="font-medium">{{ template.name }}</span>
                        </div>
                        
                        <div v-if="template.category" class="flex justify-between items-center">
                            <span class="text-gray-600">الفئة:</span>
                            <span class="font-medium">{{ template.category.name }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">حجم التصميم:</span>
                            <span class="font-medium">{{ template.canvas_size }}</span>
                        </div>
                        
                        <div v-if="template.design_notes" class="space-y-2">
                            <span class="text-gray-600">ملاحظات التصميم:</span>
                            <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded">
                                {{ template.design_notes }}
                            </p>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="flex justify-between items-center text-lg font-bold">
                            <span>السعر:</span>
                            <span class="text-blue-600">{{ template.price }} ريال سعودي</span>
                        </div>
                        
                        <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="mr-3">
                                    <h3 class="text-sm font-medium text-blue-800">ما تحصل عليه</h3>
                                    <div class="mt-1 text-sm text-blue-700">
                                        <ul class="list-disc list-inside space-y-1">
                                            <li>إمكانية تحرير القالب بالكامل</li>
                                            <li>تحميل التصميم بجودة عالية</li>
                                            <li>استخدام غير محدود للقالب</li>
                                            <li>إزالة العلامة المائية</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Form -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">
                        معلومات الدفع
                    </h2>
                    
                    <!-- Loading State -->
                    <div v-if="isLoading" class="text-center py-8">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
                        <p class="mt-4 text-gray-600">جاري تحضير نموذج الدفع...</p>
                    </div>
                    
                    <!-- Error State -->
                    <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-md p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="mr-3">
                                <h3 class="text-sm font-medium text-red-800">خطأ في تحضير الدفع</h3>
                                <p class="mt-1 text-sm text-red-700">{{ error }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Moyasar Payment Form -->
                    <div v-else>
                        <div id="moyasar-form" class="mb-4"></div>
                        
                        <div class="text-sm text-gray-500 text-center">
                            <p>الدفع آمن ومحمي بواسطة Moyasar</p>
                            <p class="mt-1">نحن لا نحتفظ بمعلومات بطاقتك الائتمانية</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Back Button -->
            <div class="text-center mt-8">
                <Link 
                    :href="route('client.templates')" 
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                    <svg class="ml-2 -mr-1 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    العودة إلى القوالب
                </Link>
            </div>
        </div>
    </div>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'

export default {
    name: 'TemplatePayment',
    components: {
        Head,
        Link
    },
    props: {
        template: {
            type: Object,
            required: true
        },
        payment: {
            type: Object,
            required: true
        },
        templatePurchase: {
            type: Object,
            required: true
        },
        moyasarConfig: {
            type: Object,
            required: true
        }
    },
    setup(props) {
        const isLoading = ref(true)
        const error = ref(null)
        
        const loadMoyasarScript = () => {
            return new Promise((resolve, reject) => {
                // Check if Moyasar is already loaded
                if (window.Moyasar) {
                    resolve()
                    return
                }
                
                // Load CSS
                const cssLink = document.createElement('link')
                cssLink.rel = 'stylesheet'
                cssLink.href = 'https://unpkg.com/moyasar-payment-form@2.0.16/dist/moyasar.css'
                document.head.appendChild(cssLink)
                
                // Load JS
                const script = document.createElement('script')
                script.src = 'https://unpkg.com/moyasar-payment-form@2.0.16/dist/moyasar.umd.js'
                script.onload = resolve
                script.onerror = reject
                document.head.appendChild(script)
            })
        }
        
        const initializeMoyasarForm = async () => {
            try {
                await loadMoyasarScript()
                
                // Initialize Moyasar form
                window.Moyasar.init({
                    element: '#moyasar-form',
                    amount: props.moyasarConfig.amount,
                    currency: props.moyasarConfig.currency,
                    description: props.moyasarConfig.description,
                    publishable_api_key: props.moyasarConfig.publishable_api_key,
                    callback_url: props.moyasarConfig.callback_url,
                    methods: ['creditcard'],
                    on_completed: async function (payment) {
                        // Save payment ID before redirect
                        try {
                            await fetch(route('client.payment.completed'), {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: JSON.stringify({
                                    payment_id: payment.id,
                                    local_payment_id: props.payment.id
                                })
                            })
                        } catch (err) {
                            console.error('Error saving payment ID:', err)
                        }
                    }
                })
                
                isLoading.value = false
                
            } catch (err) {
                console.error('Error loading Moyasar:', err)
                error.value = 'فشل في تحميل نموذج الدفع. يرجى إعادة تحميل الصفحة.'
                isLoading.value = false
            }
        }
        
        onMounted(() => {
            initializeMoyasarForm()
        })
        
        return {
            isLoading,
            error
        }
    }
}
</script>

<style scoped>
/* Custom styles for RTL support */
.text-right {
    text-align: right;
}

/* Moyasar form styling */
#moyasar-form {
    direction: ltr;
}
</style>
