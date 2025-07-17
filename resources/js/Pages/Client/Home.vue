<template>
    <Head title="الرئيسية - سامقة" />
    <ClientLayout>
        <!-- Categories List -->
        <div class="flex items-center gap-2 overflow-x-auto py-4 px-2 bg-white rounded-lg shadow mb-8 scrollbar-thin scrollbar-thumb-purple-200 scrollbar-track-transparent">
            <button
                :class="['flex items-center gap-2 px-5 py-2 rounded-full font-bold transition-all duration-200 border', selectedCategory === 'all' ? 'bg-gradient-to-r from-purple-500 to-pink-500 text-white shadow-lg scale-105 border-transparent' : 'bg-gray-100 text-gray-700 border-gray-200 hover:bg-purple-50 hover:text-purple-700']"
                @click="selectCategory('all')"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h8"/></svg>
                الكل
            </button>
            <button
                v-for="cat in props.categories"
                :key="cat.id"
                :class="['flex items-center gap-2 px-5 py-2 rounded-full font-bold transition-all duration-200 border', selectedCategory === cat.id ? 'bg-gradient-to-r from-purple-500 to-pink-500 text-white shadow-lg scale-105 border-transparent' : 'bg-gray-100 text-gray-700 border-gray-200 hover:bg-purple-50 hover:text-purple-700']"
                @click="selectCategory(cat.id)"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                {{ cat.name }}
            </button>
                </div>

        <!-- Templates Section -->
        <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-lg shadow p-6">
            <h2 class="text-2xl font-extrabold mb-6 text-center text-purple-700 tracking-tight animate-fade-in">{{ selectedCategoryName }}</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
                <div
                    v-for="template in displayedTemplates"
                    :key="template.id"
                    class="group relative border border-gray-200 rounded-2xl p-4 flex flex-col items-center bg-white shadow-md hover:shadow-xl transition-all duration-300 hover:border-purple-400 hover:scale-105 cursor-pointer animate-fade-in-up"
                >
                    <div class="relative w-28 h-28 mb-3 flex items-center justify-center overflow-hidden rounded-xl bg-gradient-to-br from-purple-100 to-pink-100">
                        <img :src="template.thumbnail_url || '/images/placeholder.png'" alt="Template" class="object-cover w-full h-full transition-transform duration-300 group-hover:scale-110" />
                        <div v-if="!template.thumbnail_url" class="absolute inset-0 flex items-center justify-center text-purple-300">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="4" y="4" width="16" height="16" rx="4" stroke-width="2"/></svg>
            </div>

                        <!-- Edit Icon Overlay -->
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 rounded-xl flex items-center justify-center transition-all duration-300">
                            <svg class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="font-bold text-lg text-gray-800 text-center mb-1 truncate w-full">{{ template.name }}</div>
                    <div v-if="selectedCategory === 'all'" class="text-xs text-purple-500 bg-purple-100 px-2 py-1 rounded-full mb-2">({{ template.category.name }})</div>
                            <Link
                        :href="route('client.templates.create', template.id)"
                        @click.stop
                        class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-gradient-to-r from-purple-500 to-pink-500 text-white px-4 py-2 rounded-full font-semibold mt-2 shadow-lg hover:from-purple-600 hover:to-pink-600 inline-block text-center"
                    >
                        عرض القالب
                            </Link>
                        </div>
                    </div>

            <!-- Pagination Controls -->
            <div v-if="totalPages > 1" class="flex justify-center mt-8 items-center gap-2 rtl">
                <button
                    @click="prevPage"
                    :disabled="currentPage === 1"
                    :class="['px-4 py-2 rounded-lg border transition-all duration-200',
                        currentPage === 1
                            ? 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed'
                            : 'bg-white text-purple-600 border-purple-200 hover:bg-purple-50 hover:border-purple-300']"
                >
                    <svg class="w-5 h-5 transform rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                </button>

                <div class="flex items-center gap-1">
                    <button
                        v-for="page in Math.min(5, totalPages)"
                        :key="page"
                        @click="goToPage(page)"
                        :class="['w-10 h-10 rounded-lg flex items-center justify-center transition-all duration-200 font-medium',
                            currentPage === page
                                ? 'bg-gradient-to-r from-purple-500 to-pink-500 text-white shadow-md'
                                : 'bg-white text-gray-700 border border-gray-200 hover:bg-purple-50 hover:text-purple-600']"
                    >
                        {{ page }}
                    </button>

                    <span v-if="totalPages > 5" class="px-2">...</span>

                    <button
                        v-if="totalPages > 5"
                        @click="goToPage(totalPages)"
                        :class="['w-10 h-10 rounded-lg flex items-center justify-center transition-all duration-200 font-medium',
                            currentPage === totalPages
                                ? 'bg-gradient-to-r from-purple-500 to-pink-500 text-white shadow-md'
                                : 'bg-white text-gray-700 border border-gray-200 hover:bg-purple-50 hover:text-purple-600']"
                    >
                        {{ totalPages }}
                    </button>
                </div>

                <button
                    @click="nextPage"
                    :disabled="currentPage === totalPages"
                    :class="['px-4 py-2 rounded-lg border transition-all duration-200',
                        currentPage === totalPages
                            ? 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed'
                            : 'bg-white text-purple-600 border-purple-200 hover:bg-purple-50 hover:border-purple-300']"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>
        </div>
    </ClientLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import ClientLayout from '@/Layouts/ClientLayout.vue';

// Define props
const props = defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    categories: Array,
    allTemplates: Object,
});

// State for selected category
const selectedCategory = ref('all');

// State for pagination
const currentPage = ref(1);
const templatesPerPage = 4;

// Compute templates to show
const displayedTemplates = computed(() => {
    let allTemplates = [];

    if (selectedCategory.value === 'all') {
        // Show ALL templates from all categories
        props.categories.forEach(cat => {
            const templates = props.allTemplates[cat.id] || [];
            templates.forEach(template => {
                allTemplates.push({ ...template, category: cat });
            });
        });
    } else {
        // Show all templates for selected category
        allTemplates = props.allTemplates[selectedCategory.value] || [];
    }

    // Apply pagination
    const startIndex = (currentPage.value - 1) * templatesPerPage;
    const endIndex = startIndex + templatesPerPage;
    return allTemplates.slice(startIndex, endIndex);
});

// Compute total templates count
const totalTemplates = computed(() => {
    if (selectedCategory.value === 'all') {
        let count = 0;
        props.categories.forEach(cat => {
            count += (props.allTemplates[cat.id] || []).length;
        });
        return count;
    } else {
        return (props.allTemplates[selectedCategory.value] || []).length;
    }
});

// Compute total pages
const totalPages = computed(() => {
    return Math.ceil(totalTemplates.value / templatesPerPage);
});

// Reset pagination when category changes
const selectCategory = (categoryId) => {
    selectedCategory.value = categoryId;
    currentPage.value = 1;
};

// Pagination methods
const goToPage = (page) => {
    if (page >= 1 && page <= totalPages.value) {
        currentPage.value = page;
    }
};

const nextPage = () => {
    if (currentPage.value < totalPages.value) {
        currentPage.value++;
    }
};

const prevPage = () => {
    if (currentPage.value > 1) {
        currentPage.value--;
    }
};

// Get selected category name
const selectedCategoryName = computed(() => {
    if (selectedCategory.value === 'all') return 'الكل';
    const cat = props.categories.find(c => c.id === selectedCategory.value);
    return cat ? cat.name : '';
});


</script>

<style scoped>
.scrollbar-thin {
    scrollbar-width: thin;
}
.scrollbar-thumb-purple-200::-webkit-scrollbar-thumb {
    background: #e9d5ff;
    border-radius: 9999px;
}
.scrollbar-track-transparent::-webkit-scrollbar-track {
    background: transparent;
}
@keyframes fade-in-up {
    0% { opacity: 0; transform: translateY(20px); }
    100% { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-up {
    animation: fade-in-up 0.7s cubic-bezier(0.4,0,0.2,1) both;
}
.animate-fade-in {
    animation: fade-in 0.7s cubic-bezier(0.4,0,0.2,1) both;
}
@keyframes fade-in {
    0% { opacity: 0; }
    100% { opacity: 1; }
}
</style>
