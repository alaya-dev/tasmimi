<template>
    <Head title="إنشاء اشتراك جديد" />

    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    إنشاء اشتراك جديد للعميل
                </h2>
                <Link
                    :href="route('admin.client-subscriptions.index')"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
                >
                    العودة للقائمة
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Client Selection -->
                            <div>
                                <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2" style="text-align: right; direction: rtl;">
                                    العميل *
                                </label>
                                <select
                                    id="user_id"
                                    v-model="form.user_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    style="text-align: right; direction: rtl;"
                                    required
                                >
                                    <option value="">اختر العميل</option>
                                    <option v-for="client in clients" :key="client.id" :value="client.id">
                                        {{ client.email }} {{ client.phone ? `(${client.phone})` : '' }}
                                    </option>
                                </select>
                                <div v-if="form.errors.user_id" class="text-red-600 text-sm mt-1" style="text-align: right; direction: rtl;">
                                    {{ form.errors.user_id }}
                                </div>
                            </div>

                            <!-- Subscription Plan Selection -->
                            <div>
                                <label for="subscription_id" class="block text-sm font-medium text-gray-700 mb-2" style="text-align: right; direction: rtl;">
                                    خطة الاشتراك *
                                </label>
                                <select
                                    id="subscription_id"
                                    v-model="form.subscription_id"
                                    @change="onSubscriptionChange"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    style="text-align: right; direction: rtl;"
                                    required
                                >
                                    <option value="">اختر خطة الاشتراك</option>
                                    <option v-for="subscription in subscriptions" :key="subscription.id" :value="subscription.id">
                                        {{ subscription.name }} - {{ subscription.price }} ريال
                                        {{ subscription.duration_days ? `(${subscription.duration_days} يوم)` : `(${getTypeText(subscription.type)})` }}
                                    </option>
                                </select>
                                <div v-if="form.errors.subscription_id" class="text-red-600 text-sm mt-1" style="text-align: right; direction: rtl;">
                                    {{ form.errors.subscription_id }}
                                </div>
                            </div>

                            <!-- Status -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2" style="text-align: right; direction: rtl;">
                                    حالة الاشتراك *
                                </label>
                                <select
                                    id="status"
                                    v-model="form.status"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    style="text-align: right; direction: rtl;"
                                    required
                                >
                                    <option value="active">نشط</option>
                                    <option value="pending">في الانتظار</option>
                                    <option value="canceled">ملغي</option>
                                    <option value="expired">منتهي الصلاحية</option>
                                </select>
                                <div v-if="form.errors.status" class="text-red-600 text-sm mt-1" style="text-align: right; direction: rtl;">
                                    {{ form.errors.status }}
                                </div>
                            </div>

                            <!-- Start Date -->
                            <div>
                                <label for="starts_at" class="block text-sm font-medium text-gray-700 mb-2" style="text-align: right; direction: rtl;">
                                    تاريخ البداية *
                                </label>
                                <input
                                    id="starts_at"
                                    v-model="form.starts_at"
                                    type="datetime-local"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    style="text-align: right; direction: rtl;"
                                    required
                                    @change="updateEndDate"
                                />
                                <div v-if="form.errors.starts_at" class="text-red-600 text-sm mt-1" style="text-align: right; direction: rtl;">
                                    {{ form.errors.starts_at }}
                                </div>
                            </div>

                            <!-- End Date -->
                            <div>
                                <label for="ends_at" class="block text-sm font-medium text-gray-700 mb-2" style="text-align: right; direction: rtl;">
                                    تاريخ الانتهاء *
                                </label>
                                <input
                                    id="ends_at"
                                    v-model="form.ends_at"
                                    type="datetime-local"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    style="text-align: right; direction: rtl;"
                                    required
                                />
                                <div v-if="form.errors.ends_at" class="text-red-600 text-sm mt-1" style="text-align: right; direction: rtl;">
                                    {{ form.errors.ends_at }}
                                </div>
                            </div>

                            <!-- Auto Renew -->
                            <div>
                                <label for="auto_renew" class="block text-sm font-medium text-gray-700 mb-2" style="text-align: right; direction: rtl;">
                                    تجديد تلقائي
                                </label>
                                <div class="flex items-center justify-end">
                                    <input
                                        id="auto_renew"
                                        v-model="form.auto_renew"
                                        type="checkbox"
                                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                    />
                                </div>
                            </div>

                            <!-- Selected Subscription Info -->
                            <div v-if="selectedSubscription" class="bg-blue-50 p-4 rounded-lg">
                                <h3 class="text-lg font-medium text-blue-900 mb-2 text-right">معلومات الخطة المحددة</h3>
                                <div class="text-sm text-blue-800 space-y-1 text-right">
                                    <p><strong>الاسم:</strong> {{ selectedSubscription.name }}</p>
                                    <p><strong>السعر:</strong> {{ selectedSubscription.price }} ريال</p>
                                    <p><strong>المدة:</strong>
                                        {{ selectedSubscription.duration_days ?
                                            `${selectedSubscription.duration_days} يوم` :
                                            getTypeText(selectedSubscription.type)
                                        }}
                                    </p>
                                    <p v-if="selectedSubscription.description"><strong>الوصف:</strong> {{ selectedSubscription.description }}</p>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex justify-end">
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded disabled:opacity-50"
                                >
                                    <span v-if="form.processing">جاري الإنشاء...</span>
                                    <span v-else>إنشاء الاشتراك</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayoutSidebar.vue';

const props = defineProps({
    clients: {
        type: Array,
        default: () => []
    },
    subscriptions: {
        type: Array,
        default: () => []
    }
});

const form = useForm({
    user_id: '',
    subscription_id: '',
    status: 'active',
    starts_at: new Date().toISOString().slice(0, 16), // Current datetime in local format
    ends_at: '',
    auto_renew: false
});

const selectedSubscription = computed(() => {
    if (!form.subscription_id) return null;
    return props.subscriptions.find(sub => sub.id == form.subscription_id);
});

const getTypeText = (type) => {
    const types = {
        'weekly': 'أسبوعي',
        'monthly': 'شهري',
        'yearly': 'سنوي',
        'annual': 'سنوي'
    };
    return types[type] || type;
};

const onSubscriptionChange = () => {
    updateEndDate();
};

const updateEndDate = () => {
    if (!form.starts_at || !selectedSubscription.value) return;

    const startDate = new Date(form.starts_at);
    let endDate = new Date(startDate);

    if (selectedSubscription.value.duration_days) {
        // Use custom duration
        endDate.setDate(startDate.getDate() + selectedSubscription.value.duration_days);
    } else {
        // Use type-based duration
        switch (selectedSubscription.value.type) {
            case 'weekly':
                endDate.setDate(startDate.getDate() + 7);
                break;
            case 'monthly':
                endDate.setMonth(startDate.getMonth() + 1);
                break;
            case 'yearly':
            case 'annual':
                endDate.setFullYear(startDate.getFullYear() + 1);
                break;
        }
    }

    form.ends_at = endDate.toISOString().slice(0, 16);
};

const submit = () => {
    form.post(route('admin.client-subscriptions.store'));
};
</script>
