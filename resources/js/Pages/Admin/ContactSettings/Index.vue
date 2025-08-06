<template>
    <Head title="إعدادات الاتصال" />

    <AdminLayoutSidebar>
        <div class="max-w-4xl mx-auto p-6" dir="rtl">
            <!-- Header -->
            <div class="bg-white rounded-2xl shadow-lg p-8 mb-8">
                <div class="flex items-center justify-between mb-6">
                    <h1 class="text-3xl font-bold text-gray-900" style="text-align: right; direction: rtl;">
                        إعدادات الاتصال
                    </h1>
                    <div class="flex items-center space-x-2 space-x-reverse">
                        <svg class="w-8 h-8 text-purple-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-gray-600" style="text-align: right; direction: rtl;">
                    إدارة معلومات الاتصال التي تظهر للعملاء في صفحة "اتصل بنا"
                </p>
            </div>

            <!-- Settings Form -->
            <div class="bg-white rounded-2xl shadow-lg p-8">
                <form @submit.prevent="submitForm" class="space-y-6">
                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2" style="text-align: right; direction: rtl;">
                            البريد الإلكتروني *
                        </label>
                        <input
                            id="email"
                            v-model="form.email"
                            type="email"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200"
                            style="text-align: right; direction: rtl;"
                            :class="{ 'border-red-500': form.errors.email }"
                            placeholder="example@domain.com"
                            required
                        />
                        <div v-if="form.errors.email" class="mt-1 text-sm text-red-600" style="text-align: right; direction: rtl;">
                            {{ form.errors.email }}
                        </div>
                    </div>

                    <!-- Phone Field -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2" style="text-align: right; direction: rtl;">
                            رقم الهاتف *
                        </label>
                        <input
                            id="phone"
                            v-model="form.phone"
                            type="text"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200"
                            style="text-align: right; direction: rtl;"
                            :class="{ 'border-red-500': form.errors.phone }"
                            placeholder="+966 12 345 6789"
                            required
                        />
                        <div v-if="form.errors.phone" class="mt-1 text-sm text-red-600" style="text-align: right; direction: rtl;">
                            {{ form.errors.phone }}
                        </div>
                    </div>

                    <!-- Address Field -->
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2" style="text-align: right; direction: rtl;">
                            العنوان *
                        </label>
                        <textarea
                            id="address"
                            v-model="form.address"
                            rows="3"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent resize-none transition-all duration-200"
                            style="text-align: right; direction: rtl;"
                            :class="{ 'border-red-500': form.errors.address }"
                            placeholder="الرياض، المملكة العربية السعودية"
                            required
                        ></textarea>
                        <div v-if="form.errors.address" class="mt-1 text-sm text-red-600" style="text-align: right; direction: rtl;">
                            {{ form.errors.address }}
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end pt-6 border-t border-gray-200">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-8 py-3 bg-gradient-to-r from-purple-600 to-blue-600 text-white font-semibold rounded-lg hover:from-purple-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <svg v-if="form.processing" class="animate-spin h-5 w-5 ml-3" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <svg v-else class="w-5 h-5 ml-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 16c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-10H5V7h10v2z"/>
                            </svg>
                            حفظ التغييرات
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayoutSidebar>
</template>

<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayoutSidebar from '@/Layouts/AdminLayoutSidebar.vue';

// Props
const props = defineProps({
    settings: Object
});

// Form
const form = useForm({
    email: props.settings.email,
    phone: props.settings.phone,
    address: props.settings.address
});

// Methods
const submitForm = () => {
    form.put(route('admin.contact-settings.update'));
};
</script>
