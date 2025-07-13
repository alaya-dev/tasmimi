/**
 * Thumbnail Service
 * Handles automatic thumbnail generation for templates
 */

export class ThumbnailService {
    constructor() {
        this.defaultOptions = {
            width: 400,
            height: 300,
            quality: 0.8,
            format: 'png',
            scale: 0.5,
            backgroundColor: '#ffffff'
        };
    }

    /**
     * Generate thumbnail from GrapesJS editor
     * @param {Object} editor - GrapesJS editor instance
     * @param {Object} options - Generation options
     * @returns {Promise<string>} Base64 image data
     */
    async generateFromEditor(editor, options = {}) {
        try {
            const config = { ...this.defaultOptions, ...options };
            
            // Get the canvas element
            const canvasElement = editor.Canvas.getElement();
            if (!canvasElement) {
                throw new Error('Canvas element not found');
            }

            // Import html-to-image library
            const { toPng, toJpeg } = await import('html-to-image');
            
            // Choose the appropriate function based on format
            const captureFunction = config.format === 'jpeg' ? toJpeg : toPng;
            
            // Generate thumbnail
            const dataUrl = await captureFunction(canvasElement, {
                quality: config.quality,
                width: config.width,
                height: config.height,
                backgroundColor: config.backgroundColor,
                style: {
                    transform: `scale(${config.scale})`,
                    transformOrigin: 'top left',
                    width: `${config.width / config.scale}px`,
                    height: `${config.height / config.scale}px`
                },
                filter: (node) => {
                    // Filter out unwanted elements
                    if (node.classList) {
                        // Remove GrapesJS UI elements
                        if (node.classList.contains('gjs-toolbar') ||
                            node.classList.contains('gjs-resizer') ||
                            node.classList.contains('gjs-badge') ||
                            node.classList.contains('gjs-highlighter')) {
                            return false;
                        }
                    }
                    return true;
                }
            });

            return dataUrl;
        } catch (error) {
            console.error('Error generating thumbnail from editor:', error);
            throw new Error('فشل في إنشاء المعاينة من المحرر');
        }
    }

    /**
     * Generate thumbnail from HTML/CSS
     * @param {string} html - HTML content
     * @param {string} css - CSS content
     * @param {Object} options - Generation options
     * @returns {Promise<string>} Base64 image data
     */
    async generateFromHTML(html, css, options = {}) {
        try {
            const config = { ...this.defaultOptions, ...options };
            
            // Create a temporary container
            const container = document.createElement('div');
            container.style.position = 'absolute';
            container.style.top = '-9999px';
            container.style.left = '-9999px';
            container.style.width = `${config.width / config.scale}px`;
            container.style.height = `${config.height / config.scale}px`;
            container.style.overflow = 'hidden';
            container.style.backgroundColor = config.backgroundColor;
            
            // Add CSS
            const styleElement = document.createElement('style');
            styleElement.textContent = css + `
                * { 
                    font-family: 'Cairo', Arial, sans-serif !important;
                    direction: rtl !important;
                }
                body { 
                    margin: 0; 
                    padding: 0; 
                    background: ${config.backgroundColor};
                }
            `;
            container.appendChild(styleElement);
            
            // Add HTML content
            const contentDiv = document.createElement('div');
            contentDiv.innerHTML = html;
            container.appendChild(contentDiv);
            
            // Add to document
            document.body.appendChild(container);
            
            // Wait for fonts and images to load
            await this.waitForContent(container);
            
            // Import html-to-image library
            const { toPng, toJpeg } = await import('html-to-image');
            
            // Choose the appropriate function based on format
            const captureFunction = config.format === 'jpeg' ? toJpeg : toPng;
            
            // Generate thumbnail
            const dataUrl = await captureFunction(container, {
                quality: config.quality,
                width: config.width,
                height: config.height,
                backgroundColor: config.backgroundColor,
                style: {
                    transform: `scale(${config.scale})`,
                    transformOrigin: 'top left'
                }
            });
            
            // Clean up
            document.body.removeChild(container);
            
            return dataUrl;
        } catch (error) {
            console.error('Error generating thumbnail from HTML:', error);
            throw new Error('فشل في إنشاء المعاينة من HTML');
        }
    }

    /**
     * Generate thumbnail with watermark
     * @param {string} baseImage - Base image data URL
     * @param {Object} watermarkOptions - Watermark options
     * @returns {Promise<string>} Base64 image data with watermark
     */
    async addWatermark(baseImage, watermarkOptions = {}) {
        try {
            const options = {
                text: 'سامقة © جميع الحقوق محفوظة',
                position: 'bottom-right',
                fontSize: 12,
                color: 'rgba(107, 114, 128, 0.6)',
                backgroundColor: 'rgba(255, 255, 255, 0.8)',
                padding: 8,
                ...watermarkOptions
            };

            return new Promise((resolve, reject) => {
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                const img = new Image();

                img.onload = () => {
                    // Set canvas size
                    canvas.width = img.width;
                    canvas.height = img.height;

                    // Draw base image
                    ctx.drawImage(img, 0, 0);

                    // Add watermark
                    this.drawWatermark(ctx, canvas.width, canvas.height, options);

                    // Convert to data URL
                    const dataUrl = canvas.toDataURL('image/png', 0.8);
                    resolve(dataUrl);
                };

                img.onerror = () => {
                    reject(new Error('فشل في تحميل الصورة الأساسية'));
                };

                img.src = baseImage;
            });
        } catch (error) {
            console.error('Error adding watermark:', error);
            throw new Error('فشل في إضافة العلامة المائية');
        }
    }

    /**
     * Draw watermark on canvas
     * @param {CanvasRenderingContext2D} ctx - Canvas context
     * @param {number} canvasWidth - Canvas width
     * @param {number} canvasHeight - Canvas height
     * @param {Object} options - Watermark options
     */
    drawWatermark(ctx, canvasWidth, canvasHeight, options) {
        // Set font
        ctx.font = `${options.fontSize}px Cairo, Arial, sans-serif`;
        ctx.textAlign = 'right';
        ctx.textBaseline = 'bottom';

        // Measure text
        const textMetrics = ctx.measureText(options.text);
        const textWidth = textMetrics.width;
        const textHeight = options.fontSize;

        // Calculate position
        let x, y;
        switch (options.position) {
            case 'top-left':
                x = options.padding + textWidth;
                y = options.padding + textHeight;
                break;
            case 'top-right':
                x = canvasWidth - options.padding;
                y = options.padding + textHeight;
                break;
            case 'bottom-left':
                x = options.padding + textWidth;
                y = canvasHeight - options.padding;
                break;
            case 'bottom-right':
            default:
                x = canvasWidth - options.padding;
                y = canvasHeight - options.padding;
                break;
        }

        // Draw background
        if (options.backgroundColor) {
            ctx.fillStyle = options.backgroundColor;
            ctx.fillRect(
                x - textWidth - options.padding,
                y - textHeight - options.padding / 2,
                textWidth + options.padding * 2,
                textHeight + options.padding
            );
        }

        // Draw text
        ctx.fillStyle = options.color;
        ctx.fillText(options.text, x, y);
    }

    /**
     * Wait for content to load (fonts, images)
     * @param {HTMLElement} container - Container element
     * @returns {Promise<void>}
     */
    async waitForContent(container) {
        // Wait for fonts
        if (document.fonts) {
            await document.fonts.ready;
        }

        // Wait for images
        const images = container.querySelectorAll('img');
        const imagePromises = Array.from(images).map(img => {
            if (img.complete) {
                return Promise.resolve();
            }
            return new Promise((resolve, reject) => {
                img.onload = resolve;
                img.onerror = resolve; // Don't fail on image errors
                setTimeout(resolve, 3000); // Timeout after 3 seconds
            });
        });

        await Promise.all(imagePromises);

        // Additional wait for rendering
        await new Promise(resolve => setTimeout(resolve, 500));
    }

    /**
     * Optimize thumbnail size
     * @param {string} dataUrl - Image data URL
     * @param {number} maxSize - Maximum file size in bytes
     * @returns {Promise<string>} Optimized image data URL
     */
    async optimizeSize(dataUrl, maxSize = 100000) { // 100KB default
        try {
            return new Promise((resolve) => {
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                const img = new Image();

                img.onload = () => {
                    canvas.width = img.width;
                    canvas.height = img.height;
                    ctx.drawImage(img, 0, 0);

                    let quality = 0.8;
                    let result = canvas.toDataURL('image/jpeg', quality);

                    // Reduce quality until size is acceptable
                    while (result.length > maxSize && quality > 0.1) {
                        quality -= 0.1;
                        result = canvas.toDataURL('image/jpeg', quality);
                    }

                    resolve(result);
                };

                img.src = dataUrl;
            });
        } catch (error) {
            console.error('Error optimizing thumbnail:', error);
            return dataUrl; // Return original if optimization fails
        }
    }

    /**
     * Generate multiple thumbnail sizes
     * @param {string} baseImage - Base image data URL
     * @param {Array} sizes - Array of size objects {width, height, name}
     * @returns {Promise<Object>} Object with different sized thumbnails
     */
    async generateMultipleSizes(baseImage, sizes = []) {
        const defaultSizes = [
            { width: 400, height: 300, name: 'medium' },
            { width: 200, height: 150, name: 'small' },
            { width: 100, height: 75, name: 'tiny' }
        ];

        const sizesToGenerate = sizes.length > 0 ? sizes : defaultSizes;
        const results = {};

        try {
            for (const size of sizesToGenerate) {
                const resized = await this.resizeImage(baseImage, size.width, size.height);
                results[size.name] = resized;
            }

            return results;
        } catch (error) {
            console.error('Error generating multiple sizes:', error);
            throw new Error('فشل في إنشاء أحجام متعددة للمعاينة');
        }
    }

    /**
     * Resize image to specific dimensions
     * @param {string} dataUrl - Image data URL
     * @param {number} width - Target width
     * @param {number} height - Target height
     * @returns {Promise<string>} Resized image data URL
     */
    async resizeImage(dataUrl, width, height) {
        return new Promise((resolve, reject) => {
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            const img = new Image();

            img.onload = () => {
                canvas.width = width;
                canvas.height = height;

                // Calculate scaling to maintain aspect ratio
                const scale = Math.min(width / img.width, height / img.height);
                const scaledWidth = img.width * scale;
                const scaledHeight = img.height * scale;

                // Center the image
                const x = (width - scaledWidth) / 2;
                const y = (height - scaledHeight) / 2;

                // Fill background
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, width, height);

                // Draw resized image
                ctx.drawImage(img, x, y, scaledWidth, scaledHeight);

                const result = canvas.toDataURL('image/png', 0.8);
                resolve(result);
            };

            img.onerror = () => {
                reject(new Error('فشل في تحميل الصورة للتغيير'));
            };

            img.src = dataUrl;
        });
    }
}

// Export singleton instance
export const thumbnailService = new ThumbnailService();
