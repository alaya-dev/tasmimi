<template>
    <Head title="الاشتراكات " />

    <ClientLayout>
        <div class="py-12" dir="rtl">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="text-center mb-12">
                    <h1 class="text-4xl font-bold text-gray-900 mb-4">اختر خطة الاشتراك المناسبة لك</h1>
                    <p class="text-xl text-gray-600">احصل على وصول كامل لجميع القوالب والميزات</p>
                </div>

                <!-- Current Subscription Alert -->
                <div v-if="activeSubscription" class="mb-8 bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-blue-600 ml-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <p class="text-blue-800 font-medium">
                                لديك اشتراك نشط: {{ currentPlan?.name }}
                            </p>
                            <p class="text-blue-600 text-sm">
                                ينتهي في {{ formatDate(activeSubscription.ends_at) }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Subscription Plans -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div 
                        v-for="subscription in subscriptions" 
                        :key="subscription.id"
                        class="relative bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden transition-transform hover:scale-105"
                        :class="{ 'ring-2 ring-blue-500 ring-opacity-50': subscription.is_popular }"
                    >
                        <!-- Popular Badge -->
                        <div v-if="subscription.is_popular" class="absolute top-0 right-0 bg-gradient-to-r from-orange-400 to-pink-500 text-white px-4 py-1 text-sm font-medium rounded-bl-lg">
                            الأكثر شعبية
                        </div>

                        <!-- Plan Header -->
                        <div class="p-6 text-center" :style="subscription.color ? { backgroundColor: subscription.color + '20' } : {}">
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ subscription.name }}</h3>
                            <div class="text-4xl font-bold text-gray-900 mb-1">
                                {{ subscription.price }} 
                                <span class="text-lg font-normal text-gray-600">ريال</span>
                            </div>
                            <p class="text-gray-600">{{ getDurationText(subscription) }}</p>
                        </div>

                        <!-- Features -->
                        <div class="px-6 pb-6">
                            <p class="text-gray-600 mb-4">{{ subscription.description }}</p>
                            
                            <ul v-if="subscription.features && subscription.features.length > 0" class="space-y-2 mb-6">
                                <li v-for="feature in subscription.features" :key="feature" class="flex items-center">
                                    <svg class="w-5 h-5 text-green-500 ml-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-gray-700">{{ feature }}</span>
                                </li>
                            </ul>

                            <!-- Action Button -->
                            <button
                                @click="selectPlan(subscription)"
                                :disabled="isCurrentPlan(subscription)"
                                class="w-full py-3 px-4 rounded-lg font-medium transition-colors"
                                :class="isCurrentPlan(subscription) 
                                    ? 'bg-gray-100 text-gray-500 cursor-not-allowed' 
                                    : subscription.is_popular 
                                        ? 'bg-gradient-to-r from-blue-600 to-purple-600 text-white hover:from-blue-700 hover:to-purple-700' 
                                        : 'bg-blue-600 text-white hover:bg-blue-700'"
                            >
                                <span v-if="isCurrentPlan(subscription)">الخطة الحالية</span>
                                <span v-else-if="activeSubscription">تغيير إلى هذه الخطة</span>
                                <span v-else>اختيار هذه الخطة</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- No Subscriptions -->
                <div v-if="subscriptions.length === 0" class="text-center py-12">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد خطط اشتراك متاحة حالياً</h3>
                    <p class="text-gray-600">يرجى المحاولة مرة أخرى لاحقاً</p>
                </div>
            </div>
        </div>
    </ClientLayout>
</template>

<script setup>
import { Head, router } from '@inertiajs/vue3';
import ClientLayout from '@/Layouts/ClientLayout.vue';

const props = defineProps({
    subscriptions: {
        type: Array,
        default: () => []
    },
    activeSubscription: {
        type: Object,
        default: null
    },
    currentPlan: {
        type: Object,
        default: null
    }
});

const getDurationText = (subscription) => {
    if (subscription.duration_days) {
        if (subscription.duration_days == 7) {
            return 'أسبوعي';
        } else if (subscription.duration_days == 30) {
            return 'شهري';
        } else if (subscription.duration_days == 90) {
            return 'ربع سنوي';
        } else if (subscription.duration_days == 365) {
            return 'سنوي';
        } else {
            return subscription.duration_days + ' يوم';
        }
    }
    
    const types = {
        'weekly': 'أسبوعي',
        'monthly': 'شهري', 
        'annual': 'سنوي'
    };
    
    return types[subscription.type] || subscription.type;
};

const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-GB', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
    }).split('/').reverse().join('/'); // Format: YYYY/MM/DD
};

const isCurrentPlan = (subscription) => {
    return props.activeSubscription && props.currentPlan && props.currentPlan.id === subscription.id;
};

const selectPlan = (subscription) => {
    if (isCurrentPlan(subscription)) {
        return;
    }
    
    router.visit(route('client.payment.show', subscription.id));
};
</script>
