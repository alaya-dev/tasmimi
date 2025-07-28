<template>
    <Head title="إدارة الاشتراك - Bitaqati" />

    <ClientLayout>
        <div class="py-12" dir="rtl">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">إدارة الاشتراك</h1>
                    <p class="text-gray-600">تحكم في اشتراكك وتاريخ المدفوعات</p>
                </div>

                <!-- Current Subscription -->
                <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-8">
                    <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-4">
                        <h2 class="text-xl font-semibold text-white">الاشتراك الحالي</h2>
                    </div>
                    
                    <div class="p-6">
                        <div v-if="activeSubscription" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <!-- Plan Info -->
                            <div class="text-center">
                                <div class="text-2xl font-bold text-gray-900 mb-1">
                                    {{ activeSubscription.subscription?.name || 'غير محدد' }}
                                </div>
                                <div class="text-gray-600">الخطة الحالية</div>
                            </div>

                            <!-- Status -->
                            <div class="text-center">
                                <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium mb-1"
                                     :class="getStatusClass(activeSubscription.status)">
                                    {{ getStatusText(activeSubscription.status) }}
                                </div>
                                <div class="text-gray-600">الحالة</div>
                            </div>

                            <!-- End Date -->
                            <div class="text-center">
                                <div class="text-2xl font-bold text-gray-900 mb-1">
                                    {{ formatDate(activeSubscription.ends_at) }}
                                </div>
                                <div class="text-gray-600">تاريخ الانتهاء</div>
                            </div>

                            <!-- Days Remaining -->
                            <div class="text-center">
                                <div class="text-2xl font-bold mb-1"
                                     :class="getDaysRemainingClass(activeSubscription)">
                                    {{ getDaysRemaining(activeSubscription) }}
                                </div>
                                <div class="text-gray-600">يوم متبقي</div>
                            </div>
                        </div>

                        <div v-else class="text-center py-8">
                            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">لا يوجد اشتراك نشط</h3>
                            <p class="text-gray-600 mb-4">اشترك الآن للحصول على وصول كامل لجميع الميزات</p>
                            <a href="/client/pricing" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                عرض الخطط المتاحة
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div v-if="activeSubscription" class="bg-white shadow-lg rounded-lg p-6 mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">إجراءات الاشتراك</h3>
                    <div class="flex flex-wrap gap-4">
                        <button
                            @click="showCancelModal = true"
                            class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors"
                        >
                            إلغاء الاشتراك
                        </button>
                        <a href="/client/pricing" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                            ترقية الخطة
                        </a>
                    </div>
                </div>

                <!-- Subscription History -->
                <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-8">
                    <div class="bg-gray-50 px-6 py-4 border-b">
                        <h2 class="text-xl font-semibold text-gray-900">تاريخ الاشتراكات</h2>
                    </div>
                    
                    <div class="p-6">
                        <div v-if="subscriptionHistory && subscriptionHistory.length > 0" class="space-y-4">
                            <div v-for="subscription in subscriptionHistory" :key="subscription.id" 
                                 class="border border-gray-200 rounded-lg p-4">
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    <div>
                                        <label class="text-sm font-medium text-gray-600">الخطة</label>
                                        <p class="text-gray-900">{{ subscription.subscription?.name || 'غير محدد' }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-600">الحالة</label>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                              :class="getStatusClass(subscription.status)">
                                            {{ getStatusText(subscription.status) }}
                                        </span>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-600">تاريخ البداية</label>
                                        <p class="text-gray-900">{{ formatDate(subscription.starts_at) }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-600">تاريخ الانتهاء</label>
                                        <p class="text-gray-900">{{ formatDate(subscription.ends_at) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div v-else class="text-center py-8">
                            <p class="text-gray-500">لا يوجد تاريخ اشتراكات</p>
                        </div>
                    </div>
                </div>

                <!-- Payment History -->
                <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                    <div class="bg-gray-50 px-6 py-4 border-b">
                        <h2 class="text-xl font-semibold text-gray-900">تاريخ المدفوعات</h2>
                    </div>
                    
                    <div class="p-6">
                        <div v-if="paymentHistory && paymentHistory.length > 0" class="space-y-4">
                            <div v-for="payment in paymentHistory" :key="payment.id" 
                                 class="border border-gray-200 rounded-lg p-4">
                                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                                    <div>
                                        <label class="text-sm font-medium text-gray-600">المبلغ</label>
                                        <p class="text-gray-900 font-semibold">{{ payment.amount }} {{ payment.currency }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-600">الخطة</label>
                                        <p class="text-gray-900">{{ payment.subscription?.name || 'غير محدد' }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-600">طريقة الدفع</label>
                                        <p class="text-gray-900">{{ getPaymentMethodText(payment.payment_method) }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-600">تاريخ الدفع</label>
                                        <p class="text-gray-900">{{ formatDate(payment.paid_at) }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-600">الحالة</label>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            مدفوع
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div v-else class="text-center py-8">
                            <p class="text-gray-500">لا يوجد تاريخ مدفوعات</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cancel Subscription Modal -->
        <div v-if="showCancelModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3 text-center">
                    <h3 class="text-lg font-medium text-gray-900">تأكيد إلغاء الاشتراك</h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-500">
                            هل أنت متأكد من رغبتك في إلغاء الاشتراك؟ ستفقد الوصول إلى جميع الميزات المدفوعة.
                        </p>
                    </div>
                    <div class="flex justify-center gap-4 px-4 py-3">
                        <button
                            @click="cancelSubscription"
                            :disabled="cancelling"
                            class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 disabled:opacity-50"
                        >
                            {{ cancelling ? 'جاري الإلغاء...' : 'نعم، إلغاء الاشتراك' }}
                        </button>
                        <button
                            @click="showCancelModal = false"
                            class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400"
                        >
                            إلغاء
                        </button>
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
    activeSubscription: {
        type: Object,
        default: null
    },
    subscriptionHistory: {
        type: Array,
        default: () => []
    },
    paymentHistory: {
        type: Array,
        default: () => []
    }
});

const showCancelModal = ref(false);
const cancelling = ref(false);

const formatDate = (dateString) => {
    if (!dateString) return 'غير محدد';
    try {
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return 'تاريخ غير صحيح';
        return date.toLocaleDateString('en-GB', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit'
        }).split('/').reverse().join('/'); // Format: YYYY/MM/DD
    } catch (error) {
        return 'تاريخ غير صحيح';
    }
};

const getStatusClass = (status) => {
    const classes = {
        'active': 'bg-green-100 text-green-800',
        'canceled': 'bg-red-100 text-red-800',
        'expired': 'bg-gray-100 text-gray-800',
        'pending': 'bg-yellow-100 text-yellow-800'
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

const getStatusText = (status) => {
    const texts = {
        'active': 'نشط',
        'canceled': 'ملغي',
        'expired': 'منتهي',
        'pending': 'قيد الانتظار'
    };
    return texts[status] || status;
};

const getDaysRemaining = (subscription) => {
    if (!subscription || !subscription.ends_at) return 0;
    const endDate = new Date(subscription.ends_at);
    const today = new Date();
    const diffTime = endDate - today;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    return Math.max(0, diffDays);
};

const getDaysRemainingClass = (subscription) => {
    if (!subscription) return 'text-gray-600';
    const days = getDaysRemaining(subscription);
    if (days <= 3) return 'text-red-600';
    if (days <= 7) return 'text-yellow-600';
    return 'text-green-600';
};

const getPaymentMethodText = (method) => {
    const methods = {
        'card': 'بطاقة ائتمان',
        'creditcard': 'بطاقة ائتمان',
        'moyasar_invoice': 'فاتورة موياسار',
        'bank_transfer': 'تحويل بنكي'
    };
    return methods[method] || method;
};

const cancelSubscription = async () => {
    cancelling.value = true;
    
    try {
        await router.post('/client/subscription/cancel');
        showCancelModal.value = false;
    } catch (error) {
        console.error('Error cancelling subscription:', error);
    } finally {
        cancelling.value = false;
    }
};
</script>
