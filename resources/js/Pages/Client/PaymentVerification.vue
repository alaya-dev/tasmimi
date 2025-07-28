<template>
    <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                <div class="text-center">
                    <!-- Loading State -->
                    <div v-if="checking" class="space-y-4">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
                        <h2 class="text-xl font-semibold text-gray-900">جاري التحقق من الدفع...</h2>
                        <p class="text-gray-600">يرجى الانتظار بينما نتحقق من حالة دفعتك</p>
                    </div>

                    <!-- Success State -->
                    <div v-else-if="result && result.success" class="space-y-4">
                        <div class="rounded-full h-12 w-12 bg-green-100 mx-auto flex items-center justify-center">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h2 class="text-xl font-semibold text-green-900">{{ result.message }}</h2>
                        <p class="text-gray-600">سيتم توجيهك خلال {{ countdown }} ثواني...</p>
                        <div class="mt-4">
                            <a :href="result.redirectUrl" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                                الانتقال الآن
                            </a>
                        </div>
                    </div>

                    <!-- Error State -->
                    <div v-else-if="result && !result.success" class="space-y-4">
                        <div class="rounded-full h-12 w-12 bg-red-100 mx-auto flex items-center justify-center">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                        <h2 class="text-xl font-semibold text-red-900">{{ result.message }}</h2>
                        <div class="mt-4 space-x-2">
                            <button @click="checkPayment" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                                إعادة المحاولة
                            </button>
                            <a href="/client/subscriptions" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                                العودة للاشتراكات
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
    payment: Object
})

const checking = ref(true)
const result = ref(null)
const countdown = ref(3)

const checkPayment = async () => {
    checking.value = true
    result.value = null
    
    try {
        // Call our verification endpoint
        const response = await fetch(`/client/payment/verify/${props.payment.id}`)
        
        if (response.ok) {
            // If verification was successful, it will redirect
            // But we can also handle it here if needed
            window.location.reload()
        } else {
            result.value = {
                success: false,
                message: 'فشل في التحقق من الدفع'
            }
        }
    } catch (error) {
        result.value = {
            success: false,
            message: 'حدث خطأ أثناء التحقق من الدفع'
        }
    } finally {
        checking.value = false
    }
}

const startCountdown = () => {
    const timer = setInterval(() => {
        countdown.value--
        if (countdown.value <= 0) {
            clearInterval(timer)
            if (result.value && result.value.redirectUrl) {
                window.location.href = result.value.redirectUrl
            }
        }
    }, 1000)
}

onMounted(() => {
    // Start checking payment status immediately
    checkPayment()

    // Only check once - no interval to prevent multiple calls
    setTimeout(() => {
        if (result.value && result.value.success) {
            startCountdown()
        }
    }, 1000)
})
</script>
