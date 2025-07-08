<template>
    <Head :title="__('client.home')" />

    <ClientLayout>
        <!-- Hero Section -->
        <section class="relative bg-gradient-to-br from-indigo-900 via-purple-900 to-pink-800 text-white overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0 opacity-10">
                <svg class="w-full h-full" viewBox="0 0 400 400" fill="none">
                    <defs>
                        <pattern id="hero-pattern" x="0" y="0" width="40" height="40" patternUnits="userSpaceOnUse">
                            <path d="M0 20h40M20 0v40" stroke="currentColor" stroke-width="0.5"/>
                            <circle cx="20" cy="20" r="2" fill="currentColor"/>
                        </pattern>
                    </defs>
                    <rect width="100%" height="100%" fill="url(#hero-pattern)"/>
                </svg>
            </div>

            <!-- Floating Elements -->
            <div class="absolute top-20 left-10 w-20 h-20 bg-white/10 rounded-full blur-xl animate-pulse"></div>
            <div class="absolute bottom-20 right-10 w-32 h-32 bg-purple-500/20 rounded-full blur-2xl animate-pulse delay-1000"></div>

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                    <div class="text-right">
                        <div class="mb-6">
                            <span class="inline-block bg-white/20 backdrop-blur-sm text-white px-4 py-2 rounded-full text-sm font-medium mb-4">
                                ✨ منصة التصميم الأولى في المنطقة
                            </span>
                        </div>
                        <h1 class="text-5xl md:text-7xl font-black mb-8 leading-tight bg-gradient-to-r from-white to-blue-200 bg-clip-text text-transparent">
                            أنشئ بطاقاتك الإلكترونية بسهولة
                        </h1>
                        <p class="text-xl md:text-2xl mb-10 text-blue-100 leading-relaxed">
                            صمم بطاقات إلكترونية احترافية ومميزة باستخدام أدواتنا المتقدمة والذكية
                        </p>
                        <div class="flex flex-col sm:flex-row gap-6">
                            <Link
                                :href="route('client.templates')"
                                class="group bg-white text-purple-900 px-10 py-5 rounded-2xl font-bold text-lg hover:bg-blue-50 transition-all duration-300 inline-flex items-center justify-center shadow-2xl hover:shadow-white/25 transform hover:scale-105"
                            >
                                ابدأ التصميم الآن
                                <svg class="mr-3 w-6 h-6 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </Link>
                            <button class="group border-2 border-white/50 text-white px-10 py-5 rounded-2xl font-bold text-lg hover:bg-white/10 hover:border-white transition-all duration-300 backdrop-blur-sm">
                                شاهد الأمثلة
                                <svg class="mr-3 w-6 h-6 inline group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </button>
                        </div>

                        <!-- Stats -->
                        <div class="mt-16 grid grid-cols-3 gap-8">
                            <div class="text-center">
                                <div class="text-3xl font-bold text-white">+10K</div>
                                <div class="text-blue-200 text-sm">مستخدم نشط</div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-bold text-white">+50K</div>
                                <div class="text-blue-200 text-sm">تصميم منشأ</div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-bold text-white">+200</div>
                                <div class="text-blue-200 text-sm">قالب جاهز</div>
                            </div>
                        </div>
                    </div>

                    <div class="relative">
                        <!-- Main Card -->
                        <div class="relative bg-white/15 backdrop-blur-xl rounded-3xl p-8 shadow-2xl border border-white/20">
                            <div class="bg-white rounded-2xl p-8 shadow-xl transform rotate-3 hover:rotate-0 transition-transform duration-500">
                                <div class="h-56 bg-gradient-to-br from-purple-400 via-pink-500 to-red-500 rounded-xl flex items-center justify-center relative overflow-hidden">
                                    <div class="absolute inset-0 bg-black/20"></div>
                                    <div class="relative text-white text-center z-10">
                                        <div class="w-16 h-16 bg-white/30 rounded-full flex items-center justify-center mx-auto mb-4 backdrop-blur-sm">
                                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                            </svg>
                                        </div>
                                        <h3 class="font-bold text-xl">بطاقة عمل احترافية</h3>
                                        <p class="text-white/90 text-sm mt-2">تصميم أنيق ومميز</p>
                                    </div>
                                </div>
                                <div class="mt-6 text-center text-gray-700">
                                    <div class="flex items-center justify-center space-x-2 space-x-reverse">
                                        <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                                        <span class="text-sm font-medium">جاهز للتحميل</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Floating Cards -->
                            <div class="absolute -top-4 -right-4 bg-white/20 backdrop-blur-sm rounded-xl p-4 border border-white/30 transform rotate-12 hover:rotate-6 transition-transform duration-300">
                                <div class="w-12 h-8 bg-gradient-to-r from-blue-400 to-purple-500 rounded"></div>
                            </div>
                            <div class="absolute -bottom-4 -left-4 bg-white/20 backdrop-blur-sm rounded-xl p-4 border border-white/30 transform -rotate-12 hover:-rotate-6 transition-transform duration-300">
                                <div class="w-12 h-8 bg-gradient-to-r from-green-400 to-blue-500 rounded"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="py-24 bg-gradient-to-b from-gray-50 to-white relative">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-20">
                    <span class="inline-block bg-purple-100 text-purple-800 px-4 py-2 rounded-full text-sm font-semibold mb-4">
                        ✨ مميزات استثنائية
                    </span>
                    <h2 class="text-4xl md:text-5xl font-black text-gray-900 mb-6">
                        لماذا تختار بطاقاتي؟
                    </h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                        نقدم لك أفضل الأدوات والتقنيات لإنشاء تصاميم احترافية تعكس هويتك المميزة
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="group relative bg-white rounded-3xl p-8 shadow-lg hover:shadow-2xl transition-all duration-500 border border-gray-100 hover:border-purple-200 transform hover:-translate-y-2">
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-purple-50 rounded-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                        <div class="relative">
                            <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-4 text-center">
                                سهولة الاستخدام
                            </h3>
                            <p class="text-gray-600 text-center leading-relaxed">
                                واجهة بسيطة وسهلة تمكنك من إنشاء بطاقاتك في دقائق معدودة دون الحاجة لخبرة تقنية
                            </p>
                            <div class="mt-6 flex justify-center">
                                <div class="w-12 h-1 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Feature 2 -->
                    <div class="group relative bg-white rounded-3xl p-8 shadow-lg hover:shadow-2xl transition-all duration-500 border border-gray-100 hover:border-green-200 transform hover:-translate-y-2">
                        <div class="absolute inset-0 bg-gradient-to-br from-green-50 to-emerald-50 rounded-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                        <div class="relative">
                            <div class="w-20 h-20 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-4 text-center">
                                قوالب متنوعة
                            </h3>
                            <p class="text-gray-600 text-center leading-relaxed">
                                مجموعة واسعة من القوالب الاحترافية المصممة خصيصاً لجميع المناسبات والأعمال
                            </p>
                            <div class="mt-6 flex justify-center">
                                <div class="w-12 h-1 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Feature 3 -->
                    <div class="group relative bg-white rounded-3xl p-8 shadow-lg hover:shadow-2xl transition-all duration-500 border border-gray-100 hover:border-pink-200 transform hover:-translate-y-2">
                        <div class="absolute inset-0 bg-gradient-to-br from-pink-50 to-rose-50 rounded-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                        <div class="relative">
                            <div class="w-20 h-20 bg-gradient-to-br from-pink-500 to-rose-600 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-4 text-center">
                                جودة عالية
                            </h3>
                            <p class="text-gray-600 text-center leading-relaxed">
                                تصاميم احترافية بجودة عالية قابلة للطباعة والمشاركة الرقمية بأفضل دقة
                            </p>
                            <div class="mt-6 flex justify-center">
                                <div class="w-12 h-1 bg-gradient-to-r from-pink-500 to-rose-600 rounded-full"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Features -->
                <div class="mt-20 grid grid-cols-2 md:grid-cols-4 gap-8">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h4 class="font-bold text-gray-900 mb-2">متوافق مع الجوال</h4>
                        <p class="text-sm text-gray-600">يعمل على جميع الأجهزة</p>
                    </div>

                    <div class="text-center">
                        <div class="w-16 h-16 bg-gradient-to-br from-indigo-400 to-purple-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                            </svg>
                        </div>
                        <h4 class="font-bold text-gray-900 mb-2">تحميل فوري</h4>
                        <p class="text-sm text-gray-600">احصل على تصميمك فوراً</p>
                    </div>

                    <div class="text-center">
                        <div class="w-16 h-16 bg-gradient-to-br from-green-400 to-blue-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <h4 class="font-bold text-gray-900 mb-2">آمن ومحمي</h4>
                        <p class="text-sm text-gray-600">بياناتك في أمان تام</p>
                    </div>

                    <div class="text-center">
                        <div class="w-16 h-16 bg-gradient-to-br from-red-400 to-pink-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <h4 class="font-bold text-gray-900 mb-2">دعم 24/7</h4>
                        <p class="text-sm text-gray-600">نحن هنا لمساعدتك</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Templates Preview Section -->
        <section class="py-24 bg-white relative overflow-hidden">
            <!-- Background Elements -->
            <div class="absolute top-0 left-0 w-72 h-72 bg-purple-100 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
            <div class="absolute top-0 right-0 w-72 h-72 bg-yellow-100 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
            <div class="absolute -bottom-8 left-20 w-72 h-72 bg-pink-100 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-4000"></div>

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-20">
                    <span class="inline-block bg-gradient-to-r from-purple-600 to-pink-600 text-white px-6 py-3 rounded-full text-sm font-bold mb-6">
                        🎨 اكتشف قوالبنا المميزة
                    </span>
                    <h2 class="text-4xl md:text-5xl font-black text-gray-900 mb-6">
                        تصفح مجموعة القوالب الاحترافية
                    </h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                        اختر من بين مئات القوالب المصممة بعناية لتناسب جميع احتياجاتك المهنية والشخصية
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <!-- Template Card 1 -->
                    <div class="group relative bg-white rounded-3xl shadow-xl overflow-hidden hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-3">
                        <div class="h-56 bg-gradient-to-br from-blue-500 via-purple-600 to-indigo-700 flex items-center justify-center relative overflow-hidden">
                            <div class="absolute inset-0 bg-black/20"></div>
                            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                            <div class="relative text-white text-center z-10">
                                <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4 backdrop-blur-sm group-hover:scale-110 transition-transform duration-300">
                                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M20 6H4c-1.1 0-2 .9-2 2v8c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-5 3h4v2h-4V9zm0 4h4v2h-4v-2zM2 6h2v2H2V6zm0 4h2v2H2v-2zm0 4h2v2H2v-2z"/>
                                    </svg>
                                </div>
                                <h4 class="font-bold text-xl">بطاقات الأعمال</h4>
                                <p class="text-white/90 text-sm mt-2">تصاميم احترافية للشركات</p>
                            </div>
                            <div class="absolute bottom-4 right-4 bg-white/20 backdrop-blur-sm rounded-full px-3 py-1 text-white text-xs font-medium">
                                +50 قالب
                            </div>
                        </div>
                        <div class="p-6">
                            <Link :href="route('client.templates')" class="block w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white text-center py-3 rounded-2xl hover:from-blue-700 hover:to-purple-700 transition-all duration-300 font-bold group-hover:scale-105 transform">
                                استخدم الآن
                            </Link>
                        </div>
                    </div>

                    <!-- Template Card 2 -->
                    <div class="group relative bg-white rounded-3xl shadow-xl overflow-hidden hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-3">
                        <div class="h-56 bg-gradient-to-br from-pink-500 via-rose-600 to-red-700 flex items-center justify-center relative overflow-hidden">
                            <div class="absolute inset-0 bg-black/20"></div>
                            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                            <div class="relative text-white text-center z-10">
                                <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4 backdrop-blur-sm group-hover:scale-110 transition-transform duration-300">
                                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                    </svg>
                                </div>
                                <h4 class="font-bold text-xl">دعوات الزفاف</h4>
                                <p class="text-white/90 text-sm mt-2">تصاميم رومانسية مميزة</p>
                            </div>
                            <div class="absolute bottom-4 right-4 bg-white/20 backdrop-blur-sm rounded-full px-3 py-1 text-white text-xs font-medium">
                                +30 قالب
                            </div>
                        </div>
                        <div class="p-6">
                            <Link :href="route('client.templates')" class="block w-full bg-gradient-to-r from-pink-600 to-rose-600 text-white text-center py-3 rounded-2xl hover:from-pink-700 hover:to-rose-700 transition-all duration-300 font-bold group-hover:scale-105 transform">
                                استخدم الآن
                            </Link>
                        </div>
                    </div>

                    <!-- Template Card 3 -->
                    <div class="group relative bg-white rounded-3xl shadow-xl overflow-hidden hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-3">
                        <div class="h-56 bg-gradient-to-br from-green-500 via-emerald-600 to-teal-700 flex items-center justify-center relative overflow-hidden">
                            <div class="absolute inset-0 bg-black/20"></div>
                            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                            <div class="relative text-white text-center z-10">
                                <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4 backdrop-blur-sm group-hover:scale-110 transition-transform duration-300">
                                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                </div>
                                <h4 class="font-bold text-xl">بطاقات المناسبات</h4>
                                <p class="text-white/90 text-sm mt-2">لجميع المناسبات الخاصة</p>
                            </div>
                            <div class="absolute bottom-4 right-4 bg-white/20 backdrop-blur-sm rounded-full px-3 py-1 text-white text-xs font-medium">
                                +40 قالب
                            </div>
                        </div>
                        <div class="p-6">
                            <Link :href="route('client.templates')" class="block w-full bg-gradient-to-r from-green-600 to-emerald-600 text-white text-center py-3 rounded-2xl hover:from-green-700 hover:to-emerald-700 transition-all duration-300 font-bold group-hover:scale-105 transform">
                                استخدم الآن
                            </Link>
                        </div>
                    </div>

                    <!-- Template Card 4 -->
                    <div class="group relative bg-white rounded-3xl shadow-xl overflow-hidden hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-3">
                        <div class="h-56 bg-gradient-to-br from-purple-500 via-violet-600 to-indigo-700 flex items-center justify-center relative overflow-hidden">
                            <div class="absolute inset-0 bg-black/20"></div>
                            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                            <div class="relative text-white text-center z-10">
                                <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4 backdrop-blur-sm group-hover:scale-110 transition-transform duration-300">
                                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M18 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM6 4h5v8l-2.5-1.5L6 12V4z"/>
                                    </svg>
                                </div>
                                <h4 class="font-bold text-xl">بطاقات التهنئة</h4>
                                <p class="text-white/90 text-sm mt-2">تصاميم متنوعة للتهاني</p>
                            </div>
                            <div class="absolute bottom-4 right-4 bg-white/20 backdrop-blur-sm rounded-full px-3 py-1 text-white text-xs font-medium">
                                +25 قالب
                            </div>
                        </div>
                        <div class="p-6">
                            <Link :href="route('client.templates')" class="block w-full bg-gradient-to-r from-purple-600 to-violet-600 text-white text-center py-3 rounded-2xl hover:from-purple-700 hover:to-violet-700 transition-all duration-300 font-bold group-hover:scale-105 transform">
                                استخدم الآن
                            </Link>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-16">
                    <Link :href="route('client.templates')" class="group inline-flex items-center bg-gradient-to-r from-purple-600 to-pink-600 text-white px-10 py-4 rounded-2xl font-bold text-lg hover:from-purple-700 hover:to-pink-700 transition-all duration-300 shadow-xl hover:shadow-2xl transform hover:scale-105">
                        تصفح جميع القوالب
                        <svg class="mr-3 w-6 h-6 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </Link>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="relative py-24 bg-gradient-to-br from-indigo-900 via-purple-900 to-pink-800 text-white overflow-hidden">
            <!-- Background Elements -->
            <div class="absolute inset-0 opacity-20">
                <svg class="w-full h-full" viewBox="0 0 400 400" fill="none">
                    <defs>
                        <pattern id="cta-pattern" x="0" y="0" width="40" height="40" patternUnits="userSpaceOnUse">
                            <path d="M0 20h40M20 0v40" stroke="currentColor" stroke-width="0.5"/>
                            <circle cx="20" cy="20" r="1.5" fill="currentColor"/>
                        </pattern>
                    </defs>
                    <rect width="100%" height="100%" fill="url(#cta-pattern)"/>
                </svg>
            </div>

            <!-- Floating Elements -->
            <div class="absolute top-10 left-10 w-32 h-32 bg-white/10 rounded-full blur-2xl animate-pulse"></div>
            <div class="absolute bottom-10 right-10 w-40 h-40 bg-purple-500/20 rounded-full blur-3xl animate-pulse delay-1000"></div>

            <div class="relative max-w-6xl mx-auto text-center px-4 sm:px-6 lg:px-8">
                <div class="mb-8">
                    <span class="inline-block bg-white/20 backdrop-blur-sm text-white px-6 py-3 rounded-full text-sm font-bold mb-6">
                        🚀 ابدأ رحلتك الإبداعية
                    </span>
                </div>

                <h2 class="text-5xl md:text-7xl font-black mb-8 leading-tight">
                    <span class="bg-gradient-to-r from-white to-blue-200 bg-clip-text text-transparent">
                        ابدأ في إنشاء
                    </span>
                    <br>
                    <span class="text-white">
                        بطاقتك الآن
                    </span>
                </h2>

                <p class="text-2xl mb-12 text-blue-100 leading-relaxed max-w-4xl mx-auto">
                    انضم إلى أكثر من <span class="font-bold text-white">10,000 مستخدم</span> يثقون في منصتنا لإنشاء بطاقاتهم الإلكترونية الاحترافية
                </p>

                <div class="flex flex-col sm:flex-row gap-6 justify-center items-center mb-12">
                    <Link
                        :href="$page.props.auth.user ? route('client.templates') : route('register')"
                        class="group bg-white text-purple-900 px-12 py-5 rounded-2xl font-black text-xl hover:bg-blue-50 transition-all duration-300 inline-flex items-center shadow-2xl hover:shadow-white/25 transform hover:scale-105"
                    >
                        {{ $page.props.auth.user ? 'ابدأ التصميم' : 'إنشاء حساب مجاني' }}
                        <svg class="mr-3 w-7 h-7 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </Link>

                    <Link
                        :href="route('client.templates')"
                        class="group border-2 border-white/50 text-white px-12 py-5 rounded-2xl font-bold text-xl hover:bg-white/10 hover:border-white transition-all duration-300 backdrop-blur-sm"
                    >
                        تصفح القوالب
                        <svg class="mr-3 w-6 h-6 inline group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </Link>
                </div>

                <!-- Trust Indicators -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-4xl mx-auto">
                    <div class="flex items-center justify-center space-x-3 space-x-reverse">
                        <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-white">آمن 100%</div>
                            <div class="text-blue-200 text-sm">بياناتك محمية</div>
                        </div>
                    </div>

                    <div class="flex items-center justify-center space-x-3 space-x-reverse">
                        <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-white">جودة عالية</div>
                            <div class="text-blue-200 text-sm">تصاميم احترافية</div>
                        </div>
                    </div>

                    <div class="flex items-center justify-center space-x-3 space-x-reverse">
                        <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-white">دعم مستمر</div>
                            <div class="text-blue-200 text-sm">نحن هنا لمساعدتك</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </ClientLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ClientLayout from '@/Layouts/ClientLayout.vue';
import { useTranslations } from '@/Composables/useTranslations';

const { __, isRTL } = useTranslations();
</script>

<style scoped>
@keyframes blob {
  0% {
    transform: translate(0px, 0px) scale(1);
  }
  33% {
    transform: translate(30px, -50px) scale(1.1);
  }
  66% {
    transform: translate(-20px, 20px) scale(0.9);
  }
  100% {
    transform: translate(0px, 0px) scale(1);
  }
}

.animate-blob {
  animation: blob 7s infinite;
}

.animation-delay-2000 {
  animation-delay: 2s;
}

.animation-delay-4000 {
  animation-delay: 4s;
}

/* Custom gradient text */
.bg-clip-text {
  -webkit-background-clip: text;
  background-clip: text;
}

/* Smooth transitions */
* {
  transition-property: transform, opacity, background-color, border-color, color, fill, stroke;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

/* Hover effects */
.group:hover .group-hover\:translate-x-1 {
  transform: translateX(0.25rem);
}

.group:hover .group-hover\:scale-110 {
  transform: scale(1.1);
}

.group:hover .group-hover\:scale-105 {
  transform: scale(1.05);
}

/* RTL specific adjustments */
[dir="rtl"] .group:hover .group-hover\:translate-x-1 {
  transform: translateX(-0.25rem);
}
</style>
