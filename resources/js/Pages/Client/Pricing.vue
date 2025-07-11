<template>
    <Head title="الأسعار - سامقة" />

    <ClientLayout>
        <div class="py-12 bg-gray-50 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Page Header -->
                <div class="text-center mb-16">
                    <h1 class="text-4xl font-bold text-gray-900 mb-4">اختر الخطة التي تناسب احتياجاتك</h1>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                        اختر الخطة التي تناسب احتياجاتك وابدأ في إنشاء تصاميم احترافية
                    </p>
                </div>



                <!-- Pricing Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
                    <!-- Dynamic Subscription Plans -->
                    <div
                        v-for="(subscription, index) in subscriptions"
                        :key="subscription.id"
                        :class="[
                            'bg-white rounded-2xl shadow-lg border p-8 relative',
                            index === 0 ? 'border-2 border-blue-500 transform scale-105 shadow-xl' : 'border-gray-200'
                        ]"
                    >
                        <!-- Popular Badge for first plan -->
                        <div v-if="index === 0" class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                            <span class="bg-blue-500 text-white px-4 py-2 rounded-full text-sm font-semibold">
                                الأكثر شعبية
                            </span>
                        </div>

                        <div class="text-center mb-8">
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ subscription.name }}</h3>
                            <div class="text-4xl font-bold mb-2" :class="index === 0 ? 'text-blue-600' : 'text-purple-600'">
                                {{ subscription.price }} ريال
                                <span class="text-lg text-gray-600">
                                    /{{ subscription.type === 'weekly' ? 'أسبوع' : subscription.type === 'monthly' ? 'شهر' : 'سنة' }}
                                </span>
                            </div>
                            <p class="text-gray-600">{{ subscription.description }}</p>
                        </div>

                        <ul class="space-y-4 mb-8">
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span>{{ subscription.type === 'weekly' ? '10' : subscription.type === 'monthly' ? '50' : 'غير محدود' }} تصميم {{ subscription.type === 'weekly' ? 'أسبوعياً' : subscription.type === 'monthly' ? 'شهرياً' : '' }}</span>
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
                                <span>دعم ذو أولوية</span>
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span>بدون علامة مائية</span>
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span>تصدير بجودة عالية</span>
                            </li>
                        </ul>

                        <button
                            @click="subscribeToPlan(subscription.type)"
                            :disabled="currentPlan === subscription.type"
                            :class="[
                                'w-full text-white py-3 rounded-lg font-semibold transition-colors',
                                currentPlan === subscription.type ? 'bg-gray-400 cursor-not-allowed' :
                                index === 0 ? 'bg-blue-600 hover:bg-blue-700' : 'bg-purple-600 hover:bg-purple-700'
                            ]"
                        >
                            {{ currentPlan === subscription.type ? 'الخطة الحالية' : 'ترقية الخطة' }}
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

// Props
const props = defineProps({
    subscriptions: {
        type: Array,
        default: () => []
    },
    currentPlan: {
        type: String,
        default: 'free'
    }
});

// State
const currentPlan = ref(props.currentPlan);

// Methods
const subscribeToPlan = (plan) => {
    router.post(route('client.subscription.create'), {
        plan: plan
    });
};
</script>
