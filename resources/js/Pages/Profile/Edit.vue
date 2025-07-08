<template>
    <Head title="الملف الشخصي - Bitaqati" />

    <AdminLayoutSidebar>
        <template #breadcrumb>
            <span class="text-gray-500">الملف الشخصي</span>
        </template>

        <template #header>
            <div class="flex justify-between items-center flex-row-reverse">
                <div class="text-right">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight text-right">الملف الشخصي</h2>
                    <p class="mt-1 text-sm text-gray-600 text-right">إدارة إعدادات حسابك وتفضيلاتك</p>
                </div>
            </div>
        </template>

        <div class="py-12" dir="rtl">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- قسم معلومات الملف الشخصي -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 px-6 py-4 text-center">
                        <h3 class="text-lg font-semibold text-white">معلومات الملف الشخصي</h3>
                        <p class="text-blue-100 text-sm">تحديث معلومات ملفك الشخصي وعنوان البريد الإلكتروني</p>
                    </div>
                    
                    <div class="p-6">
                        <form @submit.prevent="updateProfile" class="space-y-6">
                            <!-- Profile Image Section -->
                            <div class="mb-6">
                                <div class="mb-3" style="text-align: right !important;">
                                    <label class="text-sm font-medium text-gray-700" style="text-align: right !important; display: block;">
                                        صورة الملف الشخصي
                                    </label>
                                </div>
                                <div class="flex items-center gap-6 flex-row-reverse">
                                    <div class="shrink-0">
                                        <img 
                                            v-if="imagePreview || $page.props.auth.user.image" 
                                            :src="imagePreview || $page.props.auth.user.image" 
                                            :alt="$page.props.auth.user.email"
                                            class="h-20 w-20 object-cover rounded-full border-2 border-gray-300"
                                        />
                                        <div v-else class="h-20 w-20 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center border-2 border-gray-300">
                                            <span class="text-white font-semibold text-xl">
                                                {{ $page.props.auth.user.email.charAt(0).toUpperCase() }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <input
                                            ref="imageInput"
                                            type="file"
                                            accept="image/*"
                                            @change="handleImageUpload"
                                            class="hidden"
                                        />
                                        <div class="flex gap-3 justify-end mb-4 flex-row-reverse">
                                            <button
                                                type="button"
                                                @click="$refs.imageInput.click()"
                                                class="bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                            >
                                                تغيير الصورة
                                            </button>
                                            <button
                                                v-if="$page.props.auth.user.image || imagePreview"
                                                type="button"
                                                @click="removeImage"
                                                class="bg-red-600 py-2 px-3 border border-transparent rounded-md shadow-sm text-sm leading-4 font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                            >
                                                إزالة الصورة
                                            </button>
                                        </div>
                                        <p class="mt-2 text-sm text-gray-500 text-right">
                                            JPG, GIF أو PNG. حد أقصى 2MB.
                                        </p>
                                    </div>
                                </div>
                                <div v-if="profileForm.errors.image" class="mt-2 text-sm text-red-600 text-right">
                                    {{ profileForm.errors.image }}
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-6">
                                <!-- Email Field -->
                                <div>
                                    <div class="mb-3" style="text-align: right !important;">
                                        <label for="email" class="text-sm font-medium text-gray-700" style="text-align: right !important; display: block;">
                                            البريد الإلكتروني
                                        </label>
                                    </div>
                                    <input
                                        id="email"
                                        v-model="profileForm.email"
                                        type="email"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        style="text-align: right !important; direction: rtl !important;"
                                        required
                                    />
                                    <div v-if="profileForm.errors.email" class="mt-2 text-sm text-red-600 text-right">
                                        {{ profileForm.errors.email }}
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex justify-center">
                                <button
                                    type="submit"
                                    :disabled="profileForm.processing"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50"
                                >
                                    <svg v-if="profileForm.processing" class="animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 818-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    حفظ التغييرات
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- قسم تحديث كلمة المرور -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="bg-gradient-to-r from-green-500 to-teal-600 px-6 py-4 text-center">
                        <h3 class="text-lg font-semibold text-white">تحديث كلمة المرور</h3>
                        <p class="text-green-100 text-sm">تأكد من أن حسابك يستخدم كلمة مرور طويلة وعشوائية للبقاء آمناً</p>
                    </div>
                    
                    <div class="p-6">
                        <form @submit.prevent="updatePassword" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Current Password -->
                                <div class="md:col-span-2">
                                    <div class="mb-3" style="text-align: right !important;">
                                        <label for="current_password" class="text-sm font-medium text-gray-700" style="text-align: right !important; display: block;">
                                            كلمة المرور الحالية
                                        </label>
                                    </div>
                                    <input
                                        id="current_password"
                                        v-model="passwordForm.current_password"
                                        type="password"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        style="text-align: right !important; direction: rtl !important;"
                                        autocomplete="current-password"
                                        placeholder="أدخل كلمة المرور الحالية"
                                    />
                                    <div v-if="passwordForm.errors.current_password" class="mt-2 text-sm text-red-600 text-right">
                                        {{ passwordForm.errors.current_password }}
                                    </div>
                                </div>

                                <!-- New Password -->
                                <div>
                                    <div class="mb-3" style="text-align: right !important;">
                                        <label for="password" class="text-sm font-medium text-gray-700" style="text-align: right !important; display: block;">
                                            كلمة المرور الجديدة
                                        </label>
                                    </div>
                                    <input
                                        id="password"
                                        v-model="passwordForm.password"
                                        type="password"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        style="text-align: right !important; direction: rtl !important;"
                                        autocomplete="new-password"
                                        placeholder="أدخل كلمة المرور الجديدة"
                                    />
                                    <div v-if="passwordForm.errors.password" class="mt-2 text-sm text-red-600 text-right">
                                        {{ passwordForm.errors.password }}
                                    </div>
                                </div>

                                <!-- Confirm Password -->
                                <div>
                                    <div class="mb-3" style="text-align: right !important;">
                                        <label for="password_confirmation" class="text-sm font-medium text-gray-700" style="text-align: right !important; display: block;">
                                            تأكيد كلمة المرور الجديدة
                                        </label>
                                    </div>
                                    <input
                                        id="password_confirmation"
                                        v-model="passwordForm.password_confirmation"
                                        type="password"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        style="text-align: right !important; direction: rtl !important;"
                                        autocomplete="new-password"
                                        placeholder="أعد إدخال كلمة المرور الجديدة"
                                    />
                                    <div v-if="passwordForm.errors.password_confirmation" class="mt-2 text-sm text-red-600 text-right">
                                        {{ passwordForm.errors.password_confirmation }}
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex justify-center">
                                <button
                                    type="submit"
                                    :disabled="passwordForm.processing"
                                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50"
                                >
                                    <svg v-if="passwordForm.processing" class="animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 818-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    تحديث كلمة المرور
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Delete Account Section -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="bg-gradient-to-r from-red-500 to-pink-600 px-6 py-4 text-center">
                        <h3 class="text-lg font-semibold text-white">حذف الحساب</h3>
                        <p class="text-red-100 text-sm">بمجرد حذف حسابك، سيتم حذف جميع موارده وبياناته نهائياً</p>
                    </div>
                    
                    <div class="p-6">
                        <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-6">
                            <div class="flex flex-row-reverse">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="mr-3 text-right">
                                    <h3 class="text-sm font-medium text-red-800 text-right">
                                        تحذير
                                    </h3>
                                    <div class="mt-2 text-sm text-red-700 text-right">
                                        <p>بمجرد حذف حسابك، سيتم حذف جميع موارده وبياناته نهائياً</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-center">
                            <button
                                @click="confirmUserDeletion"
                                type="button"
                                class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            >
                                <span class="mr-2">حذف الحساب</span>
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="p-6" dir="rtl">
                <h2 class="text-lg font-medium text-gray-900 text-right">
                    هل أنت متأكد من حذف حسابك؟
                </h2>

                <p class="mt-1 text-sm text-gray-600 text-right">
                    بمجرد حذف حس��بك، سيتم حذف جميع موارده وبياناته نهائياً. يرجى إدخال كلمة المرور لتأكيد رغبتك في حذف حسابك نهائياً.
                </p>

                <div class="mt-6">
                    <label for="password" class="sr-only">كلمة المرور</label>
                    <input
                        id="password"
                        ref="passwordInput"
                        v-model="deleteForm.password"
                        type="password"
                        class="mt-1 block w-3/4 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="كلمة المرور"
                        @keyup.enter="deleteUser"
                        dir="rtl"
                        style="text-align: right !important; direction: rtl !important;"
                    />

                    <div v-if="deleteForm.errors.password" class="mt-2 text-sm text-red-600 text-right">
                        {{ deleteForm.errors.password }}
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3 space-x-reverse">
                    <button
                        @click="closeModal"
                        type="button"
                        class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        إلغاء
                    </button>

                    <button
                        @click="deleteUser"
                        type="button"
                        :disabled="deleteForm.processing"
                        class="inline-flex items-center justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50"
                    >
                        <svg v-if="deleteForm.processing" class="animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 818-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        حذف الحساب
                    </button>
                </div>
            </div>
        </Modal>
    </AdminLayoutSidebar>
</template>

<script setup>
import { ref, nextTick } from 'vue';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import AdminLayoutSidebar from '@/Layouts/AdminLayoutSidebar.vue';
import Modal from '@/Components/Modal.vue';
import { useTranslations } from '@/Composables/useTranslations';


const props = defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    locale: {
        type: String,
        default: 'en'
    },
    translations: {
        type: Object,
        default: () => ({})
    }
});

const { __, isRTL, direction } = useTranslations();
const page = usePage();

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);
const imagePreview = ref(null);
const imageInput = ref(null);

// Profile form
const profileForm = useForm({
    email: page.props.auth.user.email || '',
    image: null,
    remove_image: false,
});

// Password form
const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

// Delete form
const deleteForm = useForm({
    password: '',
});

const handleImageUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        profileForm.image = file;
        
        // Create preview
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const removeImage = () => {
    profileForm.image = null;
    profileForm.remove_image = true;
    imagePreview.value = null;
    if (imageInput.value) {
        imageInput.value.value = '';
    }
    
    // Submit the form to remove the image
    profileForm.patch(route('profile.update'), {
        preserveScroll: true,
        onSuccess: () => {
            profileForm.remove_image = false;
        },
    });
};

const updateProfile = () => {
    if (profileForm.image) {
        // Si il y a une image, utiliser POST avec _method=PATCH
        profileForm.transform((data) => ({
            ...data,
            _method: 'PATCH'
        })).post(route('profile.update'), {
            preserveScroll: true,
            onSuccess: () => {
                imagePreview.value = null;
            },
        });
    } else {
        // Sinon utiliser PATCH normal
        profileForm.patch(route('profile.update'), {
            preserveScroll: true,
            onSuccess: () => {
                imagePreview.value = null;
            },
        });
    }
};

const updatePassword = () => {
    passwordForm.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => {
            passwordForm.reset();
        },
        onError: () => {
            if (passwordForm.errors.password) {
                passwordForm.reset('password', 'password_confirmation');
            }
            if (passwordForm.errors.current_password) {
                passwordForm.reset('current_password');
            }
        },
    });
};

const sendVerification = () => {
    router.post(route('verification.send'));
};

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    nextTick(() => passwordInput.value?.focus());
};

const deleteUser = () => {
    deleteForm.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value?.focus(),
        onFinish: () => deleteForm.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;
    deleteForm.reset();
};
</script>