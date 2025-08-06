/**
 * Style Manager Configuration for Arabic Template Editor
 * Provides RTL-friendly styling options and Arabic typography
 */

export const styleManagerConfig = {
    sectors: [
        {
            name: 'التخطيط العام',
            open: true,
            properties: [
                {
                    name: 'العرض',
                    property: 'width',
                    type: 'integer',
                    units: ['px', '%', 'em', 'rem'],
                    defaults: 'auto'
                },
                {
                    name: 'الارتفاع',
                    property: 'height',
                    type: 'integer',
                    units: ['px', '%', 'em', 'rem'],
                    defaults: 'auto'
                },
                {
                    name: 'العرض الأقصى',
                    property: 'max-width',
                    type: 'integer',
                    units: ['px', '%', 'em', 'rem']
                },
                {
                    name: 'الارتفاع الأقصى',
                    property: 'max-height',
                    type: 'integer',
                    units: ['px', '%', 'em', 'rem']
                },
                {
                    name: 'نوع العرض',
                    property: 'display',
                    type: 'select',
                    defaults: 'block',
                    options: [
                        { value: 'block', name: 'كتلة' },
                        { value: 'inline', name: 'سطري' },
                        { value: 'inline-block', name: 'سطري-كتلة' },
                        { value: 'flex', name: 'مرن' },
                        { value: 'grid', name: 'شبكة' },
                        { value: 'none', name: 'مخفي' }
                    ]
                },
                {
                    name: 'الموضع',
                    property: 'position',
                    type: 'select',
                    defaults: 'static',
                    options: [
                        { value: 'static', name: 'ثابت' },
                        { value: 'relative', name: 'نسبي' },
                        { value: 'absolute', name: 'مطلق' },
                        { value: 'fixed', name: 'مثبت' },
                        { value: 'sticky', name: 'لاصق' }
                    ]
                }
            ]
        },
        {
            name: 'المسافات والحدود',
            open: false,
            properties: [
                {
                    name: 'الهامش الخارجي',
                    property: 'margin',
                    type: 'composite',
                    properties: [
                        { name: 'أعلى', property: 'margin-top', type: 'integer', units: ['px', 'em', '%'] },
                        { name: 'يمين', property: 'margin-right', type: 'integer', units: ['px', 'em', '%'] },
                        { name: 'أسفل', property: 'margin-bottom', type: 'integer', units: ['px', 'em', '%'] },
                        { name: 'يسار', property: 'margin-left', type: 'integer', units: ['px', 'em', '%'] }
                    ]
                },
                {
                    name: 'الهامش الداخلي',
                    property: 'padding',
                    type: 'composite',
                    properties: [
                        { name: 'أعلى', property: 'padding-top', type: 'integer', units: ['px', 'em', '%'] },
                        { name: 'يمين', property: 'padding-right', type: 'integer', units: ['px', 'em', '%'] },
                        { name: 'أسفل', property: 'padding-bottom', type: 'integer', units: ['px', 'em', '%'] },
                        { name: 'يسار', property: 'padding-left', type: 'integer', units: ['px', 'em', '%'] }
                    ]
                },
                {
                    name: 'الحدود',
                    property: 'border',
                    type: 'composite',
                    properties: [
                        { name: 'العرض', property: 'border-width', type: 'integer', units: ['px'] },
                        { 
                            name: 'النوع', 
                            property: 'border-style', 
                            type: 'select',
                            options: [
                                { value: 'none', name: 'بدون' },
                                { value: 'solid', name: 'صلب' },
                                { value: 'dashed', name: 'متقطع' },
                                { value: 'dotted', name: 'منقط' },
                                { value: 'double', name: 'مزدوج' }
                            ]
                        },
                        { name: 'اللون', property: 'border-color', type: 'color' }
                    ]
                },
                {
                    name: 'استدارة الزوايا',
                    property: 'border-radius',
                    type: 'integer',
                    units: ['px', '%'],
                    defaults: '0'
                }
            ]
        },
        {
            name: 'النصوص والخطوط',
            open: false,
            properties: [
                {
                    name: 'نوع الخط',
                    property: 'font-family',
                    type: 'select',
                    defaults: 'Cairo, Arial, sans-serif',
                    options: [
                        { value: 'Cairo, Arial, sans-serif', name: 'Cairo (موصى به)' },
                        { value: 'Amiri, serif', name: 'Amiri (تقليدي)' },
                        { value: 'Noto Sans Arabic, sans-serif', name: 'Noto Sans Arabic' },
                        { value: 'Tajawal, sans-serif', name: 'Tajawal' },
                        { value: 'Almarai, sans-serif', name: 'Almarai' },
                        { value: 'Changa, sans-serif', name: 'Changa' },
                        { value: 'خط المهند, sans-serif', name: 'خط المهند' },
                        { value: 'Tahoma, sans-serif', name: 'Tahoma خط' }
                    ]
                },
                {
                    name: 'حجم الخط',
                    property: 'font-size',
                    type: 'integer',
                    units: ['px', 'em', 'rem', '%'],
                    defaults: '16px'
                },
                {
                    name: 'وزن الخط',
                    property: 'font-weight',
                    type: 'select',
                    defaults: 'normal',
                    options: [
                        { value: '300', name: 'خفيف' },
                        { value: '400', name: 'عادي' },
                        { value: '600', name: 'متوسط' },
                        { value: '700', name: 'عريض' },
                        { value: '900', name: 'عريض جداً' }
                    ]
                },
                {
                    name: 'لون النص',
                    property: 'color',
                    type: 'color',
                    defaults: '#333333'
                },
                {
                    name: 'محاذاة النص',
                    property: 'text-align',
                    type: 'radio',
                    defaults: 'right',
                    options: [
                        { value: 'right', name: 'يمين', className: 'fa fa-align-right' },
                        { value: 'center', name: 'وسط', className: 'fa fa-align-center' },
                        { value: 'left', name: 'يسار', className: 'fa fa-align-left' },
                        { value: 'justify', name: 'ضبط', className: 'fa fa-align-justify' }
                    ]
                },
                {
                    name: 'ارتفاع السطر',
                    property: 'line-height',
                    type: 'integer',
                    units: ['', 'px', 'em'],
                    defaults: '1.5'
                },
                {
                    name: 'تباعد الأحرف',
                    property: 'letter-spacing',
                    type: 'integer',
                    units: ['px', 'em'],
                    defaults: '0'
                },
                {
                    name: 'اتجاه النص',
                    property: 'direction',
                    type: 'radio',
                    defaults: 'rtl',
                    options: [
                        { value: 'rtl', name: 'يمين لليسار' },
                        { value: 'ltr', name: 'يسار لليمين' }
                    ]
                }
            ]
        },
        {
            name: 'الخلفية والألوان',
            open: false,
            properties: [
                {
                    name: 'لون الخلفية',
                    property: 'background-color',
                    type: 'color',
                    defaults: 'transparent'
                },
                {
                    name: 'صورة الخلفية',
                    property: 'background-image',
                    type: 'file',
                    functionName: 'url'
                },
                {
                    name: 'حجم الخلفية',
                    property: 'background-size',
                    type: 'select',
                    defaults: 'auto',
                    options: [
                        { value: 'auto', name: 'تلقائي' },
                        { value: 'cover', name: 'تغطية' },
                        { value: 'contain', name: 'احتواء' },
                        { value: '100% 100%', name: 'تمدد' }
                    ]
                },
                {
                    name: 'موضع الخلفية',
                    property: 'background-position',
                    type: 'select',
                    defaults: 'center center',
                    options: [
                        { value: 'top left', name: 'أعلى يسار' },
                        { value: 'top center', name: 'أعلى وسط' },
                        { value: 'top right', name: 'أعلى يمين' },
                        { value: 'center left', name: 'وسط يسار' },
                        { value: 'center center', name: 'وسط' },
                        { value: 'center right', name: 'وسط يمين' },
                        { value: 'bottom left', name: 'أسفل يسار' },
                        { value: 'bottom center', name: 'أسفل وسط' },
                        { value: 'bottom right', name: 'أسفل يمين' }
                    ]
                },
                {
                    name: 'تكرار الخلفية',
                    property: 'background-repeat',
                    type: 'select',
                    defaults: 'no-repeat',
                    options: [
                        { value: 'no-repeat', name: 'بدون تكرار' },
                        { value: 'repeat', name: 'تكرار' },
                        { value: 'repeat-x', name: 'تكرار أفقي' },
                        { value: 'repeat-y', name: 'تكرار عمودي' }
                    ]
                }
            ]
        },
        {
            name: 'التأثيرات والظلال',
            open: false,
            properties: [
                {
                    name: 'الشفافية',
                    property: 'opacity',
                    type: 'slider',
                    defaults: '1',
                    min: 0,
                    max: 1,
                    step: 0.1
                },
                {
                    name: 'ظل الصندوق',
                    property: 'box-shadow',
                    type: 'stack',
                    preview: true,
                    properties: [
                        { name: 'الإزاحة الأفقية', property: 'box-shadow-h', type: 'integer', units: ['px'] },
                        { name: 'الإزاحة العمودية', property: 'box-shadow-v', type: 'integer', units: ['px'] },
                        { name: 'التمويه', property: 'box-shadow-blur', type: 'integer', units: ['px'] },
                        { name: 'الانتشار', property: 'box-shadow-spread', type: 'integer', units: ['px'] },
                        { name: 'اللون', property: 'box-shadow-color', type: 'color' },
                        { 
                            name: 'النوع', 
                            property: 'box-shadow-type', 
                            type: 'select',
                            options: [
                                { value: '', name: 'خارجي' },
                                { value: 'inset', name: 'داخلي' }
                            ]
                        }
                    ]
                },
                {
                    name: 'التحويل',
                    property: 'transform',
                    type: 'composite',
                    properties: [
                        { name: 'تدوير', property: 'transform-rotate', type: 'integer', units: ['deg'] },
                        { name: 'تكبير X', property: 'transform-scale-x', type: 'number', step: 0.1 },
                        { name: 'تكبير Y', property: 'transform-scale-y', type: 'number', step: 0.1 },
                        { name: 'إزاحة X', property: 'transform-translate-x', type: 'integer', units: ['px', '%'] },
                        { name: 'إزاحة Y', property: 'transform-translate-y', type: 'integer', units: ['px', '%'] }
                    ]
                }
            ]
        }
    ]
}

// Arabic font presets
export const arabicFontPresets = [
    {
        name: 'عنوان رئيسي',
        style: {
            'font-family': 'Cairo, Arial, sans-serif',
            'font-size': '32px',
            'font-weight': '700',
            'color': '#1e293b',
            'text-align': 'center',
            'direction': 'rtl'
        }
    },
    {
        name: 'عنوان فرعي',
        style: {
            'font-family': 'Cairo, Arial, sans-serif',
            'font-size': '24px',
            'font-weight': '600',
            'color': '#374151',
            'text-align': 'center',
            'direction': 'rtl'
        }
    },
    {
        name: 'نص عادي',
        style: {
            'font-family': 'Cairo, Arial, sans-serif',
            'font-size': '16px',
            'font-weight': '400',
            'color': '#4b5563',
            'text-align': 'right',
            'direction': 'rtl',
            'line-height': '1.6'
        }
    },
    {
        name: 'نص صغير',
        style: {
            'font-family': 'Cairo, Arial, sans-serif',
            'font-size': '14px',
            'font-weight': '400',
            'color': '#6b7280',
            'text-align': 'right',
            'direction': 'rtl'
        }
    }
]

// Helper function to apply style presets
export const applyStylePreset = (editor, presetName) => {
    const selected = editor.getSelected()
    if (!selected) return

    const preset = arabicFontPresets.find(p => p.name === presetName)
    if (preset) {
        selected.setStyle(preset.style)
    }
}
