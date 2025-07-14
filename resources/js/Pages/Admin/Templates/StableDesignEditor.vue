<template>
    <Head title="محرر التصميم المتقدم - إصدار مستقر">
        <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">
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
                            @click="exportDesignWithWatermark"
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
                        <ElementsPanel @add-element="addElement" />
                    </div>

                    <!-- Resources Tab -->
                    <div v-if="activeTab === 'resources'" class="h-full">
                        <ResourceManager @file-select="addImage" />
                    </div>

                    <!-- Layers Tab -->
                    <div v-if="activeTab === 'layers'" class="h-full">
                        <LayersPanel
                            :layers="layers"
                            @select-layer="selectLayer"
                            @delete-layer="deleteLayer"
                            @reorder-layers="reorderLayers"
                        />
                    </div>

                    <!-- Properties Tab -->
                    <div v-if="activeTab === 'properties'" class="h-full">
                        <PropertiesPanel
                            :selected-element="selectedElement"
                            @update-properties="updateProperties"
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
                                <button
                                    @click="resetZoom"
                                    class="p-2 text-gray-600 hover:text-gray-800 rounded"
                                >
                                    <i class="fas fa-expand-arrows-alt"></i>
                                </button>
                            </div>

                            <!-- Grid Toggle -->
                            <button
                                @click="toggleGrid"
                                :class="[
                                    'p-2 rounded transition-colors',
                                    showGrid ? 'bg-purple-100 text-purple-600' : 'text-gray-600 hover:text-gray-800'
                                ]"
                            >
                                <i class="fas fa-th"></i>
                            </button>

                            <!-- Snap Toggle -->
                            <button
                                @click="toggleSnap"
                                :class="[
                                    'p-2 rounded transition-colors',
                                    snapToGrid ? 'bg-purple-100 text-purple-600' : 'text-gray-600 hover:text-gray-800'
                                ]"
                            >
                                <i class="fas fa-magnet"></i>
                            </button>

                            <!-- Watermark Toggle -->
                            <button
                                @click="showWatermark = !showWatermark"
                                :class="[
                                    'p-2 rounded transition-colors',
                                    showWatermark ? 'bg-blue-100 text-blue-600' : 'text-gray-600 hover:text-gray-800'
                                ]"
                                title="إظهار/إخفاء العلامة المائية (ستظهر دائماً في التصدير)"
                            >
                                <i class="fas fa-copyright"></i>
                            </button>
                        </div>

                        <!-- Quick Actions -->
                        <div class="space-y-2">
                            <div class="flex items-center space-x-2 space-x-reverse">
                                <button
                                    @click="addQuickText"
                                    class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700 flex items-center space-x-1 space-x-reverse"
                                >
                                    <i class="fas fa-font text-xs"></i>
                                    <span>نص</span>
                                </button>
                                <button
                                    @click="addQuickShape"
                                    class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700 flex items-center space-x-1 space-x-reverse"
                                >
                                    <i class="fas fa-square text-xs"></i>
                                    <span>شكل</span>
                                </button>
                            </div>

                            <!-- Background Controls -->
                            <div v-if="canvasBackground" class="flex items-center space-x-2 space-x-reverse">
                                <label class="text-xs text-gray-600">حجم الخلفية:</label>
                                <select
                                    v-model="backgroundSize"
                                    class="text-xs border border-gray-300 rounded px-2 py-1"
                                >
                                    <option value="contain">احتواء كامل</option>
                                    <option value="cover">تغطية</option>
                                    <option value="100% 100%">تمدد كامل</option>
                                    <option value="auto">حجم طبيعي</option>
                                </select>
                                <button
                                    @click="removeCanvasBackground"
                                    class="bg-red-500 text-white px-2 py-1 rounded text-xs hover:bg-red-600"
                                    title="إزالة الخلفية"
                                >
                                    <i class="fas fa-times"></i>
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
                                backgroundRepeat: 'no-repeat'
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
    </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed, nextTick } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import ResourceManager from '@/Components/DesignEditor/ResourceManager.vue'
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
    { id: 'resources', name: 'الموارد', icon: 'fas fa-images' },
    { id: 'layers', name: 'الطبقات', icon: 'fas fa-layer-group' },
    { id: 'properties', name: 'الخصائص', icon: 'fas fa-cog' }
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

const addElement = (elementData) => {
    const newElement = {
        id: generateId(),
        type: elementData.type,
        name: elementData.name,
        x: 50,
        y: 50,
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
    selectedElement.value = element
}

const selectLayer = (layerId) => {
    const element = elements.value.find(el => el.id === layerId)
    if (element) {
        selectedElement.value = element
    }
}

const updateElement = (elementId, updates) => {
    const elementIndex = elements.value.findIndex(el => el.id === elementId)
    if (elementIndex !== -1) {
        elements.value[elementIndex] = { ...elements.value[elementIndex], ...updates }
        saveToHistory()
    }
}

const updateProperties = (properties) => {
    if (selectedElement.value) {
        updateElement(selectedElement.value.id, properties)
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
    saveToHistory()
}

const handleCanvasClick = (event) => {
    // Deselect if clicking on empty canvas
    if (event.target === designCanvas.value) {
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

const saveDesign = async () => {
    saving.value = true

    try {
        const designData = {
            elements: elements.value,
            canvas: {
                width: canvasWidth.value,
                height: canvasHeight.value,
                background: canvasBackground.value,
                backgroundSize: backgroundSize.value
            },
            settings: {
                device: currentDevice.value,
                zoom: zoom.value
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

        const response = await fetch(`/admin/templates/${props.template.id}/design`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                design_data: JSON.stringify(designData)
            })
        })

        if (response.ok) {
            // Show success message
            console.log('Design saved successfully')
        } else {
            throw new Error('Failed to save design')
        }
    } catch (error) {
        console.error('Error saving design:', error)
        alert('خطأ في حفظ التصميم')
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
            return `<div class="element" style="${style} font-size: ${element.fontSize || 16}px; color: ${element.color || '#000'}; font-weight: ${element.fontWeight || 'normal'};">${element.text || 'نص'}</div>`
        case 'image':
            return `<img class="element" src="${element.src}" style="${style} object-fit: cover;" alt="${element.name}">`
        case 'rectangle':
            return `<div class="element" style="${style} background-color: ${element.backgroundColor || '#000'}; border-radius: ${element.borderRadius || 0}px;"></div>`
        default:
            return ''
    }
}

const loadDesign = () => {
    if (props.template.design_data) {
        try {
            const designData = JSON.parse(props.template.design_data)
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

const setCanvasBackground = (imageUrl) => {
    canvasBackground.value = imageUrl
    backgroundSize.value = 'contain' // Default to contain for better fit
    saveToHistory()
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
            const img = new Image()
            img.crossOrigin = 'anonymous'
            img.onload = () => {
                ctx.drawImage(img, 0, 0, canvasWidth.value, canvasHeight.value)
                drawWatermarkOnCanvas(ctx)
                downloadCanvas(exportCanvas)
            }
            img.src = canvasBackground.value
        } else {
            drawWatermarkOnCanvas(ctx)
            downloadCanvas(exportCanvas)
        }

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

const downloadCanvas = (canvas) => {
    const link = document.createElement('a')
    link.download = `design-${Date.now()}.png`
    link.href = canvas.toDataURL()
    link.click()
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
