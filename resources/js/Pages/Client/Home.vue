<template>
    <Head title="الرئيسية - سامقة" />

    <ClientLayout>
        <!-- Main Container with Gradient Background -->
        <div class="min-h-screen bg-gradient-to-br from-purple-600 via-purple-700 to-pink-600 relative overflow-hidden">

            <!-- Decorative Elements -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <!-- Floating geometric shapes -->
                <div class="absolute top-20 left-10 w-32 h-32 bg-white/10 rounded-full blur-xl animate-float-slow"></div>
                <div class="absolute bottom-20 right-10 w-40 h-40 bg-white/5 rounded-full blur-2xl animate-float-slower"></div>
                <div class="absolute top-1/2 left-1/4 w-24 h-24 bg-white/15 rounded-full blur-lg animate-float"></div>

                <!-- Grid pattern overlay -->
                <div class="absolute inset-0 opacity-10">
                    <div class="w-full h-full" style="background-image: radial-gradient(circle at 2px 2px, rgba(255, 255, 255, 0.3) 1px, transparent 0); background-size: 50px 50px;"></div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="relative z-10 flex items-center justify-center min-h-screen px-4 py-8">
                <div class="max-w-4xl mx-auto text-center text-white">

                    <!-- Logo/Icon -->
                    <div class="mb-8 animate-fade-in-up" style="animation-delay: 0.2s;">
                        <div class="w-20 h-20 mx-auto bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center mb-6">
                            <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Welcome Message -->
                    <div class="mb-8 animate-fade-in-up" style="animation-delay: 0.4s;">
                        <h1 class="text-4xl md:text-6xl font-black mb-4 leading-tight">
                            أهلاً بك في سَامِقَة
                        </h1>
                        <p class="text-lg md:text-xl text-white/90 font-light leading-relaxed max-w-2xl mx-auto">
                            منصة سامقة توفر لك أدوات متقدمة وقوالب احترافية لتصميم بطاقات إلكترونية مميزة
                        </p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mb-12 animate-fade-in-up" style="animation-delay: 0.6s;">
                        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                            <!-- Primary Button -->
                            <Link
                                :href="route('client.templates')"
                                class="group bg-white/20 backdrop-blur-sm text-white px-8 py-4 rounded-xl font-bold text-lg border border-white/30 hover:bg-white/30 hover:border-white/50 transition-all duration-300 transform hover:scale-105 flex items-center space-x-3 space-x-reverse"
                            >
                                <svg class="w-5 h-5 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                <span>ابدأ التصميم الآن</span>
                            </Link>

                            <!-- Secondary Button -->
                            <Link
                                :href="route('client.pricing')"
                                class="group bg-transparent text-white px-8 py-4 rounded-xl font-bold text-lg border-2 border-white/50 hover:bg-white/10 hover:border-white transition-all duration-300 transform hover:scale-105 flex items-center space-x-3 space-x-reverse"
                            >
                                <svg class="w-5 h-5 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                </svg>
                                <span>تصفح الأسعار</span>
                            </Link>
                        </div>
                    </div>

                    <!-- Categories Section -->
                    <div class="animate-fade-in-up" style="animation-delay: 0.8s;">
                        <div class="grid grid-cols-3 md:grid-cols-6 gap-4 max-w-3xl mx-auto">
                            <Link
                                v-for="(category, index) in categories.slice(0, 6)"
                                :key="category.id"
                                :href="route('client.templates', { category: category.id })"
                                class="group bg-white/20 backdrop-blur-sm rounded-xl p-4 hover:bg-white/30 transition-all duration-300 border border-white/20 hover:border-white/40 transform hover:scale-105"
                                :style="`animation-delay: ${1 + index * 0.1}s;`"
                            >
                                <div class="flex flex-col items-center space-y-2 text-center">
                                    <!-- Icon -->
                                    <div class="w-12 h-12 bg-white/30 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                        </svg>
                                    </div>

                                    <!-- Category Name -->
                                    <h3 class="font-semibold text-white text-sm group-hover:text-white/90 transition-colors">
                                        {{ category.name }}
                                    </h3>

                                    <!-- Templates Count -->
                                    <span class="text-xs text-white/70 bg-white/20 px-2 py-1 rounded-full">
                                        {{ category.active_templates_count || 0 }}
                                    </span>
                                </div>
                            </Link>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </ClientLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ClientLayout from '@/Layouts/ClientLayout.vue';

// Define props
defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    categories: {
        type: Array,
        default: () => []
    }
});
</script>

<style scoped>
/* ===== KEYFRAME ANIMATIONS ===== */

/* Fade in with upward movement */
@keyframes fade-in-up {
    0% {
        opacity: 0;
        transform: translateY(20px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Floating animations for decorative elements */
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

@keyframes float-slow {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-15px) rotate(2deg); }
}

@keyframes float-slower {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-8px) rotate(-1deg); }
}

/* ===== ANIMATION CLASSES ===== */

.animate-fade-in-up {
    opacity: 0;
    animation: fade-in-up 0.6s ease-out forwards;
}

.animate-float {
    animation: float 6s ease-in-out infinite;
}

.animate-float-slow {
    animation: float-slow 8s ease-in-out infinite;
}

.animate-float-slower {
    animation: float-slower 12s ease-in-out infinite;
}

/* ===== RESPONSIVE DESIGN ===== */

@media (max-width: 768px) {
    h1 {
        font-size: 2.5rem !important;
        line-height: 1.1 !important;
    }

    p {
        font-size: 1rem !important;
    }

    .grid.md\\:grid-cols-6 {
        grid-template-columns: repeat(3, 1fr) !important;
    }
}

@media (max-width: 640px) {
    .grid.grid-cols-3 {
        grid-template-columns: repeat(2, 1fr) !important;
    }

    .flex.sm\\:flex-row {
        flex-direction: column !important;
    }
}

/* ===== ACCESSIBILITY ===== */

.group:focus-visible {
    outline: 2px solid rgba(255, 255, 255, 0.8);
    outline-offset: 2px;
    border-radius: 0.75rem;
}

/* Reduce motion for users who prefer it */
@media (prefers-reduced-motion: reduce) {
    * {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}

/* ===== BACKDROP BLUR FALLBACK ===== */

@supports not (backdrop-filter: blur(10px)) {
    .backdrop-blur-sm {
        background-color: rgba(255, 255, 255, 0.2) !important;
    }
}

/* ===== PERFORMANCE OPTIMIZATIONS ===== */

.group {
    will-change: transform;
}
</style>
