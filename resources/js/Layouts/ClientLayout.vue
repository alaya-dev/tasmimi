<template>
    <div :dir="direction" class="min-h-screen bg-gray-50">
        <!-- Navigation Header -->
        <nav class="bg-white/95 backdrop-blur-md shadow-xl border-b border-gray-200/50 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-20">
                    <!-- Auth Links (moved to left) -->
                    <div class="flex items-center">
                        <div v-if="!$page.props.auth.user" class="flex items-center space-x-4 space-x-reverse">
                            <Link :href="route('login')" class="group flex items-center text-gray-700 hover:text-purple-600 font-semibold transition-all duration-200 px-4 py-2 rounded-xl hover:bg-purple-50">
                                تسجيل الدخول
                                <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                </svg>
                            </Link>
                            <Link :href="route('register')" class="group flex items-center bg-gradient-to-r from-purple-600 to-pink-600 text-white px-6 py-3 rounded-2xl hover:from-purple-700 hover:to-pink-700 transition-all duration-300 font-bold shadow-lg hover:shadow-xl transform hover:scale-105">
                                إنشاء حساب
                                <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                </svg>
                            </Link>
                        </div>
                        <!-- User menu for authenticated users (moved to left) -->
                        <div v-else class="flex items-center space-x-4 space-x-reverse">
                            <!-- Cart Icon -->
                            <Link :href="route('client.cart')" class="relative p-3 text-gray-600 hover:text-purple-600 transition-colors duration-200 hover:bg-purple-50 rounded-xl group">
                                <svg class="w-6 h-6 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l-1.5-6m0 0h15.5M17 21a2 2 0 100-4 2 2 0 000 4zM9 21a2 2 0 100-4 2 2 0 000 4z"/>
                                </svg>
                                <span v-if="cartCount > 0" class="absolute -top-1 -left-1 bg-gradient-to-r from-red-500 to-pink-500 text-white text-xs rounded-full h-6 w-6 flex items-center justify-center font-bold animate-pulse">
                                    {{ cartCount }}
                                </span>
                            </Link>

                            <!-- User Dropdown -->
                            <Dropdown align="left" width="48">
                                <template #trigger>
                                    <button class="flex items-center text-sm font-medium text-gray-700 hover:text-purple-600 transition-colors duration-200 group">
                                        <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center ml-2 group-hover:scale-110 transition-transform duration-200">
                                            <span class="text-white font-bold text-lg">{{ $page.props.auth.user.email.charAt(0).toUpperCase() }}</span>
                                        </div>
                                        <svg class="w-5 h-5 group-hover:rotate-180 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>
                                </template>

                                <template #content>
                                    <DropdownLink :href="route('client.profile')" class="flex items-center space-x-2 space-x-reverse">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        <span>الملف الشخصي</span>
                                    </DropdownLink>
                                    <DropdownLink :href="route('client.orders')" class="flex items-center space-x-2 space-x-reverse">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                        </svg>
                                        <span>الطلبات</span>
                                    </DropdownLink>
                                    <DropdownLink :href="route('client.invoices')" class="flex items-center space-x-2 space-x-reverse">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <span>الفواتير</span>
                                    </DropdownLink>
                                    <hr class="my-1">
                                    <DropdownLink :href="route('logout')" method="post" as="button" class="flex items-center space-x-2 space-x-reverse text-red-600 hover:text-red-800">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        <span>تسجيل الخروج</span>
                                    </DropdownLink>
                                </template>
                            </Dropdown>
                        </div>
                    </div>

                    <!-- Desktop Navigation -->
                    <div class="hidden md:flex items-center space-x-8 space-x-reverse flex-1 justify-center">
                        <NavLink :href="route('client.home')" :active="route().current('client.home')" class="text-gray-700 hover:text-purple-600 font-semibold transition-colors duration-200 relative group">
                            الرئيسية
                            <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-purple-600 to-pink-600 group-hover:w-full transition-all duration-300"></span>
                        </NavLink>
                        <NavLink :href="route('client.templates')" :active="route().current('client.templates')" class="text-gray-700 hover:text-purple-600 font-semibold transition-colors duration-200 relative group">
                            القوالب
                            <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-purple-600 to-pink-600 group-hover:w-full transition-all duration-300"></span>
                        </NavLink>
                        <NavLink :href="route('client.my-designs')" :active="route().current('client.my-designs')" class="text-gray-700 hover:text-purple-600 font-semibold transition-colors duration-200 relative group">
                            تصاميمي
                            <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-purple-600 to-pink-600 group-hover:w-full transition-all duration-300"></span>
                        </NavLink>
                        <NavLink :href="route('client.pricing')" :active="route().current('client.pricing')" class="text-gray-700 hover:text-purple-600 font-semibold transition-colors duration-200 relative group">
                            الأسعار
                            <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-purple-600 to-pink-600 group-hover:w-full transition-all duration-300"></span>
                        </NavLink>
                        <NavLink v-if="$page.props.auth.user" :href="route('client.contact')" :active="route().current('client.contact')" class="text-gray-700 hover:text-purple-600 font-semibold transition-colors duration-200 relative group">
                            اتصل بنا
                            <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-purple-600 to-pink-600 group-hover:w-full transition-all duration-300"></span>
                        </NavLink>
                    </div>

                    <!-- Logo and Brand (moved to right) -->
                    <div class="flex items-center">
                        <Link :href="route('client.home')" class="flex items-center group">
                            <span class="text-3xl font-black bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent ml-3">سامقة</span>
                            <div class="w-12 h-12 bg-gradient-to-br from-purple-600 to-pink-600 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                            </div>
                        </Link>

                        <!-- Mobile menu button -->
                        <button @click="showMobileMenu = !showMobileMenu" class="md:hidden ml-4 p-2 text-gray-600 hover:text-blue-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path v-if="!showMobileMenu" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Navigation -->
            <div v-show="showMobileMenu" class="md:hidden bg-white border-t border-gray-200">
                <div class="px-4 py-2 space-y-1">
                    <MobileNavLink :href="route('client.home')" :active="route().current('client.home')">
                        {{ __('client.home') }}
                    </MobileNavLink>
                    <MobileNavLink :href="route('client.templates')" :active="route().current('client.templates')">
                        {{ __('client.templates') }}
                    </MobileNavLink>
                    <MobileNavLink :href="route('client.my-designs')" :active="route().current('client.my-designs')">
                        {{ __('client.my_designs') }}
                    </MobileNavLink>
                    <MobileNavLink :href="route('client.pricing')" :active="route().current('client.pricing')">
                        {{ __('client.pricing') }}
                    </MobileNavLink>

                    <div v-if="$page.props.auth.user" class="pt-2 border-t border-gray-200">
                        <MobileNavLink :href="route('client.contact')" :active="route().current('client.contact')">
                            اتصل بنا
                        </MobileNavLink>
                    </div>
                    
                    <div v-if="!$page.props.auth.user" class="pt-4 border-t border-gray-200">
                        <MobileNavLink :href="route('login')">
                            {{ __('auth.login') }}
                        </MobileNavLink>
                        <MobileNavLink :href="route('register')">
                            {{ __('auth.register') }}
                        </MobileNavLink>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="flex-1">
            <slot />
        </main>

        <!-- Footer -->
        <footer class="bg-gradient-to-br from-gray-900 via-purple-900 to-indigo-900 text-white relative overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0 opacity-10">
                <svg class="w-full h-full" viewBox="0 0 400 400" fill="none">
                    <defs>
                        <pattern id="footer-pattern" x="0" y="0" width="40" height="40" patternUnits="userSpaceOnUse">
                            <path d="M0 20h40M20 0v40" stroke="currentColor" stroke-width="0.5"/>
                            <circle cx="20" cy="20" r="1" fill="currentColor"/>
                        </pattern>
                    </defs>
                    <rect width="100%" height="100%" fill="url(#footer-pattern)"/>
                </svg>
            </div>

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-12">
                    <!-- Brand -->
                    <div class="col-span-1 md:col-span-2">
                        <div class="flex items-center mb-6">
                            <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center mr-4">
                                <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                            </div>
                            <span class="text-3xl font-black bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">سامقة</span>
                        </div>
                        <p class="text-gray-300 mb-6 text-lg leading-relaxed">
                            منصة متقدمة لإنشاء البطاقات الإلكترونية بتصاميم احترافية ومميزة تعكس هويتك الفريدة
                        </p>

                        <!-- Social Links -->
                        <div class="flex space-x-4 space-x-reverse">
                            <a href="#" class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center hover:bg-white/20 transition-colors duration-200 group">
                                <svg class="w-5 h-5 group-hover:scale-110 transition-transform duration-200" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                                </svg>
                            </a>
                            <a href="#" class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center hover:bg-white/20 transition-colors duration-200 group">
                                <svg class="w-5 h-5 group-hover:scale-110 transition-transform duration-200" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"/>
                                </svg>
                            </a>
                            <a href="#" class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center hover:bg-white/20 transition-colors duration-200 group">
                                <svg class="w-5 h-5 group-hover:scale-110 transition-transform duration-200" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.174-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.085.345-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.402.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.357-.629-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24.009 12.017 24.009c6.624 0 11.99-5.367 11.99-11.988C24.007 5.367 18.641.001.012.001z"/>
                                </svg>
                            </a>
                            <a href="#" class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center hover:bg-white/20 transition-colors duration-200 group">
                                <svg class="w-5 h-5 group-hover:scale-110 transition-transform duration-200" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12.036 6.24c-3.315 0-6.012 2.697-6.012 6.012s2.697 6.012 6.012 6.012 6.012-2.697 6.012-6.012-2.697-6.012-6.012-6.012zm0 9.939c-2.172 0-3.927-1.755-3.927-3.927s1.755-3.927 3.927-3.927 3.927 1.755 3.927 3.927-1.755 3.927-3.927 3.927zm7.695-10.188c0 .774-.627 1.401-1.401 1.401s-1.401-.627-1.401-1.401.627-1.401 1.401-1.401 1.401.627 1.401 1.401zm3.978 1.422c-.089-1.88-.518-3.547-1.894-4.923-1.376-1.376-3.043-1.805-4.923-1.894-1.942-.11-7.766-.11-9.708 0-1.88.089-3.547.518-4.923 1.894-1.376 1.376-1.805 3.043-1.894 4.923-.11 1.942-.11 7.766 0 9.708.089 1.88.518 3.547 1.894 4.923 1.376 1.376 3.043 1.805 4.923 1.894 1.942.11 7.766.11 9.708 0 1.88-.089 3.547-.518 4.923-1.894 1.376-1.376 1.805-3.043 1.894-4.923.11-1.942.11-7.766 0-9.708zm-2.496 11.83c-.406 1.021-1.191 1.806-2.212 2.212-1.531.607-5.161.467-6.851.467s-5.32.14-6.851-.467c-1.021-.406-1.806-1.191-2.212-2.212-.607-1.531-.467-5.161-.467-6.851s-.14-5.32.467-6.851c.406-1.021 1.191-1.806 2.212-2.212 1.531-.607 5.161-.467 6.851-.467s5.32-.14 6.851.467c1.021.406 1.806 1.191 2.212 2.212.607 1.531.467 5.161.467 6.851s.14 5.32-.467 6.851z"/>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div>
                        <h3 class="text-xl font-bold mb-6 text-white">روابط سريعة</h3>
                        <ul class="space-y-3">
                            <li><Link :href="route('client.templates')" class="text-gray-300 hover:text-white transition-colors duration-200 flex items-center group">
                                <svg class="w-4 h-4 mr-2 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                                القوالب
                            </Link></li>
                            <li><Link :href="route('client.pricing')" class="text-gray-300 hover:text-white transition-colors duration-200 flex items-center group">
                                <svg class="w-4 h-4 mr-2 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                                الأسعار
                            </Link></li>
                            <li v-if="$page.props.auth.user"><Link :href="route('client.my-designs')" class="text-gray-300 hover:text-white transition-colors duration-200 flex items-center group">
                                <svg class="w-4 h-4 mr-2 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                                تصاميمي
                            </Link></li>
                            <li v-if="$page.props.auth.user"><Link :href="route('client.contact')" class="text-gray-300 hover:text-white transition-colors duration-200 flex items-center group">
                                <svg class="w-4 h-4 mr-2 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                                اتصل بنا
                            </Link></li>
                        </ul>
                    </div>

                    <!-- Support -->
                    <div>
                        <h3 class="text-xl font-bold mb-6 text-white">الدعم والمساعدة</h3>
                        <ul class="space-y-3">
                            <li><a href="#" class="text-gray-300 hover:text-white transition-colors duration-200 flex items-center group">
                                <svg class="w-4 h-4 mr-2 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                                اتصل بنا
                            </a></li>
                            <li><a href="#" class="text-gray-300 hover:text-white transition-colors duration-200 flex items-center group">
                                <svg class="w-4 h-4 mr-2 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                                مركز المساعدة
                            </a></li>
                            <li><a href="#" class="text-gray-300 hover:text-white transition-colors duration-200 flex items-center group">
                                <svg class="w-4 h-4 mr-2 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                                سياسة الخصوصية
                            </a></li>
                            <li><a href="#" class="text-gray-300 hover:text-white transition-colors duration-200 flex items-center group">
                                <svg class="w-4 h-4 mr-2 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                                شروط الاستخدام
                            </a></li>
                        </ul>
                    </div>
                </div>

                <div class="border-t border-white/20 mt-12 pt-8 text-center">
                    <p class="text-gray-300 text-lg">
                        &copy; {{ new Date().getFullYear() }} <span class="font-bold text-white">سامقة</span> - جميع الحقوق محفوظة
                    </p>
                    <p class="text-gray-400 text-sm mt-2">
                        صُنع بـ ❤️ في المملكة العربية السعودية
                    </p>
                </div>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import MobileNavLink from '@/Components/MobileNavLink.vue';
import { Link } from '@inertiajs/vue3';
import { useTranslations } from '@/Composables/useTranslations';

// Props
defineProps({
    cartCount: {
        type: Number,
        default: 0
    }
});

// Composables
const { __, direction } = useTranslations();

// State
const showMobileMenu = ref(false);
</script>
