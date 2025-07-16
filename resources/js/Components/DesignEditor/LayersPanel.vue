<template>
    <div class="layers-panel h-full flex flex-col bg-white">
        <!-- Header -->
        <div class="flex-shrink-0 p-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800">الطبقات</h3>
                <div class="flex items-center space-x-2 space-x-reverse">
                    <button
                        @click="toggleAllVisibility"
                        class="p-2 text-gray-500 hover:text-gray-700 rounded-lg hover:bg-gray-100"
                        title="إظهار/إخفاء الكل"
                    >
                        <i :class="allVisible ? 'fas fa-eye' : 'fas fa-eye-slash'"></i>
                    </button>
                    <button
                        @click="lockAllLayers"
                        class="p-2 text-gray-500 hover:text-gray-700 rounded-lg hover:bg-gray-100"
                        title="قفل/إلغاء قفل الكل"
                    >
                        <i :class="allLocked ? 'fas fa-lock' : 'fas fa-unlock'"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Layers List -->
        <div class="flex-1 overflow-y-auto">
            <div v-if="layers.length === 0" class="p-8 text-center text-gray-500">
                <i class="fas fa-layer-group text-4xl mb-4 text-gray-300"></i>
                <p>لا توجد طبقات</p>
                <p class="text-sm">أضف عناصر لإنشاء طبقات</p>
            </div>

            <draggable
                v-else
                v-model="localLayers"
                @end="handleReorder"
                item-key="id"
                class="space-y-1 p-2"
            >
                <template #item="{ element: layer, index }">
                    <div
                        :class="[
                            'layer-item p-3 rounded-lg border transition-all duration-200 cursor-pointer',
                            selectedLayerId === layer.id
                                ? 'border-purple-300 bg-purple-50 shadow-md'
                                : 'border-gray-200 bg-white hover:border-gray-300 hover:shadow-sm'
                        ]"
                        @click="selectLayer(layer.id)"
                    >
                        <div class="flex items-center justify-between">
                            <!-- Layer Info -->
                            <div class="flex items-center space-x-3 space-x-reverse flex-1">
                                <!-- Drag Handle -->
                                <div class="drag-handle cursor-move text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-grip-vertical"></i>
                                </div>

                                <!-- Layer Icon -->
                                <div class="flex-shrink-0">
                                    <i :class="getLayerIcon(layer.type)" class="text-gray-600"></i>
                                </div>

                                <!-- Layer Name -->
                                <div class="flex-1 min-w-0">
                                    <div
                                        v-if="editingLayerId === layer.id"
                                        class="flex items-center space-x-2 space-x-reverse"
                                    >
                                        <input
                                            ref="nameInput"
                                            v-model="editingName"
                                            @blur="saveLayerName(layer.id)"
                                            @keyup.enter="saveLayerName(layer.id)"
                                            @keyup.escape="cancelEdit"
                                            class="flex-1 px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:border-purple-500"
                                        >
                                        <button
                                            @click="saveLayerName(layer.id)"
                                            class="text-green-600 hover:text-green-700"
                                        >
                                            <i class="fas fa-check text-xs"></i>
                                        </button>
                                        <button
                                            @click="cancelEdit"
                                            class="text-red-600 hover:text-red-700"
                                        >
                                            <i class="fas fa-times text-xs"></i>
                                        </button>
                                    </div>
                                    <div v-else class="truncate">
                                        <span
                                            class="text-sm font-medium text-gray-800"
                                            @dblclick="startEdit(layer)"
                                        >
                                            {{ layer.name }}
                                        </span>
                                        <div class="text-xs text-gray-500">{{ getLayerTypeName(layer.type) }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Layer Controls -->
                            <div class="flex items-center space-x-1 space-x-reverse">
                                <!-- Visibility Toggle -->
                                <button
                                    @click.stop="toggleVisibility(layer.id)"
                                    :class="[
                                        'p-1 rounded transition-colors',
                                        layer.visible
                                            ? 'text-gray-600 hover:text-gray-800'
                                            : 'text-gray-400 hover:text-gray-600'
                                    ]"
                                    :title="layer.visible ? 'إخفاء' : 'إظهار'"
                                >
                                    <i :class="layer.visible ? 'fas fa-eye' : 'fas fa-eye-slash'" class="text-xs"></i>
                                </button>

                                <!-- Lock Toggle -->
                                <button
                                    @click.stop="toggleLock(layer.id)"
                                    :class="[
                                        'p-1 rounded transition-colors',
                                        layer.locked
                                            ? 'text-orange-600 hover:text-orange-700'
                                            : 'text-gray-400 hover:text-gray-600'
                                    ]"
                                    :title="layer.locked ? 'إلغاء القفل' : 'قفل'"
                                >
                                    <i :class="layer.locked ? 'fas fa-lock' : 'fas fa-unlock'" class="text-xs"></i>
                                </button>

                                <!-- Delete -->
                                <button
                                    @click.stop="deleteLayer(layer.id)"
                                    class="p-1 text-red-400 hover:text-red-600 rounded transition-colors"
                                    title="حذف"
                                >
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Layer Preview (for images) -->
                        <div v-if="layer.type === 'image' && layer.src" class="mt-2">
                            <img
                                :src="layer.src"
                                :alt="layer.name"
                                class="w-full h-16 object-cover rounded border border-gray-200"
                            >
                        </div>

                        <!-- Layer Content Preview (for text) -->
                        <div v-if="layer.type === 'text' && layer.text" class="mt-2">
                            <div
                                class="text-xs text-gray-600 bg-gray-50 p-2 rounded border truncate"
                                :style="{ fontFamily: layer.fontFamily, color: layer.color }"
                            >
                                {{ layer.text }}
                            </div>
                        </div>
                    </div>
                </template>
            </draggable>
        </div>

        <!-- Layer Actions -->
        <div class="flex-shrink-0 p-4 border-t border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between text-sm text-gray-600">
                <span>{{ layers.length }} طبقة</span>
                <div class="flex items-center space-x-2 space-x-reverse">
                    <button
                        @click="duplicateSelected"
                        :disabled="!selectedLayerId"
                        class="p-2 text-gray-500 hover:text-gray-700 rounded disabled:opacity-50"
                        title="تكرار الطبقة المحددة"
                    >
                        <i class="fas fa-copy"></i>
                    </button>
                    <button
                        @click="moveToTop"
                        :disabled="!selectedLayerId"
                        class="p-2 text-gray-500 hover:text-gray-700 rounded disabled:opacity-50"
                        title="نقل للأعلى"
                    >
                        <i class="fas fa-arrow-up"></i>
                    </button>
                    <button
                        @click="moveToBottom"
                        :disabled="!selectedLayerId"
                        class="p-2 text-gray-500 hover:text-gray-700 rounded disabled:opacity-50"
                        title="نقل للأسفل"
                    >
                        <i class="fas fa-arrow-down"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch, nextTick } from 'vue'
import draggable from 'vuedraggable'

// Props
const props = defineProps({
    layers: {
        type: Array,
        default: () => []
    },
    selectedLayerId: {
        type: String,
        default: null
    }
})

// Emits
const emit = defineEmits(['select-layer', 'delete-layer', 'reorder-layers', 'update-layer', 'duplicate-layer', 'move-layer-top', 'move-layer-bottom'])

// State
const localLayers = ref([...props.layers])
const editingLayerId = ref(null)
const editingName = ref('')
const nameInput = ref(null)

// Computed
const allVisible = computed(() => {
    return localLayers.value.length > 0 && localLayers.value.every(layer => layer.visible)
})

const allLocked = computed(() => {
    return localLayers.value.length > 0 && localLayers.value.every(layer => layer.locked)
})

// Watchers
watch(() => props.layers, (newLayers) => {
    localLayers.value = [...newLayers]
}, { deep: true })

// Methods
const selectLayer = (layerId) => {
    emit('select-layer', layerId)
}

const toggleVisibility = (layerId) => {
    const layer = localLayers.value.find(l => l.id === layerId)
    if (layer) {
        layer.visible = !layer.visible
        emit('update-layer', layerId, { visible: layer.visible })
    }
}

const toggleLock = (layerId) => {
    const layer = localLayers.value.find(l => l.id === layerId)
    if (layer) {
        layer.locked = !layer.locked
        emit('update-layer', layerId, { locked: layer.locked })
    }
}

const deleteLayer = (layerId) => {
    emit('request-delete-layer', layerId)
}

const handleReorder = () => {
    const newOrder = localLayers.value.map(layer => layer.id)
    emit('reorder-layers', newOrder)
}

const toggleAllVisibility = () => {
    const newVisibility = !allVisible.value
    localLayers.value.forEach(layer => {
        layer.visible = newVisibility
        emit('update-layer', layer.id, { visible: newVisibility })
    })
}

const lockAllLayers = () => {
    const newLockState = !allLocked.value
    localLayers.value.forEach(layer => {
        layer.locked = newLockState
        emit('update-layer', layer.id, { locked: newLockState })
    })
}

const startEdit = (layer) => {
    editingLayerId.value = layer.id
    editingName.value = layer.name
    nextTick(() => {
        if (nameInput.value) {
            nameInput.value.focus()
            nameInput.value.select()
        }
    })
}

const saveLayerName = (layerId) => {
    if (editingName.value.trim()) {
        const layer = localLayers.value.find(l => l.id === layerId)
        if (layer) {
            layer.name = editingName.value.trim()
            emit('update-layer', layerId, { name: layer.name })
        }
    }
    cancelEdit()
}

const cancelEdit = () => {
    editingLayerId.value = null
    editingName.value = ''
}

const duplicateSelected = () => {
    if (props.selectedLayerId) {
        emit('duplicate-layer', props.selectedLayerId)
    }
}

const moveToTop = () => {
    if (props.selectedLayerId) {
        emit('move-layer-top', props.selectedLayerId)
    }
}

const moveToBottom = () => {
    if (props.selectedLayerId) {
        emit('move-layer-bottom', props.selectedLayerId)
    }
}

const getLayerIcon = (type) => {
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

const getLayerTypeName = (type) => {
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
.layers-panel {
    direction: rtl;
}

.layer-item {
    user-select: none;
}

.drag-handle {
    cursor: grab;
}

.drag-handle:active {
    cursor: grabbing;
}
</style>
