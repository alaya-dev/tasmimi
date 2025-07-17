<template>
    <Head :title="__('client.shopping_cart')" />

    <ClientLayout>
        <div class="py-12 bg-gray-50 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Page Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">{{ __('client.shopping_cart') }}</h1>
                    <p class="mt-2 text-gray-600">راجع عناصر سلتك قبل إتمام الشراء</p>
                </div>

                <div v-if="cartItems.length > 0" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Cart Items -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-lg shadow-sm">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h2 class="text-lg font-semibold text-gray-900">عناصر السلة ({{ cartItems.length }})</h2>
                            </div>
                            
                            <div class="divide-y divide-gray-200">
                                <div 
                                    v-for="item in cartItems" 
                                    :key="item.id"
                                    class="p-6 flex items-center space-x-6" 
                                    :class="isRTL ? 'space-x-reverse' : ''"
                                >
                                    <!-- Item Image/Preview -->
                                    <div class="w-24 h-16 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                                        <div 
                                            class="w-full h-full flex items-center justify-center text-xs font-medium"
                                            :style="{ background: item.preview_background || '#f3f4f6' }"
                                        >
                                            {{ item.name }}
                                        </div>
                                    </div>

                                    <!-- Item Details -->
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900">{{ item.name }}</h3>
                                        <p class="text-gray-600 text-sm">{{ item.description }}</p>
                                        <div class="mt-2 flex items-center space-x-4" :class="isRTL ? 'space-x-reverse' : ''">
                                            <span class="text-sm text-gray-500">النوع: {{ item.type }}</span>
                                            <span class="text-sm text-gray-500">الحجم: {{ item.size }}</span>
                                        </div>
                                    </div>

                                    <!-- Quantity Controls -->
                                    <div class="flex items-center space-x-3" :class="isRTL ? 'space-x-reverse' : ''">
                                        <button 
                                            @click="updateQuantity(item.id, item.quantity - 1)"
                                            :disabled="item.quantity <= 1"
                                            class="w-8 h-8 rounded-full border border-gray-300 flex items-center justify-center hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                            </svg>
                                        </button>
                                        <span class="w-8 text-center font-medium">{{ item.quantity }}</span>
                                        <button 
                                            @click="updateQuantity(item.id, item.quantity + 1)"
                                            class="w-8 h-8 rounded-full border border-gray-300 flex items-center justify-center hover:bg-gray-50"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Price -->
                                    <div class="text-right">
                                        <div class="text-lg font-semibold text-gray-900">
                                            {{ formatPrice(item.price * item.quantity) }} ريال
                                        </div>
                                        <div v-if="item.quantity > 1" class="text-sm text-gray-500">
                                            {{ formatPrice(item.price) }} ريال × {{ item.quantity }}
                                        </div>
                                    </div>

                                    <!-- Remove Button -->
                                    <button 
                                        @click="removeItem(item.id)"
                                        class="text-red-600 hover:text-red-800 p-2"
                                        :title="__('client.remove_item')"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Continue Shopping -->
                        <div class="mt-6">
                            <Link 
                                :href="route('client.templates')" 
                                class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium"
                            >
                                <svg :class="isRTL ? 'mr-2' : 'ml-2'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="isRTL ? 'M9 5l7 7-7 7' : 'M15 19l-7-7 7-7'"/>
                                </svg>
                                {{ __('client.continue_shopping') }}
                            </Link>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-lg shadow-sm p-6 sticky top-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-6">ملخص الطلب</h2>
                            
                            <div class="space-y-4">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">المجموع الفرعي</span>
                                    <span class="font-medium">{{ formatPrice(subtotal) }} ريال</span>
                                </div>
                                
                                <div class="flex justify-between">
                                    <span class="text-gray-600">الضريبة (15%)</span>
                                    <span class="font-medium">{{ formatPrice(tax) }} ريال</span>
                                </div>
                                
                                <div v-if="discount > 0" class="flex justify-between text-green-600">
                                    <span>الخصم</span>
                                    <span class="font-medium">-{{ formatPrice(discount) }} ريال</span>
                                </div>
                                
                                <hr class="my-4">
                                
                                <div class="flex justify-between text-lg font-semibold">
                                    <span>{{ __('client.total_price') }}</span>
                                    <span>{{ formatPrice(total) }} ريال</span>
                                </div>
                            </div>

                            <!-- Coupon Code -->
                            <div class="mt-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">كود الخصم</label>
                                <div class="flex space-x-2" :class="isRTL ? 'space-x-reverse' : ''">
                                    <input 
                                        v-model="couponCode"
                                        type="text" 
                                        placeholder="أدخل كود الخصم"
                                        class="flex-1 border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    />
                                    <button 
                                        @click="applyCoupon"
                                        :disabled="!couponCode || applyingCoupon"
                                        class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors disabled:opacity-50"
                                    >
                                        {{ applyingCoupon ? 'جاري التطبيق...' : 'تطبيق' }}
                                    </button>
                                </div>
                                <div v-if="couponError" class="mt-1 text-sm text-red-600">
                                    {{ couponError }}
                                </div>
                                <div v-if="couponSuccess" class="mt-1 text-sm text-green-600">
                                    {{ couponSuccess }}
                                </div>
                            </div>

                            <!-- Checkout Button -->
                            <button 
                                @click="proceedToCheckout"
                                :disabled="processing"
                                class="w-full mt-6 bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors disabled:opacity-50"
                            >
                                {{ processing ? 'جاري المعالجة...' : __('client.proceed_checkout') }}
                            </button>

                            <!-- Security Notice -->
                            <div class="mt-4 flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                </svg>
                                دفع آمن ومشفر
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty Cart -->
                <div v-else class="text-center py-16">
                    <svg class="w-24 h-24 text-gray-400 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l-1.5-6m0 0h15.5M17 21a2 2 0 100-4 2 2 0 000 4zM9 21a2 2 0 100-4 2 2 0 000 4z"/>
                    </svg>
                    <h3 class="text-2xl font-semibold text-gray-900 mb-4">{{ __('client.cart_empty') }}</h3>
                    <p class="text-gray-600 mb-8 max-w-md mx-auto">
                        سلة التسوق فارغة. تصفح قوالبنا وأضف التصاميم التي تعجبك.
                    </p>
                    <Link
                        :href="route('client.home')"
                        class="bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors inline-flex items-center"
                    >
                        <svg :class="isRTL ? 'mr-2' : 'ml-2'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        {{ __('client.continue_shopping') }}
                    </Link>
                </div>
            </div>
        </div>
    </ClientLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import ClientLayout from '@/Layouts/ClientLayout.vue';
import { useTranslations } from '@/Composables/useTranslations';

// Props
const props = defineProps({
    cartItems: {
        type: Array,
        default: () => []
    }
});

// Composables
const { __, isRTL } = useTranslations();

// State
const couponCode = ref('');
const applyingCoupon = ref(false);
const couponError = ref('');
const couponSuccess = ref('');
const processing = ref(false);
const discount = ref(0);

// Sample cart items (replace with actual data from props)
const sampleCartItems = [
    {
        id: 1,
        name: 'بطاقة عمل احترافية',
        description: 'تصميم أنيق للأعمال',
        type: 'بطاقة عمل',
        size: '9×5 سم',
        price: 25,
        quantity: 2,
        preview_background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)'
    },
    {
        id: 2,
        name: 'دعوة زفاف رومانسية',
        description: 'تصميم رومانسي للزفاف',
        type: 'دعوة',
        size: '14×10 سم',
        price: 15,
        quantity: 1,
        preview_background: 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)'
    }
];

const cartItems = computed(() => props.cartItems.length > 0 ? props.cartItems : sampleCartItems);

// Computed
const subtotal = computed(() => {
    return cartItems.value.reduce((sum, item) => sum + (item.price * item.quantity), 0);
});

const tax = computed(() => {
    return subtotal.value * 0.15; // 15% tax
});

const total = computed(() => {
    return subtotal.value + tax.value - discount.value;
});

// Methods
const updateQuantity = (itemId, newQuantity) => {
    if (newQuantity < 1) return;
    
    router.patch(route('client.cart.update', itemId), {
        quantity: newQuantity
    }, {
        preserveState: true
    });
};

const removeItem = (itemId) => {
    if (confirm('هل أنت متأكد من إزالة هذا العنصر من السلة؟')) {
        router.delete(route('client.cart.remove', itemId), {
            preserveState: true
        });
    }
};

const applyCoupon = async () => {
    if (!couponCode.value) return;
    
    applyingCoupon.value = true;
    couponError.value = '';
    couponSuccess.value = '';
    
    try {
        await router.post(route('client.cart.coupon'), {
            code: couponCode.value
        }, {
            preserveState: true,
            onSuccess: (page) => {
                if (page.props.flash?.coupon_applied) {
                    couponSuccess.value = 'تم تطبيق كود الخصم بنجاح';
                    discount.value = page.props.flash.discount_amount || 0;
                }
            },
            onError: (errors) => {
                couponError.value = errors.code || 'كود خصم غير صحيح';
            }
        });
    } catch (error) {
        couponError.value = 'حدث خطأ أثناء تطبيق كود الخصم';
    } finally {
        applyingCoupon.value = false;
    }
};

const proceedToCheckout = () => {
    processing.value = true;
    router.visit(route('client.checkout'));
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('ar-SA').format(price);
};
</script>
