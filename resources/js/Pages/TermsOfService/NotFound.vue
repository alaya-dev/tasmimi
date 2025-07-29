<template>
    <Head title="اتفاقية الاستخدام غير متاحة" />

    <div class="min-h-screen bg-gray-50 flex items-center justify-center">
        <div class="max-w-md w-full">
            <div class="bg-white rounded-lg shadow-lg p-8 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                
                <h1 class="text-2xl font-bold text-gray-900 mb-4">اتفاقية الاستخدام غير متاحة</h1>
                
                <p class="text-gray-600 mb-6">
                    عذراً، اتفاقية الاستخدام غير متاحة حالياً. يرجى المحاولة لاحقاً أو التواصل مع الإدارة.
                </p>
                
                <div class="space-y-3">
                    <button
                        @click="goBack"
                        class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        العودة
                    </button>
                    
                    <a
                        href="/"
                        class="w-full inline-flex justify-center items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        العودة للرئيسية
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'

const goBack = () => {
    window.history.back()
}
</script>
