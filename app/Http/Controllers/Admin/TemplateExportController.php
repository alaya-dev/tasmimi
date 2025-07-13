<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TemplateExportController extends Controller
{
    /**
     * Export template with watermark protection.
     */
    public function exportWithWatermark(Request $request, Template $template)
    {
        try {
            $request->validate([
                'format' => 'required|in:png,jpg,pdf,html',
                'quality' => 'nullable|numeric|between:0.1,1',
                'width' => 'nullable|integer|min:100|max:4000',
                'height' => 'nullable|integer|min:100|max:4000',
            ]);

            $format = $request->input('format', 'png');
            $quality = $request->input('quality', 0.8);
            $width = $request->input('width', 800);
            $height = $request->input('height', 600);

            // Ensure template has design data
            if (!$template->design_data) {
                return response()->json([
                    'success' => false,
                    'message' => 'القالب لا يحتوي على بيانات تصميم'
                ], 400);
            }

            $designData = json_decode($template->design_data, true);
            if (!$designData) {
                return response()->json([
                    'success' => false,
                    'message' => 'بيانات التصميم تالفة'
                ], 400);
            }

            // Add watermark protection to the design
            $protectedDesign = $this->addWatermarkProtection($designData);

            // Generate export based on format
            switch ($format) {
                case 'html':
                    return $this->exportAsHTML($template, $protectedDesign);
                case 'pdf':
                    return $this->exportAsPDF($template, $protectedDesign, $width, $height);
                case 'png':
                case 'jpg':
                default:
                    return $this->exportAsImage($template, $protectedDesign, $format, $quality, $width, $height);
            }

        } catch (\Exception $e) {
            \Log::error('Template export error: ' . $e->getMessage(), [
                'template_id' => $template->id,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'خطأ في تصدير القالب'
            ], 500);
        }
    }

    /**
     * Export template for client use (always with watermark).
     */
    public function exportForClient(Request $request, Template $template)
    {
        try {
            // Clients can only export with watermark
            $request->merge(['watermark_required' => true]);

            return $this->exportWithWatermark($request, $template);

        } catch (\Exception $e) {
            \Log::error('Client export error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'خطأ في تصدير القالب للعميل'
            ], 500);
        }
    }

    /**
     * Add watermark protection to design data.
     */
    private function addWatermarkProtection($designData)
    {
        $watermarkText = 'سامقة © جميع الحقوق محفوظة';
        
        // Ensure HTML has watermark
        if (isset($designData['design']['html'])) {
            $html = $designData['design']['html'];
            
            // Check if watermark already exists
            if (!str_contains($html, 'data-watermark="true"') && !str_contains($html, $watermarkText)) {
                // Add watermark HTML
                $watermarkHTML = $this->generateWatermarkHTML();
                
                // Find insertion point
                if (preg_match('/<div[^>]*>/i', $html)) {
                    $html = preg_replace('/(<div[^>]*>)/i', '$1' . $watermarkHTML, $html, 1);
                } else {
                    $html = $watermarkHTML . $html;
                }
                
                $designData['design']['html'] = $html;
            }
        }

        // Ensure CSS has watermark protection
        if (isset($designData['design']['css'])) {
            $css = $designData['design']['css'];
            
            if (!str_contains($css, '.watermark-element')) {
                $css .= $this->generateWatermarkCSS();
                $designData['design']['css'] = $css;
            }
        }

        return $designData;
    }

    /**
     * Generate watermark HTML.
     */
    private function generateWatermarkHTML()
    {
        return '
            <div 
                data-watermark="true" 
                data-element-type="watermark"
                class="watermark-element"
                style="
                    position: absolute;
                    bottom: 10px;
                    right: 10px;
                    font-size: 10px;
                    color: rgba(107, 114, 128, 0.6);
                    background-color: rgba(255, 255, 255, 0.8);
                    padding: 4px 8px;
                    border-radius: 4px;
                    font-family: Cairo, Arial, sans-serif;
                    z-index: 1000;
                    pointer-events: none;
                    user-select: none;
                    direction: ltr;
                "
            >
                سامقة © جميع الحقوق محفوظة
            </div>
        ';
    }

    /**
     * Generate watermark CSS.
     */
    private function generateWatermarkCSS()
    {
        return '
            /* Watermark Protection */
            .watermark-element {
                position: absolute !important;
                font-size: 10px !important;
                color: rgba(107, 114, 128, 0.6) !important;
                background-color: rgba(255, 255, 255, 0.8) !important;
                padding: 4px 8px !important;
                border-radius: 4px !important;
                font-family: Cairo, Arial, sans-serif !important;
                z-index: 1000 !important;
                pointer-events: none !important;
                user-select: none !important;
                direction: ltr !important;
                -webkit-user-select: none !important;
                -moz-user-select: none !important;
                -ms-user-select: none !important;
            }

            @media print {
                .watermark-element {
                    display: block !important;
                    visibility: visible !important;
                    opacity: 0.8 !important;
                }
            }

            .watermark-element[style*="display: none"],
            .watermark-element[style*="visibility: hidden"],
            .watermark-element[style*="opacity: 0"] {
                display: block !important;
                visibility: visible !important;
                opacity: 0.6 !important;
            }
        ';
    }

    /**
     * Export as HTML file.
     */
    private function exportAsHTML($template, $designData)
    {
        $html = $designData['design']['html'] ?? '';
        $css = $designData['design']['css'] ?? '';

        $fullHTML = "<!DOCTYPE html>
<html lang=\"ar\" dir=\"rtl\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>{$template->name}</title>
    <link href=\"https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap\" rel=\"stylesheet\">
    <style>
        body {
            margin: 0;
            padding: 20px;
            font-family: 'Cairo', Arial, sans-serif;
            direction: rtl;
            background: #f5f5f5;
        }
        .template-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            position: relative;
        }
        {$css}
    </style>
</head>
<body>
    <div class=\"template-container\">
        {$html}
    </div>
    <script>
        // Prevent watermark removal
        document.addEventListener('DOMContentLoaded', function() {
            const watermarks = document.querySelectorAll('.watermark-element');
            watermarks.forEach(function(watermark) {
                watermark.style.setProperty('display', 'block', 'important');
                watermark.style.setProperty('visibility', 'visible', 'important');
                watermark.style.setProperty('opacity', '0.6', 'important');
            });
        });
    </script>
</body>
</html>";

        $filename = Str::slug($template->name) . '_export.html';

        return response($fullHTML)
            ->header('Content-Type', 'text/html; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Export as image (PNG/JPG).
     */
    private function exportAsImage($template, $designData, $format, $quality, $width, $height)
    {
        // This would typically use a service like Puppeteer or similar
        // For now, return instructions for client-side generation
        
        return response()->json([
            'success' => true,
            'message' => 'استخدم وظيفة التصدير في المحرر لإنشاء الصورة',
            'export_data' => [
                'html' => $designData['design']['html'] ?? '',
                'css' => $designData['design']['css'] ?? '',
                'format' => $format,
                'quality' => $quality,
                'width' => $width,
                'height' => $height,
                'watermark_required' => true
            ]
        ]);
    }

    /**
     * Export as PDF.
     */
    private function exportAsPDF($template, $designData, $width, $height)
    {
        // This would typically use a PDF generation library
        // For now, return instructions for client-side generation
        
        return response()->json([
            'success' => true,
            'message' => 'استخدم وظيفة التصدير في المحرر لإنشاء PDF',
            'export_data' => [
                'html' => $designData['design']['html'] ?? '',
                'css' => $designData['design']['css'] ?? '',
                'format' => 'pdf',
                'width' => $width,
                'height' => $height,
                'watermark_required' => true
            ]
        ]);
    }

    /**
     * Validate export permissions.
     */
    private function validateExportPermissions($user, $template, $format)
    {
        // Admin and Super Admin can export in any format
        if (in_array($user->role, ['admin', 'super_admin'])) {
            return true;
        }

        // Clients can only export with watermark
        if ($user->role === 'client') {
            return true; // Always with watermark
        }

        return false;
    }
}
