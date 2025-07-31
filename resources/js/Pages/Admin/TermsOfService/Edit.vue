<template>
    <AdminLayoutSidebar>
        <Head title="تعديل اتفاقية الاستخدام" />
        
        <div class="bg-white rounded-lg shadow-md p-6" dir="rtl">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900 mb-2">تعديل اتفاقية الاستخدام</h1>
                <p class="text-gray-600">قم بتعديل محتوى اتفاقية الاستخدام مع دعم التنسيق الغني</p>
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
                        :initial-content="props.terms.content || ''"
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

                <!-- Terms Status -->
                <div v-if="terms.is_active" class="p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-green-600 ml-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-sm font-medium text-green-800">هذه الاتفاقية نشطة حالياً</span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <Link
                            :href="route('admin.terms-of-service.index')"
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded-lg transition duration-200"
                        >
                            إلغاء
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing || !form.title || !form.content"
                            class="bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white font-bold py-2 px-6 rounded-lg transition duration-200 disabled:cursor-not-allowed"
                        >
                            <span v-if="form.processing">جاري التحديث...</span>
                            <span v-else>تحديث الاتفاقية</span>
                        </button>
                    </div>
                    
                    <!-- Activate Terms Button -->
                    <button
                        v-if="!terms.is_active"
                        type="button"
                        @click="activateTerms"
                        :disabled="activating"
                        class="bg-green-600 hover:bg-green-700 disabled:bg-green-400 text-white font-bold py-2 px-6 rounded-lg transition duration-200 disabled:cursor-not-allowed"
                    >
                        <span v-if="activating">جاري التفعيل...</span>
                        <span v-else>تفعيل الاتفاقية</span>
                    </button>
                </div>
            </form>
        </div>
    </AdminLayoutSidebar>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link, useForm, router } from '@inertiajs/vue3'
import AdminLayoutSidebar from '@/Layouts/AdminLayoutSidebar.vue'
import RichTextEditor from '@/Components/RichTextEditor.vue'

const props = defineProps({
    terms: Object
})

const activating = ref(false)
const richTextEditor = ref(null)

const form = useForm({
    title: props.terms.title || '',
    content: '', // We'll get this from the editor when submitting
    version: props.terms.version || '1.0'
})

const submit = () => {
    // Get the current content from the rich text editor
    if (richTextEditor.value) {
        form.content = richTextEditor.value.getCurrentContent()
    }
    
    form.put(route('admin.terms-of-service.update', props.terms.id), {
        onSuccess: () => {
            // Form will be redirected automatically
        },
        onError: (errors) => {
            console.error('Validation errors:', errors)
        }
    })
}

const activateTerms = () => {
    activating.value = true
    
    router.post(route('admin.terms-of-service.activate', props.terms.id), {}, {
        onSuccess: () => {
            activating.value = false
        },
        onError: () => {
            activating.value = false
        }
    })
}
</script>
