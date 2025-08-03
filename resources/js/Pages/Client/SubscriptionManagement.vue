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
                            <button
                                @click="$inertia.visit(route('client.subscriptions.index'))"
                                class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors"
                            >
                                عرض الخطط المتاحة
                            </button>
                        </div>

                        <!-- Action Buttons -->
                        <div v-if="activeSubscription" class="mt-6 flex flex-wrap gap-4 justify-center">
                            <button
                                @click="$inertia.visit(route('client.subscriptions.index'))"
                                class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors"
                            >
                                تغيير الخطة
                            </button>

                            <button
                                v-if="activeSubscription.status === 'active'"
                                @click="confirmCancelSubscription"
                                class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition-colors"
                            >
                                إلغاء الاشتراك
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Subscription History -->
                <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-8">
                    <div class="bg-gray-50 px-6 py-4 border-b">
                        <h2 class="text-xl font-semibold text-gray-900">تاريخ الاشتراكات</h2>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        الخطة
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        تاريخ البداية
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        تاريخ النهاية
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        الحالة
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="subscription in subscriptionHistory" :key="subscription.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right">
                                        {{ subscription.subscription.name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">
                                        {{ formatDate(subscription.starts_at) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">
                                        {{ formatDate(subscription.ends_at) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                              :class="getStatusClass(subscription.status)">
                                            {{ getStatusText(subscription.status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr v-if="subscriptionHistory.length === 0">
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                        لا يوجد تاريخ اشتراكات
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Payment History -->
                <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                    <div class="bg-gray-50 px-6 py-4 border-b">
                        <h2 class="text-xl font-semibold text-gray-900">تاريخ المدفوعات</h2>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        الخطة
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        المبلغ
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        تاريخ الدفع
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        طريقة الدفع
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="payment in paymentHistory" :key="payment.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right">
                                        {{ payment.subscription.name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">
                                        {{ payment.amount }} {{ payment.currency }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">
                                        {{ formatDate(payment.paid_at) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">
                                        {{ getPaymentMethodText(payment.payment_method) }}
                                    </td>
                                </tr>
                                <tr v-if="paymentHistory.length === 0">
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                        لا يوجد تاريخ مدفوعات
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cancel Confirmation Modal -->
        <div v-if="showCancelModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click="showCancelModal = false">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white" @click.stop>
                <div class="mt-3 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mt-2">تأكيد إلغاء الاشتراك</h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-500">
                            هل أنت متأكد من إلغاء اشتراكك؟ ستفقد الوصول إلى جميع الميزات المدفوعة.
                        </p>
                    </div>
                    <div class="items-center px-4 py-3">
                        <button
                            @click="cancelSubscription"
                            :disabled="cancelling"
                            class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300 disabled:opacity-50"
                        >
                            <span v-if="cancelling">جاري الإلغاء...</span>
                            <span v-else>نعم، إلغاء الاشتراك</span>
                        </button>
                        <button
                            @click="showCancelModal = false"
                            class="mt-3 px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300"
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
    const date = new Date(dateString);
    return date.toLocaleDateString('en-GB', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
    }).split('/').reverse().join('/'); // Format: YYYY/MM/DD
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
    const endDate = new Date(subscription.ends_at);
    const today = new Date();
    const diffTime = endDate - today;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    return Math.max(0, diffDays);
};

const getDaysRemainingClass = (subscription) => {
    const days = getDaysRemaining(subscription);
    if (days <= 3) return 'text-red-600';
    if (days <= 7) return 'text-yellow-600';
    return 'text-green-600';
};

const getPaymentMethodText = (method) => {
    const methods = {
        'card': 'بطاقة ائتمان',
        'creditcard': 'بطاقة ائتمان',
        'stcpay': 'STC Pay',
        'applepay': 'Apple Pay'
    };
    return methods[method] || method;
};

const confirmCancelSubscription = () => {
    showCancelModal.value = true;
};

const cancelSubscription = async () => {
    cancelling.value = true;

    try {
        const response = await fetch(route('client.subscription.cancel'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        const result = await response.json();

        if (result.success) {
            showCancelModal.value = false;
            router.reload();
        } else {
            alert(result.error || 'حدث خطأ أثناء إلغاء الاشتراك');
        }
    } catch (error) {
        console.error('Cancel subscription error:', error);
        alert('حدث خطأ أثناء إلغاء الاشتراك');
    } finally {
        cancelling.value = false;
    }
};
</script>
