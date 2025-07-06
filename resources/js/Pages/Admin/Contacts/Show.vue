<template>
    <Head :title="__('common.contact_details')" />

    <AdminLayoutSidebar>
        <template #breadcrumb>
            <Link :href="route('admin.contacts.index')" class="text-gray-500 hover:text-gray-700">
                {{ __('common.contacts') }}
            </Link>
            <svg :class="isRTL ? 'rotate-180' : ''" class="w-5 h-5 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
            </svg>
            <span class="text-gray-500">{{ __('common.contact_details') }}</span>
        </template>

        <template #header>
            <div :class="isRTL ? 'flex-row-reverse' : 'flex'" class="flex items-center justify-between">
                <div class="flex items-center">
                    <Link 
                        :href="route('admin.contacts.index')" 
                        :class="isRTL ? 'ml-4' : 'mr-4'"
                        class="text-gray-600 hover:text-gray-900 transition-colors duration-200"
                    >
                        <svg :class="isRTL ? 'rotate-180' : ''" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </Link>
                    <div>
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('common.contact_details') }}</h2>
                        <p class="mt-1 text-sm text-gray-600">{{ __('common.view_contact_details_description') }}</p>
                    </div>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <!-- Contact Information Card -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center mr-4">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-white">{{ contact.name }}</h3>
                                    <p class="text-blue-100">{{ contact.email }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span :class="getStatusBadgeClass(contact.status)" class="inline-flex px-3 py-1 text-sm font-semibold rounded-full">
                                    {{ getStatusLabel(contact.status) }}
                                </span>
                                <p class="text-blue-100 text-sm mt-1">{{ formatDate(contact.created_at) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <!-- Message Content -->
                        <div class="mb-6">
                            <h4 class="text-lg font-semibold text-gray-900 mb-3">{{ __('common.message') }}</h4>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="text-gray-700 leading-relaxed whitespace-pre-wrap">{{ contact.content }}</p>
                            </div>
                        </div>

                        <!-- Status Update Section -->
                        <div class="border-t pt-6">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">{{ __('common.contact_status') }}</h4>
                            <form @submit.prevent="updateStatus" class="flex items-center space-x-4">
                                <div class="flex-1">
                                    <select
                                        v-model="statusForm.status"
                                        :class="isRTL ? 'text-right' : 'text-left'"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value="pending">{{ __('common.pending') }}</option>
                                        <option value="in_progress">{{ __('common.in_progress') }}</option>
                                        <option value="resolved">{{ __('common.resolved') }}</option>
                                    </select>
                                </div>
                                <button
                                    type="submit"
                                    :disabled="statusForm.processing"
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-lg transition-colors duration-200 disabled:opacity-50"
                                >
                                    <span v-if="!statusForm.processing">{{ __('common.update') }}</span>
                                    <span v-else class="flex items-center">
                                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        {{ __('common.loading') }}
                                    </span>
                                </button>
                            </form>
                        </div>

                        <!-- Actions -->
                        <div class="border-t pt-6 mt-6">
                            <div class="flex justify-between items-center">
                                <div class="text-sm text-gray-500">
                                    {{ __('common.received_at') }}: {{ formatDate(contact.created_at) }}
                                </div>
                                <button
                                    @click="deleteContact"
                                    class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-200"
                                >
                                    {{ __('common.delete') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayoutSidebar>
</template>

<script setup>
import { reactive } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AdminLayoutSidebar from '@/Layouts/AdminLayoutSidebar.vue';
import { useTranslations } from '@/Composables/useTranslations';

const props = defineProps({
    contact: Object,
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

const statusForm = useForm({
    status: props.contact.status,
});

const updateStatus = () => {
    statusForm.patch(route('admin.contacts.update', props.contact.id), {
        preserveScroll: true,
        onSuccess: () => {
            // Status updated successfully
        },
    });
};

const deleteContact = () => {
    if (confirm(__('common.confirm_delete') + ' ' + props.contact.name + '?')) {
        router.delete(route('admin.contacts.destroy', props.contact.id));
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
    return new Date(dateString).toLocaleDateString(isRTL.value ? 'ar-SA' : 'en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>