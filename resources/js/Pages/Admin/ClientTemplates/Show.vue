<template>
    <Head :title="`محرر التصميم - ${clientTemplate.name}`" />

    <AdminLayoutSidebar>
        <template #breadcrumb>
            <Link :href="route('admin.client-templates.index')" class="text-gray-500 hover:text-gray-700">
                بطاقات الحرفاء
            </Link>
            <span class="text-gray-500 mx-2">/</span>
            <span class="text-gray-500">{{ clientTemplate.name }}</span>
        </template>

        <template #header>
            <div class="flex items-center justify-between flex-row-reverse">
                <div class="flex items-center flex-row-reverse">
                    <Link
                        :href="route('admin.client-templates.index')"
                        class="ml-4 text-gray-600 hover:text-gray-900 transition-colors duration-200"
                    >
                        <svg class="w-5 h-5 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </Link>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">محرر التصميم - {{ clientTemplate.name }}</h2>
                </div>
                <div class="flex items-center space-x-3 space-x-reverse">
                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">
                        قراءة فقط
                    </span>
                </div>
            </div>
        </template>

        <div class="min-h-screen bg-gray-50" dir="rtl">
            <!-- معلومات التصميم -->
            <div class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
                <div class="max-w-7xl mx-auto">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                        <div class="text-right">
                            <span class="text-gray-500">العميل:</span>
                            <span class="font-medium mr-2">{{ clientTemplate.user.email }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-gray-500">القالب الأساسي:</span>
                            <span class="font-medium mr-2">{{ clientTemplate.template.name }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-gray-500">الإصدار:</span>
                            <span class="font-medium mr-2">{{ clientTemplate.version }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-gray-500">تاريخ الإنشاء:</span>
                            <span class="font-medium mr-2">{{ formatDate(clientTemplate.created_at) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- محرر التصميم -->
            <div class="flex-1 p-6">
                <div class="max-w-7xl mx-auto">
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                        <!-- شريط الأدوات -->
                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                            <div class="flex items-center justify-between flex-row-reverse">
                                <h3 class="text-lg font-medium text-gray-900">معاينة التصميم</h3>
                                <div class="flex items-center space-x-3 space-x-reverse">
                                    <div class="text-sm text-gray-500">
                                        آخر تعديل: {{ clientTemplate.last_edited_at ? formatDate(clientTemplate.last_edited_at) : 'غير محدد' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- منطقة التصميم -->
                        <div class="p-6">
                            <div class="flex justify-center">
                                <div class="relative bg-white border-2 border-gray-200 rounded-lg shadow-sm">
                                    <!-- Loading indicator -->
                                    <div v-if="isLoading" class="absolute inset-0 flex items-center justify-center bg-gray-50 z-10">
                                        <div class="text-center">
                                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto mb-4"></div>
                                            <p class="text-gray-600">جاري تحميل التصميم...</p>
                                        </div>
                                    </div>

                                    <!-- Error message -->
                                    <div v-else-if="loadError" class="absolute inset-0 flex items-center justify-center bg-gray-50 z-10">
                                        <div class="text-center">
                                            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                            </svg>
                                            <p class="text-gray-600 mb-2">خطأ في تحميل التصميم</p>
                                            <p class="text-sm text-gray-500">{{ loadError }}</p>
                                        </div>
                                    </div>

                                    <!-- GrapesJS Preview Container -->
                                    <div 
                                        v-else
                                        id="design-preview-container"
                                        class="relative overflow-hidden rounded-lg"
                                        :style="{
                                            width: canvasWidth + 'px',
                                            height: canvasHeight + 'px',
                                            maxWidth: '100%',
                                            maxHeight: '70vh'
                                        }"
                                    >
                                        <!-- Design Preview -->
                                        <div 
                                            id="design-preview"
                                            class="w-full h-full bg-white border border-gray-300"
                                            v-html="designHtml"
                                        ></div>
                                        
                                        <!-- طبقة الحماية للقراءة فقط -->
                                        <div class="absolute inset-0 bg-transparent cursor-not-allowed"></div>
                                    </div>

                                    <!-- مؤشر القراءة فقط -->
                                    <div class="absolute top-2 left-2 bg-blue-500 text-white text-xs px-2 py-1 rounded z-20">
                                        قراءة فقط
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- معلومات إضافية -->
                        <div v-if="clientTemplate.notes" class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                            <h4 class="text-sm font-medium text-gray-900 mb-2 text-right">ملاحظات:</h4>
                            <p class="text-sm text-gray-600 text-right">{{ clientTemplate.notes }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayoutSidebar>
</template>

<script setup>
import { ref, onMounted, computed, nextTick } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AdminLayoutSidebar from '@/Layouts/AdminLayoutSidebar.vue';
import 'grapesjs/dist/css/grapes.min.css';

const props = defineProps({
    clientTemplate: Object,
});

const isLoading = ref(true);
const loadError = ref(null);
const designHtml = ref('');
const designCss = ref('');

// حساب أبعاد الكانفاس
const canvasWidth = computed(() => {
    if (props.clientTemplate.canvas_size) {
        return props.clientTemplate.canvas_size.width || 800;
    }
    return 800;
});

const canvasHeight = computed(() => {
    if (props.clientTemplate.canvas_size) {
        return props.clientTemplate.canvas_size.height || 600;
    }
    return 600;
});

// تحميل التصميم باستخدام GrapesJS
const loadDesignWithGrapesJS = async () => {
    try {
        isLoading.value = true;
        loadError.value = null;

        console.log('Client Template Data:', props.clientTemplate);
        console.log('Design Data:', props.clientTemplate.design_data);

        // تجنب طباعة raw_design_data إذا كانت تحتوي على بيانات كبيرة
        if (props.clientTemplate.raw_design_data) {
            const rawDataStr = typeof props.clientTemplate.raw_design_data === 'string'
                ? props.clientTemplate.raw_design_data
                : JSON.stringify(props.clientTemplate.raw_design_data);
            console.log('Raw Design Data length:', rawDataStr.length);
            if (rawDataStr.length > 1000) {
                console.log('Raw Design Data (truncated):', rawDataStr.substring(0, 200) + '...');
            } else {
                console.log('Raw Design Data:', props.clientTemplate.raw_design_data);
            }
        }

        // Import GrapesJS dynamically
        const grapesjs = (await import('grapesjs')).default;

        // Create a temporary editor to parse the design data
        const tempContainer = document.createElement('div');
        tempContainer.style.display = 'none';
        document.body.appendChild(tempContainer);

        const editor = grapesjs.init({
            container: tempContainer,
            width: canvasWidth.value,
            height: canvasHeight.value,
            fromElement: false,
            showOffsets: false,
            noticeOnUnload: false,
            storageManager: false,
            panels: { defaults: [] }, // No panels needed for preview
        });

        // تحميل بيانات التصميم
        let designData = null;

        // محاولة تحميل البيانات من مصادر مختلفة
        if (props.clientTemplate.raw_design_data) {
            console.log('Using raw_design_data');
            if (typeof props.clientTemplate.raw_design_data === 'string') {
                try {
                    designData = JSON.parse(props.clientTemplate.raw_design_data);
                } catch (e) {
                    console.error('Failed to parse raw_design_data:', e);
                    // إذا فشل التحليل، جرب design_data بدلاً من ذلك
                    designData = null;
                }
            } else {
                designData = props.clientTemplate.raw_design_data;
            }
        }

        // إذا لم تنجح raw_design_data، جرب design_data
        if (!designData && props.clientTemplate.design_data) {
            console.log('Using design_data');
            if (typeof props.clientTemplate.design_data === 'string') {
                try {
                    designData = JSON.parse(props.clientTemplate.design_data);
                } catch (e) {
                    console.error('Failed to parse design_data:', e);
                }
            } else {
                designData = props.clientTemplate.design_data;
            }
        }

        console.log('Processing design data...');

        // تحميل التصميم
        if (designData) {
            console.log('Processing design data...');

            // التحقق من نوع البيانات
            if (designData.pages || designData.components || (designData.html && designData.css)) {
                console.log('Loading GrapesJS format data');

                try {
                    // محاولة تحميل البيانات كـ project data
                    if (designData.pages || designData.components) {
                        editor.loadProjectData(designData);

                        // الحصول على HTML و CSS
                        designHtml.value = editor.getHtml();
                        designCss.value = editor.getCss();
                    } else if (designData.html && designData.css) {
                        // تحميل HTML/CSS مباشرة
                        designHtml.value = designData.html;
                        designCss.value = designData.css;
                    }

                    console.log('Generated HTML length:', designHtml.value.length);
                    console.log('Generated CSS length:', designCss.value.length);

                    // إضافة CSS إلى الصفحة
                    if (designCss.value) {
                        const styleElement = document.createElement('style');
                        styleElement.textContent = designCss.value;
                        document.head.appendChild(styleElement);
                    }
                } catch (loadError) {
                    console.error('Failed to load project data:', loadError);

                    // محاولة تحميل HTML/CSS مباشرة إذا كانت متوفرة
                    if (designData.html) {
                        designHtml.value = designData.html;
                        if (designData.css) {
                            designCss.value = designData.css;
                            const styleElement = document.createElement('style');
                            styleElement.textContent = designCss.value;
                            document.head.appendChild(styleElement);
                        }
                    }
                }
            } else if (designData.elements) {
                // Toujours traiter comme format custom si elements existe (tableau ou objet)
                console.log('Loading custom elements format data');
                let elementsArr = [];
                if (Array.isArray(designData.elements)) {
                    elementsArr = designData.elements;
                } else if (typeof designData.elements === 'object') {
                    elementsArr = [designData.elements];
                }
                // تحويل العناصر المخصصة إلى HTML/CSS
                const { html, css } = convertElementsToHtmlCss(elementsArr);
                designHtml.value = html;
                designCss.value = css;

                console.log('Generated HTML from elements length:', designHtml.value.length);
                console.log('Generated CSS from elements length:', designCss.value.length);

                // إضافة CSS إلى الصفحة
                if (designCss.value) {
                    const styleElement = document.createElement('style');
                    styleElement.textContent = designCss.value;
                    document.head.appendChild(styleElement);
                }
            } else {
                console.log('Unknown design data format');
                console.log('Available keys:', Object.keys(designData));

                // محاولة أخيرة: إذا كانت البيانات تحتوي على صورة مباشرة
                if (designData.src || (designData.type === 'image' && designData.src)) {
                    console.log('Detected single image data');
                    const imageHtml = `<img src="${designData.src}" style="max-width: 100%; max-height: 100%; object-fit: contain;" alt="Design Image" />`;
                    designHtml.value = imageHtml;
                }
            }
        } else {
            console.log('No design data found');
        }

        // تنظيف المحرر المؤقت
        editor.destroy();
        document.body.removeChild(tempContainer);

        // إذا لم يكن هناك محتوى، عرض رسالة
        if (!designHtml.value || !designHtml.value.trim()) {
            console.log('No content to display, showing fallback');

            // تحديد نوع البيانات المتاحة للمساعدة في التشخيص
            let debugInfo = 'No data';
            if (designData) {
                const keys = Object.keys(designData);
                debugInfo = `Keys: ${keys.join(', ')}`;

                // إضافة معلومات إضافية حول البيانات
                if (keys.includes('elements')) {
                    debugInfo += ` | Elements: ${Array.isArray(designData.elements) ? designData.elements.length : 'object'}`;
                }
                if (keys.includes('src')) {
                    debugInfo += ` | Has image source`;
                }
            }

            designHtml.value = `
                <div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #6b7280; font-family: Arial, sans-serif; padding: 20px;">
                    <div style="text-align: center; max-width: 400px;">
                        <svg style="width: 64px; height: 64px; margin: 0 auto 16px; opacity: 0.5;" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                        </svg>
                        <p style="margin: 0 0 8px 0; font-size: 16px;">لا يوجد محتوى للعرض</p>
                        <p style="font-size: 12px; margin: 0; color: #9ca3af; word-break: break-all;">${debugInfo}</p>
                        <p style="font-size: 11px; margin: 8px 0 0 0; color: #d1d5db;">Template ID: ${props.clientTemplate.id}</p>
                    </div>
                </div>
            `;
        }

        isLoading.value = false;

    } catch (error) {
        console.error('Error loading design:', error);
        loadError.value = error.message || 'خطأ في تحميل التصميم';
        isLoading.value = false;
    }
};

// تحويل العناصر المخصصة إلى HTML/CSS
const convertElementsToHtmlCss = (elements) => {
    let html = '';
    let css = '';
    
    // إنشاء container للتصميم
    html += `<div class="design-container" style="position: relative; width: ${canvasWidth.value}px; height: ${canvasHeight.value}px; background: white;">`;
    
    // ترتيب العناصر حسب zIndex
    const sortedElements = [...elements].sort((a, b) => (a.zIndex || 0) - (b.zIndex || 0));
    
    sortedElements.forEach((element, index) => {
        if (!element.visible) return; // تجاهل العناصر المخفية
        
        const elementId = `element-${index}`;
        
        // إنشاء HTML للعنصر
        switch (element.type) {
            case 'circle':
                html += createCircleHtml(element, elementId);
                css += createCircleCss(element, elementId);
                break;
            case 'rectangle':
                html += createRectangleHtml(element, elementId);
                css += createRectangleCss(element, elementId);
                break;
            case 'image':
                html += createImageHtml(element, elementId);
                css += createImageCss(element, elementId);
                break;
            case 'text':
                html += createTextHtml(element, elementId);
                css += createTextCss(element, elementId);
                break;
            default:
                console.warn('Unknown element type:', element.type);
        }
    });
    
    html += '</div>';
    
    return { html, css };
};

// إنشاء HTML للدائرة
const createCircleHtml = (element, elementId) => {
    return `<div id="${elementId}" class="design-element circle-element"></div>`;
};

// إنشاء CSS للدائرة
const createCircleCss = (element, elementId) => {
    const radius = Math.min(element.width || 100, element.height || 100) / 2;
    return `
        #${elementId} {
            position: absolute;
            left: ${element.x || 0}px;
            top: ${element.y || 0}px;
            width: ${element.width || 100}px;
            height: ${element.height || 100}px;
            background-color: ${element.backgroundColor || '#10b981'};
            border: ${element.borderWidth || 0}px solid ${element.borderColor || '#059669'};
            border-radius: 50%;
            opacity: ${element.opacity || 1};
            transform: rotate(${element.rotation || 0}deg);
            z-index: ${element.zIndex || 0};
        }
    `;
};

// إنشاء HTML للمستطيل
const createRectangleHtml = (element, elementId) => {
    return `<div id="${elementId}" class="design-element rectangle-element"></div>`;
};

// إنشاء CSS للمستطيل
const createRectangleCss = (element, elementId) => {
    return `
        #${elementId} {
            position: absolute;
            left: ${element.x || 0}px;
            top: ${element.y || 0}px;
            width: ${element.width || 200}px;
            height: ${element.height || 100}px;
            background-color: ${element.backgroundColor || '#8b5cf6'};
            border: ${element.borderWidth || 0}px solid ${element.borderColor || '#7c3aed'};
            border-radius: ${element.borderRadius || 0}px;
            opacity: ${element.opacity || 1};
            transform: rotate(${element.rotation || 0}deg);
            z-index: ${element.zIndex || 0};
        }
    `;
};

// إنشاء HTML للصورة
const createImageHtml = (element, elementId) => {
    return `<img id="${elementId}" class="design-element image-element" src="${element.src || ''}" alt="Design Image">`;
};

// إنشاء CSS للصورة
const createImageCss = (element, elementId) => {
    return `
        #${elementId} {
            position: absolute;
            left: ${element.x || 0}px;
            top: ${element.y || 0}px;
            width: ${element.width || 100}px;
            height: ${element.height || 100}px;
            opacity: ${element.opacity || 1};
            transform: rotate(${element.rotation || 0}deg);
            z-index: ${element.zIndex || 0};
            object-fit: cover;
            border-radius: ${element.borderRadius || 0}px;
        }
    `;
};

// إنشاء HTML للنص
const createTextHtml = (element, elementId) => {
    return `<div id="${elementId}" class="design-element text-element">${element.text || ''}</div>`;
};

// إنشاء CSS للنص
const createTextCss = (element, elementId) => {
    return `
        #${elementId} {
            position: absolute;
            left: ${element.x || 0}px;
            top: ${element.y || 0}px;
            width: ${element.width || 'auto'}px;
            height: ${element.height || 'auto'}px;
            font-size: ${element.fontSize || 16}px;
            font-family: ${element.fontFamily || 'Arial, sans-serif'};
            color: ${element.color || '#000000'};
            font-weight: ${element.fontWeight || 'normal'};
            text-align: ${element.textAlign || 'left'};
            opacity: ${element.opacity || 1};
            transform: rotate(${element.rotation || 0}deg);
            z-index: ${element.zIndex || 0};
            white-space: nowrap;
            overflow: hidden;
        }
    `;
};

// تنسيق التاريخ
const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('fr-FR', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    });
};

onMounted(async () => {
    await nextTick();
    setTimeout(() => {
        loadDesignWithGrapesJS();
    }, 100);
});
</script>

<style scoped>
#design-preview-container {
    background: #f8f9fa;
    background-image: 
        linear-gradient(45deg, #e9ecef 25%, transparent 25%), 
        linear-gradient(-45deg, #e9ecef 25%, transparent 25%), 
        linear-gradient(45deg, transparent 75%, #e9ecef 75%), 
        linear-gradient(-45deg, transparent 75%, #e9ecef 75%);
    background-size: 20px 20px;
    background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
}

#design-preview {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

/* Ensure the design content is properly styled */
:deep(#design-preview *) {
    pointer-events: none;
}
</style>