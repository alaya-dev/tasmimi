<template>
    <Head title="تعديل القالب" />

    <AdminLayoutSidebar>
        <template #breadcrumb>
            <Link :href="route('admin.templates.index')" class="text-gray-500 hover:text-gray-700">
                إدارة القوالب
            </Link>
            <svg class="w-5 h-5 text-gray-400 mx-2 rotate-180" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
            </svg>
            <span class="text-gray-500">تعديل القالب</span>
        </template>

        <template #header>
            <div class="flex justify-between items-center flex-row-reverse">
                <div class="flex items-center flex-row-reverse">
                    <Link 
                        :href="route('admin.templates.index')" 
                        class="ml-4 text-gray-600 hover:text-gray-900 transition-colors duration-200"
                    >
                        <svg class="w-5 h-5 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </Link>
                    <div class="text-right">
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">تعديل القالب</h2>
                        <p class="mt-1 text-sm text-gray-600">تحديث معلومات القالب</p>
                    </div>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 px-6 py-4 text-right">
                        <h3 class="text-lg font-semibold text-white">تعديل معلومات القالب</h3>
                        <p class="text-blue-100 text-sm">تحديث تفاصيل القالب</p>
                    </div>
                    
                    <div class="p-6">
                        <form @submit.prevent="submit" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Name Field -->
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2 text-right">
                                        اسم القالب *
                                    </label>
                                    <input
                                        id="name"
                                        v-model="form.name"
                                        type="text"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-right"
                                        :class="{ 'border-red-500': form.errors.name }"
                                        required
                                    />
                                    <div v-if="form.errors.name" class="mt-1 text-sm text-red-600 text-right">
                                        {{ form.errors.name }}
                                    </div>
                                </div>

                                <!-- Category Field -->
                                <div>
                                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2 text-right">
                                        الفئة *
                                    </label>
                                    <select
                                        id="category_id"
                                        v-model="form.category_id"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-right"
                                        :class="{ 'border-red-500': form.errors.category_id }"
                                        required
                                    >
                                        <option value="">اختر الفئة</option>
                                        <option v-for="category in categories" :key="category.id" :value="category.id">
                                            {{ category.name }}
                                        </option>
                                    </select>
                                    <div v-if="form.errors.category_id" class="mt-1 text-sm text-red-600 text-right">
                                        {{ form.errors.category_id }}
                                    </div>
                                </div>
                            </div>

                            <!-- Current Thumbnail -->
                            <div v-if="template.thumbnail">
                                <label class="block text-sm font-medium text-gray-700 mb-2 text-right">
                                    الصورة المصغرة الحالية
                                </label>
                                <div class="flex justify-end mb-4">
                                    <img 
                                        :src="template.thumbnail_url || `/storage/${template.thumbnail}`" 
                                        :alt="template.name"
                                        class="w-32 h-32 object-cover rounded-lg border-2 border-gray-200"
                                    />
                                </div>
                            </div>

                            <!-- Thumbnail Field -->
                            <div>
                                <label for="thumbnail" class="block text-sm font-medium text-gray-700 mb-2 text-right">
                                    {{ template.thumbnail ? 'تغيير الصورة المصغرة' : 'الصورة المصغرة' }}
                                </label>
                                <div class="flex items-center space-x-4 space-x-reverse">
                                    <div class="flex-1">
                                        <input
                                            id="thumbnail"
                                            ref="thumbnailInput"
                                            type="file"
                                            accept="image/*"
                                            @change="handleThumbnailUpload"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            :class="{ 'border-red-500': form.errors.thumbnail }"
                                        />
                                        <p class="mt-1 text-sm text-gray-500 text-right">
                                            JPG, PNG, GIF حتى 2MB
                                        </p>
                                    </div>
                                    <div v-if="thumbnailPreview" class="w-20 h-20">
                                        <img 
                                            :src="thumbnailPreview" 
                                            alt="معاينة الصورة الجديدة"
                                            class="w-full h-full object-cover rounded-lg border-2 border-gray-200"
                                        />
                                    </div>
                                </div>
                                <div v-if="form.errors.thumbnail" class="mt-1 text-sm text-red-600 text-right">
                                    {{ form.errors.thumbnail }}
                                </div>
                            </div>



                            <!-- Active Status -->
                            <div class="flex items-center justify-end">
                                <label for="is_active" class="flex items-center cursor-pointer">
                                    <input
                                        id="is_active"
                                        v-model="form.is_active"
                                        type="checkbox"
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    />
                                    <span class="mr-2 text-sm text-gray-700">تفعيل القالب</span>
                                </label>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="flex justify-end space-x-4 space-x-reverse pt-6 border-t border-gray-200">
                                <Link
                                    :href="route('admin.templates.index')"
                                    class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                >
                                    إلغاء
                                </Link>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50"
                                >
                                    <svg v-if="form.processing" class="animate-spin h-4 w-4 ml-2" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    تحديث القالب
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayoutSidebar>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayoutSidebar from '@/Layouts/AdminLayoutSidebar.vue';

const props = defineProps({
    template: Object,
    categories: Array,
});

// Form
const form = useForm({
    name: props.template.name,
    category_id: props.template.category_id,
    thumbnail: null,
    is_active: props.template.is_active,
});

// Thumbnail preview
const thumbnailPreview = ref(null);
const thumbnailInput = ref(null);

// Methods
const handleThumbnailUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.thumbnail = file;
        
        // Create preview
        const reader = new FileReader();
        reader.onload = (e) => {
            thumbnailPreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const submit = () => {
    form.put(route('admin.templates.update', props.template.id), {
        forceFormData: true,
    });
};
</script>
