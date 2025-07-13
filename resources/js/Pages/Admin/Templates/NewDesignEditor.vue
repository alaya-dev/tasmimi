<template>
    <AdminLayout>
        <Head title="محرر التصميم المتقدم" />
        
        <!-- Loading Overlay -->
        <div v-if="isLoading" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-lg shadow-lg text-center">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto mb-4"></div>
                <p class="text-gray-700">{{ loadingMessage }}</p>
            </div>
        </div>

        <!-- Notifications -->
        <div v-if="notifications.length > 0" class="fixed top-4 right-4 z-40 space-y-2">
            <div v-for="notification in notifications" :key="notification.id" 
                 :class="[
                     'p-4 rounded-lg shadow-lg transition-all duration-300',
                     notification.type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
                 ]">
                {{ notification.message }}
            </div>
        </div>

        <!-- Header with Enhanced Controls -->
        <div class="bg-white shadow-sm border-b border-gray-200 mb-6">
            <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <div class="flex items-center space-x-4 rtl:space-x-reverse">
                        <Link :href="route('admin.templates.index')" 
                              class="text-gray-500 hover:text-gray-700 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                        </Link>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">محرر التصميم المتقدم</h1>
                            <p class="text-sm text-gray-600">{{ template.name }} - {{ template.category?.name }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-3 rtl:space-x-reverse">
                        <!-- Background Image Upload -->
                        <button @click="openBackgroundUpload" 
                                class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg transition-colors">
                            <svg class="w-4 h-4 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            صورة الخلفية
                        </button>
                        
                        <!-- Generate Thumbnail -->
                        <button @click="generateThumbnail"
                                class="bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded-lg transition-colors">
                            <svg class="w-4 h-4 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            إنشاء معاينة
                        </button>

                        <!-- Watermark Controls -->
                        <div class="flex items-center space-x-2 rtl:space-x-reverse">
                            <button @click="addWatermark"
                                    v-if="!hasWatermark()"
                                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg transition-colors">
                                <svg class="w-4 h-4 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                                إضافة العلامة المائية
                            </button>

                            <span v-else class="flex items-center text-green-600 text-sm">
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                العلامة المائية موجودة
                            </span>
                        </div>
                        
                        <!-- Preview Button -->
                        <button @click="previewDesign" 
                                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                            <svg class="w-4 h-4 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            معاينة
                        </button>
                        
                        <!-- Save Button -->
                        <button @click="saveDesign" 
                                :disabled="isSaving"
                                class="bg-green-500 hover:bg-green-600 disabled:bg-gray-400 text-white px-6 py-2 rounded-lg transition-colors">
                            <svg v-if="!isSaving" class="w-4 h-4 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                            </svg>
                            <div v-else class="animate-spin rounded-full h-4 w-4 border-b-2 border-white inline-block ml-2"></div>
                            {{ isSaving ? 'جاري الحفظ...' : 'حفظ التصميم' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Editor Layout -->
        <div class="flex h-screen bg-gray-50" dir="rtl">
            <!-- Right Sidebar - Elements Panel -->
            <div class="w-80 bg-white shadow-lg border-l border-gray-200 overflow-y-auto">
                <div class="p-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">عناصر التصميم</h3>
                    
                    <!-- Element Categories -->
                    <div class="space-y-4">
                        <!-- Text Elements -->
                        <div class="border border-gray-200 rounded-lg p-3">
                            <h4 class="font-medium text-gray-700 mb-3">عناصر النص</h4>
                            <div class="grid grid-cols-2 gap-2">
                                <div @click="addElement('text')" 
                                     class="p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-blue-50 hover:border-blue-300 transition-colors text-center">
                                    <svg class="w-6 h-6 mx-auto mb-1 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                                    </svg>
                                    <span class="text-xs text-gray-600">نص</span>
                                </div>
                                <div @click="addElement('heading')" 
                                     class="p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-blue-50 hover:border-blue-300 transition-colors text-center">
                                    <svg class="w-6 h-6 mx-auto mb-1 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                    </svg>
                                    <span class="text-xs text-gray-600">عنوان</span>
                                </div>
                            </div>
                        </div>

                        <!-- Media Elements -->
                        <div class="border border-gray-200 rounded-lg p-3">
                            <h4 class="font-medium text-gray-700 mb-3">عناصر الوسائط</h4>
                            <div class="grid grid-cols-2 gap-2">
                                <div @click="addElement('image')" 
                                     class="p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-blue-50 hover:border-blue-300 transition-colors text-center">
                                    <svg class="w-6 h-6 mx-auto mb-1 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <span class="text-xs text-gray-600">صورة</span>
                                </div>
                                <div @click="addElement('icon')" 
                                     class="p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-blue-50 hover:border-blue-300 transition-colors text-center">
                                    <svg class="w-6 h-6 mx-auto mb-1 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                    </svg>
                                    <span class="text-xs text-gray-600">أيقونة</span>
                                </div>
                            </div>
                        </div>

                        <!-- Interactive Elements -->
                        <div class="border border-gray-200 rounded-lg p-3">
                            <h4 class="font-medium text-gray-700 mb-3">عناصر تفاعلية</h4>
                            <div class="grid grid-cols-2 gap-2">
                                <div @click="addElement('button')" 
                                     class="p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-blue-50 hover:border-blue-300 transition-colors text-center">
                                    <svg class="w-6 h-6 mx-auto mb-1 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path>
                                    </svg>
                                    <span class="text-xs text-gray-600">زر</span>
                                </div>
                                <div @click="addElement('shape')" 
                                     class="p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-blue-50 hover:border-blue-300 transition-colors text-center">
                                    <svg class="w-6 h-6 mx-auto mb-1 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                    <span class="text-xs text-gray-600">شكل</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Center - Canvas Area -->
            <div class="flex-1 flex flex-col">
                <!-- Canvas Toolbar -->
                <div class="bg-white border-b border-gray-200 p-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4 rtl:space-x-reverse">
                            <!-- Zoom Controls -->
                            <div class="flex items-center space-x-2 rtl:space-x-reverse">
                                <button @click="zoomOut" class="p-2 text-gray-600 hover:text-gray-900">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7"></path>
                                    </svg>
                                </button>
                                <span class="text-sm text-gray-600">{{ Math.round(zoomLevel * 100) }}%</span>
                                <button @click="zoomIn" class="p-2 text-gray-600 hover:text-gray-900">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                                    </svg>
                                </button>
                            </div>

                            <!-- Undo/Redo -->
                            <div class="flex items-center space-x-2 rtl:space-x-reverse">
                                <button @click="undo" :disabled="!canUndo" class="p-2 text-gray-600 hover:text-gray-900 disabled:text-gray-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                                    </svg>
                                </button>
                                <button @click="redo" :disabled="!canRedo" class="p-2 text-gray-600 hover:text-gray-900 disabled:text-gray-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 10h-10a8 8 0 00-8 8v2m18-10l-6 6m6-6l-6-6"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Canvas Size Info -->
                        <div class="text-sm text-gray-600">
                            {{ canvasWidth }} × {{ canvasHeight }} px
                        </div>
                    </div>
                </div>

                <!-- Canvas Container -->
                <div class="flex-1 bg-gray-100 p-8 overflow-auto">
                    <div class="mx-auto bg-white shadow-lg" 
                         :style="{ width: canvasWidth + 'px', height: canvasHeight + 'px', transform: `scale(${zoomLevel})` }">
                        <!-- GrapesJS Editor Container -->
                        <div id="gjs-editor" class="w-full h-full"></div>
                    </div>
                </div>
            </div>

            <!-- Left Sidebar - Properties Panel -->
            <div class="w-80 bg-white shadow-lg border-r border-gray-200 overflow-y-auto">
                <div class="p-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">خصائص العنصر</h3>
                    
                    <!-- Element Properties will be dynamically populated here -->
                    <div id="element-properties" class="space-y-4">
                        <div class="text-center text-gray-500 py-8">
                            اختر عنصراً لتحرير خصائصه
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Background Upload Modal -->
        <div v-if="showBackgroundModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 w-96">
                <h3 class="text-lg font-semibold mb-4">رفع صورة الخلفية</h3>
                <input type="file" @change="handleBackgroundUpload" accept="image/*" class="w-full mb-4">
                <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                    <button @click="showBackgroundModal = false" class="px-4 py-2 text-gray-600 hover:text-gray-800">إلغاء</button>
                    <button @click="applyBackground" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">تطبيق</button>
                </div>
            </div>
        </div>

        <!-- Preview Modal -->
        <div v-if="showPreviewModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 max-w-4xl max-h-full overflow-auto">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">معاينة التصميم</h3>
                    <button @click="showPreviewModal = false" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div id="preview-container" class="border border-gray-200 rounded-lg"></div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, onMounted, onUnmounted, nextTick } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import 'grapesjs/dist/css/grapes.min.css'
import { registerCustomBlocks, blockCategories } from '@/Components/DesignEditor/CustomBlocks.js'
import { styleManagerConfig, applyStylePreset } from '@/Components/DesignEditor/StyleManager.js'
import { loadTemplatePreset, getTemplatesForCategory } from '@/Components/DesignEditor/TemplatePresets.js'
import { designDataService } from '@/Services/DesignDataService.js'
import { thumbnailService } from '@/Services/ThumbnailService.js'
import { watermarkService } from '@/Services/WatermarkService.js'

// Props
const props = defineProps({
    template: Object,
    categories: Array,
})

// Reactive state
const isLoading = ref(false)
const isSaving = ref(false)
const loadingMessage = ref('')
const notifications = ref([])
const showBackgroundModal = ref(false)
const showPreviewModal = ref(false)
const backgroundFile = ref(null)

// Editor state
const editor = ref(null)
const zoomLevel = ref(1)
const canvasWidth = ref(800)
const canvasHeight = ref(600)
const canUndo = ref(false)
const canRedo = ref(false)

// Categories for templates (as requested)
const templateCategories = [
    'دعوة مناسبات عامة',
    'دعوة خاصة',
    'زواج',
    'خطبة',
    'نجاح',
    'تخرج',
    'مولود',
    'حفل',
    'شكر وتقدير',
    'تهنئة',
    'غلاف',
    'رمضان',
    'عيد الفطر',
    'عيد الأضحى',
    'اليوم الوطني',
    'يوم التأسيس'
]

// Initialize GrapesJS editor
const initializeEditor = async () => {
    try {
        isLoading.value = true
        loadingMessage.value = 'جاري تحميل محرر التصميم...'

        // Import GrapesJS dynamically
        const grapesjs = (await import('grapesjs')).default

        // Initialize editor with enhanced configuration
        editor.value = grapesjs.init({
            container: '#gjs-editor',
            width: '100%',
            height: '100%',
            fromElement: false,
            showOffsets: true,
            noticeOnUnload: false,
            storageManager: false,

            // Canvas configuration with Arabic fonts
            canvas: {
                styles: [
                    'https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap',
                    'https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&display=swap',
                    'https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@300;400;600;700&display=swap'
                ],
                scripts: []
            },

            // Device Manager for responsive design
            deviceManager: {
                devices: [
                    {
                        name: 'بطاقة قياسية',
                        width: '800px',
                        height: '600px'
                    },
                    {
                        name: 'بطاقة مربعة',
                        width: '600px',
                        height: '600px'
                    },
                    {
                        name: 'بطاقة طولية',
                        width: '600px',
                        height: '800px'
                    }
                ]
            },

            // Block Manager - Will be configured with custom blocks
            blockManager: {
                appendTo: false, // We'll handle this manually
                blocks: []
            },

            // Style Manager with Arabic configuration
            styleManager: styleManagerConfig,

            // Layer Manager
            layerManager: {
                appendTo: '#element-properties'
            },

            // Panels configuration
            panels: {
                defaults: []
            },

            // Rich Text Editor with RTL support
            richTextEditor: {
                actions: ['bold', 'italic', 'underline', 'strikethrough'],
                config: {
                    direction: 'rtl'
                }
            },

            // Asset Manager
            assetManager: {
                embedAsBase64: false,
                upload: false,
                uploadText: 'رفع الصور هنا أو انقر للتصفح',
                addBtnText: 'إضافة صورة'
            }
        })

        // Register custom blocks
        registerCustomBlocks(editor.value)

        // Load existing design if available
        if (props.template.design_data) {
            try {
                const designData = typeof props.template.design_data === 'string'
                    ? JSON.parse(props.template.design_data)
                    : props.template.design_data

                // Validate design data before loading
                const validation = designDataService.validateDesignData(designData)
                if (!validation.isValid) {
                    console.warn('Design data validation failed:', validation.errors)
                    addNotification('بيانات التصميم قد تكون تالفة، سيتم تحميل قالب افتراضي', 'warning')
                    setDefaultContent()
                } else {
                    // Load design using our service
                    const success = designDataService.loadDesignData(editor.value, designData)
                    if (success) {
                        addNotification('تم تحميل التصميم بنجاح', 'success')

                        // Update canvas dimensions if available
                        if (designData.metadata?.canvasWidth && designData.metadata?.canvasHeight) {
                            canvasWidth.value = designData.metadata.canvasWidth
                            canvasHeight.value = designData.metadata.canvasHeight
                        }
                    } else {
                        throw new Error('Failed to load design data')
                    }
                }
            } catch (error) {
                console.error('Error loading design data:', error)
                addNotification('خطأ في تحميل التصميم المحفوظ، سيتم تحميل قالب افتراضي', 'error')
                setDefaultContent()
            }
        } else {
            // Set default content with watermark
            setDefaultContent()
        }

        // Setup event listeners
        setupEventListeners()

        isLoading.value = false
    } catch (error) {
        console.error('Error initializing editor:', error)
        addNotification('خطأ في تحميل المحرر', 'error')
        isLoading.value = false
    }
}



// Set default content with watermark
const setDefaultContent = () => {
    const defaultHtml = `
        <div style="width: 100%; height: 100%; position: relative; background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); display: flex; align-items: center; justify-content: center;">
            <div style="text-align: center; font-family: Cairo, Arial, sans-serif;">
                <h1 style="font-size: 48px; color: #1e293b; margin-bottom: 20px;" data-editable="true" data-element-type="heading">
                    مرحباً بك في محرر التصميم
                </h1>
                <p style="font-size: 18px; color: #64748b; margin-bottom: 30px;" data-editable="true" data-element-type="text">
                    ابدأ بإضافة العناصر من الشريط الجانبي
                </p>
                <div style="position: absolute; bottom: 20px; right: 20px; opacity: 0.3; font-size: 12px; color: #94a3b8;">
                    سامقة © جميع الحقوق محفوظة
                </div>
            </div>
        </div>
    `
    editor.value.setComponents(defaultHtml)
}

// Setup event listeners
const setupEventListeners = () => {
    // Listen for component selection
    editor.value.on('component:selected', (component) => {
        updateUndoRedoState()
    })

    // Listen for component changes
    editor.value.on('component:update', () => {
        updateUndoRedoState()
    })

    // Listen for canvas changes
    editor.value.on('canvas:drop', () => {
        updateUndoRedoState()
    })
}

// Update undo/redo button states
const updateUndoRedoState = () => {
    const um = editor.value.UndoManager
    canUndo.value = um.hasUndo()
    canRedo.value = um.hasRedo()
}

// Element manipulation functions
const addElement = (elementType) => {
    if (!editor.value) return

    const blockManager = editor.value.BlockManager

    // Map element types to block IDs
    const blockMapping = {
        'text': 'arabic-text',
        'heading': 'arabic-heading',
        'image': 'logo-placeholder',
        'button': 'arabic-button',
        'icon': 'islamic-pattern',
        'shape': 'decorative-border'
    }

    const blockId = blockMapping[elementType] || elementType
    const block = blockManager.get(blockId)

    if (block) {
        const component = editor.value.addComponents(block.get('content'))[0]
        editor.value.select(component)
        addNotification(`تم إضافة ${getElementTypeName(elementType)}`, 'success')
    } else {
        // Fallback: create simple element
        addSimpleElement(elementType)
    }
}

// Fallback function for simple elements
const addSimpleElement = (elementType) => {
    let content = {}

    switch (elementType) {
        case 'text':
            content = {
                type: 'text',
                content: 'أدخل النص هنا',
                style: {
                    'font-family': 'Cairo, Arial, sans-serif',
                    'font-size': '16px',
                    'color': '#333333',
                    'padding': '10px',
                    'direction': 'rtl',
                    'text-align': 'right'
                },
                attributes: {
                    'data-editable': 'true',
                    'data-element-type': 'text'
                }
            }
            break
        case 'heading':
            content = {
                tagName: 'h1',
                type: 'text',
                content: 'عنوان رئيسي',
                style: {
                    'font-family': 'Cairo, Arial, sans-serif',
                    'font-size': '32px',
                    'font-weight': 'bold',
                    'color': '#1e293b',
                    'margin': '10px 0',
                    'direction': 'rtl',
                    'text-align': 'center'
                },
                attributes: {
                    'data-editable': 'true',
                    'data-element-type': 'heading'
                }
            }
            break
        case 'image':
            content = {
                type: 'image',
                style: {
                    'width': '150px',
                    'height': '100px',
                    'object-fit': 'cover',
                    'border-radius': '8px'
                },
                attributes: {
                    'data-element-type': 'image',
                    'src': 'https://via.placeholder.com/150x100/e2e8f0/64748b?text=صورة'
                }
            }
            break
        case 'button':
            content = {
                tagName: 'button',
                type: 'text',
                content: 'انقر هنا',
                style: {
                    'background': 'linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%)',
                    'color': 'white',
                    'padding': '12px 24px',
                    'border': 'none',
                    'border-radius': '8px',
                    'font-family': 'Cairo, Arial, sans-serif',
                    'font-size': '16px',
                    'cursor': 'pointer',
                    'direction': 'rtl'
                },
                attributes: {
                    'data-editable': 'true',
                    'data-element-type': 'button'
                }
            }
            break
        default:
            return
    }

    const component = editor.value.addComponents(content)[0]
    editor.value.select(component)
    addNotification(`تم إضافة ${getElementTypeName(elementType)}`, 'success')
}

const getElementTypeName = (type) => {
    const names = {
        'text': 'نص',
        'heading': 'عنوان',
        'image': 'صورة',
        'button': 'زر',
        'icon': 'أيقونة',
        'shape': 'شكل'
    }
    return names[type] || type
}

// Zoom functions
const zoomIn = () => {
    if (zoomLevel.value < 2) {
        zoomLevel.value += 0.1
    }
}

const zoomOut = () => {
    if (zoomLevel.value > 0.3) {
        zoomLevel.value -= 0.1
    }
}

// Undo/Redo functions
const undo = () => {
    if (editor.value && canUndo.value) {
        editor.value.UndoManager.undo()
        updateUndoRedoState()
    }
}

const redo = () => {
    if (editor.value && canRedo.value) {
        editor.value.UndoManager.redo()
        updateUndoRedoState()
    }
}

// Background image functions
const openBackgroundUpload = () => {
    showBackgroundModal.value = true
}

const handleBackgroundUpload = (event) => {
    const file = event.target.files[0]
    if (file && file.type.startsWith('image/')) {
        backgroundFile.value = file
    } else {
        addNotification('يرجى اختيار ملف صورة صحيح', 'error')
    }
}

const applyBackground = () => {
    if (!backgroundFile.value || !editor.value) return

    const reader = new FileReader()
    reader.onload = (e) => {
        const imageUrl = e.target.result

        // Apply background to canvas
        const wrapper = editor.value.getWrapper()
        wrapper.setStyle({
            'background-image': `url(${imageUrl})`,
            'background-size': 'cover',
            'background-position': 'center',
            'background-repeat': 'no-repeat'
        })

        showBackgroundModal.value = false
        backgroundFile.value = null
        addNotification('تم تطبيق صورة الخلفية بنجاح', 'success')
    }
    reader.readAsDataURL(backgroundFile.value)
}

// Preview function
const previewDesign = () => {
    if (!editor.value) return

    const html = editor.value.getHtml()
    const css = editor.value.getCss()

    const previewContent = `
        <style>
            ${css}
            body { margin: 0; font-family: Cairo, Arial, sans-serif; }
            .watermark {
                position: absolute;
                bottom: 10px;
                right: 10px;
                opacity: 0.5;
                font-size: 12px;
                color: #666;
                background: rgba(255,255,255,0.8);
                padding: 5px 10px;
                border-radius: 4px;
            }
        </style>
        <div style="position: relative; width: ${canvasWidth.value}px; height: ${canvasHeight.value}px;">
            ${html}
            <div class="watermark">سامقة © جميع الحقوق محفوظة</div>
        </div>
    `

    showPreviewModal.value = true

    nextTick(() => {
        const previewContainer = document.getElementById('preview-container')
        if (previewContainer) {
            previewContainer.innerHTML = previewContent
        }
    })
}

// Load template preset function
const loadPresetTemplate = (category, templateId) => {
    if (!editor.value) return

    const success = loadTemplatePreset(editor.value, category, templateId)
    if (success) {
        addNotification('تم تحميل القالب بنجاح', 'success')
        // Add watermark automatically
        addWatermark()
    } else {
        addNotification('خطأ في تحميل القالب', 'error')
    }
}

// Add watermark function
const addWatermark = () => {
    if (!editor.value) return

    try {
        // Use watermark service to add watermark
        const watermarkConfig = watermarkService.getWatermarkConfig('editor')
        const watermark = watermarkService.addWatermarkToEditor(editor.value, watermarkConfig)

        if (watermark) {
            addNotification('تم إضافة العلامة المائية بنجاح', 'success')
        }
    } catch (error) {
        console.error('Error adding watermark:', error)
        addNotification('خطأ في إضافة العلامة المائية', 'error')
    }
}

// Remove watermark function
const removeWatermark = () => {
    if (!editor.value) return

    try {
        watermarkService.removeWatermarkFromEditor(editor.value)
        addNotification('تم إزالة العلامة المائية', 'success')
    } catch (error) {
        console.error('Error removing watermark:', error)
        addNotification('خطأ في إزالة العلامة المائية', 'error')
    }
}

// Check if watermark exists
const hasWatermark = () => {
    if (!editor.value) return false
    return watermarkService.hasWatermark(editor.value)
}

// Generate thumbnail function
const generateThumbnail = async () => {
    if (!editor.value) return

    try {
        isLoading.value = true
        loadingMessage.value = 'جاري إنشاء المعاينة...'

        // Generate thumbnail using our service
        const thumbnailData = await thumbnailService.generateFromEditor(editor.value, {
            width: 400,
            height: 300,
            quality: 0.8,
            format: 'png'
        })

        // Add watermark using watermark service configuration
        const watermarkConfig = watermarkService.getWatermarkConfig('thumbnail')
        const watermarkedThumbnail = await thumbnailService.addWatermark(thumbnailData, {
            text: watermarkConfig.text,
            position: watermarkConfig.position,
            fontSize: parseInt(watermarkConfig.fontSize),
            color: watermarkConfig.color,
            backgroundColor: watermarkConfig.backgroundColor
        })

        // Optimize size
        const optimizedThumbnail = await thumbnailService.optimizeSize(watermarkedThumbnail, 150000) // 150KB max

        // Upload to server
        const response = await fetch(route('admin.templates.thumbnail.generate', props.template.id), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                thumbnail_data: optimizedThumbnail
            })
        })

        if (response.ok) {
            const result = await response.json()
            addNotification('تم إنشاء المعاينة بنجاح', 'success')

            // Update template thumbnail
            if (result.thumbnail_url) {
                props.template.thumbnail_url = result.thumbnail_url
            }
        } else {
            const errorData = await response.json()
            throw new Error(errorData.message || 'Failed to save thumbnail')
        }

        isLoading.value = false
    } catch (error) {
        console.error('Error generating thumbnail:', error)
        addNotification(error.message || 'خطأ في إنشاء المعاينة', 'error')
        isLoading.value = false
    }
}

// Save design function
const saveDesign = async () => {
    if (!editor.value) return

    try {
        isSaving.value = true

        // Process design data using our service
        const designData = designDataService.processDesignData(editor.value, {
            canvasWidth: canvasWidth.value,
            canvasHeight: canvasHeight.value,
            category: props.template.category?.name,
            templateName: props.template.name,
            createdBy: 'admin'
        })

        // Validate design data
        const validation = designDataService.validateDesignData(designData)
        if (!validation.isValid) {
            throw new Error(validation.errors.join(', '))
        }

        // Show warnings if any
        if (validation.warnings.length > 0) {
            validation.warnings.forEach(warning => {
                addNotification(warning, 'warning')
            })
        }

        // Ensure watermark is present before saving
        if (!hasWatermark()) {
            addWatermark()
            addNotification('تم إضافة العلامة المائية تلقائياً', 'info')
        }

        // Validate watermark integrity
        const watermarkValidation = watermarkService.validateWatermarkIntegrity(designData.design.html)
        if (!watermarkValidation.isValid) {
            addNotification('تحذير: العلامة المائية قد تكون مفقودة أو تالفة', 'warning')
        }

        // Save to server
        const response = await fetch(route('admin.templates.design.save', props.template.id), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                design_data: JSON.stringify(designData),
                editable_elements: designData.editableElements,
                canvas_size: `${canvasWidth.value}x${canvasHeight.value}`,
                background_image: designData.assets?.backgroundImage || null,
                design_notes: `تم التحديث في ${new Date().toLocaleString('ar-SA')}`
            })
        })

        if (response.ok) {
            const result = await response.json()
            addNotification('تم حفظ التصميم بنجاح', 'success')

            // Update template data
            if (result.template) {
                Object.assign(props.template, result.template)
            }

            // Auto-generate thumbnail if significant changes were made
            if (designData.editableElements && designData.editableElements.length > 0) {
                setTimeout(() => {
                    generateThumbnail()
                }, 1000) // Delay to ensure UI is updated
            }
        } else {
            const errorData = await response.json()
            throw new Error(errorData.message || 'Failed to save design')
        }

        isSaving.value = false
    } catch (error) {
        console.error('Error saving design:', error)
        addNotification(error.message || 'خطأ في حفظ التصميم', 'error')
        isSaving.value = false
    }
}

// Enhanced function to get editable elements (now handled by designDataService)
const getEditableElements = () => {
    if (!editor.value) return []

    // Use the design data service to extract editable elements
    try {
        const components = editor.value.getComponents()
        return designDataService.extractEditableElements(components)
    } catch (error) {
        console.error('Error extracting editable elements:', error)
        return []
    }
}

// Notification system
const addNotification = (message, type = 'info') => {
    const notification = {
        id: Date.now(),
        message,
        type
    }

    notifications.value.push(notification)

    // Auto remove after 3 seconds
    setTimeout(() => {
        const index = notifications.value.findIndex(n => n.id === notification.id)
        if (index > -1) {
            notifications.value.splice(index, 1)
        }
    }, 3000)
}

// Lifecycle hooks
onMounted(() => {
    initializeEditor()
})

onUnmounted(() => {
    if (editor.value) {
        editor.value.destroy()
    }
})
</script>

<style scoped>
/* Custom styles for the design editor */
.gjs-editor {
    direction: rtl;
}

.gjs-editor * {
    font-family: 'Cairo', Arial, sans-serif;
}

/* RTL adjustments for GrapesJS */
.gjs-editor .gjs-toolbar {
    direction: ltr;
}

.gjs-editor .gjs-toolbar-item {
    float: right;
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Zoom transition */
#gjs-editor {
    transition: transform 0.2s ease;
}

/* Loading animation */
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.animate-spin {
    animation: spin 1s linear infinite;
}
</style>
