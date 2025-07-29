<template>
    <Head title="تعديل اتفاقية الاستخدام" />

    <AdminLayoutSidebar>
        <div class="p-6" dir="rtl">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div class="text-right">
                    <h1 class="text-2xl font-bold text-gray-900">تعديل اتفاقية الاستخدام</h1>
                    <p class="text-gray-600 mt-1">تعديل اتفاقية الاستخدام: {{ terms.title }}</p>
                </div>
                
                <div class="flex space-x-3 space-x-reverse">
                    <Link
                        :href="route('admin.terms-of-service.index')"
                        class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        العودة للقائمة
                    </Link>
                </div>
            </div>

            <!-- File Info (if has file) -->
            <div v-if="terms.file_name" class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="h-8 w-8 text-blue-500 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-blue-900">{{ terms.file_name }}</p>
                            <p class="text-sm text-blue-700">{{ terms.formatted_file_size }} • {{ getFileTypeLabel(terms.file_type) }}</p>
                        </div>
                    </div>
                    <div class="flex space-x-2 space-x-reverse">
                        <a
                            :href="route('admin.terms-of-service.download', terms.id)"
                            class="inline-flex items-center px-3 py-1 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm"
                        >
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            تحميل
                        </a>
                        <button
                            @click="deleteFile"
                            class="inline-flex items-center px-3 py-1 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm"
                        >
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            حذف الملف
                        </button>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900 text-right">
                        {{ terms.file_name ? 'تعديل محتوى الملف' : 'تعديل اتفاقية الاستخدام' }}
                    </h2>
                </div>
                
                <form @submit.prevent="submit" class="p-6 space-y-6" enctype="multipart/form-data">
                    <!-- Title -->
                    <div class="text-right">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2 text-right">
                            عنوان الاتفاقية <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="title"
                            v-model="form.title"
                            type="text"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-right"
                            :class="{ 'border-red-500': errors.title }"
                            placeholder="مثال: اتفاقية الاستخدام وسياسة الخصوصية"
                            required
                            dir="rtl"
                        />
                        <div v-if="errors.title" class="mt-1 text-sm text-red-600 text-right">
                            {{ errors.title }}
                        </div>
                    </div>

                    <!-- Replace File Section -->
                    <div v-if="!showFileUpload" class="text-right">
                        <div class="flex justify-between items-center mb-4">
                            <label class="block text-sm font-medium text-gray-700 text-right">
                                {{ terms.file_name ? 'تعديل المحتوى المستخرج' : 'محتوى الاتفاقية' }}
                                <span class="text-red-500">*</span>
                            </label>
                            <button
                                type="button"
                                @click="showFileUpload = true"
                                class="px-3 py-1 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm"
                            >
                                {{ terms.file_name ? 'استبدال الملف' : 'رفع ملف جديد' }}
                            </button>
                        </div>

                        <!-- File Content Editor -->
                        <div v-if="terms.file_name" class="text-right">
                            <textarea
                                v-model="form.extracted_content"
                                rows="20"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-right"
                                :class="{ 'border-red-500': errors.extracted_content }"
                                placeholder="محتوى الملف المستخرج..."
                                dir="rtl"
                            ></textarea>
                            <div v-if="errors.extracted_content" class="mt-1 text-sm text-red-600 text-right">
                                {{ errors.extracted_content }}
                            </div>
                            <p class="mt-2 text-sm text-gray-500 text-right">
                                يمكنك تعديل المحتوى المستخرج من الملف مباشرة هنا. التغييرات ستُحفظ دون تأثير على الملف الأصلي.
                            </p>
                        </div>

                        <!-- Manual Content Blocks -->
                        <div v-else>
                            <div class="flex justify-between items-center mb-4">
                                <button
                                    type="button"
                                    @click="addContentBlock"
                                    class="px-3 py-1 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm"
                                >
                                    + إضافة بند جديد
                                </button>
                            </div>

                            <div v-if="form.content_blocks.length === 0" class="text-center py-8 bg-gray-50 rounded-lg">
                                <p class="text-gray-500 mb-4">لا توجد بنود بعد</p>
                                <button
                                    type="button"
                                    @click="addContentBlock"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                                >
                                    إضافة أول بند
                                </button>
                            </div>

                            <div v-else class="space-y-6">
                                <div 
                                    v-for="(block, index) in form.content_blocks" 
                                    :key="index"
                                    class="border border-gray-200 rounded-lg p-4 bg-gray-50"
                                >
                                    <div class="flex justify-between items-center mb-4">
                                        <h3 class="text-lg font-medium text-gray-900 text-right">
                                            البند رقم {{ index + 1 }}
                                        </h3>
                                        <button
                                            v-if="form.content_blocks.length > 1"
                                            type="button"
                                            @click="removeContentBlock(index)"
                                            class="text-red-600 hover:text-red-800 text-sm"
                                        >
                                            حذف البند
                                        </button>
                                    </div>

                                    <!-- Subtitle -->
                                    <div class="mb-4 text-right">
                                        <label :for="'subtitle_' + index" class="block text-sm font-medium text-gray-700 mb-2 text-right">
                                            عنوان البند <span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            :id="'subtitle_' + index"
                                            v-model="block.subtitle"
                                            type="text"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-right"
                                            :class="{ 'border-red-500': errors[`content_blocks.${index}.subtitle`] }"
                                            placeholder="مثال: المادة 1: المقدمة"
                                            required
                                            dir="rtl"
                                        />
                                        <div v-if="errors[`content_blocks.${index}.subtitle`]" class="mt-1 text-sm text-red-600 text-right">
                                            {{ errors[`content_blocks.${index}.subtitle`] }}
                                        </div>
                                    </div>

                                    <!-- Content -->
                                    <div class="text-right">
                                        <label :for="'content_' + index" class="block text-sm font-medium text-gray-700 mb-2 text-right">
                                            محتوى البند <span class="text-red-500">*</span>
                                        </label>
                                        <textarea
                                            :id="'content_' + index"
                                            v-model="block.content"
                                            rows="6"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-right"
                                            :class="{ 'border-red-500': errors[`content_blocks.${index}.content`] }"
                                            placeholder="اكتب محتوى البند هنا..."
                                            required
                                            dir="rtl"
                                        ></textarea>
                                        <div v-if="errors[`content_blocks.${index}.content`]" class="mt-1 text-sm text-red-600 text-right">
                                            {{ errors[`content_blocks.${index}.content`] }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- File Upload Section -->
                    <div v-else class="text-right">
                        <div class="flex justify-between items-center mb-4">
                            <label class="block text-sm font-medium text-gray-700 text-right">
                                {{ terms.file_name ? 'استبدال الملف' : 'رفع ملف جديد' }}
                            </label>
                            <button
                                type="button"
                                @click="showFileUpload = false"
                                class="px-3 py-1 bg-gray-600 text-white rounded-md hover:bg-gray-700 text-sm"
                            >
                                إلغاء
                            </button>
                        </div>

                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                            <input
                                ref="fileInput"
                                type="file"
                                accept=".pdf,.doc,.docx"
                                @change="handleFileUpload"
                                class="hidden"
                            />
                            
                            <div v-if="!selectedFile">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="mt-4">
                                    <button
                                        type="button"
                                        @click="$refs.fileInput.click()"
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700"
                                    >
                                        اختر ملف
                                    </button>
                                    <p class="mt-2 text-sm text-gray-500">PDF, DOC, DOCX حتى 10MB</p>
                                </div>
                            </div>
                            
                            <div v-else class="text-center">
                                <div class="flex items-center justify-center mb-2">
                                    <svg class="h-8 w-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <p class="text-sm font-medium text-gray-900">{{ selectedFile.name }}</p>
                                <p class="text-sm text-gray-500">{{ formatFileSize(selectedFile.size) }}</p>
                                <button
                                    type="button"
                                    @click="removeFile"
                                    class="mt-2 text-sm text-red-600 hover:text-red-700"
                                >
                                    إزالة الملف
                                </button>
                            </div>
                        </div>
                        <div v-if="errors.file" class="mt-1 text-sm text-red-600 text-right">
                            {{ errors.file }}
                        </div>
                    </div>

                    <!-- Active Status -->
                    <div class="flex items-center justify-end">
                        <label for="is_active" class="ml-2 block text-sm text-gray-900 text-right">
                            تفعيل هذه الاتفاقية (سيتم إلغاء تفعيل الاتفاقيات الأخرى)
                        </label>
                        <input
                            id="is_active"
                            v-model="form.is_active"
                            type="checkbox"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        />
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end space-x-3 space-x-reverse">
                        <Link
                            :href="route('admin.terms-of-service.index')"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors"
                        >
                            إلغاء
                        </Link>
                        <button
                            type="submit"
                            :disabled="processing"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50"
                        >
                            {{ processing ? 'جاري الحفظ...' : 'حفظ التغييرات' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayoutSidebar>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link, useForm, router } from '@inertiajs/vue3'
import AdminLayoutSidebar from '@/Layouts/AdminLayoutSidebar.vue'

const props = defineProps({
    terms: Object,
})

// File upload toggle
const showFileUpload = ref(false)

// Selected file
const selectedFile = ref(null)

// Form
const form = useForm({
    title: props.terms.title,
    content_blocks: props.terms.content_blocks || [],
    extracted_content: props.terms.extracted_content || '',
    file: null,
    is_active: props.terms.is_active,
})

const processing = ref(false)
const errors = ref({})

// File handling
const handleFileUpload = (event) => {
    const file = event.target.files[0]
    if (file) {
        selectedFile.value = file
        form.file = file
    }
}

const removeFile = () => {
    selectedFile.value = null
    form.file = null
}

const formatFileSize = (bytes) => {
    if (bytes >= 1048576) {
        return (bytes / 1048576).toFixed(2) + ' ميجابايت'
    } else if (bytes >= 1024) {
        return (bytes / 1024).toFixed(2) + ' كيلوبايت'
    } else {
        return bytes + ' بايت'
    }
}

const getFileTypeLabel = (type) => {
    const labels = {
        'word': 'مستند Word',
        'pdf': 'مستند PDF'
    }
    return labels[type] || type
}

// Content blocks methods
const addContentBlock = () => {
    form.content_blocks.push({
        subtitle: '',
        content: ''
    })
}

const removeContentBlock = (index) => {
    if (form.content_blocks.length > 1) {
        form.content_blocks.splice(index, 1)
    }
}

// Delete file method
const deleteFile = () => {
    if (confirm('هل أنت متأكد من حذف الملف؟ سيتم تحويل المحتوى إلى النمط اليدوي.')) {
        router.delete(route('admin.terms-of-service.delete-file', props.terms.id))
    }
}

const submit = () => {
    processing.value = true
    
    if (showFileUpload.value && selectedFile.value) {
        form.put(route('admin.terms-of-service.update', props.terms.id), {
            forceFormData: true,
            onSuccess: () => {
                processing.value = false
            },
            onError: (formErrors) => {
                processing.value = false
                errors.value = formErrors
            }
        })
    } else {
        form.put(route('admin.terms-of-service.update', props.terms.id), {
            onSuccess: () => {
                processing.value = false
            },
            onError: (formErrors) => {
                processing.value = false
                errors.value = formErrors
            }
        })
    }
}
</script>
