<template>
    <Head title="الملف الشخصي" />

    <AdminLayoutSidebar>
        <template #breadcrumb>
            الملف الشخصي
        </template>

        <div class="py-6">
            <div class="w-full space-y-6">
                <!-- رأس الصفحة -->
                <div class="card-arabic p-6">
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">الملف الشخصي</h1>
                    <p class="text-gray-600">إدارة معلومات حسابك وإعدادات الأمان</p>
                </div>

                <!-- قسم معلومات الملف الشخصي -->
                <div class="card-arabic">
                    <div class="bg-gradient-to-l from-blue-500 to-purple-600 px-6 py-4">
                        <h3 class="text-lg font-semibold text-white">معلومات الملف الشخصي</h3>
                        <p class="text-blue-100 text-sm">قم بتحديث معلومات حسابك وعنوان بريدك الإلكتروني</p>
                    </div>

                    <div class="p-6">
                        <form @submit.prevent="updateProfile" class="form-arabic">
                            <!-- Profile Image Section -->
                            <div class="mb-6">
                                <label class="label-arabic">
                                    صورة الملف الشخصي
                                </label>
                                <div :class="isRTL ? 'flex-row-reverse' : 'flex'" class="flex items-center space-x-6">
                                    <div class="shrink-0">
                                        <img 
                                            v-if="imagePreview || $page.props.auth.user.image" 
                                            :src="imagePreview || $page.props.auth.user.image" 
                                            :alt="$page.props.auth.user.name"
                                            class="h-20 w-20 object-cover rounded-full border-2 border-gray-300"
                                        />
                                        <div v-else class="h-20 w-20 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center border-2 border-gray-300">
                                            <span class="text-white font-semibold text-xl">
                                                {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
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
                                        <button
                                            type="button"
                                            @click="$refs.imageInput.click()"
                                            class="btn-arabic text-sm"
                                        >
                                            تغيير الصورة
                                        </button>
                                        <button
                                            v-if="$page.props.auth.user.image || imagePreview"
                                            type="button"
                                            @click="removeImage"
                                            class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm transition-all duration-200 mr-3"
                                        >
                                            إزالة الصورة
                                        </button>
                                        <p class="mt-2 text-sm text-gray-500">
                                            {{ __('common.image_upload_note') }}
                                        </p>
                                    </div>
                                </div>
                                <div v-if="profileForm.errors.image" class="mt-2 text-sm text-red-600">
                                    {{ profileForm.errors.image }}
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- حقل الاسم -->
                                <div>
                                    <label for="name" class="label-arabic">
                                        الاسم
                                    </label>
                                    <input
                                        id="name"
                                        v-model="profileForm.name"
                                        type="text"
                                        class="input-arabic"
                                        :class="profileForm.errors.name ? 'border-red-300' : ''"
                                        required
                                    />
                                    <div v-if="profileForm.errors.name" class="mt-2 text-sm text-red-600">
                                        {{ profileForm.errors.name }}
                                    </div>
                                </div>

                                <!-- حقل البريد الإلكتروني -->
                                <div>
                                    <label for="email" class="label-arabic">
                                        البريد الإلكتروني
                                    </label>
                                    <input
                                        id="email"
                                        v-model="profileForm.email"
                                        type="email"
                                        class="input-arabic"
                                        :class="profileForm.errors.email ? 'border-red-300' : ''"
                                        required
                                    />
                                    <div v-if="profileForm.errors.email" class="mt-2 text-sm text-red-600">
                                        {{ profileForm.errors.email }}
                                    </div>
                                </div>
                            </div>

                            <!-- Email Verification Notice -->
                            <div v-if="mustVerifyEmail && $page.props.auth.user.email_verified_at === null" class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                                <div :class="isRTL ? 'flex-row-reverse' : 'flex'" class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div :class="isRTL ? 'mr-3' : 'ml-3'">
                                        <h3 class="text-sm font-medium text-yellow-800">
                                            {{ __('common.email_verification_required') }}
                                        </h3>
                                        <div class="mt-2 text-sm text-yellow-700">
                                            <p>{{ __('common.email_unverified') }}</p>
                                        </div>
                                        <div class="mt-4">
                                            <div class="-mx-2 -my-1.5 flex">
                                                <button
                                                    @click="sendVerification"
                                                    type="button"
                                                    class="bg-yellow-50 px-2 py-1.5 rounded-md text-sm font-medium text-yellow-800 hover:bg-yellow-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-yellow-50 focus:ring-yellow-600"
                                                >
                                                    {{ __('common.resend_verification') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Success Message -->
                            <div v-if="status === 'verification-link-sent'" class="bg-green-50 border border-green-200 rounded-md p-4">
                                <div :class="isRTL ? 'flex-row-reverse' : 'flex'" class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div :class="isRTL ? 'mr-3' : 'ml-3'">
                                        <p class="text-sm font-medium text-green-800">
                                            {{ __('common.verification_sent') }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- زر الحفظ -->
                            <div class="flex justify-start">
                                <button
                                    type="submit"
                                    :disabled="profileForm.processing"
                                    class="btn-arabic flex items-center"
                                >
                                    <svg v-if="profileForm.processing" class="animate-spin h-4 w-4 ml-2" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    حفظ التغييرات
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- قسم تحديث كلمة المرور -->
                <div class="card-arabic">
                    <div class="bg-gradient-to-l from-green-500 to-teal-600 px-6 py-4">
                        <h3 class="text-lg font-semibold text-white">تحديث كلمة المرور</h3>
                        <p class="text-green-100 text-sm">تأكد من أن حسابك يستخدم كلمة مرور طويلة وعشوائية للبقاء آمناً</p>
                    </div>

                    <div class="p-6">
                        <form @submit.prevent="updatePassword" class="form-arabic">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- كلمة المرور الحالية -->
                                <div class="md:col-span-2">
                                    <label for="current_password" class="label-arabic">
                                        كلمة المرور الحالية
                                    </label>
                                    <input
                                        id="current_password"
                                        v-model="passwordForm.current_password"
                                        type="password"
                                        class="input-arabic"
                                        :class="passwordForm.errors.current_password ? 'border-red-300' : ''"
                                        autocomplete="current-password"
                                    />
                                    <div v-if="passwordForm.errors.current_password" class="mt-2 text-sm text-red-600">
                                        {{ passwordForm.errors.current_password }}
                                    </div>
                                </div>

                                <!-- كلمة المرور الجديدة -->
                                <div>
                                    <label for="password" class="label-arabic">
                                        كلمة المرور الجديدة
                                    </label>
                                    <input
                                        id="password"
                                        v-model="passwordForm.password"
                                        type="password"
                                        class="input-arabic"
                                        :class="passwordForm.errors.password ? 'border-red-300' : ''"
                                        autocomplete="new-password"
                                    />
                                    <div v-if="passwordForm.errors.password" class="mt-2 text-sm text-red-600">
                                        {{ passwordForm.errors.password }}
                                    </div>
                                </div>

                                <!-- تأكيد كلمة المرور -->
                                <div>
                                    <label for="password_confirmation" class="label-arabic">
                                        تأكيد كلمة المرور الجديدة
                                    </label>
                                    <input
                                        id="password_confirmation"
                                        v-model="passwordForm.password_confirmation"
                                        type="password"
                                        class="input-arabic"
                                        :class="passwordForm.errors.password_confirmation ? 'border-red-300' : ''"
                                        autocomplete="new-password"
                                    />
                                    <div v-if="passwordForm.errors.password_confirmation" class="mt-2 text-sm text-red-600">
                                        {{ passwordForm.errors.password_confirmation }}
                                    </div>
                                </div>
                            </div>

                            <!-- زر تحديث كلمة المرور -->
                            <div class="flex justify-start">
                                <button
                                    type="submit"
                                    :disabled="passwordForm.processing"
                                    class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg font-medium transition-all duration-200 flex items-center"
                                >
                                    <svg v-if="passwordForm.processing" class="animate-spin h-4 w-4 ml-2" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    تحديث كلمة المرور
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Delete Account Section -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="bg-gradient-to-r from-red-500 to-pink-600 px-6 py-4">
                        <h3 class="text-lg font-semibold text-white">{{ __('common.delete_account') }}</h3>
                        <p class="text-red-100 text-sm">{{ __('common.delete_account_warning') }}</p>
                    </div>
                    
                    <div class="p-6">
                        <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-6">
                            <div :class="isRTL ? 'flex-row-reverse' : 'flex'" class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div :class="isRTL ? 'mr-3' : 'ml-3'">
                                    <h3 class="text-sm font-medium text-red-800">
                                        {{ __('common.warning') }}
                                    </h3>
                                    <div class="mt-2 text-sm text-red-700">
                                        <p>{{ __('common.delete_account_warning') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div :class="isRTL ? 'text-left' : 'text-right'" class="flex justify-end">
                            <button
                                @click="confirmUserDeletion"
                                type="button"
                                class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            >
                                <svg :class="isRTL ? 'ml-2' : 'mr-2'" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                                </svg>
                                {{ __('common.delete_account') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    {{ __('common.delete_account_confirmation_title') }}
                </h2>

                <p class="mt-1 text-sm text-gray-600">
                    {{ __('common.delete_account_confirmation') }}
                </p>

                <div class="mt-6">
                    <label :for="'password'" class="sr-only">{{ __('auth.password') }}</label>
                    <input
                        id="password"
                        ref="passwordInput"
                        v-model="deleteForm.password"
                        type="password"
                        :class="[
                            'mt-1 block w-3/4 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500',
                            isRTL ? 'text-right' : 'text-left',
                            deleteForm.errors.password ? 'border-red-300' : ''
                        ]"
                        :placeholder="__('auth.password')"
                        @keyup.enter="deleteUser"
                    />

                    <div v-if="deleteForm.errors.password" class="mt-2 text-sm text-red-600">
                        {{ deleteForm.errors.password }}
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button
                        @click="closeModal"
                        type="button"
                        class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        {{ __('common.cancel') }}
                    </button>

                    <button
                        @click="deleteUser"
                        type="button"
                        :disabled="deleteForm.processing"
                        :class="isRTL ? 'mr-3' : 'ml-3'"
                        class="inline-flex items-center justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50"
                    >
                        <svg v-if="deleteForm.processing" :class="isRTL ? 'ml-2' : 'mr-2'" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ __('common.delete_account') }}
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

const { __, isRTL } = useTranslations();
const page = usePage();



const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);
const imagePreview = ref(null);
const imageInput = ref(null);

// Profile form
const profileForm = useForm({
    name: page.props.auth.user.name || '',
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