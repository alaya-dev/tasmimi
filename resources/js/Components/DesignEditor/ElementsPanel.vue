<template>
    <div class="elements-panel h-full flex flex-col bg-white">
        <!-- Header -->
        <div class="flex-shrink-0 p-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">العناصر</h3>
        </div>

        <!-- Background Image Upload -->
        <div class="p-4 border-b border-gray-100 flex flex-col gap-2">
            <input ref="backgroundInput" type="file" accept="image/*" @change="handleBackgroundSelect" class="hidden" />
            <button @click="$refs.backgroundInput.click()" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-4 py-3 rounded-lg hover:from-blue-700 hover:to-indigo-700 flex items-center justify-center space-x-2 space-x-reverse font-medium shadow-lg">
                <i class="fas fa-image"></i>
                <span>إضافة خلفية للصفحة</span>
            </button>
        </div>

        <!-- Content -->
        <div class="flex-1 overflow-y-auto p-4">
            <!-- Basic Elements -->
            <div class="mb-6">
                <h4 class="font-semibold text-gray-700 mb-3">العناصر الأساسية</h4>
                <div class="grid grid-cols-2 gap-3">
                    <button
                        @click="addElement('text')"
                        class="p-4 bg-blue-50 hover:bg-blue-100 rounded-lg border border-blue-200 transition-colors group"
                    >
                        <i class="fas fa-font text-2xl text-blue-600 mb-2 group-hover:scale-110 transition-transform"></i>
                        <div class="text-sm font-medium text-blue-800">نص</div>
                    </button>

                    <button
                        @click="$refs.imageInput.click()"
                        class="p-4 bg-green-50 hover:bg-green-100 rounded-lg border border-green-200 transition-colors group"
                    >
                        <i class="fas fa-image text-2xl text-green-600 mb-2 group-hover:scale-110 transition-transform"></i>
                        <div class="text-sm font-medium text-green-800">صورة</div>
                    </button>
                    <input ref="imageInput" type="file" accept="image/*" @change="handleImageSelect" class="hidden" />

                    <button
                        @click="addElement('rectangle')"
                        class="p-4 bg-purple-50 hover:bg-purple-100 rounded-lg border border-purple-200 transition-colors group"
                    >
                        <i class="fas fa-square text-2xl text-purple-600 mb-2 group-hover:scale-110 transition-transform"></i>
                        <div class="text-sm font-medium text-purple-800">مستطيل</div>
                    </button>

                    <button
                        @click="addElement('circle')"
                        class="p-4 bg-orange-50 hover:bg-orange-100 rounded-lg border border-orange-200 transition-colors group"
                    >
                        <i class="fas fa-circle text-2xl text-orange-600 mb-2 group-hover:scale-110 transition-transform"></i>
                        <div class="text-sm font-medium text-orange-800">دائرة</div>
                    </button>

                    <button
                        @click="addElement('line')"
                        class="p-4 bg-gray-50 hover:bg-gray-100 rounded-lg border border-gray-200 transition-colors group"
                    >
                        <i class="fas fa-minus text-2xl text-gray-600 mb-2 group-hover:scale-110 transition-transform"></i>
                        <div class="text-sm font-medium text-gray-800">خط</div>
                    </button>
                </div>
            </div>

            <!-- Shapes -->
            <div class="mb-6">
                <h4 class="font-semibold text-gray-700 mb-3">الأشكال</h4>
                <div class="grid grid-cols-3 gap-2">
                    <button
                        v-for="shape in shapes"
                        :key="shape.type"
                        @click="addShape(shape)"
                        class="p-3 bg-gray-50 hover:bg-gray-100 rounded-lg border border-gray-200 transition-colors group"
                        :title="shape.name"
                    >
                        <i :class="shape.icon" class="text-lg text-gray-600 group-hover:scale-110 transition-transform"></i>
                    </button>
                </div>
            </div>

            <!-- Icons -->
            <div class="mb-6">
                <h4 class="font-semibold text-gray-700 mb-3">الأيقونات</h4>
                <div class="grid grid-cols-4 gap-2">
                    <button
                        v-for="icon in icons"
                        :key="icon.class"
                        @click="addIcon(icon)"
                        class="p-3 bg-gray-50 hover:bg-gray-100 rounded-lg border border-gray-200 transition-colors group"
                        :title="icon.name"
                    >
                        <span class="text-lg text-gray-600 group-hover:scale-110 transition-transform block">{{ icon.symbol }}</span>
                    </button>
                </div>
            </div>


        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'

// Emits
const emit = defineEmits(['add-element', 'add-background', 'add-image'])

// State

// Data
const shapes = [
    { type: 'triangle', name: 'مثلث', icon: 'fas fa-play fa-rotate-90' },
    { type: 'diamond', name: 'معين', icon: 'fas fa-diamond' },
    { type: 'star', name: 'نجمة', icon: 'fas fa-star' },
    { type: 'arrow', name: 'سهم', icon: 'fas fa-arrow-right' },
  //  { type: 'polygon', name: 'مضلع', icon: 'fas fa-draw-polygon' }
]

const icons = [
    // Basic & Communication
    { symbol: '🏠', class: 'fas fa-home', name: 'منزل' },
    { symbol: '👤', class: 'fas fa-user', name: 'مستخدم' },
    { symbol: '✉', class: 'fas fa-envelope', name: 'بريد' },
    { symbol: '📞', class: 'fas fa-phone', name: 'هاتف' },
    { symbol: '💬', class: 'fas fa-message', name: 'رسالة' },
    { symbol: '👥', class: 'fas fa-users', name: 'مستخدمون' },

    // Time & Location
    { symbol: '📅', class: 'fas fa-calendar', name: 'تقويم' },
    { symbol: '🕐', class: 'fas fa-clock', name: 'ساعة' },
    { symbol: '📍', class: 'fas fa-location-dot', name: 'موقع' },

    // Media & Entertainment
    { symbol: '📷', class: 'fas fa-camera', name: 'كاميرا' },
    { symbol: '🎵', class: 'fas fa-music', name: 'موسيقى' },
    { symbol: '🎥', class: 'fas fa-video', name: 'فيديو' },
    { symbol: '🖼', class: 'fas fa-image', name: 'صورة' },
    { symbol: '▶', class: 'fas fa-play', name: 'تشغيل' },

    // Shopping & Business
    { symbol: '🛒', class: 'fas fa-shopping-cart', name: 'سلة' },
    { symbol: '🎁', class: 'fas fa-gift', name: 'هدية' },
    { symbol: '💰', class: 'fas fa-money', name: 'مال' },
    { symbol: '🏪', class: 'fas fa-store', name: 'متجر' },

    // Transport
    { symbol: '🚗', class: 'fas fa-car', name: 'سيارة' },
    { symbol: '✈', class: 'fas fa-plane', name: 'طائرة' },
    { symbol: '🚲', class: 'fas fa-bicycle', name: 'دراجة' },
    { symbol: '🚂', class: 'fas fa-train', name: 'قطار' },

    // Education & Achievement
    { symbol: '🎓', class: 'fas fa-graduation-cap', name: 'تخرج' },
    { symbol: '🏆', class: 'fas fa-trophy', name: 'كأس' },
    { symbol: '📚', class: 'fas fa-book', name: 'كتاب' },
    { symbol: '✏', class: 'fas fa-pencil', name: 'قلم' },

    // Emotions & Symbols
    { symbol: '❤', class: 'fas fa-heart', name: 'قلب' },
    { symbol: '★', class: 'fas fa-star', name: 'نجمة' },
    { symbol: '😊', class: 'fas fa-smile', name: 'ابتسامة' },
    { symbol: '👍', class: 'fas fa-thumbs-up', name: 'إعجاب' },

    // Actions & Tools
    { symbol: '⚙', class: 'fas fa-cog', name: 'إعدادات' },
    { symbol: '🔍', class: 'fas fa-search', name: 'بحث' },
    { symbol: '✓', class: 'fas fa-check', name: 'صح' },
    { symbol: '✕', class: 'fas fa-times', name: 'خطأ' },
    { symbol: '💾', class: 'fas fa-save', name: 'حفظ' },
    { symbol: '🗑', class: 'fas fa-trash', name: 'حذف' },

    // Weather & Nature
    { symbol: '☀', class: 'fas fa-sun', name: 'شمس' },
    { symbol: '🌙', class: 'fas fa-moon', name: 'قمر' },
    { symbol: '☁', class: 'fas fa-cloud', name: 'سحابة' },
    { symbol: '🌧', class: 'fas fa-rain', name: 'مطر' },

    // Food & Drink
    { symbol: '☕', class: 'fas fa-coffee', name: 'قهوة' },
    { symbol: '🍎', class: 'fas fa-apple', name: 'تفاحة' },
    { symbol: '🎂', class: 'fas fa-cake', name: 'كعكة' },
    { symbol: '🍕', class: 'fas fa-pizza', name: 'بيتزا' },

    // Security & Info
    { symbol: '🔒', class: 'fas fa-lock', name: 'قفل' },
    { symbol: '🔑', class: 'fas fa-key', name: 'مفتاح' },
    { symbol: 'ℹ', class: 'fas fa-info', name: 'معلومات' },
    { symbol: '⚠', class: 'fas fa-warning', name: 'تحذير' },
    { symbol: '💡', class: 'fas fa-lightbulb', name: 'فكرة' }
]














// Methods
const addElement = (type) => {
    const elementData = getElementData(type)
    emit('add-element', elementData)
}

const addShape = (shape) => {
    const elementData = {
        type: 'shape',
        name: shape.name,
        width: 100,
        height: 100,
        properties: {
            shapeType: shape.type,
            backgroundColor: '#8b5cf6',
            borderColor: '#7c3aed',
            borderWidth: 2
        }
    }
    emit('add-element', elementData)
}

const addIcon = (icon) => {
    const elementData = {
        type: 'icon',
        name: icon.name,
        width: 50,
        height: 50,
        properties: {
            icon: icon.symbol,
            iconClass: icon.class, // Keep for backward compatibility
            color: '#374151',
            fontSize: 24
        }
    }
    emit('add-element', elementData)
}



const getElementData = (type) => {
    switch (type) {
        case 'text':
            return {
                type: 'text',
                name: 'نص',
                width: 200,
                height: 50,
                properties: {
                    text: 'اكتب النص هنا',
                    fontSize: 16,
                    fontFamily: 'Cairo',
                    fontWeight: 'normal',
                    color: '#374151',
                    textAlign: 'right',
                    lineHeight: 1.5
                }
            }

        case 'image':
            return {
                type: 'image',
                name: 'صورة',
                width: 300,
                height: 200,
                properties: {
                    src: '/images/placeholder.png',
                    objectFit: 'cover',
                    borderRadius: 0
                }
            }

        case 'rectangle':
            return {
                type: 'rectangle',
                name: 'مستطيل',
                width: 200,
                height: 100,
                properties: {
                    backgroundColor: '#8b5cf6',
                    borderColor: '#7c3aed',
                    borderWidth: 0,
                    borderRadius: 8
                }
            }

        case 'circle':
            return {
                type: 'circle',
                name: 'دائرة',
                width: 100,
                height: 100,
                properties: {
                    backgroundColor: '#10b981',
                    borderColor: '#059669',
                    borderWidth: 0
                }
            }

        case 'line':
            return {
                type: 'line',
                name: 'خط',
                width: 200,
                height: 2,
                properties: {
                    backgroundColor: '#6b7280',
                    borderRadius: 0
                }
            }

        default:
            return {
                type: 'rectangle',
                name: 'عنصر',
                width: 100,
                height: 100,
                properties: {}
            }
    }
}

function handleBackgroundSelect(e) {
    const file = e.target.files[0]
    if (file) {
        const reader = new FileReader()
        reader.onload = (event) => {
            emit('add-background', event.target.result)
        }
        reader.readAsDataURL(file)
    }
    e.target.value = ''
}
function handleImageSelect(e) {
    const file = e.target.files[0]
    if (file) {
        const reader = new FileReader()
        reader.onload = (event) => {
            emit('add-image', event.target.result)
        }
        reader.readAsDataURL(file)
    }
    e.target.value = ''
}
</script>

<style scoped>
.elements-panel {
    direction: rtl;
}
</style>
