<template>
    <Head title="محرر التصميم - إدارة القوالب" />

    <AdminLayoutSidebar>
        <template #breadcrumb>
            <Link :href="route('admin.templates.index')" class="text-gray-500 hover:text-gray-700">
                إدارة القوالب
            </Link>
            <svg class="w-5 h-5 text-gray-400 mx-2 rotate-180" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
            </svg>
            <span class="text-gray-500">محرر التصميم</span>
        </template>

        <template #header>
            <div class="flex justify-between items-center flex-row-reverse">
                <div class="flex items-center flex-row-reverse">
                    <Link
                        :href="route('admin.templates.index')"
                        class="ml-4 text-gray-600 hover:text-gray-900 transition-colors duration-200"
                    >
                        <svg class="w-5 h-5 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </Link>
                    <div class="text-right">
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">محرر التصميم</h2>
                        <p class="mt-1 text-sm text-gray-600">{{ template.name }}</p>
                    </div>
                </div>
            </div>
        </template>

        <!-- Unified Design Editor -->
        <UnifiedDesignEditor
            :template="template"
            context="admin"
            @save="handleSave"
            @update="handleUpdate"
            @export="handleExport"
        />
    </AdminLayoutSidebar>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayoutSidebar from '@/Layouts/AdminLayoutSidebar.vue'
import UnifiedDesignEditor from '@/Components/DesignEditor/UnifiedDesignEditor.vue'

const props = defineProps({
    template: {
        type: Object,
        required: true
    }
})

// Handle save for admin context
const handleSave = async (saveData) => {
    try {
        console.log('🔧 Admin UnifiedDesign: Received save event for template:', props.template.id)
        console.log('🔧 Admin UnifiedDesign: Save data:', saveData)

        const response = await fetch(route('admin.templates.design.save', props.template.id), {
            method: 'POST',
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

        console.log('🔧 Admin UnifiedDesign: Response status:', response.status)

        if (response.ok) {
            const result = await response.json()
            console.log('✅ Admin UnifiedDesign: Design saved successfully', result)

            if (result.success) {
                alert('تم حفظ التصميم بنجاح')
            }
        } else {
            const errorData = await response.json()
            console.error('❌ Admin UnifiedDesign: Server error:', errorData)
            throw new Error(errorData.message || 'Failed to save design')
        }
    } catch (error) {
        console.error('❌ Admin UnifiedDesign: Error saving design:', error)
        alert('خطأ في حفظ التصميم: ' + error.message)
    }
}

// Handle update for admin context
const handleUpdate = (updateData) => {
    console.log('🔧 Admin: Handling update:', updateData)
    // Handle real-time updates if needed
}

// Handle export for admin context
const handleExport = (exportData) => {
    console.log('🔧 Admin: Handling export:', exportData)
    // Handle export operations if needed
}
</script>

<style scoped>
/* Any admin-specific styles */
</style>
