/**
 * Template Presets for Different Categories
 * Provides ready-made templates for various occasions
 */

export const templatePresets = {
    // Wedding Templates
    'زواج': {
        'romantic-wedding': {
            name: 'زفاف رومانسي',
            thumbnail: '/images/templates/wedding-romantic.jpg',
            html: `
                <div style="width: 800px; height: 600px; background: linear-gradient(135deg, #fdf2f8 0%, #fce7f3 100%); position: relative; overflow: hidden; font-family: Cairo, Arial, sans-serif;">
                    <!-- Background Pattern -->
                    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0.1; background-image: url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNlYzQ4OTkiIGZpbGwtb3BhY2l0eT0iMC4xIj48cGF0aCBkPSJNMzAgMzBjMC0xNi41NjkgMTMuNDMxLTMwIDMwLTMwdjYwYy0xNi41NjkgMC0zMC0xMy40MzEtMzAtMzB6Ii8+PC9nPjwvZz48L3N2Zz4='); background-size: 60px 60px;"></div>
                    
                    <!-- Main Content -->
                    <div style="position: relative; z-index: 2; height: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; padding: 40px;">
                        
                        <!-- Hearts Decoration -->
                        <div style="font-size: 48px; color: #ec4899; margin-bottom: 20px;">💕</div>
                        
                        <!-- Main Title -->
                        <h1 style="font-size: 48px; color: #be185d; margin-bottom: 30px; font-weight: 700; text-shadow: 2px 2px 4px rgba(190, 24, 93, 0.1);" data-editable="true" data-element-type="heading" data-field-name="العنوان الرئيسي">
                            مبارك الزواج
                        </h1>
                        
                        <!-- Couple Names -->
                        <div style="background: rgba(255, 255, 255, 0.9); padding: 30px; border-radius: 20px; margin-bottom: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                            <p style="font-size: 32px; color: #be185d; margin-bottom: 15px; font-weight: 600;" data-editable="true" data-element-type="name-field" data-field-name="اسم العريس">
                                اسم العريس
                            </p>
                            <div style="font-size: 24px; color: #ec4899; margin: 15px 0;">و</div>
                            <p style="font-size: 32px; color: #be185d; margin-top: 15px; font-weight: 600;" data-editable="true" data-element-type="name-field" data-field-name="اسم العروس">
                                اسم العروس
                            </p>
                        </div>
                        
                        <!-- Wedding Details -->
                        <div style="background: rgba(255, 255, 255, 0.8); padding: 20px; border-radius: 15px; margin-bottom: 20px;">
                            <p style="font-size: 20px; color: #be185d; margin-bottom: 10px;" data-editable="true" data-element-type="date-field" data-field-name="تاريخ الزفاف">
                                التاريخ: __ / __ / ____
                            </p>
                            <p style="font-size: 20px; color: #be185d; margin-bottom: 10px;" data-editable="true" data-element-type="text" data-field-name="مكان الزفاف">
                                المكان: _______________
                            </p>
                            <p style="font-size: 18px; color: #be185d;" data-editable="true" data-element-type="text" data-field-name="وقت الزفاف">
                                الوقت: _______________
                            </p>
                        </div>
                        
                        <!-- Decorative Border -->
                        <div style="width: 300px; height: 2px; background: linear-gradient(90deg, transparent 0%, #ec4899 50%, transparent 100%); margin: 20px 0;"></div>
                        
                        <!-- Bottom Message -->
                        <p style="font-size: 18px; color: #be185d; font-style: italic;" data-editable="true" data-element-type="text" data-field-name="رسالة">
                            نتشرف بحضوركم لمشاركتنا فرحتنا
                        </p>
                    </div>
                    
                    <!-- Watermark -->
                    <div style="position: absolute; bottom: 10px; right: 10px; font-size: 10px; color: rgba(107, 114, 128, 0.6); background: rgba(255, 255, 255, 0.8); padding: 4px 8px; border-radius: 4px;">
                        سامقة © جميع الحقوق محفوظة
                    </div>
                </div>
            `,
            css: `
                @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap');
                * { font-family: 'Cairo', Arial, sans-serif; direction: rtl; }
            `
        },
        
        'elegant-wedding': {
            name: 'زفاف أنيق',
            thumbnail: '/images/templates/wedding-elegant.jpg',
            html: `
                <div style="width: 800px; height: 600px; background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); position: relative; font-family: Cairo, Arial, sans-serif;">
                    <!-- Golden Border -->
                    <div style="position: absolute; top: 20px; left: 20px; right: 20px; bottom: 20px; border: 3px solid #d4af37; border-radius: 15px;"></div>
                    <div style="position: absolute; top: 30px; left: 30px; right: 30px; bottom: 30px; border: 1px solid #d4af37; border-radius: 10px;"></div>
                    
                    <!-- Content -->
                    <div style="position: relative; z-index: 2; height: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; padding: 60px;">
                        
                        <!-- Crown Icon -->
                        <div style="font-size: 40px; color: #d4af37; margin-bottom: 20px;">👑</div>
                        
                        <!-- Main Title -->
                        <h1 style="font-size: 36px; color: #1e293b; margin-bottom: 40px; font-weight: 700; letter-spacing: 2px;" data-editable="true" data-element-type="heading" data-field-name="العنوان">
                            دعوة زفاف
                        </h1>
                        
                        <!-- Names with Elegant Styling -->
                        <div style="margin-bottom: 40px;">
                            <p style="font-size: 28px; color: #d4af37; margin-bottom: 10px; font-weight: 600;" data-editable="true" data-element-type="name-field" data-field-name="اسم العريس">
                                اسم العريس
                            </p>
                            <div style="width: 100px; height: 1px; background: #d4af37; margin: 15px auto;"></div>
                            <p style="font-size: 28px; color: #d4af37; margin-top: 10px; font-weight: 600;" data-editable="true" data-element-type="name-field" data-field-name="اسم العروس">
                                اسم العروس
                            </p>
                        </div>
                        
                        <!-- Event Details -->
                        <div style="background: rgba(255, 255, 255, 0.9); padding: 25px; border-radius: 10px; border: 1px solid #d4af37;">
                            <p style="font-size: 18px; color: #1e293b; margin-bottom: 8px;" data-editable="true" data-element-type="date-field" data-field-name="التاريخ">
                                التاريخ: __ / __ / ____
                            </p>
                            <p style="font-size: 18px; color: #1e293b; margin-bottom: 8px;" data-editable="true" data-element-type="text" data-field-name="المكان">
                                المكان: _______________
                            </p>
                            <p style="font-size: 18px; color: #1e293b;" data-editable="true" data-element-type="text" data-field-name="الوقت">
                                الوقت: _______________
                            </p>
                        </div>
                    </div>
                    
                    <!-- Watermark -->
                    <div style="position: absolute; bottom: 10px; right: 10px; font-size: 10px; color: rgba(107, 114, 128, 0.6); background: rgba(255, 255, 255, 0.8); padding: 4px 8px; border-radius: 4px;">
                        سامقة © جميع الحقوق محفوظة
                    </div>
                </div>
            `,
            css: `
                @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap');
                * { font-family: 'Cairo', Arial, sans-serif; direction: rtl; }
            `
        }
    },

    // Graduation Templates
    'تخرج': {
        'graduation-celebration': {
            name: 'احتفال التخرج',
            thumbnail: '/images/templates/graduation.jpg',
            html: `
                <div style="width: 800px; height: 600px; background: linear-gradient(135deg, #1e3a8a 0%, #3730a3 100%); position: relative; font-family: Cairo, Arial, sans-serif;">
                    <!-- Stars Background -->
                    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-image: radial-gradient(2px 2px at 20px 30px, #fbbf24, transparent), radial-gradient(2px 2px at 40px 70px, #fbbf24, transparent), radial-gradient(1px 1px at 90px 40px, #fbbf24, transparent); background-repeat: repeat; background-size: 100px 100px;"></div>
                    
                    <!-- Main Content -->
                    <div style="position: relative; z-index: 2; height: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; padding: 40px;">
                        
                        <!-- Graduation Cap -->
                        <div style="font-size: 64px; color: #fbbf24; margin-bottom: 20px;">🎓</div>
                        
                        <!-- Main Title -->
                        <h1 style="font-size: 42px; color: #ffffff; margin-bottom: 30px; font-weight: 700; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);" data-editable="true" data-element-type="heading" data-field-name="العنوان">
                            مبروك التخرج
                        </h1>
                        
                        <!-- Graduate Name -->
                        <div style="background: rgba(255, 255, 255, 0.95); padding: 30px; border-radius: 15px; margin-bottom: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
                            <p style="font-size: 36px; color: #1e3a8a; font-weight: 600;" data-editable="true" data-element-type="name-field" data-field-name="اسم الخريج">
                                اسم الخريج
                            </p>
                        </div>
                        
                        <!-- Graduation Details -->
                        <div style="background: rgba(255, 255, 255, 0.9); padding: 25px; border-radius: 12px; margin-bottom: 20px;">
                            <p style="font-size: 20px; color: #1e3a8a; margin-bottom: 10px;" data-editable="true" data-element-type="text" data-field-name="التخصص">
                                التخصص: _______________
                            </p>
                            <p style="font-size: 20px; color: #1e3a8a; margin-bottom: 10px;" data-editable="true" data-element-type="text" data-field-name="الجامعة">
                                الجامعة: _______________
                            </p>
                            <p style="font-size: 18px; color: #1e3a8a;" data-editable="true" data-element-type="date-field" data-field-name="سنة التخرج">
                                سنة التخرج: ____
                            </p>
                        </div>
                        
                        <!-- Congratulations Message -->
                        <p style="font-size: 20px; color: #fbbf24; font-weight: 600; text-shadow: 1px 1px 2px rgba(0,0,0,0.5);" data-editable="true" data-element-type="text" data-field-name="رسالة التهنئة">
                            ألف مبروك وإلى الأمام دائماً
                        </p>
                    </div>
                    
                    <!-- Watermark -->
                    <div style="position: absolute; bottom: 10px; right: 10px; font-size: 10px; color: rgba(255, 255, 255, 0.7); background: rgba(0, 0, 0, 0.3); padding: 4px 8px; border-radius: 4px;">
                        سامقة © جميع الحقوق محفوظة
                    </div>
                </div>
            `,
            css: `
                @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap');
                * { font-family: 'Cairo', Arial, sans-serif; direction: rtl; }
            `
        }
    },

    // Birthday Templates
    'مولود': {
        'baby-celebration': {
            name: 'احتفال المولود',
            thumbnail: '/images/templates/baby.jpg',
            html: `
                <div style="width: 800px; height: 600px; background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); position: relative; font-family: Cairo, Arial, sans-serif;">
                    <!-- Cute Pattern Background -->
                    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0.1; background-image: url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmNTk1MjAiIGZpbGwtb3BhY2l0eT0iMC4xIj48Y2lyY2xlIGN4PSIyMCIgY3k9IjIwIiByPSI0Ii8+PC9nPjwvZz48L3N2Zz4='); background-size: 40px 40px;"></div>
                    
                    <!-- Main Content -->
                    <div style="position: relative; z-index: 2; height: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; padding: 40px;">
                        
                        <!-- Baby Icon -->
                        <div style="font-size: 56px; margin-bottom: 20px;">👶</div>
                        
                        <!-- Main Title -->
                        <h1 style="font-size: 40px; color: #f59520; margin-bottom: 30px; font-weight: 700; text-shadow: 2px 2px 4px rgba(245, 149, 32, 0.1);" data-editable="true" data-element-type="heading" data-field-name="العنوان">
                            مبروك المولود الجديد
                        </h1>
                        
                        <!-- Baby Details -->
                        <div style="background: rgba(255, 255, 255, 0.95); padding: 35px; border-radius: 20px; margin-bottom: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border: 3px solid #fbbf24;">
                            <p style="font-size: 28px; color: #f59520; margin-bottom: 15px; font-weight: 600;" data-editable="true" data-element-type="name-field" data-field-name="اسم المولود">
                                اسم المولود
                            </p>
                            <p style="font-size: 20px; color: #f59520; margin-bottom: 10px;" data-editable="true" data-element-type="date-field" data-field-name="تاريخ الولادة">
                                تاريخ الولادة: __ / __ / ____
                            </p>
                            <p style="font-size: 18px; color: #f59520;" data-editable="true" data-element-type="text" data-field-name="الوزن">
                                الوزن: _____ كيلو
                            </p>
                        </div>
                        
                        <!-- Congratulations Message -->
                        <p style="font-size: 22px; color: #f59520; font-weight: 600; font-style: italic;" data-editable="true" data-element-type="text" data-field-name="رسالة التهنئة">
                            بارك الله لكم في الموهوب وشكرتم الواهب
                        </p>
                    </div>
                    
                    <!-- Watermark -->
                    <div style="position: absolute; bottom: 10px; right: 10px; font-size: 10px; color: rgba(107, 114, 128, 0.6); background: rgba(255, 255, 255, 0.8); padding: 4px 8px; border-radius: 4px;">
                        سامقة © جميع الحقوق محفوظة
                    </div>
                </div>
            `,
            css: `
                @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap');
                * { font-family: 'Cairo', Arial, sans-serif; direction: rtl; }
            `
        }
    }
}

// Helper function to load a template preset
export const loadTemplatePreset = (editor, category, templateId) => {
    const template = templatePresets[category]?.[templateId]
    
    if (!template) {
        console.error(`Template ${templateId} not found in category ${category}`)
        return false
    }

    try {
        // Clear current content
        editor.setComponents('')
        editor.setStyle('')
        
        // Load new template
        editor.setComponents(template.html)
        if (template.css) {
            editor.setStyle(template.css)
        }
        
        return true
    } catch (error) {
        console.error('Error loading template preset:', error)
        return false
    }
}

// Get all available templates for a category
export const getTemplatesForCategory = (category) => {
    return templatePresets[category] || {}
}

// Get all available categories
export const getAvailableCategories = () => {
    return Object.keys(templatePresets)
}
