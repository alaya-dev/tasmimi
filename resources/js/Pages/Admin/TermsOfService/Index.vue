<template>
    <Head title="إدارة اتفاقية الاستخدام" />

    <AdminLayoutSidebar>
        <div class="p-6">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">إدارة اتفاقية الاستخدام</h1>
                    <p class="text-gray-600 mt-1">إنشاء وتعديل اتفاقية الاستخدام للموقع</p>
                </div>
                
                <div class="flex space-x-3 space-x-reverse">
                    <!-- View Public Terms Button -->
                    <Link
                        v-if="activeTerms"
                        :href="route('terms-of-service.show')"
                        target="_blank"
                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                        عرض الاتفاقية
                    </Link>

                    <!-- Create Button -->
                    <Link
                        v-if="!hasTerms"
                        :href="route('admin.terms-of-service.create')"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        إنشاء اتفاقية جديدة
                    </Link>
                </div>
            </div>

            <!-- No Terms Message -->
            <div v-if="!hasTerms" class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
                <svg class="mx-auto h-12 w-12 text-yellow-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.232 19.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                <h3 class="text-lg font-medium text-yellow-800 mb-2">لا توجد اتفاقية استخدام</h3>
                <p class="text-yellow-700 mb-4">لم يتم إنشاء أي اتفاقية استخدام بعد. قم بإنشاء اتفاقية جديدة لعرضها للمستخدمين.</p>
                <Link
                    :href="route('admin.terms-of-service.create')"
                    class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    إنشاء اتفاقية الاستخدام
                </Link>
            </div>

            <!-- Terms List -->
            <div v-if="hasTerms" class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">قائمة اتفاقيات الاستخدام</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    العنوان
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    الإصدار
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    الحالة
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    تاريخ السريان
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    تم الإنشاء
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    الإجراءات
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="term in terms" :key="term.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ term.title }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="text-sm text-gray-900">{{ term.version }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <span :class="term.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'" class="px-2 py-1 text-xs font-semibold rounded-full">
                                        {{ term.is_active ? 'نشطة' : 'غير نشطة' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">
                                    {{ formatDate(term.effective_date) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">
                                    {{ formatDate(term.created_at) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2 space-x-reverse">
                                    <!-- Edit Button -->
                                    <Link
                                        :href="route('admin.terms-of-service.edit', term.id)"
                                        class="text-blue-600 hover:text-blue-900"
                                    >
                                        تعديل
                                    </Link>

                                    <!-- Activate Button -->
                                    <button
                                        v-if="!term.is_active"
                                        @click="activateTerms(term)"
                                        class="text-green-600 hover:text-green-900"
                                    >
                                        تفعيل
                                    </button>

                                    <!-- Delete Button -->
                                    <button
                                        @click="deleteTerms(term)"
                                        class="text-red-600 hover:text-red-900"
                                    >
                                        حذف
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <Modal :show="showDeleteModal" @close="showDeleteModal = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4 text-right">
                    تأكيد الحذف
                </h2>
                <p class="text-sm text-gray-600 mb-6 text-right">
                    هل أنت متأكد من حذف اتفاقية الاستخدام "{{ termsToDelete?.title }}"؟ هذا الإجراء لا يمكن التراجع عنه.
                </p>
                <div class="flex justify-end space-x-3 space-x-reverse">
                    <button
                        @click="showDeleteModal = false"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors"
                    >
                        إلغاء
                    </button>
                    <button
                        @click="confirmDelete"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
                    >
                        حذف
                    </button>
                </div>
            </div>
        </Modal>
    </AdminLayoutSidebar>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayoutSidebar from '@/Layouts/AdminLayoutSidebar.vue'
import Modal from '@/Components/Modal.vue'

const props = defineProps({
    terms: Array,
    activeTerms: Object,
    hasTerms: Boolean,
})

// Reactive data
const showDeleteModal = ref(false)
const termsToDelete = ref(null)

// Methods
const formatDate = (date) => {
    if (!date) return 'غير محدد'
    return new Date(date).toLocaleDateString('ar-SA', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
}

const activateTerms = (terms) => {
    router.post(route('admin.terms-of-service.activate', terms.id), {}, {
        onSuccess: () => {
            // Success message will be shown by the backend
        },
        onError: (errors) => {
            console.error('Failed to activate terms:', errors)
        }
    })
}

const deleteTerms = (terms) => {
    termsToDelete.value = terms
    showDeleteModal.value = true
}

const confirmDelete = () => {
    if (termsToDelete.value) {
        router.delete(route('admin.terms-of-service.destroy', termsToDelete.value.id), {
            onSuccess: () => {
                showDeleteModal.value = false
                termsToDelete.value = null
            },
            onError: (errors) => {
                console.error('Failed to delete terms:', errors)
            }
        })
    }
}
</script>
