<template>
    <Head :title="`فاتورة ${invoice.invoice_number}`" />

    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center no-print">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    تفاصيل الفاتورة {{ invoice.invoice_number }}
                </h2>
                <Link
                    :href="route('admin.invoices.index')"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
                >
                    العودة للقائمة
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <!-- Invoice Header -->
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex justify-between items-start">
                            <div class="text-right">
                                <h1 class="text-2xl font-bold text-gray-900">فاتورة</h1>
                                <p class="text-lg text-gray-600 mt-1">{{ invoice.invoice_number }}</p>
                            </div>
                            <div class="text-left">
                                <span :class="getStatusClass(invoice.status)" class="px-3 py-1 text-sm font-medium rounded-full">
                                    {{ invoice.status_arabic }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Invoice Details -->
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Customer Information -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4 text-right">معلومات العميل</h3>
                                <div class="space-y-2 text-right">
                                    <p><span class="font-medium">البريد الإلكتروني:</span> {{ invoice.user.email }}</p>
                                    <p v-if="invoice.user.phone"><span class="font-medium">الهاتف:</span> {{ invoice.user.phone }}</p>
                                </div>
                            </div>

                            <!-- Invoice Information -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4 text-right">معلومات الفاتورة</h3>
                                <div class="space-y-2 text-right">
                                    <p><span class="font-medium">تاريخ الفاتورة:</span> {{ formatDate(invoice.invoice_date) }}</p>
                                    <p><span class="font-medium">تاريخ الاستحقاق:</span> {{ formatDate(invoice.due_date) }}</p>
                                    <p v-if="invoice.paid_at"><span class="font-medium">تاريخ الدفع:</span> {{ formatDate(invoice.paid_at) }}</p>
                                    <p><span class="font-medium">العملة:</span> {{ invoice.currency }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Item Details -->
                        <div class="mt-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4 text-right">تفاصيل العنصر</h3>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex justify-between items-center">
                                    <div class="text-right">
                                        <p class="font-medium text-gray-900">{{ invoice.item_name }}</p>
                                        <p class="text-sm text-gray-600">
                                            <span :class="getTypeClass(invoice.type)" class="px-2 py-1 text-xs font-medium rounded-full">
                                                {{ invoice.type_arabic }}
                                            </span>
                                        </p>

                                        <!-- Subscription Details -->
                                        <div v-if="invoice.type === 'subscription' && invoice.subscription_details" class="mt-2 text-sm text-gray-600">
                                            <p><span class="font-medium">نوع الاشتراك:</span> {{ invoice.subscription_details.type }}</p>
                                            <p v-if="invoice.subscription_details.duration_days">
                                                <span class="font-medium">المدة:</span> {{ invoice.subscription_details.duration_days }} يوم
                                            </p>
                                            <p v-if="invoice.subscription_details.description">
                                                <span class="font-medium">الوصف:</span> {{ invoice.subscription_details.description }}
                                            </p>
                                        </div>

                                        <!-- Template Details -->
                                        <div v-if="invoice.type === 'template' && invoice.template_details" class="mt-2 text-sm text-gray-600">
                                            <p v-if="invoice.template_details.category">
                                                <span class="font-medium">الفئة:</span> {{ invoice.template_details.category.name }}
                                            </p>
                                            <p v-if="invoice.template_details.price">
                                                <span class="font-medium">سعر القالب:</span> {{ formatCurrency(invoice.template_details.price) }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-left">
                                        <p class="text-2xl font-bold text-gray-900">{{ formatCurrency(invoice.amount) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Information -->
                        <div class="mt-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4 text-right">معلومات الدفع</h3>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-right">
                                    <div>
                                        <p class="text-sm text-gray-600">المبلغ الإجمالي</p>
                                        <p class="text-lg font-bold text-gray-900">{{ formatCurrency(invoice.amount) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">حالة الدفع</p>
                                        <span :class="getStatusClass(invoice.status)" class="px-2 py-1 text-xs font-medium rounded-full">
                                            {{ invoice.status_arabic }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div v-if="invoice.description" class="mt-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4 text-right">معلومات إضافية</h3>
                            <div class="bg-gray-50 rounded-lg p-4 text-right">
                                <p class="text-sm font-medium text-gray-700">الوصف:</p>
                                <p class="text-sm text-gray-600 mt-1">{{ invoice.description }}</p>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-8 flex justify-end space-x-4 space-x-reverse no-print">
                            <button
                                @click="printInvoice"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-flex items-center"
                            >
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                </svg>
                                طباعة الفاتورة
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayoutSidebar.vue';

const props = defineProps({
    invoice: {
        type: Object,
        required: true
    }
});

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('ar-SA', {
        style: 'currency',
        currency: 'SAR'
    }).format(amount);
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('ar-SA');
};

const getStatusClass = (status) => {
    const classes = {
        'paid': 'bg-green-100 text-green-800',
        'pending': 'bg-yellow-100 text-yellow-800',
        'failed': 'bg-red-100 text-red-800',
        'canceled': 'bg-gray-100 text-gray-800',
        'refunded': 'bg-purple-100 text-purple-800'
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

const getTypeClass = (type) => {
    const classes = {
        'subscription': 'bg-blue-100 text-blue-800',
        'template': 'bg-indigo-100 text-indigo-800'
    };
    return classes[type] || 'bg-gray-100 text-gray-800';
};

const printInvoice = () => {
    window.print();
};
</script>

<style>
@media print {
    /* Hide navigation and action elements */
    .no-print {
        display: none !important;
    }

    /* Hide specific layout elements */
    nav,
    .navbar,
    .sidebar,
    header[class*="bg-"],
    .fixed,
    .sticky {
        display: none !important;
    }

    body {
        font-family: 'Arial', sans-serif;
        margin: 0;
        padding: 20px;
        background: white !important;
    }

    /* Adjust main content for print */
    .py-12 {
        padding: 0 !important;
    }

    .max-w-4xl {
        max-width: 100% !important;
        margin: 0 !important;
    }

    .sm\\:px-6, .lg\\:px-8 {
        padding: 0 !important;
    }

    /* Remove shadows and borders for clean print */
    .shadow-sm, .sm\\:rounded-lg, .overflow-hidden {
        box-shadow: none !important;
        border-radius: 0 !important;
    }

    /* Ensure content is visible */
    .bg-white {
        background: white !important;
    }

    .text-gray-900, .text-gray-800, .text-gray-700, .text-gray-600 {
        color: black !important;
    }

    /* Keep status badges visible */
    .px-3.py-1, .px-2.py-1 {
        border: 1px solid black !important;
        background: white !important;
        color: black !important;
    }
}
</style>
