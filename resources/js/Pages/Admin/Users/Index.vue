<template>
    <Head title="إدارة المستخدمين" />

    <AdminLayoutSidebar>
        <template #breadcrumb>
            إدارة المستخدمين
        </template>

        <div class="py-6">
            <div class="w-full">
                <!-- رأس الصفحة -->
                <div class="card-arabic mb-6 p-6">
                    <div class="flex justify-between items-center flex-row-reverse">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-800 mb-2">إدارة المستخدمين</h1>
                            <p class="text-gray-600">إدارة وتنظيم جميع المستخدمين في النظام</p>
                        </div>
                        <Link
                            v-if="userPermissions.canAddUsers"
                            :href="route('admin.users.create')"
                            class="btn-arabic"
                        >
                            إضافة مستخدم
                        </Link>
                    </div>
                </div>

                <!-- مرشحات البحث -->
                <div class="card-arabic mb-6">
                    <div class="p-6">
                        <form @submit.prevent="search" class="flex flex-wrap gap-4 flex-row-reverse">
                            <div class="flex-1 min-w-64">
                                <input
                                    v-model="form.search"
                                    type="text"
                                    placeholder="بحث..."
                                    class="input-arabic"
                                />
                            </div>
                            <div class="min-w-48">
                                <select
                                    v-model="form.role"
                                    class="input-arabic"
                                >
                                    <option value="">جميع الأدوار</option>
                                    <option v-for="role in roles" :key="role" :value="role">
                                        {{ getRoleLabel(role) }}
                                    </option>
                                </select>
                            </div>
                            <button
                                type="submit"
                                class="btn-arabic"
                            >
                                بحث
                            </button>
                            <button
                                type="button"
                                @click="clearFilters"
                                class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-all duration-200"
                            >
                                مسح
                            </button>
                        </form>
                    </div>
                </div>

                <!-- قائمة المستخدمين -->
                <div class="card-arabic">
                    <div class="bg-gradient-to-l from-blue-500 to-purple-600 px-6 py-4">
                        <h3 class="text-lg font-semibold text-white">قائمة المستخدمين</h3>
                        <p class="text-blue-100 text-sm">إدارة وتنظيم جميع المستخدمين في النظام</p>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="table-arabic">
                            <thead>
                                <tr>
                                    <th class="text-right px-6 py-4 text-sm font-semibold text-gray-700">
                                        <div class="flex items-center flex-row-reverse">
                                            <svg class="w-4 h-4 ml-2" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                            </svg>
                                            الاسم
                                        </div>
                                    </th>
                                    <th class="text-right px-6 py-4 text-sm font-semibold text-gray-700">
                                        <div class="flex items-center flex-row-reverse">
                                            <svg class="w-4 h-4 ml-2" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                                            </svg>
                                            البريد الإلكتروني
                                        </div>
                                    </th>
                                    <th class="text-center px-6 py-4 text-sm font-semibold text-gray-700">
                                        <div class="flex items-center justify-center">
                                            <svg class="w-4 h-4 ml-2" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                            </svg>
                                            الدور
                                        </div>
                                    </th>
                                    <th class="text-center px-6 py-4 text-sm font-semibold text-gray-700">
                                        <div class="flex items-center justify-center">
                                            <svg class="w-4 h-4 ml-2" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-7h-1V2h-2v2H8V2H6v2H5c-1.1 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z"/>
                                            </svg>
                                            تاريخ الإنشاء
                                        </div>
                                    </th>
                                    <th class="text-center px-6 py-4 text-sm font-semibold text-gray-700">
                                        الإجراءات
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="text-right px-6 py-4">
                                        <div class="flex items-center flex-row-reverse">
                                            <div class="w-10 h-10 bg-gradient-to-l from-blue-500 to-purple-600 rounded-full flex items-center justify-center ml-3">
                                                <span class="text-white font-semibold text-sm">{{ user.name.charAt(0).toUpperCase() }}</span>
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-right px-6 py-4 text-sm text-gray-500">
                                        {{ user.email }}
                                    </td>
                                    <td class="text-center px-6 py-4">
                                        <span :class="getRoleBadgeClass(user.role)" class="inline-flex px-3 py-1 text-xs font-semibold rounded-full">
                                            {{ getRoleLabel(user.role) }}
                                        </span>
                                    </td>
                                    <td class="text-center px-6 py-4 text-sm text-gray-500">
                                        {{ formatDate(user.created_at) }}
                                    </td>
                                    <td class="text-center px-6 py-4 text-sm font-medium">
                                        <div class="flex justify-center space-x-3 space-x-reverse">
                                            <button
                                                v-if="canDeleteUser(user)"
                                                @click="deleteUser(user)"
                                                class="inline-flex items-center px-3 py-1 bg-red-100 text-red-700 rounded-md hover:bg-red-200 transition-colors duration-200"
                                            >
                                                <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                                                </svg>
                                                حذف
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- ترقيم الصفحات -->
                    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        <div class="flex items-center justify-between flex-row-reverse">
                            <div class="flex-1 flex justify-between sm:hidden flex-row-reverse">
                                <Link
                                    v-if="users.next_page_url"
                                    :href="users.next_page_url"
                                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                >
                                    التالي
                                </Link>
                                <Link
                                    v-if="users.prev_page_url"
                                    :href="users.prev_page_url"
                                    class="mr-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                >
                                    السابق
                                </Link>
                            </div>
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between flex-row-reverse">
                                <div>
                                    <nav class="relative z-0 inline-flex rounded-md shadow-sm space-x-reverse">
                                        <Link
                                            v-if="users.next_page_url"
                                            :href="users.next_page_url"
                                            class="relative inline-flex items-center px-4 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                                        >
                                            التالي
                                        </Link>
                                        <Link
                                            v-if="users.prev_page_url"
                                            :href="users.prev_page_url"
                                            class="relative inline-flex items-center px-4 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                                        >
                                            السابق
                                        </Link>
                                    </nav>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-700">
                                        عرض النتائج من
                                        <span class="font-medium">{{ users.from || 0 }}</span>
                                        إلى
                                        <span class="font-medium">{{ users.to || 0 }}</span>
                                        من أصل
                                        <span class="font-medium">{{ users.total || 0 }}</span>
                                        نتيجة
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayoutSidebar>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import AdminLayoutSidebar from '@/Layouts/AdminLayoutSidebar.vue';
import { useTranslations } from '@/Composables/useTranslations';

const $page = usePage();

const props = defineProps({
    users: Object,
    filters: Object,
    roles: Array,
    userPermissions: Object
});

const { __, isRTL } = useTranslations();

// Form for search and filters
const form = reactive({
    search: props.filters.search || '',
    role: props.filters.role || ''
});

// Search function
const search = () => {
    router.get(route('admin.users.index'), form, {
        preserveState: true,
        replace: true
    });
};

// Clear filters
const clearFilters = () => {
    form.search = '';
    form.role = '';
    search();
};

// Delete user function
const deleteUser = (user) => {
    if (confirm('هل أنت متأكد من حذف ' + user.name + '؟')) {
        router.delete(route('admin.users.destroy', user.id));
    }
};

// Get role label in Arabic
const getRoleLabel = (role) => {
    const labels = {
        'super_admin': 'مشرف عام',
        'admin': 'مشرف',
        'client': 'عميل'
    };
    return labels[role] || role;
};

// Get role badge class
const getRoleBadgeClass = (role) => {
    const classes = {
        'super_admin': 'bg-red-100 text-red-800',
        'admin': 'bg-green-100 text-green-800',
        'client': 'bg-blue-100 text-blue-800'
    };
    return classes[role] || 'bg-gray-100 text-gray-800';
};

// Format date in Arabic
const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('ar-SA');
};

// Check if user can be deleted
const canDeleteUser = (user) => {
    // Ne peut pas supprimer son propre compte
    if (user.id === $page.props.auth.user.id) {
        return false;
    }

    const currentUserRole = $page.props.auth.user.role;

    // Super_Admin peut supprimer admins et clients (mais pas d'autres super_admins)
    if (currentUserRole === 'super_admin') {
        return user.role === 'admin' || user.role === 'client';
    }

    // Admin peut seulement supprimer des clients
    if (currentUserRole === 'admin') {
        return user.role === 'client';
    }

    return false;
};
</script>
