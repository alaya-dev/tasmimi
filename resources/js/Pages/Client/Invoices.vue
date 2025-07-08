<template>
    <Head :title="__('client.invoices')" />

    <ClientLayout>
        <div class="py-12 bg-gray-50 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Page Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">{{ __('client.invoices') }}</h1>
                    <p class="mt-2 text-gray-600">عرض وتحميل فواتيرك</p>
                </div>

                <!-- Invoices List -->
                <div v-if="invoices.length > 0" class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        رقم الفاتورة
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        التاريخ
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        المبلغ
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        الحالة
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        الإجراءات
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr 
                                    v-for="invoice in invoices" 
                                    :key="invoice.id"
                                    class="hover:bg-gray-50"
                                >
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            #{{ invoice.number }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ formatDate(invoice.date) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ formatPrice(invoice.amount) }} ريال
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span 
                                            :class="getStatusColor(invoice.status)"
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                        >
                                            {{ getStatusText(invoice.status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-3" :class="isRTL ? 'space-x-reverse' : ''">
                                            <button 
                                                @click="viewInvoice(invoice)"
                                                class="text-blue-600 hover:text-blue-900"
                                            >
                                                عرض
                                            </button>
                                            <button 
                                                @click="downloadInvoice(invoice)"
                                                class="text-green-600 hover:text-green-900"
                                            >
                                                تحميل
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="text-center py-16">
                    <svg class="w-24 h-24 text-gray-400 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="text-2xl font-semibold text-gray-900 mb-4">لا توجد فواتير</h3>
                    <p class="text-gray-600 mb-8 max-w-md mx-auto">
                        لم يتم إنشاء أي فواتير حتى الآن. ستظهر فواتيرك هنا بعد إتمام طلباتك.
                    </p>
                    <Link 
                        :href="route('client.templates')" 
                        class="bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors inline-flex items-center"
                    >
                        ابدأ التسوق
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
    invoices: {
        type: Array,
        default: () => []
    }
});

// Composables
const { __, isRTL } = useTranslations();

// Sample invoices data
const sampleInvoices = [
    {
        id: 1,
        number: 'INV-2024-001',
        date: '2024-01-20',
        amount: 74.75,
        status: 'paid'
    },
    {
        id: 2,
        number: 'INV-2024-002',
        date: '2024-01-15',
        amount: 125.50,
        status: 'pending'
    }
];

const invoices = props.invoices.length > 0 ? props.invoices : sampleInvoices;

// Methods
const getStatusColor = (status) => {
    const colors = {
        paid: 'bg-green-100 text-green-800',
        pending: 'bg-yellow-100 text-yellow-800',
        overdue: 'bg-red-100 text-red-800'
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};

const getStatusText = (status) => {
    const texts = {
        paid: 'مدفوعة',
        pending: 'في الانتظار',
        overdue: 'متأخرة'
    };
    return texts[status] || status;
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('ar-SA');
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('ar-SA').format(price);
};

const viewInvoice = (invoice) => {
    // Open invoice in new tab or modal
    console.log('View invoice:', invoice);
};

const downloadInvoice = (invoice) => {
    // Download PDF invoice
    console.log('Download invoice:', invoice);
};
</script>
