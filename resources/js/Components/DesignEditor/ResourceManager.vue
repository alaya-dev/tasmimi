<template>
    <div class="resource-manager h-full flex flex-col bg-white">
        <!-- Header -->
        <div class="flex-shrink-0 p-4 border-b border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">مدير الموارد</h3>
                <button
                    @click="refreshResources"
                    class="p-2 text-gray-500 hover:text-gray-700 rounded-lg hover:bg-gray-100"
                    title="تحديث"
                >
                    <i class="fas fa-sync-alt" :class="{ 'fa-spin': loading }"></i>
                </button>
            </div>

            <!-- Tabs -->
            <div class="flex space-x-1 space-x-reverse bg-gray-100 rounded-lg p-1">
                <button
                    v-for="tab in tabs"
                    :key="tab.id"
                    @click="activeTab = tab.id"
                    :class="[
                        'flex-1 py-2 px-3 text-sm font-medium rounded-md transition-colors',
                        activeTab === tab.id
                            ? 'bg-white text-purple-600 shadow-sm'
                            : 'text-gray-600 hover:text-gray-800'
                    ]"
                >
                    <i :class="tab.icon" class="ml-2"></i>
                    {{ tab.name }}
                </button>
            </div>
        </div>

        <!-- Content -->
        <div class="flex-1 overflow-hidden">
            <!-- My Files Tab -->
            <div v-if="activeTab === 'my-files'" class="h-full flex flex-col">
                <!-- Upload Area -->
                <div class="flex-shrink-0 p-4 border-b border-gray-200">
                    <div class="space-y-3">
                        <!-- Quick Upload Button -->
                        <div class="space-y-2">
                            <input
                                ref="fileInput"
                                type="file"
                                multiple
                                accept="image/*"
                                @change="handleFileSelect"
                                class="hidden"
                            >
                            <input
                                ref="backgroundInput"
                                type="file"
                                accept="image/*"
                                @change="handleBackgroundSelect"
                                class="hidden"
                            >

                            <!-- Background Image Button -->
                            <button
                                @click="$refs.backgroundInput.click()"
                                class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-4 py-3 rounded-lg hover:from-blue-700 hover:to-indigo-700 flex items-center justify-center space-x-2 space-x-reverse font-medium shadow-lg"
                            >
                                <i class="fas fa-image"></i>
                                <span>إضافة خلفية للصفحة</span>
                            </button>

                            <!-- Regular Upload Button -->
                            <div class="flex space-x-2 space-x-reverse">
                                <button
                                    @click="$refs.fileInput.click()"
                                    class="flex-1 bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 flex items-center justify-center space-x-2 space-x-reverse"
                                >
                                    <i class="fas fa-plus"></i>
                                    <span>رفع صورة عادية</span>
                                </button>
                            </div>
                        </div>

                        <!-- Drag & Drop Area -->
                        <div
                            @drop="handleDrop"
                            @dragover.prevent
                            @dragenter.prevent
                            class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-purple-400 transition-colors"
                        >
                            <i class="fas fa-cloud-upload-alt text-2xl text-gray-400 mb-1"></i>
                            <p class="text-sm text-gray-600">أو اسحب الملفات هنا</p>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="flex-shrink-0 p-4 border-b border-gray-200">
                    <div class="flex items-center space-x-4 space-x-reverse">
                        <select
                            v-model="filters.folder"
                            @change="loadUserFiles"
                            class="border border-gray-300 rounded-lg px-3 py-2 text-sm"
                        >
                            <option value="">جميع المجلدات</option>
                            <option v-for="folder in folders" :key="folder" :value="folder">
                                {{ folder }}
                            </option>
                        </select>
                        <input
                            v-model="filters.search"
                            @input="debounceSearch"
                            placeholder="البحث..."
                            class="border border-gray-300 rounded-lg px-3 py-2 text-sm flex-1"
                        >
                    </div>
                </div>

                <!-- Files Grid -->
                <div class="flex-1 overflow-y-auto p-4">
                    <div v-if="loading" class="text-center py-8">
                        <i class="fas fa-spinner fa-spin text-2xl text-gray-400"></i>
                        <p class="text-gray-600 mt-2">جاري التحميل...</p>
                    </div>
                    
                    <div v-else-if="userFiles.length === 0" class="text-center py-8">
                        <i class="fas fa-images text-4xl text-gray-300 mb-4"></i>
                        <p class="text-gray-600">لا توجد ملفات</p>
                    </div>

                    <div v-else class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        <div
                            v-for="file in userFiles"
                            :key="file.id"
                            @click="selectFile(file)"
                            class="relative group cursor-pointer bg-gray-50 rounded-lg overflow-hidden hover:shadow-md transition-shadow"
                        >
                            <div class="aspect-square">
                                <img
                                    :src="file.url"
                                    :alt="file.filename"
                                    class="w-full h-full object-cover"
                                    @error="handleImageError"
                                >
                            </div>
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-opacity"></div>
                            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-2">
                                <p class="text-white text-xs truncate">{{ file.filename }}</p>
                            </div>
                            <div class="absolute top-2 right-2 flex space-x-1 space-x-reverse opacity-0 group-hover:opacity-100 transition-opacity">
                                <button
                                    v-if="file.is_image"
                                    @click.stop="removeBackground(file)"
                                    class="w-6 h-6 bg-purple-500 text-white rounded-full flex items-center justify-center"
                                    title="إزالة الخلفية"
                                >
                                    <i class="fas fa-magic text-xs"></i>
                                </button>
                                <button
                                    @click.stop="deleteFile(file)"
                                    class="w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center"
                                    title="حذف"
                                >
                                    <i class="fas fa-times text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pexels Tab -->
            <div v-if="activeTab === 'pexels'" class="h-full flex flex-col">
                <!-- Search -->
                <div class="flex-shrink-0 p-4 border-b border-gray-200">
                    <div class="flex items-center space-x-2 space-x-reverse">
                        <input
                            v-model="pexelsSearch"
                            @keyup.enter="searchPexels"
                            placeholder="البحث في Pexels..."
                            class="border border-gray-300 rounded-lg px-3 py-2 text-sm flex-1"
                        >
                        <button
                            @click="searchPexels"
                            :disabled="!pexelsSearch.trim()"
                            class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 disabled:opacity-50"
                        >
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>

                <!-- Pexels Results -->
                <div class="flex-1 overflow-y-auto p-4">
                    <div v-if="pexelsLoading" class="text-center py-8">
                        <i class="fas fa-spinner fa-spin text-2xl text-gray-400"></i>
                        <p class="text-gray-600 mt-2">جاري البحث...</p>
                    </div>

                    <div v-else-if="pexelsPhotos.length === 0 && pexelsSearch" class="text-center py-8">
                        <i class="fas fa-search text-4xl text-gray-300 mb-4"></i>
                        <p class="text-gray-600">لا توجد نتائج للبحث</p>
                    </div>

                    <div v-else-if="pexelsPhotos.length === 0" class="text-center py-8">
                        <i class="fas fa-images text-4xl text-gray-300 mb-4"></i>
                        <p class="text-gray-600">ابحث عن الصور في Pexels</p>
                    </div>

                    <div v-else class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <div
                            v-for="photo in pexelsPhotos"
                            :key="photo.id"
                            class="relative group cursor-pointer bg-gray-50 rounded-lg overflow-hidden hover:shadow-md transition-shadow"
                        >
                            <div class="aspect-square">
                                <img
                                    :src="photo.src.medium"
                                    :alt="photo.alt"
                                    class="w-full h-full object-cover"
                                >
                            </div>
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-opacity"></div>
                            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-2">
                                <p class="text-white text-xs">{{ photo.photographer }}</p>
                            </div>
                            <button
                                @click="downloadPexelsPhoto(photo)"
                                class="absolute top-2 right-2 w-8 h-8 bg-purple-500 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center"
                            >
                                <i class="fas fa-download text-xs"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Load More -->
                    <div v-if="pexelsPhotos.length > 0 && pexelsHasMore" class="text-center mt-6">
                        <button
                            @click="loadMorePexels"
                            :disabled="pexelsLoading"
                            class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700 disabled:opacity-50"
                        >
                            تحميل المزيد
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Storage Info -->
        <div class="flex-shrink-0 p-4 border-t border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between text-sm text-gray-600">
                <span>المساحة المستخدمة</span>
                <span>{{ storageInfo.used_human }} / {{ storageInfo.limit_human }}</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                <div
                    class="bg-purple-600 h-2 rounded-full transition-all"
                    :style="{ width: storageInfo.percentage + '%' }"
                ></div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted, watch } from 'vue'
import { router } from '@inertiajs/vue3'

// Props
const props = defineProps({
    onFileSelect: {
        type: Function,
        default: () => {}
    }
})

// State
const activeTab = ref('my-files')
const loading = ref(false)
const userFiles = ref([])
const folders = ref([])
const storageInfo = ref({
    used_human: '0 B',
    limit_human: '100 MB',
    percentage: 0
})

// Filters
const filters = reactive({
    folder: '',
    search: '',
    type: 'images'
})

// Pexels
const pexelsSearch = ref('')
const pexelsPhotos = ref([])
const pexelsLoading = ref(false)
const pexelsPage = ref(1)
const pexelsHasMore = ref(false)

// Tabs configuration
const tabs = [
    { id: 'my-files', name: 'ملفاتي', icon: 'fas fa-folder' },
    { id: 'pexels', name: 'Pexels', icon: 'fas fa-globe' }
]

// Search debounce
let searchTimeout = null
const debounceSearch = () => {
    clearTimeout(searchTimeout)
    searchTimeout = setTimeout(() => {
        loadUserFiles()
    }, 500)
}

// Methods
const loadUserFiles = async () => {
    loading.value = true
    try {
        const response = await fetch('/admin/user-files?' + new URLSearchParams({
            ...filters,
            per_page: 50
        }))
        const data = await response.json()
        
        if (data.success) {
            userFiles.value = data.files.data
            storageInfo.value = data.storage_info
        }
    } catch (error) {
        console.error('Error loading user files:', error)
    } finally {
        loading.value = false
    }
}

const loadFolders = async () => {
    try {
        const response = await fetch('/admin/user-files/folders')
        const data = await response.json()
        
        if (data.success) {
            folders.value = data.folders
        }
    } catch (error) {
        console.error('Error loading folders:', error)
    }
}

const handleFileSelect = (event) => {
    const files = Array.from(event.target.files)
    uploadFiles(files)
}

const handleDrop = (event) => {
    event.preventDefault()
    const files = Array.from(event.dataTransfer.files)
    uploadFiles(files)
}

const uploadFiles = async (files) => {
    for (const file of files) {
        await uploadFile(file)
    }
    loadUserFiles()
}

const uploadFile = async (file) => {
    const formData = new FormData()
    formData.append('file', file)
    formData.append('folder', filters.folder || 'general')

    try {
        const response = await fetch('/admin/user-files', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        
        const data = await response.json()
        if (data.success) {
            storageInfo.value = data.storage_info
        }
    } catch (error) {
        console.error('Error uploading file:', error)
    }
}

const selectFile = (file) => {
    props.onFileSelect(file)
}

const handleBackgroundSelect = (event) => {
    const file = event.target.files[0]
    if (file) {
        const reader = new FileReader()
        reader.onload = (e) => {
            // Create image to get dimensions
            const img = new Image()
            img.onload = () => {
                // Add as full canvas background
                props.onFileSelect({
                    url: e.target.result,
                    filename: file.name,
                    is_background: true,
                    is_canvas_background: true,
                    width: img.naturalWidth,
                    height: img.naturalHeight
                })
            }
            img.src = e.target.result
        }
        reader.readAsDataURL(file)
    }
}

const addBackgroundImage = () => {
    // This method is kept for compatibility but we now use handleBackgroundSelect
    const input = document.createElement('input')
    input.type = 'file'
    input.accept = 'image/*'
    input.onchange = handleBackgroundSelect
    input.click()
}

const deleteFile = async (file) => {
    if (!confirm('هل أنت متأكد من حذف هذا الملف؟')) return

    try {
        const response = await fetch(`/admin/user-files/${file.id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        
        if (response.ok) {
            loadUserFiles()
        }
    } catch (error) {
        console.error('Error deleting file:', error)
    }
}

const searchPexels = async () => {
    if (!pexelsSearch.value.trim()) return

    pexelsLoading.value = true
    pexelsPage.value = 1
    
    try {
        const response = await fetch('/admin/pexels/search?' + new URLSearchParams({
            query: pexelsSearch.value,
            page: pexelsPage.value,
            per_page: 20
        }))
        const data = await response.json()
        
        if (data.success) {
            pexelsPhotos.value = data.photos
            pexelsHasMore.value = !!data.next_page
        }
    } catch (error) {
        console.error('Error searching Pexels:', error)
    } finally {
        pexelsLoading.value = false
    }
}

const loadMorePexels = async () => {
    if (pexelsLoading.value || !pexelsHasMore.value) return

    pexelsLoading.value = true
    pexelsPage.value++
    
    try {
        const response = await fetch('/admin/pexels/search?' + new URLSearchParams({
            query: pexelsSearch.value,
            page: pexelsPage.value,
            per_page: 20
        }))
        const data = await response.json()
        
        if (data.success) {
            pexelsPhotos.value.push(...data.photos)
            pexelsHasMore.value = !!data.next_page
        }
    } catch (error) {
        console.error('Error loading more Pexels photos:', error)
    } finally {
        pexelsLoading.value = false
    }
}

const downloadPexelsPhoto = async (photo) => {
    try {
        const formData = new FormData()
        formData.append('photo_id', photo.id)
        formData.append('size', 'large')
        formData.append('folder', 'pexels')

        const response = await fetch('/admin/pexels/download', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: formData
        })

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`)
        }

        const data = await response.json()
        if (data.success) {
            // Switch to my files tab and refresh
            activeTab.value = 'my-files'
            loadUserFiles()
            alert('تم تحميل الصورة بنجاح!')
        } else {
            alert(data.message || 'فشل في تحميل الصورة')
        }
    } catch (error) {
        console.error('Error downloading Pexels photo:', error)
        alert('خطأ في تحميل الصورة')
    }
}

const refreshResources = () => {
    if (activeTab.value === 'my-files') {
        loadUserFiles()
        loadFolders()
    }
}

const removeBackground = async (file) => {
    if (!confirm('هل تريد إزالة خلفية هذه الصورة؟ قد يستغرق هذا بعض الوقت.')) return

    try {
        const response = await fetch('/admin/background-removal/remove-from-file', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                file_id: file.id
            })
        })

        const data = await response.json()
        if (data.success) {
            // Refresh files to show the new processed image
            loadUserFiles()
            alert('تم إزالة الخلفية بنجاح!')
        } else {
            alert(data.message || 'فشل في إزالة الخلفية')
        }
    } catch (error) {
        console.error('Error removing background:', error)
        alert('خطأ في إزالة الخلفية')
    }
}

const handleImageError = (event) => {
    event.target.src = '/images/placeholder.png'
}

// Watchers
watch(activeTab, (newTab) => {
    if (newTab === 'my-files') {
        loadUserFiles()
    }
})

// Lifecycle
onMounted(() => {
    loadUserFiles()
    loadFolders()
})
</script>

<style scoped>
.resource-manager {
    direction: rtl;
}
</style>
