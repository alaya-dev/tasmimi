<template>
    <Head :title="__('client.templates')" />

    <ClientLayout>
        <div class="py-12 bg-gray-50 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Page Header -->
                <div class="text-center mb-12">
                    <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ __('client.browse_templates') }}</h1>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                        اختر من مجموعة واسعة من القوالب الاحترافية المصممة خصيصاً لجميع احتياجاتك
                    </p>
                </div>

                <!-- Filters -->
                <div class="mb-8">
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <div class="flex flex-wrap items-center gap-4">
                            <div class="flex items-center space-x-4" :class="isRTL ? 'space-x-reverse' : ''">
                                <label class="text-sm font-medium text-gray-700">{{ __('client.template_categories') }}:</label>
                                <select 
                                    v-model="selectedCategory" 
                                    @change="filterTemplates"
                                    class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                                    <option value="">جميع الفئات</option>
                                    <option value="business">{{ __('client.business_cards') }}</option>
                                    <option value="wedding">{{ __('client.wedding_cards') }}</option>
                                    <option value="birthday">{{ __('client.birthday_cards') }}</option>
                                    <option value="greeting">{{ __('client.greeting_cards') }}</option>
                                </select>
                            </div>

                            <div class="flex items-center space-x-4" :class="isRTL ? 'space-x-reverse' : ''">
                                <label class="text-sm font-medium text-gray-700">ترتيب حسب:</label>
                                <select 
                                    v-model="sortBy" 
                                    @change="sortTemplates"
                                    class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                                    <option value="popular">الأكثر شعبية</option>
                                    <option value="newest">الأحدث</option>
                                    <option value="name">الاسم</option>
                                </select>
                            </div>

                            <div class="flex items-center space-x-2" :class="isRTL ? 'space-x-reverse' : ''">
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
                    </div>
                </div>

                <!-- Templates Grid -->
                <div v-if="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <div 
                        v-for="template in filteredTemplates" 
                        :key="template.id"
                        class="bg-white rounded-lg shadow-sm hover:shadow-lg transition-shadow duration-300 overflow-hidden group"
                    >
                        <!-- Template Preview -->
                        <div class="relative h-64 bg-gradient-to-br overflow-hidden" :style="{ background: template.gradient }">
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="text-white text-center p-4">
                                    <h3 class="font-bold text-lg mb-2">{{ template.name }}</h3>
                                    <p class="text-sm opacity-90">{{ template.description }}</p>
                                </div>
                            </div>
                            
                            <!-- Overlay on hover -->
                            <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                                <div class="flex space-x-3" :class="isRTL ? 'space-x-reverse' : ''">
                                    <button 
                                        @click="previewTemplate(template)"
                                        class="bg-white text-gray-900 px-4 py-2 rounded-lg font-medium hover:bg-gray-100 transition-colors"
                                    >
                                        {{ __('client.preview_template') }}
                                    </button>
                                    <button 
                                        @click="useTemplate(template)"
                                        class="bg-blue-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-blue-700 transition-colors"
                                    >
                                        {{ __('client.use_template') }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Template Info -->
                        <div class="p-4">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-semibold text-gray-900">{{ template.name }}</h4>
                                <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                                    {{ getCategoryName(template.category) }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 mb-3">{{ template.description }}</p>
                            
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-1" :class="isRTL ? 'space-x-reverse' : ''">
                                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                    <span class="text-sm text-gray-600">{{ template.rating }}</span>
                                    <span class="text-xs text-gray-500">({{ template.downloads }})</span>
                                </div>
                                
                                <button 
                                    @click="useTemplate(template)"
                                    class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors"
                                >
                                    استخدم الآن
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Templates List View -->
                <div v-else class="space-y-4">
                    <div 
                        v-for="template in filteredTemplates" 
                        :key="template.id"
                        class="bg-white rounded-lg shadow-sm hover:shadow-lg transition-shadow duration-300 p-6"
                    >
                        <div class="flex items-center space-x-6" :class="isRTL ? 'space-x-reverse' : ''">
                            <!-- Template Thumbnail -->
                            <div class="w-32 h-20 rounded-lg overflow-hidden flex-shrink-0" :style="{ background: template.gradient }">
                                <div class="w-full h-full flex items-center justify-center text-white text-sm font-medium">
                                    {{ template.name }}
                                </div>
                            </div>

                            <!-- Template Info -->
                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="text-lg font-semibold text-gray-900">{{ template.name }}</h4>
                                    <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                                        {{ getCategoryName(template.category) }}
                                    </span>
                                </div>
                                <p class="text-gray-600 mb-3">{{ template.description }}</p>
                                
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4" :class="isRTL ? 'space-x-reverse' : ''">
                                        <div class="flex items-center space-x-1" :class="isRTL ? 'space-x-reverse' : ''">
                                            <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                            </svg>
                                            <span class="text-sm text-gray-600">{{ template.rating }}</span>
                                        </div>
                                        <span class="text-sm text-gray-500">{{ template.downloads }} تحميل</span>
                                    </div>
                                    
                                    <div class="flex space-x-3" :class="isRTL ? 'space-x-reverse' : ''">
                                        <button 
                                            @click="previewTemplate(template)"
                                            class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg font-medium hover:bg-gray-50 transition-colors"
                                        >
                                            {{ __('client.preview_template') }}
                                        </button>
                                        <button 
                                            @click="useTemplate(template)"
                                            class="bg-blue-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-blue-700 transition-colors"
                                        >
                                            {{ __('client.use_template') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="filteredTemplates.length === 0" class="text-center py-12">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد قوالب</h3>
                    <p class="text-gray-600">لم يتم العثور على قوالب تطابق معايير البحث الخاصة بك</p>
                </div>
            </div>
        </div>
    </ClientLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import ClientLayout from '@/Layouts/ClientLayout.vue';
import { useTranslations } from '@/Composables/useTranslations';

// Props
const props = defineProps({
    templates: {
        type: Array,
        default: () => []
    }
});

// Composables
const { __, isRTL } = useTranslations();

// State
const selectedCategory = ref('');
const sortBy = ref('popular');
const viewMode = ref('grid');

// Sample templates data (replace with actual data from props)
const sampleTemplates = [
    {
        id: 1,
        name: 'بطاقة عمل كلاسيكية',
        description: 'تصميم أنيق ومهني للأعمال',
        category: 'business',
        gradient: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
        rating: 4.8,
        downloads: 1250
    },
    {
        id: 2,
        name: 'دعوة زفاف رومانسية',
        description: 'تصميم رومانسي لحفلات الزفاف',
        category: 'wedding',
        gradient: 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
        rating: 4.9,
        downloads: 890
    },
    {
        id: 3,
        name: 'بطاقة عيد ميلاد مرحة',
        description: 'تصميم ملون ومرح لأعياد الميلاد',
        category: 'birthday',
        gradient: 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
        rating: 4.7,
        downloads: 650
    },
    {
        id: 4,
        name: 'بطاقة تهنئة عامة',
        description: 'تصميم متعدد الاستخدامات للتهاني',
        category: 'greeting',
        gradient: 'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)',
        rating: 4.6,
        downloads: 420
    }
];

const templates = computed(() => props.templates.length > 0 ? props.templates : sampleTemplates);

// Computed
const filteredTemplates = computed(() => {
    let filtered = templates.value;
    
    if (selectedCategory.value) {
        filtered = filtered.filter(template => template.category === selectedCategory.value);
    }
    
    // Sort templates
    filtered.sort((a, b) => {
        switch (sortBy.value) {
            case 'newest':
                return b.id - a.id;
            case 'name':
                return a.name.localeCompare(b.name);
            case 'popular':
            default:
                return b.downloads - a.downloads;
        }
    });
    
    return filtered;
});

// Methods
const filterTemplates = () => {
    // Filter logic is handled by computed property
};

const sortTemplates = () => {
    // Sort logic is handled by computed property
};

const getCategoryName = (category) => {
    const categories = {
        business: __('client.business_cards'),
        wedding: __('client.wedding_cards'),
        birthday: __('client.birthday_cards'),
        greeting: __('client.greeting_cards')
    };
    return categories[category] || category;
};

const previewTemplate = (template) => {
    // Open preview modal or navigate to preview page
    console.log('Preview template:', template);
};

const useTemplate = (template) => {
    // Navigate to editor with selected template
    router.visit(route('client.editor', { template: template.id }));
};
</script>
