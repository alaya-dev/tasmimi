/**
 * Design Data Service
 * Handles saving, loading, and processing of template design data
 */

export class DesignDataService {
    constructor() {
        this.version = '1.0';
        this.watermarkText = 'سامقة © جميع الحقوق محفوظة';
    }

    /**
     * Process and structure design data for saving
     * @param {Object} editor - GrapesJS editor instance
     * @param {Object} metadata - Additional metadata
     * @returns {Object} Structured design data
     */
    processDesignData(editor, metadata = {}) {
        try {
            const html = editor.getHtml();
            const css = editor.getCss();
            const components = editor.getComponents();
            const styles = editor.getStyle();
            const projectData = editor.getProjectData();

            // Extract editable elements
            const editableElements = this.extractEditableElements(components);
            
            // Extract assets (images, fonts, etc.)
            const assets = this.extractAssets(editor);
            
            // Generate design structure
            const designStructure = this.generateDesignStructure(components);

            return {
                version: this.version,
                timestamp: new Date().toISOString(),
                metadata: {
                    canvasWidth: metadata.canvasWidth || 800,
                    canvasHeight: metadata.canvasHeight || 600,
                    category: metadata.category || null,
                    templateName: metadata.templateName || null,
                    createdBy: metadata.createdBy || null,
                    ...metadata
                },
                design: {
                    html: html,
                    css: css,
                    projectData: projectData,
                    structure: designStructure
                },
                editableElements: editableElements,
                assets: assets,
                settings: {
                    hasWatermark: this.hasWatermark(html),
                    isRTL: this.isRTLDesign(css),
                    fontFamilies: this.extractFontFamilies(css),
                    colorPalette: this.extractColorPalette(css)
                }
            };
        } catch (error) {
            console.error('Error processing design data:', error);
            throw new Error('فشل في معالجة بيانات التصميم');
        }
    }

    /**
     * Extract editable elements from components
     * @param {Object} components - GrapesJS components
     * @returns {Array} Array of editable elements
     */
    extractEditableElements(components) {
        const editableElements = [];
        
        const processComponent = (component) => {
            const attributes = component.get('attributes') || {};
            
            if (attributes['data-editable'] === 'true') {
                const element = {
                    id: component.getId(),
                    type: attributes['data-element-type'] || 'text',
                    fieldName: attributes['data-field-name'] || 'حقل',
                    content: component.get('content') || '',
                    styles: component.getStyle(),
                    position: this.getElementPosition(component),
                    constraints: {
                        minLength: attributes['data-min-length'] ? parseInt(attributes['data-min-length']) : null,
                        maxLength: attributes['data-max-length'] ? parseInt(attributes['data-max-length']) : null,
                        required: attributes['data-required'] === 'true',
                        pattern: attributes['data-pattern'] || null
                    },
                    defaultValue: attributes['data-default-value'] || '',
                    placeholder: attributes['data-placeholder'] || '',
                    isLocked: attributes['data-locked'] === 'true'
                };
                
                editableElements.push(element);
            }
            
            // Process child components recursively
            const children = component.components();
            if (children && children.length > 0) {
                children.forEach(child => processComponent(child));
            }
        };
        
        components.forEach(component => processComponent(component));
        
        return editableElements;
    }

    /**
     * Get element position information
     * @param {Object} component - GrapesJS component
     * @returns {Object} Position data
     */
    getElementPosition(component) {
        const el = component.getEl();
        if (!el) return { x: 0, y: 0, width: 0, height: 0 };
        
        const rect = el.getBoundingClientRect();
        const parentRect = el.parentElement?.getBoundingClientRect() || { left: 0, top: 0 };
        
        return {
            x: rect.left - parentRect.left,
            y: rect.top - parentRect.top,
            width: rect.width,
            height: rect.height,
            zIndex: component.getStyle('z-index') || 'auto'
        };
    }

    /**
     * Extract assets from editor
     * @param {Object} editor - GrapesJS editor instance
     * @returns {Object} Assets data
     */
    extractAssets(editor) {
        const assetManager = editor.AssetManager;
        const assets = assetManager.getAll();
        
        return {
            images: assets.filter(asset => asset.get('type') === 'image').map(asset => ({
                id: asset.get('id'),
                src: asset.get('src'),
                name: asset.get('name') || '',
                type: asset.get('type'),
                size: asset.get('size') || null
            })),
            fonts: this.extractFontFamilies(editor.getCss()),
            colors: this.extractColorPalette(editor.getCss())
        };
    }

    /**
     * Generate design structure for easier manipulation
     * @param {Object} components - GrapesJS components
     * @returns {Object} Design structure
     */
    generateDesignStructure(components) {
        const structure = {
            layers: [],
            groups: {},
            hierarchy: []
        };
        
        const processComponent = (component, level = 0) => {
            const layer = {
                id: component.getId(),
                type: component.get('type'),
                tagName: component.get('tagName'),
                level: level,
                attributes: component.get('attributes') || {},
                styles: component.getStyle(),
                content: component.get('content') || '',
                children: []
            };
            
            // Process children
            const children = component.components();
            if (children && children.length > 0) {
                children.forEach(child => {
                    const childLayer = processComponent(child, level + 1);
                    layer.children.push(childLayer.id);
                    structure.layers.push(childLayer);
                });
            }
            
            structure.layers.push(layer);
            return layer;
        };
        
        components.forEach(component => {
            const rootLayer = processComponent(component);
            structure.hierarchy.push(rootLayer.id);
        });
        
        return structure;
    }

    /**
     * Check if design has watermark
     * @param {string} html - HTML content
     * @returns {boolean}
     */
    hasWatermark(html) {
        return html.includes(this.watermarkText) || html.includes('data-element-type="watermark"');
    }

    /**
     * Check if design is RTL
     * @param {string} css - CSS content
     * @returns {boolean}
     */
    isRTLDesign(css) {
        return css.includes('direction: rtl') || css.includes('direction:rtl');
    }

    /**
     * Extract font families from CSS
     * @param {string} css - CSS content
     * @returns {Array} Array of font families
     */
    extractFontFamilies(css) {
        const fontRegex = /font-family:\s*([^;]+)/gi;
        const fonts = new Set();
        let match;
        
        while ((match = fontRegex.exec(css)) !== null) {
            const fontFamily = match[1].trim().replace(/['"]/g, '');
            fonts.add(fontFamily);
        }
        
        return Array.from(fonts);
    }

    /**
     * Extract color palette from CSS
     * @param {string} css - CSS content
     * @returns {Array} Array of colors
     */
    extractColorPalette(css) {
        const colorRegex = /(#[0-9a-fA-F]{3,6}|rgb\([^)]+\)|rgba\([^)]+\)|hsl\([^)]+\)|hsla\([^)]+\))/gi;
        const colors = new Set();
        let match;
        
        while ((match = colorRegex.exec(css)) !== null) {
            colors.add(match[1]);
        }
        
        return Array.from(colors);
    }

    /**
     * Load design data into editor
     * @param {Object} editor - GrapesJS editor instance
     * @param {Object} designData - Design data to load
     * @returns {boolean} Success status
     */
    loadDesignData(editor, designData) {
        try {
            if (!designData || typeof designData !== 'object') {
                throw new Error('بيانات التصميم غير صحيحة');
            }

            // Validate version compatibility
            if (designData.version && !this.isVersionCompatible(designData.version)) {
                console.warn('Design data version mismatch:', designData.version);
            }

            // Clear current content
            editor.setComponents('');
            editor.setStyle('');

            // Load project data if available
            if (designData.design && designData.design.projectData) {
                editor.loadProjectData(designData.design.projectData);
            } else if (designData.design) {
                // Fallback to HTML/CSS loading
                if (designData.design.html) {
                    editor.setComponents(designData.design.html);
                }
                if (designData.design.css) {
                    editor.setStyle(designData.design.css);
                }
            }

            // Restore editable elements properties
            if (designData.editableElements) {
                this.restoreEditableElements(editor, designData.editableElements);
            }

            // Load assets
            if (designData.assets) {
                this.loadAssets(editor, designData.assets);
            }

            return true;
        } catch (error) {
            console.error('Error loading design data:', error);
            throw new Error('فشل في تحميل بيانات التصميم');
        }
    }

    /**
     * Check version compatibility
     * @param {string} version - Version to check
     * @returns {boolean} Compatibility status
     */
    isVersionCompatible(version) {
        const currentMajor = parseInt(this.version.split('.')[0]);
        const dataMajor = parseInt(version.split('.')[0]);
        return currentMajor === dataMajor;
    }

    /**
     * Restore editable elements properties
     * @param {Object} editor - GrapesJS editor instance
     * @param {Array} editableElements - Editable elements data
     */
    restoreEditableElements(editor, editableElements) {
        editableElements.forEach(elementData => {
            const component = editor.getComponents().find(comp => comp.getId() === elementData.id);
            if (component) {
                // Restore attributes
                component.setAttributes({
                    ...component.get('attributes'),
                    'data-editable': 'true',
                    'data-element-type': elementData.type,
                    'data-field-name': elementData.fieldName,
                    'data-required': elementData.constraints.required ? 'true' : 'false',
                    'data-locked': elementData.isLocked ? 'true' : 'false'
                });

                // Restore styles if needed
                if (elementData.styles) {
                    component.setStyle(elementData.styles);
                }
            }
        });
    }

    /**
     * Load assets into editor
     * @param {Object} editor - GrapesJS editor instance
     * @param {Object} assets - Assets data
     */
    loadAssets(editor, assets) {
        const assetManager = editor.AssetManager;
        
        if (assets.images) {
            assets.images.forEach(image => {
                assetManager.add(image);
            });
        }
    }

    /**
     * Validate design data structure
     * @param {Object} designData - Design data to validate
     * @returns {Object} Validation result
     */
    validateDesignData(designData) {
        const errors = [];
        const warnings = [];

        if (!designData) {
            errors.push('بيانات التصميم مفقودة');
            return { isValid: false, errors, warnings };
        }

        if (!designData.design) {
            errors.push('بيانات التصميم الأساسية مفقودة');
        }

        if (!designData.version) {
            warnings.push('رقم الإصدار مفقود');
        } else if (!this.isVersionCompatible(designData.version)) {
            warnings.push(`إصدار غير متوافق: ${designData.version}`);
        }

        if (designData.editableElements && !Array.isArray(designData.editableElements)) {
            errors.push('بيانات العناصر القابلة للتحرير غير صحيحة');
        }

        return {
            isValid: errors.length === 0,
            errors,
            warnings
        };
    }
}

// Export singleton instance
export const designDataService = new DesignDataService();
