<template>
    <AdminLayoutSidebar>
        <Head title="مراسلة العملاء" />
        
        <div class="bg-white rounded-lg shadow-md p-6" dir="rtl">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900 mb-2">مراسلة العملاء</h1>
                <p class="text-gray-600">إرسال رسائل إلكترونية مخصصة للعملاء</p>
            </div>

            <!-- Success/Error Messages -->
            <div v-if="$page.props.flash.success" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-green-600 ml-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-sm font-medium text-green-800">{{ $page.props.flash.success }}</span>
                </div>
            </div>

            <div v-if="$page.props.flash.error" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-red-600 ml-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-4a1 1 0 102 0v-4a1 1 0 10-2 0v4zm0-8a1 1 0 102 0 1 1 0 00-2 0z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-sm font-medium text-red-800">{{ $page.props.flash.error }}</span>
                </div>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- Client Selection -->
                <div>
                    <label for="client_id" class="block text-sm font-medium text-gray-700 mb-2">
                        اختيار العميل <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="client_id"
                        v-model="form.client_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        required
                    >
                        <option value="">اختر العميل...</option>
                        <option v-for="client in clients" :key="client.id" :value="client.id">
                            {{ client.email }} {{ client.phone ? '- ' + client.phone : '- لا يوجد رقم هاتف' }}
                        </option>
                    </select>
                    <div v-if="form.errors.client_id" class="mt-1 text-sm text-red-600">
                        {{ form.errors.client_id }}
                    </div>
                </div>

                <!-- Subject Field -->
                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
                        موضوع الرسالة <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="subject"
                        v-model="form.subject"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="أدخل موضوع الرسالة..."
                        maxlength="255"
                        required
                    />
                    <div v-if="form.errors.subject" class="mt-1 text-sm text-red-600">
                        {{ form.errors.subject }}
                    </div>
                    <p class="mt-1 text-sm text-gray-500">{{ form.subject.length }}/255 حرف</p>
                </div>

                <!-- Message Content -->
                <div>
                    <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                        محتوى الرسالة <span class="text-red-500">*</span>
                    </label>
                    <textarea
                        id="message"
                        v-model="form.message"
                        rows="8"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                        placeholder="اكتب محتوى الرسالة هنا..."
                        maxlength="2000"
                        required
                        style="direction: rtl; text-align: right;"
                    ></textarea>
                    <div v-if="form.errors.message" class="mt-1 text-sm text-red-600">
                        {{ form.errors.message }}
                    </div>
                    <p class="mt-1 text-sm text-gray-500">{{ form.message.length }}/2000 حرف</p>
                </div>

                

                <!-- Action Buttons -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <Link
                            :href="route('admin.dashboard')"
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded-lg transition duration-200"
                        >
                            إلغاء
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing || !form.client_id || !form.subject || !form.message"
                            class="bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white font-bold py-2 px-6 rounded-lg transition duration-200 disabled:cursor-not-allowed flex items-center"
                        >
                            <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span v-if="form.processing">جاري الإرسال...</span>
                            <span v-else>إرسال الرسالة</span>
                        </button>
                    </div>
                </div>
            </form>

            <!-- Instructions -->
            <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <h4 class="font-medium text-blue-900 mb-2">تعليمات الاستخدام:</h4>
                <ul class="text-sm text-blue-800 space-y-1">
                    <li>• اختر العميل المطلوب إرسال الرسالة إليه من القائمة</li>
                    <li>• أدخل موضوع واضح ومفهوم للرسالة</li>
                    <li>• اكتب محتوى الرسالة بشكل مهذب ومفصل</li>
                    <li>• تأكد من مراجعة المعاينة قبل الإرسال</li>
                    <li>• سيتم إرسال الرسالة إلى البريد الإلكتروني المسجل للعميل</li>
                </ul>
            </div>
        </div>
    </AdminLayoutSidebar>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayoutSidebar from '@/Layouts/AdminLayoutSidebar.vue'

const props = defineProps({
    clients: Array
})

const form = useForm({
    client_id: '',
    subject: '',
    message: ''
})

const submit = () => {
    form.post(route('admin.client-messaging.send'), {
        onSuccess: () => {
            // Reset form after successful submission
            form.reset()
        },
        onError: (errors) => {
            console.error('Validation errors:', errors)
        }
    })
}

const getSelectedClientName = () => {
    const client = props.clients.find(c => c.id == form.client_id)
    return client ? client.email : ''
}

const getSelectedClientEmail = () => {
    const client = props.clients.find(c => c.id == form.client_id)
    return client ? client.email : ''
}

const getSelectedClientPhone = () => {
    const client = props.clients.find(c => c.id == form.client_id)
    return client ? client.phone : null
}
</script>

<style scoped>
/* Custom styles for RTL text direction */
.rtl-text {
    direction: rtl;
    text-align: right;
}
</style>
