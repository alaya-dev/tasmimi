<?php

namespace App\Services;

class DesignDataService
{
    /**
     * Compress design data before saving to database
     */
    public static function compressDesignData($designData)
    {
        if (is_array($designData)) {
            $designData = json_encode($designData);
        }

        // Compress the data using gzip
        return base64_encode(gzcompress($designData, 9));
    }

    /**
     * Decompress design data after retrieving from database
     */
    public static function decompressDesignData($compressedData)
    {
        if (empty($compressedData)) {
            return null;
        }

        try {
            // Check if data is already compressed (starts with base64 encoded gzip)
            if (self::isCompressed($compressedData)) {
                $decompressed = gzuncompress(base64_decode($compressedData));
                return json_decode($decompressed, true);
            } else {
                // Data is not compressed, return as is (for backward compatibility)
                return is_string($compressedData) ? json_decode($compressedData, true) : $compressedData;
            }
        } catch (\Exception $e) {
            // If decompression fails, try to return original data
            return is_string($compressedData) ? json_decode($compressedData, true) : $compressedData;
        }
    }

    /**
     * Check if data is compressed
     */
    public static function isCompressed($data)
    {
        // Simple check: compressed data will be base64 encoded and much shorter than JSON
        if (!is_string($data)) {
            return false;
        }

        // Check if it's valid base64
        if (base64_encode(base64_decode($data, true)) !== $data) {
            return false;
        }

        // Try to decompress
        try {
            $decoded = base64_decode($data);
            $decompressed = gzuncompress($decoded);
            return $decompressed !== false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Optimize design data by removing unnecessary properties
     */
    public static function optimizeDesignData($designData)
    {
        if (is_string($designData)) {
            $designData = json_decode($designData, true);
        }

        if (!is_array($designData)) {
            return $designData;
        }

        // Remove unnecessary properties from elements
        if (isset($designData['elements']) && is_array($designData['elements'])) {
            foreach ($designData['elements'] as &$element) {
                // Remove default values to reduce size
                if (isset($element['rotation']) && $element['rotation'] == 0) {
                    unset($element['rotation']);
                }
                if (isset($element['opacity']) && $element['opacity'] == 1) {
                    unset($element['opacity']);
                }
                if (isset($element['visible']) && $element['visible'] == true) {
                    unset($element['visible']);
                }
                if (isset($element['locked']) && $element['locked'] == false) {
                    unset($element['locked']);
                }
                if (isset($element['borderWidth']) && $element['borderWidth'] == 0) {
                    unset($element['borderWidth']);
                }
            }
        }

        return $designData;
    }

    /**
     * Get the size of design data in bytes
     */
    public static function getDesignDataSize($designData)
    {
        if (is_array($designData)) {
            $designData = json_encode($designData);
        }

        return strlen($designData);
    }

    /**
     * Check if design data exceeds MySQL limits
     */
    public static function exceedsLimit($designData, $limit = 16777215) // 16MB default
    {
        return self::getDesignDataSize($designData) > $limit;
    }
}
