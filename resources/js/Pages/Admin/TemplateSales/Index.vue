<template>
    <Head title="القوالب المباعة" />

    <AdminLayoutSidebar>
        <div class="p-6">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">القوالب المباعة</h1>
                    <p class="text-gray-600 mt-1">إحصائيات ومبيعات القوالب</p>
                </div>
                <div class="flex space-x-3 space-x-reverse">
                    <button
                        @click="exportData"
                        class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 flex items-center space-x-2 space-x-reverse"
                    >
                        <i class="fas fa-download"></i>
                        <span>تصدير البيانات</span>
                    </button>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Sales -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <i class="fas fa-dollar-sign text-xl"></i>
                        </div>
                        <div class="mr-4 text-right">
                            <p class="text-sm font-medium text-gray-600">إجمالي المبيعات</p>
                            <p class="text-2xl font-bold text-gray-900">{{ formatCurrency(stats.total_sales) }}</p>
                            <p class="text-sm text-green-600" v-if="stats.sales_growth > 0">
                                <i class="fas fa-arrow-up"></i>
                                +{{ formatNumber(stats.sales_growth) }}% هذا الشهر
                            </p>
                            <p class="text-sm text-red-600" v-else-if="stats.sales_growth < 0">
                                <i class="fas fa-arrow-down"></i>
                                {{ formatNumber(stats.sales_growth) }}% هذا الشهر
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Total Purchases -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <i class="fas fa-shopping-cart text-xl"></i>
                        </div>
                        <div class="mr-4 text-right">
                            <p class="text-sm font-medium text-gray-600">عدد المشتريات</p>
                            <p class="text-2xl font-bold text-gray-900">{{ formatNumber(stats.total_purchases) }}</p>
                            <p class="text-sm text-blue-600" v-if="stats.purchases_growth > 0">
                                <i class="fas fa-arrow-up"></i>
                                +{{ formatNumber(stats.purchases_growth) }}% هذا الشهر
                            </p>
                            <p class="text-sm text-red-600" v-else-if="stats.purchases_growth < 0">
                                <i class="fas fa-arrow-down"></i>
                                {{ formatNumber(stats.purchases_growth) }}% هذا الشهر
                            </p>
                        </div>
                    </div>
                </div>


            </div>

            <!-- Top Selling Templates -->
            <div class="bg-white rounded-lg shadow mb-8">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">أكثر القوالب مبيعاً</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div 
                            v-for="(item, index) in topTemplates" 
                            :key="item.template.id"
                            class="flex items-center justify-between p-4 bg-gray-50 rounded-lg"
                        >
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold text-sm">
                                    {{ index + 1 }}
                                </div>
                                <div class="mr-4">
                                    <h3 class="font-medium text-gray-900">{{ item.template.name }}</h3>
                                    <p class="text-sm text-gray-600">{{ formatNumber(item.sales_count) }} مبيعة</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-green-600">{{ formatCurrency(item.total_revenue) }}</p>
                                <p class="text-sm text-gray-600">{{ formatCurrency(item.template.price) }} للقطعة</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">تصفية البيانات</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Search -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2 text-right">البحث</label>
                            <input
                                v-model="form.search"
                                type="text"
                                placeholder="اسم القالب أو البريد الإلكتروني"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-right"
                            />
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2 text-right">الحالة</label>
                            <select
                                v-model="form.status"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-right"
                            >
                                <option value="">جميع الحالات</option>
                                <option v-for="(label, value) in statuses" :key="value" :value="value">
                                    {{ label }}
                                </option>
                            </select>
                        </div>

                        <!-- Date From -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2 text-right">من تاريخ</label>
                            <input
                                v-model="form.date_from"
                                type="date"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                        </div>

                        <!-- Date To -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2 text-right">إلى تاريخ</label>
                            <input
                                v-model="form.date_to"
                                type="date"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                        </div>
                    </div>

                    <div class="flex justify-end mt-4 space-x-3 space-x-reverse">
                        <button
                            @click="clearFilters"
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
                        >
                            مسح الفلاتر
                        </button>
                        <button
                            @click="applyFilters"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                        >
                            تطبيق الفلاتر
                        </button>
                    </div>
                </div>
            </div>

            <!-- Purchases Table -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">قائمة المشتريات</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    القالب
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    المشتري
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    المبلغ
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    الحالة
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    تاريخ الشراء
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    تاريخ الدفع
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="purchase in purchases.data" :key="purchase.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="text-right">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ purchase.template?.name || 'قالب محذوف' }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ purchase.template?.category?.name || 'غير محدد' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="text-sm text-gray-900">{{ purchase.user?.email || 'مستخدم محذوف' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ formatCurrency(purchase.amount) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <span :class="getStatusClass(purchase.status)" class="px-2 py-1 text-xs font-semibold rounded-full">
                                        {{ statuses[purchase.status] || purchase.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">
                                    {{ formatDate(purchase.created_at) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">
                                    {{ purchase.paid_at ? formatDate(purchase.paid_at) : 'غير مدفوع' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200" v-if="purchases.links">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            عرض {{ purchases.from }} إلى {{ purchases.to }} من {{ purchases.total }} نتيجة
                        </div>
                        <div class="flex space-x-1 space-x-reverse">
                            <Link
                                v-for="link in purchases.links"
                                :key="link.label"
                                :href="link.url"
                                v-html="link.label"
                                :class="[
                                    'px-3 py-2 text-sm border rounded',
                                    link.active
                                        ? 'bg-blue-600 text-white border-blue-600'
                                        : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                                ]"
                                :disabled="!link.url"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayoutSidebar>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayoutSidebar from '@/Layouts/AdminLayoutSidebar.vue'

const props = defineProps({
    purchases: Object,
    stats: Object,
    topTemplates: Array,
    filters: Object,
    statuses: Object,
})

// Form for filters
const form = reactive({
    search: props.filters?.search || '',
    status: props.filters?.status || '',
    date_from: props.filters?.date_from || '',
    date_to: props.filters?.date_to || '',
})

// Methods

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-GB', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    }).replace(/(\d{2})\/(\d{2})\/(\d{4})/, '$3-$2-$1') // Convert DD/MM/YYYY to YYYY-MM-DD
}

// Format numbers in Western format (not Arabic)
const formatNumber = (number) => {
    return new Intl.NumberFormat('en-US').format(number)
}

// Format currency in Western format
const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(amount) + ' ر.س.'
}

const getStatusClass = (status) => {
    const classes = {
        'paid': 'bg-green-100 text-green-800',
        'pending': 'bg-yellow-100 text-yellow-800',
        'failed': 'bg-red-100 text-red-800',
        'refunded': 'bg-gray-100 text-gray-800',
    }
    return classes[status] || 'bg-gray-100 text-gray-800'
}

const applyFilters = () => {
    router.get(route('admin.template-sales.index'), form, {
        preserveState: true,
        replace: true,
    })
}

const clearFilters = () => {
    Object.keys(form).forEach(key => {
        form[key] = ''
    })
    applyFilters()
}

const exportData = () => {
    const params = new URLSearchParams(form).toString()
    window.location.href = route('admin.template-sales.export') + '?' + params
}
</script>
