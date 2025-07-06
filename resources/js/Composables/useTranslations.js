import { computed } from 'vue'

// Traductions EXCLUSIVEMENT en ARABE
const arabicTranslations = {
    // Navigation et menu
    'admin.dashboard': 'لوحة التحكم',
    'admin.dashboard_overview': 'نظرة عامة على إحصائيات النظام والنشاط الحديث',
    'common.users': 'المستخدمين',
    'common.admin': 'المشرف',
    'common.user': 'المستخدم',
    'common.recent_projects': 'المشاريع الحديثة',
    'common.view': 'عرض',
    'common.profile': 'الملف الشخصي',
    'common.contacts': 'جهات الاتصال',
    'common.templates': 'القوالب',
    'common.design_tools': 'أدوات التصميم',
    'common.settings': 'الإعدادات',
    'common.smart_cards': 'البطاقات الذكية',

    // Authentification
    'auth.login': 'تسجيل الدخول',
    'auth.logout': 'تسجيل الخروج',
    'auth.register': 'إنشاء حساب',
    'auth.name': 'الاسم',
    'auth.email': 'البريد الإلكتروني',
    'auth.password': 'كلمة المرور',
    'auth.confirm_password': 'تأكيد كلمة المرور',
    'auth.current_password': 'كلمة المرور الحالية',
    'auth.new_password': 'كلمة المرور الجديدة',
    'auth.remember_me': 'تذكرني',
    'auth.forgot_password': 'نسيت كلمة المرور؟',
    'auth.reset_password': 'إعادة تعيين كلمة المرور',

    // Profil
    'profile.edit': 'تعديل الملف الشخصي',
    'profile.information': 'معلومات الملف الشخصي',
    'profile.update_password': 'تحديث كلمة المرور',
    'profile.delete_account': 'حذف الحساب',
    'profile.photo': 'صورة الملف الشخصي',
    'profile.select_photo': 'اختر صورة جديدة',
    'profile.remove_photo': 'إزالة الصورة',
    'profile.save': 'حفظ',
    'profile.saved': 'تم الحفظ.',
    'profile.delete_confirm': 'هل أنت متأكد من رغبتك في حذف حسابك؟',
    'profile.delete_warning': 'بمجرد حذف حسابك، سيتم حذف جميع موارده وبياناته نهائياً.',
    'profile.delete_instruction': 'يرجى إدخال كلمة المرور لتأكيد رغبتك في حذف حسابك نهائياً.',

    // Actions
    'common.save': 'حفظ',
    'common.cancel': 'إلغاء',
    'common.delete': 'حذف',
    'common.edit': 'تعديل',
    'common.create': 'إنشاء',
    'common.update': 'تحديث',
    'common.search': 'بحث',
    'common.clear': 'مسح',
    'common.actions': 'الإجراءات',
    'common.role': 'الدور',
    'common.created_at': 'تاريخ الإنشاء',

    // Rôles
    'roles.admin': 'مشرف',
    'roles.super_admin': 'مشرف عام',
    'roles.client': 'عميل',

    // Messages
    'messages.success': 'تم بنجاح',
    'messages.error': 'حدث خطأ',
    'messages.confirm': 'تأكيد',
    'messages.warning': 'تحذير',

    // User Management
    'users.title': 'إدارة المستخدمين',
    'users.manage': 'إدارة وتنظيم جميع المستخدمين في النظام',
    'users.add': 'إضافة مستخدم',
    'users.list': 'قائمة المستخدمين',
    'users.showing_results': 'عرض النتائج من :from إلى :to من أصل :total',

    // Contacts
    'contacts.title': 'جهات الاتصال',
    'contacts.manage': 'إدارة جهات الاتصال',

    // Validation
    'validation.required': 'هذا الحقل مطلوب',
    'validation.email': 'يجب أن يكون البريد الإلكتروني صحيحاً',
    'validation.min': 'يجب أن يكون الحد الأدنى :min أحرف',
    'validation.confirmed': 'تأكيد كلمة المرور غير متطابق',

    // Success Messages
    'common.user_created_successfully': 'تم إنشاء المستخدم بنجاح',
    'common.user_updated_successfully': 'تم تحديث المستخدم بنجاح',
    'common.user_deleted_successfully': 'تم حذف المستخدم بنجاح',
    'common.profile_updated': 'تم تحديث الملف الشخصي بنجاح',
    'common.password_updated': 'تم تحديث كلمة المرور بنجاح',
}

export function useTranslations() {
    // Force ARABIC ONLY
    const currentLocale = computed(() => 'ar')
    const translations = computed(() => arabicTranslations)

    const __ = (key, replacements = {}) => {
        let translation = arabicTranslations[key] || key

        // Replace placeholders
        Object.keys(replacements).forEach(placeholder => {
            const regex = new RegExp(`:${placeholder}`, 'g')
            translation = translation.replace(regex, replacements[placeholder])
        })

        return translation
    }

    // ALWAYS RTL for Arabic
    const isRTL = computed(() => true)
    const direction = computed(() => 'rtl')

    return {
        currentLocale,
        translations,
        __,
        isRTL,
        direction
    }
}