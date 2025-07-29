<template>
    <Head title="إنشاء اتفاقية الاستخدام" />

    <AdminLayoutSidebar>
        <div class="p-6">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">إنشاء اتفاقية الاستخدام</h1>
                    <p class="text-gray-600 mt-1">إنشاء اتفاقية استخدام جديدة للموقع</p>
                </div>
                
                <Link
                    :href="route('admin.terms-of-service.index')"
                    class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    العودة للقائمة
                </Link>
            </div>

            <!-- Form -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">معلومات الاتفاقية</h2>
                </div>
                
                <form @submit.prevent="submit" class="p-6 space-y-6">
                    <!-- Title -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2 text-right">
                            عنوان الاتفاقية <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="title"
                            v-model="form.title"
                            type="text"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-right"
                            :class="{ 'border-red-500': errors.title }"
                            placeholder="مثال: اتفاقية الاستخدام وسياسة الخصوصية"
                            required
                        />
                        <div v-if="errors.title" class="mt-1 text-sm text-red-600 text-right">
                            {{ errors.title }}
                        </div>
                    </div>

                    <!-- Version -->
                    <div>
                        <label for="version" class="block text-sm font-medium text-gray-700 mb-2 text-right">
                            رقم الإصدار <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="version"
                            v-model="form.version"
                            type="text"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-right"
                            :class="{ 'border-red-500': errors.version }"
                            placeholder="1.0"
                            required
                        />
                        <div v-if="errors.version" class="mt-1 text-sm text-red-600 text-right">
                            {{ errors.version }}
                        </div>
                    </div>

                    <!-- Effective Date -->
                    <div>
                        <label for="effective_date" class="block text-sm font-medium text-gray-700 mb-2 text-right">
                            تاريخ السريان
                        </label>
                        <input
                            id="effective_date"
                            v-model="form.effective_date"
                            type="date"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            :class="{ 'border-red-500': errors.effective_date }"
                        />
                        <div v-if="errors.effective_date" class="mt-1 text-sm text-red-600 text-right">
                            {{ errors.effective_date }}
                        </div>
                    </div>

                    <!-- Content -->
                    <div>
                        <label for="content" class="block text-sm font-medium text-gray-700 mb-2 text-right">
                            محتوى الاتفاقية <span class="text-red-500">*</span>
                        </label>
                        <div class="mb-2 text-sm text-gray-600 text-right">
                            يمكنك استخدام HTML لتنسيق النص. مثال:
                            <code class="bg-gray-100 px-1 rounded">
                                &lt;h1&gt;المادة 1: المقدمة&lt;/h1&gt; &lt;h3&gt;1.1 التعريفات&lt;/h3&gt; &lt;p&gt;النص...&lt;/p&gt;
                            </code>
                        </div>
                        <textarea
                            id="content"
                            v-model="form.content"
                            rows="20"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-right font-mono"
                            :class="{ 'border-red-500': errors.content }"
                            placeholder="أدخل محتوى اتفاقية الاستخدام هنا..."
                            required
                            dir="rtl"
                        ></textarea>
                        <div v-if="errors.content" class="mt-1 text-sm text-red-600 text-right">
                            {{ errors.content }}
                        </div>
                    </div>

                    <!-- Template -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-blue-900 mb-2 text-right">قالب اتفاقية الاستخدام</h3>
                        <p class="text-sm text-blue-700 mb-3 text-right">يمكنك استخدام هذا القالب كنقطة بداية:</p>
                        <button
                            type="button"
                            @click="useTemplate"
                            class="text-sm bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 transition-colors"
                        >
                            استخدام القالب
                        </button>
                    </div>

                    <!-- Active Status -->
                    <div class="flex items-center">
                        <input
                            id="is_active"
                            v-model="form.is_active"
                            type="checkbox"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        />
                        <label for="is_active" class="mr-2 block text-sm text-gray-900">
                            تفعيل هذه الاتفاقية (سيتم إلغاء تفعيل الاتفاقيات الأخرى)
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end space-x-3 space-x-reverse">
                        <Link
                            :href="route('admin.terms-of-service.index')"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors"
                        >
                            إلغاء
                        </Link>
                        <button
                            type="submit"
                            :disabled="processing"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50"
                        >
                            {{ processing ? 'جاري الحفظ...' : 'حفظ الاتفاقية' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayoutSidebar>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayoutSidebar from '@/Layouts/AdminLayoutSidebar.vue'

// Form
const form = useForm({
    title: 'اتفاقية الاستخدام وسياسة الخصوصية',
    content: '',
    version: '1.0',
    effective_date: new Date().toISOString().split('T')[0],
    is_active: true,
})

const processing = ref(false)
const errors = ref({})

// Template content
const templateContent = `<div dir="rtl" style="text-align: right; font-family: 'Arial', sans-serif; line-height: 1.8; max-width: 800px; margin: 0 auto; padding: 20px;">

<h1 style="font-size: 2.5em; font-weight: bold; color: #1f2937; margin-bottom: 30px; text-align: center; border-bottom: 3px solid #3b82f6; padding-bottom: 15px;">
    اتفاقية الاستخدام وسياسة الخصوصية
</h1>

<p style="font-size: 1.1em; color: #6b7280; text-align: center; margin-bottom: 40px;">
    آخر تحديث: {{ new Date().toLocaleDateString('ar-SA') }}
</p>

<h2 style="font-size: 1.8em; font-weight: bold; color: #1f2937; margin: 30px 0 20px 0; border-right: 4px solid #3b82f6; padding-right: 15px;">
    المادة 1: المقدمة
</h2>

<h3 style="font-size: 1.3em; font-weight: bold; color: #374151; margin: 20px 0 10px 0;">
    1.1 التعريفات
</h3>

<p style="margin-bottom: 15px; text-align: justify;">
    مرحباً بكم في موقعنا. تحكم هذه الاتفاقية استخدامكم لموقعنا الإلكتروني وجميع الخدمات المتاحة عليه. باستخدام هذا الموقع، فإنكم توافقون على الالتزام بشروط وأحكام هذه الاتفاقية.
</p>

<h3 style="font-size: 1.3em; font-weight: bold; color: #374151; margin: 20px 0 10px 0;">
    1.2 نطاق التطبيق
</h3>

<p style="margin-bottom: 15px; text-align: justify;">
    تنطبق هذه الاتفاقية على جميع المستخدمين، بما في ذلك الزوار المتصفحين للموقع والمستخدمين المسجلين والعملاء الذين يقومون بشراء الخدمات.
</p>

<h2 style="font-size: 1.8em; font-weight: bold; color: #1f2937; margin: 30px 0 20px 0; border-right: 4px solid #3b82f6; padding-right: 15px;">
    المادة 2: شروط الاستخدام
</h2>

<h3 style="font-size: 1.3em; font-weight: bold; color: #374151; margin: 20px 0 10px 0;">
    2.1 قبول الشروط
</h3>

<p style="margin-bottom: 15px; text-align: justify;">
    بدخولكم إلى هذا الموقع واستخدامه، فإنكم تقرون بأنكم قد قرأتم وفهمتم ووافقتم على الالتزام بجميع الشروط والأحكام المذكورة في هذه الاتفاقية.
</p>

<h3 style="font-size: 1.3em; font-weight: bold; color: #374151; margin: 20px 0 10px 0;">
    2.2 الأهلية للاستخدام
</h3>

<p style="margin-bottom: 15px; text-align: justify;">
    يجب أن تكونوا أكبر من 18 عاماً أو لديكم موافقة ولي الأمر لاستخدام هذا الموقع. كما يجب أن تتمتعوا بالأهلية القانونية للدخول في اتفاقية ملزمة.
</p>

<h2 style="font-size: 1.8em; font-weight: bold; color: #1f2937; margin: 30px 0 20px 0; border-right: 4px solid #3b82f6; padding-right: 15px;">
    المادة 3: الخدمات المقدمة
</h2>

<h3 style="font-size: 1.3em; font-weight: bold; color: #374151; margin: 20px 0 10px 0;">
    3.1 وصف الخدمات
</h3>

<p style="margin-bottom: 15px; text-align: justify;">
    نقدم خدمات تصميم البطاقات والقوالب الرقمية. تشمل خدماتنا إنشاء وتخصيص وتحميل التصاميم المختلفة لتلبية احتياجاتكم.
</p>

<h3 style="font-size: 1.3em; font-weight: bold; color: #374151; margin: 20px 0 10px 0;">
    3.2 توفر الخدمات
</h3>

<p style="margin-bottom: 15px; text-align: justify;">
    نسعى لضمان توفر الخدمات على مدار الساعة، لكننا لا نضمن عدم انقطاع الخدمة لأسباب فنية أو صيانة أو ظروف خارجة عن إرادتنا.
</p>

<h2 style="font-size: 1.8em; font-weight: bold; color: #1f2937; margin: 30px 0 20px 0; border-right: 4px solid #3b82f6; padding-right: 15px;">
    المادة 4: سياسة الخصوصية
</h2>

<h3 style="font-size: 1.3em; font-weight: bold; color: #374151; margin: 20px 0 10px 0;">
    4.1 جمع المعلومات
</h3>

<p style="margin-bottom: 15px; text-align: justify;">
    نقوم بجمع المعلومات التي تقدمونها بشكل مباشر عند التسجيل أو استخدام خدماتنا، بما في ذلك الاسم، البريد الإلكتروني، ومعلومات الدفع.
</p>

<h3 style="font-size: 1.3em; font-weight: bold; color: #374151; margin: 20px 0 10px 0;">
    4.2 استخدام المعلومات
</h3>

<p style="margin-bottom: 15px; text-align: justify;">
    نستخدم معلوماتكم لتقديم الخدمات، معالجة المدفوعات، التواصل معكم، وتحسين تجربتكم على الموقع.
</p>

<h3 style="font-size: 1.3em; font-weight: bold; color: #374151; margin: 20px 0 10px 0;">
    4.3 حماية المعلومات
</h3>

<p style="margin-bottom: 15px; text-align: justify;">
    نتخذ تدابير أمنية مناسبة لحماية معلوماتكم الشخصية من الوصول غير المصرح به أو التغيير أو الكشف أو التدمير.
</p>

<h2 style="font-size: 1.8em; font-weight: bold; color: #1f2937; margin: 30px 0 20px 0; border-right: 4px solid #3b82f6; padding-right: 15px;">
    المادة 5: الدفع والاسترداد
</h2>

<h3 style="font-size: 1.3em; font-weight: bold; color: #374151; margin: 20px 0 10px 0;">
    5.1 أسعار الخدمات
</h3>

<p style="margin-bottom: 15px; text-align: justify;">
    جميع الأسعار معروضة بالريال السعودي وتشمل ضريبة القيمة المضافة حسب اللوائح المعمول بها في المملكة العربية السعودية.
</p>

<h3 style="font-size: 1.3em; font-weight: bold; color: #374151; margin: 20px 0 10px 0;">
    5.2 طرق الدفع
</h3>

<p style="margin-bottom: 15px; text-align: justify;">
    نقبل الدفع عبر البطاقات الائتمانية وطرق الدفع الإلكترونية المتاحة على الموقع.
</p>

<h2 style="font-size: 1.8em; font-weight: bold; color: #1f2937; margin: 30px 0 20px 0; border-right: 4px solid #3b82f6; padding-right: 15px;">
    المادة 6: حقوق الملكية الفكرية
</h2>

<p style="margin-bottom: 15px; text-align: justify;">
    جميع المحتويات الموجودة على هذا الموقع، بما في ذلك النصوص والصور والرسوم والتصاميم والشعارات، محمية بحقوق الطبع والنشر وحقوق الملكية الفكرية.
</p>

<h2 style="font-size: 1.8em; font-weight: bold; color: #1f2937; margin: 30px 0 20px 0; border-right: 4px solid #3b82f6; padding-right: 15px;">
    المادة 7: إخلاء المسؤولية
</h2>

<p style="margin-bottom: 15px; text-align: justify;">
    نبذل قصارى جهدنا لتقديم خدمات عالية الجودة، لكننا لا نضمن خلو الخدمات من الأخطاء أو أنها ستلبي جميع توقعاتكم.
</p>

<h2 style="font-size: 1.8em; font-weight: bold; color: #1f2937; margin: 30px 0 20px 0; border-right: 4px solid #3b82f6; padding-right: 15px;">
    المادة 8: تعديل الاتفاقية
</h2>

<p style="margin-bottom: 15px; text-align: justify;">
    نحتفظ بالحق في تعديل هذه الاتفاقية في أي وقت. سيتم نشر أي تغييرات على هذه الصفحة مع تاريخ التحديث.
</p>

<h2 style="font-size: 1.8em; font-weight: bold; color: #1f2937; margin: 30px 0 20px 0; border-right: 4px solid #3b82f6; padding-right: 15px;">
    المادة 9: التواصل
</h2>

<p style="margin-bottom: 15px; text-align: justify;">
    إذا كان لديكم أي أسئلة حول هذه الاتفاقية، يرجى التواصل معنا عبر البريد الإلكتروني أو نموذج الاتصال المتاح على الموقع.
</p>

<div style="margin-top: 40px; padding: 20px; background-color: #f3f4f6; border-radius: 8px; text-align: center;">
    <p style="margin: 0; color: #6b7280;">
        شكراً لاختياركم خدماتنا. نتطلع لخدمتكم بأفضل ما لدينا.
    </p>
</div>

</div>`

// Methods
const useTemplate = () => {
    form.content = templateContent
}

const submit = () => {
    processing.value = true
    form.post(route('admin.terms-of-service.store'), {
        onSuccess: () => {
            processing.value = false
        },
        onError: (formErrors) => {
            processing.value = false
            errors.value = formErrors
        }
    })
}
</script>
