<template>
    <Head title="محرر التصميم">
        <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&family=Amiri:wght@400;700&family=Noto+Sans+Arabic:wght@300;400;600;700&display=swap" rel="stylesheet">
    </Head>

    <AdminLayoutSidebar>
        <template #breadcrumb>
            <Link :href="route('admin.templates.index')" class="text-gray-500 hover:text-gray-700">
                إدارة القوالب
            </Link>
            <svg class="w-5 h-5 text-gray-400 mx-2 rotate-180" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
            </svg>
            <span class="text-gray-500">محرر التصميم - {{ template.name }}</span>
        </template>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
                <div class="flex justify-between items-center">
                    <div class="text-white">
                        <h1 class="text-2xl font-bold">محرر التصميم</h1>
                        <p class="text-purple-100 mt-1">{{ template.name }}</p>
                        <div class="flex items-center mt-2 space-x-4 space-x-reverse">
                            <div class="flex items-center">
                                <button
                                    @click="toggleAutoSave"
                                    :class="[
                                        'flex items-center px-3 py-1 rounded-full text-xs font-semibold transition-colors',
                                        autoSaveEnabled
                                            ? 'bg-green-500 text-white'
                                            : 'bg-gray-500 text-white'
                                    ]"
                                >
                                    <i :class="autoSaveEnabled ? 'fas fa-check-circle' : 'fas fa-times-circle'" class="ml-1"></i>
                                    {{ autoSaveEnabled ? 'الحفظ التلقائي مفعل' : 'الحفظ التلقائي معطل' }}
                                </button>
                            </div>
                            <div v-if="lastSaved" class="text-purple-100 text-xs">
                                آخر حفظ: {{ formatTime(lastSaved) }}
                            </div>
                        </div>
                    </div>
                    <div class="flex space-x-3 space-x-reverse">
                        <!-- Undo/Redo buttons -->
                        <button
                            @click="undo"
                            :disabled="!canUndo"
                            class="bg-white text-purple-600 px-4 py-2 rounded-lg font-semibold hover:bg-purple-50 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                            title="تراجع (Ctrl+Z)"
                        >
                            <i class="fas fa-undo"></i>
                        </button>
                        <button
                            @click="redo"
                            :disabled="!canRedo"
                            class="bg-white text-purple-600 px-4 py-2 rounded-lg font-semibold hover:bg-purple-50 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                            title="إعادة (Ctrl+Y)"
                        >
                            <i class="fas fa-redo"></i>
                        </button>

                        <button
                            @click="saveDesign"
                            :disabled="saving"
                            class="bg-white text-purple-600 px-6 py-2 rounded-lg font-semibold hover:bg-purple-50 transition-colors duration-200 disabled:opacity-50"
                        >
                            <span v-if="saving">جاري الحفظ...</span>
                            <span v-else>حفظ التصميم</span>
                        </button>
                        <button
                            @click="previewDesign"
                            class="bg-purple-500 text-white px-6 py-2 rounded-lg font-semibold hover:bg-purple-700 transition-colors duration-200"
                        >
                            معاينة
                        </button>
                        <div class="relative">
                            <button
                                @click="showExportMenu = !showExportMenu"
                                class="bg-green-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-green-700 transition-colors duration-200"
                            >
                                تصدير
                            </button>
                            <div v-if="showExportMenu" class="absolute left-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10">
                                <div class="py-1">
                                    <button
                                        @click="exportAsImage('png')"
                                        class="block w-full text-right px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                    >
                                        تصدير كصورة PNG
                                    </button>
                                    <button
                                        @click="exportAsImage('jpeg')"
                                        class="block w-full text-right px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                    >
                                        تصدير كصورة JPEG
                                    </button>
                                    <button
                                        @click="exportAsJSON"
                                        class="block w-full text-right px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                    >
                                        تصدير كملف JSON
                                    </button>
                                </div>
                            </div>
                        </div>
                        <button
                            @click="importJSON"
                            class="bg-blue-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-700 transition-colors duration-200"
                        >
                            استيراد
                        </button>
                        <div class="relative">
                            <button
                                @click="showTemplatesMenu = !showTemplatesMenu"
                                class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-indigo-700 transition-colors duration-200"
                            >
                                قوالب جاهزة
                            </button>
                            <div v-if="showTemplatesMenu" class="absolute left-0 mt-2 w-64 bg-white rounded-md shadow-lg z-10 max-h-96 overflow-y-auto">
                                <div class="py-1">
                                    <div class="px-4 py-2 text-sm font-semibold text-gray-700 border-b">قوالب بطاقات العمل</div>
                                    <button
                                        v-for="template in businessCardTemplates"
                                        :key="template.id"
                                        @click="loadTemplate(template)"
                                        class="block w-full text-right px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                    >
                                        {{ template.name }}
                                    </button>

                                    <div class="px-4 py-2 text-sm font-semibold text-gray-700 border-b border-t">قوالب وسائل التواصل</div>
                                    <button
                                        v-for="template in socialMediaTemplates"
                                        :key="template.id"
                                        @click="loadTemplate(template)"
                                        class="block w-full text-right px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                    >
                                        {{ template.name }}
                                    </button>

                                    <div class="px-4 py-2 text-sm font-semibold text-gray-700 border-b border-t">قوالب المناسبات</div>
                                    <button
                                        v-for="template in occasionTemplates"
                                        :key="template.id"
                                        @click="loadTemplate(template)"
                                        class="block w-full text-right px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                    >
                                        {{ template.name }}
                                    </button>

                                    <div class="px-4 py-2 text-sm font-semibold text-gray-700 border-b border-t">عناصر سريعة</div>
                                    <button
                                        @click="addQuickElement('wedding')"
                                        class="block w-full text-right px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                    >
                                        عنصر زواج
                                    </button>
                                    <button
                                        @click="addQuickElement('congratulations')"
                                        class="block w-full text-right px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                    >
                                        عنصر تهنئة
                                    </button>
                                    <button
                                        @click="addQuickElement('ramadan')"
                                        class="block w-full text-right px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                    >
                                        عنصر رمضان
                                    </button>
                                    <button
                                        @click="addQuickElement('graduation')"
                                        class="block w-full text-right px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                    >
                                        عنصر تخرج
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Editor Layout -->
            <div class="flex h-screen">
                <!-- Toolbar -->
                <div class="w-20 bg-gray-50 border-l border-gray-200 flex flex-col items-center py-4 space-y-4">
                    <button 
                        v-for="tool in tools" 
                        :key="tool.id"
                        @click="selectTool(tool.id)"
                        :class="[
                            'w-12 h-12 rounded-lg flex items-center justify-center transition-all duration-200',
                            activeTool === tool.id 
                                ? 'bg-purple-600 text-white shadow-lg' 
                                : 'bg-white text-gray-600 hover:bg-purple-50 hover:text-purple-600 shadow-sm'
                        ]"
                        :title="tool.name"
                    >
                        <i :class="tool.icon" class="text-lg"></i>
                    </button>
                </div>

                <!-- Canvas Area -->
                <div class="flex-1 bg-gray-100 relative">
                    <div class="absolute inset-4 bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <canvas 
                            ref="fabricCanvas" 
                            id="design-canvas"
                            class="w-full h-full"
                        ></canvas>
                    </div>
                </div>

                <!-- Properties Panel -->
                <div class="w-80 bg-white border-r border-gray-200 overflow-y-auto">
                    <!-- Object Properties -->
                    <div v-if="selectedObject" class="p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">خصائص العنصر</h3>
                        
                        <!-- Position -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">الموضع</label>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">X</label>
                                    <input 
                                        v-model.number="objectProperties.left"
                                        @input="updateObjectProperty('left', $event.target.value)"
                                        type="number" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm"
                                    >
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Y</label>
                                    <input 
                                        v-model.number="objectProperties.top"
                                        @input="updateObjectProperty('top', $event.target.value)"
                                        type="number" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm"
                                    >
                                </div>
                            </div>
                        </div>

                        <!-- Size -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">الحجم</label>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">العرض</label>
                                    <input 
                                        v-model.number="objectProperties.width"
                                        @input="updateObjectProperty('width', $event.target.value)"
                                        type="number" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm"
                                    >
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">الارتفاع</label>
                                    <input 
                                        v-model.number="objectProperties.height"
                                        @input="updateObjectProperty('height', $event.target.value)"
                                        type="number" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm"
                                    >
                                </div>
                            </div>
                        </div>

                        <!-- Color (for text and shapes) -->
                        <div v-if="selectedObject.type === 'text' || selectedObject.type === 'rect' || selectedObject.type === 'circle'" class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">اللون</label>
                            <input 
                                v-model="objectProperties.fill"
                                @input="updateObjectProperty('fill', $event.target.value)"
                                type="color" 
                                class="w-full h-10 border border-gray-300 rounded-md"
                            >
                        </div>

                        <!-- Text Properties -->
                        <div v-if="selectedObject.type === 'text'" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">النص</label>
                                <textarea 
                                    v-model="objectProperties.text"
                                    @input="updateObjectProperty('text', $event.target.value)"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm"
                                    rows="3"
                                ></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">حجم الخط</label>
                                <input 
                                    v-model.number="objectProperties.fontSize"
                                    @input="updateObjectProperty('fontSize', $event.target.value)"
                                    type="number" 
                                    min="8" 
                                    max="200"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm"
                                >
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">نوع الخط</label>
                                <select
                                    v-model="objectProperties.fontFamily"
                                    @change="updateObjectProperty('fontFamily', $event.target.value)"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm"
                                >
                                    <option value="Arial">Arial</option>
                                    <option value="Helvetica">Helvetica</option>
                                    <option value="Times New Roman">Times New Roman</option>
                                    <option value="Courier New">Courier New</option>
                                    <option value="Tahoma">Tahoma</option>
                                    <option value="Cairo">Cairo (عربي)</option>
                                    <option value="Amiri">Amiri (عربي)</option>
                                    <option value="Noto Sans Arabic">Noto Sans Arabic</option>
                                </select>
                            </div>
                            <div class="grid grid-cols-3 gap-2">
                                <button
                                    @click="updateObjectProperty('fontWeight', objectProperties.fontWeight === 'bold' ? 'normal' : 'bold')"
                                    :class="[
                                        'px-3 py-2 text-xs rounded-md transition-colors',
                                        objectProperties.fontWeight === 'bold'
                                            ? 'bg-purple-600 text-white'
                                            : 'bg-gray-100 hover:bg-gray-200'
                                    ]"
                                >
                                    <i class="fas fa-bold"></i>
                                </button>
                                <button
                                    @click="updateObjectProperty('fontStyle', objectProperties.fontStyle === 'italic' ? 'normal' : 'italic')"
                                    :class="[
                                        'px-3 py-2 text-xs rounded-md transition-colors',
                                        objectProperties.fontStyle === 'italic'
                                            ? 'bg-purple-600 text-white'
                                            : 'bg-gray-100 hover:bg-gray-200'
                                    ]"
                                >
                                    <i class="fas fa-italic"></i>
                                </button>
                                <button
                                    @click="updateObjectProperty('underline', !objectProperties.underline)"
                                    :class="[
                                        'px-3 py-2 text-xs rounded-md transition-colors',
                                        objectProperties.underline
                                            ? 'bg-purple-600 text-white'
                                            : 'bg-gray-100 hover:bg-gray-200'
                                    ]"
                                >
                                    <i class="fas fa-underline"></i>
                                </button>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">محاذاة النص</label>
                                <div class="grid grid-cols-3 gap-2">
                                    <button
                                        @click="updateObjectProperty('textAlign', 'left')"
                                        :class="[
                                            'px-3 py-2 text-xs rounded-md transition-colors',
                                            objectProperties.textAlign === 'left'
                                                ? 'bg-purple-600 text-white'
                                                : 'bg-gray-100 hover:bg-gray-200'
                                        ]"
                                    >
                                        <i class="fas fa-align-left"></i>
                                    </button>
                                    <button
                                        @click="updateObjectProperty('textAlign', 'center')"
                                        :class="[
                                            'px-3 py-2 text-xs rounded-md transition-colors',
                                            objectProperties.textAlign === 'center'
                                                ? 'bg-purple-600 text-white'
                                                : 'bg-gray-100 hover:bg-gray-200'
                                        ]"
                                    >
                                        <i class="fas fa-align-center"></i>
                                    </button>
                                    <button
                                        @click="updateObjectProperty('textAlign', 'right')"
                                        :class="[
                                            'px-3 py-2 text-xs rounded-md transition-colors',
                                            objectProperties.textAlign === 'right'
                                                ? 'bg-purple-600 text-white'
                                                : 'bg-gray-100 hover:bg-gray-200'
                                        ]"
                                    >
                                        <i class="fas fa-align-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Delete Button -->
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <button 
                                @click="deleteSelectedObject"
                                class="w-full bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition-colors duration-200"
                            >
                                حذف العنصر
                            </button>
                        </div>
                    </div>

                    <!-- Canvas Properties -->
                    <div v-if="!selectedObject" class="p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">خصائص اللوحة</h3>

                        <!-- Canvas Size -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">حجم اللوحة</label>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">العرض</label>
                                    <input
                                        v-model.number="canvasProperties.width"
                                        @input="updateCanvasSize"
                                        type="number"
                                        min="100"
                                        max="2000"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm"
                                    >
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">الارتفاع</label>
                                    <input
                                        v-model.number="canvasProperties.height"
                                        @input="updateCanvasSize"
                                        type="number"
                                        min="100"
                                        max="2000"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm"
                                    >
                                </div>
                            </div>
                        </div>

                        <!-- Background Color -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">لون الخلفية</label>
                            <div class="space-y-3">
                                <input
                                    v-model="canvasProperties.backgroundColor"
                                    @input="updateCanvasBackground"
                                    type="color"
                                    class="w-full h-10 border border-gray-300 rounded-md"
                                >
                                <button
                                    @click="uploadBackgroundImage"
                                    class="w-full px-3 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors text-sm"
                                >
                                    رفع صورة خلفية
                                </button>
                                <button
                                    v-if="canvasProperties.backgroundImage"
                                    @click="removeBackgroundImage"
                                    class="w-full px-3 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors text-sm"
                                >
                                    إزالة صورة الخلفية
                                </button>
                            </div>
                        </div>

                        <!-- Preset Sizes -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">أحجام جاهزة</label>
                            <div class="grid grid-cols-2 gap-2">
                                <button
                                    @click="setCanvasSize(800, 600)"
                                    class="px-3 py-2 text-xs bg-gray-100 hover:bg-gray-200 rounded-md transition-colors"
                                >
                                    بطاقة عمل
                                </button>
                                <button
                                    @click="setCanvasSize(1080, 1080)"
                                    class="px-3 py-2 text-xs bg-gray-100 hover:bg-gray-200 rounded-md transition-colors"
                                >
                                    مربع
                                </button>
                                <button
                                    @click="setCanvasSize(1920, 1080)"
                                    class="px-3 py-2 text-xs bg-gray-100 hover:bg-gray-200 rounded-md transition-colors"
                                >
                                    عرض تقديمي
                                </button>
                                <button
                                    @click="setCanvasSize(1080, 1350)"
                                    class="px-3 py-2 text-xs bg-gray-100 hover:bg-gray-200 rounded-md transition-colors"
                                >
                                    قصة
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Layers Panel -->
                    <div class="p-6 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">الطبقات</h3>
                        <div class="space-y-2">
                            <div
                                v-for="(layer, index) in layers"
                                :key="layer.id"
                                @click="selectLayer(layer)"
                                @dblclick="startRenameLayer(layer, index)"
                                :class="[
                                    'p-3 rounded-lg border cursor-pointer transition-all duration-200 layer-item',
                                    selectedObject && selectedObject.id === layer.id
                                        ? 'border-purple-500 bg-purple-50'
                                        : 'border-gray-200 hover:border-gray-300'
                                ]"
                            >
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <input
                                            v-if="layer.editing"
                                            v-model="layer.editName"
                                            @blur="finishRenameLayer(layer)"
                                            @keyup.enter="finishRenameLayer(layer)"
                                            @keyup.escape="cancelRenameLayer(layer)"
                                            class="text-sm font-medium bg-transparent border-none outline-none w-full"
                                            @click.stop
                                        >
                                        <span v-else class="text-sm font-medium">{{ layer.name || `طبقة ${index + 1}` }}</span>
                                    </div>
                                    <div class="flex items-center space-x-1 space-x-reverse">
                                        <!-- Move buttons -->
                                        <div class="flex flex-col">
                                            <button
                                                @click.stop="moveLayerUp(index)"
                                                :disabled="index === 0"
                                                class="text-xs px-1 py-0.5 text-gray-400 hover:text-gray-600 disabled:opacity-30"
                                                title="تحريك لأعلى"
                                            >
                                                <i class="fas fa-chevron-up"></i>
                                            </button>
                                            <button
                                                @click.stop="moveLayerDown(index)"
                                                :disabled="index === layers.length - 1"
                                                class="text-xs px-1 py-0.5 text-gray-400 hover:text-gray-600 disabled:opacity-30"
                                                title="تحريك لأسفل"
                                            >
                                                <i class="fas fa-chevron-down"></i>
                                            </button>
                                        </div>

                                        <!-- Control buttons -->
                                        <button
                                            @click.stop="toggleLayerVisibility(layer)"
                                            :class="[
                                                'px-2 py-1 rounded text-xs transition-colors',
                                                layer.visible
                                                    ? 'text-green-600 hover:bg-green-50'
                                                    : 'text-gray-400 hover:bg-gray-50'
                                            ]"
                                            :title="layer.visible ? 'إخفاء' : 'إظهار'"
                                        >
                                            <i :class="layer.visible ? 'fas fa-eye' : 'fas fa-eye-slash'"></i>
                                        </button>
                                        <button
                                            @click.stop="toggleLayerLock(layer)"
                                            :class="[
                                                'px-2 py-1 rounded text-xs transition-colors',
                                                layer.locked
                                                    ? 'text-red-600 hover:bg-red-50'
                                                    : 'text-gray-400 hover:bg-gray-50'
                                            ]"
                                            :title="layer.locked ? 'إلغاء القفل' : 'قفل'"
                                        >
                                            <i :class="layer.locked ? 'fas fa-lock' : 'fas fa-unlock'"></i>
                                        </button>
                                        <button
                                            @click.stop="duplicateLayer(layer)"
                                            class="px-2 py-1 rounded text-xs text-blue-600 hover:bg-blue-50 transition-colors"
                                            title="نسخ"
                                        >
                                            <i class="fas fa-copy"></i>
                                        </button>
                                        <button
                                            @click.stop="deleteLayer(layer)"
                                            class="px-2 py-1 rounded text-xs text-red-600 hover:bg-red-50 transition-colors"
                                            title="حذف"
                                        >
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="text-xs text-gray-500 mt-1">{{ layer.type }}</div>
                            </div>
                        </div>

                        <!-- Advanced Text Tools -->
                        <div v-if="selectedObject && selectedObject.type === 'text'" class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">أدوات النص المتقدمة</h3>

                            <!-- Quick Text Presets -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">نصوص جاهزة</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <button
                                        @click="setQuickText('مبروك')"
                                        class="px-3 py-2 bg-green-100 text-green-800 rounded-md hover:bg-green-200 transition-colors text-sm"
                                    >
                                        مبروك
                                    </button>
                                    <button
                                        @click="setQuickText('تهانينا')"
                                        class="px-3 py-2 bg-blue-100 text-blue-800 rounded-md hover:bg-blue-200 transition-colors text-sm"
                                    >
                                        تهانينا
                                    </button>
                                    <button
                                        @click="setQuickText('رمضان مبارك')"
                                        class="px-3 py-2 bg-purple-100 text-purple-800 rounded-md hover:bg-purple-200 transition-colors text-sm"
                                    >
                                        رمضان مبارك
                                    </button>
                                    <button
                                        @click="setQuickText('مبارك الزواج')"
                                        class="px-3 py-2 bg-pink-100 text-pink-800 rounded-md hover:bg-pink-200 transition-colors text-sm"
                                    >
                                        مبارك الزواج
                                    </button>
                                    <button
                                        @click="setQuickText('مبروك التخرج')"
                                        class="px-3 py-2 bg-yellow-100 text-yellow-800 rounded-md hover:bg-yellow-200 transition-colors text-sm"
                                    >
                                        مبروك التخرج
                                    </button>
                                    <button
                                        @click="setQuickText('عيد مبارك')"
                                        class="px-3 py-2 bg-indigo-100 text-indigo-800 rounded-md hover:bg-indigo-200 transition-colors text-sm"
                                    >
                                        عيد مبارك
                                    </button>
                                </div>
                            </div>

                            <!-- Text Decorations -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">زخارف النص</label>
                                <div class="grid grid-cols-3 gap-2">
                                    <button
                                        @click="addTextDecoration('border')"
                                        class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-md transition-colors text-sm"
                                    >
                                        إطار
                                    </button>
                                    <button
                                        @click="addTextDecoration('background')"
                                        class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-md transition-colors text-sm"
                                    >
                                        خلفية
                                    </button>
                                    <button
                                        @click="addTextDecoration('glow')"
                                        class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-md transition-colors text-sm"
                                    >
                                        توهج
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Effects Section -->
                        <div v-if="selectedObject" class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">التأثيرات</h3>

                            <!-- Opacity -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">الشفافية</label>
                                <input
                                    v-model="objectProperties.opacity"
                                    @input="updateObjectProperty('opacity', $event.target.value / 100)"
                                    type="range"
                                    min="0"
                                    max="100"
                                    class="w-full"
                                >
                                <span class="text-xs text-gray-500">{{ objectProperties.opacity }}%</span>
                            </div>

                            <!-- Shadow -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">الظل</label>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input
                                            v-model="objectProperties.hasShadow"
                                            @change="toggleShadow"
                                            type="checkbox"
                                            class="ml-2"
                                        >
                                        تفعيل الظل
                                    </label>
                                    <div v-if="objectProperties.hasShadow" class="space-y-2 pr-6">
                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <label class="text-xs text-gray-600">الإزاحة X</label>
                                                <input
                                                    v-model="objectProperties.shadowOffsetX"
                                                    @input="updateShadow"
                                                    type="number"
                                                    class="w-full px-2 py-1 border rounded text-sm"
                                                >
                                            </div>
                                            <div>
                                                <label class="text-xs text-gray-600">الإزاحة Y</label>
                                                <input
                                                    v-model="objectProperties.shadowOffsetY"
                                                    @input="updateShadow"
                                                    type="number"
                                                    class="w-full px-2 py-1 border rounded text-sm"
                                                >
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <label class="text-xs text-gray-600">التمويه</label>
                                                <input
                                                    v-model="objectProperties.shadowBlur"
                                                    @input="updateShadow"
                                                    type="number"
                                                    min="0"
                                                    class="w-full px-2 py-1 border rounded text-sm"
                                                >
                                            </div>
                                            <div>
                                                <label class="text-xs text-gray-600">لون الظل</label>
                                                <input
                                                    v-model="objectProperties.shadowColor"
                                                    @input="updateShadow"
                                                    type="color"
                                                    class="w-full h-8 border rounded"
                                                >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Border Radius (for rectangles) -->
                            <div v-if="selectedObject && selectedObject.type === 'rect'" class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">انحناء الزوايا</label>
                                <input
                                    v-model="objectProperties.borderRadius"
                                    @input="updateObjectProperty('rx', $event.target.value); updateObjectProperty('ry', $event.target.value)"
                                    type="range"
                                    min="0"
                                    max="50"
                                    class="w-full"
                                >
                                <span class="text-xs text-gray-500">{{ objectProperties.borderRadius }}px</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notifications -->
        <div v-if="notification.show" class="fixed top-4 left-4 z-50">
            <div :class="[
                'px-6 py-4 rounded-lg shadow-lg text-white font-semibold',
                notification.type === 'success' ? 'bg-green-600' : 'bg-red-600'
            ]">
                {{ notification.message }}
            </div>
        </div>

        <!-- Loading Overlay -->
        <div v-if="loading" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 text-center">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-600 mx-auto mb-4"></div>
                <p class="text-gray-700 font-semibold">{{ loadingMessage }}</p>
            </div>
        </div>
    </AdminLayoutSidebar>
</template>

<script setup>
import { ref, onMounted, onUnmounted, reactive, computed, nextTick } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayoutSidebar from '@/Layouts/AdminLayoutSidebar.vue';
import * as fabric from 'fabric';

const props = defineProps({
    template: Object,
    categories: Array,
});

// Canvas and Fabric.js
const fabricCanvas = ref(null);
let canvas = null;

// State
const activeTool = ref('select');
const selectedObject = ref(null);
const saving = ref(false);
const layers = ref([]);
const showExportMenu = ref(false);
const showTemplatesMenu = ref(false);
const loading = ref(false);
const loadingMessage = ref('');

// Notification system
const notification = reactive({
    show: false,
    message: '',
    type: 'success'
});

// Removed drag & drop state for simplicity

// Auto-save state
const autoSaveEnabled = ref(true);
const autoSaveInterval = ref(null);
const lastSaved = ref(null);

// Undo/Redo state
const history = ref([]);
const historyIndex = ref(-1);
const maxHistorySize = 50;

// Computed properties for undo/redo
const canUndo = computed(() => historyIndex.value > 0);
const canRedo = computed(() => historyIndex.value < history.value.length - 1);

// Predefined templates
const businessCardTemplates = ref([
    {
        id: 'business-classic',
        name: 'بطاقة عمل كلاسيكية',
        data: {
            version: '6.7.0',
            objects: [
                {
                    type: 'rect',
                    left: 50,
                    top: 50,
                    width: 700,
                    height: 500,
                    fill: '#ffffff',
                    stroke: '#e5e7eb',
                    strokeWidth: 2,
                    rx: 10,
                    ry: 10
                },
                {
                    type: 'text',
                    left: 100,
                    top: 120,
                    text: 'اسم الشركة',
                    fontSize: 32,
                    fontFamily: 'Cairo',
                    fill: '#1f2937',
                    fontWeight: 'bold'
                },
                {
                    type: 'text',
                    left: 100,
                    top: 180,
                    text: 'الاسم الكامل',
                    fontSize: 24,
                    fontFamily: 'Cairo',
                    fill: '#374151'
                },
                {
                    type: 'text',
                    left: 100,
                    top: 220,
                    text: 'المنصب',
                    fontSize: 18,
                    fontFamily: 'Cairo',
                    fill: '#6b7280'
                }
            ],
            background: '#f9fafb'
        }
    },
    {
        id: 'business-modern',
        name: 'بطاقة عمل عصرية',
        data: {
            version: '6.7.0',
            objects: [
                {
                    type: 'rect',
                    left: 0,
                    top: 0,
                    width: 800,
                    height: 200,
                    fill: '#3b82f6',
                    stroke: 'transparent'
                },
                {
                    type: 'text',
                    left: 50,
                    top: 50,
                    text: 'اسم الشركة',
                    fontSize: 28,
                    fontFamily: 'Cairo',
                    fill: '#ffffff',
                    fontWeight: 'bold'
                },
                {
                    type: 'text',
                    left: 50,
                    top: 250,
                    text: 'الاسم الكامل',
                    fontSize: 24,
                    fontFamily: 'Cairo',
                    fill: '#1f2937'
                },
                {
                    type: 'text',
                    left: 50,
                    top: 290,
                    text: 'المنصب',
                    fontSize: 18,
                    fontFamily: 'Cairo',
                    fill: '#6b7280'
                }
            ],
            background: '#ffffff'
        }
    }
]);

const socialMediaTemplates = ref([
    {
        id: 'instagram-post',
        name: 'منشور إنستغرام',
        data: {
            version: '6.7.0',
            objects: [
                {
                    type: 'rect',
                    left: 0,
                    top: 0,
                    width: 800,
                    height: 600,
                    fill: 'linear-gradient(45deg, #667eea 0%, #764ba2 100%)',
                    stroke: 'transparent'
                },
                {
                    type: 'text',
                    left: 400,
                    top: 250,
                    text: 'عنوان المنشور',
                    fontSize: 36,
                    fontFamily: 'Cairo',
                    fill: '#ffffff',
                    fontWeight: 'bold',
                    textAlign: 'center',
                    originX: 'center'
                },
                {
                    type: 'text',
                    left: 400,
                    top: 320,
                    text: 'وصف قصير للمحتوى',
                    fontSize: 20,
                    fontFamily: 'Cairo',
                    fill: '#f3f4f6',
                    textAlign: 'center',
                    originX: 'center'
                }
            ],
            background: '#1f2937'
        }
    }
]);

// Occasion templates
const occasionTemplates = ref([
    {
        id: 'wedding-card',
        name: 'بطاقة زواج',
        data: {
            version: '6.7.0',
            objects: [
                {
                    type: 'rect',
                    left: 50,
                    top: 50,
                    width: 700,
                    height: 500,
                    fill: '#fdf2f8',
                    stroke: '#f9a8d4',
                    strokeWidth: 3,
                    rx: 20,
                    ry: 20
                },
                {
                    type: 'polygon',
                    left: 400,
                    top: 150,
                    points: [
                        {x: 0, y: -30}, {x: -15, y: -45}, {x: -35, y: -45}, {x: -50, y: -30},
                        {x: -50, y: -15}, {x: -35, y: 0}, {x: 0, y: 35}, {x: 35, y: 0},
                        {x: 50, y: -15}, {x: 50, y: -30}, {x: 35, y: -45}, {x: 15, y: -45}
                    ],
                    fill: '#ec4899',
                    originX: 'center',
                    originY: 'center'
                },
                {
                    type: 'text',
                    left: 400,
                    top: 250,
                    text: 'مبارك الزواج',
                    fontSize: 32,
                    fontFamily: 'Cairo',
                    fill: '#be185d',
                    fontWeight: 'bold',
                    textAlign: 'center',
                    originX: 'center'
                },
                {
                    type: 'text',
                    left: 400,
                    top: 320,
                    text: 'أسماء العروسين',
                    fontSize: 24,
                    fontFamily: 'Cairo',
                    fill: '#9d174d',
                    textAlign: 'center',
                    originX: 'center'
                }
            ],
            background: '#ffffff'
        }
    },
    {
        id: 'ramadan-card',
        name: 'بطاقة رمضان',
        data: {
            version: '6.7.0',
            objects: [
                {
                    type: 'rect',
                    left: 0,
                    top: 0,
                    width: 800,
                    height: 600,
                    fill: '#065f46',
                    stroke: 'transparent'
                },
                {
                    type: 'polygon',
                    left: 400,
                    top: 150,
                    points: [
                        {x: 0, y: -50}, {x: 15, y: -20}, {x: 47, y: -15}, {x: 23, y: 7},
                        {x: 29, y: 40}, {x: 0, y: 25}, {x: -29, y: 40}, {x: -23, y: 7},
                        {x: -47, y: -15}, {x: -14, y: -20}
                    ],
                    fill: '#fbbf24',
                    originX: 'center',
                    originY: 'center'
                },
                {
                    type: 'text',
                    left: 400,
                    top: 250,
                    text: 'رمضان مبارك',
                    fontSize: 36,
                    fontFamily: 'Cairo',
                    fill: '#fbbf24',
                    fontWeight: 'bold',
                    textAlign: 'center',
                    originX: 'center'
                },
                {
                    type: 'text',
                    left: 400,
                    top: 320,
                    text: 'كل عام وأنتم بخير',
                    fontSize: 20,
                    fontFamily: 'Cairo',
                    fill: '#d1fae5',
                    textAlign: 'center',
                    originX: 'center'
                }
            ],
            background: '#064e3b'
        }
    }
]);

// Object properties
const objectProperties = reactive({
    left: 0,
    top: 0,
    width: 0,
    height: 0,
    fill: '#000000',
    text: '',
    fontSize: 16,
    fontFamily: 'Arial',
    fontWeight: 'normal',
    fontStyle: 'normal',
    underline: false,
    textAlign: 'left',
    opacity: 100,
    hasShadow: false,
    shadowOffsetX: 5,
    shadowOffsetY: 5,
    shadowBlur: 10,
    shadowColor: '#000000',
    borderRadius: 0
});

// Canvas properties
const canvasProperties = reactive({
    width: 800,
    height: 600,
    backgroundColor: '#ffffff',
    backgroundImage: null
});

// Tools configuration
const tools = [
    { id: 'select', name: 'تحديد', icon: 'fas fa-mouse-pointer' },
    { id: 'text', name: 'نص', icon: 'fas fa-font' },
    { id: 'textbox', name: 'مربع نص', icon: 'fas fa-align-left' },
    { id: 'rectangle', name: 'مستطيل', icon: 'fas fa-square' },
    { id: 'circle', name: 'دائرة', icon: 'fas fa-circle' },
    { id: 'triangle', name: 'مثلث', icon: 'fas fa-play' },
    { id: 'star', name: 'نجمة', icon: 'fas fa-star' },
    { id: 'heart', name: 'قلب', icon: 'fas fa-heart' },
    { id: 'arrow', name: 'سهم', icon: 'fas fa-arrow-right' },
    { id: 'polygon', name: 'مضلع', icon: 'fas fa-draw-polygon' },
    { id: 'ellipse', name: 'بيضاوي', icon: 'fas fa-circle' },
    { id: 'diamond', name: 'معين', icon: 'fas fa-gem' },
    { id: 'badge', name: 'شارة', icon: 'fas fa-certificate' },
    { id: 'frame', name: 'إطار', icon: 'fas fa-border-style' },
    { id: 'image', name: 'صورة', icon: 'fas fa-image' },
    { id: 'line', name: 'خط', icon: 'fas fa-minus' },
    { id: 'curve', name: 'منحنى', icon: 'fas fa-bezier-curve' },
    { id: 'brush', name: 'فرشاة', icon: 'fas fa-paint-brush' },
    { id: 'background', name: 'خلفية', icon: 'fas fa-fill-drip' },
];

// Initialize Fabric.js canvas
const initCanvas = () => {
    canvas = new fabric.Canvas('design-canvas', {
        width: 800,
        height: 600,
        backgroundColor: '#ffffff'
    });

    // Load existing design data if available
    if (props.template.design_data) {
        canvas.loadFromJSON(props.template.design_data, () => {
            canvas.renderAll();
            updateLayers();
        });
    }

    // Event listeners
    canvas.on('selection:created', handleObjectSelection);
    canvas.on('selection:updated', handleObjectSelection);
    canvas.on('selection:cleared', handleObjectDeselection);
    canvas.on('object:modified', (e) => {
        updateObjectProperties();
        // Save state after modification
        setTimeout(() => saveState(), 100);
    });
    canvas.on('object:added', (e) => {
        updateLayers();
        // Save state after adding object (but not during initial load)
        if (canvas._objects.length > 0) {
            setTimeout(() => saveState(), 100);
        }
    });
    canvas.on('object:removed', (e) => {
        updateLayers();
        // Save state after removing object
        setTimeout(() => saveState(), 100);
    });

    // Save initial state
    setTimeout(() => {
        saveState();
    }, 500);
};

// Handle object selection
const handleObjectSelection = (e) => {
    selectedObject.value = e.selected[0];
    if (selectedObject.value) {
        updateObjectPropertiesFromSelection();
    }
};

// Handle object deselection
const handleObjectDeselection = () => {
    selectedObject.value = null;
};

// Update object properties from selected object
const updateObjectPropertiesFromSelection = () => {
    if (!selectedObject.value) return;
    
    objectProperties.left = Math.round(selectedObject.value.left || 0);
    objectProperties.top = Math.round(selectedObject.value.top || 0);
    objectProperties.width = Math.round(selectedObject.value.width * selectedObject.value.scaleX || 0);
    objectProperties.height = Math.round(selectedObject.value.height * selectedObject.value.scaleY || 0);
    objectProperties.fill = selectedObject.value.fill || '#000000';
    
    if (selectedObject.value.type === 'text') {
        objectProperties.text = selectedObject.value.text || '';
        objectProperties.fontSize = selectedObject.value.fontSize || 16;
        objectProperties.fontFamily = selectedObject.value.fontFamily || 'Arial';
        objectProperties.fontWeight = selectedObject.value.fontWeight || 'normal';
        objectProperties.fontStyle = selectedObject.value.fontStyle || 'normal';
        objectProperties.underline = selectedObject.value.underline || false;
        objectProperties.textAlign = selectedObject.value.textAlign || 'left';
    }

    // Update effects properties
    objectProperties.opacity = Math.round((selectedObject.value.opacity || 1) * 100);
    objectProperties.hasShadow = !!selectedObject.value.shadow;
    if (selectedObject.value.shadow) {
        objectProperties.shadowOffsetX = selectedObject.value.shadow.offsetX || 5;
        objectProperties.shadowOffsetY = selectedObject.value.shadow.offsetY || 5;
        objectProperties.shadowBlur = selectedObject.value.shadow.blur || 10;
        objectProperties.shadowColor = selectedObject.value.shadow.color || '#000000';
    }
    objectProperties.borderRadius = selectedObject.value.rx || 0;
};

// Update object property
const updateObjectProperty = (property, value) => {
    if (!selectedObject.value) return;
    
    selectedObject.value.set(property, value);
    canvas.renderAll();
};

// Update object properties when object is modified
const updateObjectProperties = () => {
    if (selectedObject.value) {
        updateObjectPropertiesFromSelection();
    }
};

// Select tool
const selectTool = (toolId) => {
    activeTool.value = toolId;
    
    if (toolId === 'select') {
        canvas.isDrawingMode = false;
        canvas.selection = true;
    } else {
        canvas.isDrawingMode = false;
        canvas.selection = false;
        
        // Add click listener for adding objects
        canvas.off('mouse:down', handleCanvasClick);
        canvas.on('mouse:down', handleCanvasClick);
    }
};

// Handle canvas click for adding objects
const handleCanvasClick = (e) => {
    if (!canvas || activeTool.value === 'select') return;

    const pointer = canvas.getPointer(e.e);
    
    switch (activeTool.value) {
        case 'text':
            addText(pointer);
            break;
        case 'textbox':
            addTextbox(pointer);
            break;
        case 'rectangle':
            addRectangle(pointer);
            break;
        case 'circle':
            addCircle(pointer);
            break;
        case 'triangle':
            addTriangle(pointer);
            break;
        case 'star':
            addStar(pointer);
            break;
        case 'heart':
            addHeart(pointer);
            break;
        case 'arrow':
            addArrow(pointer);
            break;
        case 'polygon':
            addPolygon(pointer);
            break;
        case 'ellipse':
            addEllipse(pointer);
            break;
        case 'diamond':
            addDiamond(pointer);
            break;
        case 'badge':
            addBadge(pointer);
            break;
        case 'frame':
            addFrame(pointer);
            break;
        case 'line':
            addLine(pointer);
            break;
        case 'curve':
            addCurve(pointer);
            break;
        case 'brush':
            enableDrawing();
            break;
        case 'image':
            addImageUpload();
            break;
        case 'background':
            changeBackground();
            break;
    }
    
    // Switch back to select tool
    activeTool.value = 'select';
    selectTool('select');
};

// Add text
const addText = (pointer) => {
    if (!canvas) return;

    const text = new fabric.Text('نص جديد', {
        left: pointer.x,
        top: pointer.y,
        fontSize: 20,
        fill: '#000000',
        fontFamily: 'Cairo',
        textAlign: 'right',
        direction: 'rtl'
    });

    canvas.add(text);
    canvas.setActiveObject(text);
};

// Add rectangle
const addRectangle = (pointer) => {
    if (!canvas) return;

    const rect = new fabric.Rect({
        left: pointer.x,
        top: pointer.y,
        width: 100,
        height: 60,
        fill: '#3B82F6',
        stroke: '#1E40AF',
        strokeWidth: 2
    });

    canvas.add(rect);
    canvas.setActiveObject(rect);
};

// Helper function to add object to canvas safely
const addObjectToCanvas = (object) => {
    if (!canvas) return false;
    canvas.add(object);
    canvas.setActiveObject(object);
    return true;
};

// Add circle
const addCircle = (pointer) => {
    if (!canvas) return;

    const circle = new fabric.Circle({
        left: pointer.x,
        top: pointer.y,
        radius: 50,
        fill: '#EF4444',
        stroke: '#DC2626',
        strokeWidth: 2
    });

    addObjectToCanvas(circle);
};

// Add triangle
const addTriangle = (pointer) => {
    if (!canvas) return;

    const triangle = new fabric.Triangle({
        left: pointer.x,
        top: pointer.y,
        width: 80,
        height: 80,
        fill: '#10B981',
        stroke: '#059669',
        strokeWidth: 2
    });

    addObjectToCanvas(triangle);
};

// Add line
const addLine = (pointer) => {
    const line = new fabric.Line([pointer.x, pointer.y, pointer.x + 100, pointer.y], {
        stroke: '#000000',
        strokeWidth: 2
    });

    canvas.add(line);
    canvas.setActiveObject(line);
};

// Add star
const addStar = (pointer) => {
    const star = new fabric.Polygon([
        {x: 0, y: -50},
        {x: 14, y: -20},
        {x: 47, y: -15},
        {x: 23, y: 7},
        {x: 29, y: 40},
        {x: 0, y: 25},
        {x: -29, y: 40},
        {x: -23, y: 7},
        {x: -47, y: -15},
        {x: -14, y: -20}
    ], {
        left: pointer.x,
        top: pointer.y,
        fill: '#FFD700',
        stroke: '#FFA500',
        strokeWidth: 2
    });

    canvas.add(star);
    canvas.setActiveObject(star);
};

// Add arrow
const addArrow = (pointer) => {
    const arrow = new fabric.Polygon([
        {x: 0, y: 0},
        {x: 80, y: 0},
        {x: 80, y: -15},
        {x: 100, y: 10},
        {x: 80, y: 35},
        {x: 80, y: 20},
        {x: 0, y: 20}
    ], {
        left: pointer.x,
        top: pointer.y,
        fill: '#2563EB',
        stroke: '#1D4ED8',
        strokeWidth: 2
    });

    canvas.add(arrow);
    canvas.setActiveObject(arrow);
};

// Add polygon (hexagon)
const addPolygon = (pointer) => {
    const polygon = new fabric.Polygon([
        {x: 50, y: 0},
        {x: 93, y: 25},
        {x: 93, y: 75},
        {x: 50, y: 100},
        {x: 7, y: 75},
        {x: 7, y: 25}
    ], {
        left: pointer.x,
        top: pointer.y,
        fill: '#8B5CF6',
        stroke: '#7C3AED',
        strokeWidth: 2
    });

    canvas.add(polygon);
    canvas.setActiveObject(polygon);
};

// Add ellipse
const addEllipse = (pointer) => {
    if (!canvas) return;

    const ellipse = new fabric.Ellipse({
        left: pointer.x,
        top: pointer.y,
        rx: 60,
        ry: 40,
        fill: '#F59E0B',
        stroke: '#D97706',
        strokeWidth: 2
    });

    addObjectToCanvas(ellipse);
};

// Add textbox (multi-line text)
const addTextbox = (pointer) => {
    if (!canvas) return;

    const textbox = new fabric.Textbox('نص متعدد الأسطر\nيمكن تحريره', {
        left: pointer.x,
        top: pointer.y,
        width: 200,
        fontSize: 18,
        fill: '#000000',
        fontFamily: 'Cairo',
        textAlign: 'right',
        direction: 'rtl'
    });

    addObjectToCanvas(textbox);
};

// Add heart shape
const addHeart = (pointer) => {
    if (!canvas) return;

    const heart = new fabric.Polygon([
        {x: 0, y: -30},
        {x: -15, y: -45},
        {x: -35, y: -45},
        {x: -50, y: -30},
        {x: -50, y: -15},
        {x: -35, y: 0},
        {x: 0, y: 35},
        {x: 35, y: 0},
        {x: 50, y: -15},
        {x: 50, y: -30},
        {x: 35, y: -45},
        {x: 15, y: -45}
    ], {
        left: pointer.x,
        top: pointer.y,
        fill: '#EF4444',
        stroke: '#DC2626',
        strokeWidth: 2
    });

    addObjectToCanvas(heart);
};

// Add diamond shape
const addDiamond = (pointer) => {
    if (!canvas) return;

    const diamond = new fabric.Polygon([
        {x: 0, y: -50},
        {x: 40, y: 0},
        {x: 0, y: 50},
        {x: -40, y: 0}
    ], {
        left: pointer.x,
        top: pointer.y,
        fill: '#A855F7',
        stroke: '#9333EA',
        strokeWidth: 2
    });

    addObjectToCanvas(diamond);
};

// Add badge/seal shape
const addBadge = (pointer) => {
    if (!canvas) return;

    const badge = new fabric.Polygon([
        {x: 0, y: -50},
        {x: 15, y: -35},
        {x: 35, y: -35},
        {x: 50, y: -15},
        {x: 50, y: 15},
        {x: 35, y: 35},
        {x: 15, y: 35},
        {x: 0, y: 50},
        {x: -15, y: 35},
        {x: -35, y: 35},
        {x: -50, y: 15},
        {x: -50, y: -15},
        {x: -35, y: -35},
        {x: -15, y: -35}
    ], {
        left: pointer.x,
        top: pointer.y,
        fill: '#F59E0B',
        stroke: '#D97706',
        strokeWidth: 2
    });

    addObjectToCanvas(badge);
};

// Add decorative frame
const addFrame = (pointer) => {
    if (!canvas) return;

    const frame = new fabric.Group([
        new fabric.Rect({
            width: 200,
            height: 150,
            fill: 'transparent',
            stroke: '#6B7280',
            strokeWidth: 8
        }),
        new fabric.Rect({
            width: 180,
            height: 130,
            left: 10,
            top: 10,
            fill: 'transparent',
            stroke: '#9CA3AF',
            strokeWidth: 2
        })
    ], {
        left: pointer.x,
        top: pointer.y
    });

    addObjectToCanvas(frame);
};

// Add curve (bezier curve)
const addCurve = (pointer) => {
    if (!canvas) return;

    const curve = new fabric.Path('M 0 0 Q 50 -50 100 0', {
        left: pointer.x,
        top: pointer.y,
        fill: 'transparent',
        stroke: '#059669',
        strokeWidth: 3
    });

    addObjectToCanvas(curve);
};

// Enable drawing mode
const enableDrawing = () => {
    if (!canvas) return;

    canvas.isDrawingMode = !canvas.isDrawingMode;

    if (canvas.isDrawingMode) {
        canvas.freeDrawingBrush.width = 5;
        canvas.freeDrawingBrush.color = '#000000';
        showNotification('تم تفعيل وضع الرسم الحر', 'success');
    } else {
        showNotification('تم إيقاف وضع الرسم الحر', 'success');
    }

    // Switch back to select tool when drawing is disabled
    if (!canvas.isDrawingMode) {
        activeTool.value = 'select';
        selectTool('select');
    }
};

// Add image upload
const addImageUpload = () => {
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = 'image/*';
    input.onchange = (e) => {
        const file = e.target.files[0];
        if (file) {
            // Validate file size (max 5MB)
            if (file.size > 5 * 1024 * 1024) {
                showNotification('حجم الصورة كبير جداً. الحد الأقصى 5 ميجابايت.', 'error');
                return;
            }

            // Validate file type
            if (!file.type.startsWith('image/')) {
                showNotification('يرجى اختيار ملف صورة صحيح.', 'error');
                return;
            }

            showLoading('جاري رفع الصورة...');

            const reader = new FileReader();
            reader.onload = (event) => {
                const imgElement = new Image();
                imgElement.onload = () => {
                    const fabricImg = new fabric.Image(imgElement);

                    // Scale image to fit canvas if too large
                    const maxWidth = canvas.width * 0.8;
                    const maxHeight = canvas.height * 0.8;

                    let scaleX = 1;
                    let scaleY = 1;

                    if (fabricImg.width > maxWidth) {
                        scaleX = maxWidth / fabricImg.width;
                    }
                    if (fabricImg.height > maxHeight) {
                        scaleY = maxHeight / fabricImg.height;
                    }

                    const scale = Math.min(scaleX, scaleY);

                    fabricImg.set({
                        left: 100,
                        top: 100,
                        scaleX: scale,
                        scaleY: scale
                    });

                    canvas.add(fabricImg);
                    canvas.setActiveObject(fabricImg);
                    hideLoading();
                    showNotification('تم رفع الصورة بنجاح', 'success');
                };
                imgElement.onerror = () => {
                    hideLoading();
                    showNotification('حدث خطأ أثناء تحميل الصورة', 'error');
                };
                imgElement.src = event.target.result;
            };
            reader.onerror = () => {
                hideLoading();
                showNotification('حدث خطأ أثناء قراءة الصورة', 'error');
            };
            reader.readAsDataURL(file);
        }
    };
    input.click();
};

// Change background
const changeBackground = () => {
    const input = document.createElement('input');
    input.type = 'color';
    input.value = canvas.backgroundColor || '#ffffff';
    input.onchange = (e) => {
        canvas.setBackgroundColor(e.target.value, canvas.renderAll.bind(canvas));
        canvasProperties.backgroundColor = e.target.value;
    };
    input.click();
};

// Update canvas size
const updateCanvasSize = () => {
    canvas.setDimensions({
        width: canvasProperties.width,
        height: canvasProperties.height
    });
    canvas.renderAll();
};

// Update canvas background
const updateCanvasBackground = () => {
    if (!canvas) {
        console.warn('Canvas not initialized yet');
        return;
    }

    if (canvasProperties.backgroundImage) {
        // If there's a background image, don't override it with color
        return;
    }
    canvas.setBackgroundColor(canvasProperties.backgroundColor, canvas.renderAll.bind(canvas));
};

// Upload background image
const uploadBackgroundImage = () => {
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = 'image/*';
    input.onchange = (e) => {
        const file = e.target.files[0];
        if (file) {
            // Validate file size (max 10MB for background)
            if (file.size > 10 * 1024 * 1024) {
                showNotification('حجم الصورة كبير جداً. الحد الأقصى 10 ميجابايت.', 'error');
                return;
            }

            showLoading('جاري رفع صورة الخلفية...');

            const reader = new FileReader();
            reader.onload = (event) => {
                const imgElement = new Image();
                imgElement.onload = () => {
                    const fabricImg = new fabric.Image(imgElement);

                    // Scale image to cover the entire canvas
                    const scaleX = canvas.width / fabricImg.width;
                    const scaleY = canvas.height / fabricImg.height;
                    const scale = Math.max(scaleX, scaleY);

                    fabricImg.set({
                        scaleX: scale,
                        scaleY: scale,
                        originX: 'center',
                        originY: 'center',
                        left: canvas.width / 2,
                        top: canvas.height / 2
                    });

                    canvas.setBackgroundImage(fabricImg, canvas.renderAll.bind(canvas));
                    canvasProperties.backgroundImage = event.target.result;
                    hideLoading();
                    showNotification('تم تعيين صورة الخلفية بنجاح', 'success');
                };
                imgElement.onerror = () => {
                    hideLoading();
                    showNotification('حدث خطأ أثناء تحميل الصورة', 'error');
                };
                imgElement.src = event.target.result;
            };
            reader.onerror = () => {
                hideLoading();
                showNotification('حدث خطأ أثناء قراءة الصورة', 'error');
            };
            reader.readAsDataURL(file);
        }
    };
    input.click();
};

// Remove background image
const removeBackgroundImage = () => {
    canvas.setBackgroundImage(null, canvas.renderAll.bind(canvas));
    canvas.setBackgroundColor(canvasProperties.backgroundColor, canvas.renderAll.bind(canvas));
    canvasProperties.backgroundImage = null;
    showNotification('تم إزالة صورة الخلفية', 'success');
};

// Toggle shadow effect
const toggleShadow = () => {
    if (!selectedObject.value) return;

    if (objectProperties.hasShadow) {
        const shadow = new fabric.Shadow({
            color: objectProperties.shadowColor,
            blur: objectProperties.shadowBlur,
            offsetX: objectProperties.shadowOffsetX,
            offsetY: objectProperties.shadowOffsetY
        });
        selectedObject.value.set('shadow', shadow);
    } else {
        selectedObject.value.set('shadow', null);
    }

    canvas.renderAll();
};

// Update shadow properties
const updateShadow = () => {
    if (!selectedObject.value || !objectProperties.hasShadow) return;

    const shadow = new fabric.Shadow({
        color: objectProperties.shadowColor,
        blur: parseInt(objectProperties.shadowBlur),
        offsetX: parseInt(objectProperties.shadowOffsetX),
        offsetY: parseInt(objectProperties.shadowOffsetY)
    });

    selectedObject.value.set('shadow', shadow);
    canvas.renderAll();
};

// Set canvas size to preset
const setCanvasSize = (width, height) => {
    canvasProperties.width = width;
    canvasProperties.height = height;
    updateCanvasSize();
};

// Load predefined template
const loadTemplate = (template) => {
    if (confirm('هل أنت متأكد من تحميل هذا القالب؟ سيتم استبدال التصميم الحالي.')) {
        showLoading('جاري تحميل القالب...');

        canvas.loadFromJSON(template.data, () => {
            canvas.renderAll();
            updateLayers();

            // Update canvas properties
            canvasProperties.width = canvas.width;
            canvasProperties.height = canvas.height;
            canvasProperties.backgroundColor = template.data.background || '#ffffff';

            hideLoading();
            showNotification('تم تحميل القالب بنجاح', 'success');
            showTemplatesMenu.value = false;

            // Save state for undo/redo
            setTimeout(() => saveState(), 100);
        });
    }
};

// Add quick elements for occasions
const addQuickElement = (type) => {
    if (!canvas) return;

    const centerX = canvas.width / 2;
    const centerY = canvas.height / 2;

    switch (type) {
        case 'wedding':
            // Add wedding elements
            const heart = new fabric.Polygon([
                {x: 0, y: -30}, {x: -15, y: -45}, {x: -35, y: -45}, {x: -50, y: -30},
                {x: -50, y: -15}, {x: -35, y: 0}, {x: 0, y: 35}, {x: 35, y: 0},
                {x: 50, y: -15}, {x: 50, y: -30}, {x: 35, y: -45}, {x: 15, y: -45}
            ], {
                left: centerX - 100,
                top: centerY - 100,
                fill: '#ec4899',
                stroke: '#be185d',
                strokeWidth: 2
            });

            const weddingText = new fabric.Text('مبارك الزواج', {
                left: centerX,
                top: centerY,
                fontSize: 32,
                fontFamily: 'Cairo',
                fill: '#be185d',
                fontWeight: 'bold',
                textAlign: 'center',
                originX: 'center',
                originY: 'center'
            });

            canvas.add(heart);
            canvas.add(weddingText);
            break;

        case 'congratulations':
            const star = new fabric.Polygon([
                {x: 0, y: -50}, {x: 14, y: -20}, {x: 47, y: -15}, {x: 23, y: 7},
                {x: 29, y: 40}, {x: 0, y: 25}, {x: -29, y: 40}, {x: -23, y: 7},
                {x: -47, y: -15}, {x: -14, y: -20}
            ], {
                left: centerX - 100,
                top: centerY - 100,
                fill: '#fbbf24',
                stroke: '#f59e0b',
                strokeWidth: 2
            });

            const congratsText = new fabric.Text('مبروك', {
                left: centerX,
                top: centerY,
                fontSize: 36,
                fontFamily: 'Cairo',
                fill: '#f59e0b',
                fontWeight: 'bold',
                textAlign: 'center',
                originX: 'center',
                originY: 'center'
            });

            canvas.add(star);
            canvas.add(congratsText);
            break;

        case 'ramadan':
            const crescent = new fabric.Path('M 50 0 A 25 25 0 1 0 50 50 A 15 15 0 1 1 50 0', {
                left: centerX - 100,
                top: centerY - 100,
                fill: '#fbbf24',
                stroke: '#f59e0b',
                strokeWidth: 2
            });

            const ramadanText = new fabric.Text('رمضان مبارك', {
                left: centerX,
                top: centerY,
                fontSize: 32,
                fontFamily: 'Cairo',
                fill: '#065f46',
                fontWeight: 'bold',
                textAlign: 'center',
                originX: 'center',
                originY: 'center'
            });

            canvas.add(crescent);
            canvas.add(ramadanText);
            break;

        case 'graduation':
            const cap = new fabric.Group([
                new fabric.Rect({
                    width: 80,
                    height: 60,
                    fill: '#1f2937',
                    originX: 'center',
                    originY: 'center'
                }),
                new fabric.Rect({
                    width: 100,
                    height: 10,
                    top: -35,
                    fill: '#1f2937',
                    originX: 'center',
                    originY: 'center'
                })
            ], {
                left: centerX - 100,
                top: centerY - 100
            });

            const gradText = new fabric.Text('مبروك التخرج', {
                left: centerX,
                top: centerY,
                fontSize: 28,
                fontFamily: 'Cairo',
                fill: '#1f2937',
                fontWeight: 'bold',
                textAlign: 'center',
                originX: 'center',
                originY: 'center'
            });

            canvas.add(cap);
            canvas.add(gradText);
            break;
    }

    showNotification(`تم إضافة عنصر ${type}`, 'success');
    showTemplatesMenu.value = false;
};

// Set quick text for selected text object
const setQuickText = (text) => {
    if (!selectedObject.value || selectedObject.value.type !== 'text') return;

    selectedObject.value.set('text', text);
    objectProperties.text = text;
    canvas.renderAll();
    showNotification('تم تحديث النص', 'success');
};

// Add text decorations
const addTextDecoration = (type) => {
    if (!selectedObject.value || selectedObject.value.type !== 'text') return;

    switch (type) {
        case 'border':
            selectedObject.value.set({
                stroke: '#000000',
                strokeWidth: 2
            });
            break;

        case 'background':
            selectedObject.value.set({
                backgroundColor: '#ffffff',
                padding: 10
            });
            break;

        case 'glow':
            const shadow = new fabric.Shadow({
                color: selectedObject.value.fill,
                blur: 20,
                offsetX: 0,
                offsetY: 0
            });
            selectedObject.value.set('shadow', shadow);
            break;
    }

    canvas.renderAll();
    showNotification(`تم إضافة ${type} للنص`, 'success');
};

// Delete selected object
const deleteSelectedObject = () => {
    if (selectedObject.value) {
        canvas.remove(selectedObject.value);
        selectedObject.value = null;
    }
};

// Update layers list
const updateLayers = () => {
    layers.value = canvas.getObjects().map((obj, index) => ({
        id: obj.id || `layer-${index}`,
        name: obj.name || getObjectDisplayName(obj),
        type: getObjectTypeName(obj.type),
        visible: obj.visible !== false,
        locked: obj.lockMovementX || false,
        object: obj
    }));
};

// Get object display name
const getObjectDisplayName = (obj) => {
    switch (obj.type) {
        case 'text': return obj.text ? obj.text.substring(0, 20) + '...' : 'نص';
        case 'rect': return 'مستطيل';
        case 'circle': return 'دائرة';
        case 'line': return 'خط';
        case 'image': return 'صورة';
        default: return 'عنصر';
    }
};

// Get object type name in Arabic
const getObjectTypeName = (type) => {
    const typeNames = {
        text: 'نص',
        rect: 'مستطيل',
        circle: 'دائرة',
        line: 'خط',
        image: 'صورة'
    };
    return typeNames[type] || 'عنصر';
};

// Select layer
const selectLayer = (layer) => {
    canvas.setActiveObject(layer.object);
    canvas.renderAll();
};

// Toggle layer visibility
const toggleLayerVisibility = (layer) => {
    layer.visible = !layer.visible;
    layer.object.set('visible', layer.visible);
    canvas.renderAll();
};

// Toggle layer lock
const toggleLayerLock = (layer) => {
    layer.locked = !layer.locked;
    layer.object.set({
        lockMovementX: layer.locked,
        lockMovementY: layer.locked,
        lockRotation: layer.locked,
        lockScalingX: layer.locked,
        lockScalingY: layer.locked
    });
    canvas.renderAll();
};

// Start rename layer
const startRenameLayer = (layer, index) => {
    layer.editing = true;
    layer.editName = layer.name || `طبقة ${index + 1}`;
};

// Finish rename layer
const finishRenameLayer = (layer) => {
    if (layer.editName && layer.editName.trim()) {
        layer.name = layer.editName.trim();
        layer.object.name = layer.name;
    }
    layer.editing = false;
    delete layer.editName;
};

// Cancel rename layer
const cancelRenameLayer = (layer) => {
    layer.editing = false;
    delete layer.editName;
};

// Move layer up
const moveLayerUp = (index) => {
    if (index > 0) {
        const layer = layers.value[index];
        canvas.bringForward(layer.object);
        updateLayers();
    }
};

// Move layer down
const moveLayerDown = (index) => {
    if (index < layers.value.length - 1) {
        const layer = layers.value[index];
        canvas.sendBackwards(layer.object);
        updateLayers();
    }
};

// Duplicate layer
const duplicateLayer = (layer) => {
    layer.object.clone((cloned) => {
        cloned.set({
            left: cloned.left + 10,
            top: cloned.top + 10
        });
        canvas.add(cloned);
        canvas.setActiveObject(cloned);
    });
};

// Delete layer
const deleteLayer = (layer) => {
    if (confirm('هل أنت متأكد من حذف هذه الطبقة؟')) {
        canvas.remove(layer.object);
        if (selectedObject.value === layer.object) {
            selectedObject.value = null;
        }
    }
};

// Undo/Redo functionality
const saveState = () => {
    const state = canvas.toJSON();

    // Remove future history if we're not at the end
    if (historyIndex.value < history.value.length - 1) {
        history.value = history.value.slice(0, historyIndex.value + 1);
    }

    // Add new state
    history.value.push(state);
    historyIndex.value = history.value.length - 1;

    // Limit history size
    if (history.value.length > maxHistorySize) {
        history.value.shift();
        historyIndex.value--;
    }
};

const undo = () => {
    if (canUndo.value) {
        historyIndex.value--;
        const state = history.value[historyIndex.value];
        canvas.loadFromJSON(state, () => {
            canvas.renderAll();
            updateLayers();
            updateSelectedObject();
        });
        showNotification('تم التراجع', 'success');
    }
};

const redo = () => {
    if (canRedo.value) {
        historyIndex.value++;
        const state = history.value[historyIndex.value];
        canvas.loadFromJSON(state, () => {
            canvas.renderAll();
            updateLayers();
            updateSelectedObject();
        });
        showNotification('تم الإعادة', 'success');
    }
};

// Update selected object after undo/redo
const updateSelectedObject = () => {
    const activeObject = canvas.getActiveObject();
    if (activeObject) {
        selectedObject.value = activeObject;
        updateObjectProperties();
    } else {
        selectedObject.value = null;
    }
};

// Auto-save functionality
const autoSave = async () => {
    if (!autoSaveEnabled.value || saving.value || !canvas) return;

    try {
        const designData = canvas.toJSON();

        // Get CSRF token safely
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (!csrfToken) {
            console.warn('CSRF token not found, skipping auto-save');
            return;
        }

        const response = await fetch(route('admin.templates.design.save', props.template.id), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                design_data: designData
            })
        });

        const result = await response.json();

        if (result.success) {
            lastSaved.value = new Date();
        }
    } catch (error) {
        console.error('Auto-save failed:', error);
    }
};

// Start auto-save
const startAutoSave = () => {
    if (autoSaveInterval.value) {
        clearInterval(autoSaveInterval.value);
    }

    autoSaveInterval.value = setInterval(autoSave, 30000); // Auto-save every 30 seconds
};

// Stop auto-save
const stopAutoSave = () => {
    if (autoSaveInterval.value) {
        clearInterval(autoSaveInterval.value);
        autoSaveInterval.value = null;
    }
};

// Toggle auto-save
const toggleAutoSave = () => {
    autoSaveEnabled.value = !autoSaveEnabled.value;

    if (autoSaveEnabled.value) {
        startAutoSave();
        showNotification('تم تفعيل الحفظ التلقائي', 'success');
    } else {
        stopAutoSave();
        showNotification('تم إيقاف الحفظ التلقائي', 'success');
    }
};

// Save design
const saveDesign = async () => {
    if (!canvas) {
        showNotification('المحرر غير جاهز بعد', 'error');
        return;
    }

    saving.value = true;

    try {
        const designData = canvas.toJSON();

        // Get CSRF token safely
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (!csrfToken) {
            showNotification('خطأ في الأمان، يرجى إعادة تحميل الصفحة', 'error');
            saving.value = false;
            return;
        }

        const response = await fetch(route('admin.templates.design.save', props.template.id), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                design_data: designData
            })
        });

        const result = await response.json();

        if (result.success) {
            showNotification('تم حفظ التصميم بنجاح', 'success');
        } else {
            showNotification('حدث خطأ أثناء حفظ التصميم', 'error');
        }
    } catch (error) {
        console.error('Error saving design:', error);
        showNotification('حدث خطأ أثناء حفظ التصميم', 'error');
    } finally {
        saving.value = false;
    }
};

// Preview design
const previewDesign = () => {
    const dataURL = canvas.toDataURL({
        format: 'png',
        quality: 1
    });

    const newWindow = window.open();
    newWindow.document.write(`
        <html>
            <head><title>معاينة التصميم</title></head>
            <body style="margin:0; padding:20px; background:#f5f5f5; display:flex; justify-content:center; align-items:center; min-height:100vh;">
                <img src="${dataURL}" style="max-width:100%; max-height:100%; box-shadow:0 4px 6px rgba(0,0,0,0.1);" />
            </body>
        </html>
    `);
    showExportMenu.value = false;
};

// Export as image
const exportAsImage = (format) => {
    const dataURL = canvas.toDataURL({
        format: format,
        quality: 1
    });

    const link = document.createElement('a');
    link.download = `${props.template.name}-design.${format}`;
    link.href = dataURL;
    link.click();
    showExportMenu.value = false;
};

// Export as JSON
const exportAsJSON = () => {
    const designData = canvas.toJSON();
    const dataStr = JSON.stringify(designData, null, 2);
    const dataBlob = new Blob([dataStr], { type: 'application/json' });

    const link = document.createElement('a');
    link.download = `${props.template.name}-design.json`;
    link.href = URL.createObjectURL(dataBlob);
    link.click();
    showExportMenu.value = false;
};

// Import JSON
const importJSON = () => {
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = '.json';
    input.onchange = (e) => {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (event) => {
                try {
                    const designData = JSON.parse(event.target.result);
                    canvas.loadFromJSON(designData, () => {
                        canvas.renderAll();
                        updateLayers();
                        // Update canvas properties
                        canvasProperties.width = canvas.width;
                        canvasProperties.height = canvas.height;
                        canvasProperties.backgroundColor = canvas.backgroundColor || '#ffffff';
                        showNotification('تم استيراد التصميم بنجاح', 'success');
                    });
                } catch (error) {
                    showNotification('خطأ في قراءة الملف. تأكد من أن الملف صحيح.', 'error');
                }
            };
            reader.readAsText(file);
        }
    };
    input.click();
};

// Show notification
const showNotification = (message, type = 'success') => {
    notification.message = message;
    notification.type = type;
    notification.show = true;

    setTimeout(() => {
        notification.show = false;
    }, 3000);
};

// Show loading
const showLoading = (message) => {
    loading.value = true;
    loadingMessage.value = message;
};

// Hide loading
const hideLoading = () => {
    loading.value = false;
    loadingMessage.value = '';
};

// Format time for display
const formatTime = (date) => {
    return date.toLocaleTimeString('ar-SA', {
        hour: '2-digit',
        minute: '2-digit'
    });
};

// Keyboard shortcuts
const handleKeyDown = (event) => {
    // Ctrl+S or Cmd+S for save
    if ((event.ctrlKey || event.metaKey) && event.key === 's') {
        event.preventDefault();
        saveDesign();
    }

    // Delete key for deleting selected object
    if (event.key === 'Delete' && selectedObject.value) {
        deleteSelectedObject();
    }

    // Ctrl+Z or Cmd+Z for undo
    if ((event.ctrlKey || event.metaKey) && event.key === 'z' && !event.shiftKey) {
        event.preventDefault();
        undo();
    }

    // Ctrl+Y or Cmd+Shift+Z for redo
    if (((event.ctrlKey || event.metaKey) && event.key === 'y') ||
        ((event.ctrlKey || event.metaKey) && event.shiftKey && event.key === 'z')) {
        event.preventDefault();
        redo();
    }

    // Ctrl+D or Cmd+D for duplicate
    if ((event.ctrlKey || event.metaKey) && event.key === 'd' && selectedObject.value) {
        event.preventDefault();
        const layer = layers.value.find(l => l.object === selectedObject.value);
        if (layer) {
            duplicateLayer(layer);
        }
    }
};

// Close menus when clicking outside
const closeMenus = (event) => {
    if (!event.target.closest('.relative')) {
        showExportMenu.value = false;
        showTemplatesMenu.value = false;
    }
};

// Initialize on mount
onMounted(() => {
    nextTick(() => {
        initCanvas();
        document.addEventListener('click', closeMenus);
        document.addEventListener('keydown', handleKeyDown);

        // Start auto-save if enabled
        if (autoSaveEnabled.value) {
            startAutoSave();
        }
    });
});

// Cleanup on unmount
onUnmounted(() => {
    document.removeEventListener('click', closeMenus);
    document.removeEventListener('keydown', handleKeyDown);
    stopAutoSave();
});
</script>

<style scoped>
/* Custom styles for the design editor */
.design-editor {
    height: calc(100vh - 200px);
}

/* RTL support for canvas */
canvas {
    direction: ltr;
}

/* Fabric.js canvas container */
.canvas-container {
    margin: 0 auto !important;
}

/* Toolbar button hover effects */
.toolbar-button {
    transition: all 0.2s ease;
}

.toolbar-button:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

/* Layer item drag effects */
.layer-item {
    transition: all 0.2s ease;
}

.layer-item:hover {
    transform: translateX(2px);
}

.layer-item.dragging {
    opacity: 0.5;
    transform: rotate(2deg);
}

/* Custom scrollbar for properties panel */
.properties-panel::-webkit-scrollbar {
    width: 6px;
}

.properties-panel::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.properties-panel::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.properties-panel::-webkit-scrollbar-thumb:hover {
    background: #a1a1a1;
}

/* Animation for notifications */
@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.notification-enter-active {
    animation: slideInRight 0.3s ease;
}

.notification-leave-active {
    animation: slideInRight 0.3s ease reverse;
}

/* Loading spinner animation */
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.loading-spinner {
    animation: spin 1s linear infinite;
}

/* Canvas grid background (optional) */
.canvas-grid {
    background-image:
        linear-gradient(rgba(0,0,0,.1) 1px, transparent 1px),
        linear-gradient(90deg, rgba(0,0,0,.1) 1px, transparent 1px);
    background-size: 20px 20px;
}

/* Arabic font support */
.arabic-text {
    font-family: 'Cairo', 'Amiri', 'Noto Sans Arabic', Arial, sans-serif;
    direction: rtl;
    text-align: right;
}

/* Tool icons */
.tool-icon {
    transition: transform 0.2s ease;
}

.tool-icon:hover {
    transform: scale(1.1);
}

/* Property input focus styles */
.property-input:focus {
    border-color: #8B5CF6;
    box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
}

/* Export menu animation */
.export-menu {
    animation: fadeInDown 0.2s ease;
}

@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
