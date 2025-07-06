<template>
    <div :dir="direction" class="min-h-screen bg-gray-100">
        <div :class="isRTL ? 'flex-row-reverse' : 'flex'" class="flex">
            <!-- Sidebar -->
            <div :class="[
                'fixed inset-y-0 z-50 w-64 bg-white shadow-lg transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0',
                isRTL ? 'right-0' : 'left-0',
                sidebarOpen ? 'translate-x-0' : (isRTL ? 'translate-x-full' : '-translate-x-full')
            ]">
                <!-- User Info at Top -->
                <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-4">
                    <div :class="isRTL ? 'flex-row-reverse' : 'flex'" class="flex items-center justify-between">
                        <div :class="isRTL ? 'flex-row-reverse' : 'flex'" class="flex items-center">
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
                            <div :class="isRTL ? 'mr-3' : 'ml-3'" class="flex-1 min-w-0">
                                <p class="text-white font-medium text-sm truncate">{{ $page.props.auth.user.name }}</p>
                                <p class="text-blue-100 text-xs truncate">{{ $page.props.auth.user.email }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <Dropdown :align="isRTL ? 'left' : 'right'" width="48">
                                <template #trigger>
                                    <button class="text-white hover:text-blue-200 transition-colors">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                        </svg>
                                    </button>
                                </template>
                                <template #content>
                                    <DropdownLink :href="route('profile.edit')">{{ __('common.profile') }}</DropdownLink>
                                    <DropdownLink :href="route('logout')" method="post" as="button">
                                        {{ __('auth.logout') }}
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
                <div class="bg-gradient-to-r from-blue-500 to-purple-500 px-6 py-3 border-b border-blue-400">
                    <div :class="isRTL ? 'flex-row-reverse' : 'flex'" class="flex items-center justify-center">
                        <ApplicationLogo class="block h-8 w-auto fill-current text-white" />
                        <span :class="isRTL ? 'mr-3' : 'ml-3'" class="text-white font-bold text-lg">Bitaqati Admin</span>
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="mt-8 px-4">
                    <div class="space-y-2">
                        <!-- Dashboard -->
                        <Link
                            :href="route('admin.dashboard')"
                            :class="[
                                'group flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200',
                                route().current('admin.dashboard') 
                                    ? (isRTL ? 'bg-blue-50 text-blue-700 border-l-4 border-blue-700' : 'bg-blue-50 text-blue-700 border-r-4 border-blue-700')
                                    : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900',
                                isRTL ? 'flex-row-reverse' : ''
                            ]"
                        >
                            <svg :class="isRTL ? 'ml-3' : 'mr-3'" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                            </svg>
                            {{ __('common.dashboard') }}
                        </Link>

                        <!-- Users Management -->
                        <Link
                            :href="route('admin.users.index')"
                            :class="[
                                'group flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200',
                                route().current('admin.users.*') 
                                    ? (isRTL ? 'bg-blue-50 text-blue-700 border-l-4 border-blue-700' : 'bg-blue-50 text-blue-700 border-r-4 border-blue-700')
                                    : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900',
                                isRTL ? 'flex-row-reverse' : ''
                            ]"
                        >
                            <svg :class="isRTL ? 'ml-3' : 'mr-3'" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            {{ __('common.users') }}
                        </Link>

                        <!-- Contacts Management -->
                        <Link
                            :href="route('admin.contacts.index')"
                            :class="[
                                'group flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200',
                                route().current('admin.contacts.*') 
                                    ? (isRTL ? 'bg-blue-50 text-blue-700 border-l-4 border-blue-700' : 'bg-blue-50 text-blue-700 border-r-4 border-blue-700')
                                    : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900',
                                isRTL ? 'flex-row-reverse' : ''
                            ]"
                        >
                            <svg :class="isRTL ? 'ml-3' : 'mr-3'" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                            </svg>
                            {{ __('common.contacts') }}
                        </Link>

                        <!-- Smart Cards Section -->
                        <div class="pt-4">
                            <h3 class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                {{ __('common.smart_cards') }}
                            </h3>
                            <div class="mt-2 space-y-1">
                                <a href="#" :class="[
                                    'group flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200 text-gray-700 hover:bg-gray-50 hover:text-gray-900',
                                    isRTL ? 'flex-row-reverse' : ''
                                ]">
                                    <svg :class="isRTL ? 'ml-3' : 'mr-3'" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                                    </svg>
                                    {{ __('common.templates') }}
                                </a>
                                <a href="#" :class="[
                                    'group flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200 text-gray-700 hover:bg-gray-50 hover:text-gray-900',
                                    isRTL ? 'flex-row-reverse' : ''
                                ]">
                                    <svg :class="isRTL ? 'ml-3' : 'mr-3'" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                    {{ __('common.design_tools') }}
                                </a>
                            </div>
                        </div>

                        <!-- Settings Section -->
                        <div class="pt-4">
                            <h3 class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                {{ __('common.settings') }}
                            </h3>
                            <div class="mt-2 space-y-1">
                                <Link
                                    :href="route('profile.edit')"
                                    :class="[
                                        'group flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200 text-gray-700 hover:bg-gray-50 hover:text-gray-900',
                                        isRTL ? 'flex-row-reverse' : ''
                                    ]"
                                >
                                    <svg :class="isRTL ? 'ml-3' : 'mr-3'" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                    </svg>
                                    {{ __('common.profile') }}
                                </Link>
                            </div>
                        </div>
                    </div>
                </nav>
            </div>

            <!-- Main Content -->
            <div :class="[
                'flex-1 w-full min-h-screen bg-gray-50',
                isRTL ? 'lg:mr-64' : 'lg:ml-64'
            ]">
                <!-- Top Bar -->
                <div class="bg-white shadow-sm border-b border-gray-200">
                    <div class="px-4 sm:px-6 lg:px-8">
                        <div class="flex justify-between h-16">
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
                                <nav :class="isRTL ? 'mr-4' : 'ml-4'" class="hidden lg:flex">
                                    <ol :class="isRTL ? 'flex-row-reverse' : 'flex'" class="flex items-center space-x-2">
                                        <li>
                                            <Link :href="route('admin.dashboard')" class="text-gray-500 hover:text-gray-700">
                                                {{ __('common.admin') }}
                                            </Link>
                                        </li>
                                        <li v-if="$slots.breadcrumb">
                                            <svg :class="isRTL ? 'rotate-180' : ''" class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
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
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM10.5 3.5L6 8v13h5V8l4.5-4.5z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Page Header -->
                <header v-if="$slots.header" class="bg-white shadow-sm">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        <slot name="header" />
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 min-h-screen">
                    <div class="w-full max-w-none px-4 sm:px-6 lg:px-8">
                        <slot />
                    </div>
                </main>
            </div>
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