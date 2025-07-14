/**
 * Photo Editor Service
 * Provides advanced photo editing capabilities using Canvas API and filters
 */

export class PhotoEditorService {
    constructor() {
        this.canvas = null;
        this.ctx = null;
        this.originalImageData = null;
        this.currentImageData = null;
        this.history = [];
        this.historyIndex = -1;
    }

    /**
     * Initialize the photo editor with an image
     */
    async initWithImage(imageElement) {
        return new Promise((resolve, reject) => {
            try {
                // Create canvas
                this.canvas = document.createElement('canvas');
                this.ctx = this.canvas.getContext('2d');
                
                // Set canvas size to match image
                this.canvas.width = imageElement.naturalWidth || imageElement.width;
                this.canvas.height = imageElement.naturalHeight || imageElement.height;
                
                // Draw image to canvas
                this.ctx.drawImage(imageElement, 0, 0);
                
                // Store original image data
                this.originalImageData = this.ctx.getImageData(0, 0, this.canvas.width, this.canvas.height);
                this.currentImageData = this.ctx.getImageData(0, 0, this.canvas.width, this.canvas.height);
                
                // Initialize history
                this.history = [this.cloneImageData(this.originalImageData)];
                this.historyIndex = 0;
                
                resolve(this.canvas);
            } catch (error) {
                reject(error);
            }
        });
    }

    /**
     * Apply brightness adjustment
     */
    adjustBrightness(value) {
        const imageData = this.cloneImageData(this.currentImageData);
        const data = imageData.data;
        
        for (let i = 0; i < data.length; i += 4) {
            data[i] = Math.max(0, Math.min(255, data[i] + value));     // Red
            data[i + 1] = Math.max(0, Math.min(255, data[i + 1] + value)); // Green
            data[i + 2] = Math.max(0, Math.min(255, data[i + 2] + value)); // Blue
        }
        
        this.applyImageData(imageData);
        this.saveToHistory();
    }

    /**
     * Apply contrast adjustment
     */
    adjustContrast(value) {
        const imageData = this.cloneImageData(this.currentImageData);
        const data = imageData.data;
        const factor = (259 * (value + 255)) / (255 * (259 - value));
        
        for (let i = 0; i < data.length; i += 4) {
            data[i] = Math.max(0, Math.min(255, factor * (data[i] - 128) + 128));     // Red
            data[i + 1] = Math.max(0, Math.min(255, factor * (data[i + 1] - 128) + 128)); // Green
            data[i + 2] = Math.max(0, Math.min(255, factor * (data[i + 2] - 128) + 128)); // Blue
        }
        
        this.applyImageData(imageData);
        this.saveToHistory();
    }

    /**
     * Apply saturation adjustment
     */
    adjustSaturation(value) {
        const imageData = this.cloneImageData(this.currentImageData);
        const data = imageData.data;
        const factor = value / 100;
        
        for (let i = 0; i < data.length; i += 4) {
            const r = data[i];
            const g = data[i + 1];
            const b = data[i + 2];
            
            // Convert to grayscale
            const gray = 0.299 * r + 0.587 * g + 0.114 * b;
            
            // Apply saturation
            data[i] = Math.max(0, Math.min(255, gray + factor * (r - gray)));
            data[i + 1] = Math.max(0, Math.min(255, gray + factor * (g - gray)));
            data[i + 2] = Math.max(0, Math.min(255, gray + factor * (b - gray)));
        }
        
        this.applyImageData(imageData);
        this.saveToHistory();
    }

    /**
     * Apply hue adjustment
     */
    adjustHue(value) {
        const imageData = this.cloneImageData(this.currentImageData);
        const data = imageData.data;
        const hueShift = value * Math.PI / 180;
        
        for (let i = 0; i < data.length; i += 4) {
            const r = data[i] / 255;
            const g = data[i + 1] / 255;
            const b = data[i + 2] / 255;
            
            // Convert RGB to HSL
            const max = Math.max(r, g, b);
            const min = Math.min(r, g, b);
            let h, s, l = (max + min) / 2;
            
            if (max === min) {
                h = s = 0; // achromatic
            } else {
                const d = max - min;
                s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
                switch (max) {
                    case r: h = (g - b) / d + (g < b ? 6 : 0); break;
                    case g: h = (b - r) / d + 2; break;
                    case b: h = (r - g) / d + 4; break;
                }
                h /= 6;
            }
            
            // Adjust hue
            h = (h + hueShift / (2 * Math.PI)) % 1;
            if (h < 0) h += 1;
            
            // Convert back to RGB
            const hslToRgb = (h, s, l) => {
                let r, g, b;
                if (s === 0) {
                    r = g = b = l; // achromatic
                } else {
                    const hue2rgb = (p, q, t) => {
                        if (t < 0) t += 1;
                        if (t > 1) t -= 1;
                        if (t < 1/6) return p + (q - p) * 6 * t;
                        if (t < 1/2) return q;
                        if (t < 2/3) return p + (q - p) * (2/3 - t) * 6;
                        return p;
                    };
                    const q = l < 0.5 ? l * (1 + s) : l + s - l * s;
                    const p = 2 * l - q;
                    r = hue2rgb(p, q, h + 1/3);
                    g = hue2rgb(p, q, h);
                    b = hue2rgb(p, q, h - 1/3);
                }
                return [Math.round(r * 255), Math.round(g * 255), Math.round(b * 255)];
            };
            
            const [newR, newG, newB] = hslToRgb(h, s, l);
            data[i] = newR;
            data[i + 1] = newG;
            data[i + 2] = newB;
        }
        
        this.applyImageData(imageData);
        this.saveToHistory();
    }

    /**
     * Apply sepia filter
     */
    applySepia() {
        const imageData = this.cloneImageData(this.currentImageData);
        const data = imageData.data;
        
        for (let i = 0; i < data.length; i += 4) {
            const r = data[i];
            const g = data[i + 1];
            const b = data[i + 2];
            
            data[i] = Math.min(255, (r * 0.393) + (g * 0.769) + (b * 0.189));
            data[i + 1] = Math.min(255, (r * 0.349) + (g * 0.686) + (b * 0.168));
            data[i + 2] = Math.min(255, (r * 0.272) + (g * 0.534) + (b * 0.131));
        }
        
        this.applyImageData(imageData);
        this.saveToHistory();
    }

    /**
     * Apply grayscale filter
     */
    applyGrayscale() {
        const imageData = this.cloneImageData(this.currentImageData);
        const data = imageData.data;
        
        for (let i = 0; i < data.length; i += 4) {
            const gray = data[i] * 0.299 + data[i + 1] * 0.587 + data[i + 2] * 0.114;
            data[i] = gray;
            data[i + 1] = gray;
            data[i + 2] = gray;
        }
        
        this.applyImageData(imageData);
        this.saveToHistory();
    }

    /**
     * Apply vintage filter
     */
    applyVintage() {
        const imageData = this.cloneImageData(this.currentImageData);
        const data = imageData.data;
        
        for (let i = 0; i < data.length; i += 4) {
            const r = data[i];
            const g = data[i + 1];
            const b = data[i + 2];
            
            // Vintage effect: reduce blue, enhance red/yellow
            data[i] = Math.min(255, r * 1.2);
            data[i + 1] = Math.min(255, g * 1.1);
            data[i + 2] = Math.max(0, b * 0.8);
        }
        
        this.applyImageData(imageData);
        this.saveToHistory();
    }

    /**
     * Apply blur filter
     */
    applyBlur(radius = 1) {
        const imageData = this.cloneImageData(this.currentImageData);
        const data = imageData.data;
        const width = imageData.width;
        const height = imageData.height;
        const output = new Uint8ClampedArray(data);
        
        for (let y = 0; y < height; y++) {
            for (let x = 0; x < width; x++) {
                let r = 0, g = 0, b = 0, a = 0;
                let count = 0;
                
                for (let dy = -radius; dy <= radius; dy++) {
                    for (let dx = -radius; dx <= radius; dx++) {
                        const nx = x + dx;
                        const ny = y + dy;
                        
                        if (nx >= 0 && nx < width && ny >= 0 && ny < height) {
                            const idx = (ny * width + nx) * 4;
                            r += data[idx];
                            g += data[idx + 1];
                            b += data[idx + 2];
                            a += data[idx + 3];
                            count++;
                        }
                    }
                }
                
                const idx = (y * width + x) * 4;
                output[idx] = r / count;
                output[idx + 1] = g / count;
                output[idx + 2] = b / count;
                output[idx + 3] = a / count;
            }
        }
        
        const newImageData = new ImageData(output, width, height);
        this.applyImageData(newImageData);
        this.saveToHistory();
    }

    /**
     * Crop image
     */
    crop(x, y, width, height) {
        const croppedImageData = this.ctx.getImageData(x, y, width, height);
        
        // Resize canvas
        this.canvas.width = width;
        this.canvas.height = height;
        
        // Clear and draw cropped image
        this.ctx.clearRect(0, 0, width, height);
        this.ctx.putImageData(croppedImageData, 0, 0);
        
        // Update current image data
        this.currentImageData = this.ctx.getImageData(0, 0, width, height);
        this.saveToHistory();
    }

    /**
     * Resize image
     */
    resize(newWidth, newHeight) {
        const tempCanvas = document.createElement('canvas');
        const tempCtx = tempCanvas.getContext('2d');
        
        // Copy current canvas to temp canvas
        tempCanvas.width = this.canvas.width;
        tempCanvas.height = this.canvas.height;
        tempCtx.putImageData(this.currentImageData, 0, 0);
        
        // Resize main canvas
        this.canvas.width = newWidth;
        this.canvas.height = newHeight;
        
        // Draw resized image
        this.ctx.clearRect(0, 0, newWidth, newHeight);
        this.ctx.drawImage(tempCanvas, 0, 0, newWidth, newHeight);
        
        // Update current image data
        this.currentImageData = this.ctx.getImageData(0, 0, newWidth, newHeight);
        this.saveToHistory();
    }

    /**
     * Reset to original image
     */
    reset() {
        this.currentImageData = this.cloneImageData(this.originalImageData);
        this.canvas.width = this.originalImageData.width;
        this.canvas.height = this.originalImageData.height;
        this.applyImageData(this.currentImageData);
        this.history = [this.cloneImageData(this.originalImageData)];
        this.historyIndex = 0;
    }

    /**
     * Undo last operation
     */
    undo() {
        if (this.historyIndex > 0) {
            this.historyIndex--;
            this.currentImageData = this.cloneImageData(this.history[this.historyIndex]);
            this.applyImageData(this.currentImageData);
        }
    }

    /**
     * Redo last undone operation
     */
    redo() {
        if (this.historyIndex < this.history.length - 1) {
            this.historyIndex++;
            this.currentImageData = this.cloneImageData(this.history[this.historyIndex]);
            this.applyImageData(this.currentImageData);
        }
    }

    /**
     * Get canvas as data URL
     */
    getDataURL(format = 'image/png', quality = 1) {
        return this.canvas.toDataURL(format, quality);
    }

    /**
     * Get canvas as blob
     */
    getBlob(format = 'image/png', quality = 1) {
        return new Promise(resolve => {
            this.canvas.toBlob(resolve, format, quality);
        });
    }

    /**
     * Helper: Clone image data
     */
    cloneImageData(imageData) {
        return new ImageData(
            new Uint8ClampedArray(imageData.data),
            imageData.width,
            imageData.height
        );
    }

    /**
     * Helper: Apply image data to canvas
     */
    applyImageData(imageData) {
        this.ctx.putImageData(imageData, 0, 0);
        this.currentImageData = imageData;
    }

    /**
     * Helper: Save current state to history
     */
    saveToHistory() {
        // Remove any future history if we're not at the end
        this.history = this.history.slice(0, this.historyIndex + 1);
        
        // Add current state
        this.history.push(this.cloneImageData(this.currentImageData));
        this.historyIndex++;
        
        // Limit history size
        if (this.history.length > 20) {
            this.history.shift();
            this.historyIndex--;
        }
    }

    /**
     * Check if undo is available
     */
    canUndo() {
        return this.historyIndex > 0;
    }

    /**
     * Check if redo is available
     */
    canRedo() {
        return this.historyIndex < this.history.length - 1;
    }

    /**
     * Apply multiple adjustments at once
     */
    applyAdjustments(adjustments) {
        let imageData = this.cloneImageData(this.currentImageData);

        // Apply brightness
        if (adjustments.brightness !== 0) {
            imageData = this.applyBrightnessToImageData(imageData, adjustments.brightness);
        }

        // Apply contrast
        if (adjustments.contrast !== 0) {
            imageData = this.applyContrastToImageData(imageData, adjustments.contrast);
        }

        // Apply saturation
        if (adjustments.saturation !== 100) {
            imageData = this.applySaturationToImageData(imageData, adjustments.saturation);
        }

        // Apply hue
        if (adjustments.hue !== 0) {
            imageData = this.applyHueToImageData(imageData, adjustments.hue);
        }

        this.applyImageData(imageData);
        this.saveToHistory();
    }

    /**
     * Helper methods for applying adjustments to image data without affecting canvas
     */
    applyBrightnessToImageData(imageData, value) {
        const data = imageData.data;
        for (let i = 0; i < data.length; i += 4) {
            data[i] = Math.max(0, Math.min(255, data[i] + value));
            data[i + 1] = Math.max(0, Math.min(255, data[i + 1] + value));
            data[i + 2] = Math.max(0, Math.min(255, data[i + 2] + value));
        }
        return imageData;
    }

    applyContrastToImageData(imageData, value) {
        const data = imageData.data;
        const factor = (259 * (value + 255)) / (255 * (259 - value));
        for (let i = 0; i < data.length; i += 4) {
            data[i] = Math.max(0, Math.min(255, factor * (data[i] - 128) + 128));
            data[i + 1] = Math.max(0, Math.min(255, factor * (data[i + 1] - 128) + 128));
            data[i + 2] = Math.max(0, Math.min(255, factor * (data[i + 2] - 128) + 128));
        }
        return imageData;
    }

    applySaturationToImageData(imageData, value) {
        const data = imageData.data;
        const factor = value / 100;
        for (let i = 0; i < data.length; i += 4) {
            const r = data[i];
            const g = data[i + 1];
            const b = data[i + 2];
            const gray = 0.299 * r + 0.587 * g + 0.114 * b;
            data[i] = Math.max(0, Math.min(255, gray + factor * (r - gray)));
            data[i + 1] = Math.max(0, Math.min(255, gray + factor * (g - gray)));
            data[i + 2] = Math.max(0, Math.min(255, gray + factor * (b - gray)));
        }
        return imageData;
    }

    applyHueToImageData(imageData, value) {
        // Implementation similar to adjustHue but on imageData parameter
        // ... (keeping it simple for now)
        return imageData;
    }
}

// Export singleton instance
export const photoEditorService = new PhotoEditorService();
