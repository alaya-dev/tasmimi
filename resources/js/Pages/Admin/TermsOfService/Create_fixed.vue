<template>
    <AdminLayoutSidebar>
        <Head title="إضافة ملف اتفاقية الاستخدام" />
        
        <div class="bg-white rounded-lg shadow-md p-6" dir="rtl">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900 mb-2">إضافة ملف اتفاقية الاستخدام</h1>
                <p class="text-gray-600">قم برفع ملف PDF لاتفاقية الاستخدام (ملف واحد فقط مسموح)</p>
            </div>

            <form @submit.prevent="submit" enctype="multipart/form-data">
                <!-- File Upload Only -->
                <div class="mb-6">
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="file" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                    <span>اختر ملف PDF</span>
                                    <input
                                        id="file"
                                        type="file"
                                        class="sr-only"
                                        accept=".pdf"
                                        @change="handleFileChange"
                                        required
                                    />
                                </label>
                                <p class="pr-1">أو سحب وإفلات</p>
                            </div>
                            <p class="text-xs text-gray-500">
                                PDF فقط حتى 10MB
                            </p>
                        </div>
                    </div>
                    
                    <!-- Display selected file -->
                    <div v-if="selectedFile" class="mt-3 p-3 bg-gray-50 rounded-md">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <svg class="h-5 w-5 text-red-600 ml-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-sm font-medium text-gray-900">{{ selectedFile.name }}</span>
                                <span class="text-sm text-gray-500 mr-2">({{ formatFileSize(selectedFile.size) }})</span>
                            </div>
                            <button type="button" @click="removeFile" class="text-red-600 hover:text-red-800">
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <div v-if="form.errors.file" class="mt-1 text-sm text-red-600">
                        {{ form.errors.file }}
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end space-x-3 space-x-reverse">
                    <Link
                        :href="route('admin.terms-of-service.index')"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded transition duration-200"
                    >
                        إلغاء
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing || !selectedFile"
                        class="bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white font-bold py-2 px-4 rounded transition duration-200"
                    >
                        <span v-if="form.processing">جاري الرفع...</span>
                        <span v-else>رفع الملف</span>
                    </button>
                </div>
            </form>
        </div>
    </AdminLayoutSidebar>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayoutSidebar from '@/Layouts/AdminLayoutSidebar.vue'

const selectedFile = ref(null)

const form = useForm({
    file: null,
    is_active: true
})

const handleFileChange = (event) => {
    const file = event.target.files[0]
    if (file) {
        selectedFile.value = file
        form.file = file
    }
}

const removeFile = () => {
    selectedFile.value = null
    form.file = null
    document.getElementById('file').value = ''
}

const formatFileSize = (bytes) => {
    if (bytes === 0) return '0 بايت'
    
    const k = 1024
    const sizes = ['بايت', 'كيلوبايت', 'ميجابايت', 'جيجابايت']
    const i = Math.floor(Math.log(bytes) / Math.log(k))
    
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

const submit = () => {
    if (!selectedFile.value) {
        return
    }
    
    // Utiliser le nom du fichier comme titre automatiquement
    form.title = selectedFile.value.name.replace('.pdf', '')
    
    form.post(route('admin.terms-of-service.store'), {
        forceFormData: true,
        onSuccess: () => {
            selectedFile.value = null
        }
    })
}
</script>
