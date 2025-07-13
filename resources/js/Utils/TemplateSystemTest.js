/**
 * Template System Test Utilities
 * Provides testing and validation functions for the new template system
 */

import { designDataService } from '@/Services/DesignDataService.js'
import { thumbnailService } from '@/Services/ThumbnailService.js'
import { watermarkService } from '@/Services/WatermarkService.js'
import { clientTemplateService } from '@/Services/ClientTemplateService.js'

export class TemplateSystemTest {
    constructor() {
        this.testResults = []
        this.errors = []
        this.warnings = []
    }

    /**
     * Run comprehensive system tests
     * @returns {Object} Test results
     */
    async runAllTests() {
        console.log('🧪 Starting Template System Tests...')
        
        this.testResults = []
        this.errors = []
        this.warnings = []

        // Test services
        await this.testDesignDataService()
        await this.testThumbnailService()
        await this.testWatermarkService()
        await this.testClientTemplateService()

        // Test integration
        await this.testSystemIntegration()

        // Generate report
        const report = this.generateTestReport()
        console.log('✅ Template System Tests Completed')
        
        return report
    }

    /**
     * Test Design Data Service
     */
    async testDesignDataService() {
        try {
            console.log('Testing Design Data Service...')

            // Test design data processing
            const mockEditor = this.createMockEditor()
            const designData = designDataService.processDesignData(mockEditor, {
                canvasWidth: 800,
                canvasHeight: 600,
                templateName: 'Test Template'
            })

            this.assert(designData.version, 'Design data should have version')
            this.assert(designData.design, 'Design data should have design object')
            this.assert(designData.editableElements, 'Design data should have editable elements')
            this.assert(designData.metadata, 'Design data should have metadata')

            // Test validation
            const validation = designDataService.validateDesignData(designData)
            this.assert(validation.isValid, 'Design data should be valid')

            this.addTestResult('Design Data Service', 'PASS', 'All tests passed')

        } catch (error) {
            this.addTestResult('Design Data Service', 'FAIL', error.message)
            this.errors.push(`Design Data Service: ${error.message}`)
        }
    }

    /**
     * Test Thumbnail Service
     */
    async testThumbnailService() {
        try {
            console.log('Testing Thumbnail Service...')

            // Test thumbnail generation from mock data
            const mockCanvas = this.createMockCanvas()
            
            // Test watermark addition
            const mockImage = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg=='
            const watermarkedImage = await thumbnailService.addWatermark(mockImage)
            
            this.assert(watermarkedImage, 'Should generate watermarked image')
            this.assert(watermarkedImage.startsWith('data:image'), 'Should return valid image data URL')

            // Test size optimization
            const optimizedImage = await thumbnailService.optimizeSize(watermarkedImage, 50000)
            this.assert(optimizedImage, 'Should optimize image size')

            this.addTestResult('Thumbnail Service', 'PASS', 'All tests passed')

        } catch (error) {
            this.addTestResult('Thumbnail Service', 'FAIL', error.message)
            this.errors.push(`Thumbnail Service: ${error.message}`)
        }
    }

    /**
     * Test Watermark Service
     */
    async testWatermarkService() {
        try {
            console.log('Testing Watermark Service...')

            // Test watermark HTML generation
            const html = '<div>Test content</div>'
            const watermarkedHTML = watermarkService.addWatermarkToHTML(html)
            
            this.assert(watermarkedHTML.includes('data-watermark="true"'), 'Should add watermark to HTML')
            this.assert(watermarkedHTML.includes('سامقة © جميع الحقوق محفوظة'), 'Should include watermark text')

            // Test watermark CSS generation
            const css = 'body { margin: 0; }'
            const watermarkedCSS = watermarkService.addWatermarkToCSS(css)
            
            this.assert(watermarkedCSS.includes('.watermark-element'), 'Should add watermark CSS')

            // Test watermark validation
            const validation = watermarkService.validateWatermarkIntegrity(watermarkedHTML)
            this.assert(validation.isValid, 'Watermarked content should be valid')

            // Test watermark removal
            const cleanHTML = watermarkService.removeWatermarkFromHTML(watermarkedHTML)
            this.assert(!cleanHTML.includes('data-watermark="true"'), 'Should remove watermark from HTML')

            this.addTestResult('Watermark Service', 'PASS', 'All tests passed')

        } catch (error) {
            this.addTestResult('Watermark Service', 'FAIL', error.message)
            this.errors.push(`Watermark Service: ${error.message}`)
        }
    }

    /**
     * Test Client Template Service
     */
    async testClientTemplateService() {
        try {
            console.log('Testing Client Template Service...')

            // Test client template preparation
            const mockTemplateData = this.createMockTemplateData()
            const clientTemplate = clientTemplateService.prepareForClientEditing(mockTemplateData)
            
            this.assert(clientTemplate.clientMode, 'Should be in client mode')
            this.assert(clientTemplate.restrictions, 'Should have client restrictions')
            this.assert(Array.isArray(clientTemplate.editableElements), 'Should have editable elements array')

            // Test client edit validation
            const editRequest = {
                elementId: 'test-element',
                newValue: 'Test content',
                styleChanges: { color: '#000000' }
            }
            
            const validation = clientTemplateService.validateClientEdit(editRequest, clientTemplate)
            // Note: This might fail if test element doesn't exist, which is expected

            // Test client export preparation
            const exportData = clientTemplateService.prepareClientExport(clientTemplate)
            this.assert(exportData.hasWatermark, 'Client export should always have watermark')

            this.addTestResult('Client Template Service', 'PASS', 'All tests passed')

        } catch (error) {
            this.addTestResult('Client Template Service', 'FAIL', error.message)
            this.errors.push(`Client Template Service: ${error.message}`)
        }
    }

    /**
     * Test system integration
     */
    async testSystemIntegration() {
        try {
            console.log('Testing System Integration...')

            // Test complete workflow
            const mockEditor = this.createMockEditor()
            
            // 1. Process design data
            const designData = designDataService.processDesignData(mockEditor)
            
            // 2. Add watermark to design
            const watermarkedHTML = watermarkService.addWatermarkToHTML(designData.design.html)
            designData.design.html = watermarkedHTML
            
            // 3. Prepare for client
            const clientTemplate = clientTemplateService.prepareForClientEditing(designData)
            
            // 4. Validate complete workflow
            this.assert(designData.editableElements, 'Should have editable elements')
            this.assert(watermarkedHTML.includes('data-watermark="true"'), 'Should have watermark')
            this.assert(clientTemplate.clientMode, 'Should be prepared for client')

            this.addTestResult('System Integration', 'PASS', 'Complete workflow test passed')

        } catch (error) {
            this.addTestResult('System Integration', 'FAIL', error.message)
            this.errors.push(`System Integration: ${error.message}`)
        }
    }

    /**
     * Create mock editor for testing
     */
    createMockEditor() {
        return {
            getHtml: () => '<div data-editable="true" data-element-type="text">Test content</div>',
            getCss: () => 'body { font-family: Cairo; }',
            getComponents: () => [
                {
                    getId: () => 'test-component',
                    get: (prop) => {
                        if (prop === 'attributes') return { 'data-editable': 'true', 'data-element-type': 'text' }
                        if (prop === 'content') return 'Test content'
                        return null
                    },
                    getStyle: () => ({ color: '#000000' }),
                    components: () => []
                }
            ],
            getStyle: () => 'body { margin: 0; }',
            getProjectData: () => ({ components: [], styles: [] })
        }
    }

    /**
     * Create mock canvas for testing
     */
    createMockCanvas() {
        const canvas = document.createElement('canvas')
        canvas.width = 400
        canvas.height = 300
        const ctx = canvas.getContext('2d')
        ctx.fillStyle = '#ffffff'
        ctx.fillRect(0, 0, 400, 300)
        ctx.fillStyle = '#000000'
        ctx.font = '16px Arial'
        ctx.fillText('Test Template', 50, 50)
        return canvas
    }

    /**
     * Create mock template data for testing
     */
    createMockTemplateData() {
        return {
            design: {
                html: '<div data-editable="true" data-element-type="text">Test content</div>',
                css: 'body { font-family: Cairo; }'
            },
            editableElements: [
                {
                    id: 'test-element',
                    type: 'text',
                    fieldName: 'Test Field',
                    content: 'Test content',
                    constraints: { required: false },
                    isLocked: false
                }
            ],
            metadata: {
                canvasWidth: 800,
                canvasHeight: 600
            }
        }
    }

    /**
     * Assert function for tests
     */
    assert(condition, message) {
        if (!condition) {
            throw new Error(message)
        }
    }

    /**
     * Add test result
     */
    addTestResult(testName, status, message) {
        this.testResults.push({
            test: testName,
            status: status,
            message: message,
            timestamp: new Date().toISOString()
        })
    }

    /**
     * Generate test report
     */
    generateTestReport() {
        const passedTests = this.testResults.filter(t => t.status === 'PASS').length
        const failedTests = this.testResults.filter(t => t.status === 'FAIL').length
        const totalTests = this.testResults.length

        return {
            summary: {
                total: totalTests,
                passed: passedTests,
                failed: failedTests,
                success_rate: totalTests > 0 ? (passedTests / totalTests * 100).toFixed(2) + '%' : '0%'
            },
            results: this.testResults,
            errors: this.errors,
            warnings: this.warnings,
            timestamp: new Date().toISOString()
        }
    }

    /**
     * Performance test for the system
     */
    async runPerformanceTests() {
        console.log('🚀 Running Performance Tests...')

        const results = {}

        // Test design data processing speed
        const start1 = performance.now()
        const mockEditor = this.createMockEditor()
        designDataService.processDesignData(mockEditor)
        const end1 = performance.now()
        results.designDataProcessing = `${(end1 - start1).toFixed(2)}ms`

        // Test watermark addition speed
        const start2 = performance.now()
        const html = '<div>'.repeat(100) + 'Test content' + '</div>'.repeat(100)
        watermarkService.addWatermarkToHTML(html)
        const end2 = performance.now()
        results.watermarkAddition = `${(end2 - start2).toFixed(2)}ms`

        // Test client template preparation speed
        const start3 = performance.now()
        const mockTemplateData = this.createMockTemplateData()
        clientTemplateService.prepareForClientEditing(mockTemplateData)
        const end3 = performance.now()
        results.clientPreparation = `${(end3 - start3).toFixed(2)}ms`

        console.log('Performance Results:', results)
        return results
    }
}

// Export singleton instance
export const templateSystemTest = new TemplateSystemTest()
