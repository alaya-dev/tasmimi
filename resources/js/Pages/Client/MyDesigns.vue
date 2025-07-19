<template>
    <Head title="تصاميمي - سامقة" />
    <ClientLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">تصاميمي</h1>
                        <p class="text-gray-600">إدارة وتحرير تصاميمك الشخصية</p>
                    </div>
                    <div class="flex items-center space-x-4 space-x-reverse">
                        <Link
                            :href="route('client.home')"
                            class="bg-gradient-to-r from-purple-500 to-pink-500 text-white px-6 py-3 rounded-xl font-semibold hover:from-purple-600 hover:to-pink-600 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105"
                        >
                            <svg class="w-5 h-5 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            إنشاء تصميم جديد
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Success/Error Messages -->
            <div v-if="$page.props.flash?.success" class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl flex items-center">
                <svg class="w-5 h-5 ml-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ $page.props.flash.success }}
            </div>

            <div v-if="$page.props.flash?.error" class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl flex items-center">
                <svg class="w-5 h-5 ml-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ $page.props.flash.error }}
            </div>

            <!-- Content -->
            <div v-if="designs.length === 0" class="text-center py-16">
                <!-- Empty State -->
                <div class="max-w-md mx-auto">
                    <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-purple-100 to-pink-100 rounded-full flex items-center justify-center">
                        <svg class="w-12 h-12 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">ليس لديك أي تصاميم بعد</h3>
                    <p class="text-gray-600 mb-6">يمكنك البدء الآن من خلال اختيار قالب وإنشاء تصميمك الأول</p>
                    <Link
                        :href="route('client.home')"
                        class="inline-flex items-center bg-gradient-to-r from-purple-500 to-pink-500 text-white px-8 py-3 rounded-xl font-semibold hover:from-purple-600 hover:to-pink-600 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105"
                    >
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        اختيار قالب
                    </Link>
                </div>
            </div>

            <!-- Designs Grid -->
            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <div
                    v-for="design in designs"
                    :key="design.id"
                    class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden group border border-gray-100"
                >
                    <!-- Thumbnail -->
                    <div class="relative aspect-[4/3] bg-gradient-to-br from-gray-50 to-gray-100 overflow-hidden">
                        <img
                            v-if="design.generated_image_url || design.thumbnail_url"
                            :src="design.generated_image_url || design.thumbnail_url"
                            :alt="design.name"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                        />
                        <div v-else class="w-full h-full flex items-center justify-center">
                            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z"/>
                            </svg>
                        </div>

                        <!-- Overlay Actions -->
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-300 flex items-center justify-center opacity-0 group-hover:opacity-100">
                            <div class="flex space-x-2 space-x-reverse">
                                <button
                                    @click="editDesign(design)"
                                    class="bg-white text-gray-900 p-3 rounded-full hover:bg-gray-100 transition-colors duration-200 shadow-lg"
                                    title="تحرير"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                
                            </div>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-4">
                        <!-- Editable Name -->
                        <div class="mb-2">
                            <input
                                v-if="editingName === design.id"
                                v-model="editingNameValue"
                                @blur="saveDesignName(design)"
                                @keyup.enter="saveDesignName(design)"
                                @keyup.escape="cancelEditName"
                                class="w-full font-semibold text-gray-900 bg-transparent border-b-2 border-purple-500 focus:outline-none focus:border-purple-600 px-1 py-1"
                                ref="nameInput"
                            />
                            <h3
                                v-else
                                @click="startEditName(design)"
                                class="font-semibold text-gray-900 truncate cursor-pointer hover:text-purple-600 transition-colors duration-200 group/name"
                                title="انقر لتعديل الاسم"
                            >
                                {{ design.name }}
                                <svg class="w-4 h-4 inline-block ml-1 opacity-0 group-hover/name:opacity-100 transition-opacity duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </h3>
                        </div>
                        <div class="flex items-center justify-between text-sm text-gray-500 mb-3">
                            <span>{{ design.template.category }}</span>
                            <span>الإصدار {{ design.version }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs text-gray-400">
                            <span>آخر تعديل: {{ formatDate(design.last_edited_at || design.created_at) }}</span>
                            <span>{{ design.canvas_size }}</span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="px-4 pb-4 flex space-x-2 space-x-reverse">
                        <button
                            @click="editDesign(design)"
                            class="flex-1 bg-gradient-to-r from-purple-500 to-pink-500 text-white py-2 px-4 rounded-lg font-medium hover:from-purple-600 hover:to-pink-600 transition-all duration-200"
                        >
                            تحرير
                        </button>
                        <button
                            @click="showDeleteModal(design)"
                            class="bg-red-500 text-white p-2 rounded-lg hover:bg-red-600 transition-colors duration-200"
                            title="حذف"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

        </div>

        <!-- Export Modal -->
        <div v-if="showExportModalFlag" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click="closeExportModal">
            <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4" @click.stop>
                <h3 class="text-xl font-semibold mb-4">تصدير التصميم</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">صيغة التصدير</label>
                        <select v-model="exportFormat" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                            <option value="png">PNG</option>
                            <option value="jpeg">JPEG</option>
                            <option value="svg">SVG</option>
                            <option value="pdf">PDF</option>
                        </select>
                    </div>
                    <div v-if="exportFormat !== 'svg'">
                        <label class="block text-sm font-medium text-gray-700 mb-2">الجودة (%)</label>
                        <input v-model="exportQuality" type="range" min="50" max="100" class="w-full">
                        <div class="text-center text-sm text-gray-600">{{ exportQuality }}%</div>
                    </div>
                </div>
                <div class="flex space-x-3 space-x-reverse mt-6">
                    <button
                        @click="exportDesign"
                        class="flex-1 bg-gradient-to-r from-purple-500 to-pink-500 text-white py-2 px-4 rounded-lg font-medium hover:from-purple-600 hover:to-pink-600 transition-all duration-200"
                        :disabled="isExporting"
                    >
                        <span v-if="isExporting">جاري التصدير...</span>
                        <span v-else>تصدير</span>
                    </button>
                    <button
                        @click="closeExportModal"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200"
                    >
                        إلغاء
                    </button>
                </div>
            </div>
        </div>

        <!-- Delete Modal -->
        <div v-if="showDeleteModalFlag" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click="closeDeleteModal">
            <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4" @click.stop>
                <h3 class="text-xl font-semibold mb-4 text-red-600">تأكيد الحذف</h3>
                <p class="text-gray-600 mb-6">هل أنت متأكد من حذف التصميم "{{ selectedDesign?.name }}"؟ لا يمكن التراجع عن هذا الإجراء.</p>
                <div class="flex space-x-3 space-x-reverse">
                    <button
                        @click="deleteDesign"
                        class="flex-1 bg-red-500 text-white py-2 px-4 rounded-lg font-medium hover:bg-red-600 transition-colors duration-200"
                        :disabled="isDeleting"
                    >
                        <span v-if="isDeleting">جاري الحذف...</span>
                        <span v-else>حذف</span>
                    </button>
                    <button
                        @click="closeDeleteModal"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200"
                    >
                        إلغاء
                    </button>
                </div>
            </div>
        </div>
    </ClientLayout>
</template>

<script setup>
import { ref, nextTick } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import ClientLayout from '@/Layouts/ClientLayout.vue';

// Props
const props = defineProps({
    designs: {
        type: Array,
        default: () => []
    }
});

// State
const showExportModalFlag = ref(false);
const showDeleteModalFlag = ref(false);
const selectedDesign = ref(null);
const exportFormat = ref('png');
const exportQuality = ref(90);
const isExporting = ref(false);
const isDeleting = ref(false);
const editingName = ref(null);
const editingNameValue = ref('');
const nameInput = ref(null);

// Methods
const editDesign = (design) => {
    router.visit(route('client.designs.edit', design.id));
};

const duplicateDesign = async (design) => {
    try {
        await router.post(route('client.designs.duplicate', design.id));
    } catch (error) {
        console.error('Error duplicating design:', error);
    }
};

const showExportModal = (design) => {
    selectedDesign.value = design;
    showExportModalFlag.value = true;
};

const closeExportModal = () => {
    showExportModalFlag.value = false;
    selectedDesign.value = null;
    exportFormat.value = 'png';
    exportQuality.value = 90;
};

const exportDesign = async () => {
    if (!selectedDesign.value) return;

    isExporting.value = true;

    try {
        const response = await fetch(route('client.designs.export', selectedDesign.value.id), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                format: exportFormat.value,
                quality: exportQuality.value
            })
        });

        const result = await response.json();

        if (result.success) {
            // Handle export success - this would typically trigger a download
            console.log('Export successful:', result.export_data);
            closeExportModal();
        } else {
            showNotification('حدث خطأ أثناء التصدير: ' + result.message, 'error');
        }
    } catch (error) {
        console.error('Export error:', error);
        showNotification('حدث خطأ أثناء التصدير', 'error');
    } finally {
        isExporting.value = false;
    }
};

const showDeleteModal = (design) => {
    selectedDesign.value = design;
    showDeleteModalFlag.value = true;
};

const closeDeleteModal = () => {
    showDeleteModalFlag.value = false;
    selectedDesign.value = null;
};

const deleteDesign = async () => {
    if (!selectedDesign.value) return;

    isDeleting.value = true;

    try {
        await router.delete(route('client.designs.destroy', selectedDesign.value.id), {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                closeDeleteModal();
            },
            onError: () => {
                showNotification('حدث خطأ أثناء حذف التصميم', 'error');
            },
            onFinish: () => {
                isDeleting.value = false;
            }
        });
    } catch (error) {
        console.error('Error deleting design:', error);
        alert('حدث خطأ أثناء حذف التصميم');
        isDeleting.value = false;
    }
};

const startEditName = async (design) => {
    editingName.value = design.id;
    editingNameValue.value = design.name;
    // Focus the input after Vue updates the DOM
    await nextTick();
    const input = document.querySelector(`input[ref="nameInput"]`);
    if (input) {
        input.focus();
        input.select();
    }
};

const cancelEditName = () => {
    editingName.value = null;
    editingNameValue.value = '';
};

const saveDesignName = async (design) => {
    if (!editingNameValue.value.trim()) {
        cancelEditName();
        return;
    }

    try {
        await router.patch(route('client.designs.update-name', design.id), {
            name: editingNameValue.value.trim()
        }, {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                // Update the design name locally
                design.name = editingNameValue.value.trim();
                cancelEditName();
            },
            onError: () => {
                alert('حدث خطأ أثناء تحديث اسم التصميم');
                cancelEditName();
            }
        });
    } catch (error) {
        console.error('Error updating design name:', error);
        alert('حدث خطأ أثناء تحديث اسم التصميم');
        cancelEditName();
    }
};

const formatDate = (date) => {
    if (!date) return '';
    return new Date(date).toLocaleDateString('ar', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        calendar: 'gregory' // Force Gregorian calendar
    });
};
</script>
