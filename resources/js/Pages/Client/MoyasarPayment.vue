<template>
    <Head title="الدفع - Bitaqati" />

    <ClientLayout>
        <div class="py-12" dir="rtl">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow-xl rounded-lg overflow-hidden">
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-8 text-center">
                        <h1 class="text-3xl font-bold text-white mb-2">إتمام عملية الدفع</h1>
                        <p class="text-blue-100">اشتراك {{ subscription.name }}</p>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Order Summary -->
                            <div class="order-2 lg:order-1">
                                <h2 class="text-xl font-semibold text-gray-900 mb-4">ملخص الطلب</h2>
                                
                                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="font-medium">{{ subscription.name }}</span>
                                        <span class="font-bold">{{ subscription.price }} ريال</span>
                                    </div>
                                    <div class="flex justify-between items-center text-sm text-gray-600 mb-2">
                                        <span>المدة</span>
                                        <span>{{ getDurationText(subscription) }}</span>
                                    </div>
                                    <div v-if="subscription.features && subscription.features.length > 0" class="mt-3">
                                        <p class="text-sm font-medium text-gray-700 mb-2">الميزات المتضمنة:</p>
                                        <ul class="text-sm text-gray-600 space-y-1">
                                            <li v-for="feature in subscription.features" :key="feature" class="flex items-center">
                                                <svg class="w-4 h-4 text-green-500 ml-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                                {{ feature }}
                                            </li>
                                        </ul>
                                    </div>
                                    
                                    <hr class="my-4">
                                    <div class="flex justify-between items-center font-bold text-lg">
                                        <span>المجموع</span>
                                        <span>{{ subscription.price }} ريال</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Form -->
                            <div class="order-1 lg:order-2">
                                <h2 class="text-xl font-semibold text-gray-900 mb-4">معلومات الدفع</h2>
                                
                                <form @submit.prevent="processPayment" class="space-y-4">
                                    <!-- Payment Method Selection -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-3">طريقة الدفع</label>
                                        <div class="space-y-3">
                                            <div v-for="(method, key) in paymentMethods" :key="key" 
                                                 class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50"
                                                 :class="{ 'border-blue-500 bg-blue-50': selectedPaymentMethod === key }"
                                                 @click="selectedPaymentMethod = key">
                                                <input 
                                                    v-model="selectedPaymentMethod" 
                                                    :value="key"
                                                    type="radio" 
                                                    class="ml-3"
                                                />
                                                <div class="flex-1">
                                                    <div class="font-medium">{{ method.name }}</div>
                                                    <div class="text-sm text-gray-600">{{ method.description }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Credit Card Form -->
                                    <div v-if="selectedPaymentMethod === 'creditcard'" class="space-y-4">
                                        <div>
                                            <label for="card_name" class="block text-sm font-medium text-gray-700 mb-2">
                                                اسم حامل البطاقة
                                            </label>
                                            <input
                                                id="card_name"
                                                v-model="form.card_name"
                                                type="text"
                                                required
                                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                style="text-align: right; direction: rtl;"
                                                placeholder="الاسم كما يظهر على البطاقة"
                                            />
                                        </div>

                                        <div>
                                            <label for="card_number" class="block text-sm font-medium text-gray-700 mb-2">
                                                رقم البطاقة
                                            </label>
                                            <input
                                                id="card_number"
                                                v-model="form.card_number"
                                                type="text"
                                                required
                                                maxlength="19"
                                                @input="formatCardNumber"
                                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                placeholder="1234 5678 9012 3456"
                                            />
                                        </div>

                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label for="card_expiry" class="block text-sm font-medium text-gray-700 mb-2">
                                                    تاريخ الانتهاء
                                                </label>
                                                <input
                                                    id="card_expiry"
                                                    v-model="form.card_expiry"
                                                    type="text"
                                                    required
                                                    maxlength="5"
                                                    @input="formatExpiry"
                                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                    placeholder="MM/YY"
                                                />
                                            </div>
                                            <div>
                                                <label for="card_cvc" class="block text-sm font-medium text-gray-700 mb-2">
                                                    رمز الأمان
                                                </label>
                                                <input
                                                    id="card_cvc"
                                                    v-model="form.card_cvc"
                                                    type="text"
                                                    required
                                                    maxlength="3"
                                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                    placeholder="123"
                                                />
                                            </div>
                                        </div>
                                    </div>

                                    <!-- STC Pay / Apple Pay Token Input (hidden, would be populated by their SDKs) -->
                                    <input v-if="selectedPaymentMethod !== 'creditcard'" v-model="form.token" type="hidden" />

                                    <!-- Submit Button -->
                                    <button
                                        type="submit"
                                        :disabled="processing"
                                        class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-3 px-4 rounded-lg font-medium hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                                    >
                                        <div v-if="processing" class="flex items-center justify-center">
                                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            جاري المعالجة...
                                        </div>
                                        <span v-else>
                                            دفع {{ subscription.price }} ريال
                                        </span>
                                    </button>
                                </form>

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
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import ClientLayout from '@/Layouts/ClientLayout.vue';

const props = defineProps({
    subscription: {
        type: Object,
        required: true
    },
    moyasarKey: {
        type: String,
        required: true
    },
    paymentMethods: {
        type: Object,
        default: () => ({})
    }
});

const processing = ref(false);
const selectedPaymentMethod = ref('creditcard');

const form = ref({
    payment_method: 'creditcard',
    card_name: '',
    card_number: '',
    card_expiry: '',
    card_cvc: '',
    token: ''
});

const getDurationText = (subscription) => {
    if (subscription.duration_days) {
        if (subscription.duration_days == 7) return 'أسبوعي';
        if (subscription.duration_days == 30) return 'شهري';
        if (subscription.duration_days == 90) return 'ربع سنوي';
        if (subscription.duration_days == 365) return 'سنوي';
        return subscription.duration_days + ' يوم';
    }
    
    const types = { 'weekly': 'أسبوعي', 'monthly': 'شهري', 'annual': 'سنوي' };
    return types[subscription.type] || subscription.type;
};

const formatCardNumber = (event) => {
    let value = event.target.value.replace(/\s/g, '').replace(/[^0-9]/gi, '');
    let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
    form.value.card_number = formattedValue;
};

const formatExpiry = (event) => {
    let value = event.target.value.replace(/\D/g, '');
    if (value.length >= 2) {
        value = value.substring(0, 2) + '/' + value.substring(2, 4);
    }
    form.value.card_expiry = value;
};

const processPayment = async () => {
    if (processing.value) return;

    processing.value = true;

    try {
        // Prepare payment data based on payment method
        const paymentData = {
            payment_method: selectedPaymentMethod.value
        };

        if (selectedPaymentMethod.value === 'creditcard') {
            // Add credit card specific fields
            paymentData.card_name = form.value.card_name;
            paymentData.card_number = form.value.card_number.replace(/\s/g, ''); // Remove spaces
            paymentData.card_cvc = form.value.card_cvc;

            // Convert expiry to month/year
            if (form.value.card_expiry) {
                const [month, year] = form.value.card_expiry.split('/');
                paymentData.card_month = parseInt(month);
                paymentData.card_year = parseInt('20' + year);
            }
        } else if (selectedPaymentMethod.value === 'stcpay' || selectedPaymentMethod.value === 'applepay') {
            // Add token for digital wallet payments
            paymentData.token = form.value.token;
        }

        // Use Inertia for better CSRF handling
        router.post(route('client.subscriptions.payment', props.subscription.id), paymentData, {
            onSuccess: (page) => {
                // Check if there's a success response
                if (page.props.flash?.success) {
                    router.visit(route('client.subscription.manage'));
                } else {
                    // Handle JSON response from controller
                    const response = page.props.response;
                    if (response?.success) {
                        router.visit(response.redirect || route('client.subscription.manage'));
                    } else if (response?.pending) {
                        alert('الدفع قيد المعالجة، يرجى الانتظار...');
                    } else {
                        alert(response?.error || 'حدث خطأ أثناء معالجة الدفع');
                    }
                }
            },
            onError: (errors) => {
                console.error('Payment errors:', errors);
                const errorMessage = Object.values(errors)[0] || 'حدث خطأ أثناء معالجة الدفع';
                alert(errorMessage);
            },
            onFinish: () => {
                processing.value = false;
            }
        });

        return; // Exit early since Inertia handles the rest


    } catch (error) {
        console.error('Payment error:', error);

        let errorMessage = 'حدث خطأ أثناء معالجة الدفع';

        if (error.message.includes('انتهت صلاحية الجلسة')) {
            errorMessage = error.message;
            // Optionally reload the page after showing the error
            setTimeout(() => {
                window.location.reload();
            }, 3000);
        } else if (error.message.includes('HTTP')) {
            errorMessage += ': خطأ في الاتصال بالخادم';
        } else if (error.message.includes('JSON')) {
            errorMessage += ': استجابة غير صحيحة من الخادم';
        } else {
            errorMessage += ': ' + error.message;
        }

        alert(errorMessage);
    } finally {
        processing.value = false;
    }
};
</script>
