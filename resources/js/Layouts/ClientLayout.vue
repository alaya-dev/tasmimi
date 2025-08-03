<template>
    <div :dir="direction" class="min-h-screen bg-gray-50">
        <!-- Custom Header (replaces navbar) - Responsive -->
        <header class="bg-white shadow-sm border-b border-gray-100 sticky top-0 z-50">
            <div class="w-full px-2 sm:px-4 lg:px-6">
                <!-- Desktop Layout -->
                <div class="hidden lg:block relative h-24 w-full">
                    <!-- Logo (right) - collé au bord droit -->
                    <div class="absolute right-0 top-1/2 transform -translate-y-1/2 flex items-center justify-end flex-shrink-0 pr-1">
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

                    <!-- Welcome Message (center) - parfaitement centré -->
                    <div class="absolute left-1/2 top-1/2 transform -translate-x-1/2 -translate-y-1/2 text-center px-4 w-full max-w-2xl">
                        <h1 class="text-3xl font-bold mb-2" style="font-family: 'Cairo', 'Amiri', 'Noto Sans Arabic', Arial, sans-serif;">
                            أهلاً بكم في
                            <span class="font-black text-4xl" style="color: #8d39c5; font-family: 'Dark Star Bold', 'Cairo', 'Amiri', 'Noto Sans Arabic', Arial, sans-serif; font-weight: 900; letter-spacing: 1px;">سامقة</span>
                        </h1>
                        <p class="text-gray-600">منصة سامقة توفر لك أدوات متقدمة وقوالب احترافية لتصميم بطاقات إلكترونية مميزة</p>
                    </div>

                    <!-- Login/Register or User Menu (left) - collé au bord gauche -->
                    <div class="absolute left-0 top-1/2 transform -translate-y-1/2 flex items-center justify-start flex-shrink-0 pl-1">
                        <div v-if="!$page.props.auth.user" class="flex items-center space-x-4 space-x-reverse">
                            <Link :href="route('login')" class="group flex items-center text-gray-700 hover:text-purple-600 font-semibold transition-all duration-200 px-6 py-3 rounded-xl hover:bg-purple-50 border border-gray-200 hover:border-purple-300">
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
                            <!-- User Dropdown -->
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <button class="flex items-center text-sm font-medium text-gray-700 hover:text-purple-600 transition-colors duration-200 group">
                                        <svg class="w-5 h-5 group-hover:rotate-180 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center mr-2 group-hover:scale-110 transition-transform duration-200 overflow-hidden">
                                            <img
                                                v-if="$page.props.auth.user.image"
                                                :src="$page.props.auth.user.image"
                                                :alt="$page.props.auth.user.email"
                                                class="w-full h-full object-cover rounded-2xl"
                                            />
                                            <div v-else class="w-full h-full bg-gradient-to-br from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center">
                                                <span class="text-white font-bold text-xl">{{ $page.props.auth.user.email.charAt(0).toUpperCase() }}</span>
                                            </div>
                                        </div>
                                    </button>
                                </template>
                                <template #content>
                                    <DropdownLink :href="route('client.home')" class="flex items-center space-x-2 space-x-reverse">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                        </svg>
                                        <span>الصفحة الرئيسية</span>
                                    </DropdownLink>
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
                                    <DropdownLink :href="route('client.subscriptions.index')" class="flex items-center space-x-2 space-x-reverse">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                        </svg>
                                        <span>الاشتراكات</span>
                                    </DropdownLink>
                                    <DropdownLink :href="route('client.subscription.manage')" class="flex items-center space-x-2 space-x-reverse">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span>إدارة الاشتراك</span>
                                    </DropdownLink>
                                    <DropdownLink :href="route('client.contact')" class="flex items-center space-x-2 space-x-reverse">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        <span>اتصل بنا</span>
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

                <!-- Mobile/Tablet Layout -->
                <div class="lg:hidden">
                    <!-- Mobile Header -->
                    <div class="flex justify-between items-center h-16 px-4">
                        <!-- Logo (right) -->
                        <div class="flex items-center">
                            <Link :href="route('client.home')" class="flex items-center group">
                                <div class="bg-white rounded-lg p-2 shadow-sm border border-gray-100">
                                    <img
                                        src="/images/logo.png"
                                        alt="سامقة"
                                        class="h-8 w-auto object-contain"
                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                    />
                                    <div class="w-8 h-8 bg-gradient-to-br from-purple-600 to-pink-600 rounded-lg flex items-center justify-center" style="display: none;">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                        </svg>
                                    </div>
                                </div>
                            </Link>
                        </div>

                        <!-- Mobile Menu Button & User Actions -->
                        <div class="flex items-center space-x-2 space-x-reverse">
                            <!-- Mobile Menu Toggle -->
                            <button @click="showMobileMenu = !showMobileMenu" class="p-2 text-gray-600 hover:text-purple-600 transition-colors duration-200 hover:bg-purple-50 rounded-lg">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path v-if="!showMobileMenu" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                    <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Mobile Welcome Message -->
                    <div class="text-center px-4 py-4 border-t border-gray-100">
                        <h1 class="text-xl sm:text-2xl font-bold mb-1" style="font-family: 'Cairo', 'Amiri', 'Noto Sans Arabic', Arial, sans-serif;">
                            أهلاً بكم في
                            <span class="font-black text-2xl sm:text-3xl" style="color: #8d39c5; font-family: 'Dark Star Bold', 'Cairo', 'Amiri', 'Noto Sans Arabic', Arial, sans-serif; font-weight: 900;">سامقة</span>
                        </h1>
                        <p class="text-gray-600 text-sm">منصة سامقة توفر لك أدوات متقدمة وقوالب احترافية لتصميم بطاقات إلكترونية مميزة</p>
                    </div>

                    <!-- Mobile Menu -->
                    <div v-if="showMobileMenu" class="border-t border-gray-100 bg-white">
                        <div class="px-4 py-4 space-y-3">
                            <div v-if="!$page.props.auth.user" class="space-y-3">
                                <Link :href="route('login')" class="block w-full text-center py-3 px-4 text-gray-700 hover:text-purple-600 font-semibold transition-all duration-200 rounded-xl hover:bg-purple-50 border border-gray-200">
                                    تسجيل الدخول
                                </Link>
                                <Link :href="route('register')" class="block w-full text-center bg-gradient-to-r from-purple-600 to-pink-600 text-white py-3 px-4 rounded-xl font-bold">
                                    إنشاء حساب
                                </Link>
                            </div>
                            <div v-else class="space-y-3">
                                <Link :href="route('client.home')" class="flex items-center space-x-3 space-x-reverse py-3 px-4 text-gray-700 hover:text-purple-600 hover:bg-purple-50 rounded-xl transition-all duration-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                    </svg>
                                    <span>الصفحة الرئيسية</span>
                                </Link>
                                <Link :href="route('client.profile')" class="flex items-center space-x-3 space-x-reverse py-3 px-4 text-gray-700 hover:text-purple-600 hover:bg-purple-50 rounded-xl transition-all duration-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    <span>الملف الشخصي</span>
                                </Link>
                                <Link :href="route('client.my-designs')" class="flex items-center space-x-3 space-x-reverse py-3 px-4 text-gray-700 hover:text-purple-600 hover:bg-purple-50 rounded-xl transition-all duration-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z"/>
                                    </svg>
                                    <span>تصاميمي</span>
                                </Link>
                                <Link :href="route('client.subscriptions.index')" class="flex items-center space-x-3 space-x-reverse py-3 px-4 text-gray-700 hover:text-purple-600 hover:bg-purple-50 rounded-xl transition-all duration-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>الاشتراكات</span>
                                </Link>
                                <Link :href="route('client.subscription.manage')" class="flex items-center space-x-3 space-x-reverse py-3 px-4 text-gray-700 hover:text-purple-600 hover:bg-purple-50 rounded-xl transition-all duration-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span>إدارة الاشتراك</span>
                                </Link>
                                <Link :href="route('client.contact')" class="flex items-center space-x-3 space-x-reverse py-3 px-4 text-gray-700 hover:text-purple-600 hover:bg-purple-50 rounded-xl transition-all duration-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    <span>اتصل بنا</span>
                                </Link>
                                <hr class="my-1">
                                <Link :href="route('logout')" method="post" class="flex items-center space-x-3 space-x-reverse py-3 px-4 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-xl transition-all duration-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                    </svg>
                                    <span>تسجيل الخروج</span>
                                </Link>
                            </div>
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
import { Link } from '@inertiajs/vue3';
import { useTranslations } from '@/Composables/useTranslations';

// Composables
const { direction } = useTranslations();

// State
const showMobileMenu = ref(false);
</script>
