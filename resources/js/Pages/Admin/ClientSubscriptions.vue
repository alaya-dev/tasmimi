<template>
    <Head title="اشتراكات العملاء" />

    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                اشتراكات العملاء
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <!-- Header -->
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-medium text-gray-900">
                                قائمة اشتراكات العملاء
                            </h3>
                            <div class="text-sm text-gray-500">
                                إجمالي الاشتراكات: {{ clientSubscriptions.length }}
                            </div>
                        </div>

                        <!-- Table -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            البريد الإلكتروني
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            الاشتراك
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            نوع الاشتراك
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            تاريخ البداية
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            تاريخ النهاية
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            الحالة
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            التجديد التلقائي
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="subscription in clientSubscriptions" :key="subscription.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right">
                                            {{ subscription.client_email }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">
                                            {{ subscription.subscription_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">
                                            {{ getSubscriptionTypeText(subscription) }}
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
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">
                                            <span v-if="subscription.auto_renew" class="text-green-600">
                                                ✓ مفعل
                                            </span>
                                            <span v-else class="text-red-600">
                                                ✗ غير مفعل
                                            </span>
                                        </td>
                                    </tr>
                                    <tr v-if="clientSubscriptions.length === 0">
                                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                            لا توجد اشتراكات
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Statistics -->
                        <div class="mt-8 grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <div class="text-2xl font-bold text-blue-600">
                                    {{ getActiveCount() }}
                                </div>
                                <div class="text-sm text-blue-600">اشتراكات نشطة</div>
                            </div>
                            <div class="bg-yellow-50 p-4 rounded-lg">
                                <div class="text-2xl font-bold text-yellow-600">
                                    {{ getPendingCount() }}
                                </div>
                                <div class="text-sm text-yellow-600">اشتراكات معلقة</div>
                            </div>
                            <div class="bg-red-50 p-4 rounded-lg">
                                <div class="text-2xl font-bold text-red-600">
                                    {{ getCanceledCount() }}
                                </div>
                                <div class="text-sm text-red-600">اشتراكات ملغية</div>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="text-2xl font-bold text-gray-600">
                                    {{ getExpiredCount() }}
                                </div>
                                <div class="text-sm text-gray-600">اشتراكات منتهية</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayoutSidebar.vue';

const props = defineProps({
    clientSubscriptions: {
        type: Array,
        default: () => []
    }
});

const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-GB', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
    }).split('/').reverse().join('/'); // Format: YYYY/MM/DD
};

const getStatusText = (status) => {
    const texts = {
        'active': 'نشط',
        'pending': 'معلق',
        'canceled': 'ملغي',
        'expired': 'منتهي'
    };
    return texts[status] || status;
};

const getStatusClass = (status) => {
    const classes = {
        'active': 'bg-green-100 text-green-800',
        'pending': 'bg-yellow-100 text-yellow-800',
        'canceled': 'bg-red-100 text-red-800',
        'expired': 'bg-gray-100 text-gray-800'
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

const getActiveCount = () => {
    return props.clientSubscriptions.filter(sub => sub.status === 'active').length;
};

const getPendingCount = () => {
    return props.clientSubscriptions.filter(sub => sub.status === 'pending').length;
};

const getCanceledCount = () => {
    return props.clientSubscriptions.filter(sub => sub.status === 'canceled').length;
};

const getExpiredCount = () => {
    return props.clientSubscriptions.filter(sub => sub.status === 'expired').length;
};

const getSubscriptionTypeText = (subscription) => {
    // Si on a duration_days, on affiche le nombre de jours
    if (subscription.duration_days) {
        return `مخصص (${subscription.duration_days} يوم)`;
    }

    // Sinon on utilise le type
    const types = {
        'weekly': 'أسبوعي',
        'monthly': 'شهري',
        'yearly': 'سنوي'
    };

    return types[subscription.subscription_type] || subscription.subscription_type || 'غير محدد';
};
</script>
