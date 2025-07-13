<template>
    <Head title="إدارة القوالب" />

    <AdminLayoutSidebar>
        <template #breadcrumb>
            <span class="text-gray-500">إدارة القوالب</span>
        </template>

        <template #header>
            <div class="flex items-center justify-between flex-row-reverse">
                <div class="flex items-center flex-row-reverse">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">إدارة القوالب</h2>
                </div>
                <button
                    @click="showCreateTemplateModal = true"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded"
                >
                    إضافة قالب جديد
                </button>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- المرشحات -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <form @submit.prevent="search" class="flex flex-wrap gap-4 flex-row-reverse">
                            <div class="flex-1 min-w-64">
                                <input
                                    v-model="form.search"
                                    type="text"
                                    placeholder="بحث بالاسم..."
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-right"
                                />
                            </div>
                            <div class="min-w-48">
                                <select
                                    v-model="form.category"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-right"
                                >
                                    <option value="">جميع الفئات</option>
                                    <option v-for="category in categories" :key="category.id" :value="category.id">
                                        {{ category.name }}
                                    </option>
                                </select>
                            </div>
                            <div class="min-w-48">
                                <select
                                    v-model="form.status"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-right"
                                >
                                    <option value="">جميع الحالات</option>
                                    <option value="active">مفعل</option>
                                    <option value="inactive">غير مفعل</option>
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

                <!-- جدول القوالب -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 px-6 py-4 text-center">
                        <h3 class="text-lg font-semibold text-white">القوالب</h3>
                        <p class="text-blue-100 text-sm">إدارة وتنظيم جميع قوالب البطاقات</p>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-right">
                                        <div class="flex items-center flex-row-reverse">
                                            <svg class="w-4 h-4 ml-2" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                                            </svg>
                                            اسم القالب
                                        </div>
                                    </th>
                                    <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">
                                        <div class="flex items-center justify-center">
                                            <svg class="w-4 h-4 ml-2" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                            </svg>
                                            الفئة
                                        </div>
                                    </th>
                                    <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">
                                        <div class="flex items-center justify-center">
                                            <svg class="w-4 h-4 ml-2" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                                            </svg>
                                            الصورة المصغرة
                                        </div>
                                    </th>
                                    <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">
                                        <div class="flex items-center justify-center">
                                            <svg class="w-4 h-4 ml-2" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                            </svg>
                                            الحالة
                                        </div>
                                    </th>
                                    <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">
                                        الإجراءات
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="template in templates.data" :key="template.id" class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                        <div class="flex items-center flex-row-reverse">
                                            <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center ml-3">
                                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="font-medium">{{ template.name }}</div>
                                                <div class="text-xs text-gray-500">بواسطة: {{ template.creator.email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                            {{ template.category.name }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex justify-center">
                                            <img 
                                                v-if="template.thumbnail_url" 
                                                :src="template.thumbnail_url" 
                                                :alt="template.name"
                                                class="w-16 h-16 object-cover rounded-lg border-2 border-gray-200"
                                            />
                                            <div v-else class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                                <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                                                </svg>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <button
                                            @click="toggleStatus(template)"
                                            :class="[
                                                'inline-flex px-3 py-1 text-xs font-semibold rounded-full transition-colors duration-200',
                                                template.is_active 
                                                    ? 'bg-green-100 text-green-800 hover:bg-green-200' 
                                                    : 'bg-red-100 text-red-800 hover:bg-red-200'
                                            ]"
                                        >
                                            {{ template.is_active ? 'مفعل' : 'غير مفعل' }}
                                        </button>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                        <div class="flex justify-center space-x-2 space-x-reverse">
                                            <button
                                                @click="showTemplateDetails(template)"
                                                class="inline-flex items-center px-3 py-1 bg-green-100 text-green-700 rounded-md hover:bg-green-200 transition-colors duration-200"
                                            >
                                                <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                                </svg>
                                                تفاصيل
                                            </button>
                                            <button
                                                @click="editTemplate(template)"
                                                class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 transition-colors duration-200"
                                            >
                                                <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                                                </svg>
                                                تعديل
                                            </button>
                                            <Link
                                                :href="route('admin.templates.design', template.id)"
                                                class="inline-flex items-center px-3 py-1 bg-purple-100 text-purple-700 rounded-md hover:bg-purple-200 transition-colors duration-200"
                                            >
                                                <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z"/>
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

                    <!-- Pagination -->
                    <div v-if="templates.links" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 flex justify-between sm:hidden">
                                <Link
                                    v-if="templates.prev_page_url"
                                    :href="templates.prev_page_url"
                                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                >
                                    السابق
                                </Link>
                                <Link
                                    v-if="templates.next_page_url"
                                    :href="templates.next_page_url"
                                    class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                >
                                    التالي
                                </Link>
                            </div>
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-700">
                                        عرض
                                        <span class="font-medium">{{ templates.from }}</span>
                                        إلى
                                        <span class="font-medium">{{ templates.to }}</span>
                                        من
                                        <span class="font-medium">{{ templates.total }}</span>
                                        نتيجة
                                    </p>
                                </div>
                                <div>
                                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                        <Link
                                            v-for="link in templates.links"
                                            :key="link.label"
                                            :href="link.url"
                                            v-html="link.label"
                                            :class="[
                                                'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                                                link.active
                                                    ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                                                    : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                                                link.url ? 'cursor-pointer' : 'cursor-default opacity-50'
                                            ]"
                                        />
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal pour voir les détails d'un template -->
        <div v-if="showDetailsModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeTemplateModal"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 text-right mb-4" id="modal-title">
                                    تفاصيل القالب
                                </h3>

                                <div v-if="selectedTemplate" class="space-y-6">
                                    <!-- Template Info -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div class="text-right">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">اسم القالب</label>
                                            <p class="text-lg font-semibold text-gray-900">{{ selectedTemplate.name }}</p>
                                        </div>
                                        <div class="text-right">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">الفئة</label>
                                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-purple-100 text-purple-800">
                                                {{ selectedTemplate.category.name }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Status and Creator -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div class="text-right">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                                            <span :class="[
                                                'inline-flex px-3 py-1 text-sm font-semibold rounded-full',
                                                selectedTemplate.is_active
                                                    ? 'bg-green-100 text-green-800'
                                                    : 'bg-red-100 text-red-800'
                                            ]">
                                                {{ selectedTemplate.is_active ? 'مفعل' : 'غير مفعل' }}
                                            </span>
                                        </div>
                                        <div class="text-right">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">منشئ القالب</label>
                                            <p class="text-gray-900">{{ selectedTemplate.creator.email }}</p>
                                        </div>
                                    </div>

                                    <!-- Thumbnail -->
                                    <div class="text-right">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">الصورة المصغرة</label>
                                        <div class="flex justify-end">
                                            <img
                                                v-if="selectedTemplate.thumbnail_url"
                                                :src="selectedTemplate.thumbnail_url"
                                                :alt="selectedTemplate.name"
                                                class="w-32 h-32 object-cover rounded-lg border-2 border-gray-200"
                                            />
                                            <div v-else class="w-32 h-32 bg-gray-200 rounded-lg flex items-center justify-center">
                                                <svg class="w-12 h-12 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Dates -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div class="text-right">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ الإنشاء</label>
                                            <p class="text-gray-900">{{ formatDate(selectedTemplate.created_at) }}</p>
                                        </div>
                                        <div class="text-right">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ آخر تحديث</label>
                                            <p class="text-gray-900">{{ formatDate(selectedTemplate.updated_at) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button
                            type="button"
                            @click="closeTemplateModal"
                            class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:w-auto sm:text-sm"
                        >
                            إغلاق
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal pour créer/modifier un template -->
        <div v-if="showCreateTemplateModal || showEditTemplateModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeTemplateModal"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 text-right mb-4" id="modal-title">
                                    {{ showCreateTemplateModal ? 'إضافة قالب جديد' : 'تعديل القالب' }}
                                </h3>

                                <form @submit.prevent="submitTemplateForm" class="space-y-4">
                                    <!-- Name Field -->
                                    <div>
                                        <label for="template_name" class="block text-sm font-medium text-gray-700 text-right mb-2">
                                            اسم القالب *
                                        </label>
                                        <input
                                            id="template_name"
                                            v-model="templateForm.name"
                                            type="text"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-right"
                                            required
                                        />
                                        <div v-if="templateForm.errors.name" class="mt-1 text-sm text-red-600 text-right">
                                            {{ templateForm.errors.name }}
                                        </div>
                                    </div>

                                    <!-- Category Field -->
                                    <div>
                                        <label for="template_category" class="block text-sm font-medium text-gray-700 text-right mb-2">
                                            الفئة *
                                        </label>
                                        <select
                                            id="template_category"
                                            v-model="templateForm.category_id"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-right"
                                            required
                                        >
                                            <option value="">اختر الفئة</option>
                                            <option v-for="category in categories" :key="category.id" :value="category.id">
                                                {{ category.name }}
                                            </option>
                                        </select>
                                        <div v-if="templateForm.errors.category_id" class="mt-1 text-sm text-red-600 text-right">
                                            {{ templateForm.errors.category_id }}
                                        </div>
                                    </div>

                                    <!-- Current Thumbnail (for edit mode) -->
                                    <div v-if="showEditTemplateModal && editingTemplate?.thumbnail">
                                        <label class="block text-sm font-medium text-gray-700 mb-2 text-right">
                                            الصورة المصغرة الحالية
                                        </label>
                                        <div class="flex justify-end mb-2">
                                            <img
                                                :src="editingTemplate.thumbnail_url || `/storage/${editingTemplate.thumbnail}`"
                                                :alt="editingTemplate.name"
                                                class="w-20 h-20 object-cover rounded-lg border-2 border-gray-200"
                                            />
                                        </div>
                                    </div>

                                    <!-- Thumbnail Field -->
                                    <div>
                                        <label for="template_thumbnail" class="block text-sm font-medium text-gray-700 text-right mb-2">
                                            {{ showEditTemplateModal ? 'تغيير الصورة المصغرة' : 'الصورة المصغرة *' }}
                                        </label>
                                        <div class="flex items-center space-x-4 space-x-reverse">
                                            <input
                                                id="template_thumbnail"
                                                type="file"
                                                accept="image/*"
                                                @change="handleThumbnailUpload"
                                                class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                :required="showCreateTemplateModal"
                                            />
                                            <div v-if="thumbnailPreview" class="w-16 h-16">
                                                <img
                                                    :src="thumbnailPreview"
                                                    alt="معاينة الصورة"
                                                    class="w-full h-full object-cover rounded-lg border-2 border-gray-200"
                                                />
                                            </div>
                                        </div>
                                        <p class="mt-1 text-sm text-gray-500 text-right">
                                            JPG, PNG, GIF حتى 2MB
                                        </p>
                                        <div v-if="templateForm.errors.thumbnail" class="mt-1 text-sm text-red-600 text-right">
                                            {{ templateForm.errors.thumbnail }}
                                        </div>
                                    </div>




                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button
                            type="submit"
                            @click="submitTemplateForm"
                            :disabled="templateForm.processing"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
                        >
                            <svg v-if="templateForm.processing" class="animate-spin h-4 w-4 ml-2" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ showCreateTemplateModal ? 'حفظ' : 'تحديث' }}
                        </button>
                        <button
                            type="button"
                            @click="closeTemplateModal"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                        >
                            إلغاء
                        </button>
                    </div>
                </div>
            </div>
        </div>


    </AdminLayoutSidebar>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AdminLayoutSidebar from '@/Layouts/AdminLayoutSidebar.vue';

const props = defineProps({
    templates: Object,
    categories: Array,
    filters: Object,
});

// Form for filters
const form = reactive({
    search: props.filters?.search || '',
    category: props.filters?.category || '',
    status: props.filters?.status || '',
});

// Modal states
const showCreateTemplateModal = ref(false);
const showEditTemplateModal = ref(false);
const showDetailsModal = ref(false);
const editingTemplate = ref(null);
const selectedTemplate = ref(null);

// Template form
const templateForm = useForm({
    name: '',
    category_id: '',
    thumbnail: null,
    _method: null,
});

// Thumbnail preview
const thumbnailPreview = ref(null);

// Methods
const search = () => {
    router.get(route('admin.templates.index'), form, {
        preserveState: true,
        replace: true,
    });
};

const clearFilters = () => {
    form.search = '';
    form.category = '';
    form.status = '';
    search();
};

const toggleStatus = (template) => {
    router.patch(route('admin.templates.toggle-status', template.id));
};

const deleteTemplate = (template) => {
    if (confirm('هل أنت متأكد من حذف هذا القالب؟')) {
        router.delete(route('admin.templates.destroy', template.id));
    }
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-GB');
};

// Template modal methods
const showTemplateDetails = (template) => {
    selectedTemplate.value = template;
    showDetailsModal.value = true;
};

const editTemplate = (template) => {
    editingTemplate.value = template;

    // Clear errors but don't reset form data
    templateForm.clearErrors();

    // Set values directly
    templateForm.name = template.name || '';
    templateForm.category_id = template.category_id ? template.category_id.toString() : '';
    templateForm.thumbnail = null;
    thumbnailPreview.value = null;
    showEditTemplateModal.value = true;
};

const closeTemplateModal = () => {
    showCreateTemplateModal.value = false;
    showEditTemplateModal.value = false;
    showDetailsModal.value = false;
    editingTemplate.value = null;
    selectedTemplate.value = null;
    templateForm.reset();
    templateForm.clearErrors();
    thumbnailPreview.value = null;
};

const submitTemplateForm = () => {
    if (showCreateTemplateModal.value) {
        templateForm.post(route('admin.templates.store'), {
            forceFormData: true,
            onSuccess: () => {
                closeTemplateModal();
                // Recharger la page pour voir les nouvelles données
                router.reload();
            },
        });
    } else if (showEditTemplateModal.value) {
        // Debug: Afficher les données avant envoi
        console.log('Données du formulaire avant envoi:', {
            name: templateForm.name,
            category_id: templateForm.category_id,
            thumbnail: templateForm.thumbnail
        });

        // Ajouter _method: PUT directement dans le formulaire
        templateForm._method = 'PUT';

        // Utiliser POST pour les uploads de fichiers
        templateForm.post(route('admin.templates.update', editingTemplate.value.id), {
            forceFormData: true,
            onSuccess: () => {
                closeTemplateModal();
                // Recharger la page pour voir les modifications
                router.reload();
            },
        });
    }
};

const handleThumbnailUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        templateForm.thumbnail = file;

        // Create preview
        const reader = new FileReader();
        reader.onload = (e) => {
            thumbnailPreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};


</script>
