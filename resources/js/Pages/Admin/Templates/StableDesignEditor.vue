<template>
    <Head title="محرر التصميم المتقدم - إصدار مستقر">
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
                            @click="previewDesignAdvanced"
                            class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 flex items-center space-x-2 space-x-reverse"
                        >
                            <i class="fas fa-eye"></i>
                            <span>معاينة</span>
                        </button>

                        <!-- Back -->
                        <Link
                            :href="route('admin.templates.index')"
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
                    <div v-if="activeTab === 'properties'" class="h-full overflow-y-auto">
                        <!-- Canvas Properties -->
                        <div class="p-4 border-b border-gray-200">
                            <h3 class="text-sm font-medium text-gray-700 mb-3">خصائص اللوحة</h3>

                            <!-- Canvas Dimensions -->
                            <div class="mb-4">
                                <label class="block text-xs font-medium text-gray-600 mb-2">أبعاد اللوحة</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <input
                                            v-model.number="canvasWidth"
                                            type="number"
                                            min="100"
                                            max="2000"
                                            class="w-full px-2 py-1 text-xs border border-gray-300 rounded"
                                            placeholder="العرض"
                                        >
                                    </div>
                                    <div>
                                        <input
                                            v-model.number="canvasHeight"
                                            type="number"
                                            min="100"
                                            max="2000"
                                            class="w-full px-2 py-1 text-xs border border-gray-300 rounded"
                                            placeholder="الارتفاع"
                                        >
                                    </div>
                                </div>
                            </div>

                            <!-- Background Controls -->
                            <div v-if="canvasBackground" class="mb-4">
                                <label class="block text-xs font-medium text-gray-600 mb-2">حجم صورة الخلفية</label>
                                <select
                                    v-model="backgroundSize"
                                    class="w-full px-2 py-1 text-xs border border-gray-300 rounded"
                                >
                                    <option value="contain">احتواء (Contain)</option>
                                    <option value="cover">تغطية (Cover)</option>
                                    <option value="100% 100%">تمديد (Stretch)</option>
                                </select>
                                <div class="text-xs text-gray-500 mt-1">
                                    <span v-if="backgroundSize === 'contain'">الصورة تظهر كاملة مع حفظ النسبة</span>
                                    <span v-else-if="backgroundSize === 'cover'">الصورة تملأ اللوحة مع قص الزائد</span>
                                    <span v-else>الصورة تتمدد لتملأ اللوحة</span>
                                </div>
                                <button
                                    @click="removeCanvasBackground"
                                    class="mt-2 w-full px-2 py-1 text-xs bg-red-100 text-red-600 rounded hover:bg-red-200"
                                >
                                    إزالة صورة الخلفية
                                </button>
                            </div>
                        </div>

                        <!-- Element Properties -->
                        <div class="flex-1">
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
                            <!-- Watermark Toggle -->
                            <button
                                @click="showWatermark = !showWatermark"
                                :class="['p-2 rounded transition-colors', showWatermark ? 'bg-purple-100 text-purple-600' : 'text-gray-600 hover:text-gray-800']"
                                title="تبديل العلامة المائية"
                            >
                                <i class="fas fa-certificate"></i>
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

                            <!-- Watermark - Always on top and uneditable -->
                            <div
                                v-if="showWatermark"
                                class="absolute pointer-events-none select-none"
                                :style="{
                                    bottom: '15px',
                                    right: '15px',
                                    fontSize: '14px',
                                    color: 'rgba(0, 0, 0, 0.25)',
                                    fontFamily: 'Cairo, sans-serif',
                                    fontWeight: 'bold',
                                    textShadow: '1px 1px 2px rgba(255, 255, 255, 0.8)',
                                    zIndex: 9999,
                                    transform: 'rotate(-15deg)',
                                    transformOrigin: 'center',
                                    userSelect: 'none',
                                    WebkitUserSelect: 'none',
                                    MozUserSelect: 'none',
                                    msUserSelect: 'none',
                                    letterSpacing: '1px'
                                }"
                            >
                                سامقة للتصميم
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
        <div v-if="showExportModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click="showExportModal = false">
            <div class="bg-white rounded-2xl p-6 max-w-lg w-full mx-4" @click.stop>
                <h3 class="text-xl font-semibold mb-6 text-center">تصدير التصميم</h3>

                <!-- Preset Formats -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">اختر حجم التصدير:</label>
                    <div class="grid grid-cols-2 gap-3">
                        <button
                            v-for="preset in presetFormats"
                            :key="preset.value"
                            @click="selectedPresetFormat = preset.value"
                            :class="[
                                'p-3 border-2 rounded-lg text-center transition-all',
                                selectedPresetFormat === preset.value
                                    ? 'border-purple-500 bg-purple-50 text-purple-700'
                                    : 'border-gray-200 hover:border-gray-300'
                            ]"
                        >
                            <div class="font-medium text-sm">{{ preset.label }}</div>
                            <div class="text-xs text-gray-500 mt-1">
                                {{ preset.width }} × {{ preset.height }}
                                <span v-if="preset.dpi"> ({{ preset.dpi }} DPI)</span>
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



                <!-- Action Buttons -->
                <div class="flex space-x-3 space-x-reverse">
                    <button
                        @click="exportDesignWithFormat"
                        :disabled="isExporting"
                        class="flex-1 bg-gradient-to-r from-purple-500 to-pink-500 text-white py-3 px-4 rounded-lg font-medium hover:from-purple-600 hover:to-pink-600 transition-all duration-200 disabled:opacity-50"
                    >
                        <span v-if="isExporting">جاري التصدير...</span>
                        <span v-else>تصدير</span>
                    </button>
                    <button
                        @click="showExportModal = false"
                        class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200"
                    >
                        إلغاء
                    </button>
                </div>
            </div>
        </div>
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
const showWatermark = ref(true)
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

const devices = [
    { id: 'desktop', name: 'سطح المكتب', icon: 'fas fa-desktop' },
    { id: 'tablet', name: 'تابلت', icon: 'fas fa-tablet-alt' },
    { id: 'mobile', name: 'موبايل', icon: 'fas fa-mobile-alt' }
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

// Preset formats for export
const presetFormats = [
    { value: 'current', label: 'الحجم الحالي', width: null, height: null },
    { value: 'hd', label: 'HD', width: 1920, height: 1080, dpi: 72 },
    { value: 'print_a4', label: 'A4 للطباعة', width: 2480, height: 3508, dpi: 300 },
    { value: 'social_post', label: 'منشور سوشيال', width: 1080, height: 1080, dpi: 72 },
    { value: 'story', label: 'ستوري', width: 1080, height: 1920, dpi: 72 },
    { value: 'banner', label: 'بانر', width: 1200, height: 400, dpi: 72 }
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
            design_data: designDataString,
            canvas_size: `${canvasWidth.value}x${canvasHeight.value}`,
            design_notes: `تم التحديث في ${new Date().toLocaleString('ar-SA')}`
        }
        console.log('Request payload size:', Math.round(new Blob([JSON.stringify(requestPayload)]).size / 1024), 'KB')

        const response = await fetch(route('admin.templates.design.save', props.template.id), {
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
                    letter-spacing: 1px;
                    pointer-events: none;
                ">سامقة للتصميم</div>
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
            return `<div class="element" style="${style} font-size: ${element.fontSize || 16}px; color: ${element.color || '#000'}; font-weight: ${element.fontWeight || 'normal'}; font-family: ${element.fontFamily || 'Cairo, sans-serif'}; text-align: ${element.textAlign || 'right'};">${element.text || 'نص'}</div>`

        case 'image':
            return `<img class="element" src="${element.src}" style="${style} object-fit: cover;" alt="${element.name}">`

        case 'rectangle':
            return `<div class="element" style="${style} background-color: ${element.backgroundColor || '#000'}; border-radius: ${element.borderRadius || 0}px;"></div>`

        case 'circle':
            return `<div class="element" style="${style} background-color: ${element.backgroundColor || '#10b981'}; border-radius: 50%;"></div>`

        case 'shape':
            return generateShapeHTML(element, style)

        case 'icon':
            const iconSymbol = getIconSymbolForElement(element)
            return `<div class="element" style="${style} display: flex; align-items: center; justify-content: center; font-size: ${element.fontSize || Math.min(element.width || 50, element.height || 50)}px; color: ${element.color || element.backgroundColor || '#374151'};">
                ${iconSymbol}
            </div>`

        default:
            return ''
    }
}

// Generate HTML for shapes in preview
const generateShapeHTML = (element, style) => {
    const shapeType = element.shapeType || 'rectangle'
    const bgColor = element.backgroundColor || element.color || '#8b5cf6'
    const width = element.width || 50
    const height = element.height || 50

    switch (shapeType) {
        case 'circle':
            return `<div class="element" style="${style} background-color: ${bgColor}; border-radius: 50%;"></div>`

        case 'triangle':
            return `<div class="element" style="${style} width: 0; height: 0; border-left: ${width/2}px solid transparent; border-right: ${width/2}px solid transparent; border-bottom: ${height}px solid ${bgColor}; background: none;"></div>`

        case 'diamond':
            return `<div class="element" style="${style} background-color: ${bgColor}; transform: rotate(45deg) rotate(${element.rotation || 0}deg);"></div>`

        case 'star':
            return `<div class="element" style="${style} display: flex; align-items: center; justify-content: center; color: ${bgColor}; font-size: ${width * 0.8}px; background: none;">
                ★
            </div>`

        case 'arrow':
            return `<div class="element" style="${style} display: flex; align-items: center; justify-content: center; color: ${bgColor}; font-size: ${width * 0.6}px; background: none;">
                →
            </div>`

        default:
            // Rectangle
            return `<div class="element" style="${style} background-color: ${bgColor};"></div>`
    }
}

const loadDesign = () => {
    if (props.template.design_data) {
        try {
            // Check if design_data is already an object or needs parsing
            let designData
            if (typeof props.template.design_data === 'string') {
                designData = JSON.parse(props.template.design_data)
            } else if (typeof props.template.design_data === 'object') {
                designData = props.template.design_data
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

            // Initialize history
            saveToHistory()
        } catch (error) {
            console.error('Error loading design:', error)
            // Initialize with empty design on error
            elements.value = []
            saveToHistory()
        }
    } else {
        // Initialize with empty design
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
                // Apply the same background sizing as in the canvas display
                if (backgroundSize.value === 'contain') {
                    // Calculate aspect ratios
                    const canvasAspect = canvasWidth.value / canvasHeight.value
                    const imageAspect = img.width / img.height

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

                    ctx.drawImage(img, drawX, drawY, drawWidth, drawHeight)
                } else if (backgroundSize.value === 'cover') {
                    // Calculate aspect ratios for cover
                    const canvasAspect = canvasWidth.value / canvasHeight.value
                    const imageAspect = img.width / img.height

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

                    ctx.drawImage(img, drawX, drawY, drawWidth, drawHeight)
                } else {
                    // Default: stretch to fill (background-size: 100% 100%)
                    ctx.drawImage(img, 0, 0, canvasWidth.value, canvasHeight.value)
                }
                resolve()
            }
            img.onerror = () => resolve() // Continue even if background fails
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
                case 'shape':
                case 'circle':
                    ctx.save()
                    ctx.globalAlpha = element.opacity !== undefined ? element.opacity : 1
                    ctx.fillStyle = element.backgroundColor || element.color || '#000'
                    ctx.translate(element.x + (element.width || 0) / 2, element.y + (element.height || 0) / 2)
                    ctx.rotate((element.rotation || 0) * Math.PI / 180)

                    // Handle different shape types
                    const shapeType = element.shapeType || (element.type === 'circle' ? 'circle' : 'rectangle')
                    switch (shapeType) {
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
                            // Draw a 5-pointed star
                            const outerRadius = Math.min(element.width || 50, element.height || 50) / 2
                            const innerRadius = outerRadius * 0.4
                            ctx.beginPath()
                            for (let i = 0; i < 10; i++) {
                                const angle = (i * Math.PI) / 5
                                const radius = i % 2 === 0 ? outerRadius : innerRadius
                                const x = Math.cos(angle - Math.PI / 2) * radius
                                const y = Math.sin(angle - Math.PI / 2) * radius
                                if (i === 0) ctx.moveTo(x, y)
                                else ctx.lineTo(x, y)
                            }
                            ctx.closePath()
                            ctx.fill()
                            break
                        case 'arrow':
                            const arrowW = element.width || 50
                            const arrowH = element.height || 50
                            ctx.beginPath()
                            ctx.moveTo(-arrowW/2, -arrowH/4)
                            ctx.lineTo(arrowW/4, -arrowH/4)
                            ctx.lineTo(arrowW/4, -arrowH/2)
                            ctx.lineTo(arrowW/2, 0)
                            ctx.lineTo(arrowW/4, arrowH/2)
                            ctx.lineTo(arrowW/4, arrowH/4)
                            ctx.lineTo(-arrowW/2, arrowH/4)
                            ctx.closePath()
                            ctx.fill()
                            break
                        default:
                            // Rectangle
                            ctx.fillRect(-(element.width || 0) / 2, -(element.height || 0) / 2, element.width || 100, element.height || 100)
                    }
                    ctx.restore()
                    break
                case 'icon':
                    ctx.save()
                    ctx.globalAlpha = element.opacity !== undefined ? element.opacity : 1
                    ctx.translate(element.x + (element.width || 0) / 2, element.y + (element.height || 0) / 2)
                    ctx.rotate((element.rotation || 0) * Math.PI / 180)

                    // Get icon symbol using our enhanced mapping
                    const iconSymbol = getIconSymbolForElement(element)

                    // Draw the icon symbol
                    ctx.fillStyle = element.color || element.backgroundColor || '#374151'
                    ctx.font = `${(element.fontSize || Math.min(element.width || 50, element.height || 50))}px Arial`
                    ctx.textAlign = 'center'
                    ctx.textBaseline = 'middle'
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

const drawWatermarkOnCanvas = (ctx) => {
    // Draw watermark
    ctx.save()
    ctx.font = 'bold 14px Cairo, sans-serif'
    ctx.fillStyle = 'rgba(0, 0, 0, 0.25)'
    ctx.textAlign = 'right'

    // Position at bottom right
    const x = canvasWidth.value - 15
    const y = canvasHeight.value - 15

    // Rotate text
    ctx.translate(x, y)
    ctx.rotate(-15 * Math.PI / 180)
    ctx.fillText('سامقة للتصميم', 0, 0)
    ctx.restore()
}

const downloadCanvas = (canvas, filename = null) => {
    const link = document.createElement('a')
    link.download = filename ? `${filename}-${Date.now()}.png` : `design-${Date.now()}.png`
    link.href = canvas.toDataURL()
    link.click()
}

// Helper function to get export dimensions
const getExportDimensions = () => {
    const preset = presetFormats.find(p => p.value === selectedPresetFormat.value)
    if (!preset || preset.value === 'current') {
        return `${canvasWidth.value} × ${canvasHeight.value} بكسل`
    }
    return `${preset.width} × ${preset.height} بكسل (${preset.dpi} DPI)`
}



// Helper function to get icon symbol for element
const getIconSymbolForElement = (element) => {
    // Prioritize the icon property (Unicode symbol) over iconClass (FontAwesome)
    if (element.icon && (element.icon.length === 1 || element.icon.length === 2)) {
        return element.icon
    }

    if (element.iconClass) {
        const iconMap = {
            'fas fa-star': '★',
            'fas fa-heart': '❤',
            'fas fa-circle': '●',
            'fas fa-square': '■',
            'fas fa-diamond': '♦',
            'fas fa-arrow-right': '→',
            'fas fa-arrow-left': '←',
            'fas fa-arrow-up': '↑',
            'fas fa-arrow-down': '↓',
            'fas fa-chevron-right': '›',
            'fas fa-chevron-left': '‹',
            'fas fa-chevron-up': '⌃',
            'fas fa-chevron-down': '⌄',
            'fas fa-check': '✓',
            'fas fa-times': '✕',
            'fas fa-plus': '+',
            'fas fa-minus': '−',
            'fas fa-edit': '✎',
            'fas fa-trash': '🗑',
            'fas fa-save': '💾',
            'fas fa-download': '⬇',
            'fas fa-upload': '⬆',
            'fas fa-search': '🔍',
            'fas fa-cog': '⚙',
            'fas fa-settings': '⚙',
            'fas fa-gear': '⚙',
            'fas fa-home': '🏠',
            'fas fa-user': '👤',
            'fas fa-envelope': '✉',
            'fas fa-phone': '📞',
            'fas fa-message': '💬',
            'fas fa-comment': '💬',
            'fas fa-mail': '📧',
            'fas fa-users': '👥',
            'fas fa-group': '👥',
            'fas fa-camera': '📷',
            'fas fa-music': '🎵',
            'fas fa-video': '🎥',
            'fas fa-image': '🖼',
            'fas fa-file': '📄',
            'fas fa-folder': '📁',
            'fas fa-play': '▶',
            'fas fa-pause': '⏸',
            'fas fa-stop': '⏹',
            'fas fa-car': '🚗',
            'fas fa-plane': '✈',
            'fas fa-train': '🚂',
            'fas fa-ship': '🚢',
            'fas fa-bicycle': '🚲',
            'fas fa-trophy': '🏆',
            'fas fa-gift': '🎁',
            'fas fa-key': '🔑',
            'fas fa-lock': '🔒',
            'fas fa-unlock': '🔓',
            'fas fa-bell': '🔔',
            'fas fa-clock': '🕐',
            'fas fa-calendar': '📅',
            'fas fa-location-dot': '📍',
            'fas fa-graduation-cap': '🎓',
            'fas fa-shopping-cart': '🛒',
            'fas fa-cart': '🛒',
            'fas fa-basket': '🧺',
            'fas fa-sun': '☀',
            'fas fa-moon': '🌙',
            'fas fa-cloud': '☁',
            'fas fa-umbrella': '☂',
            'fas fa-coffee': '☕',
            'fas fa-apple': '🍎',
            'fas fa-cake': '🎂',
            'fas fa-info': 'ℹ',
            'fas fa-warning': '⚠',
            'fas fa-exclamation': '!',
            'fas fa-question': '?',
            'fas fa-lightbulb': '💡',
            'fas fa-bulb': '💡',

            // Additional new icons
            'fas fa-message': '💬',
            'fas fa-comment': '💬',
            'fas fa-users': '👥',
            'fas fa-group': '👥',
            'fas fa-image': '🖼',
            'fas fa-play': '▶',
            'fas fa-pause': '⏸',
            'fas fa-stop': '⏹',
            'fas fa-money': '💰',
            'fas fa-store': '🏪',
            'fas fa-bicycle': '🚲',
            'fas fa-train': '🚂',
            'fas fa-book': '📚',
            'fas fa-pencil': '✏',
            'fas fa-smile': '😊',
            'fas fa-thumbs-up': '👍',
            'fas fa-rain': '🌧',
            'fas fa-pizza': '🍕',
            'fas fa-key': '🔑',
            'fas fa-lock': '🔒',
            'fas fa-unlock': '🔓'
        }

        const cleanIconClass = element.iconClass.trim().toLowerCase()

        if (iconMap[cleanIconClass]) {
            return iconMap[cleanIconClass]
        } else {
            if (!cleanIconClass.startsWith('fas ') && !cleanIconClass.startsWith('far ') &&
                !cleanIconClass.startsWith('fab ') && !cleanIconClass.startsWith('fal ')) {
                const withFas = `fas ${cleanIconClass}`
                if (iconMap[withFas]) {
                    return iconMap[withFas]
                } else {
                    const withoutFaPrefix = cleanIconClass.replace(/^fa-/, '')
                    const fasVersion = `fas fa-${withoutFaPrefix}`
                    return iconMap[fasVersion] || '●'
                }
            } else {
                const withoutPrefix = cleanIconClass.replace(/^(fas|far|fab|fal)\s+/, '')
                const fasVersion = `fas ${withoutPrefix}`
                return iconMap[fasVersion] || '●'
            }
        }
    }

    return '●'
}

// Advanced preview function like client - ONLY SHOW PREVIEW, NO DOWNLOAD
const previewDesignAdvanced = async () => {
    try {
        console.log('🔍 ===== PREVIEW DESIGN ADVANCED DÉMARRÉ =====')

        // 1. Générer les données de prévisualisation
        const previewData = {
            elements: elements.value,
            canvas: {
                width: canvasWidth.value,
                height: canvasHeight.value,
                background: canvasBackground.value || '',
                backgroundSize: backgroundSize.value || 'contain'
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

        const previewHTML = generateAdvancedPreviewHTML(previewData)
        console.log('- HTML généré, longueur:', previewHTML.length)

        // 2. Ouvrir la prévisualisation dans une nouvelle fenêtre - NO DOWNLOAD
        const previewWindow = window.open('', '_blank', 'width=1000,height=800')
        previewWindow.document.write(previewHTML)

        console.log('✅ ===== PREVIEW TERMINÉ AVEC SUCCÈS (AFFICHAGE SEULEMENT) =====')

    } catch (error) {
        console.error('❌ ERREUR PENDANT PREVIEW:', error)
        alert('خطأ في معاينة التصميم: ' + error.message)
    }
}

// Generate advanced preview HTML like client
const generateAdvancedPreviewHTML = (designData) => {
    let html = `
        <!DOCTYPE html>
        <html dir="rtl">
        <head>
            <meta charset="UTF-8">
            <title>معاينة التصميم - إدارة</title>
            <style>
                @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap');
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
                .admin-badge {
                    position: fixed;
                    top: 20px;
                    left: 20px;
                    background: linear-gradient(45deg, #8b5cf6, #ec4899);
                    color: white;
                    padding: 8px 16px;
                    border-radius: 20px;
                    font-size: 12px;
                    font-weight: bold;
                    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
                }
            </style>
        </head>
        <body>
            <div class="admin-badge">معاينة الإدارة</div>
            <h2>معاينة التصميم - سامقة (إدارة)</h2>
            <div class="canvas">
    `

    designData.elements.forEach(element => {
        if (element.visible !== false) {
            html += generateElementHTML(element)
        }
    })

    html += `
            </div>
            <div class="footer">
                تم إنشاؤه بواسطة منصة سامقة للتصميم - معاينة الإدارة<br>
                ${new Date().toLocaleString('ar-SA')}
            </div>
        </body>
        </html>
    `

    return html
}

// Generate SVG for individual elements
const generateElementSVG = (element) => {
    const transform = `translate(${element.x + (element.width || 0) / 2}, ${element.y + (element.height || 0) / 2}) rotate(${element.rotation || 0})`

    switch (element.type) {
        case 'text':
            return `<text x="0" y="0" transform="${transform}"
                font-family="${element.fontFamily || 'Cairo, sans-serif'}"
                font-size="${element.fontSize || 16}"
                font-weight="${element.fontWeight || 'normal'}"
                fill="${element.color || '#000'}"
                text-anchor="middle"
                dominant-baseline="central"
                opacity="${element.opacity !== undefined ? element.opacity : 1}">
                ${element.text || ''}
            </text>`

        case 'rectangle':
            return `<rect x="${-(element.width || 0) / 2}" y="${-(element.height || 0) / 2}"
                width="${element.width || 100}" height="${element.height || 100}"
                fill="${element.backgroundColor || '#000'}"
                transform="${transform}"
                opacity="${element.opacity !== undefined ? element.opacity : 1}"/>`

        case 'circle':
            return `<circle cx="0" cy="0"
                r="${Math.min(element.width || 100, element.height || 100) / 2}"
                fill="${element.backgroundColor || '#10b981'}"
                transform="${transform}"
                opacity="${element.opacity !== undefined ? element.opacity : 1}"/>`

        case 'shape':
            return generateShapeSVG(element, transform)

        case 'icon':
            const iconSymbol = getIconSymbolForElement(element)
            return `<text x="0" y="0" text-anchor="middle" dominant-baseline="central"
                font-size="${element.fontSize || Math.min(element.width || 50, element.height || 50)}"
                fill="${element.color || element.backgroundColor || '#374151'}"
                transform="${transform}"
                opacity="${element.opacity !== undefined ? element.opacity : 1}">
                ${iconSymbol}
            </text>`

        case 'image':
            return `<image href="${element.src}"
                x="${-(element.width || 0) / 2}" y="${-(element.height || 0) / 2}"
                width="${element.width || 100}" height="${element.height || 100}"
                transform="${transform}"
                opacity="${element.opacity !== undefined ? element.opacity : 1}"/>`

        default:
            return ''
    }
}

// Generate SVG for shapes
const generateShapeSVG = (element, transform) => {
    const shapeType = element.shapeType || 'rectangle'
    const width = element.width || 50
    const height = element.height || 50
    const fill = element.backgroundColor || element.color || '#8b5cf6'
    const opacity = element.opacity !== undefined ? element.opacity : 1

    switch (shapeType) {
        case 'circle':
            return `<circle cx="0" cy="0"
                r="${Math.min(width, height) / 2}"
                fill="${fill}"
                transform="${transform}"
                opacity="${opacity}"/>`

        case 'triangle':
            return `<polygon points="0,${-height/2} ${-width/2},${height/2} ${width/2},${height/2}"
                fill="${fill}"
                transform="${transform}"
                opacity="${opacity}"/>`

        case 'diamond':
            return `<polygon points="0,${-height/2} ${width/2},0 0,${height/2} ${-width/2},0"
                fill="${fill}"
                transform="${transform}"
                opacity="${opacity}"/>`

        case 'star':
            // Generate 5-pointed star points
            const outerRadius = Math.min(width, height) / 2
            const innerRadius = outerRadius * 0.4
            let starPoints = ''
            for (let i = 0; i < 10; i++) {
                const angle = (i * Math.PI) / 5 - Math.PI / 2
                const radius = i % 2 === 0 ? outerRadius : innerRadius
                const x = Math.cos(angle) * radius
                const y = Math.sin(angle) * radius
                starPoints += `${x},${y} `
            }
            return `<polygon points="${starPoints.trim()}"
                fill="${fill}"
                transform="${transform}"
                opacity="${opacity}"/>`

        case 'arrow':
            const arrowW = width
            const arrowH = height
            return `<polygon points="${-arrowW/2},${-arrowH/4} ${arrowW/4},${-arrowH/4} ${arrowW/4},${-arrowH/2} ${arrowW/2},0 ${arrowW/4},${arrowH/2} ${arrowW/4},${arrowH/4} ${-arrowW/2},${arrowH/4}"
                fill="${fill}"
                transform="${transform}"
                opacity="${opacity}"/>`

        default:
            // Rectangle
            return `<rect x="${-width/2}" y="${-height/2}"
                width="${width}" height="${height}"
                fill="${fill}"
                transform="${transform}"
                opacity="${opacity}"/>`
    }
}

// Create advanced preview canvas like client
const createAdvancedPreviewCanvas = async () => {
    console.log('🎨 ===== CREATE ADVANCED PREVIEW CANVAS DÉMARRÉ =====')

    const exportCanvas = document.createElement('canvas')
    const ctx = exportCanvas.getContext('2d')

    exportCanvas.width = canvasWidth.value
    exportCanvas.height = canvasHeight.value
    console.log(`📐 Canvas créé avec dimensions: ${exportCanvas.width}x${exportCanvas.height}`)

    // Fill white background
    ctx.fillStyle = '#ffffff'
    ctx.fillRect(0, 0, canvasWidth.value, canvasHeight.value)
    console.log('✅ Fond blanc appliqué')

    // Draw background image if exists (with proper sizing)
    if (canvasBackground.value) {
        await new Promise((resolve) => {
            const bgImg = new Image()
            bgImg.crossOrigin = 'anonymous'
            bgImg.onload = () => {
                // Apply the same background sizing as in the canvas display
                if (backgroundSize.value === 'contain') {
                    const canvasAspect = canvasWidth.value / canvasHeight.value
                    const imageAspect = bgImg.width / bgImg.height

                    let drawWidth, drawHeight, drawX, drawY

                    if (imageAspect > canvasAspect) {
                        drawWidth = canvasWidth.value
                        drawHeight = canvasWidth.value / imageAspect
                        drawX = 0
                        drawY = (canvasHeight.value - drawHeight) / 2
                    } else {
                        drawWidth = canvasHeight.value * imageAspect
                        drawHeight = canvasHeight.value
                        drawX = (canvasWidth.value - drawWidth) / 2
                        drawY = 0
                    }

                    ctx.drawImage(bgImg, drawX, drawY, drawWidth, drawHeight)
                } else if (backgroundSize.value === 'cover') {
                    const canvasAspect = canvasWidth.value / canvasHeight.value
                    const imageAspect = bgImg.width / bgImg.height

                    let drawWidth, drawHeight, drawX, drawY

                    if (imageAspect > canvasAspect) {
                        drawHeight = canvasHeight.value
                        drawWidth = canvasHeight.value * imageAspect
                        drawX = (canvasWidth.value - drawWidth) / 2
                        drawY = 0
                    } else {
                        drawWidth = canvasWidth.value
                        drawHeight = canvasWidth.value / imageAspect
                        drawX = 0
                        drawY = (canvasHeight.value - drawHeight) / 2
                    }

                    ctx.drawImage(bgImg, drawX, drawY, drawWidth, drawHeight)
                } else {
                    ctx.drawImage(bgImg, 0, 0, canvasWidth.value, canvasHeight.value)
                }
                resolve()
            }
            bgImg.onerror = () => resolve()
            bgImg.src = canvasBackground.value
        })
    }

    // Draw all elements
    console.log(`Dessin de ${elements.value.length} éléments sur le canvas d'export`)
    for (const element of elements.value) {
        if (element.visible === false) {
            console.log(`Élément ${element.id} ignoré car non visible`)
            continue
        }

        console.log(`Dessin élément ${element.id} (${element.type})`)

        switch (element.type) {
            case 'text':
                ctx.save()
                ctx.font = `${element.fontWeight || 'normal'} ${element.fontSize || 16}px ${element.fontFamily || 'Cairo, sans-serif'}`
                ctx.fillStyle = element.color || '#000'
                ctx.textAlign = element.textAlign || 'left'
                ctx.globalAlpha = element.opacity !== undefined ? element.opacity : 1
                ctx.translate(element.x + (element.width || 0) / 2, element.y + (element.height || 0) / 2)
                ctx.rotate((element.rotation || 0) * Math.PI / 180)
                ctx.fillText(element.content || element.text || '', -(element.width || 0) / 2, 0)
                ctx.restore()
                break
            case 'image':
                if (element.src) {
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
                }
                break
            case 'rectangle':
            case 'shape':
            case 'circle':
                ctx.save()
                ctx.globalAlpha = element.opacity !== undefined ? element.opacity : 1
                ctx.fillStyle = element.backgroundColor || element.color || '#000'
                ctx.translate(element.x + (element.width || 0) / 2, element.y + (element.height || 0) / 2)
                ctx.rotate((element.rotation || 0) * Math.PI / 180)

                const shapeType = element.shapeType || (element.type === 'circle' ? 'circle' : 'rectangle')
                switch (shapeType) {
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
                        // Draw a 5-pointed star
                        const outerRadius = Math.min(element.width || 50, element.height || 50) / 2
                        const innerRadius = outerRadius * 0.4
                        ctx.beginPath()
                        for (let i = 0; i < 10; i++) {
                            const angle = (i * Math.PI) / 5
                            const radius = i % 2 === 0 ? outerRadius : innerRadius
                            const x = Math.cos(angle - Math.PI / 2) * radius
                            const y = Math.sin(angle - Math.PI / 2) * radius
                            if (i === 0) ctx.moveTo(x, y)
                            else ctx.lineTo(x, y)
                        }
                        ctx.closePath()
                        ctx.fill()
                        break
                    case 'arrow':
                        const arrowW = element.width || 50
                        const arrowH = element.height || 50
                        ctx.beginPath()
                        ctx.moveTo(-arrowW/2, -arrowH/4)
                        ctx.lineTo(arrowW/4, -arrowH/4)
                        ctx.lineTo(arrowW/4, -arrowH/2)
                        ctx.lineTo(arrowW/2, 0)
                        ctx.lineTo(arrowW/4, arrowH/2)
                        ctx.lineTo(arrowW/4, arrowH/4)
                        ctx.lineTo(-arrowW/2, arrowH/4)
                        ctx.closePath()
                        ctx.fill()
                        break
                    default:
                        // Rectangle
                        ctx.fillRect(-(element.width || 0) / 2, -(element.height || 0) / 2, element.width || 100, element.height || 100)
                }
                ctx.restore()
                break
            case 'line':
                ctx.save()
                ctx.globalAlpha = element.opacity !== undefined ? element.opacity : 1
                ctx.strokeStyle = element.backgroundColor || element.color || '#6b7280'
                ctx.lineWidth = element.height || 2
                ctx.translate(element.x + (element.width || 0) / 2, element.y + (element.height || 0) / 2)
                ctx.rotate((element.rotation || 0) * Math.PI / 180)
                ctx.beginPath()
                ctx.moveTo(-(element.width || 200) / 2, 0)
                ctx.lineTo((element.width || 200) / 2, 0)
                ctx.stroke()
                ctx.restore()
                break
            case 'icon':
                ctx.save()
                ctx.globalAlpha = element.opacity !== undefined ? element.opacity : 1
                ctx.translate(element.x + (element.width || 0) / 2, element.y + (element.height || 0) / 2)
                ctx.rotate((element.rotation || 0) * Math.PI / 180)

                // Get icon symbol using our enhanced mapping
                const iconSymbol = getIconSymbolForElement(element)

                // Draw the icon symbol
                ctx.fillStyle = element.color || element.backgroundColor || '#374151'
                ctx.font = `${(element.fontSize || Math.min(element.width || 50, element.height || 50))}px Arial`
                ctx.textAlign = 'center'
                ctx.textBaseline = 'middle'
                ctx.fillText(iconSymbol, 0, 0)
                ctx.restore()
                break
        }
    }

    // Draw watermark
    drawWatermarkOnCanvas(ctx)

    console.log('✅ Canvas d\'export créé avec succès')
    return exportCanvas
}

// Export functions like client
const exportDesignWithFormat = async () => {
    console.log('🚀 ===== EXPORT DESIGN AVEC FORMAT DÉMARRÉ (ADMIN) =====')

    if (isExporting.value) {
        console.log('⚠️ Export déjà en cours, abandon')
        return
    }

    isExporting.value = true

    try {
        const originalWatermarkState = showWatermark.value
        showWatermark.value = true
        await nextTick()

        console.log(`🎯 Lancement export format: ${selectedExportFormat.value}`)
        switch (selectedExportFormat.value) {
            case 'png':
                await exportAsPNG()
                break
            case 'jpeg':
                await exportAsJPEG()
                break
            case 'svg':
                await exportAsSVG()
                break
            case 'pdf':
                await exportAsPDF()
                break
        }

        showWatermark.value = originalWatermarkState
        showExportModal.value = false
        console.log('✅ ===== EXPORT TERMINÉ AVEC SUCCÈS (ADMIN) =====')

    } catch (error) {
        console.error('❌ ===== ERREUR PENDANT EXPORT (ADMIN) =====')
        console.error('Détails de l\'erreur:', error)
        alert('خطأ في تصدير التصميم: ' + error.message)
    } finally {
        isExporting.value = false
    }
}

const exportAsPNG = async () => {
    const canvas = await createAdvancedPreviewCanvas()
    downloadCanvas(canvas, 'admin-design-png')
}

const exportAsJPEG = async () => {
    const canvas = await createAdvancedPreviewCanvas()
    const link = document.createElement('a')
    link.download = `admin-design-jpeg-${Date.now()}.jpg`
    link.href = canvas.toDataURL('image/jpeg', exportQuality.value / 100)
    link.click()
}

const exportAsSVG = async () => {
    console.log('🎨 ===== SVG EXPORT DÉMARRÉ =====')

    // Create SVG content
    let svgContent = `<svg width="${canvasWidth.value}" height="${canvasHeight.value}" xmlns="http://www.w3.org/2000/svg">`

    // Background
    svgContent += `<rect width="100%" height="100%" fill="white"/>`

    // Background image if exists
    if (canvasBackground.value) {
        svgContent += `<image href="${canvasBackground.value}" width="${canvasWidth.value}" height="${canvasHeight.value}"/>`
    }

    // Add elements
    elements.value.forEach(element => {
        if (element.visible !== false) {
            svgContent += generateElementSVG(element)
        }
    })

    // Add watermark
    svgContent += `<text x="${canvasWidth.value - 10}" y="${canvasHeight.value - 10}"
        text-anchor="end" font-family="Cairo, Arial" font-size="12"
        fill="rgba(0,0,0,0.3)" transform="rotate(-15 ${canvasWidth.value - 50} ${canvasHeight.value - 20})">
        سامقة للتصميم
    </text>`

    svgContent += '</svg>'

    // Download SVG
    const blob = new Blob([svgContent], { type: 'image/svg+xml' })
    const link = document.createElement('a')
    link.download = `admin-design-${Date.now()}.svg`
    link.href = URL.createObjectURL(blob)
    link.click()
    URL.revokeObjectURL(link.href)

    console.log('✅ SVG Export terminé')
}

const exportAsPDF = async () => {
    console.log('📄 ===== PDF EXPORT DÉMARRÉ =====')

    try {
        // Create canvas for PDF
        const canvas = await createAdvancedPreviewCanvas()
        const imgData = canvas.toDataURL('image/png')

        // Create HTML for PDF printing
        const width = canvasWidth.value
        const height = canvasHeight.value

        const printWindow = window.open('', '_blank')
        const htmlContent = '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>تصميم سامقة</title>' +
            '<style>@page { margin: 0; size: ' + width + 'px ' + height + 'px; }' +
            'body { margin: 0; padding: 0; display: flex; justify-content: center; align-items: center; min-height: 100vh; }' +
            '.design-container { width: ' + width + 'px; height: ' + height + 'px; position: relative; }' +
            '.design-image { width: 100%; height: 100%; object-fit: contain; }' +
            '.print-info { position: absolute; bottom: 10px; right: 10px; font-size: 10px; color: rgba(0,0,0,0.5); font-family: Arial, sans-serif; }' +
            '</style></head><body>' +
            '<div class="design-container">' +
            '<img src="' + imgData + '" alt="تصميم سامقة" class="design-image">' +
            '<div class="print-info">تم إنشاؤه بواسطة منصة سامقة للتصميم</div>' +
            '</div>' +
            '<script>window.onload = function() { setTimeout(function() { window.print(); }, 1000); }<\/script>' +
            '</body></html>'
        printWindow.document.write(htmlContent)
        printWindow.document.close()

        console.log('✅ PDF Export terminé (ouverture pour impression)')

    } catch (error) {
        console.error('❌ Erreur PDF Export:', error)
        alert('خطأ في تصدير PDF: ' + error.message)
    }
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
