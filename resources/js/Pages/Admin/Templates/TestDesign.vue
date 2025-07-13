<template>
    <Head title="Test Design Editor" />

    <AdminLayoutSidebar>
        <div class="min-h-screen bg-gray-50 p-8" dir="rtl">
            <div class="max-w-4xl mx-auto">
                <div class="bg-white rounded-lg shadow-lg p-8">
                    <h1 class="text-3xl font-bold text-gray-800 mb-6">اختبار محرر التصميم</h1>
                    
                    <div class="space-y-4">
                        <div class="p-4 bg-green-100 border border-green-400 rounded-lg">
                            <h2 class="text-lg font-semibold text-green-800">✅ نجح الوصول إلى الصفحة!</h2>
                            <p class="text-green-700">تم تحميل صفحة اختبار محرر التصميم بنجاح.</p>
                        </div>

                        <div class="p-4 bg-blue-100 border border-blue-400 rounded-lg">
                            <h3 class="text-lg font-semibold text-blue-800 mb-2">معلومات القالب:</h3>
                            <ul class="text-blue-700 space-y-1">
                                <li><strong>ID:</strong> {{ template.id }}</li>
                                <li><strong>الاسم:</strong> {{ template.name }}</li>
                                <li><strong>الفئة:</strong> {{ template.category?.name || 'غير محدد' }}</li>
                                <li><strong>الحالة:</strong> {{ template.is_active ? 'مفعل' : 'معطل' }}</li>
                            </ul>
                        </div>

                        <div class="flex space-x-4 space-x-reverse">
                            <Link 
                                :href="route('admin.templates.index')"
                                class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition-colors"
                            >
                                العودة للقوالب
                            </Link>
                            
                            <Link 
                                :href="route('admin.templates.design', template.id)"
                                class="bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition-colors"
                            >
                                محرر التصميم الكامل
                            </Link>
                        </div>

                        <div class="mt-8 p-4 bg-yellow-100 border border-yellow-400 rounded-lg">
                            <h3 class="text-lg font-semibold text-yellow-800 mb-2">معلومات تقنية:</h3>
                            <div class="text-yellow-700 text-sm space-y-1">
                                <p><strong>Route Name:</strong> admin.templates.design</p>
                                <p><strong>URL:</strong> /admin/templates/{{ template.id }}/design</p>
                                <p><strong>Method:</strong> GET</p>
                                <p><strong>Controller:</strong> Admin\TemplateController@designEditor</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayoutSidebar>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayoutSidebar from '@/Layouts/AdminLayoutSidebar.vue';

const props = defineProps({
    template: Object,
    categories: Array,
    locale: String,
    translations: Object
});
</script>
