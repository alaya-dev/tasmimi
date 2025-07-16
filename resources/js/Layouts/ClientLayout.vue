<template>
    <div :dir="direction" class="min-h-screen bg-gray-50">
        <!-- Custom Header (replaces navbar) -->
        <header class="bg-white shadow-sm border-b border-gray-100 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-24">
                    <!-- Logo (right) - Moved to the right corner -->
                    <div class="flex items-center">
                        <Link :href="route('client.home')" class="flex items-center group">
                            <div class="bg-white rounded-xl p-3 shadow-sm border border-gray-100 group-hover:shadow-md transition-all duration-300">
                                <img
                                    src="/images/logo.png"
                                    alt="سامقة"
                                    class="h-16 w-auto object-contain group-hover:scale-105 transition-transform duration-300"
                                    style="max-height: 64px; min-width: 80px;"
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                />
                                <div class="w-16 h-16 bg-gradient-to-br from-purple-600 to-pink-600 rounded-xl flex items-center justify-center group-hover:scale-105 transition-transform duration-300" style="display: none;">
                                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                </div>
                            </div>
                        </Link>
                    </div>

                    <!-- Welcome Message (center) -->
                    <div class="flex-1 text-center">
                        <h1 class="text-3xl font-bold mb-2" style="font-family: 'Cairo', 'Amiri', 'Noto Sans Arabic', Arial, sans-serif;">
                            أهلاً بكم في
                            <span class="font-black text-4xl" style="background: linear-gradient(135deg, #8B5CF6 0%, #EC4899 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; font-family: 'Cairo', 'Amiri', 'Noto Sans Arabic', Arial, sans-serif; font-weight: 900; letter-spacing: 1px;">سَامِقَة</span>
                        </h1>
                        <p class="text-gray-600">منصة سامقة توفر لك أدوات متقدمة وقوالب احترافية لتصميم بطاقات إلكترونية مميزة</p>
                    </div>

                    <!-- Login/Register or User Menu (left) - Moved to the left -->
                    <div class="flex items-center justify-end min-w-[180px]">
                        <div v-if="!$page.props.auth.user" class="flex items-center space-x-4 space-x-reverse">
                            <Link :href="route('login')" class="group flex items-center text-gray-700 hover:text-purple-600 font-semibold transition-all duration-200 px-4 py-2 rounded-xl hover:bg-purple-50 border border-gray-200 hover:border-purple-300">
                                تسجيل الدخول
                                <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                </svg>
                            </Link>
                            <Link :href="route('register')" class="group flex items-center bg-gradient-to-r from-purple-600 to-pink-600 text-white px-6 py-3 rounded-2xl hover:from-purple-700 hover:to-pink-700 transition-all duration-300 font-bold shadow-lg hover:shadow-xl transform hover:scale-105">
                                إنشاء حساب
                                <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                </svg>
                            </Link>
                        </div>
                        <div v-else class="flex items-center space-x-4 space-x-reverse">
                            <!-- Cart Icon -->
                            <Link :href="route('client.cart')" class="relative p-3 text-gray-600 hover:text-purple-600 transition-colors duration-200 hover:bg-purple-50 rounded-xl group">
                                <svg class="w-6 h-6 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l-1.5-6m0 0h15.5M17 21a2 2 0 100-4 2 2 0 000 4zM9 21a2 2 0 100-4 2 2 0 000 4z"/>
                                </svg>
                                <span v-if="cartCount > 0" class="absolute -top-1 -left-1 bg-gradient-to-r from-red-500 to-pink-500 text-white text-xs rounded-full h-6 w-6 flex items-center justify-center font-bold animate-pulse">
                                    {{ cartCount }}
                                </span>
                            </Link>
                            <!-- User Dropdown -->
                            <Dropdown align="left" width="48">
                                <template #trigger>
                                    <button class="flex items-center text-sm font-medium text-gray-700 hover:text-purple-600 transition-colors duration-200 group">
                                        <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center ml-2 group-hover:scale-110 transition-transform duration-200">
                                            <span class="text-white font-bold text-lg">{{ $page.props.auth.user.email.charAt(0).toUpperCase() }}</span>
                                        </div>
                                        <svg class="w-5 h-5 group-hover:rotate-180 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>
                                </template>
                                <template #content>
                                    <DropdownLink :href="route('client.profile')" class="flex items-center space-x-2 space-x-reverse">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        <span>الملف الشخصي</span>
                                    </DropdownLink>
                                    <DropdownLink :href="route('client.my-designs')" class="flex items-center space-x-2 space-x-reverse">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z"/>
                                        </svg>
                                        <span>تصاميمي</span>
                                    </DropdownLink>
                                    <DropdownLink :href="route('client.orders')" class="flex items-center space-x-2 space-x-reverse">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                        </svg>
                                        <span>الطلبات</span>
                                    </DropdownLink>
                                    <DropdownLink :href="route('client.orders')" class="flex items-center space-x-2 space-x-reverse">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <span>الفواتير</span>
                                    </DropdownLink>
                                    <hr class="my-1">
                                    <DropdownLink :href="route('logout')" method="post" as="button" class="flex items-center space-x-2 space-x-reverse text-red-600 hover:text-red-800">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        <span>تسجيل الخروج</span>
                                    </DropdownLink>
                                </template>
                            </Dropdown>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1">
            <slot />
        </main>


    </div>
</template>

<script setup>
import { ref } from 'vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import MobileNavLink from '@/Components/MobileNavLink.vue';
import { Link } from '@inertiajs/vue3';
import { useTranslations } from '@/Composables/useTranslations';

// Props
defineProps({
    cartCount: {
        type: Number,
        default: 0
    }
});

// Composables
const { __, direction } = useTranslations();

// State
const showMobileMenu = ref(false);
</script>
