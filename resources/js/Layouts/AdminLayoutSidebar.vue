<template>
    <div :dir="direction" class="min-h-screen bg-gray-100">
        <!-- Main Content -->
        <div class="content-with-sidebar">
            <!-- Top Bar -->
            <div class="fixed top-0 left-0 bg-white shadow-sm border-b border-gray-200 z-40 h-16" 
                 style="right: 16rem;">
                <div class="px-4 sm:px-6 lg:px-8 h-full">
                    <div class="flex justify-between items-center h-full">
                        <div class="flex items-center">
                            <!-- Mobile menu button -->
                            <button
                                @click="sidebarOpen = true"
                                class="lg:hidden text-gray-500 hover:text-gray-700"
                            >
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                                </svg>
                            </button>

                            <!-- Breadcrumb -->
                            <nav class="hidden lg:flex mr-4">
                                <ol class="flex items-center space-x-2 space-x-reverse flex-row-reverse">
                                    <li>
                                        <Link :href="route('admin.dashboard')" class="text-gray-500 hover:text-gray-700">
                                            لوحة التحكم
                                        </Link>
                                    </li>
                                    <li v-if="$slots.breadcrumb">
                                        <svg class="w-5 h-5 text-gray-400 rotate-180" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    </li>
                                    <li v-if="$slots.breadcrumb">
                                        <slot name="breadcrumb" />
                                    </li>
                                </ol>
                            </nav>
                        </div>

                        <!-- Right side actions -->
                        <div class="flex items-center space-x-4">
                            <!-- Notifications -->
                            <button class="text-gray-500 hover:text-gray-700">
                                
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Wrapper with padding for fixed top bar -->
            <div class="pt-16">
                <!-- Page Header -->
                <header v-if="$slots.header" class="bg-white shadow-sm">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        <slot name="header" />
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1">
                    <slot />
                </main>
            </div>
        </div>

        <!-- Sidebar - Fixed on the right -->
        <div :class="[
            'sidebar-right bg-white shadow-lg transform transition-transform duration-300 ease-in-out',
            'lg:translate-x-0',
            sidebarOpen ? 'translate-x-0' : 'translate-x-full lg:translate-x-0'
        ]">
            <!-- User Info at Top -->
            <div class="bg-gradient-to-l from-blue-600 to-purple-600 px-6 py-4">
                <div class="flex items-center justify-between flex-row-reverse">
                    <div class="flex items-center flex-row-reverse">
                        <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                            <img
                                v-if="$page.props.auth.user.image"
                                :src="$page.props.auth.user.image"
                                :alt="$page.props.auth.user.email"
                                class="w-10 h-10 rounded-full object-cover"
                            />
                            <span v-else class="text-white font-semibold text-sm">
                                {{ $page.props.auth.user.email.charAt(0).toUpperCase() }}
                            </span>
                        </div>
                        <div class="flex-1 min-w-0 mr-3 text-right">
                            <p class="text-white font-medium text-sm truncate">{{ $page.props.auth.user.email }}</p>
                            <p class="text-blue-100 text-xs truncate">مدير عام</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2 space-x-reverse">
                        <Dropdown align="left" width="48">
                            <template #trigger>
                                <button class="text-white hover:text-blue-200 transition-colors">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                    </svg>
                                </button>
                            </template>
                            <template #content>
                                <DropdownLink :href="route('profile.edit')">الملف الشخصي</DropdownLink>
                                <DropdownLink :href="route('logout')" method="post" as="button">
                                    تسجيل الخروج
                                </DropdownLink>
                            </template>
                        </Dropdown>
                        <button
                            @click="sidebarOpen = false"
                            class="lg:hidden text-white hover:text-blue-200 transition-colors"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Logo Section -->
            <div class="bg-gradient-to-l from-blue-500 to-purple-500 px-6 py-3 border-b border-blue-400">
                <div class="flex items-center justify-center flex-row-reverse">
                    <span class="text-white font-bold text-lg mr-3">لوحة تحكم سامقة  </span>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="mt-8 px-4 overflow-y-auto" style="height: calc(100vh - 200px);" dir="rtl">
                <div class="space-y-2">
                    <!-- Dashboard -->
                    <Link
                        :href="route('admin.dashboard')"
                        :class="[
                            'group flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200 text-right',
                            route().current('admin.dashboard')
                                ? 'bg-blue-50 text-blue-700 border-r-4 border-blue-700'
                                : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900'
                        ]"
                    >
                        <span class="text-right flex-1">لوحة التحكم</span>
                        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                        </svg>
                    </Link>

                    <!-- Users Management -->
                    <Link
                        :href="route('admin.users.index')"
                        :class="[
                            'group flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200 text-right',
                            route().current('admin.users.*')
                                ? 'bg-blue-50 text-blue-700 border-r-4 border-blue-700'
                                : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900'
                        ]"
                    >
                        <span class="text-right flex-1">المستخدمون</span>
                        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </Link>

                    <!-- Contacts Management -->
                    <Link
                        :href="route('admin.contacts.index')"
                        :class="[
                            'group flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200 text-right',
                            route().current('admin.contacts.*')
                                ? 'bg-blue-50 text-blue-700 border-r-4 border-blue-700'
                                : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900'
                        ]"
                    >
                        <span class="text-right flex-1">جهات الاتصال</span>
                        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                        </svg>
                    </Link>

                    <!-- Client Messaging -->
                    <Link
                        :href="route('admin.client-messaging.index')"
                        :class="[
                            'group flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200 text-right',
                            route().current('admin.client-messaging.*')
                                ? 'bg-blue-50 text-blue-700 border-r-4 border-blue-700'
                                : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900'
                        ]"
                    >
                        <span class="text-right flex-1">مراسلة العملاء</span>
                        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                        </svg>
                    </Link>


                    <!-- Smart Cards Section -->
                    <div class="pt-4">
                        <h3 class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">
                            البطاقات الذكية
                        </h3>
                        <div class="mt-2 space-y-1">
                            <Link
                                :href="route('admin.templates.index')"
                                :class="[
                                    'group flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200 text-right',
                                    route().current('admin.templates.*')
                                        ? 'bg-blue-50 text-blue-700 border-r-4 border-blue-700'
                                        : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900'
                                ]"
                            >
                                <span class="text-right flex-1">القوالب</span>
                                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                                </svg>
                            </Link>
                            <Link
                                :href="route('admin.categories.index')"
                                :class="[
                                    'group flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200 text-right',
                                    route().current('admin.categories.*')
                                        ? 'bg-blue-50 text-blue-700 border-r-4 border-blue-700'
                                        : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900'
                                ]"
                            >
                                <span class="text-right flex-1">الفئات</span>
                                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                            </Link>
                            <Link
                                v-if="$page.props.auth.user.role === 'super_admin' || $page.props.auth.user.role === 'admin'"
                                :href="route('admin.client-templates.index')"
                                :class="[
                                    'group flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200 text-right',
                                    route().current('admin.client-templates.*')
                                        ? 'bg-blue-50 text-blue-700 border-r-4 border-blue-700'
                                        : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900'
                                ]"
                            >
                                <span class="text-right flex-1">بطاقات الحرفاء</span>
                                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                                </svg>
                            </Link>
                            <Link
                                v-if="$page.props.auth.user.role === 'super_admin' || $page.props.auth.user.role === 'admin'"
                                :href="route('admin.template-sales.index')"
                                :class="[
                                    'group flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200 text-right',
                                    route().current('admin.template-sales.*')
                                        ? 'bg-blue-50 text-blue-700 border-r-4 border-blue-700'
                                        : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900'
                                ]"
                            >
                                <span class="text-right flex-1">القوالب المباعة</span>
                                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 1H5C3.89 1 3 1.89 3 3V19C3 20.1 3.9 21 5 21H11V19H5V3H13V9H21ZM14 15.5C14 14.1 15.1 13 16.5 13S19 14.1 19 15.5V16H20V22H13V16H14V15.5ZM16.5 14.5C15.7 14.5 15 15.2 15 16H18C18 15.2 17.3 14.5 16.5 14.5Z"/>
                                </svg>
                            </Link>

                        </div>
                    </div>

                    <!-- Subscriptions Section -->
                    <div v-if="$page.props.auth.user.role === 'super_admin' || $page.props.auth.user.role === 'admin'" class="pt-4">
                        <h3 class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">
                            الاشتراكات
                        </h3>
                        <div class="mt-2 space-y-1">
                            <!-- Subscription Management (Super Admin Only) -->
                            <Link
                                v-if="$page.props.auth.user.role === 'super_admin'"
                                :href="route('admin.subscriptions.index')"
                                :class="[
                                    'group flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200 text-right',
                                    route().current('admin.subscriptions.*')
                                        ? 'bg-blue-50 text-blue-700 border-r-4 border-blue-700'
                                        : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900'
                                ]"
                            >
                                <span class="text-right flex-1">إدارة الاشتراكات</span>
                                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                </svg>
                            </Link>

                            <!-- Client Subscriptions (Admin and Super Admin) -->
                            <Link
                                :href="route('admin.client-subscriptions.index')"
                                :class="[
                                    'group flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200 text-right',
                                    route().current('admin.client-subscriptions.*')
                                        ? 'bg-blue-50 text-blue-700 border-r-4 border-blue-700'
                                        : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900'
                                ]"
                            >
                                <span class="text-right flex-1">اشتراكات العملاء</span>
                                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </Link>
                        </div>
                    </div>

                    <!-- Settings Section -->
                    <div class="pt-4">
                        <h3 class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">
                            الإعدادات
                        </h3>
                        <div class="mt-2 space-y-1">
                            <Link
                                :href="route('profile.edit')"
                                class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200 text-gray-700 hover:bg-gray-50 hover:text-gray-900 text-right"
                            >
                                <span class="text-right flex-1">الملف الشخصي</span>
                                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                </svg>
                            </Link>

                            <!-- Terms of Service Management -->
                            <Link
                                :href="route('admin.terms-of-service.index')"
                                :class="[
                                    'group flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200 text-right',
                                    route().current('admin.terms-of-service.*')
                                        ? 'bg-blue-50 text-blue-700 border-r-4 border-blue-700'
                                        : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900'
                                ]"
                            >
                                <span class="text-right flex-1">اتفاقية الاستخدام</span>
                                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
                                </svg>
                            </Link>
                        </div>
                    </div>
                </div>
            </nav>
        </div>

        <!-- Mobile sidebar overlay -->
        <div
            v-if="sidebarOpen"
            @click="sidebarOpen = false"
            class="fixed inset-0 z-40 bg-gray-600 bg-opacity-75 lg:hidden"
        ></div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { Link } from '@inertiajs/vue3';
import { useTranslations } from '@/Composables/useTranslations';

const { __, isRTL, direction } = useTranslations();
const sidebarOpen = ref(false);
</script>