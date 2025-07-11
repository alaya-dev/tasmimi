<template>
    <Head title="الرئيسية - سامقة" />

    <ClientLayout>
        <!-- Hero Section avec description du site -->
        <section class="relative bg-gradient-to-br from-indigo-900 via-purple-900 to-pink-800 text-white overflow-hidden py-16">
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
            <div class="absolute top-10 left-10 w-20 h-20 bg-white/10 rounded-full blur-xl animate-pulse"></div>
            <div class="absolute bottom-10 right-10 w-32 h-32 bg-purple-500/20 rounded-full blur-2xl animate-pulse delay-1000"></div>

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <div class="mb-6">
                    <span class="inline-block bg-white/20 backdrop-blur-sm text-white px-6 py-3 rounded-full text-sm font-bold mb-6">
                        ✨ منصة التصميم الرائدة في المنطقة
                    </span>
                </div>
                <h1 class="text-4xl md:text-6xl font-black mb-6 leading-tight bg-gradient-to-r from-white to-blue-200 bg-clip-text text-transparent">
                    أنشئ بطاقاتك الإلكترونية بسهولة
                </h1>
                <p class="text-xl md:text-2xl mb-8 text-blue-100 leading-relaxed max-w-4xl mx-auto">
                    منصة سامقة توفر لك أدوات متقدمة وقوالب احترافية لتصميم بطاقات إلكترونية مميزة تعكس هويتك الشخصية والمهنية بأعلى جودة
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    <Link
                        :href="route('client.templates')"
                        class="group bg-white text-purple-900 px-8 py-4 rounded-2xl font-bold text-lg hover:bg-blue-50 transition-all duration-300 inline-flex items-center shadow-2xl hover:shadow-white/25 transform hover:scale-105"
                    >
                        ابدأ التصميم الآن
                        <svg class="mr-3 w-6 h-6 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </Link>
                    <Link
                        :href="route('client.pricing')"
                        class="group border-2 border-white/50 text-white px-8 py-4 rounded-2xl font-bold text-lg hover:bg-white/10 hover:border-white transition-all duration-300 backdrop-blur-sm"
                    >
                        تصفح الأسعار
                    </Link>
                </div>
            </div>
        </section>

        <!-- Section principale avec design professionnel -->
        <section class="py-16 bg-gradient-to-b from-gray-50 to-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Boutons de catégories principales avec design amélioré -->
                <div class="mb-12">
                    <div class="flex flex-wrap gap-4 justify-center">
                        <button
                            @click="selectedCategory = 'all'"
                            :class="selectedCategory === 'all' ? 'bg-gradient-to-r from-purple-600 to-pink-600 text-white shadow-lg' : 'bg-white text-gray-700 hover:bg-gray-50 border-2 border-gray-200'"
                            class="px-8 py-4 rounded-2xl font-bold transition-all duration-300 transform hover:scale-105"
                        >
                            الكل
                        </button>
                        <button
                            @click="selectedCategory = 'couples'"
                            :class="selectedCategory === 'couples' ? 'bg-gradient-to-r from-purple-600 to-pink-600 text-white shadow-lg' : 'bg-white text-gray-700 hover:bg-gray-50 border-2 border-gray-200'"
                            class="px-8 py-4 rounded-2xl font-bold transition-all duration-300 transform hover:scale-105"
                        >
                            ازواج
                        </button>
                        <button
                            @click="selectedCategory = 'preview'"
                            :class="selectedCategory === 'preview' ? 'bg-gradient-to-r from-purple-600 to-pink-600 text-white shadow-lg' : 'bg-white text-gray-700 hover:bg-gray-50 border-2 border-gray-200'"
                            class="px-8 py-4 rounded-2xl font-bold transition-all duration-300 transform hover:scale-105"
                        >
                            معاينة
                        </button>
                        <button class="px-8 py-4 rounded-2xl border-2 border-dashed border-gray-300 bg-gray-50 text-gray-400 cursor-not-allowed">
                            قريباً
                        </button>
                        <button class="px-8 py-4 rounded-2xl border-2 border-dashed border-gray-300 bg-gray-50 text-gray-400 cursor-not-allowed">
                            قريباً
                        </button>
                    </div>
                </div>

                <!-- Filtres / Sous-catégories مع تصميم محسن -->
                <div class="mb-12">
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        <div class="h-16 bg-white border-2 border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 flex items-center justify-center">
                            <div class="w-8 h-8 bg-gray-200 rounded-full"></div>
                        </div>
                        <div class="h-16 bg-white border-2 border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 flex items-center justify-center">
                            <div class="w-8 h-8 bg-gray-200 rounded-full"></div>
                        </div>
                        <div class="h-16 bg-white border-2 border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 flex items-center justify-center">
                            <div class="w-8 h-8 bg-gray-200 rounded-full"></div>
                        </div>
                        <div class="h-16 bg-white border-2 border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 flex items-center justify-center">
                            <div class="w-8 h-8 bg-gray-200 rounded-full"></div>
                        </div>
                        <div class="h-16 bg-white border-2 border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 flex items-center justify-center">
                            <div class="w-8 h-8 bg-gray-200 rounded-full"></div>
                        </div>
                        <div class="h-16 bg-white border-2 border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 flex items-center justify-center">
                            <div class="w-8 h-8 bg-gray-200 rounded-full"></div>
                        </div>
                    </div>
                </div>

                <!-- Ligne de séparation élégante -->
                <div class="relative mb-12">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center">
                        <span class="bg-white px-6 py-2 text-gray-500 font-medium rounded-full border border-gray-300">
                            مجموعات التصاميم
                        </span>
                    </div>
                </div>

                <!-- Titre de la section avec design amélioré -->
                <div class="text-center mb-12">
                    <h2 class="text-4xl font-black text-gray-900 mb-4">مجموعات التصاميم المميزة</h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                        اكتشف مجموعة متنوعة من التصاميم الاحترافية المصممة خصيصاً لتلبية جميع احتياجاتك
                    </p>
                </div>

                <!-- Galerie de collections مع تصميم احترافي -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div
                        v-for="(collection, index) in collections"
                        :key="index"
                        class="group relative bg-white rounded-3xl shadow-xl overflow-hidden hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 border border-gray-100"
                    >
                        <div class="h-48 bg-gradient-to-br from-purple-400 via-pink-500 to-red-500 flex items-center justify-center relative overflow-hidden">
                            <div class="absolute inset-0 bg-black/20"></div>
                            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                            <div class="relative text-white text-center z-10">
                                <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4 backdrop-blur-sm group-hover:scale-110 transition-transform duration-300">
                                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                </div>
                                <h3 class="font-bold text-xl">{{ collection.title }}</h3>
                                <p class="text-white/90 text-sm mt-2">{{ collection.subtitle }}</p>
                            </div>
                            <div class="absolute top-4 right-4 bg-white/20 backdrop-blur-sm rounded-full px-3 py-1 text-white text-xs font-medium">
                                {{ collection.count }}+ تصميم
                            </div>
                        </div>
                        <div class="p-6">
                            <h4 class="text-xl font-bold text-gray-900 mb-2 text-right">{{ collection.title }}</h4>
                            <p class="text-gray-600 text-sm mb-4 text-right">{{ collection.description }}</p>
                            <button class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white py-3 rounded-2xl hover:from-purple-700 hover:to-pink-700 transition-all duration-300 font-bold group-hover:scale-105 transform">
                                استكشف المجموعة
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </ClientLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import ClientLayout from '@/Layouts/ClientLayout.vue';

// État réactif pour la catégorie sélectionnée
const selectedCategory = ref('all');

// Données des collections
const collections = ref([
    {
        title: 'بطاقات الأعمال',
        subtitle: 'تصاميم احترافية',
        description: 'مجموعة متنوعة من بطاقات الأعمال الاحترافية المناسبة لجميع المجالات',
        count: 50,
        gradient: 'from-blue-500 via-purple-600 to-indigo-700'
    },
    {
        title: 'دعوات الزفاف',
        subtitle: 'تصاميم رومانسية',
        description: 'دعوات زفاف أنيقة ومميزة لجعل يومكم الخاص لا يُنسى',
        count: 30,
        gradient: 'from-pink-500 via-rose-600 to-red-700'
    },
    {
        title: 'بطاقات المناسبات',
        subtitle: 'لجميع المناسبات',
        description: 'تصاميم متنوعة للمناسبات الخاصة والأعياد والاحتفالات',
        count: 40,
        gradient: 'from-green-500 via-emerald-600 to-teal-700'
    },
    {
        title: 'بطاقات التهنئة',
        subtitle: 'تصاميم متنوعة',
        description: 'بطاقات تهنئة جميلة للتعبير عن مشاعركم في المناسبات المختلفة',
        count: 25,
        gradient: 'from-purple-500 via-violet-600 to-indigo-700'
    },
    {
        title: 'بطاقات شخصية',
        subtitle: 'تصاميم عصرية',
        description: 'بطاقات شخصية مميزة تعكس شخصيتكم وأسلوبكم الفريد',
        count: 35,
        gradient: 'from-orange-500 via-amber-600 to-yellow-700'
    },
    {
        title: 'بطاقات تجارية',
        subtitle: 'للشركات والمؤسسات',
        description: 'تصاميم تجارية احترافية تعزز من هوية علامتكم التجارية',
        count: 45,
        gradient: 'from-cyan-500 via-blue-600 to-indigo-700'
    },
    {
        title: 'بطاقات إبداعية',
        subtitle: 'تصاميم فنية',
        description: 'تصاميم إبداعية وفنية لمن يبحث عن التميز والإبداع',
        count: 20,
        gradient: 'from-rose-500 via-pink-600 to-purple-700'
    },
    {
        title: 'بطاقات رسمية',
        subtitle: 'للمناسبات الرسمية',
        description: 'تصاميم رسمية أنيقة مناسبة للمناسبات والفعاليات الرسمية',
        count: 28,
        gradient: 'from-gray-600 via-slate-700 to-gray-800'
    },
    {
        title: 'بطاقات موسمية',
        subtitle: 'للمواسم والأعياد',
        description: 'تصاميم خاصة بالمواسم والأعياد المختلفة على مدار السنة',
        count: 32,
        gradient: 'from-emerald-500 via-green-600 to-lime-700'
    }
]);
</script>
