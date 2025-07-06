<template>
    <Head :title="__('common.dashboard') + ' - ' + __('common.admin')" />

    <AdminLayoutSidebar>
        <template #breadcrumb>
            <span class="text-gray-500">{{ __('common.dashboard') }}</span>
        </template>

        <template #header>
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('common.dashboard') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('common.admin_dashboard_description') }}</p>
            </div>
        </template>

        <div class="py-6">
            <div class="w-full">
                <!-- Page Header -->
                <div class="admin-header mb-6">
                    <h1 class="page-title">{{ __('admin.dashboard') }}</h1>
                    <p class="text-muted">{{ __('admin.dashboard_overview') }}</p>
                </div>

                <!-- Statistiques -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="stats-card">
                        <div class="stats-card-body">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div :class="isRTL ? 'mr-4' : 'ml-4'">
                                    <div class="text-sm font-medium text-gray-500">{{ __('common.users') }}</div>
                                    <div class="text-2xl font-bold text-gray-900">{{ stats.total_users }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="stats-card">
                        <div class="stats-card-body">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div :class="isRTL ? 'mr-4' : 'ml-4'">
                                    <div class="text-sm font-medium text-gray-500">{{ __('common.admin') }}</div>
                                    <div class="text-2xl font-bold text-gray-900">{{ stats.total_admins }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="stats-card">
                        <div class="stats-card-body">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div :class="isRTL ? 'mr-4' : 'ml-4'">
                                    <div class="text-sm font-medium text-gray-500">{{ __('common.user') }}</div>
                                    <div class="text-2xl font-bold text-gray-900">{{ stats.total_clients }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Utilisateurs récents -->
                <div class="admin-content">
                    <div class="admin-body">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="section-title">{{ __('common.recent_projects') }}</h3>
                            <Link :href="route('admin.users.index')" class="text-indigo-600 hover:text-indigo-900">
                                {{ __('common.view') }} {{ __('common.users') }}
                            </Link>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th :class="isRTL ? 'text-right' : 'text-left'" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('auth.name') }}
                                        </th>
                                        <th :class="isRTL ? 'text-right' : 'text-left'" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('auth.email') }}
                                        </th>
                                        <th :class="isRTL ? 'text-right' : 'text-left'" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('common.role') }}
                                        </th>
                                        <th :class="isRTL ? 'text-right' : 'text-left'" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('common.created_at') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="user in stats.recent_users" :key="user.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ user.name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ user.email }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="getRoleBadgeClass(user.role)" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
                                                {{ getRoleLabel(user.role) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ formatDate(user.created_at) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
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
import { useTranslations } from '@/Composables/useTranslations';

const props = defineProps({
    stats: Object,
    locale: {
        type: String,
        default: 'en'
    },
    translations: {
        type: Object,
        default: () => ({})
    }
});

const { __, isRTL, direction } = useTranslations();

const getRoleLabel = (role) => {
    const labels = {
        'super_admin': __('common.super_admin'),
        'admin': __('common.admin'),
        'client': __('common.user')
    };
    return labels[role] || role;
};

const getRoleBadgeClass = (role) => {
    const classes = {
        'super_admin': 'bg-red-100 text-red-800',
        'admin': 'bg-green-100 text-green-800',
        'client': 'bg-blue-100 text-blue-800'
    };
    return classes[role] || 'bg-gray-100 text-gray-800';
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('fr-FR');
};
</script>

