/**
 * Service pour gérer le filigrane côté client
 * Le filigrane est obligatoire et ne peut jamais être supprimé
 */
export class ClientWatermarkService {
    /**
     * Configuration du filigrane client (plus visible que l'admin)
     */
    static getWatermarkConfig() {
        return {
            text: 'منصة سامقة للتصميم ',
            enabled: true,
            position: 'bottom-right',
            style: {
                fontSize: '16px',
                color: 'rgba(0, 0, 0, 0.4)', // Plus visible que l'admin (0.25)
                fontFamily: 'Cairo, sans-serif',
                fontWeight: 'bold',
                rotation: '-15deg',
                opacity: '0.6',
                textShadow: '1px 1px 2px rgba(0,0,0,0.1)',
                userSelect: 'none',
                pointerEvents: 'none'
            },
            removable: false, // Ne peut jamais être supprimé
            editable: false   // Ne peut jamais être modifié
        };
    }

    /**
     * Applique le filigrane à un design
     */
    static applyWatermark(designData) {
        if (!designData || typeof designData !== 'object') {
            designData = {};
        }

        // Forcer l'application du filigrane
        designData.watermark = this.getWatermarkConfig();
        
        return designData;
    }

    /**
     * Vérifie si le filigrane est présent et valide
     */
    static validateWatermark(designData) {
        if (!designData || !designData.watermark) {
            return false;
        }

        const watermark = designData.watermark;
        const config = this.getWatermarkConfig();

        // Vérifier les propriétés essentielles
        return (
            watermark.text === config.text &&
            watermark.enabled === true &&
            watermark.removable === false &&
            watermark.style &&
            watermark.style.opacity === config.style.opacity
        );
    }

    /**
     * Répare un design en ajoutant/corrigeant le filigrane
     */
    static repairWatermark(designData) {
        if (!this.validateWatermark(designData)) {
            return this.applyWatermark(designData);
        }
        return designData;
    }

    /**
     * Crée l'élément DOM du filigrane pour l'affichage
     */
    static createWatermarkElement() {
        const watermark = document.createElement('div');
        const config = this.getWatermarkConfig();
        
        watermark.textContent = config.text;
        watermark.style.cssText = `
            position: absolute;
            bottom: 16px;
            right: 16px;
            font-size: ${config.style.fontSize};
            color: ${config.style.color};
            font-family: ${config.style.fontFamily};
            font-weight: ${config.style.fontWeight};
            transform: rotate(${config.style.rotation});
            opacity: ${config.style.opacity};
            text-shadow: ${config.style.textShadow};
            user-select: ${config.style.userSelect};
            pointer-events: ${config.style.pointerEvents};
            z-index: 9999;
        `;
        
        // Ajouter des attributs pour empêcher la manipulation
        watermark.setAttribute('data-watermark', 'true');
        watermark.setAttribute('data-protected', 'true');
        
        return watermark;
    }

    /**
     * Surveille et protège le filigrane contre la suppression
     */
    static protectWatermark(container) {
        if (!container) return;

        // Observer pour détecter les tentatives de suppression
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.type === 'childList') {
                    // Vérifier si le filigrane existe toujours
                    const watermark = container.querySelector('[data-watermark="true"]');
                    if (!watermark) {
                        // Recréer le filigrane s'il a été supprimé
                        const newWatermark = this.createWatermarkElement();
                        container.appendChild(newWatermark);
                    }
                }
            });
        });

        observer.observe(container, {
            childList: true,
            subtree: true
        });

        // Ajouter le filigrane initial s'il n'existe pas
        if (!container.querySelector('[data-watermark="true"]')) {
            const watermark = this.createWatermarkElement();
            container.appendChild(watermark);
        }

        return observer;
    }

    /**
     * Applique le filigrane lors de l'export
     */
    static applyWatermarkForExport(canvas, format = 'png') {
        const config = this.getWatermarkConfig();
        
        if (canvas && canvas.getContext) {
            const ctx = canvas.getContext('2d');
            
            // Sauvegarder l'état actuel
            ctx.save();
            
            // Configurer le style du filigrane
            ctx.font = `${config.style.fontWeight} ${config.style.fontSize} ${config.style.fontFamily}`;
            ctx.fillStyle = config.style.color;
            ctx.globalAlpha = parseFloat(config.style.opacity);
            
            // Calculer la position
            const text = config.text;
            const textMetrics = ctx.measureText(text);
            const x = canvas.width - textMetrics.width - 20;
            const y = canvas.height - 20;
            
            // Appliquer la rotation
            ctx.translate(x + textMetrics.width / 2, y);
            ctx.rotate((parseFloat(config.style.rotation) * Math.PI) / 180);
            ctx.translate(-textMetrics.width / 2, 0);
            
            // Dessiner le filigrane
            ctx.fillText(text, 0, 0);
            
            // Restaurer l'état
            ctx.restore();
        }
        
        return canvas;
    }

    /**
     * Vérifie si l'utilisateur est un client (pour appliquer le filigrane)
     */
    static shouldApplyClientWatermark(user) {
        return user && user.role === 'client';
    }

    /**
     * Nettoie les tentatives de suppression du filigrane dans les données
     */
    static sanitizeDesignData(designData) {
        if (!designData || typeof designData !== 'object') {
            return this.applyWatermark({});
        }

        // Forcer la présence du filigrane
        designData = this.applyWatermark(designData);
        
        // Supprimer toute tentative de désactivation
        if (designData.watermark) {
            designData.watermark.enabled = true;
            designData.watermark.removable = false;
            designData.watermark.editable = false;
        }
        
        return designData;
    }

    /**
     * Génère un hash du filigrane pour vérifier l'intégrité
     */
    static generateWatermarkHash() {
        const config = this.getWatermarkConfig();
        const data = JSON.stringify({
            text: config.text,
            enabled: config.enabled,
            removable: config.removable
        });
        
        // Simple hash pour vérification côté client
        let hash = 0;
        for (let i = 0; i < data.length; i++) {
            const char = data.charCodeAt(i);
            hash = ((hash << 5) - hash) + char;
            hash = hash & hash; // Convert to 32-bit integer
        }
        
        return hash.toString(16);
    }

    /**
     * Vérifie l'intégrité du filigrane
     */
    static verifyWatermarkIntegrity(designData) {
        if (!this.validateWatermark(designData)) {
            return false;
        }
        
        // Vérifications supplémentaires
        const watermark = designData.watermark;
        
        return (
            watermark.text === 'سامقة للتصميم' &&
            watermark.enabled === true &&
            watermark.removable === false &&
            watermark.editable === false
        );
    }
}

export default ClientWatermarkService;
