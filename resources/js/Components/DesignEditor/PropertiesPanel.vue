<template>
    <div class="properties-panel h-full flex flex-col bg-white">
        <!-- Header -->
        <div class="flex-shrink-0 p-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">خصائص العنصر</h3>
        </div>

        <!-- Content -->
        <div class="flex-1 overflow-y-auto">
            <div v-if="!selectedElement" class="p-8 text-center text-gray-500">
                <i class="fas fa-mouse-pointer text-4xl mb-4 text-gray-300"></i>
                <p>اختر عنصراً لتعديل خصائصه</p>
            </div>

            <div v-else class="p-4 space-y-6">
                <!-- Element Info -->
                <div class="bg-gray-50 p-3 rounded-lg">
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <i :class="getElementIcon(selectedElement.type)" class="text-purple-600"></i>
                        <div>
                            <div class="font-medium text-gray-800">{{ selectedElement.name }}</div>
                            <div class="text-sm text-gray-600">{{ getElementTypeName(selectedElement.type) }}</div>
                        </div>
                    </div>
                </div>

                <!-- Position & Size -->
                <div>
                    <h4 class="font-semibold text-gray-700 mb-3">الموضع والحجم</h4>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">X</label>
                            <input
                                v-model.number="localProperties.x"
                                @input="updateProperty('x', $event.target.value)"
                                type="number"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                            >
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Y</label>
                            <input
                                v-model.number="localProperties.y"
                                @input="updateProperty('y', $event.target.value)"
                                type="number"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                            >
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">العرض</label>
                            <input
                                v-model.number="localProperties.width"
                                @input="updateProperty('width', $event.target.value)"
                                type="number"
                                min="1"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                            >
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">الارتفاع</label>
                            <input
                                v-model.number="localProperties.height"
                                @input="updateProperty('height', $event.target.value)"
                                type="number"
                                min="1"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                            >
                        </div>
                    </div>
                </div>

                <!-- Transform -->
                <div>
                    <h4 class="font-semibold text-gray-700 mb-3">التحويل</h4>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                الدوران: {{ localProperties.rotation }}°
                            </label>
                            <input
                                v-model.number="localProperties.rotation"
                                @input="updateProperty('rotation', $event.target.value)"
                                type="range"
                                min="0"
                                max="360"
                                step="1"
                                class="w-full"
                            >
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                الشفافية: {{ Math.round(localProperties.opacity * 100) }}%
                            </label>
                            <input
                                v-model.number="localProperties.opacity"
                                @input="updateProperty('opacity', $event.target.value)"
                                type="range"
                                min="0"
                                max="1"
                                step="0.01"
                                class="w-full"
                            >
                        </div>
                    </div>
                </div>

                <!-- Text Properties -->
                <div v-if="selectedElement.type === 'text'">
                    <h4 class="font-semibold text-gray-700 mb-3">خصائص النص</h4>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">النص</label>
                            <textarea
                                v-model="localProperties.text"
                                @input="updateProperty('text', $event.target.value)"
                                rows="3"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                            ></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">حجم الخط</label>
                                <input
                                    v-model.number="localProperties.fontSize"
                                    @input="updateProperty('fontSize', $event.target.value)"
                                    type="number"
                                    min="8"
                                    max="200"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                                >
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">اللون</label>
                                <input
                                    v-model="localProperties.color"
                                    @input="updateProperty('color', $event.target.value)"
                                    type="color"
                                    class="w-full h-10 border border-gray-300 rounded-lg"
                                >
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">نوع الخط</label>
                            <select
                                v-model="localProperties.fontFamily"
                                @change="updateProperty('fontFamily', $event.target.value)"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                            >
                                <option value="Cairo">Cairo</option>
                                <option value="Amiri">Amiri</option>
                                <option value="Noto Sans Arabic">Noto Sans Arabic</option>
                                <option value="Arial">Arial</option>
                                <option value="Times New Roman">Times New Roman</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">وزن الخط</label>
                            <select
                                v-model="localProperties.fontWeight"
                                @change="updateProperty('fontWeight', $event.target.value)"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                            >
                                <option value="normal">عادي</option>
                                <option value="bold">عريض</option>
                                <option value="lighter">خفيف</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">محاذاة النص</label>
                            <div class="flex space-x-1 space-x-reverse">
                                <button
                                    v-for="align in textAlignments"
                                    :key="align.value"
                                    @click="updateProperty('textAlign', align.value)"
                                    :class="[
                                        'flex-1 p-2 border rounded transition-colors',
                                        localProperties.textAlign === align.value
                                            ? 'border-purple-500 bg-purple-50 text-purple-700'
                                            : 'border-gray-300 hover:border-gray-400'
                                    ]"
                                >
                                    <i :class="align.icon"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Image Properties -->
                <div v-if="selectedElement.type === 'image'">
                    <h4 class="font-semibold text-gray-700 mb-3">خصائص الصورة</h4>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">مصدر الصورة</label>
                            <input
                                v-model="localProperties.src"
                                @input="updateProperty('src', $event.target.value)"
                                type="url"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                                placeholder="رابط الصورة"
                            >
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">طريقة العرض</label>
                            <select
                                v-model="localProperties.objectFit"
                                @change="updateProperty('objectFit', $event.target.value)"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                            >
                                <option value="cover">تغطية</option>
                                <option value="contain">احتواء</option>
                                <option value="fill">ملء</option>
                                <option value="none">بدون تغيير</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                انحناء الزوايا: {{ localProperties.borderRadius }}px
                            </label>
                            <input
                                v-model.number="localProperties.borderRadius"
                                @input="updateProperty('borderRadius', $event.target.value)"
                                type="range"
                                min="0"
                                max="50"
                                step="1"
                                class="w-full"
                            >
                        </div>
                    </div>
                </div>

                <!-- Shape Properties -->
                <div v-if="['rectangle', 'circle', 'button'].includes(selectedElement.type)">
                    <h4 class="font-semibold text-gray-700 mb-3">خصائص الشكل</h4>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">لون الخلفية</label>
                            <input
                                v-model="localProperties.backgroundColor"
                                @input="updateProperty('backgroundColor', $event.target.value)"
                                type="color"
                                class="w-full h-10 border border-gray-300 rounded-lg"
                            >
                        </div>
                        <div v-if="selectedElement.type !== 'circle'">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                انحناء الزوايا: {{ localProperties.borderRadius }}px
                            </label>
                            <input
                                v-model.number="localProperties.borderRadius"
                                @input="updateProperty('borderRadius', $event.target.value)"
                                type="range"
                                min="0"
                                max="50"
                                step="1"
                                class="w-full"
                            >
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">لون الحدود</label>
                            <input
                                v-model="localProperties.borderColor"
                                @input="updateProperty('borderColor', $event.target.value)"
                                type="color"
                                class="w-full h-10 border border-gray-300 rounded-lg"
                            >
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                سمك الحدود: {{ localProperties.borderWidth }}px
                            </label>
                            <input
                                v-model.number="localProperties.borderWidth"
                                @input="updateProperty('borderWidth', $event.target.value)"
                                type="range"
                                min="0"
                                max="10"
                                step="1"
                                class="w-full"
                            >
                        </div>
                    </div>
                </div>

                <!-- Layer Order -->
                <div>
                    <h4 class="font-semibold text-gray-700 mb-3">ترتيب الطبقة</h4>
                    <div class="flex space-x-2 space-x-reverse">
                        <button
                            @click="moveLayer('front')"
                            class="flex-1 bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 text-sm"
                        >
                            للأمام
                        </button>
                        <button
                            @click="moveLayer('back')"
                            class="flex-1 bg-gray-600 text-white py-2 rounded-lg hover:bg-gray-700 text-sm"
                        >
                            للخلف
                        </button>
                    </div>
                </div>

                <!-- Actions -->
                <div>
                    <h4 class="font-semibold text-gray-700 mb-3">إجراءات</h4>
                    <div class="space-y-2">
                        <button
                            @click="duplicateElement"
                            class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 text-sm flex items-center justify-center space-x-2 space-x-reverse"
                        >
                            <i class="fas fa-copy"></i>
                            <span>تكرار العنصر</span>
                        </button>
                        <button
                            @click="deleteElement"
                            class="w-full bg-red-600 text-white py-2 rounded-lg hover:bg-red-700 text-sm flex items-center justify-center space-x-2 space-x-reverse"
                        >
                            <i class="fas fa-trash"></i>
                            <span>حذف العنصر</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, watch, computed } from 'vue'

// Props
const props = defineProps({
    selectedElement: {
        type: Object,
        default: null
    }
})

// Emits
const emit = defineEmits(['update-properties'])

// State
const localProperties = reactive({})

// Configuration
const textAlignments = [
    { value: 'right', icon: 'fas fa-align-right' },
    { value: 'center', icon: 'fas fa-align-center' },
    { value: 'left', icon: 'fas fa-align-left' }
]

// Watchers
watch(() => props.selectedElement, (newElement) => {
    if (newElement) {
        Object.assign(localProperties, newElement)
    } else {
        Object.keys(localProperties).forEach(key => {
            delete localProperties[key]
        })
    }
}, { immediate: true, deep: true })

// Methods
const updateProperty = (key, value) => {
    localProperties[key] = value
    emit('update-properties', { [key]: value })
}

const moveLayer = (direction) => {
    // This would be handled by the parent component
    console.log('Move layer:', direction)
}

const duplicateElement = () => {
    // This would be handled by the parent component
    console.log('Duplicate element')
}

const deleteElement = () => {
    if (confirm('هل أنت متأكد من حذف هذا العنصر؟')) {
        // This would be handled by the parent component
        console.log('Delete element')
    }
}

const getElementIcon = (type) => {
    const icons = {
        text: 'fas fa-font',
        image: 'fas fa-image',
        rectangle: 'fas fa-square',
        circle: 'fas fa-circle',
        button: 'fas fa-hand-pointer',
        line: 'fas fa-minus',
        shape: 'fas fa-shapes',
        icon: 'fas fa-icons'
    }
    return icons[type] || 'fas fa-square'
}

const getElementTypeName = (type) => {
    const names = {
        text: 'نص',
        image: 'صورة',
        rectangle: 'مستطيل',
        circle: 'دائرة',
        button: 'زر',
        line: 'خط',
        shape: 'شكل',
        icon: 'أيقونة'
    }
    return names[type] || 'عنصر'
}
</script>

<style scoped>
.properties-panel {
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
