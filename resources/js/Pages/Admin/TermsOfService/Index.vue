<template>
    <Head title="إدارة اتفاقية الاستخدام" />

    <AdminLayoutSidebar>
        <div class="p-6" dir="rtl">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">إدارة اتفاقية الاستخدام</h1>
                    <p class="text-gray-600 mt-1">إنشاء وتعديل اتفاقية الاستخدام للموقع</p>
                </div>
                
                <div class="flex space-x-3 space-x-reverse">
                    <!-- Create Button - Only show if no terms exist -->
                    <Link
                        v-if="!hasTerms"
                        :href="route('admin.terms-of-service.create')"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        إضافة ملف جديد
                    </Link>
                    
                    <!-- Message when file exists -->
                    <div v-if="hasTerms" class="text-sm text-gray-600 bg-yellow-50 px-3 py-2 rounded-md border border-yellow-200">
                        <svg class="w-4 h-4 inline ml-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                        ملف واحد فقط مسموح. استخدم "تعديل" لاستبدال الملف.
                    </div>
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
                    إضافة ملف اتفاقية الاستخدام
                </Link>
            </div>

            <!-- Terms List -->
            <div v-if="hasTerms" class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">قائمة ملفات اتفاقية الاستخدام</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    اسم الملف
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    تاريخ الإنشاء
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    الإجراءات
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="term in terms" :key="term.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end">
                                        <div class="text-right">
                                            <div class="text-sm font-medium text-gray-900">{{ term.file_name || term.title }}</div>
                                            <div v-if="term.file_size" class="text-xs text-gray-500">{{ formatFileSize(term.file_size) }}</div>
                                        </div>
                                        <svg class="h-5 w-5 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">
                                    {{ formatDate(term.created_at) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2 space-x-reverse">
                                        <!-- Edit Button -->
                                        <Link
                                            :href="route('admin.terms-of-service.edit', term.id)"
                                            class="inline-flex items-center px-3 py-1 bg-blue-600 border border-transparent rounded text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                        >
                                            <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            تعديل
                                        </Link>
                                        
                                        <!-- Delete Button -->
                                        <button
                                            @click="confirmDelete(term)"
                                            class="inline-flex items-center px-3 py-1 bg-red-600 border border-transparent rounded text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                        >
                                            <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            حذف
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <Modal v-model:show="showDeleteModal" @close="showDeleteModal = false">
            <div class="p-6" dir="rtl">
                <h2 class="text-lg font-medium text-gray-900 mb-4">تأكيد الحذف</h2>
                <p class="text-sm text-gray-600 mb-6">
                    هل أنت متأكد من أنك تريد حذف ملف "{{ termToDelete?.file_name || termToDelete?.title }}"؟
                    هذا الإجراء لا يمكن التراجع عنه.
                </p>
                <div class="flex justify-end space-x-3 space-x-reverse">
                    <button
                        @click="showDeleteModal = false"
                        class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded transition duration-200"
                    >
                        إلغاء
                    </button>
                    <button
                        @click="deleteTerm"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded transition duration-200"
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

// Props
defineProps({
    terms: Array,
    activeTerms: Object,
    hasTerms: Boolean,
})

// Reactive data
const showDeleteModal = ref(false)
const termToDelete = ref(null)

// Methods
const formatDate = (dateString) => {
    const date = new Date(dateString)
    return date.toLocaleDateString('fr-FR', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
    })
}

const formatFileSize = (bytes) => {
    if (!bytes) return ''
    if (bytes === 0) return '0 بايت'
    
    const k = 1024
    const sizes = ['بايت', 'كيلوبايت', 'ميجابايت', 'جيجابايت']
    const i = Math.floor(Math.log(bytes) / Math.log(k))
    
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

const confirmDelete = (term) => {
    termToDelete.value = term
    showDeleteModal.value = true
}

const deleteTerm = () => {
    if (termToDelete.value) {
        router.delete(route('admin.terms-of-service.destroy', termToDelete.value.id), {
            onSuccess: () => {
                showDeleteModal.value = false
                termToDelete.value = null
            }
        })
    }
}
</script>
