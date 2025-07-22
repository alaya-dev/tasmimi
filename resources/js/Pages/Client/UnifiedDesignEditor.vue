<template>
    <Head title="محرر التصميم" />
    
    <ClientLayout>
        <template #header>
            <div class="flex justify-between items-center flex-row-reverse">
                <div class="flex items-center flex-row-reverse">
                    <Link 
                        :href="route('client.my-designs')" 
                        class="ml-4 text-gray-600 hover:text-gray-900 transition-colors duration-200"
                    >
                        <svg class="w-5 h-5 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </Link>
                    <div class="text-right">
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">محرر التصميم</h2>
                        <p class="mt-1 text-sm text-gray-600">{{ template.name || 'تصميمي' }}</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4 space-x-reverse">
                    <button
                        @click="saveDesign"
                        :disabled="saving"
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50"
                    >
                        <span v-if="saving">جاري الحفظ...</span>
                        <span v-else>حفظ التصميم</span>
                    </button>
                </div>
            </div>
        </template>

        <!-- Unified Design Editor -->
        <UnifiedDesignEditor
            :template="template"
            context="client"
            :user="$page.props.auth.user"
            @save="handleSave"
            @update="handleUpdate"
            @export="handleExport"
        />
    </ClientLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import ClientLayout from '@/Layouts/ClientLayout.vue'
import UnifiedDesignEditor from '@/Components/DesignEditor/UnifiedDesignEditor.vue'

const props = defineProps({
    template: {
        type: Object,
        required: true
    }
})

const saving = ref(false)
const page = usePage()

// Handle save for client context
const handleSave = async (saveData) => {
    try {
        saving.value = true
        console.log('🔧 Client: Handling save for template:', props.template.id)
        
        const response = await fetch(route('client.designs.update', props.template.id), {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                design_data: saveData.designDataString,
                canvas_size: saveData.canvasSize,
                design_notes: saveData.designNotes
            })
        })

        if (response.ok) {
            const result = await response.json()
            console.log('✅ Client: Design saved successfully', result)
            
            if (result.success) {
                alert('تم حفظ التصميم بنجاح')
            }
        } else {
            const errorData = await response.json()
            throw new Error(errorData.message || 'Failed to save design')
        }
    } catch (error) {
        console.error('❌ Client: Error saving design:', error)
        alert('خطأ في حفظ التصميم: ' + error.message)
    } finally {
        saving.value = false
    }
}

// Handle update for client context
const handleUpdate = (updateData) => {
    console.log('🔧 Client: Handling update:', updateData)
    // Handle real-time updates if needed
}

// Handle export for client context
const handleExport = (exportData) => {
    console.log('🔧 Client: Handling export:', exportData)
    // Handle export operations if needed
}

// Quick save function for header button
const saveDesign = () => {
    // This will trigger the save through the unified editor
    // The actual save logic is handled in handleSave
}
</script>

<style scoped>
/* Any client-specific styles */
</style>
