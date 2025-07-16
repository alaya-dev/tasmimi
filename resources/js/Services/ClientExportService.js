import ClientWatermarkService from './ClientWatermarkService.js';

/**
 * Service pour l'export des designs clients avec filigrane intégré
 */
export class ClientExportService {
    /**
     * Formats d'export supportés
     */
    static getSupportedFormats() {
        return {
            png: {
                name: 'PNG',
                extension: 'png',
                mimeType: 'image/png',
                supportsQuality: false,
                description: 'صورة PNG عالية الجودة'
            },
            jpeg: {
                name: 'JPEG',
                extension: 'jpg',
                mimeType: 'image/jpeg',
                supportsQuality: true,
                description: 'صورة JPEG مضغوطة'
            },
            svg: {
                name: 'SVG',
                extension: 'svg',
                mimeType: 'image/svg+xml',
                supportsQuality: false,
                description: 'رسم متجه قابل للتكبير'
            },
            pdf: {
                name: 'PDF',
                extension: 'pdf',
                mimeType: 'application/pdf',
                supportsQuality: true,
                description: 'مستند PDF للطباعة'
            }
        };
    }

    /**
     * Exporte un design vers le format spécifié
     */
    static async exportDesign(designData, options = {}) {
        const {
            format = 'png',
            quality = 90,
            width = 800,
            height = 600,
            filename = 'design'
        } = options;

        // Vérifier que le format est supporté
        const formats = this.getSupportedFormats();
        if (!formats[format]) {
            throw new Error(`Format non supporté: ${format}`);
        }

        // Appliquer le filigrane obligatoire
        designData = ClientWatermarkService.applyWatermark(designData);

        try {
            switch (format) {
                case 'png':
                    return await this.exportToPNG(designData, { width, height, filename });
                case 'jpeg':
                    return await this.exportToJPEG(designData, { width, height, quality, filename });
                case 'svg':
                    return await this.exportToSVG(designData, { width, height, filename });
                case 'pdf':
                    return await this.exportToPDF(designData, { width, height, quality, filename });
                default:
                    throw new Error(`Format non implémenté: ${format}`);
            }
        } catch (error) {
            console.error('Export error:', error);
            throw new Error(`Erreur lors de l'export: ${error.message}`);
        }
    }

    /**
     * Crée un canvas à partir des données de design
     */
    static async createCanvasFromDesign(designData, width, height) {
        const canvas = document.createElement('canvas');
        canvas.width = width;
        canvas.height = height;
        const ctx = canvas.getContext('2d');

        // Fond blanc par défaut
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, width, height);

        // Dessiner les éléments du design
        if (designData.elements) {
            for (const element of designData.elements) {
                await this.drawElementOnCanvas(ctx, element, width, height);
            }
        }

        // Appliquer le filigrane
        ClientWatermarkService.applyWatermarkForExport(canvas);

        return canvas;
    }

    /**
     * Dessine un élément sur le canvas
     */
    static async drawElementOnCanvas(ctx, element, canvasWidth, canvasHeight) {
        ctx.save();

        // Calculer les positions relatives
        const x = (element.x / 800) * canvasWidth;
        const y = (element.y / 600) * canvasHeight;
        const width = (element.width / 800) * canvasWidth;
        const height = (element.height / 600) * canvasHeight;

        // Appliquer les transformations
        if (element.rotation) {
            ctx.translate(x + width / 2, y + height / 2);
            ctx.rotate((element.rotation * Math.PI) / 180);
            ctx.translate(-width / 2, -height / 2);
        } else {
            ctx.translate(x, y);
        }

        switch (element.type) {
            case 'text':
                await this.drawTextElement(ctx, element, width, height);
                break;
            case 'image':
                await this.drawImageElement(ctx, element, width, height);
                break;
            case 'shape':
                await this.drawShapeElement(ctx, element, width, height);
                break;
        }

        ctx.restore();
    }

    /**
     * Dessine un élément texte
     */
    static async drawTextElement(ctx, element, width, height) {
        const style = element.style || {};
        
        ctx.font = `${style.fontWeight || 'normal'} ${style.fontSize || '16px'} ${style.fontFamily || 'Arial'}`;
        ctx.fillStyle = style.color || '#000000';
        ctx.textAlign = style.textAlign || 'right';
        ctx.textBaseline = 'top';

        // Dessiner le texte
        const lines = element.content.split('\n');
        const lineHeight = parseInt(style.fontSize || '16') * 1.2;
        
        lines.forEach((line, index) => {
            ctx.fillText(line, style.textAlign === 'center' ? width / 2 : 0, index * lineHeight);
        });
    }

    /**
     * Dessine un élément image
     */
    static async drawImageElement(ctx, element, width, height) {
        return new Promise((resolve) => {
            const img = new Image();
            img.crossOrigin = 'anonymous';
            
            img.onload = () => {
                ctx.drawImage(img, 0, 0, width, height);
                resolve();
            };
            
            img.onerror = () => {
                // Dessiner un placeholder en cas d'erreur
                ctx.fillStyle = '#f0f0f0';
                ctx.fillRect(0, 0, width, height);
                ctx.strokeStyle = '#ccc';
                ctx.strokeRect(0, 0, width, height);
                resolve();
            };
            
            img.src = element.src;
        });
    }

    /**
     * Dessine un élément forme
     */
    static async drawShapeElement(ctx, element, width, height) {
        const style = element.style || {};
        
        ctx.fillStyle = style.backgroundColor || '#000000';
        
        if (element.shapeType === 'circle') {
            ctx.beginPath();
            ctx.arc(width / 2, height / 2, Math.min(width, height) / 2, 0, 2 * Math.PI);
            ctx.fill();
        } else {
            // Rectangle par défaut
            ctx.fillRect(0, 0, width, height);
        }
    }

    /**
     * Export en PNG
     */
    static async exportToPNG(designData, options) {
        const canvas = await this.createCanvasFromDesign(designData, options.width, options.height);
        const dataUrl = canvas.toDataURL('image/png');
        
        return {
            dataUrl,
            blob: await this.dataUrlToBlob(dataUrl),
            filename: `${options.filename}.png`
        };
    }

    /**
     * Export en JPEG
     */
    static async exportToJPEG(designData, options) {
        const canvas = await this.createCanvasFromDesign(designData, options.width, options.height);
        const quality = options.quality / 100;
        const dataUrl = canvas.toDataURL('image/jpeg', quality);
        
        return {
            dataUrl,
            blob: await this.dataUrlToBlob(dataUrl),
            filename: `${options.filename}.jpg`
        };
    }

    /**
     * Export en SVG
     */
    static async exportToSVG(designData, options) {
        let svg = `<svg width="${options.width}" height="${options.height}" xmlns="http://www.w3.org/2000/svg">`;
        
        // Fond blanc
        svg += `<rect width="100%" height="100%" fill="white"/>`;
        
        // Ajouter les éléments
        if (designData.elements) {
            for (const element of designData.elements) {
                svg += this.elementToSVG(element, options.width, options.height);
            }
        }
        
        // Ajouter le filigrane
        const watermark = ClientWatermarkService.getWatermarkConfig();
        svg += `<text x="${options.width - 100}" y="${options.height - 20}" 
                 font-family="${watermark.style.fontFamily}" 
                 font-size="${watermark.style.fontSize}" 
                 fill="${watermark.style.color}" 
                 opacity="${watermark.style.opacity}"
                 transform="rotate(${watermark.style.rotation} ${options.width - 50} ${options.height - 20})">
                 ${watermark.text}
                 </text>`;
        
        svg += '</svg>';
        
        const blob = new Blob([svg], { type: 'image/svg+xml' });
        
        return {
            svg,
            blob,
            filename: `${options.filename}.svg`
        };
    }

    /**
     * Export en PDF (nécessite une bibliothèque PDF)
     */
    static async exportToPDF(designData, options) {
        // Pour l'instant, on convertit en PNG puis en PDF
        // Dans une implémentation complète, on utiliserait jsPDF ou similar
        const pngResult = await this.exportToPNG(designData, options);
        
        return {
            dataUrl: pngResult.dataUrl,
            blob: pngResult.blob,
            filename: `${options.filename}.pdf`,
            note: 'Export PDF basique - nécessite une bibliothèque PDF pour une implémentation complète'
        };
    }

    /**
     * Convertit un élément en SVG
     */
    static elementToSVG(element, canvasWidth, canvasHeight) {
        const x = (element.x / 800) * canvasWidth;
        const y = (element.y / 600) * canvasHeight;
        const width = (element.width / 800) * canvasWidth;
        const height = (element.height / 600) * canvasHeight;

        switch (element.type) {
            case 'text':
                const style = element.style || {};
                return `<text x="${x}" y="${y + 20}" 
                        font-family="${style.fontFamily || 'Arial'}" 
                        font-size="${style.fontSize || '16px'}" 
                        fill="${style.color || '#000000'}"
                        text-anchor="${style.textAlign === 'center' ? 'middle' : 'start'}">
                        ${element.content}
                        </text>`;
            
            case 'shape':
                if (element.shapeType === 'circle') {
                    return `<circle cx="${x + width/2}" cy="${y + height/2}" r="${Math.min(width, height)/2}" 
                            fill="${element.style?.backgroundColor || '#000000'}"/>`;
                } else {
                    return `<rect x="${x}" y="${y}" width="${width}" height="${height}" 
                            fill="${element.style?.backgroundColor || '#000000'}"/>`;
                }
            
            case 'image':
                return `<image x="${x}" y="${y}" width="${width}" height="${height}" href="${element.src}"/>`;
            
            default:
                return '';
        }
    }

    /**
     * Convertit un data URL en Blob
     */
    static async dataUrlToBlob(dataUrl) {
        const response = await fetch(dataUrl);
        return await response.blob();
    }

    /**
     * Télécharge un fichier
     */
    static downloadFile(blob, filename) {
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }

    /**
     * Génère un nom de fichier unique
     */
    static generateFilename(baseName, format) {
        const timestamp = new Date().toISOString().slice(0, 19).replace(/[:-]/g, '');
        const extension = this.getSupportedFormats()[format]?.extension || format;
        return `${baseName}_${timestamp}.${extension}`;
    }
}

export default ClientExportService;
