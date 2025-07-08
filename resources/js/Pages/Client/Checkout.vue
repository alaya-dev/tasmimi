<template>
    <Head :title="__('client.payment_details')" />

    <ClientLayout>
        <div class="py-12 bg-gray-50 min-h-screen">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Page Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">{{ __('client.complete_payment') }}</h1>
                    <p class="mt-2 text-gray-600">أكمل عملية الدفع لإتمام طلبك</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Payment Form -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-6">{{ __('client.payment_details') }}</h2>
                        
                        <form @submit.prevent="processPayment" class="space-y-6">
                            <!-- Payment Method -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-3">{{ __('client.payment_method') }}</label>
                                <div class="space-y-3">
                                    <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                                        <input 
                                            v-model="paymentMethod" 
                                            type="radio" 
                                            value="credit_card" 
                                            class="mr-3"
                                        />
                                        <svg class="w-6 h-6 mr-3 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2zm0 2v12h16V6H4zm2 2h12v2H6V8zm0 4h8v2H6v-2z"/>
                                        </svg>
                                        <span class="font-medium">{{ __('client.credit_card') }}</span>
                                    </label>
                                    
                                    <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                                        <input 
                                            v-model="paymentMethod" 
                                            type="radio" 
                                            value="paypal" 
                                            class="mr-3"
                                        />
                                        <svg class="w-6 h-6 mr-3 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M7.076 21.337H2.47a.641.641 0 0 1-.633-.74L4.944.901C5.026.382 5.474 0 5.998 0h7.46c2.57 0 4.578.543 5.69 1.81 1.01 1.15 1.304 2.42 1.012 4.287-.023.143-.047.288-.077.437-.983 5.05-4.349 6.797-8.647 6.797h-2.19c-.524 0-.968.382-1.05.9l-1.12 7.106zm14.146-14.42a3.35 3.35 0 0 0-.607-.541c-.013.076-.026.175-.041.26-.93 4.778-4.005 6.495-7.974 6.495h-2.19c-.524 0-.968.382-1.05.9L7.076 21.337h4.47a.641.641 0 0 0 .633-.74l.929-5.888c.082-.518.526-.9 1.05-.9h1.121c3.69 0 6.58-1.5 7.42-5.84.345-1.778.208-3.24-.477-4.052z"/>
                                        </svg>
                                        <span class="font-medium">{{ __('client.paypal') }}</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Credit Card Details -->
                            <div v-if="paymentMethod === 'credit_card'" class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('client.cardholder_name') }}</label>
                                    <input 
                                        v-model="cardDetails.name"
                                        type="text" 
                                        required
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        placeholder="الاسم كما يظهر على البطاقة"
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('client.card_number') }}</label>
                                    <input 
                                        v-model="cardDetails.number"
                                        type="text" 
                                        required
                                        maxlength="19"
                                        @input="formatCardNumber"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        placeholder="1234 5678 9012 3456"
                                    />
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('client.expiry_date') }}</label>
                                        <input 
                                            v-model="cardDetails.expiry"
                                            type="text" 
                                            required
                                            maxlength="5"
                                            @input="formatExpiry"
                                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            placeholder="MM/YY"
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('client.cvv') }}</label>
                                        <input 
                                            v-model="cardDetails.cvv"
                                            type="text" 
                                            required
                                            maxlength="4"
                                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            placeholder="123"
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Billing Address -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('client.billing_address') }}</h3>
                                <div class="space-y-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">الاسم الأول</label>
                                            <input 
                                                v-model="billingAddress.firstName"
                                                type="text" 
                                                required
                                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            />
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">اسم العائلة</label>
                                            <input 
                                                v-model="billingAddress.lastName"
                                                type="text" 
                                                required
                                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            />
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">العنوان</label>
                                        <input 
                                            v-model="billingAddress.address"
                                            type="text" 
                                            required
                                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        />
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">المدينة</label>
                                            <input 
                                                v-model="billingAddress.city"
                                                type="text" 
                                                required
                                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            />
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">الرمز البريدي</label>
                                            <input 
                                                v-model="billingAddress.zipCode"
                                                type="text" 
                                                required
                                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button 
                                type="submit"
                                :disabled="processing"
                                class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors disabled:opacity-50"
                            >
                                {{ processing ? 'جاري المعالجة...' : __('client.complete_payment') }}
                            </button>
                        </form>
                    </div>

                    <!-- Order Summary -->
                    <div class="bg-white rounded-lg shadow-sm p-6 h-fit sticky top-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-6">ملخص الطلب</h2>
                        
                        <div class="space-y-4 mb-6">
                            <div 
                                v-for="item in orderItems" 
                                :key="item.id"
                                class="flex justify-between"
                            >
                                <div>
                                    <div class="font-medium">{{ item.name }}</div>
                                    <div class="text-sm text-gray-600">الكمية: {{ item.quantity }}</div>
                                </div>
                                <div class="font-medium">{{ formatPrice(item.price * item.quantity) }} ريال</div>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">المجموع الفرعي</span>
                                <span>{{ formatPrice(subtotal) }} ريال</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">الضريبة</span>
                                <span>{{ formatPrice(tax) }} ريال</span>
                            </div>
                            <div class="flex justify-between text-lg font-semibold">
                                <span>الإجمالي</span>
                                <span>{{ formatPrice(total) }} ريال</span>
                            </div>
                        </div>

                        <!-- Security Notice -->
                        <div class="mt-6 flex items-center text-sm text-gray-600">
                            <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                            </svg>
                            معلوماتك محمية بتشفير SSL
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </ClientLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import ClientLayout from '@/Layouts/ClientLayout.vue';
import { useTranslations } from '@/Composables/useTranslations';

// Props
const props = defineProps({
    orderItems: Array,
    subtotal: Number,
    tax: Number,
    total: Number
});

// Composables
const { __ } = useTranslations();

// State
const processing = ref(false);
const paymentMethod = ref('credit_card');

const cardDetails = ref({
    name: '',
    number: '',
    expiry: '',
    cvv: ''
});

const billingAddress = ref({
    firstName: '',
    lastName: '',
    address: '',
    city: '',
    zipCode: ''
});

// Sample data
const sampleOrderItems = [
    { id: 1, name: 'بطاقة عمل احترافية', quantity: 2, price: 25 },
    { id: 2, name: 'دعوة زفاف رومانسية', quantity: 1, price: 15 }
];

const orderItems = computed(() => props.orderItems || sampleOrderItems);
const subtotal = computed(() => props.subtotal || 65);
const tax = computed(() => props.tax || 9.75);
const total = computed(() => props.total || 74.75);

// Methods
const formatCardNumber = (event) => {
    let value = event.target.value.replace(/\s/g, '').replace(/[^0-9]/gi, '');
    let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
    cardDetails.value.number = formattedValue;
};

const formatExpiry = (event) => {
    let value = event.target.value.replace(/\D/g, '');
    if (value.length >= 2) {
        value = value.substring(0, 2) + '/' + value.substring(2, 4);
    }
    cardDetails.value.expiry = value;
};

const processPayment = async () => {
    processing.value = true;
    
    try {
        await router.post(route('client.payment.process'), {
            payment_method: paymentMethod.value,
            card_details: cardDetails.value,
            billing_address: billingAddress.value
        });
    } catch (error) {
        console.error('Payment error:', error);
    } finally {
        processing.value = false;
    }
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('ar-SA').format(price);
};
</script>
