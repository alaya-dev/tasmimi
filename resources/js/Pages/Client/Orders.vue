<template>
    <Head :title="__('client.orders')" />

    <ClientLayout>
        <div class="py-12 bg-gray-50 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Page Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">{{ __('client.order_history') }}</h1>
                    <p class="mt-2 text-gray-600">تتبع طلباتك وحالة التسليم</p>
                </div>

                <!-- Orders List -->
                <div v-if="orders.length > 0" class="space-y-6">
                    <div 
                        v-for="order in orders" 
                        :key="order.id"
                        class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden"
                    >
                        <!-- Order Header -->
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        {{ __('client.order_number') }}: #{{ order.id }}
                                    </h3>
                                    <p class="text-sm text-gray-600">
                                        {{ __('client.order_date') }}: {{ formatDate(order.created_at) }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <span 
                                        :class="getStatusColor(order.status)"
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                                    >
                                        {{ getStatusText(order.status) }}
                                    </span>
                                    <div class="text-lg font-semibold text-gray-900 mt-1">
                                        {{ formatPrice(order.total) }} ريال
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Order Items -->
                        <div class="px-6 py-4">
                            <div class="space-y-3">
                                <div 
                                    v-for="item in order.items" 
                                    :key="item.id"
                                    class="flex items-center space-x-4" 
                                    :class="isRTL ? 'space-x-reverse' : ''"
                                >
                                    <div class="w-16 h-12 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                                        <div 
                                            class="w-full h-full flex items-center justify-center text-xs"
                                            :style="{ background: item.preview_background || '#f3f4f6' }"
                                        >
                                            {{ item.name.substring(0, 3) }}
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-900">{{ item.name }}</h4>
                                        <p class="text-sm text-gray-600">الكمية: {{ item.quantity }}</p>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-medium">{{ formatPrice(item.price * item.quantity) }} ريال</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Order Actions -->
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                            <div class="flex items-center justify-between">
                                <div class="flex space-x-3" :class="isRTL ? 'space-x-reverse' : ''">
                                    <button 
                                        @click="viewOrder(order)"
                                        class="text-blue-600 hover:text-blue-800 font-medium text-sm"
                                    >
                                        {{ __('client.view_order') }}
                                    </button>
                                    <button 
                                        v-if="order.status === 'completed'"
                                        @click="downloadInvoice(order)"
                                        class="text-green-600 hover:text-green-800 font-medium text-sm"
                                    >
                                        {{ __('client.download_invoice') }}
                                    </button>
                                </div>
                                <div class="text-sm text-gray-500">
                                    آخر تحديث: {{ formatDate(order.updated_at) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="text-center py-16">
                    <svg class="w-24 h-24 text-gray-400 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="text-2xl font-semibold text-gray-900 mb-4">لا توجد طلبات</h3>
                    <p class="text-gray-600 mb-8 max-w-md mx-auto">
                        لم تقم بتقديم أي طلبات حتى الآن. ابدأ في تصفح قوالبنا وأنشئ طلبك الأول.
                    </p>
                    <Link 
                        :href="route('client.templates')" 
                        class="bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors inline-flex items-center"
                    >
                        تصفح القوالب
                    </Link>
                </div>
            </div>
        </div>
    </ClientLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ClientLayout from '@/Layouts/ClientLayout.vue';
import { useTranslations } from '@/Composables/useTranslations';

// Props
const props = defineProps({
    orders: {
        type: Array,
        default: () => []
    }
});

// Composables
const { __, isRTL } = useTranslations();

// Sample orders data
const sampleOrders = [
    {
        id: '12345',
        status: 'completed',
        total: 74.75,
        created_at: '2024-01-15',
        updated_at: '2024-01-20',
        items: [
            {
                id: 1,
                name: 'بطاقة عمل احترافية',
                quantity: 2,
                price: 25,
                preview_background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)'
            }
        ]
    }
];

const orders = props.orders.length > 0 ? props.orders : sampleOrders;

// Methods
const getStatusColor = (status) => {
    const colors = {
        pending: 'bg-yellow-100 text-yellow-800',
        processing: 'bg-blue-100 text-blue-800',
        completed: 'bg-green-100 text-green-800',
        cancelled: 'bg-red-100 text-red-800'
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};

const getStatusText = (status) => {
    const texts = {
        pending: __('client.pending'),
        processing: __('client.processing'),
        completed: __('client.completed'),
        cancelled: __('client.cancelled')
    };
    return texts[status] || status;
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('ar-SA');
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('ar-SA').format(price);
};

const viewOrder = (order) => {
    // Navigate to order details
    console.log('View order:', order);
};

const downloadInvoice = (order) => {
    // Download invoice
    console.log('Download invoice for order:', order);
};
</script>
