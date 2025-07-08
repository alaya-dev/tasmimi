<template>
    <Head :title="__('client.pricing')" />

    <ClientLayout>
        <div class="py-12 bg-gray-50 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Page Header -->
                <div class="text-center mb-16">
                    <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ __('client.choose_plan') }}</h1>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                        اختر الخطة التي تناسب احتياجاتك وابدأ في إنشاء تصاميم احترافية
                    </p>
                </div>

                <!-- Billing Toggle -->
                <div class="flex items-center justify-center mb-12">
                    <div class="bg-white rounded-lg p-1 shadow-sm border border-gray-200">
                        <div class="flex items-center">
                            <button 
                                @click="billingPeriod = 'monthly'"
                                :class="billingPeriod === 'monthly' ? 'bg-blue-600 text-white' : 'text-gray-700'"
                                class="px-6 py-2 rounded-md font-medium transition-colors"
                            >
                                {{ __('client.per_month') }}
                            </button>
                            <button 
                                @click="billingPeriod = 'yearly'"
                                :class="billingPeriod === 'yearly' ? 'bg-blue-600 text-white' : 'text-gray-700'"
                                class="px-6 py-2 rounded-md font-medium transition-colors relative"
                            >
                                {{ __('client.per_year') }}
                                <span class="absolute -top-2 -right-2 bg-green-500 text-white text-xs px-2 py-1 rounded-full">
                                    وفر 20%
                                </span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Pricing Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
                    <!-- Free Plan -->
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-8 relative">
                        <div class="text-center mb-8">
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ __('client.free_plan') }}</h3>
                            <div class="text-4xl font-bold text-gray-900 mb-2">
                                مجاني
                            </div>
                            <p class="text-gray-600">للمبتدئين والاستخدام الشخصي</p>
                        </div>

                        <ul class="space-y-4 mb-8">
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span>5 تصاميم شهرياً</span>
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span>{{ __('client.limited_templates') }}</span>
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span>{{ __('client.basic_support') }}</span>
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-red-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                                <span>{{ __('client.watermark') }}</span>
                            </li>
                        </ul>

                        <button 
                            :disabled="currentPlan === 'free'"
                            :class="currentPlan === 'free' ? 'bg-gray-400 cursor-not-allowed' : 'bg-gray-900 hover:bg-gray-800'"
                            class="w-full text-white py-3 rounded-lg font-semibold transition-colors"
                        >
                            {{ currentPlan === 'free' ? __('client.current_plan') : 'ابدأ مجاناً' }}
                        </button>
                    </div>

                    <!-- Premium Plan -->
                    <div class="bg-white rounded-2xl shadow-xl border-2 border-blue-500 p-8 relative transform scale-105">
                        <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                            <span class="bg-blue-500 text-white px-4 py-2 rounded-full text-sm font-semibold">
                                الأكثر شعبية
                            </span>
                        </div>

                        <div class="text-center mb-8">
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ __('client.premium_plan') }}</h3>
                            <div class="text-4xl font-bold text-blue-600 mb-2">
                                {{ billingPeriod === 'monthly' ? '99' : '950' }} ريال
                                <span class="text-lg text-gray-600">
                                    /{{ billingPeriod === 'monthly' ? 'شهر' : 'سنة' }}
                                </span>
                            </div>
                            <p class="text-gray-600">للمحترفين والشركات الصغيرة</p>
                        </div>

                        <ul class="space-y-4 mb-8">
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span>50 تصميم شهرياً</span>
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span>جميع القوالب المتاحة</span>
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span>{{ __('client.priority_support') }}</span>
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span>{{ __('client.no_watermark') }}</span>
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span>تصدير بجودة عالية</span>
                            </li>
                        </ul>

                        <button 
                            @click="subscribeToPlan('premium')"
                            :disabled="currentPlan === 'premium'"
                            :class="currentPlan === 'premium' ? 'bg-gray-400 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700'"
                            class="w-full text-white py-3 rounded-lg font-semibold transition-colors"
                        >
                            {{ currentPlan === 'premium' ? __('client.current_plan') : __('client.upgrade_plan') }}
                        </button>
                    </div>

                    <!-- Unlimited Plan -->
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-8 relative">
                        <div class="text-center mb-8">
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ __('client.unlimited_plan') }}</h3>
                            <div class="text-4xl font-bold text-purple-600 mb-2">
                                {{ billingPeriod === 'monthly' ? '199' : '1900' }} ريال
                                <span class="text-lg text-gray-600">
                                    /{{ billingPeriod === 'monthly' ? 'شهر' : 'سنة' }}
                                </span>
                            </div>
                            <p class="text-gray-600">للشركات الكبيرة والوكالات</p>
                        </div>

                        <ul class="space-y-4 mb-8">
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span>تصاميم غير محدودة</span>
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span>جميع القوالب + قوالب حصرية</span>
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span>دعم مخصص 24/7</span>
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span>API للتكامل</span>
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span>تدريب مخصص</span>
                            </li>
                        </ul>

                        <button 
                            @click="subscribeToPlan('unlimited')"
                            :disabled="currentPlan === 'unlimited'"
                            :class="currentPlan === 'unlimited' ? 'bg-gray-400 cursor-not-allowed' : 'bg-purple-600 hover:bg-purple-700'"
                            class="w-full text-white py-3 rounded-lg font-semibold transition-colors"
                        >
                            {{ currentPlan === 'unlimited' ? __('client.current_plan') : __('client.upgrade_plan') }}
                        </button>
                    </div>
                </div>

                <!-- FAQ Section -->
                <div class="max-w-4xl mx-auto">
                    <h2 class="text-3xl font-bold text-center text-gray-900 mb-12">الأسئلة الشائعة</h2>
                    
                    <div class="space-y-6">
                        <div class="bg-white rounded-lg shadow-sm p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">هل يمكنني تغيير خطتي في أي وقت؟</h3>
                            <p class="text-gray-600">نعم، يمكنك ترقية أو تخفيض خطتك في أي وقت. التغييرات ستدخل حيز التنفيذ في دورة الفوترة التالية.</p>
                        </div>

                        <div class="bg-white rounded-lg shadow-sm p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">ما هي طرق الدفع المتاحة؟</h3>
                            <p class="text-gray-600">نقبل جميع بطاقات الائتمان الرئيسية، PayPal، والتحويل البنكي للخطط السنوية.</p>
                        </div>

                        <div class="bg-white rounded-lg shadow-sm p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">هل هناك فترة تجريبية مجانية؟</h3>
                            <p class="text-gray-600">نعم، نقدم فترة تجريبية مجانية لمدة 14 يوماً لجميع الخطط المدفوعة.</p>
                        </div>

                        <div class="bg-white rounded-lg shadow-sm p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">هل يمكنني إلغاء اشتراكي؟</h3>
                            <p class="text-gray-600">نعم، يمكنك إلغاء اشتراكك في أي وقت من إعدادات حسابك. ستحتفظ بالوصول حتى نهاية فترة الفوترة الحالية.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </ClientLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import ClientLayout from '@/Layouts/ClientLayout.vue';
import { useTranslations } from '@/Composables/useTranslations';

// Props
const props = defineProps({
    currentPlan: {
        type: String,
        default: 'free'
    }
});

// Composables
const { __ } = useTranslations();

// State
const billingPeriod = ref('monthly');
const currentPlan = ref(props.currentPlan);

// Methods
const subscribeToPlan = (plan) => {
    router.post(route('client.subscription.create'), {
        plan: plan,
        billing_period: billingPeriod.value
    });
};
</script>
