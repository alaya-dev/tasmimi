<template>
    <Head :title="__('common.add_user')" />

    <AdminLayout>
        <template #header>
            <div :class="isRTL ? 'flex-row-reverse' : 'flex'" class="flex justify-between items-center">
                <div class="flex items-center">
                    <Link 
                        :href="route('admin.users.index')" 
                        :class="isRTL ? 'ml-4' : 'mr-4'"
                        class="text-gray-600 hover:text-gray-900 transition-colors duration-200"
                    >
                        <svg :class="isRTL ? 'rotate-180' : ''" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </Link>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('common.add_user') }}</h2>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">{{ __('auth.name') }}</label>
                                <input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    :class="[
                                        'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500',
                                        isRTL ? 'text-right' : 'text-left',
                                        form.errors.name ? 'border-red-500' : ''
                                    ]"
                                    required
                                />
                                <div v-if="form.errors.name" class="mt-2 text-sm text-red-600">
                                    {{ form.errors.name }}
                                </div>
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">{{ __('auth.email') }}</label>
                                <input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    :class="[
                                        'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500',
                                        isRTL ? 'text-right' : 'text-left',
                                        form.errors.email ? 'border-red-500' : ''
                                    ]"
                                    required
                                />
                                <div v-if="form.errors.email" class="mt-2 text-sm text-red-600">
                                    {{ form.errors.email }}
                                </div>
                            </div>

                            <div>
                                <label for="role" class="block text-sm font-medium text-gray-700">{{ __('common.role') }}</label>
                                <select
                                    id="role"
                                    v-model="form.role"
                                    :class="[
                                        'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500',
                                        isRTL ? 'text-right' : 'text-left',
                                        form.errors.role ? 'border-red-500' : ''
                                    ]"
                                    required
                                >
                                    <option value="">{{ __('common.select_role') }}</option>
                                    <option v-for="role in roles" :key="role" :value="role">
                                        {{ getRoleLabel(role) }}
                                    </option>
                                </select>
                                <div v-if="form.errors.role" class="mt-2 text-sm text-red-600">
                                    {{ form.errors.role }}
                                </div>
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700">{{ __('auth.password_field') }}</label>
                                <input
                                    id="password"
                                    v-model="form.password"
                                    type="password"
                                    :class="[
                                        'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500',
                                        isRTL ? 'text-right' : 'text-left',
                                        form.errors.password ? 'border-red-500' : ''
                                    ]"
                                    required
                                />
                                <div v-if="form.errors.password" class="mt-2 text-sm text-red-600">
                                    {{ form.errors.password }}
                                </div>
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">{{ __('auth.confirm_password') }}</label>
                                <input
                                    id="password_confirmation"
                                    v-model="form.password_confirmation"
                                    type="password"
                                    :class="[
                                        'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500',
                                        isRTL ? 'text-right' : 'text-left'
                                    ]"
                                    required
                                />
                            </div>

                            <div :class="isRTL ? 'flex-row-reverse space-x-reverse' : 'flex'" class="flex items-center justify-end space-x-4">
                                <Link :href="route('admin.users.index')" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded transition-colors duration-200">
                                    {{ __('common.cancel') }}
                                </Link>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50 transition-colors duration-200"
                                >
                                    <span v-if="form.processing">{{ __('common.loading') }}...</span>
                                    <span v-else>{{ __('common.create') }}</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useTranslations } from '@/Composables/useTranslations';

const props = defineProps({
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

const { __, isRTL } = useTranslations();

const form = useForm({
    name: '',
    email: '',
    role: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('admin.users.store'));
};

const getRoleLabel = (role) => {
    const labels = {
        'super_admin': __('common.super_admin'),
        'admin': __('common.admin'),
        'client': __('common.user')
    };
    return labels[role] || role;
};
</script>

