<template>
    <Head title="محرر التصميم الاحترافي - GrapesJS Pro">
        <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&family=Amiri:wght@400;700&family=Noto+Sans+Arabic:wght@300;400;600;700&display=swap" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    </Head>

    <div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900" dir="rtl">
        <!-- Professional Header with Glass Effect -->
        <div class="relative bg-white/10 backdrop-blur-md border-b border-white/20 shadow-2xl">
            <div class="absolute inset-0 bg-gradient-to-r from-purple-600/20 to-blue-600/20"></div>
            <div class="relative max-w-7xl mx-auto px-6 py-4">
                <div class="flex justify-between items-center">
                    <!-- Left Side - Template Info -->
                    <div class="text-white">
                        <div class="flex items-center space-x-3 space-x-reverse mb-2">
                            <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-blue-500 rounded-xl flex items-center justify-center shadow-lg">
                                <i class="fas fa-palette text-white text-xl"></i>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold bg-gradient-to-r from-white to-purple-200 bg-clip-text text-transparent">
                                    محرر التصميم الاحترافي
                                </h1>
                                <p class="text-purple-200 text-sm">{{ template.name }}</p>
                            </div>
                        </div>

                        <!-- Status Indicators -->
                        <div class="flex items-center space-x-4 space-x-reverse text-sm">
                            <div class="flex items-center space-x-2 space-x-reverse">
                                <div :class="[
                                    'w-2 h-2 rounded-full animate-pulse',
                                    autoSaveEnabled ? 'bg-green-400' : 'bg-red-400'
                                ]"></div>
                                <span class="text-white/80">
                                    {{ autoSaveEnabled ? 'حفظ تلقائي' : 'حفظ يدوي' }}
                                </span>
                            </div>
                            <div v-if="lastSaved" class="text-white/60">
                                آخر حفظ: {{ formatTime(lastSaved) }}
                            </div>
                            <div class="flex items-center space-x-2 space-x-reverse">
                                <div class="w-2 h-2 bg-blue-400 rounded-full animate-pulse"></div>
                                <span class="text-white/80">متصل</span>
                            </div>
                        </div>
                    </div>

                    <!-- Professional Toolbar - FIXED AND VISIBLE -->
                    <div class="flex items-center space-x-4 space-x-reverse">
                        <!-- History Controls -->
                        <div class="flex items-center bg-white/20 backdrop-blur-sm rounded-xl p-1 border border-white/30 shadow-lg">
                            <button
                                @click="undo"
                                :disabled="!canUndo"
                                class="p-3 rounded-lg transition-all duration-200 hover:bg-white/30 disabled:opacity-50 disabled:cursor-not-allowed group"
                                title="تراجع (Ctrl+Z)"
                            >
                                <i class="fas fa-undo text-white text-lg group-hover:scale-110 transition-transform"></i>
                            </button>
                            <button
                                @click="redo"
                                :disabled="!canRedo"
                                class="p-3 rounded-lg transition-all duration-200 hover:bg-white/30 disabled:opacity-50 disabled:cursor-not-allowed group"
                                title="إعادة (Ctrl+Y)"
                            >
                                <i class="fas fa-redo text-white text-lg group-hover:scale-110 transition-transform"></i>
                            </button>
                        </div>

                        <!-- Device Preview -->
                        <div class="flex items-center bg-white/20 backdrop-blur-sm rounded-xl p-1 border border-white/30 shadow-lg">
                            <button
                                @click="setDevice('desktop')"
                                :class="[
                                    'p-3 rounded-lg transition-all duration-200 group',
                                    currentDevice === 'desktop' ? 'bg-white/40 text-white shadow-md' : 'text-white/80 hover:bg-white/30'
                                ]"
                                title="عرض سطح المكتب"
                            >
                                <i class="fas fa-desktop text-lg group-hover:scale-110 transition-transform"></i>
                            </button>
                            <button
                                @click="setDevice('tablet')"
                                :class="[
                                    'p-3 rounded-lg transition-all duration-200 group',
                                    currentDevice === 'tablet' ? 'bg-white/40 text-white shadow-md' : 'text-white/80 hover:bg-white/30'
                                ]"
                                title="عرض التابلت"
                            >
                                <i class="fas fa-tablet-alt text-lg group-hover:scale-110 transition-transform"></i>
                            </button>
                            <button
                                @click="setDevice('mobile')"
                                :class="[
                                    'p-3 rounded-lg transition-all duration-200 group',
                                    currentDevice === 'mobile' ? 'bg-white/40 text-white shadow-md' : 'text-white/80 hover:bg-white/30'
                                ]"
                                title="عرض الموبايل"
                            >
                                <i class="fas fa-mobile-alt text-lg group-hover:scale-110 transition-transform"></i>
                            </button>
                        </div>

                        <!-- Main Actions - ENHANCED VISIBILITY -->
                        <div class="flex items-center space-x-3 space-x-reverse">
                            <button
                                @click="saveDesign"
                                :disabled="saving"
                                class="bg-gradient-to-r from-green-500 to-emerald-600 text-white px-8 py-4 rounded-xl font-bold hover:from-green-600 hover:to-emerald-700 transition-all duration-200 disabled:opacity-50 shadow-xl hover:shadow-2xl transform hover:-translate-y-1 hover:scale-105 flex items-center space-x-3 space-x-reverse text-lg"
                            >
                                <i :class="saving ? 'fas fa-spinner fa-spin text-xl' : 'fas fa-save text-xl'"></i>
                                <span>{{ saving ? 'جاري الحفظ...' : 'حفظ التصميم' }}</span>
                            </button>

                            <button
                                @click="previewDesign"
                                class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white px-8 py-4 rounded-xl font-bold hover:from-blue-600 hover:to-indigo-700 transition-all duration-200 shadow-xl hover:shadow-2xl transform hover:-translate-y-1 hover:scale-105 flex items-center space-x-3 space-x-reverse text-lg"
                            >
                                <i class="fas fa-eye text-xl"></i>
                                <span>معاينة</span>
                            </button>

                            <!-- Advanced Dropdown Menu - PROPERLY POSITIONED -->
                            <div class="relative" style="z-index: 9999;">
                                <button
                                    @click="showAdvancedMenu = !showAdvancedMenu"
                                    class="bg-gradient-to-r from-purple-500 to-pink-600 text-white px-8 py-4 rounded-xl font-bold hover:from-purple-600 hover:to-pink-700 transition-all duration-200 shadow-xl hover:shadow-2xl transform hover:-translate-y-1 hover:scale-105 flex items-center space-x-3 space-x-reverse text-lg"
                                >
                                    <i class="fas fa-magic text-xl"></i>
                                    <span>أدوات متقدمة</span>
                                    <i :class="['fas fa-chevron-down text-sm transition-transform duration-200', showAdvancedMenu ? 'rotate-180' : '']"></i>
                                </button>

                                <div v-if="showAdvancedMenu" class="absolute left-0 top-full mt-2 w-96 bg-white rounded-3xl shadow-2xl border-2 border-purple-200 overflow-hidden animate__animated animate__fadeInUp animate__faster" style="z-index: 10000; max-height: 80vh; overflow-y: auto;">
                                    <!-- Header -->
                                    <div class="bg-gradient-to-r from-purple-600 to-pink-600 p-4 text-white">
                                        <div class="flex items-center justify-between">
                                            <h3 class="text-lg font-bold flex items-center">
                                                <i class="fas fa-magic ml-3 text-xl"></i>
                                                الأدوات المتقدمة
                                            </h3>
                                            <button
                                                @click="showAdvancedMenu = false"
                                                class="text-white/80 hover:text-white transition-colors p-2 rounded-lg hover:bg-white/20"
                                            >
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Export Section -->
                                    <div class="p-6 border-b border-gray-100">
                                        <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                            <i class="fas fa-download text-blue-500 ml-3 text-lg"></i>
                                            تصدير التصميم
                                        </h4>
                                        <div class="grid grid-cols-2 gap-3">
                                            <button
                                                @click="exportAsHTML"
                                                class="flex items-center justify-center space-x-2 space-x-reverse p-4 bg-gradient-to-r from-orange-100 to-orange-200 hover:from-orange-200 hover:to-orange-300 rounded-xl transition-all duration-200 text-orange-700 font-semibold shadow-md hover:shadow-lg transform hover:-translate-y-1"
                                            >
                                                <i class="fab fa-html5 text-lg"></i>
                                                <span>HTML</span>
                                            </button>
                                            <button
                                                @click="exportAsJSON"
                                                class="flex items-center justify-center space-x-2 space-x-reverse p-4 bg-gradient-to-r from-blue-100 to-blue-200 hover:from-blue-200 hover:to-blue-300 rounded-xl transition-all duration-200 text-blue-700 font-semibold shadow-md hover:shadow-lg transform hover:-translate-y-1"
                                            >
                                                <i class="fas fa-code text-lg"></i>
                                                <span>JSON</span>
                                            </button>
                                            <button
                                                @click="exportAsPDF"
                                                class="flex items-center justify-center space-x-2 space-x-reverse p-4 bg-gradient-to-r from-red-100 to-red-200 hover:from-red-200 hover:to-red-300 rounded-xl transition-all duration-200 text-red-700 font-semibold shadow-md hover:shadow-lg transform hover:-translate-y-1"
                                            >
                                                <i class="fas fa-file-pdf text-lg"></i>
                                                <span>PDF</span>
                                            </button>
                                            <button
                                                @click="exportAsImage"
                                                class="flex items-center justify-center space-x-2 space-x-reverse p-4 bg-gradient-to-r from-green-100 to-green-200 hover:from-green-200 hover:to-green-300 rounded-xl transition-all duration-200 text-green-700 font-semibold shadow-md hover:shadow-lg transform hover:-translate-y-1"
                                            >
                                                <i class="fas fa-image text-lg"></i>
                                                <span>صورة</span>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Templates Section -->
                                    <div class="p-6 border-b border-gray-100">
                                        <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                            <i class="fas fa-layer-group text-purple-500 ml-3 text-lg"></i>
                                            قوالب احترافية
                                        </h4>
                                        <div class="space-y-3">
                                            <button
                                                @click="loadTemplate('wedding')"
                                                class="w-full flex items-center space-x-4 space-x-reverse p-4 bg-gradient-to-r from-pink-50 to-rose-50 hover:from-pink-100 hover:to-rose-100 rounded-xl transition-all duration-200 text-right border border-pink-200 hover:border-pink-300 shadow-sm hover:shadow-md transform hover:-translate-y-1"
                                            >
                                                <div class="w-12 h-12 bg-gradient-to-r from-pink-500 to-rose-500 rounded-xl flex items-center justify-center shadow-lg">
                                                    <i class="fas fa-heart text-white text-lg"></i>
                                                </div>
                                                <div class="flex-1">
                                                    <div class="text-base font-bold text-gray-800">بطاقة زواج</div>
                                                    <div class="text-sm text-gray-600">تصميم رومانسي أنيق مع زخارف ذهبية</div>
                                                </div>
                                            </button>
                                            <button
                                                @click="loadTemplate('ramadan')"
                                                class="w-full flex items-center space-x-4 space-x-reverse p-4 bg-gradient-to-r from-green-50 to-emerald-50 hover:from-green-100 hover:to-emerald-100 rounded-xl transition-all duration-200 text-right border border-green-200 hover:border-green-300 shadow-sm hover:shadow-md transform hover:-translate-y-1"
                                            >
                                                <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-500 rounded-xl flex items-center justify-center shadow-lg">
                                                    <i class="fas fa-moon text-white text-lg"></i>
                                                </div>
                                                <div class="flex-1">
                                                    <div class="text-base font-bold text-gray-800">بطاقة رمضان</div>
                                                    <div class="text-sm text-gray-600">تصميم إسلامي مبارك بألوان دافئة</div>
                                                </div>
                                            </button>
                                            <button
                                                @click="loadTemplate('business')"
                                                class="w-full flex items-center space-x-4 space-x-reverse p-4 bg-gradient-to-r from-blue-50 to-indigo-50 hover:from-blue-100 hover:to-indigo-100 rounded-xl transition-all duration-200 text-right border border-blue-200 hover:border-blue-300 shadow-sm hover:shadow-md transform hover:-translate-y-1"
                                            >
                                                <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-xl flex items-center justify-center shadow-lg">
                                                    <i class="fas fa-briefcase text-white text-lg"></i>
                                                </div>
                                                <div class="flex-1">
                                                    <div class="text-base font-bold text-gray-800">بطاقة عمل</div>
                                                    <div class="text-sm text-gray-600">تصميم مهني احترافي للشركات</div>
                                                </div>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- AI & Import Section -->
                                    <div class="p-6">
                                        <div class="space-y-4">
                                            <!-- AI Button -->
                                            <button
                                                @click="showAIModal = true"
                                                class="w-full flex items-center justify-center space-x-3 space-x-reverse p-4 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 rounded-xl transition-all duration-200 text-white font-bold shadow-lg hover:shadow-xl transform hover:-translate-y-1"
                                            >
                                                <i class="fas fa-robot text-xl"></i>
                                                <span>مساعد الذكاء الاصطناعي</span>
                                            </button>

                                            <!-- Import Button -->
                                            <button
                                                @click="importJSON"
                                                class="w-full flex items-center justify-center space-x-3 space-x-reverse p-4 bg-gradient-to-r from-indigo-500 to-blue-500 hover:from-indigo-600 hover:to-blue-600 rounded-xl transition-all duration-200 text-white font-bold shadow-lg hover:shadow-xl transform hover:-translate-y-1"
                                            >
                                                <i class="fas fa-file-import text-xl"></i>
                                                <span>استيراد تصميم</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Editor Interface -->
        <div class="flex h-screen">
            <!-- Left Sidebar - Tools & Blocks -->
            <div class="w-80 bg-white/95 backdrop-blur-md border-l border-white/20 shadow-2xl overflow-hidden">
                <!-- Sidebar Header -->
                <div class="bg-gradient-to-r from-slate-800 to-slate-900 p-4 border-b border-slate-700">
                    <div class="flex items-center justify-between">
                        <h2 class="text-white font-bold text-lg">أدوات التصميم</h2>
                        <button
                            @click="toggleSidebar"
                            class="text-white/70 hover:text-white transition-colors p-2 rounded-lg hover:bg-white/10"
                        >
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <!-- Sidebar Tabs -->
                <div class="flex border-b border-gray-200 bg-gray-50">
                    <button
                        v-for="tab in sidebarTabs"
                        :key="tab.id"
                        @click="switchToTab(tab.id)"
                        :class="[
                            'flex-1 py-3 px-4 text-sm font-medium transition-all duration-200 flex items-center justify-center space-x-2 space-x-reverse',
                            activeSidebarTab === tab.id
                                ? 'bg-white text-blue-600 border-b-2 border-blue-600 shadow-sm'
                                : 'text-gray-600 hover:text-gray-800 hover:bg-gray-100'
                        ]"
                    >
                        <i :class="tab.icon"></i>
                        <span>{{ tab.name }}</span>
                    </button>
                </div>

                <!-- Sidebar Content -->
                <div class="flex-1 overflow-y-auto">
                    <!-- Blocks Tab -->
                    <div v-if="activeSidebarTab === 'blocks'" class="p-4">
                        <div class="blocks-container">
                            <!-- Blocks will be populated by GrapesJS -->
                        </div>
                    </div>

                    <!-- Layers Tab -->
                    <div v-if="activeSidebarTab === 'layers'" class="p-4">
                        <div v-if="!editor" class="text-center py-8 text-gray-500">
                            <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                            <p>جاري تحميل الطبقات...</p>
                        </div>
                        <div class="layers-container">
                            <!-- Layers will be populated by GrapesJS -->
                        </div>
                    </div>

                    <!-- Styles Tab -->
                    <div v-if="activeSidebarTab === 'styles'" class="p-4">
                        <div v-if="!editor" class="text-center py-8 text-gray-500">
                            <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                            <p>جاري تحميل الأنماط...</p>
                        </div>
                        <div class="styles-container">
                            <!-- Styles will be populated by GrapesJS -->
                        </div>
                    </div>

                    <!-- Properties Tab -->
                    <div v-if="activeSidebarTab === 'properties'" class="p-4">
                        <div v-if="!editor" class="text-center py-8 text-gray-500">
                            <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                            <p>جاري تحميل الخصائص...</p>
                        </div>

                        <!-- CANVA-LIKE POSITIONING TOOLS -->
                        <div class="mb-6 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-200">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-arrows-alt text-blue-500 ml-2"></i>
                                أدوات الموضع
                            </h3>

                            <div class="grid grid-cols-2 gap-2 mb-4">
                                <button
                                    @click="runCommand('enable-absolute-positioning')"
                                    class="flex items-center justify-center p-3 bg-blue-100 hover:bg-blue-200 rounded-lg transition-colors text-blue-700 text-sm font-medium"
                                    title="تفعيل الموضع المطلق"
                                >
                                    <i class="fas fa-expand-arrows-alt"></i>
                                    <span class="mr-2">موضع مطلق</span>
                                </button>

                                <button
                                    @click="runCommand('center-element')"
                                    class="flex items-center justify-center p-3 bg-green-100 hover:bg-green-200 rounded-lg transition-colors text-green-700 text-sm font-medium"
                                    title="توسيط العنصر"
                                >
                                    <i class="fas fa-crosshairs"></i>
                                    <span class="mr-2">توسيط</span>
                                </button>

                                <button
                                    @click="runCommand('bring-to-front')"
                                    class="flex items-center justify-center p-3 bg-purple-100 hover:bg-purple-200 rounded-lg transition-colors text-purple-700 text-sm font-medium"
                                    title="إحضار للأمام"
                                >
                                    <i class="fas fa-arrow-up"></i>
                                    <span class="mr-2">للأمام</span>
                                </button>

                                <button
                                    @click="runCommand('send-to-back')"
                                    class="flex items-center justify-center p-3 bg-orange-100 hover:bg-orange-200 rounded-lg transition-colors text-orange-700 text-sm font-medium"
                                    title="إرسال للخلف"
                                >
                                    <i class="fas fa-arrow-down"></i>
                                    <span class="mr-2">للخلف</span>
                                </button>
                            </div>

                            <!-- Quick Position Presets -->
                            <div class="mb-4">
                                <h4 class="text-sm font-semibold text-gray-700 mb-2">مواضع سريعة</h4>
                                <div class="grid grid-cols-3 gap-1">
                                    <button
                                        @click="setQuickPosition('top-left')"
                                        class="p-2 bg-gray-100 hover:bg-gray-200 rounded text-xs"
                                        title="أعلى يسار"
                                    >
                                        <i class="fas fa-arrow-up"></i>
                                        <i class="fas fa-arrow-left"></i>
                                    </button>
                                    <button
                                        @click="setQuickPosition('top-center')"
                                        class="p-2 bg-gray-100 hover:bg-gray-200 rounded text-xs"
                                        title="أعلى وسط"
                                    >
                                        <i class="fas fa-arrow-up"></i>
                                    </button>
                                    <button
                                        @click="setQuickPosition('top-right')"
                                        class="p-2 bg-gray-100 hover:bg-gray-200 rounded text-xs"
                                        title="أعلى يمين"
                                    >
                                        <i class="fas fa-arrow-up"></i>
                                        <i class="fas fa-arrow-right"></i>
                                    </button>
                                    <button
                                        @click="setQuickPosition('center-left')"
                                        class="p-2 bg-gray-100 hover:bg-gray-200 rounded text-xs"
                                        title="وسط يسار"
                                    >
                                        <i class="fas fa-arrow-left"></i>
                                    </button>
                                    <button
                                        @click="setQuickPosition('center')"
                                        class="p-2 bg-blue-100 hover:bg-blue-200 rounded text-xs"
                                        title="وسط"
                                    >
                                        <i class="fas fa-crosshairs"></i>
                                    </button>
                                    <button
                                        @click="setQuickPosition('center-right')"
                                        class="p-2 bg-gray-100 hover:bg-gray-200 rounded text-xs"
                                        title="وسط يمين"
                                    >
                                        <i class="fas fa-arrow-right"></i>
                                    </button>
                                    <button
                                        @click="setQuickPosition('bottom-left')"
                                        class="p-2 bg-gray-100 hover:bg-gray-200 rounded text-xs"
                                        title="أسفل يسار"
                                    >
                                        <i class="fas fa-arrow-down"></i>
                                        <i class="fas fa-arrow-left"></i>
                                    </button>
                                    <button
                                        @click="setQuickPosition('bottom-center')"
                                        class="p-2 bg-gray-100 hover:bg-gray-200 rounded text-xs"
                                        title="أسفل وسط"
                                    >
                                        <i class="fas fa-arrow-down"></i>
                                    </button>
                                    <button
                                        @click="setQuickPosition('bottom-right')"
                                        class="p-2 bg-gray-100 hover:bg-gray-200 rounded text-xs"
                                        title="أسفل يمين"
                                    >
                                        <i class="fas fa-arrow-down"></i>
                                        <i class="fas fa-arrow-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="traits-container">
                            <!-- Properties will be populated by GrapesJS -->
                        </div>
                    </div>

                    <!-- AI Assistant Tab -->
                    <div v-if="activeSidebarTab === 'ai'" class="p-4">
                        <div class="space-y-4">
                            <div class="bg-gradient-to-r from-purple-100 to-blue-100 p-4 rounded-xl border border-purple-200">
                                <h3 class="font-bold text-gray-800 mb-2 flex items-center">
                                    <i class="fas fa-robot text-purple-600 ml-2"></i>
                                    مساعد الذكاء الاصطناعي
                                </h3>
                                <p class="text-sm text-gray-600 mb-3">اطلب من المساعد إنشاء تصميم أو تحسين العناصر</p>
                                <textarea
                                    v-model="aiPrompt"
                                    placeholder="مثال: أنشئ بطاقة زواج باللون الوردي مع زخارف ذهبية..."
                                    class="w-full p-3 border border-gray-300 rounded-lg text-sm resize-none"
                                    rows="3"
                                ></textarea>
                                <button
                                    @click="generateWithAI"
                                    :disabled="!aiPrompt.trim() || aiGenerating"
                                    class="w-full mt-3 bg-gradient-to-r from-purple-500 to-blue-500 text-white py-2 px-4 rounded-lg font-medium hover:from-purple-600 hover:to-blue-600 transition-all duration-200 disabled:opacity-50 flex items-center justify-center space-x-2 space-x-reverse"
                                >
                                    <i :class="aiGenerating ? 'fas fa-spinner fa-spin' : 'fas fa-magic'"></i>
                                    <span>{{ aiGenerating ? 'جاري الإنشاء...' : 'إنشاء بالذكاء الاصطناعي' }}</span>
                                </button>
                            </div>

                            <!-- Quick AI Templates -->
                            <div class="space-y-2">
                                <h4 class="font-semibold text-gray-800 text-sm">قوالب سريعة بالذكاء الاصطناعي</h4>
                                <button
                                    v-for="template in aiQuickTemplates"
                                    :key="template.id"
                                    @click="generateQuickAI(template.prompt)"
                                    class="w-full text-right p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors border border-gray-200 text-sm"
                                >
                                    <div class="flex items-center space-x-3 space-x-reverse">
                                        <div :class="['w-8 h-8 rounded-lg flex items-center justify-center', template.color]">
                                            <i :class="[template.icon, 'text-white text-xs']"></i>
                                        </div>
                                        <div class="flex-1">
                                            <div class="font-medium text-gray-800">{{ template.name }}</div>
                                            <div class="text-xs text-gray-500">{{ template.description }}</div>
                                        </div>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Canvas Area -->
            <div class="flex-1 relative bg-gray-100">
                <!-- Canvas Toolbar -->
                <div class="absolute top-4 right-4 z-20 flex items-center space-x-2 space-x-reverse">
                    <!-- Zoom Controls -->
                    <div class="bg-white/90 backdrop-blur-sm rounded-xl p-2 shadow-lg border border-white/20 flex items-center space-x-1 space-x-reverse">
                        <button
                            @click="zoomOut"
                            class="p-2 hover:bg-gray-100 rounded-lg transition-colors"
                            title="تصغير"
                        >
                            <i class="fas fa-search-minus text-gray-600"></i>
                        </button>
                        <span class="text-sm font-medium text-gray-700 px-2">{{ Math.round(zoomLevel * 100) }}%</span>
                        <button
                            @click="zoomIn"
                            class="p-2 hover:bg-gray-100 rounded-lg transition-colors"
                            title="تكبير"
                        >
                            <i class="fas fa-search-plus text-gray-600"></i>
                        </button>
                        <button
                            @click="resetZoom"
                            class="p-2 hover:bg-gray-100 rounded-lg transition-colors"
                            title="إعادة تعيين"
                        >
                            <i class="fas fa-expand-arrows-alt text-gray-600"></i>
                        </button>
                    </div>

                    <!-- Grid Toggle -->
                    <button
                        @click="toggleGrid"
                        :class="[
                            'p-3 rounded-xl transition-all duration-200 shadow-lg border border-white/20',
                            showGrid ? 'bg-blue-500 text-white' : 'bg-white/90 text-gray-600 hover:bg-gray-100'
                        ]"
                        title="إظهار/إخفاء الشبكة"
                    >
                        <i class="fas fa-th"></i>
                    </button>

                    <!-- Rulers Toggle -->
                    <button
                        @click="toggleRulers"
                        :class="[
                            'p-3 rounded-xl transition-all duration-200 shadow-lg border border-white/20',
                            showRulers ? 'bg-green-500 text-white' : 'bg-white/90 text-gray-600 hover:bg-gray-100'
                        ]"
                        title="إظهار/إخفاء المساطر"
                    >
                        <i class="fas fa-ruler-combined"></i>
                    </button>

                    <!-- Snap to Grid Toggle -->
                    <button
                        @click="toggleSnapToGrid"
                        :class="[
                            'p-3 rounded-xl transition-all duration-200 shadow-lg border border-white/20',
                            snapToGrid ? 'bg-purple-500 text-white' : 'bg-white/90 text-gray-600 hover:bg-gray-100'
                        ]"
                        title="المحاذاة للشبكة"
                    >
                        <i class="fas fa-magnet"></i>
                    </button>

                    <!-- Position Guide Toggle -->
                    <button
                        @click="togglePositionGuide"
                        :class="[
                            'p-3 rounded-xl transition-all duration-200 shadow-lg border border-white/20',
                            showPositionGuide ? 'bg-orange-500 text-white' : 'bg-white/90 text-gray-600 hover:bg-gray-100'
                        ]"
                        title="دليل الموضع"
                    >
                        <i class="fas fa-crosshairs"></i>
                    </button>

                    <!-- Debug Button -->
                    <button
                        @click="debugPanels"
                        class="p-3 rounded-xl transition-all duration-200 shadow-lg border border-white/20 bg-red-500 text-white hover:bg-red-600"
                        title="تشخيص المشاكل"
                    >
                        <i class="fas fa-bug"></i>
                    </button>
                </div>

                <!-- GrapesJS Canvas -->
                <div id="gjs" class="w-full h-full"></div>
            </div>
        </div>

        <!-- Professional Notifications -->
        <div class="fixed top-4 left-4 z-50 space-y-2">
            <div
                v-for="notif in notifications"
                :key="notif.id"
                :class="[
                    'px-6 py-4 rounded-xl shadow-2xl text-white font-medium transform transition-all duration-300 backdrop-blur-sm border border-white/20 animate__animated animate__slideInLeft',
                    notif.type === 'success' ? 'bg-gradient-to-r from-green-500 to-emerald-600' :
                    notif.type === 'error' ? 'bg-gradient-to-r from-red-500 to-pink-600' :
                    notif.type === 'warning' ? 'bg-gradient-to-r from-yellow-500 to-orange-600' :
                    'bg-gradient-to-r from-blue-500 to-indigo-600'
                ]"
            >
                <div class="flex items-center space-x-3 space-x-reverse">
                    <i :class="[
                        notif.type === 'success' ? 'fas fa-check-circle' :
                        notif.type === 'error' ? 'fas fa-exclamation-circle' :
                        notif.type === 'warning' ? 'fas fa-exclamation-triangle' :
                        'fas fa-info-circle'
                    ]"></i>
                    <span>{{ notif.message }}</span>
                    <button
                        @click="removeNotification(notif.id)"
                        class="text-white/70 hover:text-white transition-colors"
                    >
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Professional Loading Overlay -->
        <div v-if="loading" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
            <div class="bg-white/95 backdrop-blur-md rounded-2xl p-8 text-center shadow-2xl border border-white/20 max-w-md mx-4">
                <div class="relative mb-6">
                    <div class="w-16 h-16 mx-auto">
                        <div class="absolute inset-0 rounded-full border-4 border-purple-200"></div>
                        <div class="absolute inset-0 rounded-full border-4 border-purple-600 border-t-transparent animate-spin"></div>
                    </div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <i class="fas fa-palette text-purple-600 text-xl"></i>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">{{ loadingTitle || 'جاري المعالجة' }}</h3>
                <p class="text-gray-600 mb-4">{{ loadingMessage }}</p>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div
                        class="bg-gradient-to-r from-purple-500 to-blue-500 h-2 rounded-full transition-all duration-300"
                        :style="{ width: loadingProgress + '%' }"
                    ></div>
                </div>
                <p class="text-sm text-gray-500 mt-2">{{ loadingProgress }}% مكتمل</p>
            </div>
        </div>

        <!-- AI Generation Modal -->
        <div v-if="showAIModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
            <div class="bg-white rounded-2xl p-8 max-w-2xl mx-4 shadow-2xl border border-white/20">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-robot text-purple-600 ml-3"></i>
                        مساعد الذكاء الاصطناعي
                    </h2>
                    <button
                        @click="showAIModal = false"
                        class="text-gray-400 hover:text-gray-600 transition-colors"
                    >
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">وصف التصميم المطلوب</label>
                        <textarea
                            v-model="aiPrompt"
                            placeholder="اكتب وصفاً مفصلاً للتصميم الذي تريده..."
                            class="w-full p-4 border border-gray-300 rounded-xl text-sm resize-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            rows="4"
                        ></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">نوع التصميم</label>
                            <select
                                v-model="aiDesignType"
                                class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            >
                                <option value="card">بطاقة</option>
                                <option value="poster">ملصق</option>
                                <option value="banner">بانر</option>
                                <option value="invitation">دعوة</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">الألوان المفضلة</label>
                            <select
                                v-model="aiColorScheme"
                                class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            >
                                <option value="warm">ألوان دافئة</option>
                                <option value="cool">ألوان باردة</option>
                                <option value="neutral">ألوان محايدة</option>
                                <option value="vibrant">ألوان زاهية</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 space-x-reverse pt-4">
                        <button
                            @click="showAIModal = false"
                            class="px-6 py-3 text-gray-600 hover:text-gray-800 transition-colors"
                        >
                            إلغاء
                        </button>
                        <button
                            @click="generateWithAI"
                            :disabled="!aiPrompt.trim() || aiGenerating"
                            class="px-8 py-3 bg-gradient-to-r from-purple-500 to-blue-500 text-white rounded-xl font-medium hover:from-purple-600 hover:to-blue-600 transition-all duration-200 disabled:opacity-50 flex items-center space-x-2 space-x-reverse"
                        >
                            <i :class="aiGenerating ? 'fas fa-spinner fa-spin' : 'fas fa-magic'"></i>
                            <span>{{ aiGenerating ? 'جاري الإنشاء...' : 'إنشاء التصميم' }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Keyboard Shortcuts Help -->
        <div v-if="showShortcuts" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
            <div class="bg-white rounded-2xl p-8 max-w-lg mx-4 shadow-2xl border border-white/20">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-800">اختصارات لوحة المفاتيح</h2>
                    <button
                        @click="showShortcuts = false"
                        class="text-gray-400 hover:text-gray-600 transition-colors"
                    >
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="space-y-3">
                    <div v-for="shortcut in keyboardShortcuts" :key="shortcut.key" class="flex justify-between items-center py-2">
                        <span class="text-gray-700">{{ shortcut.description }}</span>
                        <kbd class="px-3 py-1 bg-gray-100 rounded-lg text-sm font-mono">{{ shortcut.key }}</kbd>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, reactive, computed, nextTick, watch } from 'vue';
import { Head } from '@inertiajs/vue3';
import grapesjs from 'grapesjs';
import 'grapesjs/dist/css/grapes.min.css';

// Props
const props = defineProps({
    template: Object,
    locale: String,
    translations: Object,
    errors: Object
});

// State Management
let editor = null;
const saving = ref(false);
const loading = ref(false);
const loadingMessage = ref('');
const loadingTitle = ref('');
const loadingProgress = ref(0);

// UI State
const showAdvancedMenu = ref(false);
const showAIModal = ref(false);
const showShortcuts = ref(false);
const activeSidebarTab = ref('blocks');
const currentDevice = ref('desktop');
const zoomLevel = ref(1);
const showGrid = ref(false);
const showRulers = ref(false);
const snapToGrid = ref(false);
const showPositionGuide = ref(false);
const showExportMenu = ref(false);

// Auto-save state
const autoSaveEnabled = ref(true);
const autoSaveInterval = ref(null);
const lastSaved = ref(null);

// Undo/Redo state
const undoManager = ref(null);
const canUndo = ref(false);
const canRedo = ref(false);

// AI Assistant
const aiPrompt = ref('');
const aiDesignType = ref('card');
const aiColorScheme = ref('warm');
const aiGenerating = ref(false);

// Professional Notification System
const notifications = ref([]);
let notificationId = 0;

// Sidebar Configuration
const sidebarTabs = ref([
    { id: 'blocks', name: 'العناصر', icon: 'fas fa-cubes' },
    { id: 'layers', name: 'الطبقات', icon: 'fas fa-layer-group' },
    { id: 'styles', name: 'الأنماط', icon: 'fas fa-paint-brush' },
    { id: 'properties', name: 'الخصائص', icon: 'fas fa-cog' },
    { id: 'ai', name: 'الذكاء الاصطناعي', icon: 'fas fa-robot' }
]);

// AI Quick Templates
const aiQuickTemplates = ref([
    {
        id: 'wedding',
        name: 'بطاقة زواج رومانسية',
        description: 'تصميم أنيق للأعراس',
        prompt: 'أنشئ بطاقة زواج أنيقة باللون الوردي والذهبي مع زخارف رومانسية وقلوب',
        icon: 'fas fa-heart',
        color: 'bg-pink-500'
    },
    {
        id: 'business',
        name: 'بطاقة عمل احترافية',
        description: 'تصميم مهني للأعمال',
        prompt: 'أنشئ بطاقة عمل احترافية بألوان زرقاء وبيضاء مع تصميم نظيف وحديث',
        icon: 'fas fa-briefcase',
        color: 'bg-blue-500'
    },
    {
        id: 'event',
        name: 'دعوة حدث',
        description: 'دعوة لمناسبة خاصة',
        prompt: 'أنشئ دعوة حدث جذابة بألوان زاهية ونصوص أنيقة',
        icon: 'fas fa-calendar',
        color: 'bg-green-500'
    }
]);

// Keyboard Shortcuts
const keyboardShortcuts = ref([
    { key: 'Ctrl + S', description: 'حفظ التصميم' },
    { key: 'Ctrl + Z', description: 'تراجع' },
    { key: 'Ctrl + Y', description: 'إعادة' },
    { key: 'Ctrl + D', description: 'نسخ العنصر' },
    { key: 'Delete', description: 'حذف العنصر' },
    { key: 'Ctrl + A', description: 'تحديد الكل' },
    { key: 'Ctrl + +', description: 'تكبير' },
    { key: 'Ctrl + -', description: 'تصغير' },
    { key: 'F11', description: 'ملء الشاشة' },
    { key: '?', description: 'إظهار الاختصارات' }
]);

// Professional Notification System
const addNotification = (message, type = 'success', duration = 5000) => {
    const id = ++notificationId;
    notifications.value.push({
        id,
        message,
        type,
        timestamp: new Date()
    });

    setTimeout(() => {
        removeNotification(id);
    }, duration);

    return id;
};

const removeNotification = (id) => {
    const index = notifications.value.findIndex(n => n.id === id);
    if (index > -1) {
        notifications.value.splice(index, 1);
    }
};

// Show/Hide loading with progress
const showLoadingWithProgress = (title, message, progress = 0) => {
    loading.value = true;
    loadingTitle.value = title;
    loadingMessage.value = message;
    loadingProgress.value = progress;
};

const updateLoadingProgress = (progress, message = null) => {
    loadingProgress.value = progress;
    if (message) loadingMessage.value = message;
};

const hideLoading = () => {
    loading.value = false;
    loadingTitle.value = '';
    loadingMessage.value = '';
    loadingProgress.value = 0;
};

// Format time for display
const formatTime = (date) => {
    return date.toLocaleTimeString('ar-SA', {
        hour: '2-digit',
        minute: '2-digit'
    });
};

// Initialize GrapesJS Editor
const initEditor = () => {
    editor = grapesjs.init({
        container: '#gjs',
        height: '100vh',
        width: 'auto',
        storageManager: false, // We'll handle storage manually

        // Panels configuration
        panels: {
            defaults: [
                {
                    id: 'basic-actions',
                    el: '.panel__basic-actions',
                    buttons: [
                        {
                            id: 'visibility',
                            active: true,
                            className: 'btn-toggle-borders',
                            label: '<i class="fa fa-clone"></i>',
                            command: 'sw-visibility',
                        },
                        {
                            id: 'export',
                            className: 'btn-open-export',
                            label: '<i class="fa fa-code"></i>',
                            command: 'export-template',
                            context: 'export-template',
                        },
                        {
                            id: 'show-json',
                            className: 'btn-show-json',
                            label: '<i class="fa fa-file-code-o"></i>',
                            context: 'show-json',
                            command(editor) {
                                editor.Modal.setTitle('Components JSON')
                                    .setContent(`<textarea style="width:100%; height: 250px;">
                                        ${JSON.stringify(editor.getComponents())}
                                    </textarea>`)
                                    .open();
                            },
                        }
                    ],
                }
            ]
        },

        // Block Manager
        blockManager: {
            appendTo: '.blocks-container',
            blocks: [
                // Basic blocks
                {
                    id: 'text',
                    label: 'نص',
                    category: 'أساسي',
                    content: '<div data-gjs-type="text" data-gjs-editable="true" style="padding: 10px; font-family: Cairo, Arial, sans-serif;">أدخل النص هنا</div>',
                },
                {
                    id: 'heading',
                    label: 'عنوان',
                    category: 'أساسي',
                    content: '<h1 data-gjs-type="text" data-gjs-editable="true" style="font-family: Cairo, Arial, sans-serif; color: #333; margin: 10px 0;">عنوان رئيسي</h1>',
                },
                {
                    id: 'paragraph',
                    label: 'فقرة',
                    category: 'أساسي',
                    content: '<p data-gjs-type="text" data-gjs-editable="true" style="font-family: Cairo, Arial, sans-serif; line-height: 1.6; margin: 10px 0;">هذه فقرة نصية يمكن تحريرها</p>',
                },
                {
                    id: 'image',
                    label: 'صورة',
                    category: 'أساسي',
                    content: '<img src="https://via.placeholder.com/300x200?text=صورة" style="max-width: 100%; height: auto;" alt="صورة">',
                },
                {
                    id: 'button',
                    label: 'زر',
                    category: 'أساسي',
                    content: '<button data-gjs-type="text" data-gjs-editable="true" style="background: #3b82f6; color: white; padding: 12px 24px; border: none; border-radius: 6px; font-family: Cairo, Arial, sans-serif; cursor: pointer;">انقر هنا</button>',
                },

                // Wedding blocks
                {
                    id: 'wedding-card',
                    label: 'بطاقة زواج',
                    category: 'مناسبات',
                    content: `
                        <div style="background: linear-gradient(135deg, #fdf2f8 0%, #fce7f3 100%); padding: 30px; text-align: center; border-radius: 15px; box-shadow: 0 8px 25px rgba(0,0,0,0.1);">
                            <div style="color: #ec4899; font-size: 40px; margin-bottom: 15px;">💕</div>
                            <h2 data-gjs-editable="true" style="color: #be185d; font-size: 28px; margin-bottom: 15px; font-family: Cairo, Arial, sans-serif;">مبارك الزواج</h2>
                            <p data-gjs-editable="true" style="color: #9d174d; font-size: 20px; margin-bottom: 20px; font-family: Cairo, Arial, sans-serif;">اسم العريس & اسم العروس</p>
                            <p data-gjs-editable="true" style="color: #7c2d12; font-size: 16px; font-family: Cairo, Arial, sans-serif;">التاريخ: __ / __ / ____</p>
                        </div>
                    `,
                },
                {
                    id: 'ramadan-card',
                    label: 'بطاقة رمضان',
                    category: 'مناسبات',
                    content: `
                        <div style="background: linear-gradient(135deg, #064e3b 0%, #065f46 100%); padding: 30px; text-align: center; border-radius: 15px; color: white;">
                            <div style="color: #fbbf24; font-size: 40px; margin-bottom: 15px;">🌙</div>
                            <h2 data-gjs-editable="true" style="color: #fbbf24; font-size: 28px; margin-bottom: 15px; font-family: Cairo, Arial, sans-serif;">رمضان مبارك</h2>
                            <p data-gjs-editable="true" style="color: #d1fae5; font-size: 18px; font-family: Cairo, Arial, sans-serif;">كل عام وأنتم بخير</p>
                        </div>
                    `,
                },
                {
                    id: 'graduation-card',
                    label: 'بطاقة تخرج',
                    category: 'مناسبات',
                    content: `
                        <div style="background: linear-gradient(135deg, #1e3a8a 0%, #3730a3 100%); padding: 30px; text-align: center; border-radius: 15px; color: white;">
                            <div style="color: #fbbf24; font-size: 40px; margin-bottom: 15px;">🎓</div>
                            <h2 data-gjs-editable="true" style="color: #fbbf24; font-size: 28px; margin-bottom: 15px; font-family: Cairo, Arial, sans-serif;">مبروك التخرج</h2>
                            <p data-gjs-editable="true" style="color: #e0e7ff; font-size: 20px; margin-bottom: 10px; font-family: Cairo, Arial, sans-serif;">اسم الخريج</p>
                            <p data-gjs-editable="true" style="color: #c7d2fe; font-size: 16px; font-family: Cairo, Arial, sans-serif;">التخصص: _____________</p>
                        </div>
                    `,
                },

                // Layout blocks
                {
                    id: 'container',
                    label: 'حاوي',
                    category: 'تخطيط',
                    content: '<div style="padding: 20px; border: 2px dashed #ccc; min-height: 100px;">اسحب العناصر هنا</div>',
                },
                {
                    id: 'row',
                    label: 'صف',
                    category: 'تخطيط',
                    content: '<div style="display: flex; gap: 10px; padding: 10px;"><div style="flex: 1; padding: 20px; background: #f3f4f6; text-align: center;">عمود 1</div><div style="flex: 1; padding: 20px; background: #f3f4f6; text-align: center;">عمود 2</div></div>',
                },

                // Background blocks - NEW
                {
                    id: 'background-section',
                    label: 'قسم بخلفية',
                    category: 'خلفيات',
                    content: `
                        <section style="
                            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                            padding: 60px 20px;
                            text-align: center;
                            min-height: 300px;
                            display: flex;
                            flex-direction: column;
                            justify-content: center;
                            align-items: center;
                            position: relative;
                            border-radius: 12px;
                            margin: 20px 0;
                        ">
                            <h2 style="color: white; font-family: Cairo, Arial, sans-serif; font-size: 2.5em; margin-bottom: 20px;" data-gjs-editable="true">عنوان القسم</h2>
                            <p style="color: rgba(255,255,255,0.9); font-family: Cairo, Arial, sans-serif; font-size: 1.2em; max-width: 600px;" data-gjs-editable="true">وصف القسم يمكن تحريره وتخصيصه</p>
                        </section>
                    `,
                },
                {
                    id: 'image-background',
                    label: 'خلفية صورة',
                    category: 'خلفيات',
                    content: `
                        <div style="
                            background-image: url('https://images.unsplash.com/photo-1557804506-669a67965ba0?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80');
                            background-size: cover;
                            background-position: center;
                            background-repeat: no-repeat;
                            padding: 80px 20px;
                            text-align: center;
                            min-height: 400px;
                            display: flex;
                            flex-direction: column;
                            justify-content: center;
                            align-items: center;
                            position: relative;
                            border-radius: 12px;
                            margin: 20px 0;
                        ">
                            <div style="
                                background: rgba(0,0,0,0.5);
                                padding: 40px;
                                border-radius: 12px;
                                backdrop-filter: blur(10px);
                            ">
                                <h2 style="color: white; font-family: Cairo, Arial, sans-serif; font-size: 2.5em; margin-bottom: 20px;" data-gjs-editable="true">نص على الصورة</h2>
                                <p style="color: rgba(255,255,255,0.9); font-family: Cairo, Arial, sans-serif; font-size: 1.2em;" data-gjs-editable="true">يمكنك إضافة نص فوق الصورة</p>
                            </div>
                        </div>
                    `,
                },
                {
                    id: 'text-zone',
                    label: 'منطقة نص',
                    category: 'خلفيات',
                    content: `
                        <div style="
                            background: rgba(255,255,255,0.95);
                            padding: 30px;
                            border-radius: 12px;
                            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                            margin: 20px;
                            border: 2px dashed #ddd;
                            min-height: 150px;
                            display: flex;
                            flex-direction: column;
                            justify-content: center;
                        ">
                            <h3 style="font-family: Cairo, Arial, sans-serif; color: #333; margin-bottom: 15px;" data-gjs-editable="true">منطقة نص قابلة للتحرير</h3>
                            <p style="font-family: Cairo, Arial, sans-serif; color: #666; line-height: 1.6;" data-gjs-editable="true">اكتب المحتوى الخاص بك هنا. يمكن تحرير هذا النص وتنسيقه.</p>
                        </div>
                    `,
                },
                {
                    id: 'upload-image',
                    label: 'رفع صورة',
                    category: 'وسائط',
                    content: `
                        <div style="
                            border: 3px dashed #ccc;
                            padding: 40px;
                            text-align: center;
                            border-radius: 12px;
                            background: #f9f9f9;
                            cursor: pointer;
                            transition: all 0.3s;
                        " onclick="this.querySelector('input').click()">
                            <input type="file" accept="image/*" style="display: none;" onchange="
                                const file = this.files[0];
                                if (file) {
                                    const reader = new FileReader();
                                    reader.onload = (e) => {
                                        this.parentElement.innerHTML = '<img src=&quot;' + e.target.result + '&quot; style=&quot;max-width: 100%; height: auto; border-radius: 8px;&quot; alt=&quot;صورة مرفوعة&quot;>';
                                    };
                                    reader.readAsDataURL(file);
                                }
                            ">
                            <i class="fas fa-cloud-upload-alt" style="font-size: 48px; color: #ccc; margin-bottom: 15px;"></i>
                            <p style="font-family: Cairo, Arial, sans-serif; color: #666; margin: 0;">اضغط لرفع صورة</p>
                        </div>
                    `,
                },

                // CANVA-LIKE POSITIONING BLOCKS
                {
                    id: 'canvas-container',
                    label: 'لوحة تصميم',
                    category: 'كانفا',
                    content: `
                        <div class="canvas-container" style="
                            position: relative;
                            width: 600px;
                            height: 400px;
                            background: #f8f9fa;
                            border: 2px dashed #dee2e6;
                            border-radius: 12px;
                            margin: 20px auto;
                            overflow: hidden;
                            background-image: url('data:image/svg+xml,<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; width=&quot;20&quot; height=&quot;20&quot; viewBox=&quot;0 0 20 20&quot;><rect width=&quot;20&quot; height=&quot;20&quot; fill=&quot;%23f8f9fa&quot;/><rect width=&quot;1&quot; height=&quot;20&quot; fill=&quot;%23e9ecef&quot;/><rect width=&quot;20&quot; height=&quot;1&quot; fill=&quot;%23e9ecef&quot;/></svg>');
                        ">
                            <div style="
                                position: absolute;
                                top: 50%;
                                left: 50%;
                                transform: translate(-50%, -50%);
                                text-align: center;
                                color: #6c757d;
                                font-family: Cairo, Arial, sans-serif;
                                pointer-events: none;
                            ">
                                <i class="fas fa-plus-circle" style="font-size: 48px; margin-bottom: 10px;"></i>
                                <p style="margin: 0; font-size: 16px;">اسحب العناصر هنا لإنشاء تصميمك</p>
                            </div>
                        </div>
                    `,
                },
                {
                    id: 'background-image-canvas',
                    label: 'لوحة بخلفية',
                    category: 'كانفا',
                    content: `
                        <div class="canvas-container" style="
                            position: relative;
                            width: 600px;
                            height: 400px;
                            background-image: url('https://images.unsplash.com/photo-1557804506-669a67965ba0?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80');
                            background-size: cover;
                            background-position: center;
                            background-repeat: no-repeat;
                            border-radius: 12px;
                            margin: 20px auto;
                            overflow: hidden;
                            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
                        ">
                            <div style="
                                position: absolute;
                                top: 20px;
                                right: 20px;
                                background: rgba(255,255,255,0.9);
                                padding: 8px 12px;
                                border-radius: 20px;
                                font-family: Cairo, Arial, sans-serif;
                                font-size: 12px;
                                color: #666;
                                backdrop-filter: blur(10px);
                            ">
                                اسحب العناصر هنا
                            </div>
                        </div>
                    `,
                },
                {
                    id: 'floating-text',
                    label: 'نص عائم',
                    category: 'كانفا',
                    content: `
                        <div style="
                            position: absolute;
                            top: 50px;
                            left: 50px;
                            background: rgba(255,255,255,0.95);
                            padding: 15px 20px;
                            border-radius: 8px;
                            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                            font-family: Cairo, Arial, sans-serif;
                            color: #333;
                            cursor: move;
                            border: 2px solid transparent;
                            transition: all 0.3s;
                            backdrop-filter: blur(10px);
                        " data-gjs-editable="true" onmouseover="this.style.borderColor='#007bff'" onmouseout="this.style.borderColor='transparent'">
                            اكتب النص هنا
                        </div>
                    `,
                },
                {
                    id: 'floating-button',
                    label: 'زر عائم',
                    category: 'كانفا',
                    content: `
                        <button style="
                            position: absolute;
                            top: 100px;
                            left: 100px;
                            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                            color: white;
                            padding: 12px 24px;
                            border: none;
                            border-radius: 25px;
                            font-family: Cairo, Arial, sans-serif;
                            font-weight: 600;
                            cursor: pointer;
                            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
                            transition: all 0.3s;
                            border: 2px solid transparent;
                        " data-gjs-editable="true" onmouseover="this.style.transform='translateY(-2px)'; this.style.borderColor='#fff'" onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='transparent'">
                            انقر هنا
                        </button>
                    `,
                },
                {
                    id: 'floating-image',
                    label: 'صورة عائمة',
                    category: 'كانفا',
                    content: `
                        <div style="
                            position: absolute;
                            top: 150px;
                            left: 150px;
                            width: 120px;
                            height: 120px;
                            border-radius: 12px;
                            overflow: hidden;
                            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
                            cursor: move;
                            border: 3px solid transparent;
                            transition: all 0.3s;
                        " onmouseover="this.style.borderColor='#007bff'; this.style.transform='scale(1.05)'" onmouseout="this.style.borderColor='transparent'; this.style.transform='scale(1)'">
                            <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80" style="
                                width: 100%;
                                height: 100%;
                                object-fit: cover;
                            " alt="صورة عائمة">
                        </div>
                    `,
                },
                {
                    id: 'floating-badge',
                    label: 'شارة عائمة',
                    category: 'كانفا',
                    content: `
                        <div style="
                            position: absolute;
                            top: 30px;
                            left: 200px;
                            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
                            color: white;
                            padding: 8px 16px;
                            border-radius: 20px;
                            font-family: Cairo, Arial, sans-serif;
                            font-size: 14px;
                            font-weight: 600;
                            box-shadow: 0 4px 12px rgba(255, 107, 107, 0.4);
                            cursor: move;
                            border: 2px solid transparent;
                            transition: all 0.3s;
                        " data-gjs-editable="true" onmouseover="this.style.borderColor='#fff'; this.style.transform='rotate(-5deg)'" onmouseout="this.style.borderColor='transparent'; this.style.transform='rotate(0deg)'">
                            جديد!
                        </div>
                    `,
                }
            ]
        },

        // Layer Manager
        layerManager: {
            appendTo: '.layers-container'
        },

        // Style Manager - ENHANCED FOR CANVA-LIKE POSITIONING
        styleManager: {
            appendTo: '.styles-container',
            sectors: [
                {
                    name: 'الموضع والتخطيط',
                    open: true,
                    buildProps: ['position', 'top', 'right', 'left', 'bottom', 'z-index', 'transform', 'display', 'float']
                },
                {
                    name: 'الأبعاد',
                    open: false,
                    buildProps: ['width', 'flex-width', 'height', 'max-width', 'min-height', 'margin', 'padding']
                },
                {
                    name: 'النص',
                    open: false,
                    buildProps: ['font-family', 'font-size', 'font-weight', 'letter-spacing', 'color', 'line-height', 'text-align', 'text-decoration', 'text-shadow']
                },
                {
                    name: 'الزخرفة',
                    open: false,
                    buildProps: ['opacity', 'background-color', 'border-radius', 'border', 'box-shadow', 'background']
                },
                {
                    name: 'التأثيرات',
                    open: false,
                    buildProps: ['transition', 'animation', 'filter', 'backdrop-filter']
                }
            ]
        },

        // Trait Manager
        traitManager: {
            appendTo: '.traits-container',
        },

        // Canvas - FIXED STYLES TO PREVENT ATTRIBUTE ERRORS
        canvas: {
            styles: [
                'https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap'
            ],
            scripts: []
        },

        // Commands - SIMPLIFIED TO AVOID ERRORS
        commands: {
            defaults: []
        },

        // Device Manager - FIXED
        deviceManager: {
            devices: [
                {
                    id: 'desktop',
                    name: 'سطح المكتب',
                    width: '',
                },
                {
                    id: 'tablet',
                    name: 'تابلت',
                    width: '768px',
                    widthMedia: '992px',
                },
                {
                    id: 'mobile',
                    name: 'موبايل',
                    width: '320px',
                    widthMedia: '768px',
                }
            ]
        },

        // Rich Text Editor
        richTextEditor: {
            actions: ['bold', 'italic', 'underline', 'strikethrough', 'link']
        },

        // Asset Manager
        assetManager: {
            embedAsBase64: true,
            upload: false, // We'll handle uploads separately
            uploadText: 'رفع الصور هنا أو انقر للتصفح',
            addBtnText: 'إضافة صورة',
            customFetch: (url, options) => {
                // Custom fetch for handling uploads
                return fetch(url, options);
            }
        }
    });

    // Load existing design if available
    if (props.template.design_data) {
        try {
            const designData = JSON.parse(props.template.design_data);
            editor.loadProjectData(designData);
        } catch (error) {
            console.error('Error loading design data:', error);
            addNotification('خطأ في تحميل التصميم المحفوظ', 'error');
        }
    }

    // Setup undo manager
    undoManager.value = editor.UndoManager;

    // Update undo/redo buttons state
    const updateUndoRedoState = () => {
        canUndo.value = undoManager.value.hasUndo();
        canRedo.value = undoManager.value.hasRedo();
    };

    // Listen to undo manager changes
    editor.on('change:changesCount', updateUndoRedoState);

    // Auto-save functionality
    if (autoSaveEnabled.value) {
        startAutoSave();
    }

    // ENHANCED: Force panels initialization after editor is ready
    nextTick(() => {
        // Initial panels setup
        setTimeout(() => {
            initializePanels();

            // Ensure blocks are visible by default
            if (activeSidebarTab.value === 'blocks') {
                setTimeout(() => {
                    const blocksContainer = document.querySelector('.blocks-container');
                    if (blocksContainer && blocksContainer.children.length === 0) {
                        console.log('Blocks container empty, forcing re-render');
                        editor.BlockManager.render(blocksContainer);
                    }
                }, 500);
            }
        }, 200);
    });

    addNotification('تم تحميل المحرر بنجاح', 'success');
};

// ENHANCED: Refresh panels when switching tabs - FIXED CONTENT EMPTYING
const refreshPanels = () => {
    if (!editor) return;

    try {
        // Get all managers
        const layerManager = editor.LayerManager;
        const styleManager = editor.StyleManager;
        const traitManager = editor.TraitManager;
        const blockManager = editor.BlockManager;

        // Force complete re-render with proper container targeting
        setTimeout(() => {
            // Re-append to containers to force refresh
            if (activeSidebarTab.value === 'blocks') {
                const blocksContainer = document.querySelector('.blocks-container');
                if (blocksContainer) {
                    blocksContainer.innerHTML = '';
                    blockManager.render(blocksContainer);
                }
            } else if (activeSidebarTab.value === 'layers') {
                const layersContainer = document.querySelector('.layers-container');
                if (layersContainer) {
                    layersContainer.innerHTML = '';
                    layerManager.render(layersContainer);
                }
            } else if (activeSidebarTab.value === 'styles') {
                const stylesContainer = document.querySelector('.styles-container');
                if (stylesContainer) {
                    stylesContainer.innerHTML = '';
                    styleManager.render(stylesContainer);
                }
            } else if (activeSidebarTab.value === 'properties') {
                const traitsContainer = document.querySelector('.traits-container');
                if (traitsContainer) {
                    traitsContainer.innerHTML = '';
                    traitManager.render(traitsContainer);
                }
            }
        }, 100);

    } catch (error) {
        console.log('Panel refresh error:', error);
        // Fallback: Force complete re-initialization
        setTimeout(() => {
            initializePanels();
        }, 200);
    }
};

// NEW: Initialize panels properly - WITH ENHANCED ERROR HANDLING
const initializePanels = () => {
    if (!editor) {
        console.warn('Editor not ready for panel initialization');
        return;
    }

    try {
        // Force render all panels to their containers with error handling
        const blockManager = editor.BlockManager;
        const layerManager = editor.LayerManager;
        const styleManager = editor.StyleManager;
        const traitManager = editor.TraitManager;

        // Render blocks with error handling
        try {
            const blocksContainer = document.querySelector('.blocks-container');
            if (blocksContainer && blockManager) {
                blocksContainer.innerHTML = '';
                blockManager.render(blocksContainer);
                console.log('Blocks rendered successfully');
            }
        } catch (blockError) {
            console.error('Error rendering blocks:', blockError);
        }

        // Render layers with error handling
        try {
            const layersContainer = document.querySelector('.layers-container');
            if (layersContainer && layerManager) {
                layersContainer.innerHTML = '';
                layerManager.render(layersContainer);
                console.log('Layers rendered successfully');
            }
        } catch (layerError) {
            console.error('Error rendering layers:', layerError);
        }

        // Render styles with error handling
        try {
            const stylesContainer = document.querySelector('.styles-container');
            if (stylesContainer && styleManager) {
                stylesContainer.innerHTML = '';
                styleManager.render(stylesContainer);
                console.log('Styles rendered successfully');
            }
        } catch (styleError) {
            console.error('Error rendering styles:', styleError);
        }

        // Render traits with error handling
        try {
            const traitsContainer = document.querySelector('.traits-container');
            if (traitsContainer && traitManager) {
                traitsContainer.innerHTML = '';
                traitManager.render(traitsContainer);
                console.log('Traits rendered successfully');
            }
        } catch (traitError) {
            console.error('Error rendering traits:', traitError);
        }

        console.log('Panels initialization completed');

    } catch (error) {
        console.error('Panel initialization error:', error);
        // Fallback: try again after a delay
        setTimeout(() => {
            console.log('Retrying panel initialization...');
            initializePanels();
        }, 1000);
    }
};

// Professional UI Controls
const toggleSidebar = () => {
    // Toggle sidebar visibility
    const sidebar = document.querySelector('.w-80');
    if (sidebar) {
        sidebar.classList.toggle('hidden');
    }
};

const setDevice = (device) => {
    if (!editor) return;

    currentDevice.value = device;

    const deviceManager = editor.DeviceManager;

    try {
        // Use device ID directly
        deviceManager.select(device);

        addNotification(`تم التبديل إلى عرض ${device === 'desktop' ? 'سطح المكتب' : device === 'tablet' ? 'التابلت' : 'الموبايل'}`, 'info');

    } catch (error) {
        console.error('Device switch error:', error);
        addNotification('حدث خطأ أثناء تغيير العرض', 'error');
    }
};

// Zoom Controls
const zoomIn = () => {
    if (!editor) return;

    zoomLevel.value = Math.min(zoomLevel.value + 0.1, 3);
    editor.Canvas.setZoom(zoomLevel.value);
    addNotification(`تم التكبير إلى ${Math.round(zoomLevel.value * 100)}%`, 'info');
};

const zoomOut = () => {
    if (!editor) return;

    zoomLevel.value = Math.max(zoomLevel.value - 0.1, 0.1);
    editor.Canvas.setZoom(zoomLevel.value);
    addNotification(`تم التصغير إلى ${Math.round(zoomLevel.value * 100)}%`, 'info');
};

const resetZoom = () => {
    if (!editor) return;

    zoomLevel.value = 1;
    editor.Canvas.setZoom(1);
    addNotification('تم إعادة تعيين التكبير', 'info');
};

// Grid and Rulers
const toggleGrid = () => {
    showGrid.value = !showGrid.value;
    // Implementation for grid toggle
    addNotification(`تم ${showGrid.value ? 'إظهار' : 'إخفاء'} الشبكة`, 'info');
};

const toggleRulers = () => {
    showRulers.value = !showRulers.value;
    // Implementation for rulers toggle
    addNotification(`تم ${showRulers.value ? 'إظهار' : 'إخفاء'} المساطر`, 'info');
};

// Snap to Grid Toggle
const toggleSnapToGrid = () => {
    snapToGrid.value = !snapToGrid.value;

    if (editor) {
        // Enable/disable snap to grid in GrapesJS
        const canvas = editor.Canvas;
        if (snapToGrid.value) {
            // Add snap functionality
            canvas.getBody().style.backgroundImage = `
                linear-gradient(rgba(0,0,0,.1) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0,0,0,.1) 1px, transparent 1px)
            `;
            canvas.getBody().style.backgroundSize = '20px 20px';
        } else {
            canvas.getBody().style.backgroundImage = 'none';
        }
    }

    addNotification(`تم ${snapToGrid.value ? 'تفعيل' : 'إلغاء'} المحاذاة للشبكة`, 'info');
};

// Position Guide Toggle
const togglePositionGuide = () => {
    showPositionGuide.value = !showPositionGuide.value;

    if (editor) {
        const canvas = editor.Canvas;
        const canvasBody = canvas.getBody();

        if (showPositionGuide.value) {
            // Add position guide lines
            const guideStyle = `
                .position-guide::before,
                .position-guide::after {
                    content: '';
                    position: absolute;
                    background: rgba(0, 123, 255, 0.5);
                    z-index: 9999;
                    pointer-events: none;
                }
                .position-guide::before {
                    top: 0;
                    left: 50%;
                    width: 1px;
                    height: 100%;
                    transform: translateX(-50%);
                }
                .position-guide::after {
                    top: 50%;
                    left: 0;
                    width: 100%;
                    height: 1px;
                    transform: translateY(-50%);
                }
            `;

            const styleElement = canvasBody.querySelector('#position-guide-style') || canvasBody.ownerDocument.createElement('style');
            styleElement.id = 'position-guide-style';
            styleElement.textContent = guideStyle;
            canvasBody.ownerDocument.head.appendChild(styleElement);

            canvasBody.classList.add('position-guide');
        } else {
            canvasBody.classList.remove('position-guide');
            const styleElement = canvasBody.querySelector('#position-guide-style');
            if (styleElement) {
                styleElement.remove();
            }
        }
    }

    addNotification(`تم ${showPositionGuide.value ? 'إظهار' : 'إخفاء'} دليل الموضع`, 'info');
};

// Save design - FIXED
const saveDesign = async () => {
    if (!editor) {
        addNotification('المحرر غير جاهز بعد', 'error');
        return;
    }

    saving.value = true;
    showLoadingWithProgress('حفظ التصميم', 'جاري حفظ التصميم...', 0);

    try {
        updateLoadingProgress(25, 'جاري تحضير البيانات...');

        // Clean project data to avoid issues with external resources
        const projectData = editor.getProjectData();

        // Replace problematic external images with placeholders
        if (projectData.assets) {
            projectData.assets = projectData.assets.filter(asset => {
                return !asset.src || !asset.src.includes('300x200') || asset.src.startsWith('data:');
            });
        }

        updateLoadingProgress(50, 'جاري إرسال البيانات...');

        // Get CSRF token safely
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (!csrfToken) {
            addNotification('خطأ في الأمان، يرجى إعادة تحميل الصفحة', 'error');
            saving.value = false;
            hideLoading();
            return;
        }

        updateLoadingProgress(75, 'جاري معالجة الطلب...');

        const response = await fetch(route('admin.templates.design.save', props.template.id), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                design_data: JSON.stringify(projectData)
            })
        });

        updateLoadingProgress(90, 'جاري التحقق من النتيجة...');

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();

        updateLoadingProgress(100, 'تم الحفظ بنجاح!');

        if (result.success) {
            addNotification('تم حفظ التصميم بنجاح! 🎉', 'success');
            lastSaved.value = new Date();
        } else {
            addNotification(result.message || 'حدث خطأ أثناء حفظ التصميم', 'error');
        }
    } catch (error) {
        console.error('Error saving design:', error);
        addNotification('حدث خطأ في الاتصال أثناء حفظ التصميم', 'error');
    } finally {
        saving.value = false;
        hideLoading();
    }
};

// Auto-save functionality
const autoSave = async () => {
    if (!autoSaveEnabled.value || saving.value || !editor) return;

    try {
        const projectData = editor.getProjectData();

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
                design_data: JSON.stringify(projectData)
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

// Undo/Redo functions - FIXED
const undo = () => {
    if (editor && undoManager.value && undoManager.value.hasUndo()) {
        undoManager.value.undo();
        addNotification('تم التراجع', 'info');
    }
};

const redo = () => {
    if (editor && undoManager.value && undoManager.value.hasRedo()) {
        undoManager.value.redo();
        addNotification('تم الإعادة', 'success');
    }
};

// AI Assistant Functions
const generateWithAI = async () => {
    if (!aiPrompt.value.trim()) {
        addNotification('يرجى إدخال وصف للتصميم المطلوب', 'warning');
        return;
    }

    aiGenerating.value = true;
    showLoadingWithProgress('الذكاء الاصطناعي يعمل', 'جاري إنشاء التصميم...', 0);

    try {
        // Simulate AI generation process
        for (let i = 0; i <= 100; i += 10) {
            await new Promise(resolve => setTimeout(resolve, 200));
            updateLoadingProgress(i, `جاري تحليل المتطلبات... ${i}%`);
        }

        // Here you would integrate with actual AI service
        // For now, we'll create a sample design based on the prompt
        await createAIDesign(aiPrompt.value, aiDesignType.value, aiColorScheme.value);

        addNotification('تم إنشاء التصميم بالذكاء الاصطناعي بنجاح!', 'success');
        showAIModal.value = false;
        aiPrompt.value = '';

    } catch (error) {
        console.error('AI Generation Error:', error);
        addNotification('حدث خطأ أثناء إنشاء التصميم', 'error');
    } finally {
        aiGenerating.value = false;
        hideLoading();
    }
};

const generateQuickAI = async (prompt) => {
    aiPrompt.value = prompt;
    await generateWithAI();
};

const createAIDesign = async (prompt, type, colorScheme) => {
    if (!editor) return;

    // Clear current design
    editor.setComponents('');
    editor.setStyle('');

    // Create AI-generated design based on prompt
    let design = '';
    let styles = '';

    if (prompt.includes('زواج') || prompt.includes('عرس')) {
        design = `
            <div class="ai-wedding-card">
                <div class="card-header">
                    <h1 class="main-title">مبارك الزواج</h1>
                    <div class="heart-decoration">💕</div>
                </div>
                <div class="card-body">
                    <p class="couple-names" data-gjs-editable="true">اسم العريس & اسم العروس</p>
                    <p class="wedding-date" data-gjs-editable="true">التاريخ: __ / __ / ____</p>
                    <p class="wedding-venue" data-gjs-editable="true">المكان: _____________</p>
                </div>
                <div class="card-footer">
                    <div class="decorative-border"></div>
                </div>
            </div>
        `;

        styles = `
            .ai-wedding-card {
                background: linear-gradient(135deg, #fdf2f8 0%, #fce7f3 100%);
                padding: 40px;
                text-align: center;
                border-radius: 20px;
                box-shadow: 0 20px 40px rgba(0,0,0,0.1);
                max-width: 600px;
                margin: 50px auto;
                border: 3px solid #f9a8d4;
                font-family: 'Cairo', Arial, sans-serif;
            }
            .card-header {
                margin-bottom: 30px;
            }
            .main-title {
                color: #be185d;
                font-size: 36px;
                font-weight: bold;
                margin-bottom: 15px;
                text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
            }
            .heart-decoration {
                font-size: 48px;
                margin: 10px 0;
                animation: pulse 2s infinite;
            }
            .couple-names {
                color: #9d174d;
                font-size: 24px;
                margin: 20px 0;
                font-weight: 600;
            }
            .wedding-date, .wedding-venue {
                color: #7c2d12;
                font-size: 18px;
                margin: 10px 0;
            }
            .decorative-border {
                height: 4px;
                background: linear-gradient(90deg, #f9a8d4, #ec4899, #f9a8d4);
                border-radius: 2px;
                margin-top: 30px;
            }
            @keyframes pulse {
                0%, 100% { transform: scale(1); }
                50% { transform: scale(1.1); }
            }
        `;
    } else if (prompt.includes('عمل') || prompt.includes('مهني')) {
        design = `
            <div class="ai-business-card">
                <div class="card-left">
                    <div class="company-logo">
                        <i class="fas fa-building"></i>
                    </div>
                </div>
                <div class="card-right">
                    <h1 class="company-name" data-gjs-editable="true">اسم الشركة</h1>
                    <h2 class="person-name" data-gjs-editable="true">الاسم الكامل</h2>
                    <p class="job-title" data-gjs-editable="true">المنصب</p>
                    <div class="contact-info">
                        <p data-gjs-editable="true">الهاتف: +966 XX XXX XXXX</p>
                        <p data-gjs-editable="true">البريد: email@company.com</p>
                        <p data-gjs-editable="true">الموقع: www.company.com</p>
                    </div>
                </div>
            </div>
        `;

        styles = `
            .ai-business-card {
                display: flex;
                background: linear-gradient(135deg, #1e40af 0%, #3730a3 100%);
                border-radius: 15px;
                overflow: hidden;
                box-shadow: 0 15px 30px rgba(0,0,0,0.2);
                max-width: 500px;
                margin: 50px auto;
                height: 300px;
                font-family: 'Cairo', Arial, sans-serif;
            }
            .card-left {
                background: rgba(255,255,255,0.1);
                width: 120px;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .company-logo {
                color: white;
                font-size: 48px;
            }
            .card-right {
                flex: 1;
                padding: 30px;
                color: white;
            }
            .company-name {
                font-size: 24px;
                font-weight: bold;
                margin-bottom: 10px;
            }
            .person-name {
                font-size: 20px;
                color: #fbbf24;
                margin-bottom: 5px;
            }
            .job-title {
                font-size: 16px;
                color: #e0e7ff;
                margin-bottom: 20px;
            }
            .contact-info p {
                font-size: 14px;
                margin: 5px 0;
                color: #c7d2fe;
            }
        `;
    } else {
        // Default creative design
        design = `
            <div class="ai-creative-card">
                <div class="creative-header">
                    <h1 class="creative-title" data-gjs-editable="true">تصميم إبداعي</h1>
                    <div class="creative-subtitle" data-gjs-editable="true">مُنشأ بالذكاء الاصطناعي</div>
                </div>
                <div class="creative-content">
                    <p class="creative-text" data-gjs-editable="true">هذا نص يمكن تحريره وتخصيصه حسب احتياجاتك</p>
                </div>
                <div class="creative-footer">
                    <div class="creative-pattern"></div>
                </div>
            </div>
        `;

        styles = `
            .ai-creative-card {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                padding: 40px;
                text-align: center;
                border-radius: 20px;
                box-shadow: 0 20px 40px rgba(0,0,0,0.15);
                max-width: 600px;
                margin: 50px auto;
                color: white;
                position: relative;
                overflow: hidden;
                font-family: 'Cairo', Arial, sans-serif;
            }
            .creative-header {
                margin-bottom: 30px;
            }
            .creative-title {
                font-size: 32px;
                font-weight: bold;
                margin-bottom: 10px;
            }
            .creative-subtitle {
                font-size: 16px;
                opacity: 0.8;
            }
            .creative-text {
                font-size: 18px;
                line-height: 1.6;
                margin: 20px 0;
            }
            .creative-pattern {
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                height: 60px;
                background: linear-gradient(45deg, rgba(255,255,255,0.1) 25%, transparent 25%),
                           linear-gradient(-45deg, rgba(255,255,255,0.1) 25%, transparent 25%);
                background-size: 20px 20px;
            }
        `;
    }

    // Apply the generated design
    editor.setComponents(design);
    editor.setStyle(styles);

    addNotification('تم تطبيق التصميم المُنشأ بالذكاء الاصطناعي', 'success');
};

// Preview Design
const previewDesign = () => {
    if (!editor) {
        addNotification('المحرر غير جاهز بعد', 'error');
        return;
    }

    try {
        const html = editor.getHtml();
        const css = editor.getCss();

        const previewContent = `
            <!DOCTYPE html>
            <html dir="rtl" lang="ar">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>معاينة التصميم - ${props.template.name}</title>
                <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">
                <style>
                    body {
                        margin: 0;
                        padding: 20px;
                        font-family: 'Cairo', Arial, sans-serif;
                        background: #f8fafc;
                        direction: rtl;
                    }
                    .preview-container {
                        max-width: 1200px;
                        margin: 0 auto;
                        background: white;
                        border-radius: 12px;
                        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                        overflow: hidden;
                    }
                    .preview-header {
                        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                        color: white;
                        padding: 20px;
                        text-align: center;
                    }
                    .preview-content {
                        padding: 20px;
                    }
                    ${css}
                </style>
            </head>
            <body>
                <div class="preview-container">
                    <div class="preview-header">
                        <h1>معاينة التصميم</h1>
                        <p>${props.template.name}</p>
                    </div>
                    <div class="preview-content">
                        ${html}
                    </div>
                </div>
            </body>
            </html>
        `;

        const previewWindow = window.open('', '_blank');
        previewWindow.document.write(previewContent);
        previewWindow.document.close();

        addNotification('تم فتح المعاينة في نافذة جديدة', 'success');

    } catch (error) {
        console.error('Preview error:', error);
        addNotification('حدث خطأ أثناء إنشاء المعاينة', 'error');
    }
};



// Export as HTML
const exportAsHTML = () => {
    if (!editor) return;

    const html = editor.getHtml();
    const css = editor.getCss();

    const fullHTML = `
        <!DOCTYPE html>
        <html dir="rtl" lang="ar">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>${props.template.name}</title>
            <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">
            <style>
                body {
                    margin: 0;
                    font-family: 'Cairo', Arial, sans-serif;
                }
                ${css}
            </style>
        </head>
        <body>
            ${html}
        </body>
        </html>
    `;

    const blob = new Blob([fullHTML], { type: 'text/html' });
    const link = document.createElement('a');
    link.download = `${props.template.name}-design.html`;
    link.href = URL.createObjectURL(blob);
    link.click();
    showExportMenu.value = false;
    showNotification('تم تصدير HTML بنجاح', 'success');
};

// Export as JSON
const exportAsJSON = () => {
    if (!editor) return;

    const projectData = editor.getProjectData();
    const dataStr = JSON.stringify(projectData, null, 2);
    const dataBlob = new Blob([dataStr], { type: 'application/json' });

    const link = document.createElement('a');
    link.download = `${props.template.name}-design.json`;
    link.href = URL.createObjectURL(dataBlob);
    link.click();
    showExportMenu.value = false;
    showNotification('تم تصدير JSON بنجاح', 'success');
};

// Export as Image (Professional)
const exportAsImage = async () => {
    if (!editor) return;

    showLoadingWithProgress('تصدير الصورة', 'جاري إنشاء الصورة...', 0);

    try {
        updateLoadingProgress(25, 'جاري تحضير المحتوى...');

        const canvas = editor.Canvas.getCanvasView();
        const iframe = canvas.getFrameEl();
        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;

        updateLoadingProgress(50, 'جاري معالجة العناصر...');

        // Create a temporary canvas
        const tempCanvas = document.createElement('canvas');
        const ctx = tempCanvas.getContext('2d');

        // Set canvas size
        tempCanvas.width = 800;
        tempCanvas.height = 600;

        updateLoadingProgress(75, 'جاري إنشاء الصورة...');

        // Fill with white background
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, tempCanvas.width, tempCanvas.height);

        // Add text indicating this is a preview
        ctx.fillStyle = '#333333';
        ctx.font = '24px Cairo, Arial, sans-serif';
        ctx.textAlign = 'center';
        ctx.fillText('معاينة التصميم', tempCanvas.width / 2, tempCanvas.height / 2);

        updateLoadingProgress(100, 'جاري التحميل...');

        // Convert to blob and download
        tempCanvas.toBlob((blob) => {
            const link = document.createElement('a');
            link.download = `${props.template.name}-design.png`;
            link.href = URL.createObjectURL(blob);
            link.click();

            addNotification('تم تصدير الصورة بنجاح', 'success');
            hideLoading();
        }, 'image/png');

    } catch (error) {
        console.error('Export error:', error);
        addNotification('حدث خطأ أثناء تصدير الصورة', 'error');
        hideLoading();
    }

    showAdvancedMenu.value = false;
};

// Import JSON Design
const importJSON = () => {
    if (!editor) {
        addNotification('المحرر غير جاهز بعد', 'error');
        return;
    }

    const input = document.createElement('input');
    input.type = 'file';
    input.accept = '.json';

    input.onchange = async (event) => {
        const file = event.target.files[0];
        if (!file) return;

        showLoadingWithProgress('استيراد التصميم', 'جاري قراءة الملف...', 0);

        try {
            updateLoadingProgress(25, 'جاري تحليل البيانات...');

            const text = await file.text();
            const designData = JSON.parse(text);

            updateLoadingProgress(50, 'جاري التحقق من صحة البيانات...');

            if (!designData || typeof designData !== 'object') {
                throw new Error('ملف غير صحيح');
            }

            updateLoadingProgress(75, 'جاري تطبيق التصميم...');

            editor.loadProjectData(designData);

            updateLoadingProgress(100, 'تم الاستيراد بنجاح!');

            addNotification('تم استيراد التصميم بنجاح!', 'success');
            showAdvancedMenu.value = false;

        } catch (error) {
            console.error('Import error:', error);
            addNotification('خطأ في استيراد الملف. تأكد من أنه ملف JSON صحيح', 'error');
        } finally {
            hideLoading();
        }
    };

    input.click();
};

// CANVA-LIKE POSITIONING FUNCTIONS - SIMPLIFIED
const runCommand = (commandId) => {
    if (!editor) {
        addNotification('المحرر غير جاهز بعد', 'error');
        return;
    }

    const selected = editor.getSelected();
    if (!selected) {
        addNotification('يرجى تحديد عنصر أولاً', 'warning');
        return;
    }

    try {
        // Direct style manipulation instead of commands
        switch (commandId) {
            case 'enable-absolute-positioning':
                selected.addStyle({
                    position: 'absolute',
                    top: '50px',
                    left: '50px',
                    'z-index': '10'
                });
                addNotification('تم تفعيل الموضع المطلق', 'success');
                break;
            case 'bring-to-front':
                const currentZ = selected.getStyle()['z-index'] || 1;
                selected.addStyle({ 'z-index': parseInt(currentZ) + 10 });
                addNotification('تم إحضار العنصر للأمام', 'success');
                break;
            case 'send-to-back':
                const currentZBack = selected.getStyle()['z-index'] || 1;
                selected.addStyle({ 'z-index': Math.max(1, parseInt(currentZBack) - 10) });
                addNotification('تم إرسال العنصر للخلف', 'success');
                break;
            case 'center-element':
                selected.addStyle({
                    position: 'absolute',
                    top: '50%',
                    left: '50%',
                    transform: 'translate(-50%, -50%)'
                });
                addNotification('تم توسيط العنصر', 'success');
                break;
        }
    } catch (error) {
        console.error('Command error:', error);
        addNotification('حدث خطأ أثناء تنفيذ الأمر', 'error');
    }
};

const setQuickPosition = (position) => {
    if (!editor) {
        addNotification('المحرر غير جاهز بعد', 'error');
        return;
    }

    const selected = editor.getSelected();
    if (!selected) {
        addNotification('يرجى تحديد عنصر أولاً', 'warning');
        return;
    }

    try {
        let styles = { position: 'absolute' };

        switch (position) {
            case 'top-left':
                styles = { ...styles, top: '20px', left: '20px', transform: 'none' };
                break;
            case 'top-center':
                styles = { ...styles, top: '20px', left: '50%', transform: 'translateX(-50%)' };
                break;
            case 'top-right':
                styles = { ...styles, top: '20px', right: '20px', left: 'auto', transform: 'none' };
                break;
            case 'center-left':
                styles = { ...styles, top: '50%', left: '20px', transform: 'translateY(-50%)' };
                break;
            case 'center':
                styles = { ...styles, top: '50%', left: '50%', transform: 'translate(-50%, -50%)' };
                break;
            case 'center-right':
                styles = { ...styles, top: '50%', right: '20px', left: 'auto', transform: 'translateY(-50%)' };
                break;
            case 'bottom-left':
                styles = { ...styles, bottom: '20px', left: '20px', top: 'auto', transform: 'none' };
                break;
            case 'bottom-center':
                styles = { ...styles, bottom: '20px', left: '50%', top: 'auto', transform: 'translateX(-50%)' };
                break;
            case 'bottom-right':
                styles = { ...styles, bottom: '20px', right: '20px', left: 'auto', top: 'auto', transform: 'none' };
                break;
        }

        selected.addStyle(styles);
        addNotification(`تم تطبيق الموضع: ${getPositionName(position)}`, 'success');

    } catch (error) {
        console.error('Position error:', error);
        addNotification('حدث خطأ أثناء تطبيق الموضع', 'error');
    }
};

const getPositionName = (position) => {
    const names = {
        'top-left': 'أعلى يسار',
        'top-center': 'أعلى وسط',
        'top-right': 'أعلى يمين',
        'center-left': 'وسط يسار',
        'center': 'وسط',
        'center-right': 'وسط يمين',
        'bottom-left': 'أسفل يسار',
        'bottom-center': 'أسفل وسط',
        'bottom-right': 'أسفل يمين'
    };
    return names[position] || position;
};

// ENHANCED: Safe tab switching function - PREVENTS CONTENT LOSS
const switchToTab = (tabName) => {
    console.log(`Switching to tab: ${tabName}`);

    // Store current tab state
    const previousTab = activeSidebarTab.value;

    // Change tab
    activeSidebarTab.value = tabName;

    // Force refresh after tab change
    nextTick(() => {
        setTimeout(() => {
            // Check if content needs to be restored
            const containerSelector = `.${tabName === 'blocks' ? 'blocks' : tabName === 'layers' ? 'layers' : tabName === 'styles' ? 'styles' : 'traits'}-container`;
            const container = document.querySelector(containerSelector);

            if (container) {
                if (container.children.length === 0) {
                    console.log(`Container ${tabName} is empty, initializing...`);

                    if (editor) {
                        try {
                            switch (tabName) {
                                case 'blocks':
                                    editor.BlockManager.render(container);
                                    break;
                                case 'layers':
                                    editor.LayerManager.render(container);
                                    break;
                                case 'styles':
                                    editor.StyleManager.render(container);
                                    break;
                                case 'properties':
                                    editor.TraitManager.render(container);
                                    break;
                            }
                        } catch (error) {
                            console.error(`Error rendering ${tabName}:`, error);
                            // Fallback: full re-initialization
                            initializePanels();
                        }
                    }
                } else {
                    console.log(`Container ${tabName} has content:`, container.children.length, 'elements');
                }
            }
        }, 100);
    });
};

// DEBUG: Function to diagnose panel issues
const debugPanels = () => {
    console.log('=== PANEL DEBUGGING ===');
    console.log('Current tab:', activeSidebarTab.value);
    console.log('Editor exists:', !!editor);

    if (editor) {
        console.log('BlockManager:', editor.BlockManager);
        console.log('LayerManager:', editor.LayerManager);
        console.log('StyleManager:', editor.StyleManager);
        console.log('TraitManager:', editor.TraitManager);
    }

    // Check containers
    const containers = [
        { name: 'blocks', selector: '.blocks-container' },
        { name: 'layers', selector: '.layers-container' },
        { name: 'styles', selector: '.styles-container' },
        { name: 'traits', selector: '.traits-container' }
    ];

    containers.forEach(container => {
        const element = document.querySelector(container.selector);
        console.log(`${container.name} container:`, {
            exists: !!element,
            children: element ? element.children.length : 0,
            innerHTML: element ? element.innerHTML.substring(0, 100) + '...' : 'N/A'
        });
    });

    // Force re-initialization
    console.log('Forcing re-initialization...');
    initializePanels();

    addNotification('تم تشخيص المشاكل - تحقق من وحدة التحكم', 'info');
};

// Close menus function - FIXES showExportMenu error
const closeMenus = (event) => {
    // Close export menu if clicking outside
    if (showExportMenu.value && !event.target.closest('.export-menu')) {
        showExportMenu.value = false;
    }

    // Close advanced menu if clicking outside
    if (showAdvancedMenu.value && !event.target.closest('.advanced-menu')) {
        showAdvancedMenu.value = false;
    }
};

// Export as PDF
const exportAsPDF = async () => {
    if (!editor) return;

    showLoadingWithProgress('تصدير PDF', 'جاري إنشاء ملف PDF...', 0);

    try {
        updateLoadingProgress(50, 'جاري معالجة المحتوى...');

        const html = editor.getHtml();
        const css = editor.getCss();

        // Create PDF content
        const pdfContent = `
            <!DOCTYPE html>
            <html dir="rtl" lang="ar">
            <head>
                <meta charset="UTF-8">
                <title>${props.template.name}</title>
                <style>
                    @page { size: A4; margin: 0; }
                    body { margin: 0; font-family: 'Cairo', Arial, sans-serif; }
                    ${css}
                </style>
            </head>
            <body>
                ${html}
            </body>
            </html>
        `;

        updateLoadingProgress(100, 'جاري التحميل...');

        // For now, we'll create an HTML file that can be printed as PDF
        const blob = new Blob([pdfContent], { type: 'text/html' });
        const link = document.createElement('a');
        link.download = `${props.template.name}-design.html`;
        link.href = URL.createObjectURL(blob);
        link.click();

        addNotification('تم إنشاء ملف HTML للطباعة كـ PDF', 'success');

    } catch (error) {
        console.error('PDF Export error:', error);
        addNotification('حدث خطأ أثناء تصدير PDF', 'error');
    } finally {
        hideLoading();
        showAdvancedMenu.value = false;
    }
};



// Predefined templates
const templates = {
    wedding: {
        html: `
            <div style="background: linear-gradient(135deg, #fdf2f8 0%, #fce7f3 100%); padding: 40px; text-align: center; min-height: 500px; display: flex; flex-direction: column; justify-content: center;">
                <div style="background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); max-width: 600px; margin: 0 auto;">
                    <div style="color: #ec4899; font-size: 48px; margin-bottom: 20px;">💕</div>
                    <h1 style="color: #be185d; font-size: 36px; margin-bottom: 20px; font-family: 'Cairo', Arial, sans-serif;" data-gjs-editable="true">مبارك الزواج</h1>
                    <p style="color: #9d174d; font-size: 24px; margin-bottom: 30px; font-family: 'Cairo', Arial, sans-serif;" data-gjs-editable="true">اسم العريس & اسم العروس</p>
                    <p style="color: #7c2d12; font-size: 18px; font-family: 'Cairo', Arial, sans-serif;" data-gjs-editable="true">التاريخ: __ / __ / ____</p>
                    <p style="color: #7c2d12; font-size: 18px; font-family: 'Cairo', Arial, sans-serif;" data-gjs-editable="true">المكان: _____________</p>
                </div>
            </div>
        `,
        css: `
            * { box-sizing: border-box; }
            body { margin: 0; font-family: 'Cairo', Arial, sans-serif; direction: rtl; }
        `
    },
    ramadan: {
        html: `
            <div style="background: linear-gradient(135deg, #064e3b 0%, #065f46 100%); padding: 40px; text-align: center; min-height: 500px; display: flex; flex-direction: column; justify-content: center;">
                <div style="background: rgba(255,255,255,0.1); padding: 40px; border-radius: 20px; backdrop-filter: blur(10px); max-width: 600px; margin: 0 auto; border: 1px solid rgba(255,255,255,0.2);">
                    <div style="color: #fbbf24; font-size: 48px; margin-bottom: 20px;">🌙</div>
                    <h1 style="color: #fbbf24; font-size: 36px; margin-bottom: 20px; font-family: 'Cairo', Arial, sans-serif;" data-gjs-editable="true">رمضان مبارك</h1>
                    <p style="color: #d1fae5; font-size: 20px; margin-bottom: 30px; font-family: 'Cairo', Arial, sans-serif;" data-gjs-editable="true">كل عام وأنتم بخير</p>
                    <p style="color: #a7f3d0; font-size: 16px; font-family: 'Cairo', Arial, sans-serif;" data-gjs-editable="true">تقبل الله منا ومنكم صالح الأعمال</p>
                </div>
            </div>
        `,
        css: `
            * { box-sizing: border-box; }
            body { margin: 0; font-family: 'Cairo', Arial, sans-serif; direction: rtl; }
        `
    },
    graduation: {
        html: `
            <div style="background: linear-gradient(135deg, #1e3a8a 0%, #3730a3 100%); padding: 40px; text-align: center; min-height: 500px; display: flex; flex-direction: column; justify-content: center;">
                <div style="background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); max-width: 600px; margin: 0 auto;">
                    <div style="color: #1e40af; font-size: 48px; margin-bottom: 20px;">🎓</div>
                    <h1 style="color: #1e40af; font-size: 36px; margin-bottom: 20px; font-family: 'Cairo', Arial, sans-serif;" data-gjs-editable="true">مبروك التخرج</h1>
                    <p style="color: #1e3a8a; font-size: 24px; margin-bottom: 30px; font-family: 'Cairo', Arial, sans-serif;" data-gjs-editable="true">اسم الخريج</p>
                    <p style="color: #374151; font-size: 18px; font-family: 'Cairo', Arial, sans-serif;" data-gjs-editable="true">التخصص: _____________</p>
                    <p style="color: #374151; font-size: 18px; font-family: 'Cairo', Arial, sans-serif;" data-gjs-editable="true">الجامعة: _____________</p>
                    <p style="color: #374151; font-size: 16px; font-family: 'Cairo', Arial, sans-serif;" data-gjs-editable="true">سنة التخرج: ____</p>
                </div>
            </div>
        `,
        css: `
            * { box-sizing: border-box; }
            body { margin: 0; font-family: 'Cairo', Arial, sans-serif; direction: rtl; }
        `
    },
    business: {
        html: `
            <div style="background: #f8fafc; padding: 40px; min-height: 500px; display: flex; align-items: center; justify-content: center;">
                <div style="background: white; padding: 40px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); max-width: 500px; width: 100%; border-left: 4px solid #3b82f6;">
                    <h1 style="color: #1f2937; font-size: 28px; margin-bottom: 10px; font-family: 'Cairo', Arial, sans-serif;" data-gjs-editable="true">اسم الشركة</h1>
                    <h2 style="color: #3b82f6; font-size: 22px; margin-bottom: 20px; font-family: 'Cairo', Arial, sans-serif;" data-gjs-editable="true">الاسم الكامل</h2>
                    <p style="color: #6b7280; font-size: 16px; margin-bottom: 10px; font-family: 'Cairo', Arial, sans-serif;" data-gjs-editable="true">المنصب: _____________</p>
                    <p style="color: #6b7280; font-size: 16px; margin-bottom: 10px; font-family: 'Cairo', Arial, sans-serif;" data-gjs-editable="true">الهاتف: _____________</p>
                    <p style="color: #6b7280; font-size: 16px; margin-bottom: 10px; font-family: 'Cairo', Arial, sans-serif;" data-gjs-editable="true">البريد الإلكتروني: _____________</p>
                    <p style="color: #6b7280; font-size: 16px; font-family: 'Cairo', Arial, sans-serif;" data-gjs-editable="true">العنوان: _____________</p>
                </div>
            </div>
        `,
        css: `
            * { box-sizing: border-box; }
            body { margin: 0; font-family: 'Cairo', Arial, sans-serif; direction: rtl; }
        `
    }
};

// Load template
const loadTemplate = (templateType) => {
    if (!editor || !templates[templateType]) return;

    if (confirm('هل أنت متأكد من تحميل هذا القالب؟ سيتم استبدال التصميم الحالي.')) {
        showLoading('جاري تحميل القالب...');

        const template = templates[templateType];

        // Clear current content
        editor.setComponents(template.html);
        editor.setStyle(template.css);

        hideLoading();
        addNotification('تم تحميل القالب بنجاح', 'success');
        showAdvancedMenu.value = false;
    }
};



// Keyboard shortcuts
const handleKeyDown = (event) => {
    // Ctrl+S or Cmd+S for save
    if ((event.ctrlKey || event.metaKey) && event.key === 's') {
        event.preventDefault();
        saveDesign();
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
};

// ENHANCED: Watchers for tab switching - PREVENTS CONTENT EMPTYING
watch(activeSidebarTab, (newTab, oldTab) => {
    console.log(`Switching from ${oldTab} to ${newTab}`);

    if (editor && newTab) {
        // Small delay to ensure DOM is ready
        nextTick(() => {
            setTimeout(() => {
                refreshPanels();

                // Additional check to ensure content is loaded
                setTimeout(() => {
                    const container = document.querySelector(`.${newTab === 'blocks' ? 'blocks' : newTab === 'layers' ? 'layers' : newTab === 'styles' ? 'styles' : 'traits'}-container`);
                    if (container && container.children.length === 0) {
                        console.log(`Container ${newTab} is empty, forcing re-initialization`);
                        initializePanels();
                    }
                }, 300);

            }, 50);
        });
    }
}, { immediate: false });

// Initialize on mount
onMounted(() => {
    nextTick(() => {
        initEditor();
        document.addEventListener('click', closeMenus);
        document.addEventListener('keydown', handleKeyDown);
    });
});

// Cleanup on unmount
onUnmounted(() => {
    document.removeEventListener('click', closeMenus);
    document.removeEventListener('keydown', handleKeyDown);
    stopAutoSave();

    if (editor) {
        editor.destroy();
    }
});
</script>

<style scoped>
/* Professional GrapesJS Styling */
#gjs {
    direction: ltr; /* GrapesJS needs LTR for proper functioning */
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
}

/* Glass Morphism Effects */
.bg-white\/95 {
    background: rgba(255, 255, 255, 0.95);
}

.bg-white\/10 {
    background: rgba(255, 255, 255, 0.1);
}

.backdrop-blur-md {
    backdrop-filter: blur(12px);
}

.backdrop-blur-sm {
    backdrop-filter: blur(6px);
}

/* Professional Animations */
@keyframes slideInLeft {
    from {
        transform: translateX(-100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes fadeInUp {
    from {
        transform: translateY(20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.animate__slideInLeft {
    animation: slideInLeft 0.3s ease-out;
}

.animate__fadeInUp {
    animation: fadeInUp 0.3s ease-out;
}

.animate__faster {
    animation-duration: 0.2s;
}

/* Professional GrapesJS Block Styling */
:deep(.gjs-pn-panel) {
    direction: rtl;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

:deep(.gjs-blocks-c) {
    direction: rtl;
    padding: 15px;
    background: transparent;
}

:deep(.gjs-block) {
    width: calc(50% - 8px);
    margin: 4px;
    padding: 15px 10px;
    border: 2px solid transparent;
    border-radius: 12px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    font-family: 'Cairo', Arial, sans-serif;
    font-size: 11px;
    font-weight: 600;
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    position: relative;
    overflow: hidden;
}

:deep(.gjs-block::before) {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(147, 51, 234, 0.1) 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
}

:deep(.gjs-block:hover) {
    border-color: #3b82f6;
    box-shadow: 0 8px 25px rgba(59, 130, 246, 0.25);
    transform: translateY(-2px) scale(1.02);
}

:deep(.gjs-block:hover::before) {
    opacity: 1;
}

:deep(.gjs-block-svg) {
    width: 100%;
    height: 32px;
    margin-bottom: 8px;
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
}

:deep(.gjs-block-label) {
    color: #374151;
    font-weight: 600;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

/* Professional Style Manager */
:deep(.gjs-sm-sector) {
    direction: rtl;
    margin-bottom: 20px;
    background: rgba(255, 255, 255, 0.8);
    border-radius: 12px;
    padding: 15px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.3);
}

:deep(.gjs-sm-title) {
    font-family: 'Cairo', Arial, sans-serif;
    font-weight: 700;
    color: #1f2937;
    padding: 12px 15px;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    border-radius: 8px;
    margin-bottom: 15px;
    border: 1px solid #e5e7eb;
    cursor: pointer;
    transition: all 0.2s ease;
}

:deep(.gjs-sm-title:hover) {
    background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%);
    transform: translateY(-1px);
}

:deep(.gjs-sm-property) {
    margin-bottom: 15px;
    padding: 10px;
    background: rgba(248, 250, 252, 0.5);
    border-radius: 8px;
    border: 1px solid rgba(226, 232, 240, 0.5);
}

:deep(.gjs-sm-label) {
    font-family: 'Cairo', Arial, sans-serif;
    color: #374151;
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 8px;
    display: block;
}

:deep(.gjs-field) {
    width: 100%;
    padding: 10px 12px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-family: 'Cairo', Arial, sans-serif;
    font-size: 14px;
    background: white;
    transition: all 0.2s ease;
}

:deep(.gjs-field:focus) {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    transform: translateY(-1px);
}

:deep(.gjs-field:hover) {
    border-color: #9ca3af;
}

/* Professional Canvas Styling */
:deep(.gjs-frame) {
    font-family: 'Cairo', 'Amiri', 'Noto Sans Arabic', Arial, sans-serif;
    border-radius: 12px;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    border: 2px solid rgba(255, 255, 255, 0.3);
}

:deep(.gjs-cv-canvas) {
    background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
    border-radius: 8px;
}

:deep(.gjs-cv-canvas__frames) {
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

/* Professional Layer Manager */
:deep(.gjs-lm-layer) {
    font-family: 'Cairo', Arial, sans-serif;
    padding: 12px 15px;
    border-radius: 8px;
    margin-bottom: 4px;
    cursor: pointer;
    transition: all 0.2s ease;
    background: rgba(248, 250, 252, 0.5);
    border: 1px solid rgba(226, 232, 240, 0.5);
    position: relative;
}

:deep(.gjs-lm-layer:hover) {
    background: rgba(59, 130, 246, 0.05);
    border-color: rgba(59, 130, 246, 0.2);
    transform: translateX(4px);
}

:deep(.gjs-lm-layer.gjs-lm-selected) {
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    color: #1d4ed8;
    border-color: #3b82f6;
    box-shadow: 0 2px 8px rgba(59, 130, 246, 0.15);
}

:deep(.gjs-lm-name) {
    font-weight: 600;
    color: #374151;
}

/* Professional Trait Manager */
:deep(.gjs-trt-trait) {
    margin-bottom: 20px;
    padding: 15px;
    background: rgba(255, 255, 255, 0.8);
    border-radius: 10px;
    border: 1px solid rgba(226, 232, 240, 0.5);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
}

:deep(.gjs-trt-label) {
    font-family: 'Cairo', Arial, sans-serif;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 8px;
    display: block;
    font-size: 13px;
}

/* Custom scrollbar */
:deep(.gjs-cv-canvas)::-webkit-scrollbar,
:deep(.panel__content)::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}

:deep(.gjs-cv-canvas)::-webkit-scrollbar-track,
:deep(.panel__content)::-webkit-scrollbar-track {
    background: #f1f1f1;
}

:deep(.gjs-cv-canvas)::-webkit-scrollbar-thumb,
:deep(.panel__content)::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

:deep(.gjs-cv-canvas)::-webkit-scrollbar-thumb:hover,
:deep(.panel__content)::-webkit-scrollbar-thumb:hover {
    background: #a1a1a1;
}

/* Block categories */
:deep(.gjs-block-category) {
    margin-bottom: 20px;
}

:deep(.gjs-block-category .gjs-title) {
    font-family: 'Cairo', Arial, sans-serif;
    font-weight: 600;
    color: #1f2937;
    padding: 8px 0;
    border-bottom: 1px solid #e5e7eb;
    margin-bottom: 10px;
}

/* Responsive design */
@media (max-width: 1024px) {
    .panel__right {
        width: 300px;
    }
}

@media (max-width: 768px) {
    .panel__right {
        width: 100%;
        position: fixed;
        z-index: 50;
    }
}
</style>
