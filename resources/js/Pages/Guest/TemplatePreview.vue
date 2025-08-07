<template>
    <Head :title="`معاينة القالب - ${template.name}`" />

    <div class="min-h-screen bg-gradient-to-br from-purple-50 to-pink-50">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <Link href="/" class="flex items-center space-x-2 space-x-reverse">
                            <img src="/images/logo.png" alt="سامقة" class="h-8 w-auto" />
                            <span class="text-xl font-bold text-purple-600">سامقة</span>
                        </Link>
                    </div>

                    <!-- Navigation -->
                    <div class="flex items-center space-x-4 space-x-reverse">
                        <Link
                            v-if="canLogin"
                            :href="route('login')"
                            class="text-gray-600 hover:text-purple-600 font-medium transition-colors"
                        >
                            تسجيل الدخول
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium transition-colors"
                        >
                            إنشاء حساب
                        </Link>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Template Info -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
                <div class="flex items-center justify-between">
                    <div class="text-right">
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ template.name }}</h1>
                        <p v-if="template.description" class="text-gray-600 mb-4">{{ template.description }}</p>
                        <div class="flex items-center space-x-4 space-x-reverse">
                            <span v-if="template.category" class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm font-medium">
                                {{ template.category.name }}
                            </span>
                            <span v-if="template.price > 0" class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                                {{ formatPrice(template.price) }} ريال
                            </span>
                            <span v-else class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                                مجاني
                            </span>
                        </div>
                    </div>

                    <!-- Edit Button -->
                    <div class="text-left">
                        <button
                            @click="redirectToEdit"
                            class="bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white px-8 py-3 rounded-lg font-bold text-lg shadow-lg transition-all duration-300 transform hover:scale-105"
                        >
                            <svg class="w-5 h-5 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            تعديل القالب
                        </button>
                    </div>
                </div>
            </div>

            <!-- Template Preview -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900 text-right">معاينة القالب</h2>
                    <p class="text-gray-600 text-right mt-1">يمكنك مشاهدة القالب هنا. للتعديل، يرجى تسجيل الدخول أولاً.</p>
                </div>

                <!-- Preview Container -->
                <div class="p-6">
                    <div class="relative">
                        <!-- Preview Area -->
                        <div class="bg-gray-100 rounded-lg p-8 min-h-[600px] flex items-center justify-center">
                            <!-- Template Thumbnail or Preview -->
                            <div v-if="template.thumbnail_url" class="max-w-md mx-auto">
                                <img
                                    :src="template.thumbnail_url"
                                    :alt="template.name"
                                    class="w-full h-auto rounded-lg shadow-lg"
                                />
                            </div>

                            <!-- Fallback Preview -->
                            <div v-else class="text-center">
                                <div class="w-64 h-96 bg-gradient-to-br from-purple-200 to-pink-200 rounded-lg shadow-lg flex items-center justify-center mx-auto mb-4">
                                    <div class="text-center">
                                        <svg class="w-16 h-16 text-purple-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17v4a2 2 0 002 2h4M13 5v4a2 2 0 002 2h4"></path>
                                        </svg>
                                        <h3 class="text-lg font-semibold text-purple-800">{{ template.name }}</h3>
                                        <p class="text-purple-600 text-sm mt-2">قالب تصميم احترافي</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Overlay for Read-Only -->
                        <div class="absolute inset-0 bg-black bg-opacity-10 rounded-lg flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity duration-300">
                            <div class="bg-white rounded-lg p-6 text-center shadow-xl">
                                <svg class="w-12 h-12 text-purple-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">معاينة فقط</h3>
                                <p class="text-gray-600 mb-4">للتعديل والتخصيص، يرجى تسجيل الدخول</p>
                                <button
                                    @click="redirectToEdit"
                                    class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg font-medium transition-colors"
                                >
                                    تسجيل الدخول والتعديل
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Template Features -->
            <div class="bg-white rounded-lg shadow-sm p-6 mt-8">
                <h3 class="text-xl font-semibold text-gray-900 mb-4 text-right">مميزات القالب</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17v4a2 2 0 002 2h4M13 5v4a2 2 0 002 2h4"></path>
                            </svg>
                        </div>
                        <div class="text-right">
                            <h4 class="font-medium text-gray-900">تصميم احترافي</h4>
                            <p class="text-sm text-gray-600">قالب مصمم بعناية</p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3 space-x-reverse">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </div>
                        <div class="text-right">
                            <h4 class="font-medium text-gray-900">قابل للتخصيص</h4>
                            <p class="text-sm text-gray-600">يمكن تعديل جميع العناصر</p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3 space-x-reverse">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="text-right">
                            <h4 class="font-medium text-gray-900">تحميل عالي الجودة</h4>
                            <p class="text-sm text-gray-600">تصدير بدقة عالية</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Call to Action -->
            <div class="bg-gradient-to-r from-purple-600 to-pink-600 rounded-lg p-8 mt-8 text-center">
                <h3 class="text-2xl font-bold text-white mb-4">جاهز لبدء التصميم؟</h3>
                <p class="text-purple-100 mb-6">سجل الدخول الآن وابدأ في تخصيص هذا القالب حسب احتياجاتك</p>
                <button
                    @click="redirectToEdit"
                    class="bg-white text-purple-600 hover:bg-gray-100 px-8 py-3 rounded-lg font-bold text-lg transition-colors"
                >
                    ابدأ التعديل الآن
                </button>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-gray-800 text-white py-8 mt-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <p>&copy; 2025 سامقة. جميع الحقوق محفوظة.</p>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    template: {
        type: Object,
        required: true
    },
    canLogin: {
        type: Boolean,
        default: true
    },
    canRegister: {
        type: Boolean,
        default: true
    }
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('ar-SA').format(price);
};

const redirectToEdit = () => {
    // Create the intended URL for after login
    const intendedUrl = route('client.templates.create', props.template.id);

    // Redirect to login with intended URL as query parameter
    window.location.href = route('login') + '?intended=' + encodeURIComponent(intendedUrl);
};
</script>

<style scoped>
/* Add any additional styles if needed */
</style>
