<template>
    <Head title="إدارة المستخدمين" />

    <AdminLayoutSidebar>
        <template #breadcrumb>
            <span class="text-gray-500">إدارة المستخدمين</span>
        </template>

        <template #header>
            <div class="flex justify-between items-center flex-row-reverse">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">إدارة المستخدمين</h2>
                <button
                    v-if="$page.props.auth.user.role === 'super_admin'"
                    @click="showCreateModal = true"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded"
                >
                    إضافة مستخدم
                </button>
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
                                    :class="isRTL ? 'text-right' : 'text-left'"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />
                            </div>
                            <div class="min-w-48">
                                <select
                                    v-model="form.role"
                                    :class="isRTL ? 'text-right' : 'text-left'"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
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
                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 px-6 py-4 text-center">
                        <h3 class="text-lg font-semibold text-white">المستخدمون</h3>
                        <p class="text-blue-100 text-sm">إدارة وتنظيم جميع المستخدمين في النظام</p>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200" dir="rtl">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th :class="[
                                        'px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider',
                                        isRTL ? 'text-right' : 'text-left'
                                    ]">
                                        <div :class="isRTL ? 'flex-row-reverse' : 'flex'" class="flex items-center">
                                            <svg :class="isRTL ? 'ml-2' : 'mr-2'" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                                            </svg>
                                            البريد الإلكتروني
                                        </div>
                                    </th>
                                    <th :class="[
                                        'px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider',
                                        isRTL ? 'text-right' : 'text-left'
                                    ]">
                                        <div :class="isRTL ? 'flex-row-reverse' : 'flex'" class="flex items-center">
                                            <svg :class="isRTL ? 'ml-2' : 'mr-2'" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                            </svg>
                                            الدور
                                        </div>
                                    </th>
                                    <th :class="[
                                        'px-6 py-4 text-xs font-semibold text-gray-600 uppercase tracking-wider',
                                        isRTL ? 'text-right' : 'text-left'
                                    ]">
                                        <div :class="isRTL ? 'flex-row-reverse' : 'flex'" class="flex items-center">
                                            <svg :class="isRTL ? 'ml-2' : 'mr-2'" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
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
                                <tr v-for="(user, index) in filteredUsers" :key="user.id" 
                                    class="hover:bg-gray-50 transition-all duration-300 ease-out transform hover:translate-x-2 hover:shadow-md"
                                    :style="{ animationDelay: `${index * 100}ms` }"
                                    @mouseenter="onRowHover"
                                    @mouseleave="onRowLeave">
                                    <td :class="[
                                        'px-6 py-4 whitespace-nowrap',
                                        isRTL ? 'text-right' : 'text-left'
                                    ]">
                                        <div :class="isRTL ? 'justify-end' : 'justify-start'" class="flex items-center">
                                            <div :class="isRTL ? 'mr-3' : 'ml-3'" class="order-2">
                                                <div :class="isRTL ? 'text-right' : 'text-left'" class="text-sm font-medium text-gray-900">{{ user.email }}</div>
                                            </div>
                                            <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center order-1">
                                                <span class="text-white font-semibold text-sm">{{ user.email.charAt(0).toUpperCase() }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td :class="[
                                        'px-6 py-4 whitespace-nowrap',
                                        isRTL ? 'text-right' : 'text-left'
                                    ]">
                                        <div :class="isRTL ? 'justify-end' : 'justify-start'" class="flex">
                                            <span :class="getRoleBadgeClass(user.role)" class="inline-flex px-3 py-1 text-xs font-semibold rounded-full">
                                                {{ getRoleLabel(user.role) }}
                                            </span>
                                        </div>
                                    </td>
                                    <td :class="[
                                        'px-6 py-4 whitespace-nowrap text-sm text-gray-500',
                                        isRTL ? 'text-right' : 'text-left'
                                    ]">
                                        {{ formatDate(user.created_at) }}
                                    </td>
                                    <td :class="[
                                        'px-6 py-4 whitespace-nowrap text-sm font-medium',
                                        isRTL ? 'text-right' : 'text-left'
                                    ]">
                                        <div :class="isRTL ? 'justify-end space-x-reverse' : 'justify-start'" class="flex space-x-3">
                                            <button
                                                v-if="canDeleteUser(user)"
                                                @click="deleteUser(user)"
                                                class="inline-flex items-center px-3 py-1 bg-red-100 text-red-700 rounded-md hover:bg-red-200 transition-colors duration-200"
                                            >
                                                <svg :class="isRTL ? 'ml-1' : 'mr-1'" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                                                </svg>
                                                <span>حذف</span>
                                            </button>
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

        <!-- Modal pour ajouter un utilisateur -->
        <div v-if="showCreateModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showCreateModal = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 text-right mb-4" id="modal-title">
                                    إضافة مستخدم جديد
                                </h3>

                                <form @submit.prevent="createUser" class="space-y-4">
                                    <!-- Email Field -->
                                    <div>
                                        <label for="modal_email" class="block text-sm font-medium text-gray-700 text-right mb-2">
                                            البريد الإلكتروني
                                        </label>
                                        <input
                                            id="modal_email"
                                            v-model="createForm.email"
                                            type="email"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-right"
                                            required
                                        />
                                        <div v-if="createForm.errors.email" class="mt-1 text-sm text-red-600 text-right">
                                            {{ createForm.errors.email }}
                                        </div>
                                    </div>

                                    <!-- Role Field -->
                                    <div>
                                        <label for="modal_role" class="block text-sm font-medium text-gray-700 text-right mb-2">
                                            الدور
                                        </label>
                                        <select
                                            id="modal_role"
                                            v-model="createForm.role"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-right"
                                            required
                                        >
                                            <option value="">اختر الدور</option>
                                            <option v-for="role in availableRoles" :key="role" :value="role">
                                                {{ getRoleLabel(role) }}
                                            </option>
                                        </select>
                                        <div v-if="createForm.errors.role" class="mt-1 text-sm text-red-600 text-right">
                                            {{ createForm.errors.role }}
                                        </div>
                                    </div>

                                    <!-- Password Field -->
                                    <div>
                                        <label for="modal_password" class="block text-sm font-medium text-gray-700 text-right mb-2">
                                            كلمة المرور
                                        </label>
                                        <input
                                            id="modal_password"
                                            v-model="createForm.password"
                                            type="password"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-right"
                                            required
                                        />
                                        <div v-if="createForm.errors.password" class="mt-1 text-sm text-red-600 text-right">
                                            {{ createForm.errors.password }}
                                        </div>
                                    </div>

                                    <!-- Password Confirmation Field -->
                                    <div>
                                        <label for="modal_password_confirmation" class="block text-sm font-medium text-gray-700 text-right mb-2">
                                            تأكيد كلمة المرور
                                        </label>
                                        <input
                                            id="modal_password_confirmation"
                                            v-model="createForm.password_confirmation"
                                            type="password"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-right"
                                            required
                                        />
                                        <div v-if="createForm.errors.password_confirmation" class="mt-1 text-sm text-red-600 text-right">
                                            {{ createForm.errors.password_confirmation }}
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button
                            type="submit"
                            @click="createUser"
                            :disabled="createForm.processing"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
                        >
                            <svg v-if="createForm.processing" class="animate-spin h-4 w-4 ml-2" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            إضافة المستخدم
                        </button>
                        <button
                            type="button"
                            @click="showCreateModal = false"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                        >
                            إلغاء
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayoutSidebar>
</template>

<script setup>
import { reactive, computed, ref } from 'vue';
import { Head, Link, router, usePage, useForm } from '@inertiajs/vue3';
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

// Modal state
const showCreateModal = ref(false);

// Create user form
const createForm = useForm({
    email: '',
    role: '',
    password: '',
    password_confirmation: '',
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

// Create user function
const createUser = () => {
    createForm.post(route('admin.users.store'), {
        onSuccess: () => {
            showCreateModal.value = false;
            createForm.reset();
        },
    });
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

// Animation handlers for smooth slide effect
const onRowHover = (event) => {
    event.currentTarget.style.transform = 'translateX(8px)';
    event.currentTarget.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.1)';
};

const onRowLeave = (event) => {
    event.currentTarget.style.transform = 'translateX(0)';
    event.currentTarget.style.boxShadow = 'none';
};
</script>

<style scoped>
/* Enhanced table row animations */
tbody tr {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
}

tbody tr:hover {
    background-color: #f8fafc;
    transform: translateX(8px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Staggered animation for initial load */
tbody tr {
    animation: slideInFromRight 0.5s ease-out forwards;
    opacity: 0;
    transform: translateX(20px);
}

@keyframes slideInFromRight {
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* Add smooth transitions to table cells */
tbody td {
    transition: all 0.3s ease;
}

/* Enhanced button hover effects */
.inline-flex.items-center {
    transition: all 0.2s ease;
}

.inline-flex.items-center:hover {
    transform: scale(1.05);
}
</style>