<template>
    <Head :title="terms.title" />

    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <header class="bg-white shadow">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex justify-between items-center">
                    <button
                        @click="goBack"
                        class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        العودة
                    </button>
                    
                    <div class="text-right">
                        <h1 class="text-lg font-semibold text-gray-900">{{ terms.title }}</h1>
                        <p class="text-sm text-gray-600">الإصدار {{ terms.version }}</p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Content -->
        <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-blue-50 border-b border-blue-200">
                    <div class="flex justify-between items-center">
                        <div class="text-right flex-1">
                            <h2 class="text-xl font-bold text-blue-900">{{ terms.title }}</h2>
                            <p class="text-blue-700 text-sm mt-1">
                                الإصدار {{ terms.version }}
                                <span v-if="terms.effective_date" class="mr-4">
                                    ساري المفعول من: {{ formatDate(terms.effective_date) }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <!-- Terms Content -->
                    <div 
                        v-html="terms.content" 
                        class="prose prose-lg max-w-none terms-content"
                        dir="rtl"
                    ></div>
                </div>

                <!-- Footer -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <div class="text-center text-sm text-gray-600">
                        <p>آخر تحديث: {{ formatDate(terms.updated_at) }}</p>
                        <p class="mt-2">
                            لأي استفسارات حول هذه الاتفاقية، يرجى 
                            <a href="/contact" class="text-blue-600 hover:text-blue-800 underline">التواصل معنا</a>
                        </p>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'

const props = defineProps({
    terms: Object,
})

// Methods
const formatDate = (date) => {
    if (!date) return ''
    return new Date(date).toLocaleDateString('ar-SA', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
}

const goBack = () => {
    window.history.back()
}
</script>

<style scoped>
/* Custom styles for terms content */
:deep(.terms-content) {
    direction: rtl;
    text-align: right;
}

:deep(.terms-content h1) {
    font-size: 2.5em;
    font-weight: bold;
    color: #1f2937;
    margin-bottom: 30px;
    text-align: center;
    border-bottom: 3px solid #3b82f6;
    padding-bottom: 15px;
}

:deep(.terms-content h2) {
    font-size: 1.8em;
    font-weight: bold;
    color: #1f2937;
    margin: 30px 0 20px 0;
    border-right: 4px solid #3b82f6;
    padding-right: 15px;
}

:deep(.terms-content h3) {
    font-size: 1.3em;
    font-weight: bold;
    color: #374151;
    margin: 20px 0 10px 0;
}

:deep(.terms-content p) {
    margin-bottom: 15px;
    text-align: justify;
    line-height: 1.8;
}

:deep(.terms-content ul) {
    margin: 15px 0;
    padding-right: 25px;
}

:deep(.terms-content li) {
    margin-bottom: 8px;
    line-height: 1.6;
}

:deep(.terms-content code) {
    background-color: #f3f4f6;
    padding: 2px 4px;
    border-radius: 4px;
    font-size: 0.9em;
}

:deep(.terms-content blockquote) {
    border-right: 4px solid #e5e7eb;
    padding-right: 15px;
    margin: 20px 0;
    color: #6b7280;
    font-style: italic;
}

/* Print styles */
@media print {
    .no-print {
        display: none !important;
    }
    
    .terms-content {
        font-size: 12pt;
        line-height: 1.6;
    }
}
</style>
