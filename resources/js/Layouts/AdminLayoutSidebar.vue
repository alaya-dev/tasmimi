<template>
    <div dir="rtl" class="min-h-screen bg-gray-50">
        <!-- Sidebar FIXE à DROITE -->
        <div class="fixed inset-y-0 right-0 z-50 w-64 bg-white shadow-xl">
            <div class="flex flex-col h-full">
                <!-- معلومات المستخدم في الأعلى -->
                <div class="bg-gradient-to-l from-blue-600 to-purple-600 px-6 py-4">
                    <div class="flex items-center justify-between flex-row-reverse">
                        <div class="flex items-center flex-row-reverse">
                            <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                <img
                                    v-if="$page.props.auth.user.image"
                                    :src="$page.props.auth.user.image"
                                    :alt="$page.props.auth.user.name"
                                    class="w-10 h-10 rounded-full object-cover"
                                />
                                <span v-else class="text-white font-semibold text-sm">
                                    {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                                </span>
                            </div>
                            <div class="mr-3 flex-1 min-w-0">
                                <p class="text-white font-medium text-sm truncate">{{ $page.props.auth.user.name }}</p>
                                <p class="text-blue-100 text-xs truncate">{{ $page.props.auth.user.email }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2 space-x-reverse">
                            <Dropdown align="left" width="48">
                                <template #trigger>
                                    <button class="text-white hover:text-blue-200 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zM12 13a1 1 0 110-2 1 1 0 010 2zM12 20a1 1 0 110-2 1 1 0 010 2z"></path>
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
                        </div>
                    </div>
                </div>

                <!-- قسم الشعار -->
                <div class="bg-gradient-to-l from-blue-500 to-purple-500 px-6 py-3 border-b border-blue-400">
                    <div class="flex items-center justify-center flex-row-reverse">
                        <ApplicationLogo class="block h-8 w-auto fill-current text-white" />
                        <span class="mr-3 text-white font-bold text-lg">إدارة بطاقتي</span>
                    </div>
                </div>

                <!-- التنقل -->
                <nav class="mt-8 px-4 flex-1">
                    <div class="space-y-2">
                        <!-- لوحة التحكم -->
                        <Link
                            :href="route('admin.dashboard')"
                            :class="[
                                'group flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200 flex-row-reverse',
                                route().current('admin.dashboard')
                                    ? 'bg-blue-50 text-blue-700 border-l-4 border-blue-700'
                                    : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900'
                            ]"
                        >
                            <svg class="ml-3 w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                            </svg>
                            لوحة التحكم
                        </Link>

                        <!-- المستخدمين -->
                        <Link
                            :href="route('admin.users.index')"
                            :class="[
                                'group flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200 flex-row-reverse',
                                route().current('admin.users.*')
                                    ? 'bg-blue-50 text-blue-700 border-l-4 border-blue-700'
                                    : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900'
                            ]"
                        >
                            <svg class="ml-3 w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                            </svg>
                            المستخدمين
                        </Link>

                        <!-- جهات الاتصال -->
                        <Link
                            :href="route('admin.contacts.index')"
                            :class="[
                                'group flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200 flex-row-reverse',
                                route().current('admin.contacts.*')
                                    ? 'bg-blue-50 text-blue-700 border-l-4 border-blue-700'
                                    : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900'
                            ]"
                        >
                            <svg class="ml-3 w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                            </svg>
                            جهات الاتصال
                        </Link>

                        <!-- البطاقات الذكية -->
                        <div class="pt-4">
                            <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">البطاقات الذكية</p>
                            <div class="mt-2 space-y-1">
                                <a href="#" class="group flex items-center px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 hover:text-gray-900 flex-row-reverse">
                                    <svg class="ml-3 w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                                    </svg>
                                    القوالب
                                </a>
                                <a href="#" class="group flex items-center px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 hover:text-gray-900 flex-row-reverse">
                                    <svg class="ml-3 w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                    أدوات التصميم
                                </a>
                            </div>
                        </div>

                        <!-- الإعدادات -->
                        <div class="pt-4">
                            <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">الإعدادات</p>
                            <div class="mt-2 space-y-1">
                                <Link
                                    :href="route('profile.edit')"
                                    class="group flex items-center px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 hover:text-gray-900 flex-row-reverse"
                                >
                                    <svg class="ml-3 w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                    </svg>
                                    الملف الشخصي
                                </Link>
                            </div>
                        </div>
                    </div>
                </nav>
            </div>
        </div>

        <!-- المحتوى الرئيسي GLISSANT vers la GAUCHE -->
        <div class="min-h-screen transition-all duration-300 ease-in-out" style="margin-right: 256px;">
            <!-- شريط علوي بسيط -->
            <div class="bg-white shadow-sm border-b border-gray-200">
                <div class="px-6 py-4">
                    <div class="flex justify-between items-center">
                        <!-- مسار التنقل -->
                        <nav class="flex">
                            <ol class="flex items-center space-x-2 space-x-reverse">
                                <li>
                                    <Link :href="route('admin.dashboard')" class="text-gray-500 hover:text-gray-700">
                                        الإدارة
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

                        <!-- إشعارات -->
                        <div class="flex items-center space-x-4 space-x-reverse">
                            <button class="text-gray-500 hover:text-gray-700">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM10.5 3.5L6 8v13h5V8l4.5-4.5z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- محتوى الصفحة -->
            <main class="flex-1 min-h-screen">
                <div class="w-full px-6 py-6">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import ApplicationLogo from '@/Components/ApplicationLogo.vue'
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
import { useTranslations } from '@/Composables/useTranslations'

const { __, isRTL, direction } = useTranslations();
const sidebarOpen = ref(false);
</script>