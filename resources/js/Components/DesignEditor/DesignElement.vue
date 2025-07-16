<template>
    <div
        :class="[
            'design-element absolute select-none',
            placementMode ? 'cursor-crosshair' : (element.locked ? 'cursor-not-allowed' : 'cursor-move'),
            {
                'ring-2 ring-purple-500 ring-opacity-50': selected,
                'border-2 border-dashed border-blue-300': element.isBackground && selected
            }
        ]"
        :style="elementStyle"
        @mousedown="startDrag"
        @click="handleClick"
    >
        <!-- Text Element -->
        <div
            v-if="element.type === 'text'"
            :style="textStyle"
            class="w-full h-full flex items-center"
            contenteditable
            @blur="updateText"
            @input="updateText"
        >
            {{ element.text || 'اكتب النص هنا' }}
        </div>

        <!-- Image Element -->
        <img
            v-else-if="element.type === 'image'"
            :src="element.src || '/images/placeholder.png'"
            :alt="element.name"
            :style="imageStyle"
            class="w-full h-full"
            @error="handleImageError"
        >

        <!-- Rectangle Element -->
        <div
            v-else-if="element.type === 'rectangle'"
            :style="rectangleStyle"
            class="w-full h-full"
        ></div>

        <!-- Circle Element -->
        <div
            v-else-if="element.type === 'circle'"
            :style="circleStyle"
            class="w-full h-full rounded-full"
        ></div>

        <!-- Button Element -->
        <button
            v-else-if="element.type === 'button'"
            :style="buttonStyle"
            class="w-full h-full flex items-center justify-center font-medium"
            @click.stop
        >
            {{ element.text || 'اضغط هنا' }}
        </button>

        <!-- Line Element -->
        <div
            v-else-if="element.type === 'line'"
            :style="lineStyle"
            class="w-full h-full"
        ></div>

        <!-- Icon Element -->
        <div
            v-else-if="element.type === 'icon'"
            :style="iconStyle"
            class="w-full h-full flex items-center justify-center"
        >
            <i :class="element.iconClass || 'fas fa-star'" :style="{ fontSize: element.fontSize + 'px' }"></i>
        </div>

        <!-- Shape Element -->
        <div
            v-else-if="element.type === 'shape'"
            :style="shapeStyle"
            class="w-full h-full flex items-center justify-center"
        >
            <i :class="getShapeIcon(element.shapeType)" class="text-4xl"></i>
        </div>

        <!-- Selection Handles -->
        <div v-if="selected" class="selection-handles">
            <!-- Corner handles -->
            <div
                v-for="handle in cornerHandles"
                :key="handle.position"
                :class="[
                    'handle corner-handle',
                    handle.position,
                    handle.cursor
                ]"
                @mousedown.stop="startResize(handle.position, $event)"
            ></div>

            <!-- Edge handles -->
            <div
                v-for="handle in edgeHandles"
                :key="handle.position"
                :class="[
                    'handle edge-handle',
                    handle.position,
                    handle.cursor
                ]"
                @mousedown.stop="startResize(handle.position, $event)"
            ></div>

            <!-- Rotation handle -->
            <div
                class="handle rotation-handle"
                @mousedown.stop="startRotate"
            >
                <i class="fas fa-redo text-xs"></i>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, nextTick } from 'vue'

// Props
const props = defineProps({
    element: {
        type: Object,
        required: true
    },
    selected: {
        type: Boolean,
        default: false
    },
    zoom: {
        type: Number,
        default: 1
    },
    placementMode: {
        type: Boolean,
        default: false
    }
})

// Emits
const emit = defineEmits(['select', 'update', 'delete'])

// State
const isDragging = ref(false)
const isResizing = ref(false)
const isRotating = ref(false)
const dragStart = ref({ x: 0, y: 0 })
const resizeHandle = ref('')

// Computed Styles
const elementStyle = computed(() => ({
    left: props.element.x + 'px',
    top: props.element.y + 'px',
    width: props.element.width + 'px',
    height: props.element.height + 'px',
    transform: `rotate(${props.element.rotation || 0}deg)`,
    opacity: props.element.opacity || 1,
    zIndex: props.element.zIndex || 0,
    visibility: props.element.visible !== false ? 'visible' : 'hidden'
}))

const textStyle = computed(() => ({
    fontSize: (props.element.fontSize || 16) + 'px',
    fontFamily: props.element.fontFamily || 'Cairo',
    fontWeight: props.element.fontWeight || 'normal',
    color: props.element.color || '#000000',
    textAlign: props.element.textAlign || 'right',
    lineHeight: props.element.lineHeight || 1.5,
    padding: '8px'
}))

const imageStyle = computed(() => ({
    objectFit: props.element.objectFit || 'cover',
    borderRadius: (props.element.borderRadius || 0) + 'px'
}))

const rectangleStyle = computed(() => ({
    backgroundColor: props.element.backgroundColor || '#8b5cf6',
    borderRadius: (props.element.borderRadius || 0) + 'px',
    border: props.element.borderWidth ? 
        `${props.element.borderWidth}px solid ${props.element.borderColor || '#000'}` : 'none'
}))

const circleStyle = computed(() => ({
    backgroundColor: props.element.backgroundColor || '#10b981',
    border: props.element.borderWidth ? 
        `${props.element.borderWidth}px solid ${props.element.borderColor || '#000'}` : 'none'
}))

const buttonStyle = computed(() => ({
    backgroundColor: props.element.backgroundColor || '#3b82f6',
    color: props.element.color || '#ffffff',
    fontSize: (props.element.fontSize || 14) + 'px',
    fontWeight: props.element.fontWeight || 'bold',
    borderRadius: (props.element.borderRadius || 6) + 'px',
    border: props.element.borderWidth ? 
        `${props.element.borderWidth}px solid ${props.element.borderColor || 'transparent'}` : 'none'
}))

const lineStyle = computed(() => ({
    backgroundColor: props.element.backgroundColor || '#6b7280',
    borderRadius: (props.element.borderRadius || 0) + 'px'
}))

const iconStyle = computed(() => ({
    color: props.element.color || '#374151',
    fontSize: (props.element.fontSize || 24) + 'px'
}))

const shapeStyle = computed(() => ({
    color: props.element.backgroundColor || '#8b5cf6'
}))

// Handle configurations
const cornerHandles = [
    { position: 'top-left', cursor: 'cursor-nw-resize' },
    { position: 'top-right', cursor: 'cursor-ne-resize' },
    { position: 'bottom-left', cursor: 'cursor-sw-resize' },
    { position: 'bottom-right', cursor: 'cursor-se-resize' }
]

const edgeHandles = [
    { position: 'top-center', cursor: 'cursor-n-resize' },
    { position: 'bottom-center', cursor: 'cursor-s-resize' },
    { position: 'left-center', cursor: 'cursor-w-resize' },
    { position: 'right-center', cursor: 'cursor-e-resize' }
]

// Methods
const selectElement = () => {
    emit('select', props.element)
}

const startDrag = (event) => {
    if (props.element.locked) return
    
    isDragging.value = true
    dragStart.value = {
        x: event.clientX - props.element.x,
        y: event.clientY - props.element.y
    }
    
    document.addEventListener('mousemove', handleDrag)
    document.addEventListener('mouseup', stopDrag)
    event.preventDefault()
}

const handleDrag = (event) => {
    if (!isDragging.value) return
    
    const newX = event.clientX - dragStart.value.x
    const newY = event.clientY - dragStart.value.y
    
    emit('update', props.element.id, {
        x: Math.max(0, newX),
        y: Math.max(0, newY)
    })
}

const stopDrag = () => {
    isDragging.value = false
    document.removeEventListener('mousemove', handleDrag)
    document.removeEventListener('mouseup', stopDrag)
}

const startResize = (handle, event) => {
    if (props.element.locked) return
    
    isResizing.value = true
    resizeHandle.value = handle
    dragStart.value = {
        x: event.clientX,
        y: event.clientY,
        width: props.element.width,
        height: props.element.height,
        elementX: props.element.x,
        elementY: props.element.y
    }
    
    document.addEventListener('mousemove', handleResize)
    document.addEventListener('mouseup', stopResize)
    event.preventDefault()
}

const handleResize = (event) => {
    if (!isResizing.value) return
    
    const deltaX = event.clientX - dragStart.value.x
    const deltaY = event.clientY - dragStart.value.y
    
    let newWidth = dragStart.value.width
    let newHeight = dragStart.value.height
    let newX = dragStart.value.elementX
    let newY = dragStart.value.elementY
    
    switch (resizeHandle.value) {
        case 'top-left':
            newWidth = Math.max(20, dragStart.value.width - deltaX)
            newHeight = Math.max(20, dragStart.value.height - deltaY)
            newX = dragStart.value.elementX + deltaX
            newY = dragStart.value.elementY + deltaY
            break
        case 'top-right':
            newWidth = Math.max(20, dragStart.value.width + deltaX)
            newHeight = Math.max(20, dragStart.value.height - deltaY)
            newY = dragStart.value.elementY + deltaY
            break
        case 'bottom-left':
            newWidth = Math.max(20, dragStart.value.width - deltaX)
            newHeight = Math.max(20, dragStart.value.height + deltaY)
            newX = dragStart.value.elementX + deltaX
            break
        case 'bottom-right':
            newWidth = Math.max(20, dragStart.value.width + deltaX)
            newHeight = Math.max(20, dragStart.value.height + deltaY)
            break
        case 'top-center':
            newHeight = Math.max(20, dragStart.value.height - deltaY)
            newY = dragStart.value.elementY + deltaY
            break
        case 'bottom-center':
            newHeight = Math.max(20, dragStart.value.height + deltaY)
            break
        case 'left-center':
            newWidth = Math.max(20, dragStart.value.width - deltaX)
            newX = dragStart.value.elementX + deltaX
            break
        case 'right-center':
            newWidth = Math.max(20, dragStart.value.width + deltaX)
            break
    }
    
    emit('update', props.element.id, {
        x: newX,
        y: newY,
        width: newWidth,
        height: newHeight
    })
}

const stopResize = () => {
    isResizing.value = false
    resizeHandle.value = ''
    document.removeEventListener('mousemove', handleResize)
    document.removeEventListener('mouseup', stopResize)
}

const startRotate = (event) => {
    if (props.element.locked) return
    
    isRotating.value = true
    document.addEventListener('mousemove', handleRotate)
    document.addEventListener('mouseup', stopRotate)
    event.preventDefault()
}

const handleRotate = (event) => {
    if (!isRotating.value) return
    
    // Calculate rotation based on mouse position
    const rect = event.target.closest('.design-element').getBoundingClientRect()
    const centerX = rect.left + rect.width / 2
    const centerY = rect.top + rect.height / 2
    
    const angle = Math.atan2(event.clientY - centerY, event.clientX - centerX)
    const rotation = (angle * 180 / Math.PI + 90) % 360
    
    emit('update', props.element.id, {
        rotation: Math.round(rotation)
    })
}

const stopRotate = () => {
    isRotating.value = false
    document.removeEventListener('mousemove', handleRotate)
    document.removeEventListener('mouseup', stopRotate)
}

const updateText = (event) => {
    const newText = event.target.textContent
    emit('update', props.element.id, { text: newText })
}

const handleImageError = (event) => {
    event.target.src = '/images/placeholder.png'
}

const getShapeIcon = (shapeType) => {
    const icons = {
        triangle: 'fas fa-play fa-rotate-90',
        diamond: 'fas fa-diamond',
        star: 'fas fa-star',
        heart: 'fas fa-heart',
        arrow: 'fas fa-arrow-right',
        polygon: 'fas fa-draw-polygon'
    }
    return icons[shapeType] || 'fas fa-square'
}

function handleClick(event) {
    if (!props.placementMode) {
        event.stopPropagation();
        selectElement();
    }
}
</script>

<style scoped>
.design-element {
    direction: ltr; /* Elements should be LTR for proper positioning */
}

.selection-handles {
    position: absolute;
    inset: -8px;
    pointer-events: none;
}

.handle {
    position: absolute;
    background: #7c3aed;
    border: 2px solid white;
    border-radius: 50%;
    pointer-events: auto;
}

.corner-handle {
    width: 12px;
    height: 12px;
}

.edge-handle {
    width: 8px;
    height: 8px;
}

.top-left { top: 0; left: 0; transform: translate(-50%, -50%); }
.top-right { top: 0; right: 0; transform: translate(50%, -50%); }
.bottom-left { bottom: 0; left: 0; transform: translate(-50%, 50%); }
.bottom-right { bottom: 0; right: 0; transform: translate(50%, 50%); }
.top-center { top: 0; left: 50%; transform: translate(-50%, -50%); }
.bottom-center { bottom: 0; left: 50%; transform: translate(-50%, 50%); }
.left-center { left: 0; top: 50%; transform: translate(-50%, -50%); }
.right-center { right: 0; top: 50%; transform: translate(50%, -50%); }

.rotation-handle {
    position: absolute;
    top: -30px;
    left: 50%;
    transform: translateX(-50%);
    width: 20px;
    height: 20px;
    background: #10b981;
    border: 2px solid white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    cursor: grab;
    pointer-events: auto;
}

.rotation-handle:active {
    cursor: grabbing;
}

/* Cursor classes */
.cursor-nw-resize { cursor: nw-resize; }
.cursor-ne-resize { cursor: ne-resize; }
.cursor-sw-resize { cursor: sw-resize; }
.cursor-se-resize { cursor: se-resize; }
.cursor-n-resize { cursor: n-resize; }
.cursor-s-resize { cursor: s-resize; }
.cursor-w-resize { cursor: w-resize; }
.cursor-e-resize { cursor: e-resize; }
</style>
