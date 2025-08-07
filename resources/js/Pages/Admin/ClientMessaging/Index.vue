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
                <!-- Send Type Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        نوع الإرسال <span class="text-red-500">*</span>
                    </label>
                    <div class="flex space-x-4 space-x-reverse">
                        <label class="flex items-center">
                            <input
                                type="radio"
                                v-model="form.send_type"
                                value="single"
                                class="form-radio h-4 w-4 text-blue-600"
                            />
                            <span class="mr-2 text-sm">إرسال لعميل واحد</span>
                        </label>
                        <label class="flex items-center">
                            <input
                                type="radio"
                                v-model="form.send_type"
                                value="all"
                                class="form-radio h-4 w-4 text-blue-600"
                            />
                            <span class="mr-2 text-sm">إرسال لجميع العملاء</span>
                        </label>
                    </div>
                </div>

                <!-- Client Selection (Only show when send_type is 'single') -->
                <div v-if="form.send_type === 'single'">
                    <label for="client_id" class="block text-sm font-medium text-gray-700 mb-2">
                        اختيار العميل <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="client_id"
                        v-model="form.client_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        :required="form.send_type === 'single'"
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

                <!-- All Clients Info (Only show when send_type is 'all') -->
                <div v-if="form.send_type === 'all'" class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-yellow-600 ml-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-yellow-800">سيتم إرسال الرسالة إلى جميع العملاء</p>
                            <p class="text-sm text-yellow-700">عدد العملاء: {{ clients.length }} عميل</p>
                        </div>
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
                            :disabled="form.processing || !form.send_type || !form.subject || !form.message || (form.send_type === 'single' && !form.client_id)"
                            class="bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white font-bold py-2 px-6 rounded-lg transition duration-200 disabled:cursor-not-allowed flex items-center"
                        >
                            <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span v-if="form.processing">جاري الإرسال...</span>
                            <span v-else-if="form.send_type === 'all'">إرسال لجميع العملاء ({{ clients.length }})</span>
                            <span v-else>إرسال الرسالة</span>
                        </button>
                    </div>
                </div>
            </form>

            <!-- Instructions -->
            <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <h4 class="font-medium text-blue-900 mb-2">تعليمات الاستخدام:</h4>
                <ul class="text-sm text-blue-800 space-y-1">
                    <li>• اختر نوع الإرسال: لعميل واحد أو لجميع العملاء</li>
                    <li>• في حالة الإرسال لعميل واحد: اختر العميل من القائمة</li>
                    <li>• في حالة الإرسال الجماعي: سيتم إرسال الرسالة لجميع العملاء المسجلين</li>
                    <li>• أدخل موضوع واضح ومفهوم للرسالة</li>
                    <li>• اكتب محتوى الرسالة بشكل مهذب ومفصل</li>
                    <li>• تأكد من مراجعة المحتوى قبل الإرسال</li>
                    <li>• سيتم إرسال الرسالة إلى البريد الإلكتروني المسجل للعملاء</li>
                </ul>
            </div>
        </div>

        <!-- Confirmation Modal for Mass Send -->
        <div v-if="showConfirmModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4" dir="rtl">
                <div class="flex items-center mb-4">
                    <svg class="h-6 w-6 text-yellow-600 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900">تأكيد الإرسال الجماعي</h3>
                </div>
                <p class="text-sm text-gray-600 mb-6">
                    هل أنت متأكد من إرسال هذه الرسالة إلى جميع العملاء ({{ clients.length }} عميل)؟
                    <br><br>
                    <strong>الموضوع:</strong> {{ form.subject }}
                </p>
                <div class="flex justify-end space-x-3 space-x-reverse">
                    <button
                        @click="showConfirmModal = false"
                        type="button"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200"
                    >
                        إلغاء
                    </button>
                    <button
                        @click="confirmSend"
                        type="button"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700"
                    >
                        تأكيد الإرسال
                    </button>
                </div>
            </div>
        </div>
    </AdminLayoutSidebar>
</template>

<script setup>
import { ref, watch } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayoutSidebar from '@/Layouts/AdminLayoutSidebar.vue'

const props = defineProps({
    clients: Array
})

const form = useForm({
    send_type: 'single', // Default to single client
    client_id: '',
    subject: '',
    message: ''
})

const showConfirmModal = ref(false)

// Reset client_id when send_type changes
watch(() => form.send_type, (newType) => {
    if (newType === 'all') {
        form.client_id = ''
    }
})

const submit = () => {
    // Show confirmation modal for mass send
    if (form.send_type === 'all') {
        showConfirmModal.value = true
        return
    }
    
    // Send directly for single client
    sendMessage()
}

const confirmSend = () => {
    showConfirmModal.value = false
    sendMessage()
}

const sendMessage = () => {
    form.post(route('admin.client-messaging.send'), {
        onSuccess: () => {
            // Reset form after successful submission
            form.reset()
            form.send_type = 'single' // Reset to default
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
