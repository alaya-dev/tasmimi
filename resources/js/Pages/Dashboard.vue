<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { useTranslations } from '@/Composables/useTranslations';

const { __, direction } = useTranslations();
</script>

<template>
    <Head :title="__('common.dashboard')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('common.dashboard') }}</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">{{ __('common.welcome') }}!</div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
