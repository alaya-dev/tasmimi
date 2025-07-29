<template>
    <Head title="تعديل اتفاقية الاستخدام" />

    <AdminLayoutSidebar>
        <div class="p-6">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">تعديل اتفاقية الاستخدام</h1>
                    <p class="text-gray-600 mt-1">تعديل اتفاقية الاستخدام الموجودة</p>
                </div>
                
                <div class="flex space-x-3 space-x-reverse">
                    <Link
                        :href="route('terms-of-service.show')"
                        target="_blank"
                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                        معاينة
                    </Link>
                    
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

            <!-- Form -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">تعديل معلومات الاتفاقية</h2>
                </div>
                
                <form @submit.prevent="submit" class="p-6 space-y-6">
                    <!-- Title -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2 text-right">
                            عنوان الاتفاقية <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="title"
                            v-model="form.title"
                            type="text"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-right"
                            :class="{ 'border-red-500': errors.title }"
                            required
                        />
                        <div v-if="errors.title" class="mt-1 text-sm text-red-600 text-right">
                            {{ errors.title }}
                        </div>
                    </div>

                    <!-- Version -->
                    <div>
                        <label for="version" class="block text-sm font-medium text-gray-700 mb-2 text-right">
                            رقم الإصدار <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="version"
                            v-model="form.version"
                            type="text"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-right"
                            :class="{ 'border-red-500': errors.version }"
                            required
                        />
                        <div v-if="errors.version" class="mt-1 text-sm text-red-600 text-right">
                            {{ errors.version }}
                        </div>
                    </div>

                    <!-- Effective Date -->
                    <div>
                        <label for="effective_date" class="block text-sm font-medium text-gray-700 mb-2 text-right">
                            تاريخ السريان
                        </label>
                        <input
                            id="effective_date"
                            v-model="form.effective_date"
                            type="date"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            :class="{ 'border-red-500': errors.effective_date }"
                        />
                        <div v-if="errors.effective_date" class="mt-1 text-sm text-red-600 text-right">
                            {{ errors.effective_date }}
                        </div>
                    </div>

                    <!-- Content -->
                    <div>
                        <label for="content" class="block text-sm font-medium text-gray-700 mb-2 text-right">
                            محتوى الاتفاقية <span class="text-red-500">*</span>
                        </label>
                        <div class="mb-2 text-sm text-gray-600 text-right">
                            يمكنك استخدام HTML لتنسيق النص.
                        </div>
                        <textarea
                            id="content"
                            v-model="form.content"
                            rows="20"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-right font-mono"
                            :class="{ 'border-red-500': errors.content }"
                            required
                            dir="rtl"
                        ></textarea>
                        <div v-if="errors.content" class="mt-1 text-sm text-red-600 text-right">
                            {{ errors.content }}
                        </div>
                    </div>

                    <!-- Active Status -->
                    <div class="flex items-center">
                        <input
                            id="is_active"
                            v-model="form.is_active"
                            type="checkbox"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        />
                        <label for="is_active" class="mr-2 block text-sm text-gray-900">
                            تفعيل هذه الاتفاقية (سيتم إلغاء تفعيل الاتفاقيات الأخرى)
                        </label>
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
                            {{ processing ? 'جاري الحفظ...' : 'حفظ التعديلات' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayoutSidebar>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayoutSidebar from '@/Layouts/AdminLayoutSidebar.vue'

const props = defineProps({
    terms: Object,
})

// Form
const form = useForm({
    title: props.terms.title,
    content: props.terms.content,
    version: props.terms.version,
    effective_date: props.terms.effective_date ? props.terms.effective_date.split('T')[0] : '',
    is_active: props.terms.is_active,
})

const processing = ref(false)
const errors = ref({})

// Methods
const submit = () => {
    processing.value = true
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
</script>
