<template>
    <Head title="محرر التصميم" />

    <ClientLayout>
        <template #header>
            <div class="flex justify-between items-center flex-row-reverse">
                <div class="flex items-center flex-row-reverse">
                    <button
                        @click="goBack"
                        class="ml-4 text-gray-600 hover:text-gray-900 transition-colors duration-200"
                    >
                        <svg class="w-5 h-5 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                    <div class="text-right">
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">محرر التصميم</h2>
                        <div class="flex items-center space-x-2 space-x-reverse mt-1">
                            <p class="text-sm text-gray-600">{{ template.name || 'تصميمي' }}</p>
                            <!-- Template Status Badge -->
                            <span v-if="templatePrice === 0" class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full font-semibold">
                                مجاني
                            </span>
                            <span v-else-if="canSaveAndDownload" class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full font-semibold">
                                وصول كامل
                            </span>
                            <span v-else class="text-xs bg-orange-100 text-orange-800 px-2 py-1 rounded-full font-semibold">
                                معاينة - {{ templatePrice }} ر.س
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center space-x-4 space-x-reverse">
                    <!-- Save Button - Conditional based on permissions -->
                    <button
                        v-if="canSaveAndDownload"
                        @click="saveDesign"
                        :disabled="saving"
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50"
                    >
                        <span v-if="saving">جاري الحفظ...</span>
                        <span v-else>حفظ التصميم</span>
                    </button>

                    <!-- Purchase/Subscribe Options for restricted users -->
                    <div v-else class="flex flex-col space-y-3">
                        <div class="flex items-center space-x-3 space-x-reverse">
                            <button
                                @click="redirectToPurchase"
                                class="bg-orange-600 text-white px-6 py-2 rounded-lg hover:bg-orange-700 transition-colors flex items-center space-x-2 space-x-reverse"
                            >
                                <i class="fas fa-shopping-cart"></i>
                                <span>شراء القالب ({{ templatePrice }} ريال)</span>
                            </button>
                            <span class="text-gray-400">أو</span>
                            <Link
                                :href="route('client.subscriptions.index')"
                                class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center space-x-2 space-x-reverse"
                            >
                                <i class="fas fa-crown"></i>
                                <span>الاشتراك الشهري</span>
                            </Link>
                        </div>
                        <div class="text-sm text-gray-600 bg-blue-50 px-4 py-3 rounded-lg border border-blue-200">
                            <div class="flex items-start space-x-2 space-x-reverse">
                                <i class="fas fa-info-circle text-blue-600 mt-0.5"></i>
                                <div>
                                    <p class="font-semibold text-blue-800 mb-1">يمكنك الآن:</p>
                                    <ul class="text-blue-700 space-y-1">
                                        <li>• تعديل التصميم والمعاينة</li>
                                        <li>• تجربة جميع الأدوات</li>
                                    </ul>
                                    <p class="font-semibold text-blue-800 mt-2 mb-1">للحفظ والتحميل تحتاج:</p>
                                    <ul class="text-blue-700 space-y-1">
                                        <li>• شراء هذا القالب، أو</li>
                                        <li>• الاشتراك الشهري للوصول لجميع القوالب</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <!-- Unified Design Editor -->
        <UnifiedDesignEditor
            :template="template"
            context="client"
            :user="$page.props.auth.user"
            :hasActiveSubscription="hasActiveSubscription"
            :templatePrice="templatePrice"
            :canSaveAndDownload="canSaveAndDownload"
            @save="handleSave"
            @update="handleUpdate"
            @export="handleExport"
        />

        <!-- Modern Success Popup -->
        <Transition
            enter-active-class="transition ease-out duration-300"
            enter-from-class="opacity-0 transform translate-y-2"
            enter-to-class="opacity-100 transform translate-y-0"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100 transform translate-y-0"
            leave-to-class="opacity-0 transform translate-y-2"
        >
            <div v-if="showSuccessPopup" class="fixed top-4 right-4 z-50">
                <div class="bg-white rounded-lg shadow-xl border-l-4 border-green-500 p-4 max-w-sm">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="mr-3">
                            <p class="text-sm font-medium text-gray-900">{{ popupMessage }}</p>
                        </div>
                        <div class="mr-auto pl-3">
                            <button @click="showSuccessPopup = false" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>

        <!-- Modern Error Popup -->
        <Transition
            enter-active-class="transition ease-out duration-300"
            enter-from-class="opacity-0 transform translate-y-2"
            enter-to-class="opacity-100 transform translate-y-0"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100 transform translate-y-0"
            leave-to-class="opacity-0 transform translate-y-2"
        >
            <div v-if="showErrorPopup" class="fixed top-4 right-4 z-50">
                <div class="bg-white rounded-lg shadow-xl border-l-4 border-red-500 p-4 max-w-sm">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="mr-3">
                            <p class="text-sm font-medium text-gray-900">{{ popupMessage }}</p>
                        </div>
                        <div class="mr-auto pl-3">
                            <button @click="showErrorPopup = false" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
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
    },
    originalTemplate: {
        type: Object,
        required: false
    },
    mode: {
        type: String,
        required: false
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

const saving = ref(false)
const page = usePage()

// Modern popup message state
const showSuccessPopup = ref(false)
const showErrorPopup = ref(false)
const popupMessage = ref('')

// Redirect to purchase page
const redirectToPurchase = () => {
    window.location.href = route('client.templates.purchase', props.template.id)
}

// Handle save for client context
const handleSave = async (saveData) => {
    try {
        // Check if user can save
        if (!props.canSaveAndDownload) {
            showErrorMessage('للحفظ والتحميل، يجب شراء هذا القالب أو الاشتراك الشهري')
            return
        }

        saving.value = true
        console.log('🔧 Client: Handling save for template:', props.template.id)
        console.log('🔧 Client: Template object:', props.template)
        console.log('🔧 Client: Original template:', props.originalTemplate)
        console.log('🔧 Client: Mode:', props.mode)

        // Determine the correct template ID to use
        let templateId
        if (props.originalTemplate) {
            // We're editing a client template, use the original template ID
            templateId = props.originalTemplate.id
            console.log('🔧 Client: Editing client template, using original template ID:', templateId)
        } else {
            // We're creating from an original template, use the template ID
            templateId = props.template.id
            console.log('🔧 Client: Creating from original template, using template ID:', templateId)
        }

        console.log('🔧 Client: Template object:', props.template)
        console.log('🔧 Client: Original template:', props.originalTemplate)
        console.log('🔧 Client: Final template_id being sent:', templateId)

        // Get CSRF token with better error handling and refresh if needed
        let csrfTokenElement = document.querySelector('meta[name="csrf-token"]')
        if (!csrfTokenElement) {
            throw new Error('CSRF token not found in page meta tags')
        }

        let csrfToken = csrfTokenElement.getAttribute('content')
        if (!csrfToken) {
            throw new Error('CSRF token is empty')
        }

        // If this is the first save attempt and we get a 419, try to refresh the token
        console.log('🔧 Client: CSRF token found:', csrfToken.substring(0, 10) + '...')

        // Prepare request data
        const requestData = {
            name: props.template.name || 'تصميمي الجديد',
            template_id: templateId,
            design_data: saveData.designDataString,
            canvas_size: saveData.canvasSize,
            design_notes: saveData.designNotes
        }

        // If we're editing a client template, include the client template ID
        if (props.mode === 'edit' && props.template.id) {
            requestData.client_template_id = props.template.id
        }

        console.log('🔧 Client: Request data being sent:', {
            name: requestData.name,
            template_id: requestData.template_id,
            client_template_id: requestData.client_template_id,
            data_size: requestData.design_data.length
        })

        // Use fetch for JSON API endpoint
        const response = await fetch(route('client.designs.store'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(requestData)
        })

        console.log('🔍 Client: Response status:', response.status)
        console.log('🔍 Client: Response headers:', Object.fromEntries(response.headers.entries()))

        if (response.ok) {
            const result = await response.json()
            console.log('✅ Client: Design saved successfully', result)

            if (result.success) {
                // Show different message based on action
                const action = result.action || 'saved'
                let message = result.message || 'تم حفظ التصميم بنجاح'

                if (action === 'created') {
                    console.log('🆕 Client: New design created')
                } else if (action === 'updated') {
                    console.log('🔄 Client: Existing design updated')
                }

                showSuccessMessage(message)
            }
        } else {
            // Handle specific error codes
            if (response.status === 419) {
                console.log('🔄 Client: CSRF token expired, refreshing silently...')

                // Silently refresh CSRF token and retry the save operation
                try {
                    const newToken = await refreshCSRFToken()

                    // Retry the save operation with the new token
                    console.log('🔄 Client: Retrying save with fresh CSRF token...')
                    const retryResponse = await fetch(route('client.designs.store'), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': newToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(requestData)
                    })

                    if (retryResponse.ok) {
                        const retryResult = await retryResponse.json()
                        console.log('✅ Client: Save successful after token refresh:', retryResult)

                        if (retryResult.success) {
                            const action = retryResult.action || 'saved'
                            let message = retryResult.message || 'تم حفظ التصميم بنجاح'

                            if (action === 'created') {
                                console.log('🆕 Client: New design created')
                            } else if (action === 'updated') {
                                console.log('🔄 Client: Existing design updated')
                            }

                            // Show only success message - user doesn't need to know about token refresh
                            showSuccessMessage(message)
                            return // Success, exit the function
                        }
                    }

                    // If retry also failed, show error
                    console.log('❌ Client: Retry failed after token refresh')
                    showErrorMessage('حدث خطأ أثناء حفظ التصميم')
                    return

                } catch (tokenError) {
                    console.error('❌ Client: Failed to refresh token:', tokenError)
                    showErrorMessage('حدث خطأ أثناء حفظ التصميم')
                    return
                }
            }

            // Try to parse error response
            const responseText = await response.text()
            console.error('❌ Client: Raw error response:', responseText)

            let errorMessage = 'Failed to save design'
            try {
                const errorData = JSON.parse(responseText)
                errorMessage = errorData.message || errorMessage
            } catch (parseError) {
                console.error('❌ Client: Could not parse error response as JSON')
                if (responseText.includes('<!DOCTYPE')) {
                    errorMessage = `خطأ في الخادم (${response.status}). يرجى المحاولة مرة أخرى.`
                } else {
                    errorMessage = `خطأ غير متوقع (${response.status}): ${responseText.substring(0, 100)}`
                }
            }

            throw new Error(errorMessage)
        }
    } catch (error) {
        console.error('❌ Client: Error saving design:', error)
        showErrorMessage('خطأ في حفظ التصميم: ' + error.message)
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

// Go back to previous page
const goBack = () => {
    window.history.back()
}

// Modern popup message functions
const showSuccessMessage = (message) => {
    popupMessage.value = message
    showSuccessPopup.value = true
    setTimeout(() => {
        showSuccessPopup.value = false
    }, 3000)
}

const showErrorMessage = (message) => {
    popupMessage.value = message
    showErrorPopup.value = true
    setTimeout(() => {
        showErrorPopup.value = false
    }, 3000)
}

// Function to refresh CSRF token
const refreshCSRFToken = async () => {
    try {
        console.log('🔄 Client: Refreshing CSRF token...')
        const response = await fetch('/csrf-token', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })

        if (response.ok) {
            const data = await response.json()
            if (data.csrf_token) {
                // Update the meta tag
                const metaTag = document.querySelector('meta[name="csrf-token"]')
                if (metaTag) {
                    metaTag.setAttribute('content', data.csrf_token)
                    console.log('✅ Client: CSRF token refreshed successfully')
                    return data.csrf_token
                }
            }
        }

        throw new Error('Failed to refresh CSRF token')
    } catch (error) {
        console.error('❌ Client: Error refreshing CSRF token:', error)
        throw error
    }
}
</script>

<style scoped>
/* Any client-specific styles */
</style>
