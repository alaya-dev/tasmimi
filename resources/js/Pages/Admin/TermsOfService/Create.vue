<template>
    <AdminLayoutSidebar>
        <Head title="إضافة اتفاقية الاستخدام" />
        
        <div class="bg-white rounded-lg shadow-md p-6" dir="rtl">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900 mb-2">إضافة اتفاقية الاستخدام</h1>
                <p class="text-gray-600">قم بكتابة اتفاقية الاستخدام بالتفصيل مع دعم التنسيق الغني</p>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- Title Field -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        عنوان الاتفاقية <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="title"
                        v-model="form.title"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="مثال: اتفاقية استخدام موقع سامقة للتصميم"
                        required
                    />
                    <div v-if="form.errors.title" class="mt-1 text-sm text-red-600">
                        {{ form.errors.title }}
                    </div>
                </div>

                <!-- Version Field -->
                <div>
                    <label for="version" class="block text-sm font-medium text-gray-700 mb-2">
                        رقم الإصدار
                    </label>
                    <input
                        type="text"
                        id="version"
                        v-model="form.version"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="مثال: 1.0"
                    />
                    <div v-if="form.errors.version" class="mt-1 text-sm text-red-600">
                        {{ form.errors.version }}
                    </div>
                </div>

                <!-- Rich Text Content -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        محتوى الاتفاقية <span class="text-red-500">*</span>
                    </label>
                    <RichTextEditor
                        ref="richTextEditor"
                        :initial-content="''"
                        placeholder="اكتب محتوى اتفاقية الاستخدام هنا..."
                        class="min-h-[400px]"
                    />
                    <div v-if="form.errors.content" class="mt-1 text-sm text-red-600">
                        {{ form.errors.content }}
                    </div>
                    <p class="mt-2 text-sm text-gray-500">
                        يمكنك استخدام التنسيق الغني مثل النص العريض، المائل، القوائم والعناوين. يدعم النص العربي مع المحاذاة من اليمين لليسار.
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end space-x-3 space-x-reverse pt-6 border-t border-gray-200">
                    <Link
                        :href="route('admin.terms-of-service.index')"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded-lg transition duration-200"
                    >
                        إلغاء
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing || !form.title"
                        class="bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white font-bold py-2 px-6 rounded-lg transition duration-200 disabled:cursor-not-allowed"
                    >
                        <span v-if="form.processing">جاري الحفظ...</span>
                        <span v-else>حفظ الاتفاقية</span>
                    </button>
                </div>
            </form>
        </div>
    </AdminLayoutSidebar>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayoutSidebar from '@/Layouts/AdminLayoutSidebar.vue'
import RichTextEditor from '@/Components/RichTextEditor.vue'

const richTextEditor = ref(null)

const form = useForm({
    title: '',
    content: '', // We'll get this from the editor when submitting
    version: '1.0'
})

const submit = () => {
    // IMPORTANT : Toujours récupérer le contenu via getCurrentContent() pour éviter toute auto-soumission.
    if (richTextEditor.value) {
        form.content = richTextEditor.value.getCurrentContent()
    }
    
    // Validate that we have content
    if (!form.content || form.content.trim() === '' || form.content === '<p></p>') {
        form.setError('content', 'محتوى الاتفاقية مطلوب')
        return
    }
    
    form.post(route('admin.terms-of-service.store'), {
        onSuccess: () => {
            // Form will be redirected automatically
        },
        onError: (errors) => {
            console.error('Validation errors:', errors)
        }
    })
}
</script>
