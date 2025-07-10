<template>
    <Head title="إدارة الاشتراكات - Bitaqati" />

    <AdminLayoutSidebar>
        <template #breadcrumb>
            <span class="text-gray-500">إدارة الاشتراكات</span>
        </template>

        <template #header>
            <div class="flex justify-between items-center flex-row-reverse">
                <div class="text-right">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight text-right">إدارة الاشتراكات</h2>
                    <p class="mt-1 text-sm text-gray-600 text-right">إدارة أسعار الاشتراكات السنوية والشهرية والأسبوعية</p>
                </div>
                <button
                    @click="openAddModal"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200 flex items-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    إضافة اشتراك جديد
                </button>
            </div>
        </template>

        <div class="py-12" dir="rtl">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 px-6 py-4 text-center">
                        <h3 class="text-lg font-semibold text-white">قائمة الاشتراكات</h3>
                        <p class="text-blue-100 text-sm">إدارة جميع أنواع الاشتراكات وأسعارها</p>
                    </div>
                    
                    <div class="p-6">
                        <!-- Subscriptions Table -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            الاسم
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            النوع
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            السعر
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
                                    <tr v-for="subscription in subscriptions" :key="subscription.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right">
                                            {{ subscription.name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                                  :class="getTypeClass(subscription.type)">
                                                {{ types[subscription.type] }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">
                                            {{ subscription.price }} ريال
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                                  :class="subscription.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                                {{ subscription.is_active ? 'نشط' : 'غير نشط' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right">
                                            <div class="flex items-center gap-2 justify-end">
                                                <button
                                                    @click="openEditModal(subscription)"
                                                    class="text-indigo-600 hover:text-indigo-900 transition duration-200"
                                                >
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </button>
                                                <button
                                                    @click="confirmDelete(subscription)"
                                                    class="text-red-600 hover:text-red-900 transition duration-200"
                                                >
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="subscriptions.length === 0">
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                            لا توجد اشتراكات مضافة بعد
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add/Edit Modal -->
        <Modal :show="showModal" @close="closeModal">
            <div class="p-6" dir="rtl">
                <h2 class="text-lg font-medium text-gray-900 text-right mb-4">
                    {{ isEditing ? 'تعديل الاشتراك' : 'إضافة اشتراك جديد' }}
                </h2>

                <form @submit.prevent="submitForm" class="space-y-4">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 text-right mb-2">
                            اسم الاشتراك
                        </label>
                        <input
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            style="text-align: right; direction: rtl;"
                            required
                        />
                        <div v-if="form.errors.name" class="mt-2 text-sm text-red-600 text-right">
                            {{ form.errors.name }}
                        </div>
                    </div>

                    <!-- Type -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 text-right mb-2">
                            نوع الاشتراك
                        </label>
                        <select
                            id="type"
                            v-model="form.type"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            style="text-align: right; direction: rtl;"
                            required
                        >
                            <option value="">اختر نوع الاشتراك</option>
                            <option v-for="(label, value) in types" :key="value" :value="value">
                                {{ label }}
                            </option>
                        </select>
                        <div v-if="form.errors.type" class="mt-2 text-sm text-red-600 text-right">
                            {{ form.errors.type }}
                        </div>
                    </div>

                    <!-- Price -->
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 text-right mb-2">
                            السعر (ريال)
                        </label>
                        <input
                            id="price"
                            v-model="form.price"
                            type="number"
                            step="0.01"
                            min="0"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            style="text-align: right; direction: rtl;"
                            required
                        />
                        <div v-if="form.errors.price" class="mt-2 text-sm text-red-600 text-right">
                            {{ form.errors.price }}
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 text-right mb-2">
                            الوصف (اختياري)
                        </label>
                        <textarea
                            id="description"
                            v-model="form.description"
                            rows="3"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            style="text-align: right; direction: rtl;"
                        ></textarea>
                        <div v-if="form.errors.description" class="mt-2 text-sm text-red-600 text-right">
                            {{ form.errors.description }}
                        </div>
                    </div>

                    <!-- Is Active -->
                    <div class="flex items-center justify-end">
                        <label for="is_active" class="flex items-center">
                            <input
                                id="is_active"
                                v-model="form.is_active"
                                type="checkbox"
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                            <span class="mr-2 text-sm text-gray-700">نشط</span>
                        </label>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end space-x-3 space-x-reverse pt-4">
                        <button
                            type="button"
                            @click="closeModal"
                            class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            إلغاء
                        </button>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50"
                        >
                            <div v-if="form.processing" class="animate-spin h-4 w-4 mr-2 border-2 border-white border-t-transparent rounded-full"></div>
                            {{ isEditing ? 'تحديث' : 'إضافة' }}
                        </button>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Delete Confirmation Modal -->
        <Modal :show="showDeleteModal" @close="closeDeleteModal">
            <div class="p-6" dir="rtl">
                <h2 class="text-lg font-medium text-gray-900 text-right">
                    تأكيد حذف الاشتراك
                </h2>

                <p class="mt-1 text-sm text-gray-600 text-right">
                    هل أنت متأكد من حذف اشتراك "{{ subscriptionToDelete?.name }}"؟ هذا الإجراء لا يمكن التراجع عنه.
                </p>

                <div class="mt-6 flex justify-end space-x-3 space-x-reverse">
                    <button
                        @click="closeDeleteModal"
                        type="button"
                        class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        إلغاء
                    </button>

                    <button
                        @click="deleteSubscription"
                        type="button"
                        :disabled="deleteForm.processing"
                        class="inline-flex items-center justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50"
                    >
                        <div v-if="deleteForm.processing" class="animate-spin h-4 w-4 mr-2 border-2 border-white border-t-transparent rounded-full"></div>
                        حذف
                    </button>
                </div>
            </div>
        </Modal>
    </AdminLayoutSidebar>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayoutSidebar from '@/Layouts/AdminLayoutSidebar.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    subscriptions: {
        type: Array,
        default: () => []
    },
    types: {
        type: Object,
        default: () => ({})
    }
});

const showModal = ref(false);
const showDeleteModal = ref(false);
const isEditing = ref(false);
const subscriptionToDelete = ref(null);

const form = useForm({
    name: '',
    type: '',
    price: '',
    description: '',
    is_active: true,
});

const deleteForm = useForm({});

const getTypeClass = (type) => {
    const classes = {
        'annual': 'bg-purple-100 text-purple-800',
        'monthly': 'bg-blue-100 text-blue-800',
        'weekly': 'bg-green-100 text-green-800',
    };
    return classes[type] || 'bg-gray-100 text-gray-800';
};

const openAddModal = () => {
    isEditing.value = false;
    form.reset();
    form.clearErrors();
    showModal.value = true;
};

const openEditModal = (subscription) => {
    isEditing.value = true;
    form.reset();
    form.clearErrors();
    
    form.name = subscription.name;
    form.type = subscription.type;
    form.price = subscription.price;
    form.description = subscription.description || '';
    form.is_active = subscription.is_active;
    
    form.subscription_id = subscription.id;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    form.reset();
    form.clearErrors();
};

const submitForm = () => {
    if (isEditing.value) {
        form.put(route('admin.subscriptions.update', form.subscription_id), {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
            },
        });
    } else {
        form.post(route('admin.subscriptions.store'), {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
            },
        });
    }
};

const confirmDelete = (subscription) => {
    subscriptionToDelete.value = subscription;
    showDeleteModal.value = true;
};

const closeDeleteModal = () => {
    showDeleteModal.value = false;
    subscriptionToDelete.value = null;
};

const deleteSubscription = () => {
    deleteForm.delete(route('admin.subscriptions.destroy', subscriptionToDelete.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            closeDeleteModal();
        },
    });
};
</script>
