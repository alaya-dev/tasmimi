<template>
    <Head title="اتصل بنا" />

    <ClientLayout>
        <div class="min-h-screen bg-gradient-to-br from-purple-50 via-blue-50 to-indigo-100 py-12">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header Section -->
                <div class="text-center mb-12">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-purple-600 to-blue-600 rounded-full mb-6">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                        </svg>
                    </div>
                    <h1 class="text-4xl font-bold text-gray-900 mb-4">اتصل بنا</h1>
                    <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                        نحن هنا لمساعدتك! أرسل لنا رسالة وسنقوم بالرد عليك في أقرب وقت ممكن
                    </p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Contact Info -->
                    <div class="lg:col-span-1">
                        
                    </div>

                    <!-- Contact Form -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-2xl shadow-xl p-8">
                            <h3 class="text-2xl font-bold text-gray-900 mb-6 text-right">أرسل لنا رسالة</h3>
                            
                            <form @submit.prevent="submitForm" class="space-y-6">
                              

                                <!-- Subject Field -->
                                <div>
                                    <label for="subject" class="block text-sm font-medium text-gray-700 mb-2 text-right">
                                        الموضوع *
                                    </label>
                                    <input
                                        id="subject"
                                        v-model="form.subject"
                                        type="text"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-right"
                                        :class="{ 'border-red-500': form.errors.subject }"
                                        required
                                    />
                                    <div v-if="form.errors.subject" class="mt-1 text-sm text-red-600 text-right">
                                        {{ form.errors.subject }}
                                    </div>
                                </div>

                                <!-- Message Field -->
                                <div>
                                    <label for="content" class="block text-sm font-medium text-gray-700 mb-2 text-right">
                                        الرسالة *
                                    </label>
                                    <textarea
                                        id="content"
                                        v-model="form.content"
                                        rows="6"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-right resize-none"
                                        :class="{ 'border-red-500': form.errors.content }"
                                        placeholder="اكتب رسالتك هنا..."
                                        required
                                    ></textarea>
                                    <div v-if="form.errors.content" class="mt-1 text-sm text-red-600 text-right">
                                        {{ form.errors.content }}
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="flex justify-end">
                                    <button
                                        type="submit"
                                        :disabled="form.processing"
                                        class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-purple-600 to-blue-600 text-white font-semibold rounded-lg hover:from-purple-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                                    >
                                        <svg v-if="form.processing" class="animate-spin h-5 w-5 ml-3" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <svg v-else class="w-5 h-5 ml-2" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                                        </svg>
                                        إرسال الرسالة
                                    </button>
                                </div>
                            </form>

                            <!-- Success Message -->
                            <div v-if="showSuccess" class="mt-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg text-right">
                                <div class="flex items-center flex-row-reverse">
                                    <svg class="w-5 h-5 ml-2" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                    </svg>
                                    تم إرسال رسالتك بنجاح! سنقوم بالرد عليك قريباً.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </ClientLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import ClientLayout from '@/Layouts/ClientLayout.vue';

const showSuccess = ref(false);

const form = useForm({
    email: '',
    subject: '',
    content: '',
});

const submitForm = () => {
    form.post(route('client.contact.store'), {
        onSuccess: () => {
            showSuccess.value = true;
            form.reset();
            setTimeout(() => {
                showSuccess.value = false;
            }, 5000);
        },
    });
};
</script>
