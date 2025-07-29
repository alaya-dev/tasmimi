<?php

namespace App\Services;

use Smalot\PdfParser\Parser;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Exception;

class FileProcessorService
{
    /**
     * Process uploaded file and extract content
     */
    public function processFile(UploadedFile $file): array
    {
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('terms_files', $fileName, 'public');
        $fileType = $this->getFileType($file);
        $fileSize = $file->getSize();
        
        try {
            $extractedContent = $this->extractContent($file, $fileType);
            
            return [
                'success' => true,
                'file_name' => $fileName,
                'file_path' => $filePath,
                'file_type' => $fileType,
                'file_size' => $fileSize,
                'extracted_content' => $extractedContent,
            ];
        } catch (Exception $e) {
            // Clean up uploaded file if processing fails
            Storage::disk('public')->delete($filePath);
            
            return [
                'success' => false,
                'error' => 'فشل في معالجة الملف: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Extract text content from file
     */
    private function extractContent(UploadedFile $file, string $fileType): string
    {
        switch ($fileType) {
            case 'pdf':
                return $this->extractPdfContent($file);
            default:
                throw new Exception('نوع الملف غير مدعوم. يرجى رفع ملف PDF فقط.');
        }
    }

    /**
     * Extract content from PDF document
     */
    private function extractPdfContent(UploadedFile $file): string
    {
        try {
            $parser = new Parser();
            $pdf = $parser->parseFile($file->getPathname());
            $content = $pdf->getText();
            
            return trim($content);
        } catch (Exception $e) {
            throw new Exception('فشل في قراءة ملف PDF: ' . $e->getMessage());
        }
    }

    /**
     * Determine file type
     */
    private function getFileType(UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $mimeType = $file->getMimeType();
        
        if ($extension === 'pdf' || $mimeType === 'application/pdf') {
            return 'pdf';
        }
        
        throw new Exception('نوع الملف غير مدعوم. يرجى رفع ملف PDF فقط.');
    }

    /**
     * Delete file from storage
     */
    public function deleteFile(string $filePath): bool
    {
        try {
            return Storage::disk('public')->delete($filePath);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get file URL for download
     */
    public function getFileUrl(string $filePath): string
    {
        return Storage::disk('public')->url($filePath);
    }
}
