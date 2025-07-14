<template>
    <Head title="الرئيسية - سامقة" />

    <ClientLayout>
        <div class="min-h-screen bg-gradient-to-br from-gray-50 to-white overflow-hidden">
            <!-- Hero Section with Description -->
            <div class="bg-gradient-to-r from-purple-600 via-purple-700 to-pink-600 text-white py-12">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                    <h1 class="text-4xl md:text-5xl font-black mb-4 leading-tight">
                        أهلاً بك في سامقة
                    </h1>
                    <p class="text-xl md:text-2xl font-light leading-relaxed max-w-3xl mx-auto opacity-90">
                        منصة سامقة توفر لك أدوات متقدمة وقوالب احترافية لتصميم بطاقات إلكترونية مميزة
                    </p>
                </div>
            </div>

            <!-- Main Content Section -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <!-- Categories and Designs Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
                    <!-- Categories Section -->
                    <div class="bg-white rounded-3xl shadow-xl p-8 border border-gray-100 hover:shadow-2xl transition-shadow duration-300">
                        <div class="flex items-center justify-between mb-8">
                            <h2 class="text-3xl font-bold text-gray-900">الفئات</h2>
                            <div class="w-12 h-12 bg-gradient-to-r from-purple-600 to-pink-600 rounded-2xl flex items-center justify-center shadow-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div
                                v-for="category in categories.slice(0, 6)"
                                :key="category.id"
                                class="p-6 bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl hover:from-purple-50 hover:to-pink-50 hover:border-purple-200 border border-gray-200 transition-all duration-300 cursor-pointer group hover:shadow-lg transform hover:-translate-y-1"
                            >
                                <div class="text-center">
                                    <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                        </svg>
                                    </div>
                                    <h3 class="font-bold text-gray-900 text-base mb-2">{{ category.name }}</h3>
                                    <p class="text-sm text-gray-600 font-medium">{{ category.active_templates_count || 0 }} قالب</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Featured Designs Section -->
                    <div class="bg-white rounded-3xl shadow-xl p-8 border border-gray-100 hover:shadow-2xl transition-shadow duration-300">
                        <div class="flex items-center justify-between mb-8">
                            <h2 class="text-3xl font-bold text-gray-900">التصاميم المميزة</h2>
                            <div class="w-12 h-12 bg-gradient-to-r from-purple-600 to-pink-600 rounded-2xl flex items-center justify-center shadow-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div
                                v-for="template in featuredTemplates.slice(0, 6)"
                                :key="template.id"
                                class="group relative bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl overflow-hidden hover:shadow-lg transition-all duration-300 cursor-pointer border border-gray-200 hover:border-purple-200 transform hover:-translate-y-1"
                            >
                                <div class="aspect-square bg-gradient-to-br from-purple-400 to-pink-500 flex items-center justify-center">
                                    <img
                                        v-if="template.thumbnail_url"
                                        :src="template.thumbnail_url"
                                        :alt="template.name"
                                        class="w-full h-full object-cover"
                                    />
                                    <div v-else class="text-white text-center">
                                        <svg class="w-10 h-10 mx-auto mb-3" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                        </svg>
                                        <p class="text-sm font-medium">{{ template.name }}</p>
                                    </div>
                                </div>
                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-all duration-300 flex items-center justify-center">
                                    <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                        <button class="bg-white text-purple-600 px-6 py-3 rounded-xl font-bold text-sm shadow-xl hover:shadow-2xl transform hover:scale-105 transition-all duration-200">
                                            معاينة
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="text-center">
                    <div class="flex flex-col sm:flex-row gap-6 justify-center items-center">
                        <Link
                            :href="route('client.templates')"
                            class="group bg-gradient-to-r from-purple-600 to-pink-600 text-white px-10 py-5 rounded-3xl font-bold text-xl hover:from-purple-700 hover:to-pink-700 transition-all duration-300 inline-flex items-center shadow-2xl hover:shadow-purple-500/25 transform hover:scale-105"
                        >
                            ابدأ التصميم الآن
                            <svg class="mr-4 w-7 h-7 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </Link>
                        <Link
                            :href="route('client.pricing')"
                            class="group border-3 border-purple-600 text-purple-600 px-10 py-5 rounded-3xl font-bold text-xl hover:bg-purple-600 hover:text-white transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105"
                        >
                            تصفح الأسعار
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </ClientLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ClientLayout from '@/Layouts/ClientLayout.vue';

// Define props
defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
    categories: {
        type: Array,
        default: () => []
    },
    featuredTemplates: {
        type: Array,
        default: () => []
    }
});
</script>
