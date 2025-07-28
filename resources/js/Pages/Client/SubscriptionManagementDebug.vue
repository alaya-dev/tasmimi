<template>
    <Head title="إدارة الاشتراك - Debug" />

    <div class="min-h-screen bg-gray-50 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">إدارة الاشتراك - Debug</h1>
            
            <!-- Debug Info -->
            <div class="bg-white shadow rounded-lg p-6 mb-8">
                <h2 class="text-xl font-semibold mb-4">Debug Information</h2>
                
                <div class="space-y-4">
                    <div>
                        <h3 class="font-medium">Active Subscription:</h3>
                        <pre class="bg-gray-100 p-2 rounded text-sm overflow-auto">{{ JSON.stringify(activeSubscription, null, 2) }}</pre>
                    </div>
                    
                    <div>
                        <h3 class="font-medium">Subscription History Count:</h3>
                        <p>{{ subscriptionHistory ? subscriptionHistory.length : 'null' }}</p>
                    </div>
                    
                    <div>
                        <h3 class="font-medium">Payment History Count:</h3>
                        <p>{{ paymentHistory ? paymentHistory.length : 'null' }}</p>
                    </div>
                </div>
            </div>

            <!-- Simple Active Subscription Display -->
            <div class="bg-white shadow rounded-lg p-6 mb-8">
                <h2 class="text-xl font-semibold mb-4">الاشتراك الحالي</h2>
                
                <div v-if="activeSubscription">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">اسم الخطة</label>
                            <p class="mt-1 text-lg">{{ activeSubscription.subscription?.name || 'غير محدد' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">الحالة</label>
                            <p class="mt-1 text-lg">{{ activeSubscription.status }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">تاريخ الانتهاء</label>
                            <p class="mt-1 text-lg">{{ activeSubscription.ends_at }}</p>
                        </div>
                    </div>
                </div>
                
                <div v-else class="text-gray-500">
                    لا يوجد اشتراك نشط
                </div>
            </div>

            <!-- Simple History -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4">تاريخ الاشتراكات</h2>
                
                <div v-if="subscriptionHistory && subscriptionHistory.length > 0">
                    <div class="space-y-2">
                        <div v-for="sub in subscriptionHistory" :key="sub.id" class="border-b pb-2">
                            <p><strong>ID:</strong> {{ sub.id }}</p>
                            <p><strong>Status:</strong> {{ sub.status }}</p>
                            <p><strong>Plan:</strong> {{ sub.subscription?.name || 'N/A' }}</p>
                        </div>
                    </div>
                </div>
                
                <div v-else class="text-gray-500">
                    لا يوجد تاريخ اشتراكات
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    activeSubscription: {
        type: Object,
        default: null
    },
    subscriptionHistory: {
        type: Array,
        default: () => []
    },
    paymentHistory: {
        type: Array,
        default: () => []
    }
});

// Log props for debugging
console.log('Props received:', props);
</script>
