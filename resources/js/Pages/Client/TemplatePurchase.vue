<template>
    <Head title="شراء القالب" />

    <ClientLayout>
        <div class="min-h-screen bg-gradient-to-br from-blue-50 to-purple-50 py-8">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="text-center mb-8">
                    <div class="bg-white rounded-2xl shadow-lg p-8 mb-8">
                        <div class="flex items-center justify-center mb-4">
                            <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-full p-3">
                                <i class="fas fa-shopping-cart text-white text-2xl"></i>
                            </div>
                        </div>
                        <h1 class="text-4xl font-bold text-gray-900 mb-3">شراء القالب</h1>
                        <p class="text-lg text-gray-600">اشترِ هذا القالب للحصول على إمكانية التحميل والاستخدام الكامل</p>
                        <div class="mt-4 flex items-center justify-center space-x-2 space-x-reverse">
                            <i class="fas fa-shield-alt text-green-600"></i>
                            <span class="text-sm text-green-700 font-medium">دفع آمن ومحمي بتشفير SSL</span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Template Preview -->
                    <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                        <div class="flex items-center mb-6">
                            <div class="bg-gradient-to-r from-purple-500 to-pink-500 rounded-full p-2 ml-3">
                                <i class="fas fa-palette text-white"></i>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-900">معاينة القالب</h2>
                        </div>

                        <div class="aspect-video bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl mb-6 overflow-hidden shadow-inner">
                            <img
                                v-if="template.thumbnail_url"
                                :src="template.thumbnail_url"
                                :alt="template.name"
                                class="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
                            />
                            <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
                                <div class="text-center">
                                    <i class="fas fa-image text-6xl mb-4"></i>
                                    <p class="text-lg">لا توجد صورة معاينة</p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-gray-600 font-medium">اسم القالب:</span>
                                    <span class="font-bold text-gray-900">{{ template.name }}</span>
                                </div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-gray-600 font-medium">الفئة:</span>
                                    <span class="font-semibold text-purple-600">{{ template.category || 'غير محدد' }}</span>
                                </div>
                                <div class="flex justify-between items-center pt-3 border-t border-gray-200">
                                    <span class="text-gray-600 font-medium">السعر:</span>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-3xl font-bold text-green-600">{{ template.price }}</span>
                                        <img src="/images/Saudi_Riyal.png" alt="ريال سعودي" class="w-6 h-6">
                                    </div>
                                </div>
                            </div>

                            <!-- Features -->
                            <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                                <h3 class="font-semibold text-green-800 mb-3 flex items-center">
                                    <i class="fas fa-check-circle ml-2"></i>
                                    ما ستحصل عليه:
                                </h3>
                                <ul class="space-y-2 text-green-700">
                                    <li class="flex items-center">
                                        <i class="fas fa-check text-green-600 ml-2 text-sm"></i>
                                        إمكانية التعديل الكامل
                                    </li>
                                    <li class="flex items-center">
                                        <i class="fas fa-check text-green-600 ml-2 text-sm"></i>
                                        حفظ التصميمات
                                    </li>
                                    <li class="flex items-center">
                                        <i class="fas fa-check text-green-600 ml-2 text-sm"></i>
                                        تحميل بجودة عالية
                                    </li>
                                    <li class="flex items-center">
                                        <i class="fas fa-check text-green-600 ml-2 text-sm"></i>
                                        استخدام تجاري مسموح
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Form -->
                    <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                        <div class="flex items-center mb-6">
                            <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-full p-2 ml-3">
                                <i class="fas fa-credit-card text-white"></i>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-900">معلومات الدفع</h2>
                        </div>

                        <!-- Payment Method Selection -->
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-800 mb-4 text-right">اختر طريقة الدفع</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div
                                    @click="paymentMethod = 'custom'"
                                    :class="paymentMethod === 'custom' ? 'border-green-500 bg-green-50' : 'border-gray-200'"
                                    class="border-2 rounded-xl p-4 cursor-pointer transition-all duration-200 hover:border-green-300"
                                >
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h3 class="font-semibold text-gray-900">إدخال بيانات البطاقة</h3>
                                            <p class="text-sm text-gray-600">أدخل بيانات بطاقتك مباشرة</p>
                                        </div>
                                        <i class="fas fa-credit-card text-2xl text-gray-400"></i>
                                    </div>
                                </div>
                                <div
                                    @click="paymentMethod = 'moyasar'"
                                    :class="paymentMethod === 'moyasar' ? 'border-green-500 bg-green-50' : 'border-gray-200'"
                                    class="border-2 rounded-xl p-4 cursor-pointer transition-all duration-200 hover:border-green-300"
                                >
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h3 class="font-semibold text-gray-900">نموذج Moyasar الآمن</h3>
                                            <p class="text-sm text-gray-600">استخدم نموذج الدفع الآمن</p>
                                        </div>
                                        <i class="fas fa-shield-alt text-2xl text-gray-400"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Error Display -->
                        <div v-if="errors.payment" class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-circle text-red-600 ml-3"></i>
                                <div class="text-red-800">
                                    <p class="font-semibold">خطأ في معالجة الدفع</p>
                                    <p class="text-sm mt-1">{{ errors.payment }}</p>
                                </div>
                            </div>
                        </div>

                        

                        <!-- Custom Payment Form -->
                        <form v-if="paymentMethod === 'custom'" @submit.prevent="processPayment" class="space-y-6">
                            <!-- Payment method indicator -->
                            <input type="hidden" name="payment_method" value="custom">
                        </form>

                        <!-- Moyasar Payment Form -->
                        <div v-if="paymentMethod === 'moyasar'" class="space-y-6">
                            <div v-if="!moyasarLoaded" class="text-center py-8">
                                <div class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm shadow rounded-md text-white bg-green-500 hover:bg-green-400 transition ease-in-out duration-150 cursor-not-allowed">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    جاري تحميل نموذج الدفع...
                                </div>
                            </div>
                            <div id="moyasar-form"></div>
                        </div>

                        <form v-if="paymentMethod === 'custom'" @submit.prevent="processPayment" class="space-y-6">
                            <!-- Card Holder Name -->
                            <div>
                                <label for="card_name" class="block text-sm font-bold text-gray-800 mb-3 text-right flex items-center justify-end">
                                    <span>اسم حامل البطاقة *</span>
                                    <i class="fas fa-user mr-2 text-gray-600"></i>
                                </label>
                                <input
                                    id="card_name"
                                    v-model="form.card_name"
                                    type="text"
                                    class="w-full rounded-xl border-2 border-gray-200 shadow-sm focus:border-green-500 focus:ring-green-500 text-right py-3 px-4 text-lg transition-all duration-200"
                                    :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-500': errors.card_name }"
                                    placeholder="أدخل اسم حامل البطاقة"
                                    required
                                />
                                <div v-if="errors.card_name" class="mt-2 text-sm text-red-600 text-right flex items-center">
                                    <i class="fas fa-exclamation-circle ml-1"></i>
                                    {{ errors.card_name }}
                                </div>
                            </div>

                            <!-- Card Number -->
                            <div>
                                <label for="card_number" class="block text-sm font-bold text-gray-800 mb-3 text-right flex items-center justify-end">
                                    <span>رقم البطاقة *</span>
                                    <i class="fas fa-credit-card mr-2 text-gray-600"></i>
                                </label>
                                <div class="relative">
                                    <input
                                        id="card_number"
                                        v-model="form.card_number"
                                        type="text"
                                        maxlength="19"
                                        dir="ltr"
                                        class="w-full rounded-xl border-2 border-gray-200 shadow-sm focus:border-green-500 focus:ring-green-500 py-3 px-4 text-lg font-mono tracking-wider transition-all duration-200 text-left"
                                        style="text-align: left !important;"
                                        :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-500': errors.card_number }"
                                        placeholder="1234 5678 9012 3456"
                                        @input="formatCardNumber"
                                        required
                                    />
                                    <div class="absolute left-3 top-1/2 transform -translate-y-1/2">
                                        <i class="fas fa-lock text-gray-400"></i>
                                    </div>
                                </div>
                                <div v-if="errors.card_number" class="mt-2 text-sm text-red-600 text-right flex items-center">
                                    <i class="fas fa-exclamation-circle ml-1"></i>
                                    {{ errors.card_number }}
                                </div>
                            </div>

                            <!-- Expiry and CVC -->
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label for="card_expiry" class="block text-sm font-bold text-gray-800 mb-3 text-right flex items-center justify-end">
                                        <span>تاريخ الانتهاء *</span>
                                        <i class="fas fa-calendar-alt mr-2 text-gray-600"></i>
                                    </label>
                                    <input
                                        id="card_expiry"
                                        v-model="form.card_expiry"
                                        type="text"
                                        maxlength="5"
                                        dir="ltr"
                                        class="w-full rounded-xl border-2 border-gray-200 shadow-sm focus:border-green-500 focus:ring-green-500 text-center py-3 px-4 text-lg font-mono tracking-wider transition-all duration-200"
                                        :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-500': errors.card_month || errors.card_year }"
                                        placeholder="MM/YY"
                                        @input="formatExpiry"
                                        required
                                    />
                                </div>
                                <div>
                                    <label for="card_cvc" class="block text-sm font-bold text-gray-800 mb-3 text-right flex items-center justify-end">
                                        <span>رمز الأمان *</span>
                                        <i class="fas fa-shield-alt mr-2 text-gray-600"></i>
                                    </label>
                                    <div class="relative">
                                        <input
                                            id="card_cvc"
                                            v-model="form.card_cvc"
                                            type="text"
                                            maxlength="3"
                                            dir="ltr"
                                            class="w-full rounded-xl border-2 border-gray-200 shadow-sm focus:border-green-500 focus:ring-green-500 text-center py-3 px-4 text-lg font-mono tracking-wider transition-all duration-200"
                                            :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-500': errors.card_cvc }"
                                            placeholder="123"
                                            required
                                        />
                                        <div class="absolute left-3 top-1/2 transform -translate-y-1/2">
                                            <i class="fas fa-question-circle text-gray-400 cursor-help" title="الرقم المكون من 3 أرقام خلف البطاقة"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Error Messages -->
                            <div v-if="errors.card_month || errors.card_year || errors.card_cvc" class="text-sm text-red-600 text-right">
                                <div v-if="errors.card_month">{{ errors.card_month }}</div>
                                <div v-if="errors.card_year">{{ errors.card_year }}</div>
                                <div v-if="errors.card_cvc">{{ errors.card_cvc }}</div>
                            </div>

                            <!-- Payment Summary -->
                            <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-6 border-2 border-green-200">
                                <div class="flex justify-between items-center">
                                    <span class="text-xl font-bold text-gray-800">المجموع:</span>
                                    <div class="flex items-center space-x-3">
                                        <span class="text-3xl font-bold text-green-600">{{ template.price }}</span>
                                        <img src="/images/Saudi_Riyal.png" alt="ريال سعودي" class="w-8 h-8">
                                        <span class="text-lg font-semibold text-gray-600">ريال سعودي</span>
                                    </div>
                                </div>
                                <div class="mt-3 text-sm text-green-700 flex items-center">
                                    <i class="fas fa-info-circle ml-2"></i>
                                    <span>دفعة واحدة - بدون رسوم إضافية</span>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button
                                type="submit"
                                :disabled="processing"
                                class="w-full bg-gradient-to-r from-green-600 to-emerald-600 text-white py-4 px-6 rounded-xl font-bold text-lg hover:from-green-700 hover:to-emerald-700 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg hover:shadow-xl transform hover:-translate-y-1"
                            >
                                <span v-if="processing" class="flex items-center justify-center">
                                    <i class="fas fa-spinner fa-spin ml-3"></i>
                                    جاري المعالجة...
                                </span>
                                <span v-else class="flex items-center justify-center">
                                    <i class="fas fa-shopping-cart ml-3"></i>
                                    شراء القالب الآن
                                    <div class="flex items-center mr-3">
                                        <span>{{ template.price }}</span>
                                        <img src="/images/Saudi_Riyal.png" alt="ريال سعودي" class="w-5 h-5 mr-1">
                                    </div>
                                </span>
                            </button>
                        </form>

                        <!-- Security Notice -->
                        <div class="mt-8 p-6 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-200">
                            <div class="flex items-start">
                                <div class="bg-blue-100 rounded-full p-2 ml-3">
                                    <i class="fas fa-shield-alt text-blue-600"></i>
                                </div>
                                <div class="text-sm text-blue-800">
                                    <p class="font-bold mb-2 text-lg">دفع آمن ومحمي 🔒</p>
                                    <ul class="space-y-1">
                                        <li class="flex items-center">
                                            <i class="fas fa-check text-blue-600 ml-2 text-xs"></i>
                                            تشفير SSL 256-bit
                                        </li>
                                        <li class="flex items-center">
                                            <i class="fas fa-check text-blue-600 ml-2 text-xs"></i>
                                            معالجة آمنة عبر Moyasar
                                        </li>
                                        <li class="flex items-center">
                                            <i class="fas fa-check text-blue-600 ml-2 text-xs"></i>
                                            حماية بيانات البطاقة
                                        </li>
                                    </ul>
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
import { ref, reactive, watch, onMounted } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import ClientLayout from '@/Layouts/ClientLayout.vue'

const props = defineProps({
    template: {
        type: Object,
        required: true
    },
    moyasarKey: {
        type: String,
        required: true
    },
    errors: {
        type: Object,
        default: () => ({})
    }
})

const processing = ref(false)
const errors = ref(props.errors || {}) // Initialize with server errors
const paymentMethod = ref('custom') // Default to custom form
const moyasarLoaded = ref(false)
let moyasarInstance = null

const form = reactive({
    card_name: '',
    card_number: '',
    card_expiry: '',
    card_cvc: '',
    card_month: '',
    card_year: ''
})

// Load Moyasar script dynamically
const loadMoyasarScript = () => {
    return new Promise((resolve, reject) => {
        if (window.Moyasar) {
            moyasarLoaded.value = true
            resolve()
            return
        }

        const script = document.createElement('script')
        script.src = 'https://cdn.moyasar.com/mpf/1.14.0/moyasar.js'
        script.onload = () => {
            moyasarLoaded.value = true
            resolve()
        }
        script.onerror = reject
        document.head.appendChild(script)
    })
}

// Format card number with spaces
const formatCardNumber = (event) => {
    let value = event.target.value.replace(/\s/g, '').replace(/[^0-9]/gi, '')
    let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value
    form.card_number = formattedValue
}

// Format expiry date
const formatExpiry = (event) => {
    let value = event.target.value.replace(/\D/g, '')
    if (value.length >= 2) {
        value = value.substring(0, 2) + '/' + value.substring(2, 4)
    }
    form.card_expiry = value
    
    // Extract month and year
    if (value.length === 5) {
        const [month, year] = value.split('/')
        form.card_month = parseInt(month)
        form.card_year = parseInt('20' + year)
    }
}

// Initialize Moyasar form
const initializeMoyasar = async () => {
    if (paymentMethod.value === 'moyasar') {
        try {
            await loadMoyasarScript()

            if (window.Moyasar) {
                moyasarInstance = window.Moyasar.init({
                    element: '#moyasar-form',
                    amount: props.template.price * 100, // Convert to halalas
                    currency: 'SAR',
                    description: `شراء قالب: ${props.template.name}`,
                    publishable_api_key: props.moyasarKey,
                    callback_url: `${window.location.origin}/client/template-purchase/callback`,
                    methods: ['creditcard', 'stcpay'],
                    on_completed: (payment) => {
                        // Payment completed successfully
                        router.visit(route('client.my-designs'), {
                            onSuccess: () => {
                                // Show success message
                            }
                        })
                    },
                    on_failed: (error) => {
                        // Payment failed
                        console.error('Payment failed:', error)
                        alert('فشل في معالجة الدفع: ' + (error.message || 'خطأ غير معروف'))
                    }
                })
            }
        } catch (error) {
            console.error('Failed to load Moyasar script:', error)
            alert('فشل في تحميل نموذج الدفع، يرجى المحاولة مرة أخرى')
        }
    }
}

// Watch for payment method changes
const handlePaymentMethodChange = () => {
    if (paymentMethod.value === 'moyasar') {
        // Initialize Moyasar form after DOM update
        setTimeout(() => {
            initializeMoyasar()
        }, 100)
    }
}

// Process payment using custom form
const processPayment = () => {
    // Prevent multiple submissions
    if (processing.value) {
        return
    }

    processing.value = true
    errors.value = {}

    // Use Inertia for better CSRF handling and error management
    router.post(route('client.templates.purchase.process', props.template.id), {
        card_name: form.card_name,
        card_number: form.card_number.replace(/\s/g, ''),
        card_cvc: form.card_cvc,
        card_month: form.card_month,
        card_year: form.card_year
    }, {
        onSuccess: (page) => {
            // Success will be handled by redirect with flash message
            processing.value = false
        },
        onError: (responseErrors) => {
            // Handle validation errors
            console.log('Payment errors received:', responseErrors)
            errors.value = responseErrors
            processing.value = false
        },
        onFinish: () => {
            processing.value = false
        }
    })
}

// Watch for payment method changes
watch(paymentMethod, handlePaymentMethodChange)

// Initialize on mount
onMounted(() => {
    if (paymentMethod.value === 'moyasar') {
        initializeMoyasar()
    }
})
</script>
