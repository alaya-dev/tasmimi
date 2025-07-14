<template>
    <div class="photo-editor h-full flex flex-col bg-white">
        <!-- Header -->
        <div class="flex-shrink-0 p-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800">محرر الصور</h3>
                <div class="flex items-center space-x-2 space-x-reverse">
                    <button
                        @click="undo"
                        :disabled="!canUndo"
                        class="p-2 text-gray-500 hover:text-gray-700 rounded-lg hover:bg-gray-100 disabled:opacity-50"
                        title="تراجع"
                    >
                        <i class="fas fa-undo"></i>
                    </button>
                    <button
                        @click="redo"
                        :disabled="!canRedo"
                        class="p-2 text-gray-500 hover:text-gray-700 rounded-lg hover:bg-gray-100 disabled:opacity-50"
                        title="إعادة"
                    >
                        <i class="fas fa-redo"></i>
                    </button>
                    <button
                        @click="reset"
                        class="p-2 text-gray-500 hover:text-gray-700 rounded-lg hover:bg-gray-100"
                        title="إعادة تعيين"
                    >
                        <i class="fas fa-refresh"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="flex-1 overflow-hidden flex">
            <!-- Controls Panel -->
            <div class="w-80 border-l border-gray-200 overflow-y-auto">
                <!-- Adjustments -->
                <div class="p-4">
                    <h4 class="font-semibold text-gray-800 mb-4">التعديلات</h4>
                    
                    <!-- Brightness -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            السطوع: {{ adjustments.brightness }}
                        </label>
                        <input
                            v-model.number="adjustments.brightness"
                            @input="applyAdjustments"
                            type="range"
                            min="-100"
                            max="100"
                            step="1"
                            class="w-full"
                        >
                    </div>

                    <!-- Contrast -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            التباين: {{ adjustments.contrast }}
                        </label>
                        <input
                            v-model.number="adjustments.contrast"
                            @input="applyAdjustments"
                            type="range"
                            min="-100"
                            max="100"
                            step="1"
                            class="w-full"
                        >
                    </div>

                    <!-- Saturation -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            التشبع: {{ adjustments.saturation }}%
                        </label>
                        <input
                            v-model.number="adjustments.saturation"
                            @input="applyAdjustments"
                            type="range"
                            min="0"
                            max="200"
                            step="1"
                            class="w-full"
                        >
                    </div>

                    <!-- Hue -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            درجة اللون: {{ adjustments.hue }}°
                        </label>
                        <input
                            v-model.number="adjustments.hue"
                            @input="applyAdjustments"
                            type="range"
                            min="-180"
                            max="180"
                            step="1"
                            class="w-full"
                        >
                    </div>
                </div>

                <!-- Filters -->
                <div class="p-4 border-t border-gray-200">
                    <h4 class="font-semibold text-gray-800 mb-4">الفلاتر</h4>
                    
                    <div class="grid grid-cols-2 gap-2">
                        <button
                            @click="applyFilter('sepia')"
                            class="p-3 text-sm bg-amber-100 text-amber-800 rounded-lg hover:bg-amber-200 transition-colors"
                        >
                            <i class="fas fa-palette mb-1"></i>
                            <div>سيبيا</div>
                        </button>
                        
                        <button
                            @click="applyFilter('grayscale')"
                            class="p-3 text-sm bg-gray-100 text-gray-800 rounded-lg hover:bg-gray-200 transition-colors"
                        >
                            <i class="fas fa-adjust mb-1"></i>
                            <div>رمادي</div>
                        </button>
                        
                        <button
                            @click="applyFilter('vintage')"
                            class="p-3 text-sm bg-orange-100 text-orange-800 rounded-lg hover:bg-orange-200 transition-colors"
                        >
                            <i class="fas fa-camera-retro mb-1"></i>
                            <div>كلاسيكي</div>
                        </button>
                        
                        <button
                            @click="applyFilter('blur')"
                            class="p-3 text-sm bg-blue-100 text-blue-800 rounded-lg hover:bg-blue-200 transition-colors"
                        >
                            <i class="fas fa-eye-slash mb-1"></i>
                            <div>ضبابي</div>
                        </button>
                    </div>
                </div>

                <!-- Resize -->
                <div class="p-4 border-t border-gray-200">
                    <h4 class="font-semibold text-gray-800 mb-4">تغيير الحجم</h4>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">العرض</label>
                            <input
                                v-model.number="resizeOptions.width"
                                type="number"
                                min="1"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                            >
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">الارتفاع</label>
                            <input
                                v-model.number="resizeOptions.height"
                                type="number"
                                min="1"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                            >
                        </div>
                        
                        <div class="flex items-center">
                            <input
                                v-model="resizeOptions.maintainAspectRatio"
                                type="checkbox"
                                id="maintain-aspect"
                                class="ml-2"
                            >
                            <label for="maintain-aspect" class="text-sm text-gray-700">
                                الحفاظ على النسبة
                            </label>
                        </div>
                        
                        <button
                            @click="resize"
                            class="w-full bg-purple-600 text-white py-2 rounded-lg hover:bg-purple-700"
                        >
                            تطبيق التغيير
                        </button>
                    </div>
                </div>

                <!-- Actions -->
                <div class="p-4 border-t border-gray-200">
                    <div class="space-y-2">
                        <button
                            @click="saveImage"
                            class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700"
                        >
                            <i class="fas fa-save ml-2"></i>
                            حفظ الصورة
                        </button>
                        
                        <button
                            @click="exportImage"
                            class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700"
                        >
                            <i class="fas fa-download ml-2"></i>
                            تصدير الصورة
                        </button>
                    </div>
                </div>
            </div>

            <!-- Canvas Area -->
            <div class="flex-1 p-4 bg-gray-50 flex items-center justify-center">
                <div class="max-w-full max-h-full overflow-auto">
                    <canvas
                        ref="previewCanvas"
                        class="max-w-full max-h-full border border-gray-300 rounded-lg shadow-lg bg-white"
                        style="image-rendering: pixelated;"
                    ></canvas>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted, watch, nextTick } from 'vue'
import { photoEditorService } from '@/Services/PhotoEditorService.js'

// Props
const props = defineProps({
    imageElement: {
        type: HTMLImageElement,
        required: true
    },
    onSave: {
        type: Function,
        default: () => {}
    }
})

// State
const previewCanvas = ref(null)
const canUndo = ref(false)
const canRedo = ref(false)

// Adjustments
const adjustments = reactive({
    brightness: 0,
    contrast: 0,
    saturation: 100,
    hue: 0
})

// Resize options
const resizeOptions = reactive({
    width: 0,
    height: 0,
    maintainAspectRatio: true
})

// Methods
const initEditor = async () => {
    try {
        const canvas = await photoEditorService.initWithImage(props.imageElement)
        
        // Copy canvas to preview
        previewCanvas.value.width = canvas.width
        previewCanvas.value.height = canvas.height
        const ctx = previewCanvas.value.getContext('2d')
        ctx.drawImage(canvas, 0, 0)
        
        // Set initial resize options
        resizeOptions.width = canvas.width
        resizeOptions.height = canvas.height
        
        updateHistoryButtons()
    } catch (error) {
        console.error('Error initializing photo editor:', error)
    }
}

const applyAdjustments = () => {
    photoEditorService.applyAdjustments(adjustments)
    updatePreview()
    updateHistoryButtons()
}

const applyFilter = (filterType) => {
    switch (filterType) {
        case 'sepia':
            photoEditorService.applySepia()
            break
        case 'grayscale':
            photoEditorService.applyGrayscale()
            break
        case 'vintage':
            photoEditorService.applyVintage()
            break
        case 'blur':
            photoEditorService.applyBlur(2)
            break
    }
    updatePreview()
    updateHistoryButtons()
}

const resize = () => {
    if (resizeOptions.width > 0 && resizeOptions.height > 0) {
        photoEditorService.resize(resizeOptions.width, resizeOptions.height)
        updatePreview()
        updateHistoryButtons()
    }
}

const undo = () => {
    photoEditorService.undo()
    updatePreview()
    updateHistoryButtons()
}

const redo = () => {
    photoEditorService.redo()
    updatePreview()
    updateHistoryButtons()
}

const reset = () => {
    photoEditorService.reset()
    
    // Reset adjustments
    adjustments.brightness = 0
    adjustments.contrast = 0
    adjustments.saturation = 100
    adjustments.hue = 0
    
    updatePreview()
    updateHistoryButtons()
}

const updatePreview = () => {
    const canvas = photoEditorService.canvas
    if (canvas && previewCanvas.value) {
        previewCanvas.value.width = canvas.width
        previewCanvas.value.height = canvas.height
        const ctx = previewCanvas.value.getContext('2d')
        ctx.drawImage(canvas, 0, 0)
    }
}

const updateHistoryButtons = () => {
    canUndo.value = photoEditorService.canUndo()
    canRedo.value = photoEditorService.canRedo()
}

const saveImage = async () => {
    try {
        const blob = await photoEditorService.getBlob('image/png', 1)
        props.onSave(blob)
    } catch (error) {
        console.error('Error saving image:', error)
    }
}

const exportImage = () => {
    const dataURL = photoEditorService.getDataURL('image/png', 1)
    const link = document.createElement('a')
    link.download = 'edited-image.png'
    link.href = dataURL
    link.click()
}

// Watchers
watch(() => resizeOptions.width, (newWidth) => {
    if (resizeOptions.maintainAspectRatio && props.imageElement) {
        const aspectRatio = props.imageElement.naturalHeight / props.imageElement.naturalWidth
        resizeOptions.height = Math.round(newWidth * aspectRatio)
    }
})

watch(() => resizeOptions.height, (newHeight) => {
    if (resizeOptions.maintainAspectRatio && props.imageElement) {
        const aspectRatio = props.imageElement.naturalWidth / props.imageElement.naturalHeight
        resizeOptions.width = Math.round(newHeight * aspectRatio)
    }
})

// Lifecycle
onMounted(() => {
    nextTick(() => {
        initEditor()
    })
})
</script>

<style scoped>
.photo-editor {
    direction: rtl;
}

input[type="range"] {
    -webkit-appearance: none;
    appearance: none;
    height: 6px;
    background: #e5e7eb;
    border-radius: 3px;
    outline: none;
}

input[type="range"]::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 18px;
    height: 18px;
    background: #7c3aed;
    border-radius: 50%;
    cursor: pointer;
}

input[type="range"]::-moz-range-thumb {
    width: 18px;
    height: 18px;
    background: #7c3aed;
    border-radius: 50%;
    cursor: pointer;
    border: none;
}
</style>
