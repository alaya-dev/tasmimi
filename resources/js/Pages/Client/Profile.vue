<template>
    <Head title="الملف الشخصي - سامقة" />

    <ClientLayout>
        <div class="py-12 bg-gray-50 min-h-screen" dir="rtl">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Page Header - STRICT RTL -->
                <div class="mb-8 text-right">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2 text-right">الملف الشخصي</h1>
                    <p class="text-gray-600 text-right">إدارة معلوماتك الشخصية وإعدادات حسابك</p>
                </div>

                <div class="space-y-8">
                        <!-- Personal Information Form -->
                        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                            <div class="px-8 py-6 bg-gradient-to-l from-purple-600 via-blue-600 to-indigo-700">
                                <div class="text-center">
                                    <h3 class="text-xl font-bold text-white mb-2">
                                        المعلومات الشخصية
                                    </h3>
                                    <p class="text-purple-100 text-sm">تحديث معلوماتك الشخصية وصورة الملف الشخصي</p>
                                </div>
                            </div>
                            <div class="p-8">
                                <form @submit.prevent="updateProfile" class="space-y-8">
                                    <!-- Profile Image Section -->
                                    <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                                        <label class="block text-lg font-bold text-gray-900 mb-4 text-right">
                                            صورة الملف الشخصي
                                        </label>
                                        <div class="flex items-center gap-8 flex-row-reverse">
                                            <div class="shrink-0">
                                                <img 
                                                    v-if="imagePreview || user.image" 
                                                    :src="imagePreview || user.image" 
                                                    :alt="user.email"
                                                    class="h-24 w-24 object-cover rounded-full border-4 border-purple-200 shadow-lg"
                                                />
                                                <div v-else class="h-24 w-24 bg-gradient-to-br from-purple-500 to-blue-600 rounded-full flex items-center justify-center border-4 border-purple-200 shadow-lg">
                                                    <span class="text-white font-bold text-2xl">
                                                        {{ user.email.charAt(0).toUpperCase() }}
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
                                                <div class="flex gap-4 justify-end mb-4 flex-row-reverse">
                                                    <button
                                                        type="button"
                                                        @click="$refs.imageInput.click()"
                                                        class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-lg font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-md"
                                                    >
                                                        <svg class="w-5 h-5 ml-2" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                                                        </svg>
                                                        تغيير الصورة
                                                    </button>
                                                    <button
                                                        v-if="user.image || imagePreview"
                                                        type="button"
                                                        @click="removeImage"
                                                        class="inline-flex items-center px-6 py-3 bg-red-600 border border-transparent rounded-lg font-semibold text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200 shadow-md"
                                                    >
                                                        <svg class="w-5 h-5 ml-2" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" clip-rule="evenodd" />
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8 7a1 1 0 012 0v4a1 1 0 11-2 0V7zM12 7a1 1 0 012 0v4a1 1 0 11-2 0V7z" clip-rule="evenodd" />
                                                        </svg>
                                                        إزالة الصورة
                                                    </button>
                                                </div>
                                                <p class="text-sm text-gray-500 text-right bg-blue-50 p-3 rounded-lg border border-blue-200">
                                                    <svg class="w-4 h-4 inline ml-1 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                                    </svg>
                                                    يُفضل استخدام صور بصيغة JPG أو PNG. الحد الأقصى للحجم 2 ميجابايت.
                                                </p>
                                            </div>
                                        </div>
                                        <div v-if="profileForm.errors.image" class="mt-3 text-sm text-red-600 text-right bg-red-50 p-3 rounded-lg border border-red-200">
                                            {{ profileForm.errors.image }}
                                        </div>
                                    </div>

                                    <!-- Email Field -->
                                    <div class="space-y-4">
                                        <div style="text-align: right !important;">
                                            <label for="email" class="text-lg font-bold text-gray-900">
                                                البريد الإلكتروني
                                            </label>
                                        </div>
                                        <div class="relative">
                                            <input
                                                id="email"
                                                type="email"
                                                v-model="profileForm.email"
                                                required
                                                class="w-full border-2 border-gray-300 rounded-xl px-6 py-4 pr-14 text-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 text-right bg-gray-50"
                                                style="text-align: right !important; direction: rtl !important;"
                                                :class="{ 'border-red-500 bg-red-50': profileForm.errors.email }"
                                                placeholder="أدخل عنوان بريدك الإلكتروني"
                                                dir="rtl"
                                            />
                                            <div class="absolute left-4 top-1/2 transform -translate-y-1/2">
                                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                            </div>
                                        </div>
                                        <div v-if="profileForm.errors.email" class="text-sm text-red-600 text-right bg-red-50 p-3 rounded-lg border border-red-200">
                                            {{ profileForm.errors.email }}
                                        </div>
                                    </div>

                                    <div class="flex justify-center pt-4">
                                        <button
                                            type="submit"
                                            :disabled="profileForm.processing"
                                            class="inline-flex items-center px-8 py-4 bg-gradient-to-l from-purple-600 to-blue-600 border border-transparent rounded-xl font-bold text-white hover:from-purple-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200 shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
                                        >
                                            <svg v-if="profileForm.processing" class="animate-spin h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            <svg v-else class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                            {{ profileForm.processing ? 'جاري الحفظ...' : 'حفظ التغييرات' }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Change Password Form -->
                        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                            <div class="px-8 py-6 bg-gradient-to-l from-green-600 via-emerald-600 to-teal-700">
                                <div class="text-center">
                                    <h3 class="text-xl font-bold text-white mb-2">
                                        تغيير كلمة المرور
                                    </h3>
                                    <p class="text-green-100 text-sm">تأكد من أن حسابك يستخدم كلمة مرور قوية للبقاء آمناً</p>
                                </div>
                            </div>
                            <div class="p-8">
                                <form @submit.prevent="updatePassword" class="space-y-6">
                                    <div>
                                        <div class="mb-3" style="text-align: right !important;">
                                            <label for="current_password" class="text-lg font-bold text-gray-900" style="text-align: right !important; display: block;">
                                                كلمة المرور الحالية
                                            </label>
                                        </div>
                                        <div class="relative">
                                            <input
                                                id="current_password"
                                                type="password"
                                                v-model="passwordForm.current_password"
                                                required
                                                class="w-full border-2 border-gray-300 rounded-xl px-6 py-4 pr-14 text-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 bg-gray-50"
                                                :class="{ 'border-red-500 bg-red-50': passwordForm.errors.current_password }"
                                                placeholder="أدخل كلمة المرور الحالية"
                                                dir="rtl"
                                                style="text-align: right !important; direction: rtl !important;"
                                            />
                                            <div class="absolute left-4 top-1/2 transform -translate-y-1/2">
                                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div v-if="passwordForm.errors.current_password" class="mt-2 text-sm text-red-600 text-right bg-red-50 p-3 rounded-lg border border-red-200">
                                            {{ passwordForm.errors.current_password }}
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <div class="mb-3" style="text-align: right !important;">
                                                <label for="password" class="text-lg font-bold text-gray-900" style="text-align: right !important; display: block;">
                                                    كلمة المرور الجديدة
                                                </label>
                                            </div>
                                            <div class="relative">
                                                <input
                                                    id="password"
                                                    type="password"
                                                    v-model="passwordForm.password"
                                                    required
                                                    class="w-full border-2 border-gray-300 rounded-xl px-6 py-4 pr-14 text-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 bg-gray-50"
                                                    :class="{ 'border-red-500 bg-red-50': passwordForm.errors.password }"
                                                    placeholder="أدخل كلمة المرور الجديدة"
                                                    dir="rtl"
                                                    style="text-align: right !important; direction: rtl !important;"
                                                />
                                                <div class="absolute left-4 top-1/2 transform -translate-y-1/2">
                                                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <div v-if="passwordForm.errors.password" class="mt-2 text-sm text-red-600 text-right bg-red-50 p-3 rounded-lg border border-red-200">
                                                {{ passwordForm.errors.password }}
                                            </div>
                                        </div>

                                        <div>
                                            <div class="mb-3" style="text-align: right !important;">
                                                <label for="password_confirmation" class="text-lg font-bold text-gray-900" style="text-align: right !important; display: block;">
                                                    تأكيد كلمة المرور الجديدة
                                                </label>
                                            </div>
                                            <div class="relative">
                                                <input
                                                    id="password_confirmation"
                                                    type="password"
                                                    v-model="passwordForm.password_confirmation"
                                                    required
                                                    class="w-full border-2 border-gray-300 rounded-xl px-6 py-4 pr-14 text-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 bg-gray-50"
                                                    :class="{ 'border-red-500 bg-red-50': passwordForm.errors.password_confirmation }"
                                                    placeholder="أعد إدخال كلمة المرور الجديدة"
                                                    dir="rtl"
                                                    style="text-align: right !important; direction: rtl !important;"
                                                />
                                                <div class="absolute left-4 top-1/2 transform -translate-y-1/2">
                                                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <div v-if="passwordForm.errors.password_confirmation" class="mt-2 text-sm text-red-600 text-right bg-red-50 p-3 rounded-lg border border-red-200">
                                                {{ passwordForm.errors.password_confirmation }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex justify-end pt-4">
                                        <button
                                            type="submit"
                                            :disabled="passwordForm.processing"
                                            class="inline-flex items-center px-8 py-4 bg-gradient-to-l from-green-600 to-emerald-600 border border-transparent rounded-xl font-bold text-white hover:from-green-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
                                        >
                                            <svg v-if="passwordForm.processing" class="animate-spin h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            <svg v-else class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                            </svg>
                                            {{ passwordForm.processing ? 'جاري التحديث...' : 'تحديث كلمة المرور' }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Account Settings -->

                </div>
            </div>
        </div>
    </ClientLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import ClientLayout from '@/Layouts/ClientLayout.vue';

// Props
const props = defineProps({
    user: Object,
    stats: {
        type: Object,
        default: () => ({})
    }
});

// Reactive data
const imagePreview = ref(null);
const imageInput = ref(null);

// Forms
const profileForm = useForm({
    email: props.user.email || '',
    image: null,
    remove_image: false,
});

const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

// Methods
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
    profileForm.patch(route('client.profile.update'), {
        preserveScroll: true,
        onSuccess: () => {
            profileForm.remove_image = false;
        },
    });
};

const updateProfile = () => {
    if (profileForm.image) {
        // If there's an image, use POST with _method=PATCH
        profileForm.transform((data) => ({
            ...data,
            _method: 'PATCH'
        })).post(route('client.profile.update'), {
            preserveScroll: true,
            onSuccess: () => {
                imagePreview.value = null;
            },
        });
    } else {
        // Otherwise use PATCH normal
        profileForm.patch(route('client.profile.update'), {
            preserveScroll: true,
            onSuccess: () => {
                imagePreview.value = null;
            },
        });
    }
};

const updatePassword = () => {
    passwordForm.put(route('client.password.update'), {
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
</script>