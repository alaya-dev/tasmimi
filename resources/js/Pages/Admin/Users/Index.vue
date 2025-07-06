<template>
    <Head :title="__('common.user_management')" />

    <AdminLayout>
        <template #header>
            <div :class="isRTL ? 'flex-row-reverse' : 'flex'" class="flex justify-between items-center">
                <div class="flex items-center">
                    <Link 
                        :href="route('admin.dashboard')" 
                        :class="isRTL ? 'ml-4' : 'mr-4'"
                        class="text-gray-600 hover:text-gray-900 transition-colors duration-200"
                    >
                        <svg :class="isRTL ? 'rotate-180' : ''" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </Link>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('common.user_management') }}</h2>
                </div>
                <Link :href="route('admin.users.create')" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
                    {{ __('common.add_user') }}
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Filtres -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <form @submit.prevent="search" class="flex flex-wrap gap-4">
                            <div class="flex-1 min-w-64">
                                <input
                                    v-model="form.search"
                                    type="text"
                                    :placeholder="__('common.search') + '...'"
                                    :class="isRTL ? 'text-right' : 'text-left'"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />
                            </div>
                            <div class="min-w-48">
                                <select
                                    v-model="form.role"
                                    :class="isRTL ? 'text-right' : 'text-left'"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="">{{ __('common.role') }}</option>
                                    <option v-for="role in roles" :key="role" :value="role">
                                        {{ getRoleLabel(role) }}
                                    </option>
                                </select>
                            </div>
                            <button
                                type="submit"
                                class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
                            >
                                {{ __('common.search') }}
                            </button>
                            <button
                                type="button"
                                @click="clearFilters"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded"
                            >
                                {{ __('common.clear') }}
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Liste des utilisateurs -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 px-6 py-4">
                        <h3 class="text-lg font-semibold text-white">{{ __('common.users') }}</h3>
                        <p class="text-blue-100 text-sm">{{ __('common.manage_users_description') }}</p>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th :class="isRTL ? 'text-right' : 'text-left'" class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                            </svg>
                                            {{ __('auth.name') }}
                                        </div>
                                    </th>
                                    <th :class="isRTL ? 'text-right' : 'text-left'" class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                                            </svg>
                                            {{ __('auth.email') }}
                                        </div>
                                    </th>
                                    <th :class="isRTL ? 'text-right' : 'text-left'" class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                            </svg>
                                            {{ __('common.role') }}
                                        </div>
                                    </th>
                                    <th :class="isRTL ? 'text-right' : 'text-left'" class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-7h-1V2h-2v2H8V2H6v2H5c-1.1 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z"/>
                                            </svg>
                                            {{ __('common.created_at') }}
                                        </div>
                                    </th>
                                    <th :class="isRTL ? 'text-right' : 'text-left'" class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        {{ __('common.actions') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50 transition-colors duration-200">
                                    <td :class="isRTL ? 'text-right' : 'text-left'" class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center mr-3">
                                                <span class="text-white font-semibold text-sm">{{ user.name.charAt(0).toUpperCase() }}</span>
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td :class="isRTL ? 'text-right' : 'text-left'" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ user.email }}
                                    </td>
                                    <td :class="isRTL ? 'text-right' : 'text-left'" class="px-6 py-4 whitespace-nowrap">
                                        <span :class="getRoleBadgeClass(user.role)" class="inline-flex px-3 py-1 text-xs font-semibold rounded-full">
                                            {{ getRoleLabel(user.role) }}
                                        </span>
                                    </td>
                                    <td :class="isRTL ? 'text-right' : 'text-left'" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ formatDate(user.created_at) }}
                                    </td>
                                    <td :class="isRTL ? 'text-right' : 'text-left'" class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div :class="isRTL ? 'space-x-reverse' : ''" class="flex space-x-3">
                                            <Link
                                                :href="route('admin.users.edit', user.id)"
                                                class="inline-flex items-center px-3 py-1 bg-indigo-100 text-indigo-700 rounded-md hover:bg-indigo-200 transition-colors duration-200"
                                            >
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                                                </svg>
                                                {{ __('common.edit') }}
                                            </Link>
                                            <button
                                                v-if="user.id !== $page.props.auth.user.id"
                                                @click="deleteUser(user)"
                                                class="inline-flex items-center px-3 py-1 bg-red-100 text-red-700 rounded-md hover:bg-red-200 transition-colors duration-200"
                                            >
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                                                </svg>
                                                {{ __('common.delete') }}
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 flex justify-between sm:hidden">
                                <Link
                                    v-if="users.prev_page_url"
                                    :href="users.prev_page_url"
                                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                >
                                    Précédent
                                </Link>
                                <Link
                                    v-if="users.next_page_url"
                                    :href="users.next_page_url"
                                    class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                >
                                    Suivant
                                </Link>
                            </div>
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-700">
                                        Affichage de
                                        <span class="font-medium">{{ users.from }}</span>
                                        à
                                        <span class="font-medium">{{ users.to }}</span>
                                        sur
                                        <span class="font-medium">{{ users.total }}</span>
                                        résultats
                                    </p>
                                </div>
                                <div>
                                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                        <Link
                                            v-if="users.prev_page_url"
                                            :href="users.prev_page_url"
                                            class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                                        >
                                            Précédent
                                        </Link>
                                        <Link
                                            v-if="users.next_page_url"
                                            :href="users.next_page_url"
                                            class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                                        >
                                            Suivant
                                        </Link>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useTranslations } from '@/Composables/useTranslations';

const props = defineProps({
    users: Object,
    filters: Object,
    roles: Array,
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

const form = reactive({
    search: props.filters.search || '',
    role: props.filters.role || '',
});

const search = () => {
    router.get(route('admin.users.index'), form, {
        preserveState: true,
        replace: true,
    });
};

const clearFilters = () => {
    form.search = '';
    form.role = '';
    router.get(route('admin.users.index'), {}, {
        preserveState: true,
        replace: true,
    });
};

const deleteUser = (user) => {
    if (confirm(__('common.confirm_delete') + ' ' + user.name + '?')) {
        router.delete(route('admin.users.destroy', user.id));
    }
};

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

