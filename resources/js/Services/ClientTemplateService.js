/**
 * Client Template Service
 * Handles template customization for clients with restricted editing capabilities
 */

export class ClientTemplateService {
    constructor() {
        this.allowedEditTypes = ['text', 'name-field', 'date-field', 'occasion-field'];
        this.restrictedElements = ['watermark', 'logo', 'decorative-border'];
    }

    /**
     * Prepare template for client editing
     * @param {Object} templateData - Template design data
     * @returns {Object} Client-ready template data
     */
    prepareForClientEditing(templateData) {
        try {
            if (!templateData || !templateData.editableElements) {
                throw new Error('بيانات القالب غير صحيحة');
            }

            // Filter editable elements for client
            const clientEditableElements = this.filterClientEditableElements(templateData.editableElements);
            
            // Create client-safe design data
            const clientTemplateData = {
                ...templateData,
                editableElements: clientEditableElements,
                clientMode: true,
                restrictions: this.getClientRestrictions(),
                allowedActions: this.getAllowedClientActions()
            };

            return clientTemplateData;
        } catch (error) {
            console.error('Error preparing template for client:', error);
            throw new Error('فشل في تحضير القالب للعميل');
        }
    }

    /**
     * Filter editable elements that clients can modify
     * @param {Array} editableElements - All editable elements
     * @returns {Array} Client-editable elements
     */
    filterClientEditableElements(editableElements) {
        return editableElements.filter(element => {
            // Allow only specific element types
            if (!this.allowedEditTypes.includes(element.type)) {
                return false;
            }

            // Exclude locked elements
            if (element.isLocked) {
                return false;
            }

            // Exclude restricted elements
            if (this.restrictedElements.includes(element.type)) {
                return false;
            }

            return true;
        }).map(element => ({
            ...element,
            // Add client-specific constraints
            clientConstraints: {
                canChangeFont: element.type === 'text',
                canChangeColor: true,
                canChangeSize: element.type === 'text',
                canMove: false, // Clients cannot move elements
                canResize: false, // Clients cannot resize elements
                canDelete: false // Clients cannot delete elements
            }
        }));
    }

    /**
     * Get client restrictions
     * @returns {Object} Client restrictions
     */
    getClientRestrictions() {
        return {
            canAddElements: false,
            canDeleteElements: false,
            canMoveElements: false,
            canResizeElements: false,
            canChangeLayout: false,
            canModifyWatermark: false,
            canExportWithoutWatermark: false,
            maxTextLength: 100,
            allowedFonts: [
                'Cairo, Arial, sans-serif',
                'Amiri, serif',
                'Noto Sans Arabic, sans-serif'
            ],
            allowedColors: [
                '#000000', '#333333', '#666666', '#999999',
                '#1e40af', '#3b82f6', '#60a5fa',
                '#dc2626', '#ef4444', '#f87171',
                '#059669', '#10b981', '#34d399',
                '#d97706', '#f59e0b', '#fbbf24'
            ]
        };
    }

    /**
     * Get allowed client actions
     * @returns {Array} Allowed actions
     */
    getAllowedClientActions() {
        return [
            'edit-text',
            'change-font-size',
            'change-text-color',
            'change-font-family',
            'preview',
            'save-draft',
            'export-with-watermark'
        ];
    }

    /**
     * Validate client edit request
     * @param {Object} editRequest - Client edit request
     * @param {Object} templateData - Template data
     * @returns {Object} Validation result
     */
    validateClientEdit(editRequest, templateData) {
        const errors = [];
        const warnings = [];

        if (!editRequest.elementId || !editRequest.newValue) {
            errors.push('بيانات التحرير غير مكتملة');
            return { isValid: false, errors, warnings };
        }

        // Find the element
        const element = templateData.editableElements.find(el => el.id === editRequest.elementId);
        if (!element) {
            errors.push('العنصر المطلوب تحريره غير موجود');
            return { isValid: false, errors, warnings };
        }

        // Check if element is client-editable
        if (!this.allowedEditTypes.includes(element.type)) {
            errors.push('هذا العنصر غير قابل للتحرير');
            return { isValid: false, errors, warnings };
        }

        // Check if element is locked
        if (element.isLocked) {
            errors.push('هذا العنصر مقفل ولا يمكن تحريره');
            return { isValid: false, errors, warnings };
        }

        // Validate content constraints
        const contentValidation = this.validateContent(editRequest.newValue, element);
        if (!contentValidation.isValid) {
            errors.push(...contentValidation.errors);
        }

        // Validate style changes if any
        if (editRequest.styleChanges) {
            const styleValidation = this.validateStyleChanges(editRequest.styleChanges, element);
            if (!styleValidation.isValid) {
                errors.push(...styleValidation.errors);
            }
            warnings.push(...styleValidation.warnings);
        }

        return {
            isValid: errors.length === 0,
            errors,
            warnings
        };
    }

    /**
     * Validate content against element constraints
     * @param {string} content - New content
     * @param {Object} element - Element data
     * @returns {Object} Validation result
     */
    validateContent(content, element) {
        const errors = [];

        // Check required constraint
        if (element.constraints.required && (!content || content.trim() === '')) {
            errors.push(`${element.fieldName} مطلوب`);
        }

        // Check length constraints
        if (element.constraints.minLength && content.length < element.constraints.minLength) {
            errors.push(`${element.fieldName} يجب أن يكون ${element.constraints.minLength} أحرف على الأقل`);
        }

        if (element.constraints.maxLength && content.length > element.constraints.maxLength) {
            errors.push(`${element.fieldName} يجب أن يكون ${element.constraints.maxLength} أحرف كحد أقصى`);
        }

        // Check pattern constraint
        if (element.constraints.pattern && !new RegExp(element.constraints.pattern).test(content)) {
            errors.push(`${element.fieldName} لا يتطابق مع الصيغة المطلوبة`);
        }

        // General restrictions
        const restrictions = this.getClientRestrictions();
        if (content.length > restrictions.maxTextLength) {
            errors.push(`النص طويل جداً (الحد الأقصى ${restrictions.maxTextLength} حرف)`);
        }

        return {
            isValid: errors.length === 0,
            errors
        };
    }

    /**
     * Validate style changes
     * @param {Object} styleChanges - Style changes
     * @param {Object} element - Element data
     * @returns {Object} Validation result
     */
    validateStyleChanges(styleChanges, element) {
        const errors = [];
        const warnings = [];
        const restrictions = this.getClientRestrictions();

        // Check font family
        if (styleChanges['font-family'] && !restrictions.allowedFonts.includes(styleChanges['font-family'])) {
            errors.push('نوع الخط غير مسموح');
        }

        // Check color
        if (styleChanges.color && !restrictions.allowedColors.includes(styleChanges.color)) {
            warnings.push('اللون المختار قد لا يكون مناسباً');
        }

        // Check font size limits
        if (styleChanges['font-size']) {
            const fontSize = parseInt(styleChanges['font-size']);
            if (fontSize < 10 || fontSize > 72) {
                errors.push('حجم الخط يجب أن يكون بين 10 و 72');
            }
        }

        // Prevent position/layout changes
        const restrictedProperties = ['position', 'top', 'left', 'right', 'bottom', 'width', 'height', 'transform'];
        const hasRestrictedChanges = restrictedProperties.some(prop => styleChanges.hasOwnProperty(prop));
        if (hasRestrictedChanges) {
            errors.push('لا يمكن تغيير موضع أو حجم العناصر');
        }

        return {
            isValid: errors.length === 0,
            errors,
            warnings
        };
    }

    /**
     * Apply client edit to template
     * @param {Object} editRequest - Client edit request
     * @param {Object} templateData - Template data
     * @returns {Object} Updated template data
     */
    applyClientEdit(editRequest, templateData) {
        try {
            // Validate edit first
            const validation = this.validateClientEdit(editRequest, templateData);
            if (!validation.isValid) {
                throw new Error(validation.errors.join(', '));
            }

            // Clone template data
            const updatedTemplateData = JSON.parse(JSON.stringify(templateData));

            // Find and update the element
            const elementIndex = updatedTemplateData.editableElements.findIndex(el => el.id === editRequest.elementId);
            if (elementIndex !== -1) {
                // Update content
                updatedTemplateData.editableElements[elementIndex].content = editRequest.newValue;

                // Update styles if provided
                if (editRequest.styleChanges) {
                    updatedTemplateData.editableElements[elementIndex].styles = {
                        ...updatedTemplateData.editableElements[elementIndex].styles,
                        ...editRequest.styleChanges
                    };
                }

                // Update timestamp
                updatedTemplateData.lastModified = new Date().toISOString();
                updatedTemplateData.modifiedBy = 'client';
            }

            return updatedTemplateData;
        } catch (error) {
            console.error('Error applying client edit:', error);
            throw new Error('فشل في تطبيق التعديل');
        }
    }

    /**
     * Generate client preview
     * @param {Object} templateData - Template data
     * @returns {Object} Preview data
     */
    generateClientPreview(templateData) {
        try {
            // Create preview with watermark
            const previewData = {
                html: templateData.design.html,
                css: templateData.design.css,
                hasWatermark: true,
                watermarkText: 'سامقة © جميع الحقوق محفوظة',
                previewMode: true,
                timestamp: new Date().toISOString()
            };

            return previewData;
        } catch (error) {
            console.error('Error generating client preview:', error);
            throw new Error('فشل في إنشاء المعاينة');
        }
    }

    /**
     * Prepare template for client export
     * @param {Object} templateData - Template data
     * @param {Object} exportOptions - Export options
     * @returns {Object} Export data
     */
    prepareClientExport(templateData, exportOptions = {}) {
        try {
            const exportData = {
                html: templateData.design.html,
                css: templateData.design.css,
                format: exportOptions.format || 'png',
                quality: exportOptions.quality || 0.8,
                width: templateData.metadata.canvasWidth || 800,
                height: templateData.metadata.canvasHeight || 600,
                hasWatermark: true, // Always include watermark for client exports
                watermarkPosition: 'bottom-right',
                exportedAt: new Date().toISOString(),
                exportedBy: 'client'
            };

            return exportData;
        } catch (error) {
            console.error('Error preparing client export:', error);
            throw new Error('فشل في تحضير التصدير');
        }
    }
}

// Export singleton instance
export const clientTemplateService = new ClientTemplateService();
