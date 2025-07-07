<template>
    <Head title="إدارة جهات الاتصال" />

    <AdminLayoutSidebar>
        <template #breadcrumb>
            <span class="text-gray-500">إدارة جهات الاتصال</span>
        </template>

        <template #header>
            <div class="flex items-center justify-between flex-row-reverse">
                <div class="flex items-center flex-row-reverse">
                    <Link
                        :href="route('admin.dashboard')"
                        class="ml-4 text-gray-600 hover:text-gray-900 transition-colors duration-200"
                    >
                        <svg class="w-5 h-5 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </Link>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">إدارة جهات الاتصال</h2>
                </div>
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
                                    v-model="form.status"
                                    :class="isRTL ? 'text-right' : 'text-left'"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="">{{ __('common.contact_status') }}</option>
                                    <option v-for="status in statuses" :key="status" :value="status">
                                        {{ getStatusLabel(status) }}
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

                <!-- Liste des contacts -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 px-6 py-4">
                        <h3 class="text-lg font-semibold text-white">{{ __('common.contacts') }}</h3>
                        <p class="text-blue-100 text-sm">{{ __('common.manage_contacts_description') }}</p>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th :class="[
                                        'px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider',
                                        isRTL ? 'text-right' : 'text-left'
                                    ]">
                                        <div :class="isRTL ? 'flex-row-reverse' : 'flex'" class="flex items-center">
                                            <svg :class="isRTL ? 'ml-2' : 'mr-2'" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                            </svg>
                                            {{ __('auth.name') }}
                                        </div>
                                    </th>
                                    <th :class="[
                                        'px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider',
                                        isRTL ? 'text-right' : 'text-left'
                                    ]">
                                        <div :class="isRTL ? 'flex-row-reverse' : 'flex'" class="flex items-center">
                                            <svg :class="isRTL ? 'ml-2' : 'mr-2'" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                                            </svg>
                                            {{ __('auth.email') }}
                                        </div>
                                    </th>
                                    <th :class="[
                                        'px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider',
                                        isRTL ? 'text-right' : 'text-left'
                                    ]">
                                        <div :class="isRTL ? 'flex-row-reverse' : 'flex'" class="flex items-center">
                                            <svg :class="isRTL ? 'ml-2' : 'mr-2'" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M20 2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h14l4 4V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z"/>
                                            </svg>
                                            {{ __('common.message') }}
                                        </div>
                                    </th>
                                    <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">
                                        <div class="flex items-center justify-center">
                                            <svg :class="isRTL ? 'ml-2' : 'mr-2'" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                            </svg>
                                            {{ __('common.status') }}
                                        </div>
                                    </th>
                                    <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">
                                        <div class="flex items-center justify-center">
                                            <svg :class="isRTL ? 'ml-2' : 'mr-2'" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M9 11H7v2h2v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-7h-1V2h-2v2H8V2H6v2H5c-1.1 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z"/>
                                            </svg>
                                            {{ __('common.received_at') }}
                                        </div>
                                    </th>
                                    <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">
                                        {{ __('common.actions') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="contact in contacts.data" :key="contact.id" class="hover:bg-gray-50 transition-colors duration-200">
                                    <td :class="[
                                        'px-6 py-4 whitespace-nowrap',
                                        isRTL ? 'text-right' : 'text-left'
                                    ]">
                                        <div :class="isRTL ? 'flex-row-reverse' : 'flex'" class="flex items-center">
                                            <div :class="isRTL ? 'ml-3' : 'mr-3'" class="w-10 h-10 bg-gradient-to-r from-green-500 to-blue-600 rounded-full flex items-center justify-center">
                                                <span class="text-white font-semibold text-sm">{{ contact.name.charAt(0).toUpperCase() }}</span>
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ contact.name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td :class="[
                                        'px-6 py-4 whitespace-nowrap text-sm text-gray-500',
                                        isRTL ? 'text-right' : 'text-left'
                                    ]">
                                        {{ contact.email }}
                                    </td>
                                    <td :class="[
                                        'px-6 py-4 text-sm text-gray-500',
                                        isRTL ? 'text-right' : 'text-left'
                                    ]">
                                        <div class="max-w-xs truncate">
                                            {{ contact.content }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span :class="getStatusBadgeClass(contact.status)" class="inline-flex px-3 py-1 text-xs font-semibold rounded-full">
                                            {{ getStatusLabel(contact.status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                        {{ formatDate(contact.created_at) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                        <div :class="isRTL ? 'space-x-reverse' : ''" class="flex justify-center space-x-3">
                                            <Link
                                                :href="route('admin.contacts.show', contact.id)"
                                                class="inline-flex items-center px-3 py-1 bg-indigo-100 text-indigo-700 rounded-md hover:bg-indigo-200 transition-colors duration-200"
                                            >
                                                <svg :class="isRTL ? 'ml-1' : 'mr-1'" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                                                </svg>
                                                {{ __('common.view') }}
                                            </Link>
                                            <button
                                                @click="deleteContact(contact)"
                                                class="inline-flex items-center px-3 py-1 bg-red-100 text-red-700 rounded-md hover:bg-red-200 transition-colors duration-200"
                                            >
                                                <svg :class="isRTL ? 'ml-1' : 'mr-1'" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
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
                                    v-if="contacts.prev_page_url"
                                    :href="contacts.prev_page_url"
                                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                >
                                    {{ __('common.previous') }}
                                </Link>
                                <Link
                                    v-if="contacts.next_page_url"
                                    :href="contacts.next_page_url"
                                    class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                >
                                    {{ __('common.next') }}
                                </Link>
                            </div>
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-700">
                                        {{ __('common.show') }}
                                        <span class="font-medium">{{ contacts.from }}</span>
                                        {{ __('common.to') }}
                                        <span class="font-medium">{{ contacts.to }}</span>
                                        {{ __('common.of') }}
                                        <span class="font-medium">{{ contacts.total }}</span>
                                        {{ __('common.results') }}
                                    </p>
                                </div>
                                <div>
                                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                        <Link
                                            v-if="contacts.prev_page_url"
                                            :href="contacts.prev_page_url"
                                            class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                                        >
                                            {{ __('common.previous') }}
                                        </Link>
                                        <Link
                                            v-if="contacts.next_page_url"
                                            :href="contacts.next_page_url"
                                            class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                                        >
                                            {{ __('common.next') }}
                                        </Link>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayoutSidebar>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayoutSidebar from '@/Layouts/AdminLayoutSidebar.vue';
import { useTranslations } from '@/Composables/useTranslations';

const props = defineProps({
    contacts: Object,
    filters: Object,
    statuses: Array,
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
    status: props.filters.status || '',
});

const search = () => {
    router.get(route('admin.contacts.index'), form, {
        preserveState: true,
        replace: true,
    });
};

const clearFilters = () => {
    form.search = '';
    form.status = '';
    router.get(route('admin.contacts.index'), {}, {
        preserveState: true,
        replace: true,
    });
};

const deleteContact = (contact) => {
    if (confirm(__('common.confirm_delete') + ' ' + contact.name + '?')) {
        router.delete(route('admin.contacts.destroy', contact.id));
    }
};

const getStatusLabel = (status) => {
    const labels = {
        'pending': __('common.pending'),
        'in_progress': __('common.in_progress'),
        'resolved': __('common.resolved')
    };
    return labels[status] || status;
};

const getStatusBadgeClass = (status) => {
    const classes = {
        'pending': 'bg-yellow-100 text-yellow-800',
        'in_progress': 'bg-blue-100 text-blue-800',
        'resolved': 'bg-green-100 text-green-800'
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString(isRTL.value ? 'ar-SA' : 'en-US');
};
</script>