<template>
    <div class="unified-design-editor">
        <!-- Import the StableDesignEditor component -->
        <StableDesignEditor
            :template="template"
            :context="context"
            :user="user"
            :hasActiveSubscription="hasActiveSubscription"
            :templatePrice="templatePrice"
            :canSaveAndDownload="canSaveAndDownload"
            @save="handleSave"
            @update="handleUpdate"
            @export="handleExport"
        />
    </div>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue'
import StableDesignEditor from '../../Pages/Admin/Templates/StableDesignEditor.vue'

const props = defineProps({
    template: {
        type: Object,
        required: true
    },
    context: {
        type: String,
        required: true,
        validator: (value) => ['admin', 'admin-client', 'client'].includes(value)
    },
    user: {
        type: Object,
        default: null
    },
    hasActiveSubscription: {
        type: Boolean,
        default: true
    },
    templatePrice: {
        type: Number,
        default: 0
    },
    canSaveAndDownload: {
        type: Boolean,
        default: true
    }
})

const emit = defineEmits(['save', 'update', 'export'])

// Handle save based on context
const handleSave = (saveData) => {
    console.log('🔄 UnifiedDesignEditor: Received save event:', saveData)
    emit('save', saveData)
}

// Handle update based on context
const handleUpdate = (designData) => {
    emit('update', {
        context: props.context,
        template: props.template,
        designData: designData
    })
}

// Handle export based on context
const handleExport = (exportData) => {
    emit('export', {
        context: props.context,
        template: props.template,
        exportData: exportData
    })
}
</script>

<style scoped>
.unified-design-editor {
    width: 100%;
    height: 100%;
}
</style>
