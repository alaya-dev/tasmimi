<template>
    <Head title="بطاقات الحرفاء" />

    <AdminLayoutSidebar>
        <template #breadcrumb>
            <span class="text-gray-500">بطاقات الحرفاء</span>
        </template>

        <template #header>
            <div class="flex items-center justify-between flex-row-reverse">
                <div class="flex items-center flex-row-reverse">
                    <Link
                        :href="route('admin.dashboard')"
                        class="ml-4 text-gray-600 hover:text-gray-900 transition-colors duration-200"
                    >
                        <svg class="w-5 h-5 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </Link>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">بطاقات الحرفاء</h2>
                </div>
            </div>
        </template>

        <div class="py-12" dir="rtl">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- المرشحات -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <form @submit.prevent="search" class="flex flex-wrap gap-4 flex-row-reverse">
                            <div class="flex-1 min-w-64">
                                <input
                                    v-model="form.search"
                                    type="text"
                                    placeholder="بحث بالاسم أو البريد الإلكتروني..."
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-right"
                                />
                            </div>
                            <div class="min-w-48">
                                <select
                                    v-model="form.user_id"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-right"
                                >
                                    <option value="">جميع العملاء</option>
                                    <option v-for="client in clients" :key="client.id" :value="client.id">
                                        {{ client.email }}
                                    </option>
                                </select>
                            </div>
                            <button
                                type="submit"
                                class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
                            >
                                بحث
                            </button>
                            <button
                                type="button"
                                @click="clearFilters"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded"
                            >
                                مسح
                            </button>
                        </form>
                    </div>
                </div>

                <!-- قائمة التصاميم -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 px-6 py-4 text-right">
                        <h3 class="text-lg font-semibold text-white">بطاقات الحرفاء</h3>
                        <p class="text-blue-100 text-sm">عرض وإدارة تصاميم العملاء</p>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200" dir="rtl">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-right">
                                        <div class="flex items-center flex-row-reverse">
                                            <svg class="w-4 h-4 ml-2" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                                            </svg>
                                            اسم التصميم
                                        </div>
                                    </th>
                                    <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-right">
                                        <div class="flex items-center flex-row-reverse">
                                            <svg class="w-4 h-4 ml-2" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                                            </svg>
                                            العميل
                                        </div>
                                    </th>
                                    <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-right">
                                        <div class="flex items-center flex-row-reverse">
                                            <svg class="w-4 h-4 ml-2" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                                            </svg>
                                            القالب الأساسي
                                        </div>
                                    </th>
                                    <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-right">
                                        <div class="flex items-center flex-row-reverse">
                                            <svg class="w-4 h-4 ml-2" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-7h-1V2h-2v2H8V2H6v2H5c-1.1 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z"/>
                                            </svg>
                                            تاريخ الإنشاء
                                        </div>
                                    </th>
                                    <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-right">
                                        الإجراءات
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-if="clientTemplates.data.length === 0">
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                        لا توجد تصاميم
                                    </td>
                                </tr>
                                <tr v-for="template in clientTemplates.data" :key="template.id" class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="flex items-center flex-row-reverse">
                                            <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center ml-3">
                                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900 text-right">{{ template.name }}</div>
                                                <div class="text-sm text-gray-500 text-right">الإصدار {{ template.version }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">
                                        {{ template.user.email }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">
                                        <div>
                                            <div class="font-medium">{{ template.template.name }}</div>
                                            <div v-if="template.template.category" class="text-xs text-gray-400">{{ template.template.category.name }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">
                                        {{ formatDate(template.created_at) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right">
                                        <div class="flex justify-end space-x-3 space-x-reverse">
                                            <Link
                                                :href="route('admin.client-templates.show', template.id)"
                                                class="inline-flex items-center px-3 py-1 bg-indigo-100 text-indigo-700 rounded-md hover:bg-indigo-200 transition-colors duration-200"
                                            >
                                                <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                                                </svg>
                                                محرر التصميم
                                            </Link>
                                            <button
                                                @click="deleteTemplate(template)"
                                                class="inline-flex items-center px-3 py-1 bg-red-100 text-red-700 rounded-md hover:bg-red-200 transition-colors duration-200"
                                            >
                                                <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                                                </svg>
                                                حذف
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- ترقيم الصفحات -->
                    <div v-if="clientTemplates.data.length > 0" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        <div class="flex items-center justify-between flex-row-reverse">
                            <div class="flex-1 flex justify-between sm:hidden flex-row-reverse">
                                <Link
                                    v-if="clientTemplates.next_page_url"
                                    :href="clientTemplates.next_page_url"
                                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                >
                                    التالي
                                </Link>
                                <Link
                                    v-if="clientTemplates.prev_page_url"
                                    :href="clientTemplates.prev_page_url"
                                    class="mr-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                >
                                    السابق
                                </Link>
                            </div>
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between flex-row-reverse">
                                <div>
                                    <p class="text-sm text-gray-700 text-right">
                                        عرض من
                                        <span class="font-medium">{{ clientTemplates.from }}</span>
                                        إلى
                                        <span class="font-medium">{{ clientTemplates.to }}</span>
                                        من أصل
                                        <span class="font-medium">{{ clientTemplates.total }}</span>
                                        نتيجة
                                    </p>
                                </div>
                                <div>
                                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                        <Link
                                            v-if="clientTemplates.prev_page_url"
                                            :href="clientTemplates.prev_page_url"
                                            class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                                        >
                                            السابق
                                        </Link>
                                        <Link
                                            v-if="clientTemplates.next_page_url"
                                            :href="clientTemplates.next_page_url"
                                            class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                                        >
                                            التالي
                                        </Link>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayoutSidebar>
</template>

<script setup>
import { reactive } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayoutSidebar from '@/Layouts/AdminLayoutSidebar.vue';

const props = defineProps({
    clientTemplates: Object,
    filters: Object,
    clients: Array,
});

const form = reactive({
    search: props.filters.search || '',
    user_id: props.filters.user_id || '',
});

const search = () => {
    router.get(route('admin.client-templates.index'), form, {
        preserveState: true,
        replace: true,
    });
};

const clearFilters = () => {
    form.search = '';
    form.user_id = '';
    router.get(route('admin.client-templates.index'), {}, {
        preserveState: true,
        replace: true,
    });
};

const deleteTemplate = (template) => {
    if (confirm('هل أنت متأكد من حذف تصميم "' + template.name + '"؟')) {
        router.delete(route('admin.client-templates.destroy', template.id));
    }
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('fr-FR', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
    });
};
</script>