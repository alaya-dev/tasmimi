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
                    
                    
                </div>
            </div>
        </header>

        <!-- Content -->
        <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-blue-50 border-b border-blue-200">
                    <div class="flex justify-between items-center">
                        <div class="text-right flex-1">
                            <h2 class="text-center font-bold text-blue-900">{{ terms.title }}</h2>
                            
                        </div>
                        
                        <!-- PDF Download button -->
                        <div v-if="terms.file_name" class="text-left">
                            <a
                                :href="route('admin.terms-of-service.download', terms.id)"
                                target="_blank"
                                class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            >
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                                تحميل PDF
                            </a>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <!-- PDF Embed or Content -->
                    <div class="terms-content" dir="rtl">
                        <div v-if="terms.file_name" class="text-center mb-6">
                            <div class="bg-gray-50 p-6 rounded-lg border-2 border-dashed border-gray-300">
                                <svg class="mx-auto h-12 w-12 text-red-600 mb-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">{{ terms.file_name }}</h3>
                                <p class="text-gray-600 mb-4">حجم الملف: {{ formatFileSize(terms.file_size) }}</p>
                                <a
                                    :href="route('admin.terms-of-service.download', terms.id)"
                                    target="_blank"
                                    class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                >
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                    عرض وتحميل شروط الاستخدام
                                </a>
                            </div>
                        </div>
                        
                        <!-- Show extracted content if available -->
                        <div v-if="terms.extracted_content" class="prose prose-lg max-w-none mt-6">
                            <div class="whitespace-pre-wrap">{{ terms.extracted_content }}</div>
                        </div>
                        
                        <!-- Show HTML content if available -->
                        <div v-else-if="terms.content" class="prose prose-lg max-w-none mt-6" v-html="terms.content"></div>
                        
                        <!-- No content message -->
                        <div v-else-if="!terms.file_name" class="text-gray-500 text-center py-8">
                            <p>لا توجد شروط استخدام متاحة حالياً</p>
                        </div>
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

const formatDate = (date) => {
    if (!date) return ''
    return new Date(date).toLocaleDateString('fr-FR')
}

const formatFileSize = (bytes) => {
    if (!bytes) return ''
    if (bytes === 0) return '0 بايت'
    
    const k = 1024
    const sizes = ['بايت', 'كيلوبايت', 'ميجابايت', 'جيجابايت']
    const i = Math.floor(Math.log(bytes) / Math.log(k))
    
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

const goBack = () => {
    window.history.back()
}
</script>

<style scoped>
:deep(.terms-content) {
    direction: rtl;
    text-align: right;
    line-height: 1.8;
}
</style>