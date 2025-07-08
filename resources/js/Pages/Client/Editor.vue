<template>
    <Head :title="__('client.design_editor')" />

    <div :dir="direction" class="min-h-screen bg-gray-100">
        <!-- Editor Header -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <!-- Left Side -->
                    <div class="flex items-center space-x-4" :class="isRTL ? 'space-x-reverse' : ''">
                        <Link :href="route('client.templates')" class="text-gray-600 hover:text-gray-900">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="isRTL ? 'M9 5l7 7-7 7' : 'M15 19l-7-7 7-7'"/>
                            </svg>
                        </Link>
                        <h1 class="text-xl font-semibold text-gray-900">{{ __('client.design_editor') }}</h1>
                        <span class="text-sm text-gray-500">{{ template?.name || 'تصميم جديد' }}</span>
                    </div>

                    <!-- Right Side -->
                    <div class="flex items-center space-x-4" :class="isRTL ? 'space-x-reverse' : ''">
                        <button 
                            @click="saveDesign"
                            :disabled="saving"
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-blue-700 transition-colors disabled:opacity-50"
                        >
                            {{ saving ? __('common.saving') : __('client.save_design') }}
                        </button>
                        <button 
                            @click="downloadDesign"
                            class="bg-green-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-green-700 transition-colors"
                        >
                            {{ __('client.download_design') }}
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <div class="flex h-screen">
            <!-- Tools Sidebar -->
            <div class="w-80 bg-white shadow-lg border-r border-gray-200 overflow-y-auto">
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6">أدوات التصميم</h2>
                    
                    <!-- Tool Tabs -->
                    <div class="flex space-x-1 mb-6 bg-gray-100 rounded-lg p-1" :class="isRTL ? 'space-x-reverse' : ''">
                        <button 
                            v-for="tab in toolTabs" 
                            :key="tab.id"
                            @click="activeTab = tab.id"
                            :class="activeTab === tab.id ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-600'"
                            class="flex-1 py-2 px-3 rounded-md text-sm font-medium transition-colors"
                        >
                            {{ tab.name }}
                        </button>
                    </div>

                    <!-- Text Tools -->
                    <div v-if="activeTab === 'text'" class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('client.add_text') }}</label>
                            <input 
                                v-model="newText"
                                type="text" 
                                placeholder="اكتب النص هنا..."
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                            <button 
                                @click="addText"
                                class="w-full mt-2 bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition-colors"
                            >
                                إضافة النص
                            </button>
                        </div>

                        <div v-if="selectedElement?.type === 'text'">
                            <h3 class="text-sm font-medium text-gray-700 mb-3">{{ __('client.text_properties') }}</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">{{ __('client.font_size') }}</label>
                                    <input 
                                        v-model="selectedElement.fontSize"
                                        type="range" 
                                        min="12" 
                                        max="72" 
                                        class="w-full"
                                    />
                                    <span class="text-xs text-gray-500">{{ selectedElement.fontSize }}px</span>
                                </div>

                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">{{ __('client.font_color') }}</label>
                                    <input 
                                        v-model="selectedElement.color"
                                        type="color" 
                                        class="w-full h-10 border border-gray-300 rounded-lg"
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">{{ __('client.font_family') }}</label>
                                    <select 
                                        v-model="selectedElement.fontFamily"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2"
                                    >
                                        <option value="Arial">Arial</option>
                                        <option value="Cairo">Cairo</option>
                                        <option value="Amiri">Amiri</option>
                                        <option value="Tajawal">Tajawal</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">{{ __('client.text_align') }}</label>
                                    <div class="flex space-x-2" :class="isRTL ? 'space-x-reverse' : ''">
                                        <button 
                                            v-for="align in ['left', 'center', 'right']" 
                                            :key="align"
                                            @click="selectedElement.textAlign = align"
                                            :class="selectedElement.textAlign === align ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'"
                                            class="flex-1 py-2 rounded-lg text-sm transition-colors"
                                        >
                                            {{ align === 'left' ? 'يسار' : align === 'center' ? 'وسط' : 'يمين' }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Background Tools -->
                    <div v-if="activeTab === 'background'" class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('client.change_background') }}</label>
                            
                            <div class="grid grid-cols-3 gap-2 mb-4">
                                <div 
                                    v-for="bg in backgroundOptions" 
                                    :key="bg.id"
                                    @click="changeBackground(bg)"
                                    class="h-16 rounded-lg cursor-pointer border-2 hover:border-blue-500 transition-colors"
                                    :class="currentBackground?.id === bg.id ? 'border-blue-500' : 'border-gray-200'"
                                    :style="{ background: bg.value }"
                                ></div>
                            </div>

                            <div>
                                <label class="block text-sm text-gray-600 mb-1">لون مخصص</label>
                                <input 
                                    v-model="customBackground"
                                    @change="changeBackground({ id: 'custom', value: customBackground })"
                                    type="color" 
                                    class="w-full h-10 border border-gray-300 rounded-lg"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Image Tools -->
                    <div v-if="activeTab === 'images'" class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('client.add_image') }}</label>
                            <input 
                                @change="handleImageUpload"
                                type="file" 
                                accept="image/*"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2"
                            />
                        </div>

                        <div v-if="selectedElement?.type === 'image'">
                            <h3 class="text-sm font-medium text-gray-700 mb-3">خصائص الصورة</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">العرض</label>
                                    <input 
                                        v-model="selectedElement.width"
                                        type="range" 
                                        min="50" 
                                        max="400" 
                                        class="w-full"
                                    />
                                    <span class="text-xs text-gray-500">{{ selectedElement.width }}px</span>
                                </div>

                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">الارتفاع</label>
                                    <input 
                                        v-model="selectedElement.height"
                                        type="range" 
                                        min="50" 
                                        max="400" 
                                        class="w-full"
                                    />
                                    <span class="text-xs text-gray-500">{{ selectedElement.height }}px</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Canvas Area -->
            <div class="flex-1 flex items-center justify-center p-8">
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <!-- Canvas -->
                    <div 
                        ref="canvas"
                        class="relative bg-white border border-gray-300"
                        :style="{ 
                            width: canvasWidth + 'px', 
                            height: canvasHeight + 'px',
                            background: currentBackground?.value || '#ffffff'
                        }"
                        @click="deselectElement"
                    >
                        <!-- Design Elements -->
                        <div 
                            v-for="element in designElements" 
                            :key="element.id"
                            @click.stop="selectElement(element)"
                            @mousedown="startDrag(element, $event)"
                            :class="selectedElement?.id === element.id ? 'ring-2 ring-blue-500' : ''"
                            class="absolute cursor-move"
                            :style="{
                                left: element.x + 'px',
                                top: element.y + 'px',
                                fontSize: element.fontSize + 'px',
                                color: element.color,
                                fontFamily: element.fontFamily,
                                textAlign: element.textAlign,
                                width: element.width + 'px',
                                height: element.height + 'px'
                            }"
                        >
                            <span v-if="element.type === 'text'">{{ element.content }}</span>
                            <img v-if="element.type === 'image'" :src="element.src" class="w-full h-full object-cover" />
                        </div>
                    </div>

                    <!-- Canvas Info -->
                    <div class="bg-gray-50 px-4 py-2 text-sm text-gray-600 text-center">
                        {{ canvasWidth }} × {{ canvasHeight }} بكسل
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { useTranslations } from '@/Composables/useTranslations';

// Props
const props = defineProps({
    template: Object,
    design: Object
});

// Composables
const { __, isRTL, direction } = useTranslations();

// State
const activeTab = ref('text');
const selectedElement = ref(null);
const saving = ref(false);
const newText = ref('');
const customBackground = ref('#ffffff');
const canvasWidth = 400;
const canvasHeight = 600;

const toolTabs = [
    { id: 'text', name: 'النص' },
    { id: 'background', name: 'الخلفية' },
    { id: 'images', name: 'الصور' }
];

const backgroundOptions = [
    { id: 1, value: '#ffffff' },
    { id: 2, value: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)' },
    { id: 3, value: 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)' },
    { id: 4, value: 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)' },
    { id: 5, value: 'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)' },
    { id: 6, value: '#000000' }
];

const currentBackground = ref(backgroundOptions[0]);
const designElements = ref([]);

// Methods
const addText = () => {
    if (!newText.value.trim()) return;
    
    const element = {
        id: Date.now(),
        type: 'text',
        content: newText.value,
        x: 50,
        y: 50,
        fontSize: 24,
        color: '#000000',
        fontFamily: 'Cairo',
        textAlign: 'right'
    };
    
    designElements.value.push(element);
    newText.value = '';
    selectElement(element);
};

const selectElement = (element) => {
    selectedElement.value = element;
};

const deselectElement = () => {
    selectedElement.value = null;
};

const changeBackground = (bg) => {
    currentBackground.value = bg;
};

const handleImageUpload = (event) => {
    const file = event.target.files[0];
    if (!file) return;
    
    const reader = new FileReader();
    reader.onload = (e) => {
        const element = {
            id: Date.now(),
            type: 'image',
            src: e.target.result,
            x: 50,
            y: 50,
            width: 150,
            height: 150
        };
        
        designElements.value.push(element);
        selectElement(element);
    };
    reader.readAsDataURL(file);
};

const startDrag = (element, event) => {
    const startX = event.clientX - element.x;
    const startY = event.clientY - element.y;
    
    const onMouseMove = (e) => {
        element.x = e.clientX - startX;
        element.y = e.clientY - startY;
    };
    
    const onMouseUp = () => {
        document.removeEventListener('mousemove', onMouseMove);
        document.removeEventListener('mouseup', onMouseUp);
    };
    
    document.addEventListener('mousemove', onMouseMove);
    document.addEventListener('mouseup', onMouseUp);
};

const saveDesign = async () => {
    saving.value = true;
    
    try {
        const designData = {
            elements: designElements.value,
            background: currentBackground.value,
            canvas: { width: canvasWidth, height: canvasHeight }
        };
        
        // Save to backend
        await router.post(route('client.designs.store'), {
            name: props.template?.name || 'تصميم جديد',
            data: designData
        });
        
        // Show success message
    } catch (error) {
        console.error('Error saving design:', error);
    } finally {
        saving.value = false;
    }
};

const downloadDesign = () => {
    // Implement download functionality
    console.log('Download design');
};

// Initialize
onMounted(() => {
    if (props.design) {
        designElements.value = props.design.data.elements || [];
        currentBackground.value = props.design.data.background || backgroundOptions[0];
    }
});
</script>
