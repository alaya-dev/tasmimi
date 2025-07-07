<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useTranslations } from '@/Composables/useTranslations';

const props = defineProps({
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

const form = useForm({
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <div :dir="direction" class="min-h-screen bg-gradient-to-br from-blue-900 via-purple-900 to-indigo-900">
        <Head :title="__('auth.register')" />
        
        <!-- Language Switcher -->
        <div class="absolute top-4 z-10" :class="isRTL ? 'left-4' : 'right-4'">
            <LanguageSwitcher :current-locale="props.locale" />
        </div>

        <div class="min-h-screen flex">
            <!-- Left Side - Branding -->
            <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-600/20 to-purple-600/20"></div>
                <div class="relative z-10 flex flex-col justify-center px-12 text-white">
                    <div class="mb-8">
                        <div class="flex items-center mb-6">
                            <div class="w-12 h-12 bg-gradient-to-r from-blue-400 to-purple-500 rounded-lg flex items-center justify-center mr-4">
                                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2zm0 2v12h16V6H4zm2 2h12v2H6V8zm0 4h8v2H6v-2z"/>
                                </svg>
                            </div>
                            <h1 class="text-3xl font-bold">{{ __('auth.smart_card_design') }}</h1>
                        </div>
                        <p class="text-xl text-blue-100 leading-relaxed">
                            {{ __('auth.design_description') }}
                        </p>
                    </div>
                    
                    <!-- Animated Circuit Pattern -->
                    <div class="absolute inset-0 opacity-10">
                        <svg class="w-full h-full" viewBox="0 0 400 400" fill="none">
                            <defs>
                                <pattern id="circuit" x="0" y="0" width="40" height="40" patternUnits="userSpaceOnUse">
                                    <path d="M0 20h40M20 0v40" stroke="currentColor" stroke-width="0.5"/>
                                    <circle cx="20" cy="20" r="2" fill="currentColor"/>
                                </pattern>
                            </defs>
                            <rect width="100%" height="100%" fill="url(#circuit)"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Right Side - Register Form -->
            <div class="w-full lg:w-1/2 flex items-center justify-center p-8">
                <div class="w-full max-w-md">
                    <!-- Mobile Branding -->
                    <div class="lg:hidden text-center mb-8">
                        <div class="w-16 h-16 bg-gradient-to-r from-blue-400 to-purple-500 rounded-xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2zm0 2v12h16V6H4zm2 2h12v2H6V8zm0 4h8v2H6v-2z"/>
                            </svg>
                        </div>
                        <h1 class="text-2xl font-bold text-white mb-2">{{ __('auth.smart_card_design') }}</h1>
                    </div>

                    <!-- Register Card -->
                    <div class="bg-white/10 backdrop-blur-lg rounded-2xl shadow-2xl border border-white/20 p-8">
                        <div class="text-center mb-8">
                            <h2 class="text-3xl font-bold text-white mb-2">{{ __('auth.join_us') }}</h2>
                            <p class="text-blue-100">{{ __('auth.get_started') }}</p>
                        </div>

                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <label for="email" class="block text-sm font-medium text-blue-100 mb-2">
                                    {{ __('auth.email') }}
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 flex items-center pointer-events-none" :class="isRTL ? 'right-0 pr-3' : 'left-0 pl-3'">
                                        <svg class="w-5 h-5 text-blue-300" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                                        </svg>
                                    </div>
                                    <input
                                        id="email"
                                        type="email"
                                        v-model="form.email"
                                        required
                                        autofocus
                                        autocomplete="username"
                                        class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white placeholder-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all duration-200"
                                        :class="[
                                            isRTL ? 'pr-10 text-right' : 'pl-10',
                                            form.errors.email ? 'border-red-400' : ''
                                        ]"
                                        :placeholder="__('auth.email')"
                                    />
                                </div>
                                <InputError class="mt-2 text-red-300" :message="form.errors.email" />
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-medium text-blue-100 mb-2">
                                    {{ __('auth.password_field') }}
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 flex items-center pointer-events-none" :class="isRTL ? 'right-0 pr-3' : 'left-0 pl-3'">
                                        <svg class="w-5 h-5 text-blue-300" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <input
                                        id="password"
                                        type="password"
                                        v-model="form.password"
                                        required
                                        autocomplete="new-password"
                                        class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white placeholder-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all duration-200"
                                        :class="[
                                            isRTL ? 'pr-10 text-right' : 'pl-10',
                                            form.errors.password ? 'border-red-400' : ''
                                        ]"
                                        :placeholder="__('auth.password_field')"
                                    />
                                </div>
                                <InputError class="mt-2 text-red-300" :message="form.errors.password" />
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-blue-100 mb-2">
                                    {{ __('auth.confirm_password') }}
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 flex items-center pointer-events-none" :class="isRTL ? 'right-0 pr-3' : 'left-0 pl-3'">
                                        <svg class="w-5 h-5 text-blue-300" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <input
                                        id="password_confirmation"
                                        type="password"
                                        v-model="form.password_confirmation"
                                        required
                                        autocomplete="new-password"
                                        class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white placeholder-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all duration-200"
                                        :class="[
                                            isRTL ? 'pr-10 text-right' : 'pl-10',
                                            form.errors.password_confirmation ? 'border-red-400' : ''
                                        ]"
                                        :placeholder="__('auth.confirm_password')"
                                    />
                                </div>
                                <InputError class="mt-2 text-red-300" :message="form.errors.password_confirmation" />
                            </div>

                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="w-full bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 focus:ring-offset-transparent disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                            >
                                <span v-if="!form.processing">{{ __('auth.create_account') }}</span>
                                <span v-else class="flex items-center justify-center">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    {{ __('common.loading') }}
                                </span>
                            </button>

                            <div class="text-center">
                                <p class="text-blue-100">
                                    {{ __('auth.already_registered') }}
                                    <Link
                                        :href="route('login')"
                                        class="text-blue-300 hover:text-blue-100 font-medium transition-colors duration-200"
                                    >
                                        {{ __('auth.sign_in') }}
                                    </Link>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
