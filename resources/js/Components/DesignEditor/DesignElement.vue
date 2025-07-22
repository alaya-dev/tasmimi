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
        <svg
            v-else-if="element.type === 'circle'"
            :width="element.width"
            :height="element.height"
            class="w-full h-full"
        >
            <circle
                :cx="element.width / 2"
                :cy="element.height / 2"
                :r="Math.min(element.width, element.height) / 2"
                :fill="element.backgroundColor || '#10b981'"
                :stroke="element.borderColor || 'none'"
                :stroke-width="element.borderWidth || 0"
            />
        </svg>

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
            <span :style="{
                fontSize: (props.element.fontSize !== undefined && props.element.fontSize !== null && props.element.fontSize > 0)
                    ? props.element.fontSize + 'px'
                    : Math.min(props.element.width, props.element.height) + 'px',
                color: props.element.color || '#374151'
            }">
                {{ getIconSymbol(element.icon || element.iconClass) }}
            </span>
        </div>

        <!-- Shape Element -->
        <svg
            v-else-if="element.type === 'shape'"
            :width="element.width"
            :height="element.height"
            class="w-full h-full"
        >
            <template v-if="element.shapeType === 'triangle'">
                <polygon
                    :points="`
                        ${element.width / 2},0
                        0,${element.height}
                        ${element.width},${element.height}
                    `"
                    :fill="element.color || element.backgroundColor || '#8b5cf6'"
                />
            </template>
            <template v-else-if="element.shapeType === 'diamond'">
                <polygon
                    :points="`
                        ${element.width / 2},0
                        0,${element.height / 2}
                        ${element.width / 2},${element.height}
                        ${element.width},${element.height / 2}
                    `"
                    :fill="element.color || element.backgroundColor || '#8b5cf6'"
                />
            </template>
            <template v-else-if="element.shapeType === 'star'">
                <polygon
                    :points="getStarPoints(element.width / 2, element.height / 2, Math.min(element.width, element.height) / 2, Math.min(element.width, element.height) / 4, 5)"
                    :fill="element.color || element.backgroundColor || '#8b5cf6'"
                />
            </template>

            <template v-else-if="element.shapeType === 'arrow'">
                <polygon
                    :points="getArrowPoints(element.width, element.height)"
                    :fill="element.color || element.backgroundColor || '#8b5cf6'"
                />
            </template>
            <template v-else-if="element.shapeType === 'polygon'">
                <polygon
                    :points="getPolygonPoints(element.width / 2, element.height / 2, Math.min(element.width, element.height) / 2, 6)"
                    :fill="element.color || element.backgroundColor || '#8b5cf6'"
                />
            </template>
        </svg>

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
    color: props.element.color || '#374151'
    // fontSize is handled inline on the span element
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

    // For icons, also update fontSize to match the new size
    const updateData = {
        x: newX,
        y: newY,
        width: newWidth,
        height: newHeight
    }

    // If this is an icon, automatically adjust fontSize based on the smaller dimension
    if (props.element.type === 'icon') {
        const newFontSize = Math.min(newWidth, newHeight)
        // Constrain fontSize to reasonable bounds (8px - 200px)
        updateData.fontSize = Math.max(8, Math.min(200, newFontSize))

        console.log(`🔄 Icon Resize: ${newWidth}x${newHeight} → fontSize: ${updateData.fontSize}`)
    }

    emit('update', props.element.id, updateData)
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
        arrow: 'fas fa-arrow-right',
        polygon: 'fas fa-draw-polygon'
    }
    return icons[shapeType] || 'fas fa-square'
}

const getIconSymbol = (iconInput) => {
    // If it's already a symbol (Unicode character), return it
    if (iconInput && (iconInput.length === 1 || iconInput.length === 2)) {
        return iconInput
    }

    // If it's a FontAwesome class, convert it
    if (iconInput && typeof iconInput === 'string') {
        const iconMap = {
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
            'fas fa-info': 'ℹ',
            'fas fa-warning': '⚠',
            'fas fa-exclamation': '!',
            'fas fa-question': '?',
            'fas fa-lightbulb': '💡',
            'fas fa-bulb': '💡',
            'fas fa-mail': '📧',
            'fas fa-message': '💬',
            'fas fa-comment': '💬',
            'fas fa-users': '👥',
            'fas fa-group': '👥',
            'fas fa-file': '📄',
            'fas fa-folder': '📁',
            'fas fa-image': '🖼',
            'fas fa-play': '▶',
            'fas fa-pause': '⏸',
            'fas fa-stop': '⏹',
            'fas fa-key': '🔑',
            'fas fa-lock': '🔒',
            'fas fa-unlock': '🔓',
            'fas fa-bell': '🔔',
            'fas fa-sun': '☀',
            'fas fa-moon': '🌙',
            'fas fa-cloud': '☁',
            'fas fa-umbrella': '☂',
            'fas fa-coffee': '☕',
            'fas fa-apple': '🍎',
            'fas fa-cake': '🎂'
        }

        const cleanIconClass = iconInput.trim().toLowerCase()

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

function handleClick(event) {
    if (!props.placementMode) {
        event.stopPropagation();
        selectElement();
    }
}

// Helper functions for SVG shapes
function getStarPoints(cx, cy, outerRadius, innerRadius, numPoints) {
    const points = []
    const angle = Math.PI / numPoints
    for (let i = 0; i < 2 * numPoints; i++) {
        const r = i % 2 === 0 ? outerRadius : innerRadius
        const a = i * angle - Math.PI / 2
        points.push([
            cx + r * Math.cos(a),
            cy + r * Math.sin(a)
        ])
    }
    return points.map(p => p.join(",")).join(" ")
}



function getArrowPoints(width, height) {
    // Simple right arrow
    const w = width
    const h = height
    return `0,${h / 3} ${w * 0.7},${h / 3} ${w * 0.7},0 ${w},${h / 2} ${w * 0.7},${h} ${w * 0.7},${h * 2 / 3} 0,${h * 2 / 3}`
}

function getPolygonPoints(cx, cy, radius, numSides) {
    const points = []
    for (let i = 0; i < numSides; i++) {
        const angle = (2 * Math.PI * i) / numSides - Math.PI / 2
        points.push([
            cx + radius * Math.cos(angle),
            cy + radius * Math.sin(angle)
        ])
    }
    return points.map(p => p.join(",")).join(" ")
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
