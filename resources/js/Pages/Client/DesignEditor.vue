<template>
    <Head title="محرر التصميم - سامقة">
        <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@300;400;600;700&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Changa:wght@400;700&display=swap" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    </Head>

    <div class="min-h-screen bg-gray-100" dir="rtl">
        <!-- Header -->
        <div class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-full mx-auto px-6 py-4">
                <div class="flex justify-between items-center">
                    <!-- Template Info -->
                    <div class="flex items-center space-x-4 space-x-reverse">
                        <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-palette text-white"></i>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-gray-800">محرر التصميم</h1>
                            <p class="text-sm text-gray-600">
                                {{ mode === 'edit' ? clientTemplate?.name : template?.name }}
                            </p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <!-- Save Button -->
                        <button
                            @click="saveDesign"
                            :disabled="isSaving"
                            class="bg-gradient-to-r from-purple-500 to-pink-500 text-white px-6 py-2 rounded-lg font-semibold hover:from-purple-600 hover:to-pink-600 transition-all duration-200 disabled:opacity-50"
                        >
                            <i class="fas fa-save ml-2"></i>
                            <span v-if="isSaving">جاري الحفظ...</span>
                            <span v-else>حفظ التصميم</span>
                        </button>

                        <!-- Export Button -->
                        <button
                            @click="showExportModal = true"
                            class="bg-green-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-green-600 transition-colors"
                        >
                            <i class="fas fa-download ml-2"></i>
                            تصدير
                        </button>

                        <!-- Back Button -->
                        <Link
                            :href="route('client.my-designs')"
                            class="bg-gray-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-gray-600 transition-colors"
                        >
                            <i class="fas fa-arrow-right ml-2"></i>
                            العودة
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex h-screen">
            <!-- Sidebar -->
            <div class="w-80 bg-white shadow-lg border-l border-gray-200 flex flex-col">
                <!-- Tabs -->
                <div class="flex border-b border-gray-200">
                    <button
                        @click="activeTab = 'elements'"
                        :class="activeTab === 'elements' ? 'bg-purple-50 text-purple-600 border-purple-600' : 'text-gray-600 hover:text-gray-800'"
                        class="flex-1 py-3 px-4 text-sm font-medium border-b-2 border-transparent transition-colors"
                    >
                        <i class="fas fa-shapes ml-2"></i>
                        العناصر
                    </button>
                    <button
                        @click="activeTab = 'properties'"
                        :class="activeTab === 'properties' ? 'bg-purple-50 text-purple-600 border-purple-600' : 'text-gray-600 hover:text-gray-800'"
                        class="flex-1 py-3 px-4 text-sm font-medium border-b-2 border-transparent transition-colors"
                    >
                        <i class="fas fa-cog ml-2"></i>
                        الخصائص
                    </button>
                </div>

                <!-- Tab Content -->
                <div class="flex-1 overflow-hidden">
                    <!-- Elements Tab -->
                    <div v-if="activeTab === 'elements'" class="h-full p-4 overflow-y-auto">
                        <div class="space-y-4">
                            <!-- Text Elements -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h3 class="font-semibold text-gray-800 mb-3">عناصر النص</h3>
                                <div class="grid grid-cols-2 gap-2">
                                    <button
                                        @click="addTextElement"
                                        class="p-3 bg-white border border-gray-200 rounded-lg hover:border-purple-300 hover:bg-purple-50 transition-colors text-center"
                                    >
                                        <i class="fas fa-font text-purple-500 mb-1"></i>
                                        <div class="text-xs">نص</div>
                                    </button>
                                    <button
                                        @click="addHeadingElement"
                                        class="p-3 bg-white border border-gray-200 rounded-lg hover:border-purple-300 hover:bg-purple-50 transition-colors text-center"
                                    >
                                        <i class="fas fa-heading text-purple-500 mb-1"></i>
                                        <div class="text-xs">عنوان</div>
                                    </button>
                                </div>
                            </div>

                            <!-- Shape Elements -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h3 class="font-semibold text-gray-800 mb-3">الأشكال</h3>
                                <div class="grid grid-cols-2 gap-2">
                                    <button
                                        @click="addRectangleElement"
                                        class="p-3 bg-white border border-gray-200 rounded-lg hover:border-purple-300 hover:bg-purple-50 transition-colors text-center"
                                    >
                                        <i class="fas fa-square text-purple-500 mb-1"></i>
                                        <div class="text-xs">مربع</div>
                                    </button>
                                    <button
                                        @click="addCircleElement"
                                        class="p-3 bg-white border border-gray-200 rounded-lg hover:border-purple-300 hover:bg-purple-50 transition-colors text-center"
                                    >
                                        <i class="fas fa-circle text-purple-500 mb-1"></i>
                                        <div class="text-xs">دائرة</div>
                                    </button>
                                </div>
                            </div>

                            <!-- Image Elements -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h3 class="font-semibold text-gray-800 mb-3">الصور</h3>
                                <button
                                    @click="addImageElement"
                                    class="w-full p-3 bg-white border border-gray-200 rounded-lg hover:border-purple-300 hover:bg-purple-50 transition-colors text-center"
                                >
                                    <i class="fas fa-image text-purple-500 mb-1"></i>
                                    <div class="text-xs">إضافة صورة</div>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Properties Tab -->
                    <div v-if="activeTab === 'properties'" class="h-full p-4 overflow-y-auto">
                        <div v-if="selectedElement" class="space-y-4">
                            <h3 class="font-semibold text-gray-800">خصائص العنصر</h3>
                            
                            <!-- Element properties will be rendered here -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="text-sm text-gray-600">اختر عنصراً لتحرير خصائصه</p>
                            </div>
                        </div>
                        <div v-else class="text-center py-8">
                            <i class="fas fa-mouse-pointer text-gray-400 text-3xl mb-3"></i>
                            <p class="text-gray-500">اختر عنصراً لتحرير خصائصه</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Canvas Area -->
            <div class="flex-1 flex flex-col">
                <!-- Canvas Toolbar -->
                <div class="bg-white border-b border-gray-200 px-6 py-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4 space-x-reverse">
                            <!-- Zoom Controls -->
                            <div class="flex items-center bg-gray-100 rounded-lg p-1">
                                <button
                                    @click="zoomOut"
                                    class="p-2 text-gray-600 hover:text-gray-800 rounded"
                                    title="تصغير"
                                >
                                    <i class="fas fa-minus"></i>
                                </button>
                                <span class="px-3 text-sm font-medium">{{ Math.round(zoom * 100) }}%</span>
                                <button
                                    @click="zoomIn"
                                    class="p-2 text-gray-600 hover:text-gray-800 rounded"
                                    title="تكبير"
                                >
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>

                            <!-- Canvas Size -->
                            <div class="text-sm text-gray-600">
                                {{ canvasWidth }} × {{ canvasHeight }} px
                            </div>
                        </div>

                        <!-- Watermark Notice -->
                        <div class="flex items-center text-sm text-purple-600 bg-purple-50 px-3 py-1 rounded-lg">
                            <i class="fas fa-shield-alt ml-2"></i>
                            يحتوي التصميم على علامة مائية محمية
                        </div>
                    </div>
                </div>

                <!-- Canvas Container -->
                <div class="flex-1 bg-gray-200 overflow-auto p-8">
                    <div class="flex items-center justify-center min-h-full">
                        <div
                            ref="designCanvas"
                            class="bg-white shadow-lg relative"
                            :style="{
                                width: canvasWidth * zoom + 'px',
                                height: canvasHeight * zoom + 'px',
                                transform: `scale(${zoom})`,
                                transformOrigin: 'center center'
                            }"
                        >
                            <!-- Canvas content will be rendered here -->
                            <div class="w-full h-full relative">
                                <!-- Design elements -->
                                <div
                                    v-for="element in elements"
                                    :key="element.id"
                                    :class="['absolute', { 'ring-2 ring-purple-500': selectedElement?.id === element.id }]"
                                    :style="getElementStyle(element)"
                                    @click="selectElement(element)"
                                >
                                    <!-- Element content based on type -->
                                    <div v-if="element.type === 'text'" v-html="element.content"></div>
                                    <img v-else-if="element.type === 'image'" :src="element.src" class="w-full h-full object-cover" />
                                    <div v-else-if="element.type === 'shape'" :class="element.shapeClass"></div>
                                </div>

                                <!-- Client Watermark - Always visible and non-removable -->
                                <div class="absolute bottom-4 right-4 pointer-events-none select-none">
                                    <div
                                        class="text-gray-600 font-bold transform -rotate-12 opacity-60"
                                        style="font-family: Cairo, sans-serif; font-size: 16px; text-shadow: 1px 1px 2px rgba(0,0,0,0.1);"
                                    >
                                        سامقة للتصميم
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Save Modal -->
        <div v-if="showSaveModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4">
                <h3 class="text-xl font-semibold mb-4">حفظ التصميم</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">اسم التصميم</label>
                        <input
                            v-model="designName"
                            type="text"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500"
                            placeholder="أدخل اسم التصميم"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">ملاحظات (اختياري)</label>
                        <textarea
                            v-model="designNotes"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500"
                            rows="3"
                            placeholder="أضف ملاحظات حول التصميم"
                        ></textarea>
                    </div>
                </div>
                <div class="flex space-x-3 space-x-reverse mt-6">
                    <button
                        @click="confirmSave"
                        :disabled="!designName.trim() || isSaving"
                        class="flex-1 bg-gradient-to-r from-purple-500 to-pink-500 text-white py-2 px-4 rounded-lg font-medium hover:from-purple-600 hover:to-pink-600 transition-all duration-200 disabled:opacity-50"
                    >
                        <span v-if="isSaving">جاري الحفظ...</span>
                        <span v-else>حفظ</span>
                    </button>
                    <button
                        @click="showSaveModal = false"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200"
                    >
                        إلغاء
                    </button>
                </div>
            </div>
        </div>

        <!-- Export Modal -->
        <div v-if="showExportModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4">
                <h3 class="text-xl font-semibold mb-4">تصدير التصميم</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">صيغة التصدير</label>
                        <select v-model="exportFormat" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                            <option value="png">PNG</option>
                            <option value="jpeg">JPEG</option>
                            <option value="svg">SVG</option>
                            <option value="pdf">PDF</option>
                        </select>
                    </div>
                    <div v-if="exportFormat !== 'svg'">
                        <label class="block text-sm font-medium text-gray-700 mb-2">الجودة (%)</label>
                        <input v-model="exportQuality" type="range" min="50" max="100" class="w-full">
                        <div class="text-center text-sm text-gray-600">{{ exportQuality }}%</div>
                    </div>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                        <div class="flex items-center">
                            <i class="fas fa-info-circle text-yellow-600 ml-2"></i>
                            <span class="text-sm text-yellow-800">سيتم تضمين العلامة المائية في التصدير</span>
                        </div>
                    </div>
                </div>
                <div class="flex space-x-3 space-x-reverse mt-6">
                    <button
                        @click="exportDesign"
                        :disabled="isExporting"
                        class="flex-1 bg-gradient-to-r from-purple-500 to-pink-500 text-white py-2 px-4 rounded-lg font-medium hover:from-purple-600 hover:to-pink-600 transition-all duration-200 disabled:opacity-50"
                    >
                        <span v-if="isExporting">جاري التصدير...</span>
                        <span v-else>تصدير</span>
                    </button>
                    <button
                        @click="showExportModal = false"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200"
                    >
                        إلغاء
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import ClientWatermarkService from '@/Services/ClientWatermarkService.js';
import ClientExportService from '@/Services/ClientExportService.js';

// Props
const props = defineProps({
    template: {
        type: Object,
        default: null
    },
    clientTemplate: {
        type: Object,
        default: null
    },
    mode: {
        type: String,
        default: 'create' // 'create' or 'edit'
    }
});

// State
const activeTab = ref('elements');
const selectedElement = ref(null);
const elements = ref([]);
const zoom = ref(1);
const canvasWidth = ref(800);
const canvasHeight = ref(600);

// Modals
const showSaveModal = ref(false);
const showExportModal = ref(false);
const designName = ref('');
const designNotes = ref('');
const exportFormat = ref('png');
const exportQuality = ref(90);

// Loading states
const isSaving = ref(false);
const isExporting = ref(false);

// Computed
const isEditMode = computed(() => props.mode === 'edit');

// Methods
const initializeEditor = () => {
    if (isEditMode.value && props.clientTemplate) {
        // Load existing client template
        designName.value = props.clientTemplate.name;
        designNotes.value = props.clientTemplate.notes || '';

        if (props.clientTemplate.design_data) {
            loadDesignData(props.clientTemplate.design_data);
        }

        if (props.clientTemplate.canvas_size) {
            const [width, height] = props.clientTemplate.canvas_size.split('x').map(Number);
            canvasWidth.value = width;
            canvasHeight.value = height;
        }
    } else if (props.template) {
        // Load template for new design
        designName.value = `تصميم جديد - ${props.template.name}`;

        if (props.template.design_data) {
            loadDesignData(props.template.design_data);
        }

        if (props.template.canvas_size) {
            const [width, height] = props.template.canvas_size.split('x').map(Number);
            canvasWidth.value = width;
            canvasHeight.value = height;
        }
    }
};

const loadDesignData = (designData) => {
    try {
        if (typeof designData === 'string') {
            designData = JSON.parse(designData);
        }

        // Appliquer et vérifier le filigrane client
        designData = ClientWatermarkService.sanitizeDesignData(designData);

        if (designData.elements) {
            elements.value = designData.elements;
        }

        if (designData.canvas) {
            canvasWidth.value = designData.canvas.width || 800;
            canvasHeight.value = designData.canvas.height || 600;
        }
    } catch (error) {
        console.error('Error loading design data:', error);
    }
};

const selectElement = (element) => {
    selectedElement.value = element;
    activeTab.value = 'properties';
};

const getElementStyle = (element) => {
    return {
        left: element.x + 'px',
        top: element.y + 'px',
        width: element.width + 'px',
        height: element.height + 'px',
        transform: element.rotation ? `rotate(${element.rotation}deg)` : '',
        zIndex: element.zIndex || 1,
        ...element.style
    };
};

// Element creation methods
const addTextElement = () => {
    const newElement = {
        id: Date.now(),
        type: 'text',
        content: 'نص جديد',
        x: 100,
        y: 100,
        width: 200,
        height: 50,
        style: {
            fontSize: '16px',
            fontFamily: 'Cairo, sans-serif',
            color: '#000000',
            textAlign: 'right'
        }
    };

    elements.value.push(newElement);
    selectElement(newElement);
};

const addHeadingElement = () => {
    const newElement = {
        id: Date.now(),
        type: 'text',
        content: 'عنوان جديد',
        x: 100,
        y: 100,
        width: 300,
        height: 60,
        style: {
            fontSize: '24px',
            fontFamily: 'Cairo, sans-serif',
            fontWeight: 'bold',
            color: '#000000',
            textAlign: 'right'
        }
    };

    elements.value.push(newElement);
    selectElement(newElement);
};

const addRectangleElement = () => {
    const newElement = {
        id: Date.now(),
        type: 'shape',
        shapeType: 'rectangle',
        x: 100,
        y: 100,
        width: 150,
        height: 100,
        style: {
            backgroundColor: '#3B82F6',
            borderRadius: '8px'
        }
    };

    elements.value.push(newElement);
    selectElement(newElement);
};

const addCircleElement = () => {
    const newElement = {
        id: Date.now(),
        type: 'shape',
        shapeType: 'circle',
        x: 100,
        y: 100,
        width: 100,
        height: 100,
        style: {
            backgroundColor: '#EF4444',
            borderRadius: '50%'
        }
    };

    elements.value.push(newElement);
    selectElement(newElement);
};

const addImageElement = () => {
    // This would typically open a file picker
    const newElement = {
        id: Date.now(),
        type: 'image',
        src: '/images/placeholder.jpg',
        x: 100,
        y: 100,
        width: 200,
        height: 150,
        style: {
            borderRadius: '8px'
        }
    };

    elements.value.push(newElement);
    selectElement(newElement);
};

// Zoom controls
const zoomIn = () => {
    zoom.value = Math.min(zoom.value + 0.1, 2);
};

const zoomOut = () => {
    zoom.value = Math.max(zoom.value - 0.1, 0.5);
};

// Save functionality
const saveDesign = () => {
    if (isEditMode.value) {
        // Direct save for edit mode
        confirmSave();
    } else {
        // Show modal for new design
        showSaveModal.value = true;
    }
};

const confirmSave = async () => {
    if (!designName.value.trim()) return;

    isSaving.value = true;

    try {
        let designData = {
            elements: elements.value,
            canvas: {
                width: canvasWidth.value,
                height: canvasHeight.value
            }
        };

        // Appliquer le filigrane client obligatoire
        designData = ClientWatermarkService.applyWatermark(designData);

        const payload = {
            name: designName.value,
            design_data: JSON.stringify(designData),
            editable_elements: elements.value.map(el => el.id),
            canvas_size: `${canvasWidth.value}x${canvasHeight.value}`,
            notes: designNotes.value
        };

        if (isEditMode.value) {
            // Update existing design
            await router.put(route('client.designs.update', props.clientTemplate.id), payload);
        } else {
            // Create new design
            payload.template_id = props.template.id;
            await router.post(route('client.designs.store'), payload);
        }

        showSaveModal.value = false;

    } catch (error) {
        console.error('Save error:', error);
        alert('حدث خطأ أثناء حفظ التصميم');
    } finally {
        isSaving.value = false;
    }
};

// Export functionality
const exportDesign = async () => {
    isExporting.value = true;

    try {
        // Préparer les données de design avec filigrane
        let designData = {
            elements: elements.value,
            canvas: {
                width: canvasWidth.value,
                height: canvasHeight.value
            }
        };

        // Appliquer le filigrane obligatoire
        designData = ClientWatermarkService.applyWatermark(designData);

        // Options d'export
        const exportOptions = {
            format: exportFormat.value,
            quality: exportQuality.value,
            width: canvasWidth.value,
            height: canvasHeight.value,
            filename: designName.value || 'design'
        };

        // Exporter avec le service client
        const result = await ClientExportService.exportDesign(designData, exportOptions);

        // Télécharger le fichier
        ClientExportService.downloadFile(result.blob, result.filename);

        showExportModal.value = false;

        // Optionnel : enregistrer l'activité d'export côté serveur
        if (isEditMode.value && props.clientTemplate) {
            try {
                await fetch(route('client.designs.export', props.clientTemplate.id), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        format: exportFormat.value,
                        quality: exportQuality.value
                    })
                });
            } catch (serverError) {
                console.warn('Failed to log export activity:', serverError);
            }
        }

    } catch (error) {
        console.error('Export error:', error);
        alert('حدث خطأ أثناء التصدير: ' + error.message);
    } finally {
        isExporting.value = false;
    }
};

// Lifecycle
onMounted(() => {
    initializeEditor();
});
</script>
