<template>
    <Head title="إدارة المستخدمين" />

    <AdminLayoutSidebar>
        <template #breadcrumb>
            <span class="text-gray-500">إدارة المستخدمين</span>
        </template>

        <template #header>
            <div class="flex justify-between items-center flex-row-reverse">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">إدارة المستخدمين</h2>
                <Link 
                    v-if="$page.props.auth.user.role === 'super_admin'"
                    :href="route('admin.users.create')" 
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded"
                >
                    إضافة مستخدم
                </Link>
            </div>
        </template>

        <div class="py-12" dir="rtl">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- المرشحات -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <form @submit.prevent="search" class="flex flex-wrap gap-4 flex-row-reverse">
                            <div class="flex-1 min-w-64">
                                <input
                                    v-model="form.search"
                                    type="text"
                                    placeholder="بحث بالبريد الإلكتروني..."
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-right"
                                    dir="rtl"
                                />
                            </div>
                            <div class="min-w-48">
                                <select
                                    v-model="form.role"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-right"
                                    dir="rtl"
                                >
                                    <option value="">جميع الأدوار</option>
                                    <option v-for="role in availableRoles" :key="role" :value="role">
                                        {{ getRoleLabel(role) }}
                                    </option>
                                </select>
                            </div>
                            <button
                                type="submit"
                                class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
                            >
                                بحث
                            </button>
                            <button
                                type="button"
                                @click="clearFilters"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded"
                            >
                                مسح
                            </button>
                        </form>
                    </div>
                </div>

                <!-- جدول المستخدمين -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 px-6 py-4 text-right">
                        <h3 class="text-lg font-semibold text-white">المستخدمون</h3>
                        <p class="text-blue-100 text-sm">إدارة وتنظيم جميع المستخدمين في النظام</p>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200" dir="rtl">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-right">
                                        <div class="flex items-center flex-row-reverse">
                                            <svg class="w-4 h-4 ml-2" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                                            </svg>
                                            البريد الإلكتروني
                                        </div>
                                    </th>
                                    <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-right">
                                        <div class="flex items-center flex-row-reverse">
                                            <svg class="w-4 h-4 ml-2" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                            </svg>
                                            الدور
                                        </div>
                                    </th>
                                    <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-right">
                                        <div class="flex items-center flex-row-reverse">
                                            <svg class="w-4 h-4 ml-2" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-7h-1V2h-2v2H8V2H6v2H5c-1.1 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z"/>
                                            </svg>
                                            تاريخ الإنشاء
                                        </div>
                                    </th>
                                    <th class="px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-right">
                                        الإجراءات
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-if="filteredUsers?.length === 0">
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                        لا توجد مستخدمين
                                    </td>
                                </tr>
                                <tr v-for="user in filteredUsers" :key="user.id" class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="flex items-center justify-end">
                                            <div class="text-right mr-3">
                                                <div class="text-sm font-medium text-gray-900 text-right">{{ user.email }}</div>
                                            </div>
                                            <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                                <span class="text-white font-semibold text-sm">{{ user.email.charAt(0).toUpperCase() }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="flex justify-end">
                                            <span :class="getRoleBadgeClass(user.role)" class="inline-flex px-3 py-1 text-xs font-semibold rounded-full">
                                                {{ getRoleLabel(user.role) }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">
                                        {{ formatDate(user.created_at) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right">
                                        <div class="flex justify-end space-x-2 space-x-reverse">
                                            <button
                                                v-if="canDeleteUser(user)"
                                                @click="deleteUser(user)"
                                                class="inline-flex items-center px-3 py-1 bg-red-100 text-red-700 rounded-md hover:bg-red-200 transition-colors duration-200"
                                            >
                                                <span class="mr-1">حذف</span>
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                                                </svg>
                                            </button>
                                            <Link
                                                :href="route('admin.users.edit', user.id)"
                                                class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 transition-colors duration-200"
                                            >
                                                <span class="mr-1">تعديل</span>
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                                                </svg>
                                            </Link>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- ترقيم الصفحات -->
                    <div v-if="users?.data?.length > 0" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
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
                                    <p class="text-sm text-gray-700 text-right">
                                        عرض من
                                        <span class="font-medium">{{ users.from }}</span>
                                        إلى
                                        <span class="font-medium">{{ users.to }}</span>
                                        من أصل
                                        <span class="font-medium">{{ users.total }}</span>
                                        نتيجة
                                    </p>
                                </div>
                                <div>
                                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                        <Link
                                            v-if="users.prev_page_url"
                                            :href="users.prev_page_url"
                                            class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                                        >
                                            السابق
                                        </Link>
                                        <Link
                                            v-if="users.next_page_url"
                                            :href="users.next_page_url"
                                            class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                                        >
                                            التالي
                                        </Link>
                                    </nav>
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
import { ref, reactive, computed } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import AdminLayoutSidebar from '@/Layouts/AdminLayoutSidebar.vue';
import { useTranslations } from '@/Composables/useTranslations';

const props = defineProps({
    users: Object,
    filters: Object,
    roles: Array,
});

const { __, isRTL, direction } = useTranslations();

const form = reactive({
    search: props.filters?.search || '',
    role: props.filters?.role || '',
});

// Filtrer les rôles disponibles (exclure super_admin pour les non-super_admin)
const availableRoles = computed(() => {
    if (props.roles) {
        return props.roles.filter(role => role !== 'super_admin');
    }
    return [];
});

// Filtrer les utilisateurs (exclure les super_admin)
const filteredUsers = computed(() => {
    if (props.users?.data) {
        return props.users.data.filter(user => user.role !== 'super_admin');
    }
    return [];
});

const search = () => {
    router.get(route('admin.users.index'), form, {
        preserveState: true,
        replace: true,
    });
};

const clearFilters = () => {
    form.search = '';
    form.role = '';
    router.get(route('admin.users.index'), {}, {
        preserveState: true,
        replace: true,
    });
};

const deleteUser = (user) => {
    if (confirm('هل أنت متأكد من حذف ' + user.email + '؟')) {
        router.delete(route('admin.users.destroy', user.id));
    }
};

// Vérifier si l'utilisateur peut être supprimé
const canDeleteUser = (user) => {
    const currentUser = usePage().props.auth.user;
    
    // Ne peut pas supprimer son propre compte
    if (user.id === currentUser.id) {
        return false;
    }
    
    // Si l'utilisateur connecté est un Admin, il ne peut pas supprimer un autre Admin
    if (currentUser.role === 'admin' && user.role === 'admin') {
        return false;
    }
    
    // Super Admin peut tout supprimer (sauf les autres super_admin qui sont déjà filtrés)
    return true;
};

const getRoleLabel = (role) => {
    const labels = {
        'super_admin': 'مدير عام',
        'admin': 'مدير',
        'client': 'مستخدم'
    };
    return labels[role] || role;
};

const getRoleBadgeClass = (role) => {
    const classes = {
        'super_admin': 'bg-red-100 text-red-800',
        'admin': 'bg-green-100 text-green-800',
        'client': 'bg-blue-100 text-blue-800'
    };
    return classes[role] || 'bg-gray-100 text-gray-800';
};

// Format de date normal (non hijri)
const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
    });
};
</script>