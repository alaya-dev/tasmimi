import { ref, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

export function useTranslations() {
    const page = usePage()
    
    // Get current locale from page props
    const currentLocale = computed(() => page.props.locale || 'ar')
    
    // Get translations from page props
    const translations = computed(() => page.props.translations || {})
    
    // Translation function
    const __ = (key, replacements = {}) => {
        let translation = translations.value[key] || key
        
        // Handle replacements like :attribute, :seconds, etc.
        Object.keys(replacements).forEach(placeholder => {
            const regex = new RegExp(`:${placeholder}`, 'g')
            translation = translation.replace(regex, replacements[placeholder])
        })
        
        return translation
    }
    
    // Check if current locale is RTL
    const isRTL = computed(() => currentLocale.value === 'ar')
    
    // Get direction for CSS
    const direction = computed(() => isRTL.value ? 'rtl' : 'ltr')
    
    return {
        currentLocale,
        translations,
        __,
        isRTL,
        direction
    }
}