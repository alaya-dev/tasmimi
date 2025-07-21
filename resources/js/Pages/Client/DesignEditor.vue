<template>
    <Head title="محرر التصميم المتقدم - إصدار العملاء">
        <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@300;400;600;700&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Changa:wght@400;700&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Cairo+Play:wght@400;700&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;700&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Reem+Kufi:wght@400;700&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Lalezar&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Harmattan:wght@400;700&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Markazi+Text:wght@400;700&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@400;700&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Amiri+Quran&display=swap" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    </Head>

    <div class="min-h-screen bg-gray-100" dir="rtl">
        <!-- Header -->
        <div class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-full mx-auto px-6 py-4">
                <div class="flex justify-between items-center">
                    <!-- Template Info -->
                    <div class="flex items-center space-x-4 space-x-reverse">
                        <div class="w-10 h-10 bg-purple-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-palette text-white"></i>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-gray-800">محرر التصميم المتقدم</h1>
                            <p class="text-sm text-gray-600">{{ template.name }}</p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <!-- History -->
                        <div class="flex items-center bg-gray-100 rounded-lg p-1">
                            <button
                                @click="undo"
                                :disabled="!canUndo"
                                class="p-2 text-gray-600 hover:text-gray-800 rounded disabled:opacity-50"
                                title="تراجع"
                            >
                                <i class="fas fa-undo"></i>
                            </button>
                            <button
                                @click="redo"
                                :disabled="!canRedo"
                                class="p-2 text-gray-600 hover:text-gray-800 rounded disabled:opacity-50"
                                title="إعادة"
                            >
                                <i class="fas fa-redo"></i>
                            </button>
                        </div>

                        <!-- Device Preview -->
                        <div class="flex items-center bg-gray-100 rounded-lg p-1">
                            <button
                                v-for="device in devices"
                                :key="device.id"
                                @click="setDevice(device.id)"
                                :class="[
                                    'p-2 rounded transition-colors',
                                    currentDevice === device.id
                                        ? 'bg-purple-600 text-white'
                                        : 'text-gray-600 hover:text-gray-800'
                                ]"
                                :title="device.name"
                            >
                                <i :class="device.icon"></i>
                            </button>
                        </div>

                        <!-- Save -->
                        <button
                            @click="saveDesign"
                            :disabled="saving"
                            class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 disabled:opacity-50 flex items-center space-x-2 space-x-reverse"
                        >
                            <i :class="saving ? 'fas fa-spinner fa-spin' : 'fas fa-save'"></i>
                            <span>{{ saving ? 'جاري الحفظ...' : 'حفظ' }}</span>
                        </button>

                        <!-- Export -->
                        <button
                            @click="showExportModal = true"
                            class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 flex items-center space-x-2 space-x-reverse"
                            title="تصدير التصميم مع العلامة المائية"
                        >
                            <i class="fas fa-download"></i>
                            <span>تصدير</span>
                        </button>

                        <!-- Preview -->
                        <button
                            @click="previewDesign"
                            class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 flex items-center space-x-2 space-x-reverse"
                        >
                            <i class="fas fa-eye"></i>
                            <span>معاينة</span>
                        </button>

                        <!-- Back -->
                        <Link
                            :href="route('client.my-designs')"
                            class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700 flex items-center space-x-2 space-x-reverse"
                        >
                            <i class="fas fa-arrow-right"></i>
                            <span>رجوع</span>
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Editor -->
        <div class="flex h-screen">
            <!-- Sidebar -->
            <div class="w-80 bg-white border-l border-gray-200 flex flex-col">
                <!-- Tabs -->
                <div class="flex-shrink-0 border-b border-gray-200">
                    <div class="flex">
                        <button
                            v-for="tab in tabs"
                            :key="tab.id"
                            @click="activeTab = tab.id"
                            :class="[
                                'flex-1 py-3 px-4 text-sm font-medium border-b-2 transition-colors',
                                activeTab === tab.id
                                    ? 'border-purple-600 text-purple-600 bg-purple-50'
                                    : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50'
                            ]"
                        >
                            <i :class="tab.icon" class="ml-2"></i>
                            {{ tab.name }}
                        </button>
                    </div>
                </div>

                <!-- Tab Content -->
                <div class="flex-1 overflow-hidden">
                    <!-- Elements Tab -->
                    <div v-if="activeTab === 'elements'" class="h-full">
                        <ElementsPanel
                            @add-element="addElement"
                            @add-background="setCanvasBackground"
                            @add-image="addImageElement"
                        />
                    </div>

                    <!-- Layers Tab -->
                    <div v-if="activeTab === 'layers'" class="h-full">
                        <LayersPanel
                            :layers="layers"
                            :selected-layer-id="selectedElement?.id"
                            @select-layer="selectLayer"
                            @request-delete-layer="onRequestDeleteLayer"
                            @reorder-layers="reorderLayers"
                            @update-layer="updateLayer"
                            @duplicate-layer="duplicateLayer"
                            @move-layer-top="moveLayerToTop"
                            @move-layer-bottom="moveLayerToBottom"
                        />
                    </div>

                    <!-- Properties Tab -->
                    <div v-if="activeTab === 'properties'" class="h-full">
                        <PropertiesPanel
                            :selected-element="selectedElement"
                            @update-properties="updateProperties"
                            @move-layer="onMoveLayer"
                            @duplicate-element="onDuplicateElement"
                            @request-delete="onRequestDeleteElement"
                        />
                    </div>
                </div>
            </div>

            <!-- Canvas Area -->
            <div class="flex-1 bg-gray-50 flex flex-col">
                <!-- Canvas Toolbar -->
                <div class="flex-shrink-0 bg-white border-b border-gray-200 p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4 space-x-reverse">
                            <!-- Zoom Controls -->
                            <div class="flex items-center space-x-2 space-x-reverse">
                                <button
                                    @click="zoomOut"
                                    class="p-2 text-gray-600 hover:text-gray-800 rounded"
                                >
                                    <i class="fas fa-search-minus"></i>
                                </button>
                                <span class="text-sm text-gray-600 min-w-16 text-center">{{ Math.round(zoom * 100) }}%</span>
                                <button
                                    @click="zoomIn"
                                    class="p-2 text-gray-600 hover:text-gray-800 rounded"
                                >
                                    <i class="fas fa-search-plus"></i>
                                </button>
                            </div>
                            <!-- Watermark Notice (Non-removable for clients) -->
                            <div class="flex items-center bg-purple-50 text-purple-700 px-3 py-1 rounded-lg text-sm">
                                <i class="fas fa-shield-alt ml-2"></i>
                                <span>علامة مائية محمية</span>
                            </div>

                            <!-- BOUTONS DE TEST ET DIAGNOSTIC -->
                            <div class="flex items-center space-x-2 space-x-reverse">
                                <button
                                    @click="runFullDiagnostic"
                                    class="px-3 py-1 bg-blue-500 text-white rounded text-sm hover:bg-blue-600"
                                    title="Diagnostic complet avec logs"
                                >
                                    🔍 Diagnostic
                                </button>
                                <button
                                    @click="addTestElements"
                                    class="px-3 py-1 bg-green-500 text-white rounded text-sm hover:bg-green-600"
                                    title="Ajouter des éléments de test"
                                >
                                    Test Complet
                                </button>
                                <button
                                    @click="debugExport"
                                    class="px-3 py-1 bg-orange-500 text-white rounded text-sm hover:bg-orange-600"
                                    title="Export avec debug détaillé"
                                >
                                    🐛 Debug Export
                                </button>
                                <button
                                    @click="comparePreviewAndExport"
                                    class="px-3 py-1 bg-purple-500 text-white rounded text-sm hover:bg-purple-600"
                                    title="Comparer prévisualisation et export"
                                >
                                    ⚖️ Comparer
                                </button>
                            </div>

                            <!-- BOUTON DE TEST TEMPORAIRE -->
                            <button
                                @click="addTestDiamond"
                                class="px-3 py-1 bg-red-500 text-white rounded text-sm hover:bg-red-600"
                                title="Ajouter un losange noir pour test"
                            >
                                Test ◆
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Canvas Container -->
                <div class="flex-1 overflow-auto p-8 flex items-center justify-center">
                    <div
                        ref="canvasContainer"
                        class="relative bg-white shadow-lg"
                        :style="{
                            width: canvasWidth * zoom + 'px',
                            height: canvasHeight * zoom + 'px',
                            transform: `scale(${zoom})`,
                            transformOrigin: 'top left'
                        }"
                    >
                        <!-- Grid Overlay -->
                        <div
                            v-if="showGrid"
                            class="absolute inset-0 pointer-events-none"
                            :style="gridStyle"
                        ></div>

                        <!-- Design Canvas -->
                        <div
                            ref="designCanvas"
                            class="w-full h-full relative overflow-hidden"
                            :style="{
                                backgroundImage: canvasBackground ? `url(${canvasBackground})` : 'none',
                                backgroundSize: backgroundSize,
                                backgroundPosition: 'center',
                                backgroundRepeat: 'no-repeat',
                                cursor: pendingElementType ? 'crosshair' : 'default'
                            }"
                            @click="handleCanvasClick"
                            @drop="handleDrop"
                            @dragover.prevent
                        >
                            <!-- Render elements here -->
                            <DesignElement
                                v-for="element in elements"
                                :key="element.id"
                                :element="element"
                                :selected="selectedElement?.id === element.id"
                                :zoom="zoom"
                                :placementMode="!!pendingElementType"
                                @select="selectElement"
                                @update="updateElement"
                                @delete="deleteElement"
                            />

                            <!-- Watermark - Large diagonal like admin -->
                            <div
                                class="absolute pointer-events-none select-none"
                                :style="{
                                    top: '50%',
                                    left: '50%',
                                    transform: 'translate(-50%, -50%) rotate(-45deg)',
                                    fontSize: '48px',
                                    color: 'rgba(0, 0, 0, 0.08)',
                                    fontFamily: 'Cairo, sans-serif',
                                    fontWeight: 'bold',
                                    textShadow: '1px 1px 2px rgba(255, 255, 255, 0.5)',
                                    zIndex: 9999,
                                    userSelect: 'none',
                                    WebkitUserSelect: 'none',
                                    MozUserSelect: 'none',
                                    msUserSelect: 'none',
                                    letterSpacing: '4px',
                                    whiteSpace: 'nowrap'
                                }"
                            >
                            منصة سامقة للتصميم
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Photo Editor Modal -->
        <div
            v-if="showPhotoEditor"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
            @click.self="closePhotoEditor"
        >
            <div class="bg-white rounded-lg shadow-xl max-w-6xl max-h-[90vh] w-full mx-4">
                <PhotoEditor
                    :image-element="photoEditorImage"
                    @save="handlePhotoSave"
                />
            </div>
        </div>

        <!-- Loading Overlay -->
        <div v-if="loading" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-40">
            <div class="bg-white rounded-lg p-8 flex items-center space-x-4 space-x-reverse">
                <i class="fas fa-spinner fa-spin text-2xl text-purple-600"></i>
                <span class="text-lg font-medium">{{ loadingMessage }}</span>
            </div>
        </div>

        <!-- Add a modal for delete confirmation -->
        <template v-if="showDeleteConfirm">
            <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg shadow-lg p-8 max-w-sm w-full text-center">
                    <h2 class="text-lg font-bold mb-4">تأكيد الحذف</h2>
                    <p class="mb-6">هل أنت متأكد أنك تريد حذف هذا العنصر؟ لا يمكن التراجع عن هذا الإجراء.</p>
                    <div class="flex justify-center gap-4">
                        <button @click="performDeleteLayer" class="bg-red-600 text-white px-6 py-2 rounded hover:bg-red-700">حذف</button>
                        <button @click="cancelDeleteLayer" class="bg-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-400">إلغاء</button>
                    </div>
                </div>
            </div>
        </template>

        <!-- Export Modal -->
        <template v-if="showExportModal">
            <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg shadow-xl p-8 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
                    <h2 class="text-xl font-bold mb-6 text-center">تصدير التصميم</h2>

                    <!-- Preset Format Selection -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">اختر الحجم والجودة:</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-60 overflow-y-auto">
                            <button
                                v-for="preset in presetFormats"
                                :key="preset.value"
                                @click="selectedPresetFormat = preset.value"
                                :class="[
                                    'p-3 border-2 rounded-lg text-right transition-all',
                                    selectedPresetFormat === preset.value
                                        ? 'border-purple-500 bg-purple-50 text-purple-700'
                                        : 'border-gray-200 hover:border-gray-300'
                                ]"
                            >
                                <div class="font-medium text-sm">{{ preset.label }}</div>
                                <div class="text-xs text-gray-500 mt-1">{{ preset.description }}</div>
                                <div v-if="preset.width && preset.height" class="text-xs text-purple-600 mt-1">
                                    {{ preset.width }} × {{ preset.height }} ({{ preset.dpi }} DPI)
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- Format Selection -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">اختر صيغة التصدير:</label>
                        <div class="grid grid-cols-2 gap-3">
                            <button
                                v-for="format in exportFormats"
                                :key="format.value"
                                @click="selectedExportFormat = format.value"
                                :class="[
                                    'p-4 border-2 rounded-lg text-center transition-all',
                                    selectedExportFormat === format.value
                                        ? 'border-purple-500 bg-purple-50 text-purple-700'
                                        : 'border-gray-200 hover:border-gray-300'
                                ]"
                            >
                                <i :class="format.icon" class="text-2xl mb-2 block"></i>
                                <div class="font-medium">{{ format.label }}</div>
                                <div class="text-xs text-gray-500">{{ format.description }}</div>
                            </button>
                        </div>
                    </div>

                    <!-- Quality Settings for JPEG -->
                    <div v-if="selectedExportFormat === 'jpeg'" class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">جودة الصورة:</label>
                        <input
                            v-model="exportQuality"
                            type="range"
                            min="10"
                            max="100"
                            step="10"
                            class="w-full"
                        >
                        <div class="text-center text-sm text-gray-600 mt-1">{{ exportQuality }}%</div>
                    </div>

                    <!-- Preview Info -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <h3 class="font-medium text-gray-700 mb-2">معلومات التصدير:</h3>
                        <div class="text-sm text-gray-600 space-y-1">
                            <div>الصيغة: {{ exportFormats.find(f => f.value === selectedExportFormat)?.label }}</div>
                            <div>الحجم: {{ getExportDimensions() }}</div>
                            <div v-if="selectedExportFormat === 'jpeg'">الجودة: {{ exportQuality }}%</div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-center gap-4">
                        <button
                            @click="showExportModal = false"
                            class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
                        >
                            إلغاء
                        </button>
                        <button
                            @click="exportDesignWithFormat"
                            :disabled="isExporting"
                            class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 disabled:opacity-50"
                        >
                            <span v-if="isExporting">جاري التصدير...</span>
                            <span v-else>تصدير</span>
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed, nextTick } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import PhotoEditor from '@/Components/DesignEditor/PhotoEditor.vue'
import ElementsPanel from '@/Components/DesignEditor/ElementsPanel.vue'
import LayersPanel from '@/Components/DesignEditor/LayersPanel.vue'
import PropertiesPanel from '@/Components/DesignEditor/PropertiesPanel.vue'
import DesignElement from '@/Components/DesignEditor/DesignElement.vue'

// Props
const props = defineProps({
    template: {
        type: Object,
        required: true
    },
    clientTemplate: {
        type: Object,
        default: null
    },
    mode: {
        type: String,
        default: 'create'
    }
})

// State
const activeTab = ref('elements')
const currentDevice = ref('desktop')
const zoom = ref(1)
const showGrid = ref(false)
const snapToGrid = ref(false)
const canvasWidth = ref(800)
const canvasHeight = ref(600)
const saving = ref(false)
const loading = ref(false)
const loadingMessage = ref('')
const showPhotoEditor = ref(false)
const photoEditorImage = ref(null)
const canvasBackground = ref('')
const backgroundSize = ref('contain')
const showWatermark = ref(true) // Always true for clients - non-removable
const pendingElementType = ref(null)
const showDeleteConfirm = ref(false)
const layerToDelete = ref(null)

// Export state
const showExportModal = ref(false)
const selectedExportFormat = ref('png')
const selectedPresetFormat = ref('current')
const exportQuality = ref(90)
const isExporting = ref(false)

// Design state
const elements = ref([])
const selectedElement = ref(null)
const history = ref([])
const historyIndex = ref(-1)

// Refs
const canvasContainer = ref(null)
const designCanvas = ref(null)

// Configuration
const tabs = [
    { id: 'elements', name: 'العناصر', icon: 'fas fa-shapes' },
    { id: 'layers', name: 'الطبقات', icon: 'fas fa-layer-group' },
    { id: 'properties', name: 'الخصائص', icon: 'fas fa-cog' }
]

// Export formats configuration
const exportFormats = [
    {
        value: 'png',
        label: 'PNG',
        description: 'جودة عالية مع شفافية',
        icon: 'fas fa-file-image'
    },
    {
        value: 'jpeg',
        label: 'JPEG',
        description: 'حجم أصغر للصور',
        icon: 'fas fa-image'
    },
    {
        value: 'svg',
        label: 'SVG',
        description: 'رسوميات متجهة قابلة للتكبير',
        icon: 'fas fa-vector-square'
    },
    {
        value: 'pdf',
        label: 'PDF',
        description: 'مستند قابل للطباعة',
        icon: 'fas fa-file-pdf'
    }
]

// Formats prédéfinis avec dimensions optimisées
const presetFormats = [
    {
        value: 'current',
        label: 'الحجم الحالي',
        description: 'استخدام أبعاد التصميم الحالية',
        width: null,
        height: null,
        dpi: 72
    },
    {
        value: 'a4-portrait',
        label: 'A4 عمودي',
        description: '210 × 297 مم - جودة طباعة عالية',
        width: 2480,
        height: 3508,
        dpi: 300
    },
    {
        value: 'a4-landscape',
        label: 'A4 أفقي',
        description: '297 × 210 مم - جودة طباعة عالية',
        width: 3508,
        height: 2480,
        dpi: 300
    },
    {
        value: 'a5-portrait',
        label: 'A5 عمودي',
        description: '148 × 210 مم - مناسب للبروشورات',
        width: 1748,
        height: 2480,
        dpi: 300
    },
    {
        value: 'a5-landscape',
        label: 'A5 أفقي',
        description: '210 × 148 مم - مناسب للبروشورات',
        width: 2480,
        height: 1748,
        dpi: 300
    },
    {
        value: 'hd-169',
        label: '16:9 HD',
        description: '1920 × 1080 بكسل - مناسب للعروض',
        width: 1920,
        height: 1080,
        dpi: 72
    },
    {
        value: '4k-169',
        label: '16:9 4K',
        description: '3840 × 2160 بكسل - جودة فائقة',
        width: 3840,
        height: 2160,
        dpi: 72
    },
    {
        value: 'square-hd',
        label: 'مربع HD',
        description: '1080 × 1080 بكسل - مناسب لوسائل التواصل',
        width: 1080,
        height: 1080,
        dpi: 72
    },
    {
        value: 'instagram-post',
        label: 'منشور إنستغرام',
        description: '1080 × 1080 بكسل - مُحسَّن للإنستغرام',
        width: 1080,
        height: 1080,
        dpi: 72
    },
    {
        value: 'instagram-story',
        label: 'ستوري إنستغرام',
        description: '1080 × 1920 بكسل - مُحسَّن للستوريز',
        width: 1080,
        height: 1920,
        dpi: 72
    },
    {
        value: 'facebook-cover',
        label: 'غلاف فيسبوك',
        description: '1200 × 630 بكسل - مُحسَّن لفيسبوك',
        width: 1200,
        height: 630,
        dpi: 72
    },
    {
        value: 'business-card',
        label: 'بطاقة عمل',
        description: '89 × 51 مم - حجم بطاقة عمل قياسي',
        width: 1050,
        height: 600,
        dpi: 300
    }
]

const devices = [
    { id: 'desktop', name: 'سطح المكتب', icon: 'fas fa-desktop' },
    { id: 'tablet', name: 'تابلت', icon: 'fas fa-tablet-alt' },
    { id: 'mobile', name: 'موبايل', icon: 'fas fa-mobile-alt' }
]

// Computed
const canUndo = computed(() => historyIndex.value > 0)
const canRedo = computed(() => historyIndex.value < history.value.length - 1)

const layers = computed(() => {
    return elements.value.map((element, index) => ({
        id: element.id,
        name: element.name || `عنصر ${index + 1}`,
        type: element.type,
        visible: element.visible !== false,
        locked: element.locked || false
    }))
})

const gridStyle = computed(() => {
    const gridSize = 20 * zoom.value
    return {
        backgroundImage: `
            linear-gradient(to right, #e5e7eb 1px, transparent 1px),
            linear-gradient(to bottom, #e5e7eb 1px, transparent 1px)
        `,
        backgroundSize: `${gridSize}px ${gridSize}px`
    }
})

// Methods
const setDevice = (device) => {
    currentDevice.value = device

    // Update canvas size based on device
    switch (device) {
        case 'desktop':
            canvasWidth.value = 1200
            canvasHeight.value = 800
            break
        case 'tablet':
            canvasWidth.value = 768
            canvasHeight.value = 1024
            break
        case 'mobile':
            canvasWidth.value = 375
            canvasHeight.value = 667
            break
    }
}

const zoomIn = () => {
    zoom.value = Math.min(zoom.value + 0.1, 3)
}

const zoomOut = () => {
    zoom.value = Math.max(zoom.value - 0.1, 0.1)
}

const resetZoom = () => {
    zoom.value = 1
}

const toggleGrid = () => {
    showGrid.value = !showGrid.value
}

const toggleSnap = () => {
    snapToGrid.value = !snapToGrid.value
}

// Update addElement to use placement mode for non-image elements
const addElement = (elementData) => {
    if (elementData.type === 'image') {
        // Add image immediately at center
        const newElement = {
            id: generateId(),
            type: elementData.type,
            name: elementData.name,
            x: canvasWidth.value / 2 - 100,
            y: canvasHeight.value / 2 - 100,
            width: elementData.width || 200,
            height: elementData.height || 100,
            rotation: 0,
            opacity: 1,
            visible: true,
            locked: false,
            zIndex: elements.value.length,
            ...elementData.properties
        }
        elements.value.push(newElement)
        selectedElement.value = newElement
        saveToHistory()
    } else {
        // Enter placement mode
        pendingElementType.value = elementData
    }
}

const addImage = (file) => {
    let newElement

    if (file.is_canvas_background) {
        // Set as canvas background (CSS background)
        setCanvasBackground(file.url)
        return
    } else if (file.is_background) {
        // Add as background image element (full canvas size)
        newElement = {
            id: generateId(),
            type: 'image',
            name: 'صورة خلفية - ' + file.filename,
            x: 0,
            y: 0,
            width: canvasWidth.value,
            height: canvasHeight.value,
            rotation: 0,
            opacity: 1,
            visible: true,
            locked: false,
            zIndex: 0, // Background should be at the bottom
            src: file.url,
            isBackground: true,
            originalWidth: file.width || canvasWidth.value,
            originalHeight: file.height || canvasHeight.value
        }

        // Move all existing elements up one z-index
        elements.value.forEach(el => el.zIndex++)
    } else {
        // Add as regular image
        newElement = {
            id: generateId(),
            type: 'image',
            name: file.filename,
            x: 50,
            y: 50,
            width: Math.min(300, canvasWidth.value - 100),
            height: Math.min(200, canvasHeight.value - 100),
            rotation: 0,
            opacity: 1,
            visible: true,
            locked: false,
            zIndex: elements.value.length,
            src: file.url,
            originalWidth: file.metadata?.dimensions?.width || 300,
            originalHeight: file.metadata?.dimensions?.height || 200
        }
    }

    elements.value.push(newElement)
    selectedElement.value = newElement
    saveToHistory()
}

const selectElement = (element) => {
    // Always assign a deep copy to trigger reactivity and watcher
    selectedElement.value = {
        ...element,
        properties: element.properties ? { ...element.properties } : undefined
    };
    activeTab.value = 'properties'
}

const selectLayer = (layerId) => {
    const element = elements.value.find(el => el.id === layerId)
    if (element) {
        // Always assign a deep copy to trigger reactivity and watcher
        selectedElement.value = {
            ...element,
            properties: element.properties ? { ...element.properties } : undefined
        };
        activeTab.value = 'properties'
    }
}

const updateElement = (elementId, updates) => {
    const elementIndex = elements.value.findIndex(el => el.id === elementId)
    if (elementIndex !== -1) {
        elements.value[elementIndex] = { ...elements.value[elementIndex], ...updates }
        saveToHistory()
    }
}

const deleteElement = (elementId) => {
    const elementIndex = elements.value.findIndex(el => el.id === elementId)
    if (elementIndex !== -1) {
        elements.value.splice(elementIndex, 1)
        if (selectedElement.value?.id === elementId) {
            selectedElement.value = null
        }
        saveToHistory()
    }
}

const deleteLayer = (layerId) => {
    deleteElement(layerId)
}

const reorderLayers = (newOrder) => {
    // Reorder elements based on layer order
    const reorderedElements = newOrder.map(layerId =>
        elements.value.find(el => el.id === layerId)
    ).filter(Boolean)

    elements.value = reorderedElements
    updateAllZIndices()
    saveToHistory()
}

// Update handleCanvasClick to place pending element
const handleCanvasClick = (event) => {
    if (pendingElementType.value) {
        // Get click coordinates relative to canvas
        const rect = designCanvas.value.getBoundingClientRect()
        const x = (event.clientX - rect.left) / zoom.value
        const y = (event.clientY - rect.top) / zoom.value
        const elementData = pendingElementType.value
        const newElement = {
            id: generateId(),
            type: elementData.type,
            name: elementData.name,
            x: x,
            y: y,
            width: elementData.width || 200,
            height: elementData.height || 100,
            rotation: 0,
            opacity: 1,
            visible: true,
            locked: false,
            zIndex: elements.value.length,
            ...elementData.properties
        }
        elements.value.push(newElement)
        selectedElement.value = newElement
        saveToHistory()
        pendingElementType.value = null
    } else if (event.target === designCanvas.value) {
        selectedElement.value = null
    }
}

const handleDrop = (event) => {
    event.preventDefault()
    // Handle file drops
    const files = Array.from(event.dataTransfer.files)
    files.forEach(file => {
        if (file.type.startsWith('image/')) {
            // Handle image drop
            const reader = new FileReader()
            reader.onload = (e) => {
                const newElement = {
                    id: generateId(),
                    type: 'image',
                    name: file.name,
                    x: event.offsetX,
                    y: event.offsetY,
                    width: 300,
                    height: 200,
                    rotation: 0,
                    opacity: 1,
                    visible: true,
                    locked: false,
                    zIndex: elements.value.length,
                    src: e.target.result
                }

                elements.value.push(newElement)
                selectedElement.value = newElement
                saveToHistory()
            }
            reader.readAsDataURL(file)
        }
    })
}

const saveToHistory = () => {
    // Remove any future history if we're not at the end
    history.value = history.value.slice(0, historyIndex.value + 1)

    // Add current state
    history.value.push(JSON.parse(JSON.stringify(elements.value)))
    historyIndex.value++

    // Limit history size
    if (history.value.length > 50) {
        history.value.shift()
        historyIndex.value--
    }
}

const undo = () => {
    if (canUndo.value) {
        historyIndex.value--
        elements.value = JSON.parse(JSON.stringify(history.value[historyIndex.value]))
        selectedElement.value = null
    }
}

const redo = () => {
    if (canRedo.value) {
        historyIndex.value++
        elements.value = JSON.parse(JSON.stringify(history.value[historyIndex.value]))
        selectedElement.value = null
    }
}

const generateId = () => {
    return 'element_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9)
}

// Utility function to compress base64 images
const compressBase64Image = (base64String, maxWidth = 800, quality = 0.7) => {
    return new Promise((resolve) => {
        if (!base64String.startsWith('data:image/')) {
            resolve(base64String)
            return
        }

        const img = new Image()
        img.onload = () => {
            const canvas = document.createElement('canvas')
            const ctx = canvas.getContext('2d')

            // Calculate new dimensions with more aggressive compression for large images
            let { width, height } = img
            const originalSize = base64String.length

            // More aggressive compression for very large images
            if (originalSize > 1000000) { // > 1MB
                maxWidth = 600
                quality = 0.5
            } else if (originalSize > 500000) { // > 500KB
                maxWidth = 700
                quality = 0.6
            }

            if (width > maxWidth || height > maxWidth) {
                if (width > height) {
                    height = (height * maxWidth) / width
                    width = maxWidth
                } else {
                    width = (width * maxWidth) / height
                    height = maxWidth
                }
            }

            canvas.width = width
            canvas.height = height

            // Draw and compress
            ctx.drawImage(img, 0, 0, width, height)
            const compressedBase64 = canvas.toDataURL('image/jpeg', quality)

            console.log(`Image compressed: ${Math.round(originalSize/1024)}KB -> ${Math.round(compressedBase64.length/1024)}KB`)
            resolve(compressedBase64)
        }
        img.src = base64String
    })
}

const saveDesign = async () => {
    saving.value = true

    try {
        // Check if template ID exists
        if (!props.template?.id) {
            throw new Error('Template ID is missing')
        }

        // Check CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content
        if (!csrfToken) {
            throw new Error('CSRF token not found')
        }

        // Compress images in elements before saving
        console.log('Compressing images...')
        const compressedElements = await Promise.all(
            (elements.value || []).map(async (element) => {
                if (element.type === 'image' && element.src && element.src.startsWith('data:image/')) {
                    const compressedSrc = await compressBase64Image(element.src)
                    return { ...element, src: compressedSrc }
                }
                return element
            })
        )
        console.log('Image compression complete')

        const designData = {
            elements: compressedElements,
            canvas: {
                width: canvasWidth.value || 800,
                height: canvasHeight.value || 600,
                background: canvasBackground.value || '',
                backgroundSize: backgroundSize.value || 'contain'
            },
            settings: {
                device: currentDevice.value || 'desktop',
                zoom: zoom.value || 1
            },
            watermark: {
                text: 'سامقة للتصميم',
                enabled: true,
                position: 'bottom-right',
                style: {
                    fontSize: '14px',
                    color: 'rgba(0, 0, 0, 0.25)',
                    fontFamily: 'Cairo, sans-serif',
                    fontWeight: 'bold',
                    rotation: '-15deg'
                }
            }
        }

        // Validate design data and check size
        let designDataString
        try {
            designDataString = JSON.stringify(designData)

            // Check data size (16MB limit)
            const dataSize = new Blob([designDataString]).size
            if (dataSize > 16777215) {
                throw new Error(`بيانات التصميم كبيرة جداً (${Math.round(dataSize / 1024 / 1024)}MB). يرجى تقليل حجم الصور.`)
            }
        } catch (e) {
            throw new Error('Invalid design data: ' + e.message)
        }

        console.log('Saving design data:', designData)
        console.log('Template ID:', props.template.id)
        console.log('Data size:', Math.round(new Blob([designDataString]).size / 1024), 'KB')

        const requestPayload = {
            design_data: designDataString
        }
        console.log('Request payload size:', Math.round(new Blob([JSON.stringify(requestPayload)]).size / 1024), 'KB')

        const response = await fetch(`/client/templates/${props.template.id}/design`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(requestPayload)
        })

        if (response.ok) {
            const result = await response.json()
            console.log('Design saved successfully', result)

            // Show success notification
            if (result.success) {
                alert('تم حفظ التصميم بنجاح')
            }
        } else {
            // Get detailed error information
            let errorMessage = 'Failed to save design'
            try {
                const errorData = await response.json()
                errorMessage = errorData.message || errorMessage
                console.error('Server error details:', errorData)
            } catch (e) {
                console.error('Could not parse error response:', e)
            }

            console.error('HTTP Status:', response.status, response.statusText)
            throw new Error(errorMessage)
        }
    } catch (error) {
        console.error('Error saving design:', error)

        // Show more detailed error information
        let errorMessage = 'خطأ في حفظ التصميم'
        if (error.message) {
            errorMessage += ': ' + error.message
        }

        alert(errorMessage)
    } finally {
        saving.value = false
    }
}

const previewDesign = () => {
    // Open preview in new window
    const previewData = {
        elements: elements.value,
        canvas: {
            width: canvasWidth.value,
            height: canvasHeight.value
        }
    }

    const previewWindow = window.open('', '_blank')
    previewWindow.document.write(generatePreviewHTML(previewData))
}

const generatePreviewHTML = (designData) => {
    // Generate HTML for preview
    let html = `
        <!DOCTYPE html>
        <html dir="rtl">
        <head>
            <meta charset="UTF-8">
            <title>معاينة التصميم</title>
            <style>
                @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap');
                @import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');
                body {
                    margin: 0;
                    padding: 20px;
                    font-family: 'Cairo', sans-serif;
                    background: #f5f5f5;
                }
                .canvas {
                    width: ${designData.canvas.width}px;
                    height: ${designData.canvas.height}px;
                    position: relative;
                    background: white;
                    border: 1px solid #ddd;
                    margin: 0 auto;
                    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                    ${canvasBackground.value ? `
                        background-image: url(${canvasBackground.value});
                        background-size: ${backgroundSize.value};
                        background-position: center;
                        background-repeat: no-repeat;
                    ` : ''}
                }
                .element { position: absolute; }
                h2 { text-align: center; color: #333; margin-bottom: 20px; }
                .footer {
                    text-align: center;
                    margin-top: 20px;
                    color: #666;
                    font-size: 12px;
                }
            </style>
        </head>
        <body>
            <h2>معاينة التصميم - سامقة</h2>
            <div class="canvas">
    `

    designData.elements.forEach(element => {
        if (element.visible !== false) {
            html += generateElementHTML(element)
        }
    })

    html += `
                <!-- Always show watermark in preview -->
                <div style="
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    font-size: 48px;
                    color: rgba(0, 0, 0, 0.08);
                    font-family: 'Cairo', sans-serif;
                    font-weight: bold;
                    z-index: 9999;
                    transform: translate(-50%, -50%) rotate(-45deg);
                    transform-origin: center;
                    user-select: none;
                    letter-spacing: 2px;
                    pointer-events: none;
                    white-space: nowrap;
                ">منصة سامقة للتصميم</div>
            </div>
            <div class="footer">
                <p>© ${new Date().getFullYear()} سامقة للتصميم - جميع الحقوق محفوظة</p>
                <p>تم إنشاء هذا التصميم باستخدام منصة سامقة للتصميم</p>
            </div>
        </body>
        </html>
    `

    return html
}

const generateElementHTML = (element) => {
    const style = `
        left: ${element.x}px;
        top: ${element.y}px;
        width: ${element.width}px;
        height: ${element.height}px;
        transform: rotate(${element.rotation}deg);
        opacity: ${element.opacity};
        z-index: ${element.zIndex};
    `

    switch (element.type) {
        case 'text':
            return `<div class="element" style="${style} font-size: ${element.fontSize || 16}px; color: ${element.color || '#000'}; font-weight: ${element.fontWeight || 'normal'};">${element.text || 'نص'}</div>`
        case 'image':
            return `<img class="element" src="${element.src}" style="${style} object-fit: cover;" alt="${element.name}">`
        case 'rectangle':
            return `<div class="element" style="${style} background-color: ${element.backgroundColor || '#000'}; border-radius: ${element.borderRadius || 0}px;"></div>`
        case 'circle':
            return `<div class="element" style="${style} background-color: ${element.backgroundColor || '#10b981'}; border-radius: 50%;"></div>`
        case 'shape':
            // CORRECTION: Utiliser des formes CSS au lieu d'icônes FontAwesome
            return generateShapeHTML(element, style)
        case 'icon':
            // CORRECTION: Utiliser des symboles simples au lieu d'icônes FontAwesome
            const symbol = getIconSymbol(element.iconClass || 'fas fa-star')
            return `<div class="element" style="${style} display: flex; align-items: center; justify-content: center; background-color: ${element.color || '#374151'}; border-radius: 50%;">
                <span style="font-size: ${(element.fontSize || 24) * 0.6}px; color: white;">${symbol}</span>
            </div>`
        default:
            return ''
    }
}

const getShapeIconClass = (shapeType) => {
    const icons = {
        triangle: 'fas fa-play fa-rotate-90',
        diamond: 'fas fa-diamond',
        star: 'fas fa-star',
        arrow: 'fas fa-arrow-right',
        polygon: 'fas fa-draw-polygon'
    }
    return icons[shapeType] || 'fas fa-square'
}

// Fonction pour dessiner une étoile
const drawStar = (ctx, cx, cy, spikes, outerRadius, innerRadius) => {
    let rot = Math.PI / 2 * 3
    let x = cx
    let y = cy
    const step = Math.PI / spikes

    ctx.beginPath()
    ctx.moveTo(cx, cy - outerRadius)
    for (let i = 0; i < spikes; i++) {
        x = cx + Math.cos(rot) * outerRadius
        y = cy + Math.sin(rot) * outerRadius
        ctx.lineTo(x, y)
        rot += step

        x = cx + Math.cos(rot) * innerRadius
        y = cy + Math.sin(rot) * innerRadius
        ctx.lineTo(x, y)
        rot += step
    }
    ctx.lineTo(cx, cy - outerRadius)
    ctx.closePath()
}





// Fonction pour générer les points d'une étoile pour SVG
const generateStarPoints = (cx, cy, spikes, outerRadius, innerRadius) => {
    let points = []
    let rot = Math.PI / 2 * 3
    const step = Math.PI / spikes

    for (let i = 0; i < spikes; i++) {
        let x = cx + Math.cos(rot) * outerRadius
        let y = cy + Math.sin(rot) * outerRadius
        points.push(`${x},${y}`)
        rot += step

        x = cx + Math.cos(rot) * innerRadius
        y = cy + Math.sin(rot) * innerRadius
        points.push(`${x},${y}`)
        rot += step
    }

    return points.join(' ')
}

// Fonction pour obtenir les dimensions d'export
const getExportDimensions = () => {
    const preset = presetFormats.find(p => p.value === selectedPresetFormat.value)
    if (!preset || preset.value === 'current') {
        return `${canvasWidth.value} × ${canvasHeight.value} بكسل`
    }
    return `${preset.width} × ${preset.height} بكسل (${preset.dpi} DPI)`
}

const loadDesign = () => {
    // Déterminer quelle source de données utiliser
    let designDataSource = null

    if (props.clientTemplate && props.clientTemplate.design_data) {
        // Mode édition : utiliser les données du client template
        designDataSource = props.clientTemplate.design_data
        console.log('Loading from clientTemplate:', designDataSource)
    } else if (props.template.design_data) {
        // Mode création : utiliser les données du template original
        designDataSource = props.template.design_data
        console.log('Loading from template:', designDataSource)
    }

    if (designDataSource) {
        try {
            // Gérer les données qui peuvent être déjà un objet ou une string JSON
            let designData
            if (typeof designDataSource === 'string') {
                designData = JSON.parse(designDataSource)
            } else if (typeof designDataSource === 'object') {
                designData = designDataSource
            } else {
                throw new Error('Invalid design data format')
            }

            elements.value = designData.elements || []

            if (designData.canvas) {
                canvasWidth.value = designData.canvas.width || 800
                canvasHeight.value = designData.canvas.height || 600
                canvasBackground.value = designData.canvas.background || ''
                backgroundSize.value = designData.canvas.backgroundSize || 'contain'
            }

            if (designData.settings) {
                currentDevice.value = designData.settings.device || 'desktop'
                zoom.value = designData.settings.zoom || 1
            }

            console.log('Design loaded successfully:', {
                elementsCount: elements.value.length,
                canvas: { width: canvasWidth.value, height: canvasHeight.value },
                mode: props.mode
            })

            // Initialize history
            saveToHistory()
        } catch (error) {
            console.error('Error loading design:', error)
            // Initialize with empty design on error
            elements.value = []
            saveToHistory()
        }
    } else {
        console.log('No design data found, initializing empty design')
        // Initialize with empty design
        elements.value = []
        saveToHistory()
    }
}

const openPhotoEditor = (imageElement) => {
    photoEditorImage.value = imageElement
    showPhotoEditor.value = true
}

const closePhotoEditor = () => {
    showPhotoEditor.value = false
    photoEditorImage.value = null
}

const handlePhotoSave = (blob) => {
    // Handle saving edited photo
    console.log('Photo edited:', blob)
    closePhotoEditor()
}

const addQuickText = () => {
    const newElement = {
        id: generateId(),
        type: 'text',
        name: 'نص سريع',
        x: canvasWidth.value / 2 - 100,
        y: canvasHeight.value / 2 - 25,
        width: 200,
        height: 50,
        rotation: 0,
        opacity: 1,
        visible: true,
        locked: false,
        zIndex: elements.value.length,
        text: 'اكتب النص هنا',
        fontSize: 24,
        fontFamily: 'Cairo',
        fontWeight: 'bold',
        color: '#ffffff',
        textAlign: 'center',
        lineHeight: 1.5
    }

    elements.value.push(newElement)
    selectedElement.value = newElement
    saveToHistory()
}

const addQuickShape = () => {
    const newElement = {
        id: generateId(),
        type: 'rectangle',
        name: 'شكل سريع',
        x: canvasWidth.value / 2 - 75,
        y: canvasHeight.value / 2 - 50,
        width: 150,
        height: 100,
        rotation: 0,
        opacity: 0.8,
        visible: true,
        locked: false,
        zIndex: elements.value.length,
        backgroundColor: '#8b5cf6',
        borderRadius: 10,
        borderWidth: 0
    }

    elements.value.push(newElement)
    selectedElement.value = newElement
    saveToHistory()
}

// Fonction de test pour ajouter un losange noir comme dans votre exemple
const addTestDiamond = () => {
    const newElement = {
        id: generateId(),
        type: 'shape',
        shapeType: 'diamond',
        name: 'Losange Test',
        x: canvasWidth.value - 100,  // En bas à droite
        y: canvasHeight.value - 100,
        width: 60,
        height: 60,
        rotation: 0,
        opacity: 1,
        visible: true,
        locked: false,
        zIndex: elements.value.length,
        backgroundColor: '#000000'  // Noir
    }
    elements.value.push(newElement)
    selectedElement.value = newElement
    saveToHistory()
    console.log('Losange noir ajouté pour test:', newElement)
}

// Fonction de test complète pour reproduire votre exemple
const addTestElements = () => {
    // Cercle vert
    const circle = {
        id: generateId(),
        type: 'circle',
        name: 'Cercle Vert',
        x: 50,
        y: 50,
        width: 100,
        height: 100,
        rotation: 0,
        opacity: 1,
        visible: true,
        locked: false,
        zIndex: elements.value.length,
        backgroundColor: '#10b981'
    }
    elements.value.push(circle)

    // Rectangle violet
    const rectangle = {
        id: generateId(),
        type: 'rectangle',
        name: 'Rectangle Violet',
        x: 600,
        y: 80,
        width: 150,
        height: 80,
        rotation: 0,
        opacity: 1,
        visible: true,
        locked: false,
        zIndex: elements.value.length,
        backgroundColor: '#8b5cf6'
    }
    elements.value.push(rectangle)

    // Losange noir
    const diamond = {
        id: generateId(),
        type: 'shape',
        shapeType: 'diamond',
        name: 'Losange Noir',
        x: canvasWidth.value - 80,
        y: canvasHeight.value - 80,
        width: 50,
        height: 50,
        rotation: 0,
        opacity: 1,
        visible: true,
        locked: false,
        zIndex: elements.value.length,
        backgroundColor: '#000000'
    }
    elements.value.push(diamond)

    // Texte
    const text = {
        id: generateId(),
        type: 'text',
        name: 'Texte Test',
        x: 300,
        y: 400,
        width: 200,
        height: 50,
        rotation: 0,
        opacity: 1,
        visible: true,
        locked: false,
        zIndex: elements.value.length,
        text: 'qsdasdasdasd',
        fontSize: 24,
        color: '#333333',
        fontFamily: 'Cairo, sans-serif'
    }
    elements.value.push(text)

    saveToHistory()
    console.log('Éléments de test ajoutés:', elements.value.length)
}

// ===== FONCTIONS DE DIAGNOSTIC DÉTAILLÉ =====

const runFullDiagnostic = () => {
    console.log('🔍 ===== DIAGNOSTIC COMPLET DÉMARRÉ =====')

    // 1. État général
    console.log('📊 ÉTAT GÉNÉRAL:')
    console.log('- Nombre d\'éléments:', elements.value.length)
    console.log('- Canvas dimensions:', `${canvasWidth.value}x${canvasHeight.value}`)
    console.log('- Zoom actuel:', zoom.value)
    console.log('- Onglet actif:', activeTab.value)
    console.log('- Preset sélectionné:', selectedPresetFormat.value)
    console.log('- Format d\'export:', selectedExportFormat.value)

    // 2. Détail des éléments
    console.log('📋 DÉTAIL DES ÉLÉMENTS:')
    elements.value.forEach((element, index) => {
        console.log(`  ${index + 1}. ${element.type} (${element.name || 'Sans nom'})`)
        console.log(`     - ID: ${element.id}`)
        console.log(`     - Position: x=${element.x}, y=${element.y}`)
        console.log(`     - Taille: ${element.width}x${element.height}`)
        console.log(`     - Visible: ${element.visible}`)
        console.log(`     - Propriétés spéciales:`, element.shapeType || element.iconClass || element.text || 'Aucune')
        console.log('     - Objet complet:', element)
    })

    // 3. Vérification des composants
    console.log('🧩 VÉRIFICATION DES COMPOSANTS:')
    console.log('- ElementsPanel importé:', !!ElementsPanel)
    console.log('- Référence designCanvas:', !!designCanvas.value)
    console.log('- Référence canvasContainer:', !!canvasContainer.value)

    // 4. Vérification des sections dans ElementsPanel
    console.log('📦 SECTIONS DISPONIBLES:')
    console.log('- Onglet elements actif:', activeTab.value === 'elements')

    // 5. Test des fonctions d'export
    console.log('⚙️ FONCTIONS D\'EXPORT:')
    console.log('- createExportCanvas disponible:', typeof createExportCanvas === 'function')
    console.log('- createPreviewCanvas disponible:', typeof createPreviewCanvas === 'function')
    console.log('- generatePreviewHTML disponible:', typeof generatePreviewHTML === 'function')

    console.log('🔍 ===== DIAGNOSTIC TERMINÉ =====')

    // Afficher aussi dans l'interface
    alert(`Diagnostic terminé!\n\nÉléments: ${elements.value.length}\nCanvas: ${canvasWidth.value}x${canvasHeight.value}\nOnglet: ${activeTab.value}\n\nVoir la console pour les détails complets.`)
}

const debugExport = async () => {
    console.log('🐛 ===== DEBUG EXPORT DÉMARRÉ =====')

    try {
        console.log('📋 AVANT EXPORT:')
        console.log('- Éléments à exporter:', elements.value.length)
        console.log('- Watermark visible:', showWatermark.value)
        console.log('- Preset actuel:', selectedPresetFormat.value)

        // Sauvegarder l'état original
        const originalWatermarkState = showWatermark.value
        const originalPreset = selectedPresetFormat.value

        // Forcer les bonnes valeurs
        showWatermark.value = true
        selectedPresetFormat.value = 'current'

        console.log('⚙️ PENDANT EXPORT:')
        console.log('- Watermark forcé à:', showWatermark.value)
        console.log('- Preset forcé à:', selectedPresetFormat.value)

        await nextTick()

        // Tester les deux méthodes d'export
        console.log('🎨 TEST MÉTHODE 1 - createExportCanvas:')
        const canvas1 = await createExportCanvas()
        console.log('- Canvas créé:', canvas1.width + 'x' + canvas1.height)
        console.log('- Data URL length:', canvas1.toDataURL().length)

        console.log('🎨 TEST MÉTHODE 2 - createPreviewCanvas:')
        const canvas2 = await createPreviewCanvas()
        console.log('- Canvas créé:', canvas2.width + 'x' + canvas2.height)
        console.log('- Data URL length:', canvas2.toDataURL().length)

        // Comparer les deux canvas
        console.log('⚖️ COMPARAISON:')
        console.log('- Même taille:', canvas1.width === canvas2.width && canvas1.height === canvas2.height)
        console.log('- Même contenu:', canvas1.toDataURL() === canvas2.toDataURL())

        // Télécharger les deux pour comparaison
        downloadCanvas(canvas1, 'debug-methode1')
        setTimeout(() => downloadCanvas(canvas2, 'debug-methode2'), 1000)

        // Restaurer l'état
        showWatermark.value = originalWatermarkState
        selectedPresetFormat.value = originalPreset

        console.log('✅ DEBUG EXPORT TERMINÉ')
        alert('Debug export terminé!\n\nDeux fichiers téléchargés:\n- debug-methode1.png\n- debug-methode2.png\n\nVoir la console pour les détails.')

    } catch (error) {
        console.error('❌ ERREUR PENDANT DEBUG EXPORT:', error)
        alert('Erreur pendant le debug: ' + error.message)
    }
}

const comparePreviewAndExport = async () => {
    console.log('⚖️ ===== COMPARAISON PRÉVISUALISATION/EXPORT =====')

    try {
        // 1. Générer la prévisualisation HTML
        console.log('📄 GÉNÉRATION PRÉVISUALISATION HTML:')
        const previewData = {
            elements: elements.value,
            canvas: {
                width: canvasWidth.value,
                height: canvasHeight.value
            }
        }
        const previewHTML = generatePreviewHTML(previewData)
        console.log('- HTML généré, longueur:', previewHTML.length)
        console.log('- Éléments dans preview:', previewData.elements.length)

        // 2. Créer le canvas d'export
        console.log('🎨 GÉNÉRATION CANVAS EXPORT:')
        const exportCanvas = await createPreviewCanvas()
        console.log('- Canvas créé:', exportCanvas.width + 'x' + exportCanvas.height)

        // 3. Ouvrir la prévisualisation dans une nouvelle fenêtre
        console.log('🪟 OUVERTURE PRÉVISUALISATION:')
        const previewWindow = window.open('', '_blank', 'width=1000,height=800')
        previewWindow.document.write(previewHTML)

        // 4. Télécharger l'export
        console.log('💾 TÉLÉCHARGEMENT EXPORT:')
        downloadCanvas(exportCanvas, 'comparaison-export')

        console.log('✅ COMPARAISON PRÊTE')
        alert('Comparaison prête!\n\n1. Fenêtre de prévisualisation ouverte\n2. Fichier export téléchargé\n\nComparez visuellement les deux!')

    } catch (error) {
        console.error('❌ ERREUR PENDANT COMPARAISON:', error)
        alert('Erreur pendant la comparaison: ' + error.message)
    }
}

function setCanvasBackground(bgDataUrl) {
    canvasBackground.value = bgDataUrl;
}
function addImageElement(imgDataUrl) {
    // Add a new image element to the canvas
    const newElement = {
        id: Date.now(),
        type: 'image',
        src: imgDataUrl,
        x: canvasWidth.value / 2 - 100,
        y: canvasHeight.value / 2 - 100,
        width: 200,
        height: 200,
        name: 'صورة'
    };
    elements.value.push(newElement);
    selectedElement.value = newElement;
}

const removeCanvasBackground = () => {
    canvasBackground.value = ''
    backgroundSize.value = 'contain'
    saveToHistory()
}

// New comprehensive export function
const exportDesignWithFormat = async () => {
    console.log('🚀 ===== EXPORT DESIGN AVEC FORMAT DÉMARRÉ =====')
    console.log('📋 État avant export:')
    console.log('- isExporting:', isExporting.value)
    console.log('- Format sélectionné:', selectedExportFormat.value)
    console.log('- Preset sélectionné:', selectedPresetFormat.value)
    console.log('- Nombre d\'éléments:', elements.value.length)
    console.log('- Watermark visible:', showWatermark.value)
    console.log('- Canvas dimensions:', `${canvasWidth.value}x${canvasHeight.value}`)

    if (isExporting.value) {
        console.log('⚠️ Export déjà en cours, abandon')
        return
    }

    isExporting.value = true

    try {
        // Temporarily ensure watermark is visible
        const originalWatermarkState = showWatermark.value
        showWatermark.value = true
        console.log('✅ Watermark forcé à true (était:', originalWatermarkState, ')')

        // Wait for DOM update
        await nextTick()
        console.log('✅ DOM mis à jour')

        console.log(`🎯 Lancement export format: ${selectedExportFormat.value}`)
        switch (selectedExportFormat.value) {
            case 'png':
                console.log('📸 Appel exportAsPNG()')
                await exportAsPNG()
                break
            case 'jpeg':
                console.log('📸 Appel exportAsJPEG()')
                await exportAsJPEG()
                break
            case 'svg':
                console.log('🎨 Appel exportAsSVG()')
                await exportAsSVG()
                break
            case 'pdf':
                console.log('📄 Appel exportAsPDF()')
                await exportAsPDF()
                break
        }

        // Restore original watermark state
        showWatermark.value = originalWatermarkState
        showExportModal.value = false
        console.log('✅ Watermark restauré et modal fermée')
        console.log('🎉 ===== EXPORT TERMINÉ AVEC SUCCÈS =====')

    } catch (error) {
        console.error('❌ ===== ERREUR PENDANT EXPORT =====')
        console.error('Détails de l\'erreur:', error)
        console.error('Stack trace:', error.stack)
        alert('خطأ في تصدير التصميم: ' + error.message)
    } finally {
        isExporting.value = false
        console.log('🔄 isExporting remis à false')
    }
}

const exportAsPNG = async () => {
    await exportDesignWithWatermarkSimple()
}

const exportAsJPEG = async () => {
    await exportDesignWithWatermarkSimple()
}

// Copie exacte de la fonction admin qui marche
const exportDesignWithWatermark = async () => {
    try {
        // Temporarily ensure watermark is visible
        const originalWatermarkState = showWatermark.value
        showWatermark.value = true

        // Wait for DOM update
        await nextTick()

        // Get canvas element
        const canvas = designCanvas.value
        if (!canvas) return

        // Create a new canvas for export
        const exportCanvas = document.createElement('canvas')
        const ctx = exportCanvas.getContext('2d')

        exportCanvas.width = canvasWidth.value
        exportCanvas.height = canvasHeight.value

        // Fill background
        ctx.fillStyle = '#ffffff'
        ctx.fillRect(0, 0, canvasWidth.value, canvasHeight.value)

        // Draw background image if exists
        if (canvasBackground.value) {
            await new Promise((resolve) => {
                const img = new Image()
                img.crossOrigin = 'anonymous'
                img.onload = () => {
                    ctx.drawImage(img, 0, 0, canvasWidth.value, canvasHeight.value)
                    resolve()
                }
                img.src = canvasBackground.value
            })
        }

        // Draw all elements
        for (const element of elements.value) {
            if (element.visible === false) continue
            switch (element.type) {
                case 'text':
                    ctx.save()
                    ctx.font = `${element.fontWeight || 'normal'} ${element.fontSize || 16}px ${element.fontFamily || 'Cairo, sans-serif'}`
                    ctx.fillStyle = element.color || '#000'
                    ctx.textAlign = element.textAlign || 'left'
                    ctx.globalAlpha = element.opacity !== undefined ? element.opacity : 1
                    ctx.translate(element.x + (element.width || 0) / 2, element.y + (element.height || 0) / 2)
                    ctx.rotate((element.rotation || 0) * Math.PI / 180)
                    ctx.fillText(element.text || '', 0, 0)
                    ctx.restore()
                    break
                case 'image':
                    await new Promise((resolve) => {
                        const img = new Image()
                        img.crossOrigin = 'anonymous'
                        img.onload = () => {
                            ctx.save()
                            ctx.globalAlpha = element.opacity !== undefined ? element.opacity : 1
                            ctx.translate(element.x + (element.width || 0) / 2, element.y + (element.height || 0) / 2)
                            ctx.rotate((element.rotation || 0) * Math.PI / 180)
                            ctx.drawImage(img, -(element.width || 0) / 2, -(element.height || 0) / 2, element.width || 100, element.height || 100)
                            ctx.restore()
                            resolve()
                        }
                        img.src = element.src
                    })
                    break
                case 'rectangle':
                    ctx.save()
                    ctx.globalAlpha = element.opacity !== undefined ? element.opacity : 1
                    ctx.fillStyle = element.backgroundColor || '#000'
                    ctx.translate(element.x + (element.width || 0) / 2, element.y + (element.height || 0) / 2)
                    ctx.rotate((element.rotation || 0) * Math.PI / 180)
                    ctx.fillRect(-(element.width || 0) / 2, -(element.height || 0) / 2, element.width || 100, element.height || 100)
                    ctx.restore()
                    break
                case 'circle':
                    ctx.save()
                    ctx.globalAlpha = element.opacity !== undefined ? element.opacity : 1
                    ctx.fillStyle = element.backgroundColor || '#10b981'
                    ctx.translate(element.x + (element.width || 0) / 2, element.y + (element.height || 0) / 2)
                    ctx.rotate((element.rotation || 0) * Math.PI / 180)
                    ctx.beginPath()
                    ctx.arc(0, 0, Math.min(element.width || 100, element.height || 100) / 2, 0, 2 * Math.PI)
                    ctx.fill()
                    ctx.restore()
                    break
                case 'shape':
                    ctx.save()
                    ctx.globalAlpha = element.opacity !== undefined ? element.opacity : 1
                    ctx.fillStyle = element.backgroundColor || element.color || '#8b5cf6'
                    ctx.translate(element.x + (element.width || 0) / 2, element.y + (element.height || 0) / 2)
                    ctx.rotate((element.rotation || 0) * Math.PI / 180)

                    // Dessiner selon le type de forme
                    switch (element.shapeType) {
                        case 'circle':
                            ctx.beginPath()
                            ctx.arc(0, 0, Math.min(element.width || 50, element.height || 50) / 2, 0, 2 * Math.PI)
                            ctx.fill()
                            break
                        case 'triangle':
                            ctx.beginPath()
                            ctx.moveTo(0, -(element.height || 50) / 2)
                            ctx.lineTo(-(element.width || 50) / 2, (element.height || 50) / 2)
                            ctx.lineTo((element.width || 50) / 2, (element.height || 50) / 2)
                            ctx.closePath()
                            ctx.fill()
                            break
                        case 'rectangle':
                            ctx.fillRect(-(element.width || 50) / 2, -(element.height || 50) / 2, element.width || 50, element.height || 50)
                            break
                        case 'diamond':
                            ctx.beginPath()
                            ctx.moveTo(0, -(element.height || 50) / 2)
                            ctx.lineTo((element.width || 50) / 2, 0)
                            ctx.lineTo(0, (element.height || 50) / 2)
                            ctx.lineTo(-(element.width || 50) / 2, 0)
                            ctx.closePath()
                            ctx.fill()
                            break
                        case 'star':
                            drawStar(ctx, 0, 0, 5, (element.width || 50) / 2, (element.width || 50) / 4)
                            break

                        case 'arrow':
                            const w = element.width || 50
                            const h = element.height || 50
                            ctx.beginPath()
                            ctx.moveTo(-w/2, -h/4)
                            ctx.lineTo(w/4, -h/4)
                            ctx.lineTo(w/4, -h/2)
                            ctx.lineTo(w/2, 0)
                            ctx.lineTo(w/4, h/2)
                            ctx.lineTo(w/4, h/4)
                            ctx.lineTo(-w/2, h/4)
                            ctx.closePath()
                            ctx.fill()
                            break
                        default:
                            // Rectangle par défaut
                            ctx.fillRect(-(element.width || 50) / 2, -(element.height || 50) / 2, element.width || 50, element.height || 50)
                    }
                    ctx.restore()
                    break
                case 'icon':
                    // Pour les icônes, dessiner un cercle avec un symbole
                    ctx.save()
                    ctx.globalAlpha = element.opacity !== undefined ? element.opacity : 1
                    ctx.fillStyle = element.backgroundColor || element.color || '#374151'
                    ctx.translate(element.x + (element.width || 0) / 2, element.y + (element.height || 0) / 2)
                    ctx.rotate((element.rotation || 0) * Math.PI / 180)

                    // Dessiner un cercle de fond
                    ctx.beginPath()
                    ctx.arc(0, 0, Math.min(element.width || 50, element.height || 50) / 2, 0, 2 * Math.PI)
                    ctx.fill()

                    // Ajouter un symbole simple au centre
                    ctx.fillStyle = element.textColor || '#ffffff'
                    ctx.font = `${(element.fontSize || 24)}px Arial`
                    ctx.textAlign = 'center'
                    ctx.textBaseline = 'middle'
                    const iconSymbol = getIconSymbol(element.iconClass || element.icon || 'fas fa-star')
                    ctx.fillText(iconSymbol, 0, 0)
                    ctx.restore()
                    break
                // Add more element types as needed
            }
        }

        // Draw watermark
        drawWatermarkOnCanvas(ctx)
        downloadCanvas(exportCanvas)

        // Restore original watermark state
        showWatermark.value = originalWatermarkState

    } catch (error) {
        console.error('Export error:', error)
        alert('خطأ في تصدير التصميم')
    }
}

// NOUVELLE VERSION: Export identique à la prévisualisation
const exportDesignWithWatermarkSimple = async () => {
    console.log('🎨 ===== EXPORT WATERMARK SIMPLE DÉMARRÉ =====')
    try {
        // Temporarily ensure watermark is visible
        const originalWatermarkState = showWatermark.value
        showWatermark.value = true
        console.log('✅ Watermark forcé à true dans exportDesignWithWatermarkSimple')

        console.log(`📊 Export avec ${elements.value.length} éléments, dimensions: ${canvasWidth.value}x${canvasHeight.value}`)
        console.log('📋 Liste des éléments à exporter:')
        elements.value.forEach((el, i) => {
            console.log(`  ${i+1}. ${el.type} (${el.name}) - visible: ${el.visible} - pos: ${el.x},${el.y} - taille: ${el.width}x${el.height}`)
        })

        // Wait for DOM update
        await nextTick()
        console.log('✅ DOM mis à jour dans exportDesignWithWatermarkSimple')

        // NOUVELLE MÉTHODE: Créer un canvas identique à la prévisualisation
        console.log('🎨 Appel createPreviewCanvas()')
        const exportCanvas = await createPreviewCanvas()
        console.log('✅ Canvas créé:', exportCanvas.width + 'x' + exportCanvas.height)

        console.log('💾 Appel downloadCanvas()')
        downloadCanvas(exportCanvas)

        // Restore original states
        showWatermark.value = originalWatermarkState
        console.log('✅ Watermark restauré à:', originalWatermarkState)
        console.log('🎉 ===== EXPORT WATERMARK SIMPLE TERMINÉ =====')

    } catch (error) {
        console.error('❌ ===== ERREUR DANS EXPORT WATERMARK SIMPLE =====')
        console.error('Détails de l\'erreur:', error)
        console.error('Stack trace:', error.stack)
        alert('خطأ في تصدير التصميم: ' + error.message)
    }
}

// NOUVELLE FONCTION: Créer un canvas identique à la prévisualisation HTML
const createPreviewCanvas = async () => {
    console.log('🎨 ===== CREATE PREVIEW CANVAS DÉMARRÉ =====')

    // Create a new canvas with exact dimensions
    const exportCanvas = document.createElement('canvas')
    const ctx = exportCanvas.getContext('2d')

    exportCanvas.width = canvasWidth.value
    exportCanvas.height = canvasHeight.value
    console.log(`📐 Canvas créé avec dimensions: ${exportCanvas.width}x${exportCanvas.height}`)

    // Fill white background
    ctx.fillStyle = '#ffffff'
    ctx.fillRect(0, 0, canvasWidth.value, canvasHeight.value)
    console.log('✅ Fond blanc appliqué')

    // Draw background image if exists
    if (canvasBackground.value) {
        console.log('🖼️ Image de fond détectée:', canvasBackground.value.substring(0, 50) + '...')
        await new Promise((resolve) => {
            const bgImg = new Image()
            bgImg.crossOrigin = 'anonymous'
            bgImg.onload = () => {
                ctx.drawImage(bgImg, 0, 0, canvasWidth.value, canvasHeight.value)
                console.log('✅ Image de fond dessinée')
                resolve()
            }
            bgImg.onerror = (err) => {
                console.log('❌ Erreur chargement image de fond:', err)
                resolve()
            }
            bgImg.src = canvasBackground.value
        })
    } else {
        console.log('ℹ️ Pas d\'image de fond')
    }

    // Draw all elements exactly like in preview
    console.log(`🎯 Début dessin de ${elements.value.length} éléments sur le canvas d'export`)
    for (let i = 0; i < elements.value.length; i++) {
        const element = elements.value[i]
        console.log(`\n--- Élément ${i+1}/${elements.value.length} ---`)
        console.log('Type:', element.type)
        console.log('Nom:', element.name)
        console.log('Visible:', element.visible)
        console.log('Position:', `x=${element.x}, y=${element.y}`)
        console.log('Taille:', `${element.width}x${element.height}`)
        console.log('Propriétés spéciales:', element.shapeType || element.iconClass || element.text || 'Aucune')

        if (element.visible === false) {
            console.log('⏭️ Élément ignoré car non visible')
            continue
        }

        console.log('🎨 Dessin de l\'élément...')
        try {
            await drawElementOnCanvas(ctx, element)
            console.log('✅ Élément dessiné avec succès')
        } catch (error) {
            console.error('❌ Erreur lors du dessin de l\'élément:', error)
        }
    }

    // Draw watermark
    console.log('🔒 Application du watermark...')
    try {
        drawWatermarkOnExportCanvas(ctx, canvasWidth.value, canvasHeight.value)
        console.log('✅ Watermark appliqué')
    } catch (error) {
        console.error('❌ Erreur watermark:', error)
    }

    console.log(`🎉 Canvas d'export créé avec succès: ${canvasWidth.value}x${canvasHeight.value}`)
    console.log('🎨 ===== CREATE PREVIEW CANVAS TERMINÉ =====')
    return exportCanvas
}

// NOUVELLE FONCTION: Dessiner un élément sur le canvas (identique à la prévisualisation)
const drawElementOnCanvas = async (ctx, element) => {
    console.log(`  🎨 drawElementOnCanvas - Type: ${element.type}`)
    ctx.save()

    // Apply transformations
    const opacity = element.opacity !== undefined ? element.opacity : 1
    const centerX = element.x + (element.width || 0) / 2
    const centerY = element.y + (element.height || 0) / 2
    const rotation = (element.rotation || 0) * Math.PI / 180

    console.log(`  📐 Transformations - Opacity: ${opacity}, Center: ${centerX},${centerY}, Rotation: ${element.rotation || 0}°`)

    ctx.globalAlpha = opacity
    ctx.translate(centerX, centerY)
    ctx.rotate(rotation)

    console.log(`  🎯 Switch sur type: ${element.type}`)
    switch (element.type) {
        case 'text':
            console.log(`    📝 Dessin texte: "${element.text}" - Taille: ${element.fontSize}px - Couleur: ${element.color}`)
            ctx.font = `${element.fontWeight || 'normal'} ${element.fontSize || 16}px ${element.fontFamily || 'Cairo, sans-serif'}`
            ctx.fillStyle = element.color || '#000'
            ctx.textAlign = 'center'
            ctx.textBaseline = 'middle'
            ctx.fillText(element.text || 'نص', 0, 0)
            console.log(`    ✅ Texte dessiné`)
            break

        case 'image':
            console.log(`    🖼️ Dessin image: ${element.src ? element.src.substring(0, 50) + '...' : 'Pas de src'}`)
            await new Promise((resolve) => {
                const img = new Image()
                img.crossOrigin = 'anonymous'
                img.onload = () => {
                    ctx.drawImage(img, -(element.width || 0) / 2, -(element.height || 0) / 2, element.width || 100, element.height || 100)
                    console.log(`    ✅ Image dessinée`)
                    resolve()
                }
                img.onerror = (err) => {
                    console.log(`    ❌ Erreur chargement image:`, err)
                    resolve()
                }
                img.src = element.src
            })
            break

        case 'rectangle':
            console.log(`    ⬛ Dessin rectangle - Couleur: ${element.backgroundColor} - Taille: ${element.width}x${element.height}`)
            ctx.fillStyle = element.backgroundColor || '#000'
            ctx.fillRect(-(element.width || 0) / 2, -(element.height || 0) / 2, element.width || 100, element.height || 100)
            console.log(`    ✅ Rectangle dessiné`)
            break

        case 'circle':
            console.log(`    ⭕ Dessin cercle - Couleur: ${element.backgroundColor} - Rayon: ${Math.min(element.width || 100, element.height || 100) / 2}`)
            ctx.fillStyle = element.backgroundColor || '#10b981'
            ctx.beginPath()
            ctx.arc(0, 0, Math.min(element.width || 100, element.height || 100) / 2, 0, 2 * Math.PI)
            ctx.fill()
            console.log(`    ✅ Cercle dessiné`)
            break

        case 'shape':
            console.log(`    🔷 Dessin forme - Type: ${element.shapeType} - Couleur: ${element.backgroundColor} - Taille: ${element.width}x${element.height}`)
            // CORRECTION: Dessiner les formes géométriques réelles au lieu d'icônes
            ctx.fillStyle = element.backgroundColor || '#8b5cf6'
            console.log(`    🎨 Couleur appliquée: ${ctx.fillStyle}`)

            switch (element.shapeType) {
                case 'circle':
                    console.log(`    ⭕ Dessin cercle de forme`)
                    ctx.beginPath()
                    ctx.arc(0, 0, Math.min(element.width || 50, element.height || 50) / 2, 0, 2 * Math.PI)
                    ctx.fill()
                    console.log(`    ✅ Cercle de forme dessiné`)
                    break
                case 'triangle':
                    console.log(`    🔺 Dessin triangle`)
                    ctx.beginPath()
                    ctx.moveTo(0, -(element.height || 50) / 2)
                    ctx.lineTo(-(element.width || 50) / 2, (element.height || 50) / 2)
                    ctx.lineTo((element.width || 50) / 2, (element.height || 50) / 2)
                    ctx.closePath()
                    ctx.fill()
                    console.log(`    ✅ Triangle dessiné`)
                    break
                case 'diamond':
                    console.log(`    ◆ DESSIN LOSANGE - CRITIQUE!`)
                    ctx.beginPath()
                    const w = (element.width || 50) / 2
                    const h = (element.height || 50) / 2
                    console.log(`    📐 Dimensions losange: w=${w}, h=${h}`)
                    ctx.moveTo(0, -h)  // Haut
                    console.log(`    📍 Point haut: 0, ${-h}`)
                    ctx.lineTo(w, 0)   // Droite
                    console.log(`    📍 Point droite: ${w}, 0`)
                    ctx.lineTo(0, h)   // Bas
                    console.log(`    📍 Point bas: 0, ${h}`)
                    ctx.lineTo(-w, 0)  // Gauche
                    console.log(`    📍 Point gauche: ${-w}, 0`)
                    ctx.closePath()
                    ctx.fill()
                    console.log(`    ✅ LOSANGE DESSINÉ AVEC SUCCÈS!`)
                    break
                case 'star':
                    console.log(`    ⭐ Dessin étoile`)
                    drawStar(ctx, 0, 0, 5, (element.width || 50) / 2, (element.width || 50) / 4)
                    ctx.fill()
                    console.log(`    ✅ Étoile dessinée`)
                    break

                default:
                    console.log(`    ⬛ Dessin rectangle par défaut pour forme inconnue: ${element.shapeType}`)
                    ctx.fillRect(-(element.width || 50) / 2, -(element.height || 50) / 2, element.width || 50, element.height || 50)
                    console.log(`    ✅ Rectangle par défaut dessiné`)
            }
            console.log(`    ✅ Forme ${element.shapeType} terminée`)
            break

        case 'icon':
            // Pour les icônes, dessiner un cercle avec un symbole simple
            ctx.fillStyle = element.color || '#374151'
            ctx.beginPath()
            ctx.arc(0, 0, Math.min(element.width || 50, element.height || 50) / 2, 0, 2 * Math.PI)
            ctx.fill()

            // Ajouter un symbole simple au centre
            ctx.fillStyle = '#ffffff'
            ctx.font = `${(element.fontSize || 24)}px Arial`
            ctx.textAlign = 'center'
            ctx.textBaseline = 'middle'
            ctx.fillText(getIconSymbol(element.iconClass), 0, 0)
            break
    }

    ctx.restore()
}

// Fonction pour convertir les classes FontAwesome en symboles simples
const getIconSymbol = (iconClass) => {
    const symbols = {
        'fas fa-home': '🏠',
        'fas fa-user': '👤',
        'fas fa-envelope': '✉',
        'fas fa-phone': '📞',
        'fas fa-calendar': '📅',
        'fas fa-clock': '🕐',
        'fas fa-location-dot': '📍',
        'fas fa-camera': '📷',
        'fas fa-music': '🎵',
        'fas fa-video': '🎥',
        'fas fa-gift': '🎁',
        'fas fa-shopping-cart': '🛒',
        'fas fa-car': '🚗',
        'fas fa-plane': '✈',
        'fas fa-graduation-cap': '🎓',
        'fas fa-trophy': '🏆',
        'fas fa-star': '⭐',
        'fas fa-heart': '❤',
        'fas fa-diamond': '◆'
    }
    return symbols[iconClass] || '●'
}

// Fonction pour générer des formes HTML avec CSS (identiques au canvas)
const generateShapeHTML = (element, style) => {
    const bgColor = element.backgroundColor || '#8b5cf6'
    const width = element.width || 50
    const height = element.height || 50

    switch (element.shapeType) {
        case 'circle':
            return `<div class="element" style="${style} background-color: ${bgColor}; border-radius: 50%;"></div>`

        case 'triangle':
            return `<div class="element" style="${style} width: 0; height: 0;
                border-left: ${width/2}px solid transparent;
                border-right: ${width/2}px solid transparent;
                border-bottom: ${height}px solid ${bgColor};
                transform: translate(-50%, -50%) rotate(${element.rotation || 0}deg);
                left: ${element.x + width/2}px;
                top: ${element.y + height/2}px;"></div>`

        case 'diamond':
            return `<div class="element" style="${style} background-color: ${bgColor};
                transform: translate(-50%, -50%) rotate(45deg) rotate(${element.rotation || 0}deg);
                left: ${element.x + width/2}px;
                top: ${element.y + height/2}px;"></div>`

        case 'star':
            // Pour l'étoile, utiliser un symbole Unicode
            return `<div class="element" style="${style} display: flex; align-items: center; justify-content: center; color: ${bgColor}; font-size: ${width * 0.8}px;">
                ⭐
            </div>`



        default:
            // Rectangle par défaut
            return `<div class="element" style="${style} background-color: ${bgColor};"></div>`
    }
}

// Copie exacte de la fonction admin qui marche
const drawWatermarkOnCanvas = (ctx) => {
    // Draw watermark
    ctx.save()
    ctx.font = 'bold 48px Cairo, sans-serif'
    ctx.fillStyle = 'rgba(0, 0, 0, 0.08)'
    ctx.textAlign = 'center'

    // Position at center and rotate diagonally
    const x = canvasWidth.value / 2
    const y = canvasHeight.value / 2

    // Rotate text
    ctx.translate(x, y)
    ctx.rotate(-45 * Math.PI / 180)
    ctx.fillText('سامقة للتصميم', 0, 0)
    ctx.restore()
}

// Fonction pour dessiner la filigrane sur le canvas d'export avec dimensions personnalisées
const drawWatermarkOnExportCanvas = (ctx, width, height) => {
    ctx.save()

    // Ajuster la taille de la police selon les dimensions
    const fontSize = Math.min(width, height) * 0.08 // 8% de la plus petite dimension
    ctx.font = `bold ${fontSize}px Cairo, sans-serif`
    ctx.fillStyle = 'rgba(0, 0, 0, 0.08)'
    ctx.textAlign = 'center'

    // Position at center and rotate diagonally
    const x = width / 2
    const y = height / 2

    // Rotate text
    ctx.translate(x, y)
    ctx.rotate(-45 * Math.PI / 180)
    ctx.fillText('منصة سامقة للتصميم', 0, 0)
    ctx.restore()
}

const downloadCanvas = (canvas, filename = null) => {
    const link = document.createElement('a')
    link.download = filename ? `${filename}-${Date.now()}.png` : `design-${Date.now()}.png`
    link.href = canvas.toDataURL()

    // DÉBOGAGE: Afficher les informations sur le canvas téléchargé
    console.log(`💾 TÉLÉCHARGEMENT: ${link.download}`)
    console.log(`📐 Dimensions: ${canvas.width}x${canvas.height}`)
    console.log(`📊 Data URL length: ${link.href.length} caractères`)
    console.log(`🎨 Premier pixel (debug):`, canvas.getContext('2d').getImageData(0, 0, 1, 1).data)

    link.click()
}

const exportAsSVG = async () => {
    // Get export dimensions based on selected preset
    const preset = presetFormats.find(p => p.value === selectedPresetFormat.value)
    let exportWidth = canvasWidth.value
    let exportHeight = canvasHeight.value
    let scaleFactor = 1

    if (preset && preset.width && preset.height) {
        exportWidth = preset.width
        exportHeight = preset.height
        // Calculate scale factor to fit content
        scaleFactor = Math.min(exportWidth / canvasWidth.value, exportHeight / canvasHeight.value)
    }

    // Center the content if needed
    const offsetX = (exportWidth - canvasWidth.value * scaleFactor) / 2
    const offsetY = (exportHeight - canvasHeight.value * scaleFactor) / 2

    // Create SVG string with all elements
    let svgContent = `<svg width="${exportWidth}" height="${exportHeight}" xmlns="http://www.w3.org/2000/svg">`

    // Background
    svgContent += `<rect width="100%" height="100%" fill="white"/>`

    // Create a group for scaled content
    svgContent += `<g transform="translate(${offsetX}, ${offsetY}) scale(${scaleFactor})">`

    // Background image if exists
    if (canvasBackground.value) {
        svgContent += `<image href="${canvasBackground.value}" width="${canvasWidth.value}" height="${canvasHeight.value}"/>`
    }

    // Elements
    for (const element of elements.value) {
        if (element.visible === false) continue

        const transform = `translate(${element.x + (element.width || 0) / 2}, ${element.y + (element.height || 0) / 2}) rotate(${element.rotation || 0})`

        switch (element.type) {
            case 'text':
                svgContent += `<text x="0" y="0" transform="${transform}"
                    font-family="${element.fontFamily || 'Cairo, sans-serif'}"
                    font-size="${element.fontSize || 16}"
                    font-weight="${element.fontWeight || 'normal'}"
                    fill="${element.color || '#000'}"
                    text-anchor="middle"
                    opacity="${element.opacity !== undefined ? element.opacity : 1}">
                    ${element.text || ''}
                </text>`
                break
            case 'rectangle':
                svgContent += `<rect x="${-(element.width || 0) / 2}" y="${-(element.height || 0) / 2}"
                    width="${element.width || 100}" height="${element.height || 100}"
                    fill="${element.backgroundColor || '#000'}"
                    transform="${transform}"
                    opacity="${element.opacity !== undefined ? element.opacity : 1}"/>`
                break
            case 'circle':
                svgContent += `<circle cx="0" cy="0"
                    r="${Math.min(element.width || 100, element.height || 100) / 2}"
                    fill="${element.backgroundColor || '#10b981'}"
                    transform="${transform}"
                    opacity="${element.opacity !== undefined ? element.opacity : 1}"/>`
                break
            case 'shape':
                // Dessiner selon le type de forme
                switch (element.shapeType) {
                    case 'circle':
                        svgContent += `<circle cx="0" cy="0"
                            r="${Math.min(element.width || 50, element.height || 50) / 2}"
                            fill="${element.backgroundColor || '#8b5cf6'}"
                            transform="${transform}"
                            opacity="${element.opacity !== undefined ? element.opacity : 1}"/>`
                        break
                    case 'triangle':
                        const w = element.width || 50
                        const h = element.height || 50
                        svgContent += `<polygon points="0,${-h/2} ${-w/2},${h/2} ${w/2},${h/2}"
                            fill="${element.backgroundColor || '#8b5cf6'}"
                            transform="${transform}"
                            opacity="${element.opacity !== undefined ? element.opacity : 1}"/>`
                        break
                    case 'star':
                        // Créer les points d'une étoile à 5 branches
                        const starPoints = generateStarPoints(0, 0, 5, (element.width || 50) / 2, (element.width || 50) / 4)
                        svgContent += `<polygon points="${starPoints}"
                            fill="${element.backgroundColor || '#8b5cf6'}"
                            transform="${transform}"
                            opacity="${element.opacity !== undefined ? element.opacity : 1}"/>`
                        break
                    default:
                        // Rectangle par défaut
                        svgContent += `<rect x="${-(element.width || 50) / 2}" y="${-(element.height || 50) / 2}"
                            width="${element.width || 50}" height="${element.height || 50}"
                            fill="${element.backgroundColor || '#8b5cf6'}"
                            transform="${transform}"
                            opacity="${element.opacity !== undefined ? element.opacity : 1}"/>`
                }
                break
            case 'icon':
                // Pour les icônes, dessiner un cercle avec du texte
                svgContent += `<circle cx="0" cy="0"
                    r="${Math.min(element.width || 50, element.height || 50) / 2}"
                    fill="${element.color || '#374151'}"
                    transform="${transform}"
                    opacity="${element.opacity !== undefined ? element.opacity : 1}"/>
                <text x="0" y="0" text-anchor="middle" dominant-baseline="central"
                    font-size="${element.fontSize || 24}" fill="white"
                    transform="${transform}"
                    opacity="${element.opacity !== undefined ? element.opacity : 1}">
                    ${getIconSymbol(element.iconClass)}
                </text>`
                break
            case 'image':
                svgContent += `<image href="${element.src}"
                    x="${-(element.width || 0) / 2}" y="${-(element.height || 0) / 2}"
                    width="${element.width || 100}" height="${element.height || 100}"
                    transform="${transform}"
                    opacity="${element.opacity !== undefined ? element.opacity : 1}"/>`
                break
        }
    }

    // Close the scaled content group
    svgContent += '</g>'

    // Add watermark (outside the scaled group, on the full export canvas)
    const watermarkFontSize = Math.min(exportWidth, exportHeight) * 0.08
    svgContent += `<text x="${exportWidth / 2}" y="${exportHeight / 2}"
        font-family="Cairo, sans-serif" font-size="${watermarkFontSize}" font-weight="bold"
        fill="rgba(0, 0, 0, 0.08)" text-anchor="middle"
        transform="rotate(-45 ${exportWidth / 2} ${exportHeight / 2})">منصة سامقة للتصميم</text>`

    svgContent += '</svg>'

    // Download SVG
    const blob = new Blob([svgContent], { type: 'image/svg+xml' })
    const link = document.createElement('a')
    link.download = `design-${Date.now()}.svg`
    link.href = URL.createObjectURL(blob)
    link.click()
    URL.revokeObjectURL(link.href)
}

// Fonction pour créer un canvas d'export
const createExportCanvas = async () => {
    // Get export dimensions based on selected preset
    const preset = presetFormats.find(p => p.value === selectedPresetFormat.value)
    let exportWidth = canvasWidth.value
    let exportHeight = canvasHeight.value
    let scaleFactor = 1

    // CORRECTION: Toujours utiliser les dimensions actuelles du canvas pour éviter les problèmes de scaling
    // Seul le preset 'current' ou null devrait être utilisé pour l'export client
    if (preset && preset.width && preset.height && preset.value !== 'current') {
        // Pour les presets spécifiques, on garde les dimensions originales mais on peut ajuster la qualité
        console.log(`Export avec preset ${preset.value}: ${preset.width}x${preset.height}, mais on garde les dimensions originales pour éviter les problèmes de scaling`)
    }

    // Create a new canvas for export - TOUJOURS utiliser les dimensions originales
    const exportCanvas = document.createElement('canvas')
    const ctx = exportCanvas.getContext('2d')

    exportCanvas.width = exportWidth
    exportCanvas.height = exportHeight

    // Fill background - TOUJOURS blanc pour préserver le fond
    ctx.fillStyle = '#ffffff'
    ctx.fillRect(0, 0, exportWidth, exportHeight)

    // PAS de scaling ni de translation - dessiner directement aux bonnes positions
    // ctx.save() - Retiré pour éviter les transformations
    // ctx.translate(offsetX, offsetY) - Retiré
    // ctx.scale(scaleFactor, scaleFactor) - Retiré

    // Draw background image if exists
    if (canvasBackground.value) {
        await new Promise((resolve) => {
            const bgImg = new Image()
            bgImg.crossOrigin = 'anonymous'
            bgImg.onload = () => {
                // Apply the same background sizing as in the canvas display
                if (backgroundSize.value === 'contain') {
                    // Calculate aspect ratios
                    const canvasAspect = canvasWidth.value / canvasHeight.value
                    const imageAspect = bgImg.width / bgImg.height

                    let drawWidth, drawHeight, drawX, drawY

                    if (imageAspect > canvasAspect) {
                        // Image is wider - fit to width
                        drawWidth = canvasWidth.value
                        drawHeight = canvasWidth.value / imageAspect
                        drawX = 0
                        drawY = (canvasHeight.value - drawHeight) / 2
                    } else {
                        // Image is taller - fit to height
                        drawWidth = canvasHeight.value * imageAspect
                        drawHeight = canvasHeight.value
                        drawX = (canvasWidth.value - drawWidth) / 2
                        drawY = 0
                    }

                    ctx.drawImage(bgImg, drawX, drawY, drawWidth, drawHeight)
                } else if (backgroundSize.value === 'cover') {
                    // Calculate aspect ratios for cover
                    const canvasAspect = canvasWidth.value / canvasHeight.value
                    const imageAspect = bgImg.width / bgImg.height

                    let drawWidth, drawHeight, drawX, drawY

                    if (imageAspect > canvasAspect) {
                        // Image is wider - fit to height and crop sides
                        drawHeight = canvasHeight.value
                        drawWidth = canvasHeight.value * imageAspect
                        drawX = (canvasWidth.value - drawWidth) / 2
                        drawY = 0
                    } else {
                        // Image is taller - fit to width and crop top/bottom
                        drawWidth = canvasWidth.value
                        drawHeight = canvasWidth.value / imageAspect
                        drawX = 0
                        drawY = (canvasHeight.value - drawHeight) / 2
                    }

                    ctx.drawImage(bgImg, drawX, drawY, drawWidth, drawHeight)
                } else {
                    // Default: stretch to fill (background-size: 100% 100%)
                    ctx.drawImage(bgImg, 0, 0, canvasWidth.value, canvasHeight.value)
                }
                resolve()
            }
            bgImg.onerror = () => resolve() // Continue even if background fails
            bgImg.src = canvasBackground.value
        })
    }

    // Draw all elements - CORRECTION: S'assurer que tous les éléments sont inclus
    console.log(`Dessin de ${elements.value.length} éléments sur le canvas d'export`)
    for (const element of elements.value) {
        if (element.visible === false) {
            console.log(`Élément ${element.id} ignoré car non visible`)
            continue
        }
        console.log(`Dessin de l'élément ${element.id} de type ${element.type}`, element)
        switch (element.type) {
            case 'text':
                ctx.save()
                ctx.font = `${element.fontWeight || 'normal'} ${element.fontSize || 16}px ${element.fontFamily || 'Cairo, sans-serif'}`
                ctx.fillStyle = element.color || '#000'
                ctx.textAlign = element.textAlign || 'left'
                ctx.globalAlpha = element.opacity !== undefined ? element.opacity : 1
                ctx.translate(element.x + (element.width || 0) / 2, element.y + (element.height || 0) / 2)
                ctx.rotate((element.rotation || 0) * Math.PI / 180)
                ctx.fillText(element.text || '', 0, 0)
                ctx.restore()
                break
            case 'image':
                await new Promise((resolve) => {
                    const img = new Image()
                    img.crossOrigin = 'anonymous'
                    img.onload = () => {
                        ctx.save()
                        ctx.globalAlpha = element.opacity !== undefined ? element.opacity : 1
                        ctx.translate(element.x + (element.width || 0) / 2, element.y + (element.height || 0) / 2)
                        ctx.rotate((element.rotation || 0) * Math.PI / 180)
                        ctx.drawImage(img, -(element.width || 0) / 2, -(element.height || 0) / 2, element.width || 100, element.height || 100)
                        ctx.restore()
                        resolve()
                    }
                    img.onerror = () => resolve()
                    img.src = element.src
                })
                break
            case 'rectangle':
                ctx.save()
                ctx.globalAlpha = element.opacity !== undefined ? element.opacity : 1
                ctx.fillStyle = element.backgroundColor || '#000'
                ctx.translate(element.x + (element.width || 0) / 2, element.y + (element.height || 0) / 2)
                ctx.rotate((element.rotation || 0) * Math.PI / 180)
                ctx.fillRect(-(element.width || 0) / 2, -(element.height || 0) / 2, element.width || 100, element.height || 100)
                ctx.restore()
                break
            case 'circle':
                ctx.save()
                ctx.globalAlpha = element.opacity !== undefined ? element.opacity : 1
                ctx.fillStyle = element.backgroundColor || '#10b981'
                ctx.translate(element.x + (element.width || 0) / 2, element.y + (element.height || 0) / 2)
                ctx.rotate((element.rotation || 0) * Math.PI / 180)
                ctx.beginPath()
                ctx.arc(0, 0, Math.min(element.width || 100, element.height || 100) / 2, 0, 2 * Math.PI)
                ctx.fill()
                ctx.restore()
                break
            case 'shape':
                ctx.save()
                ctx.globalAlpha = element.opacity !== undefined ? element.opacity : 1
                ctx.fillStyle = element.backgroundColor || '#8b5cf6'
                ctx.translate(element.x + (element.width || 0) / 2, element.y + (element.height || 0) / 2)
                ctx.rotate((element.rotation || 0) * Math.PI / 180)

                // Dessiner selon le type de forme
                switch (element.shapeType) {
                    case 'circle':
                        ctx.beginPath()
                        ctx.arc(0, 0, Math.min(element.width || 50, element.height || 50) / 2, 0, 2 * Math.PI)
                        ctx.fill()
                        break
                    case 'triangle':
                        ctx.beginPath()
                        ctx.moveTo(0, -(element.height || 50) / 2)
                        ctx.lineTo(-(element.width || 50) / 2, (element.height || 50) / 2)
                        ctx.lineTo((element.width || 50) / 2, (element.height || 50) / 2)
                        ctx.closePath()
                        ctx.fill()
                        break
                    case 'star':
                        drawStar(ctx, 0, 0, 5, (element.width || 50) / 2, (element.width || 50) / 4)
                        ctx.fill()
                        break

                    case 'diamond':
                        // Dessiner un losange (diamant)
                        ctx.beginPath()
                        const w = (element.width || 50) / 2
                        const h = (element.height || 50) / 2
                        ctx.moveTo(0, -h)  // Haut
                        ctx.lineTo(w, 0)   // Droite
                        ctx.lineTo(0, h)   // Bas
                        ctx.lineTo(-w, 0)  // Gauche
                        ctx.closePath()
                        ctx.fill()
                        break
                    default:
                        // Rectangle par défaut
                        ctx.fillRect(-(element.width || 50) / 2, -(element.height || 50) / 2, element.width || 50, element.height || 50)
                }
                ctx.restore()
                break
            case 'icon':
                // Pour les icônes, dessiner un cercle avec la première lettre de l'icône
                ctx.save()
                ctx.globalAlpha = element.opacity !== undefined ? element.opacity : 1
                ctx.fillStyle = element.color || '#374151'
                ctx.translate(element.x + (element.width || 0) / 2, element.y + (element.height || 0) / 2)
                ctx.rotate((element.rotation || 0) * Math.PI / 180)

                // Dessiner un cercle de fond
                ctx.beginPath()
                ctx.arc(0, 0, Math.min(element.width || 50, element.height || 50) / 2, 0, 2 * Math.PI)
                ctx.fill()

                // Ajouter un symbole simple au centre
                ctx.fillStyle = '#ffffff'
                ctx.font = `${(element.fontSize || 24)}px Arial`
                ctx.textAlign = 'center'
                ctx.textBaseline = 'middle'
                ctx.fillText(getIconSymbol(element.iconClass), 0, 0)
                ctx.restore()
                break
        }
    }

    // Draw watermark on the full export canvas
    drawWatermarkOnExportCanvas(ctx, exportWidth, exportHeight)

    console.log(`Canvas d'export créé avec succès: ${exportWidth}x${exportHeight}`)
    return exportCanvas
}

const exportAsPDF = async () => {
    // For PDF export, we'll use the canvas approach and convert to PDF
    // This requires jsPDF library - for now, we'll convert canvas to image and embed in PDF
    const canvas = await createExportCanvas()

    // Create a simple PDF with the canvas image
    const imgData = canvas.toDataURL('image/png')

    // Create a new window for printing
    const printWindow = window.open('', '_blank')

    // Build HTML content using DOM methods to avoid template literal issues
    printWindow.document.write('<!DOCTYPE html>')
    printWindow.document.write('<html>')
    printWindow.document.write('<head>')
    printWindow.document.write('<title>تصميم سامقة</title>')
    printWindow.document.write('<style>')
    printWindow.document.write('body { margin: 0; padding: 20px; text-align: center; }')
    printWindow.document.write('img { max-width: 100%; height: auto; }')
    printWindow.document.write('@media print { body { margin: 0; padding: 0; } }')
    printWindow.document.write('</style>')
    printWindow.document.write('</head>')
    printWindow.document.write('<body>')
    printWindow.document.write(`<img src="${imgData}" alt="Design" />`)
    printWindow.document.write('<script>')
    printWindow.document.write('window.onload = function() {')
    printWindow.document.write('window.print();')
    printWindow.document.write('setTimeout(function() { window.close(); }, 1000);')
    printWindow.document.write('}')
    printWindow.document.write('<' + '/script>')
    printWindow.document.write('<' + '/body>')
    printWindow.document.write('<' + '/html>')
    printWindow.document.close()
}

// Lifecycle
onMounted(() => {
    loadDesign()

    // Keyboard shortcuts
    document.addEventListener('keydown', (e) => {
        if (e.ctrlKey || e.metaKey) {
            switch (e.key) {
                case 'z':
                    e.preventDefault()
                    if (e.shiftKey) {
                        redo()
                    } else {
                        undo()
                    }
                    break
                case 'y':
                    e.preventDefault()
                    redo()
                    break
                case 's':
                    e.preventDefault()
                    saveDesign()
                    break
            }
        }

        if (e.key === 'Delete' && selectedElement.value) {
            deleteElement(selectedElement.value.id)
        }
    })
})

function updateAllZIndices() {
    elements.value.forEach((el, idx) => {
        el.zIndex = idx;
    });
}

function onMoveLayer(direction) {
    if (!selectedElement.value) return;
    const idx = elements.value.findIndex(el => el.id === selectedElement.value.id);
    if (idx === -1) return;
    if (direction === 'front' && idx < elements.value.length - 1) {
        const temp = elements.value[idx];
        elements.value.splice(idx, 1);
        elements.value.splice(idx + 1, 0, temp);
        updateAllZIndices();
        saveToHistory();
    } else if (direction === 'back' && idx > 0) {
        const temp = elements.value[idx];
        elements.value.splice(idx, 1);
        elements.value.splice(idx - 1, 0, temp);
        updateAllZIndices();
        saveToHistory();
    }
}

function onDuplicateElement() {
    if (!selectedElement.value) return;
    const element = { ...selectedElement.value, id: generateId() };
    elements.value.push(element);
    updateAllZIndices();
    selectedElement.value = element;
    saveToHistory();
}

function onRequestDeleteElement() {
    if (!selectedElement.value) return;
    layerToDelete.value = selectedElement.value.id;
    showDeleteConfirm.value = true;
}

function onRequestDeleteLayer(layerId) {
    layerToDelete.value = layerId;
    showDeleteConfirm.value = true;
}

function performDeleteLayer() {
    if (layerToDelete.value) {
        deleteElement(layerToDelete.value);
        layerToDelete.value = null;
        showDeleteConfirm.value = false;
    }
}

function cancelDeleteLayer() {
    layerToDelete.value = null;
    showDeleteConfirm.value = false;
}

// Update updateProperties to merge into .properties if needed
function updateProperties(properties) {
    if (selectedElement.value) {
        const elementIndex = elements.value.findIndex(el => el.id === selectedElement.value.id);
        if (elementIndex !== -1) {
            const element = elements.value[elementIndex];
            Object.keys(properties).forEach(key => {
                // Always update both root and .properties
                element[key] = properties[key];
                if (element.properties) {
                    element.properties[key] = properties[key];
                }
            });
            elements.value[elementIndex] = { ...element };
            saveToHistory();
        }
    }
}
</script>

<style scoped>
.design-canvas {
    direction: ltr; /* Canvas should be LTR for proper positioning */
}

/* Watermark styles */
.watermark {
    position: absolute;
    bottom: 15px;
    right: 15px;
    font-size: 14px;
    color: rgba(0, 0, 0, 0.25);
    font-family: 'Cairo', sans-serif;
    font-weight: bold;
    text-shadow: 1px 1px 2px rgba(255, 255, 255, 0.8);
    z-index: 9999;
    transform: rotate(-15deg);
    transform-origin: center;
    user-select: none;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    letter-spacing: 1px;
    pointer-events: none;
}

/* Ensure watermark is always visible */
.watermark::before {
    content: '';
    position: absolute;
    top: -2px;
    left: -2px;
    right: -2px;
    bottom: -2px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 2px;
    z-index: -1;
}

/* Print styles - ensure watermark appears in prints */
@media print {
    .watermark {
        color: rgba(0, 0, 0, 0.4) !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
}
</style>
