/**
 * Watermark Service
 * Handles automatic watermark insertion and management for templates
 */

export class WatermarkService {
    constructor() {
        this.defaultWatermark = {
            text: 'سامقة © جميع الحقوق محفوظة',
            position: 'bottom-right',
            fontSize: '10px',
            color: 'rgba(107, 114, 128, 0.6)',
            backgroundColor: 'rgba(255, 255, 255, 0.8)',
            padding: '4px 8px',
            borderRadius: '4px',
            fontFamily: 'Cairo, Arial, sans-serif',
            zIndex: '1000',
            pointerEvents: 'none',
            userSelect: 'none'
        };

        this.positions = {
            'top-left': { top: '10px', left: '10px' },
            'top-right': { top: '10px', right: '10px' },
            'top-center': { top: '10px', left: '50%', transform: 'translateX(-50%)' },
            'center-left': { top: '50%', left: '10px', transform: 'translateY(-50%)' },
            'center': { top: '50%', left: '50%', transform: 'translate(-50%, -50%)' },
            'center-right': { top: '50%', right: '10px', transform: 'translateY(-50%)' },
            'bottom-left': { bottom: '10px', left: '10px' },
            'bottom-center': { bottom: '10px', left: '50%', transform: 'translateX(-50%)' },
            'bottom-right': { bottom: '10px', right: '10px' }
        };
    }

    /**
     * Add watermark to GrapesJS editor
     * @param {Object} editor - GrapesJS editor instance
     * @param {Object} options - Watermark options
     * @returns {Object} Watermark component
     */
    addWatermarkToEditor(editor, options = {}) {
        try {
            // Remove existing watermark first
            this.removeWatermarkFromEditor(editor);

            const watermarkOptions = { ...this.defaultWatermark, ...options };
            const position = this.positions[watermarkOptions.position] || this.positions['bottom-right'];

            // Create watermark component
            const watermarkComponent = {
                type: 'text',
                content: watermarkOptions.text,
                style: {
                    position: 'absolute',
                    fontSize: watermarkOptions.fontSize,
                    color: watermarkOptions.color,
                    backgroundColor: watermarkOptions.backgroundColor,
                    padding: watermarkOptions.padding,
                    borderRadius: watermarkOptions.borderRadius,
                    fontFamily: watermarkOptions.fontFamily,
                    zIndex: watermarkOptions.zIndex,
                    pointerEvents: watermarkOptions.pointerEvents,
                    userSelect: watermarkOptions.userSelect,
                    direction: 'ltr', // Force LTR for copyright text
                    ...position
                },
                attributes: {
                    'data-element-type': 'watermark',
                    'data-locked': 'true',
                    'data-watermark': 'true',
                    'class': 'watermark-element'
                }
            };

            // Add to editor
            const component = editor.addComponents(watermarkComponent)[0];
            
            // Make watermark non-selectable and non-removable
            component.set('selectable', false);
            component.set('removable', false);
            component.set('draggable', false);
            component.set('copyable', false);
            component.set('badgable', false);

            return component;
        } catch (error) {
            console.error('Error adding watermark to editor:', error);
            throw new Error('فشل في إضافة العلامة المائية');
        }
    }

    /**
     * Remove watermark from GrapesJS editor
     * @param {Object} editor - GrapesJS editor instance
     */
    removeWatermarkFromEditor(editor) {
        try {
            const components = editor.getComponents();
            const watermarks = [];

            // Find all watermark components
            const findWatermarks = (component) => {
                const attributes = component.get('attributes') || {};
                if (attributes['data-watermark'] === 'true' || attributes['data-element-type'] === 'watermark') {
                    watermarks.push(component);
                }

                // Check children recursively
                const children = component.components();
                if (children && children.length > 0) {
                    children.forEach(child => findWatermarks(child));
                }
            };

            components.forEach(component => findWatermarks(component));

            // Remove all found watermarks
            watermarks.forEach(watermark => {
                watermark.remove();
            });

        } catch (error) {
            console.error('Error removing watermark from editor:', error);
        }
    }

    /**
     * Check if editor has watermark
     * @param {Object} editor - GrapesJS editor instance
     * @returns {boolean}
     */
    hasWatermark(editor) {
        try {
            const html = editor.getHtml();
            return html.includes('data-watermark="true"') || 
                   html.includes('data-element-type="watermark"') ||
                   html.includes(this.defaultWatermark.text);
        } catch (error) {
            console.error('Error checking watermark:', error);
            return false;
        }
    }

    /**
     * Add watermark to HTML content
     * @param {string} html - HTML content
     * @param {Object} options - Watermark options
     * @returns {string} HTML with watermark
     */
    addWatermarkToHTML(html, options = {}) {
        try {
            const watermarkOptions = { ...this.defaultWatermark, ...options };
            const position = this.positions[watermarkOptions.position] || this.positions['bottom-right'];

            // Check if watermark already exists
            if (html.includes('data-watermark="true"') || html.includes(watermarkOptions.text)) {
                return html;
            }

            // Create watermark HTML
            const watermarkHTML = `
                <div 
                    data-watermark="true" 
                    data-element-type="watermark"
                    class="watermark-element"
                    style="
                        position: absolute;
                        font-size: ${watermarkOptions.fontSize};
                        color: ${watermarkOptions.color};
                        background-color: ${watermarkOptions.backgroundColor};
                        padding: ${watermarkOptions.padding};
                        border-radius: ${watermarkOptions.borderRadius};
                        font-family: ${watermarkOptions.fontFamily};
                        z-index: ${watermarkOptions.zIndex};
                        pointer-events: ${watermarkOptions.pointerEvents};
                        user-select: ${watermarkOptions.userSelect};
                        direction: ltr;
                        ${Object.entries(position).map(([key, value]) => `${key}: ${value}`).join('; ')};
                    "
                >
                    ${watermarkOptions.text}
                </div>
            `;

            // Find the main container or body
            const containerRegex = /<div[^>]*class="[^"]*container[^"]*"[^>]*>/i;
            const bodyRegex = /<body[^>]*>/i;

            if (containerRegex.test(html)) {
                // Add after the main container opening tag
                return html.replace(containerRegex, (match) => match + watermarkHTML);
            } else if (bodyRegex.test(html)) {
                // Add after body opening tag
                return html.replace(bodyRegex, (match) => match + watermarkHTML);
            } else {
                // Add at the beginning of the content
                return watermarkHTML + html;
            }
        } catch (error) {
            console.error('Error adding watermark to HTML:', error);
            return html;
        }
    }

    /**
     * Remove watermark from HTML content
     * @param {string} html - HTML content
     * @returns {string} HTML without watermark
     */
    removeWatermarkFromHTML(html) {
        try {
            // Remove watermark elements
            const watermarkRegex = /<div[^>]*data-watermark="true"[^>]*>.*?<\/div>/gis;
            const elementTypeRegex = /<div[^>]*data-element-type="watermark"[^>]*>.*?<\/div>/gis;
            const classRegex = /<div[^>]*class="[^"]*watermark-element[^"]*"[^>]*>.*?<\/div>/gis;

            let cleanHTML = html;
            cleanHTML = cleanHTML.replace(watermarkRegex, '');
            cleanHTML = cleanHTML.replace(elementTypeRegex, '');
            cleanHTML = cleanHTML.replace(classRegex, '');

            return cleanHTML;
        } catch (error) {
            console.error('Error removing watermark from HTML:', error);
            return html;
        }
    }

    /**
     * Add watermark to CSS for print/export protection
     * @param {string} css - CSS content
     * @param {Object} options - Watermark options
     * @returns {string} CSS with watermark styles
     */
    addWatermarkToCSS(css, options = {}) {
        try {
            const watermarkOptions = { ...this.defaultWatermark, ...options };
            
            const watermarkCSS = `
                /* Watermark Protection */
                .watermark-element {
                    position: absolute !important;
                    font-size: ${watermarkOptions.fontSize} !important;
                    color: ${watermarkOptions.color} !important;
                    background-color: ${watermarkOptions.backgroundColor} !important;
                    padding: ${watermarkOptions.padding} !important;
                    border-radius: ${watermarkOptions.borderRadius} !important;
                    font-family: ${watermarkOptions.fontFamily} !important;
                    z-index: ${watermarkOptions.zIndex} !important;
                    pointer-events: ${watermarkOptions.pointerEvents} !important;
                    user-select: ${watermarkOptions.userSelect} !important;
                    direction: ltr !important;
                    -webkit-user-select: none !important;
                    -moz-user-select: none !important;
                    -ms-user-select: none !important;
                }

                /* Print protection */
                @media print {
                    .watermark-element {
                        display: block !important;
                        visibility: visible !important;
                        opacity: 0.8 !important;
                    }
                }

                /* Additional protection against hiding */
                .watermark-element[style*="display: none"],
                .watermark-element[style*="visibility: hidden"],
                .watermark-element[style*="opacity: 0"] {
                    display: block !important;
                    visibility: visible !important;
                    opacity: 0.6 !important;
                }
            `;

            return css + '\n' + watermarkCSS;
        } catch (error) {
            console.error('Error adding watermark to CSS:', error);
            return css;
        }
    }

    /**
     * Create watermark for canvas/image export
     * @param {HTMLCanvasElement} canvas - Canvas element
     * @param {Object} options - Watermark options
     */
    addWatermarkToCanvas(canvas, options = {}) {
        try {
            const ctx = canvas.getContext('2d');
            const watermarkOptions = { ...this.defaultWatermark, ...options };
            
            // Set font
            ctx.font = `${parseInt(watermarkOptions.fontSize)}px ${watermarkOptions.fontFamily}`;
            ctx.textAlign = 'right';
            ctx.textBaseline = 'bottom';

            // Measure text
            const textMetrics = ctx.measureText(watermarkOptions.text);
            const textWidth = textMetrics.width;
            const textHeight = parseInt(watermarkOptions.fontSize);
            const padding = parseInt(watermarkOptions.padding);

            // Calculate position
            const position = this.positions[watermarkOptions.position] || this.positions['bottom-right'];
            let x, y;

            if (watermarkOptions.position.includes('right')) {
                x = canvas.width - padding;
            } else if (watermarkOptions.position.includes('left')) {
                x = padding + textWidth;
            } else {
                x = canvas.width / 2;
                ctx.textAlign = 'center';
            }

            if (watermarkOptions.position.includes('bottom')) {
                y = canvas.height - padding;
            } else if (watermarkOptions.position.includes('top')) {
                y = padding + textHeight;
            } else {
                y = canvas.height / 2;
                ctx.textBaseline = 'middle';
            }

            // Draw background
            if (watermarkOptions.backgroundColor && watermarkOptions.backgroundColor !== 'transparent') {
                ctx.fillStyle = watermarkOptions.backgroundColor;
                const bgX = watermarkOptions.position.includes('right') ? x - textWidth - padding : x - padding;
                const bgY = y - textHeight - padding / 2;
                ctx.fillRect(bgX, bgY, textWidth + padding * 2, textHeight + padding);
            }

            // Draw text
            ctx.fillStyle = watermarkOptions.color;
            ctx.fillText(watermarkOptions.text, x, y);

        } catch (error) {
            console.error('Error adding watermark to canvas:', error);
        }
    }

    /**
     * Validate watermark integrity
     * @param {string} content - Content to validate (HTML or CSS)
     * @returns {Object} Validation result
     */
    validateWatermarkIntegrity(content) {
        try {
            const hasWatermarkElement = content.includes('data-watermark="true"') || 
                                      content.includes('data-element-type="watermark"');
            const hasWatermarkText = content.includes(this.defaultWatermark.text);
            const hasWatermarkCSS = content.includes('.watermark-element');

            return {
                isValid: hasWatermarkElement || hasWatermarkText,
                hasElement: hasWatermarkElement,
                hasText: hasWatermarkText,
                hasCSS: hasWatermarkCSS,
                warnings: []
            };
        } catch (error) {
            console.error('Error validating watermark integrity:', error);
            return {
                isValid: false,
                hasElement: false,
                hasText: false,
                hasCSS: false,
                warnings: ['خطأ في التحقق من سلامة العلامة المائية']
            };
        }
    }

    /**
     * Get watermark configuration for different contexts
     * @param {string} context - Context (editor, export, print, etc.)
     * @returns {Object} Watermark configuration
     */
    getWatermarkConfig(context = 'default') {
        const configs = {
            editor: {
                ...this.defaultWatermark,
                color: 'rgba(107, 114, 128, 0.4)',
                fontSize: '10px'
            },
            export: {
                ...this.defaultWatermark,
                color: 'rgba(107, 114, 128, 0.8)',
                fontSize: '12px'
            },
            print: {
                ...this.defaultWatermark,
                color: 'rgba(0, 0, 0, 0.6)',
                backgroundColor: 'rgba(255, 255, 255, 0.9)',
                fontSize: '10px'
            },
            thumbnail: {
                ...this.defaultWatermark,
                color: 'rgba(107, 114, 128, 0.5)',
                fontSize: '8px',
                padding: '2px 4px'
            }
        };

        return configs[context] || this.defaultWatermark;
    }
}

// Export singleton instance
export const watermarkService = new WatermarkService();
