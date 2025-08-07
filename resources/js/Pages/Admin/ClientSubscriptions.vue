<template>
    <Head title="اشتراكات العملاء" />

    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    اشتراكات العملاء
                </h2>
                
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                <i class="fas fa-chart-bar text-xl"></i>
                            </div>
                            <div class="mr-4 text-right">
                                <p class="text-sm font-medium text-gray-600">إجمالي الاشتراكات</p>
                                <p class="text-2xl font-bold text-gray-900">{{ stats.total }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 text-green-600">
                                <i class="fas fa-check-circle text-xl"></i>
                            </div>
                            <div class="mr-4 text-right">
                                <p class="text-sm font-medium text-gray-600">اشتراكات نشطة</p>
                                <p class="text-2xl font-bold text-green-600">{{ stats.active }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                                <i class="fas fa-clock text-xl"></i>
                            </div>
                            <div class="mr-4 text-right">
                                <p class="text-sm font-medium text-gray-600">اشتراكات معلقة</p>
                                <p class="text-2xl font-bold text-yellow-600">{{ stats.pending }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-red-100 text-red-600">
                                <i class="fas fa-times-circle text-xl"></i>
                            </div>
                            <div class="mr-4 text-right">
                                <p class="text-sm font-medium text-gray-600">اشتراكات ملغية</p>
                                <p class="text-2xl font-bold text-red-600">{{ stats.canceled }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-gray-100 text-gray-600">
                                <i class="fas fa-calendar-times text-xl"></i>
                            </div>
                            <div class="mr-4 text-right">
                                <p class="text-sm font-medium text-gray-600">اشتراكات منتهية</p>
                                <p class="text-2xl font-bold text-gray-600">{{ stats.expired }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <!-- Header -->
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-medium text-gray-900">
                                قائمة اشتراكات العملاء
                            </h3>
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
                                            الإجراءات
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
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                            <div v-if="subscription.status === 'canceled'" class="flex justify-end">
                                                <button
                                                    @click="confirmDelete(subscription)"
                                                    class="inline-flex items-center px-3 py-1 text-red-600 hover:text-red-900 hover:bg-red-50 rounded-md transition-colors"
                                                    title="حذف الاشتراك"
                                                >
                                                    <i class="fas fa-trash-alt ml-1"></i>
                                                    <span>حذف</span>
                                                </button>
                                            </div>

                                            <div v-else-if="subscription.status === 'active'" class="flex justify-end">
                                                <span class="inline-flex items-center px-3 py-1 text-gray-400 bg-gray-50 rounded-md" title="لا يمكن حذف الاشتراكات النشطة">
                                                    <i class="fas fa-lock ml-1"></i>
                                                </span>
                                            </div>

                                            <div v-else class="flex justify-end">
                                                <span class="inline-flex items-center px-3 py-1 text-blue-600 bg-blue-50 rounded-md" title="حالة غير معروفة">
                                                    <i class="fas fa-question ml-1"></i>
                                                    <span>{{ subscription.status }}</span>
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="clientSubscriptions.length === 0">
                                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                            لا توجد اشتراكات
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mt-4">تأكيد الحذف</h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-500">
                            هل أنت متأكد من حذف اشتراك العميل: <strong>{{ subscriptionToDelete?.client_email }}</strong>؟
                        </p>
                        <p class="text-xs text-red-600 mt-2">
                            هذا الإجراء لا يمكن التراجع عنه
                        </p>
                    </div>
                    <div class="items-center px-4 py-3">
                        <button
                            @click="deleteSubscription"
                            :disabled="deleting"
                            class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md w-24 mr-2 hover:bg-red-700 disabled:opacity-50"
                        >
                            <span v-if="deleting">
                                <i class="fas fa-spinner fa-spin"></i>
                            </span>
                            <span v-else>حذف</span>
                        </button>
                        <button
                            @click="cancelDelete"
                            :disabled="deleting"
                            class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md w-24 hover:bg-gray-400 disabled:opacity-50"
                        >
                            إلغاء
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayoutSidebar.vue';

const props = defineProps({
    clientSubscriptions: {
        type: Array,
        default: () => []
    },
    stats: {
        type: Object,
        default: () => ({})
    }
});

// State for delete modal
const showDeleteModal = ref(false);
const subscriptionToDelete = ref(null);
const deleting = ref(false);

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

// Delete functions
const canDelete = (status) => {
    // Allow deletion for canceled, expired, and pending statuses
    return ['canceled', 'expired', 'pending'].includes(status);
};

const confirmDelete = (subscription) => {
    subscriptionToDelete.value = subscription;
    showDeleteModal.value = true;
};

const cancelDelete = () => {
    showDeleteModal.value = false;
    subscriptionToDelete.value = null;
    deleting.value = false;
};

const deleteSubscription = () => {
    if (!subscriptionToDelete.value) return;

    deleting.value = true;

    router.delete(route('admin.client-subscriptions.destroy', subscriptionToDelete.value.id), {
        onSuccess: () => {
            showDeleteModal.value = false;
            subscriptionToDelete.value = null;
            deleting.value = false;
        },
        onError: () => {
            deleting.value = false;
        }
    });
};
</script>
