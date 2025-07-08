<template>
    <Head :title="__('client.my_designs')" />

    <ClientLayout>
        <div class="py-12 bg-gray-50 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Page Header -->
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ __('client.my_designs_title') }}</h1>
                        <p class="mt-2 text-gray-600">إدارة وتعديل تصاميمك المحفوظة</p>
                    </div>
                    <Link 
                        :href="route('client.templates')" 
                        class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors inline-flex items-center"
                    >
                        <svg :class="isRTL ? 'mr-2' : 'ml-2'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        تصميم جديد
                    </Link>
                </div>

                <!-- Filters and View Options -->
                <div class="mb-8">
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <div class="flex items-center space-x-4" :class="isRTL ? 'space-x-reverse' : ''">
                                <div class="flex items-center space-x-2" :class="isRTL ? 'space-x-reverse' : ''">
                                    <label class="text-sm font-medium text-gray-700">ترتيب حسب:</label>
                                    <select 
                                        v-model="sortBy" 
                                        @change="sortDesigns"
                                        class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    >
                                        <option value="newest">الأحدث</option>
                                        <option value="oldest">الأقدم</option>
                                        <option value="name">الاسم</option>
                                        <option value="modified">آخر تعديل</option>
                                    </select>
                                </div>

                                <div class="flex items-center space-x-2" :class="isRTL ? 'space-x-reverse' : ''">
                                    <label class="text-sm font-medium text-gray-700">عرض:</label>
                                    <button 
                                        @click="viewMode = 'grid'"
                                        :class="viewMode === 'grid' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'"
                                        class="p-2 rounded-lg transition-colors"
                                    >
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M3 3h7v7H3V3zm0 11h7v7H3v-7zm11-11h7v7h-7V3zm0 11h7v7h-7v-7z"/>
                                        </svg>
                                    </button>
                                    <button 
                                        @click="viewMode = 'list'"
                                        :class="viewMode === 'list' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'"
                                        class="p-2 rounded-lg transition-colors"
                                    >
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M3 13h2v-2H3v2zm0 4h2v-2H3v2zm0-8h2V7H3v2zm4 4h14v-2H7v2zm0 4h14v-2H7v2zM7 7v2h14V7H7z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="text-sm text-gray-600">
                                {{ sortedDesigns.length }} تصميم
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Designs Grid -->
                <div v-if="sortedDesigns.length > 0">
                    <!-- Grid View -->
                    <div v-if="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        <div 
                            v-for="design in sortedDesigns" 
                            :key="design.id"
                            class="bg-white rounded-lg shadow-sm hover:shadow-lg transition-shadow duration-300 overflow-hidden group"
                        >
                            <!-- Design Preview -->
                            <div class="relative h-48 bg-gray-100 overflow-hidden">
                                <div 
                                    class="w-full h-full flex items-center justify-center"
                                    :style="{ background: design.background || '#ffffff' }"
                                >
                                    <div class="text-center p-4">
                                        <h4 class="font-bold text-lg">{{ design.name }}</h4>
                                        <p class="text-sm opacity-75 mt-1">{{ design.category }}</p>
                                    </div>
                                </div>
                                
                                <!-- Overlay on hover -->
                                <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                                    <div class="flex space-x-2" :class="isRTL ? 'space-x-reverse' : ''">
                                        <button 
                                            @click="editDesign(design)"
                                            class="bg-blue-600 text-white p-2 rounded-lg hover:bg-blue-700 transition-colors"
                                            :title="__('client.edit_design')"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                        <button 
                                            @click="duplicateDesign(design)"
                                            class="bg-green-600 text-white p-2 rounded-lg hover:bg-green-700 transition-colors"
                                            :title="__('client.duplicate_design')"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                            </svg>
                                        </button>
                                        <button 
                                            @click="downloadDesign(design)"
                                            class="bg-purple-600 text-white p-2 rounded-lg hover:bg-purple-700 transition-colors"
                                            :title="__('client.download_design')"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </button>
                                        <button 
                                            @click="deleteDesign(design)"
                                            class="bg-red-600 text-white p-2 rounded-lg hover:bg-red-700 transition-colors"
                                            :title="__('client.delete_design')"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Design Info -->
                            <div class="p-4">
                                <h4 class="font-semibold text-gray-900 mb-1">{{ design.name }}</h4>
                                <p class="text-sm text-gray-600 mb-2">{{ design.category }}</p>
                                
                                <div class="flex items-center justify-between text-xs text-gray-500">
                                    <span>{{ __('client.design_created') }}: {{ formatDate(design.created_at) }}</span>
                                    <span>{{ __('client.design_modified') }}: {{ formatDate(design.updated_at) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- List View -->
                    <div v-else class="space-y-4">
                        <div 
                            v-for="design in sortedDesigns" 
                            :key="design.id"
                            class="bg-white rounded-lg shadow-sm hover:shadow-lg transition-shadow duration-300 p-6"
                        >
                            <div class="flex items-center space-x-6" :class="isRTL ? 'space-x-reverse' : ''">
                                <!-- Design Thumbnail -->
                                <div 
                                    class="w-24 h-16 rounded-lg overflow-hidden flex-shrink-0"
                                    :style="{ background: design.background || '#ffffff' }"
                                >
                                    <div class="w-full h-full flex items-center justify-center text-xs font-medium">
                                        {{ design.name }}
                                    </div>
                                </div>

                                <!-- Design Info -->
                                <div class="flex-1">
                                    <h4 class="text-lg font-semibold text-gray-900 mb-1">{{ design.name }}</h4>
                                    <p class="text-gray-600 mb-2">{{ design.category }}</p>
                                    
                                    <div class="flex items-center space-x-4 text-sm text-gray-500" :class="isRTL ? 'space-x-reverse' : ''">
                                        <span>{{ __('client.design_created') }}: {{ formatDate(design.created_at) }}</span>
                                        <span>{{ __('client.design_modified') }}: {{ formatDate(design.updated_at) }}</span>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex space-x-2" :class="isRTL ? 'space-x-reverse' : ''">
                                    <button 
                                        @click="editDesign(design)"
                                        class="bg-blue-600 text-white px-3 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors"
                                    >
                                        {{ __('client.edit') }}
                                    </button>
                                    <button 
                                        @click="duplicateDesign(design)"
                                        class="bg-green-600 text-white px-3 py-2 rounded-lg text-sm font-medium hover:bg-green-700 transition-colors"
                                    >
                                        نسخ
                                    </button>
                                    <button 
                                        @click="downloadDesign(design)"
                                        class="bg-purple-600 text-white px-3 py-2 rounded-lg text-sm font-medium hover:bg-purple-700 transition-colors"
                                    >
                                        {{ __('client.download') }}
                                    </button>
                                    <button 
                                        @click="deleteDesign(design)"
                                        class="bg-red-600 text-white px-3 py-2 rounded-lg text-sm font-medium hover:bg-red-700 transition-colors"
                                    >
                                        {{ __('client.delete') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="text-center py-16">
                    <svg class="w-24 h-24 text-gray-400 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="text-2xl font-semibold text-gray-900 mb-4">{{ __('client.no_designs') }}</h3>
                    <p class="text-gray-600 mb-8 max-w-md mx-auto">
                        لم تقم بإنشاء أي تصاميم حتى الآن. ابدأ في إنشاء تصميمك الأول باستخدام قوالبنا الجاهزة.
                    </p>
                    <Link 
                        :href="route('client.templates')" 
                        class="bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors inline-flex items-center"
                    >
                        <svg :class="isRTL ? 'mr-2' : 'ml-2'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        {{ __('client.create_first_design') }}
                    </Link>
                </div>
            </div>
        </div>
    </ClientLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import ClientLayout from '@/Layouts/ClientLayout.vue';
import { useTranslations } from '@/Composables/useTranslations';

// Props
const props = defineProps({
    designs: {
        type: Array,
        default: () => []
    }
});

// Composables
const { __, isRTL } = useTranslations();

// State
const sortBy = ref('newest');
const viewMode = ref('grid');

// Sample designs data (replace with actual data from props)
const sampleDesigns = [
    {
        id: 1,
        name: 'بطاقة عمل شخصية',
        category: 'بطاقات أعمال',
        background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
        created_at: '2024-01-15',
        updated_at: '2024-01-20'
    },
    {
        id: 2,
        name: 'دعوة زفاف',
        category: 'دعوات',
        background: 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
        created_at: '2024-01-10',
        updated_at: '2024-01-18'
    }
];

const designs = computed(() => props.designs.length > 0 ? props.designs : sampleDesigns);

// Computed
const sortedDesigns = computed(() => {
    const sorted = [...designs.value];
    
    switch (sortBy.value) {
        case 'oldest':
            return sorted.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
        case 'name':
            return sorted.sort((a, b) => a.name.localeCompare(b.name));
        case 'modified':
            return sorted.sort((a, b) => new Date(b.updated_at) - new Date(a.updated_at));
        case 'newest':
        default:
            return sorted.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
    }
});

// Methods
const sortDesigns = () => {
    // Sorting is handled by computed property
};

const editDesign = (design) => {
    router.visit(route('client.editor', { design: design.id }));
};

const duplicateDesign = (design) => {
    router.post(route('client.designs.duplicate', design.id));
};

const downloadDesign = (design) => {
    // Implement download functionality
    console.log('Download design:', design);
};

const deleteDesign = (design) => {
    if (confirm('هل أنت متأكد من حذف هذا التصميم؟')) {
        router.delete(route('client.designs.destroy', design.id));
    }
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('ar-SA');
};
</script>
