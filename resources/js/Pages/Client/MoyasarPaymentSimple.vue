<template>
    <div class="min-h-screen bg-gray-50 py-12">
        <div class="max-w-md mx-auto">
            <div class="bg-white rounded-lg shadow-md p-6">
                <!-- Header -->
                <div class="text-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">إتمام الدفع</h1>
                    <p class="text-gray-600">اشتراك {{ subscription.name }}</p>
                </div>

                <!-- Subscription Details -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-600">الخطة:</span>
                        <span class="font-semibold">{{ subscription.name }}</span>
                    </div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-600">المدة:</span>
                        <span class="font-semibold">{{ subscription.duration_days }} يوم</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">السعر:</span>
                        <span class="font-bold text-lg text-green-600">{{ subscription.price }} ريال</span>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="mb-6">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center">
                        <div class="flex items-center justify-center mb-2">
                            <svg class="w-8 h-8 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                            <span class="text-blue-800 font-semibold">Moyasar</span>
                        </div>
                        <p class="text-blue-700 text-sm">
                            دفع آمن ومضمون عبر منصة Moyasar
                        </p>
                        <p class="text-blue-600 text-xs mt-1">
                            يدعم جميع البطاقات الائتمانية والمحافظ الرقمية
                        </p>
                    </div>
                </div>

                <!-- Error Messages -->
                <div v-if="$page.props.errors.payment" class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-red-700 text-sm">{{ $page.props.errors.payment }}</p>
                </div>

                <!-- Pay Button -->
                <div class="space-y-4">
                    <a
                        :href="route('client.payment.moyasar', subscription.id)"
                        :class="[
                            'w-full py-3 px-4 rounded-lg font-semibold text-white transition-all duration-200 block text-center',
                            processing
                                ? 'bg-gray-400 cursor-not-allowed'
                                : 'bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-200'
                        ]"
                        @click="processing = true"
                    >
                        <span v-if="processing" class="flex items-center justify-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            جاري التحويل إلى Moyasar...
                        </span>
                        <span v-else class="flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            ادفع {{ subscription.price }} ريال
                        </span>
                    </a>
                </div>

                <!-- Security Notice -->
                <div class="mt-6 text-center">
                    <p class="text-xs text-gray-500">
                        🔒 جميع المعاملات محمية بتشفير SSL
                    </p>
                </div>

                <!-- Back Link -->
                <div class="mt-4 text-center">
                    <Link 
                        :href="route('client.subscriptions.index')" 
                        class="text-blue-600 hover:text-blue-800 text-sm"
                    >
                        ← العودة للاشتراكات
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { router, Link } from '@inertiajs/vue3'

const props = defineProps({
    subscription: Object
})

const processing = ref(false)

// Form submission is handled by HTML form, no JavaScript needed
</script>
