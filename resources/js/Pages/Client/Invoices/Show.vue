<template>
    <Head :title="`فاتورة ${invoice.invoice_number}`" />

    <ClientLayout>
        <template #header>
            <div class="flex justify-between items-center no-print">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    فاتورة {{ invoice.invoice_number }}
                </h2>
                <Link
                    :href="route('client.invoices.index')"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
                >
                    العودة للفواتير
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

                            <!-- Payment Status -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4 text-right">حالة الدفع</h3>
                                <div class="text-right">
                                    <span :class="getStatusClass(invoice.status)" class="px-3 py-1 text-sm font-medium rounded-full">
                                        {{ invoice.status_arabic }}
                                    </span>
                                    <p v-if="invoice.status === 'pending'" class="text-sm text-yellow-600 mt-2">
                                        هذه الفاتورة في انتظار الدفع
                                    </p>
                                    <p v-else-if="invoice.status === 'paid'" class="text-sm text-green-600 mt-2">
                                        تم دفع هذه الفاتورة بنجاح
                                    </p>
                                    <p v-else-if="invoice.status === 'failed'" class="text-sm text-red-600 mt-2">
                                        فشل في دفع هذه الفاتورة
                                    </p>
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
                                            <div v-if="invoice.subscription_details.features" class="mt-2">
                                                <p class="font-medium">الميزات المتضمنة:</p>
                                                <ul class="list-disc list-inside text-xs mt-1">
                                                    <li v-for="feature in invoice.subscription_details.features" :key="feature">
                                                        {{ feature }}
                                                    </li>
                                                </ul>
                                            </div>
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

                        <!-- Payment Summary -->
                        <div class="mt-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4 text-right">ملخص الدفع</h3>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex justify-between items-center text-right">
                                    <div>
                                        <p class="text-sm text-gray-600">المبلغ الإجمالي</p>
                                        <p class="text-2xl font-bold text-gray-900">{{ formatCurrency(invoice.amount) }}</p>
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

                        <!-- Description -->
                        <div v-if="invoice.description" class="mt-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4 text-right">تفاصيل إضافية</h3>
                            <div class="bg-gray-50 rounded-lg p-4 text-right">
                                <p class="text-sm text-gray-600">{{ invoice.description }}</p>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-8 flex justify-end no-print">
                            <button
                                @click="downloadInvoice"
                                class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded inline-flex items-center"
                            >
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                تحميل PDF
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </ClientLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ClientLayout from '@/Layouts/ClientLayout.vue';

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

const downloadInvoice = () => {
    // This would typically generate and download a PDF
    // For now, we'll just print
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
    .sticky,
    .bg-gradient-to-br,
    .from-purple-600,
    .to-blue-600 {
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

    /* Hide any background gradients or colors */
    .min-h-screen {
        min-height: auto !important;
        background: white !important;
    }

    /* Ensure content backgrounds are white but keep text visible */
    .bg-white, .bg-gray-50 {
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
