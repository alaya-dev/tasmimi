<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TermsOfService extends Model
{
    use HasFactory;

    protected $table = 'terms_of_service';

    protected $fillable = [
        'title',
        'content',
        'is_active',
        'version',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the user who created the terms.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the terms.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the current active terms of service.
     */
    public static function getActive()
    {
        return self::where('is_active', true)->first();
    }

    /**
     * Deactivate all other terms when activating this one.
     */
    public function activate()
    {
        // Deactivate all other terms
        self::where('id', '!=', $this->id)->update(['is_active' => false]);
        
        // Activate this one
        $this->update(['is_active' => true]);
    }

    /**
     * Clean and validate HTML content for RTL support
     */
    public function getCleanContentAttribute(): string
    {
        $content = $this->content ?? '';
        
        // Allow specific HTML tags for rich text
        $allowedTags = '<p><br><b><strong><i><em><u><ul><ol><li><div><span><h1><h2><h3><h4><h5><h6>';
        $content = strip_tags($content, $allowedTags);
        
        // Ensure RTL direction for Arabic content
        if (preg_match('/[\x{0600}-\x{06FF}]/u', $content)) {
            // Wrap content in RTL div if it contains Arabic
            if (strpos($content, 'dir="rtl"') === false && strpos($content, 'text-align: right') === false) {
                $content = '<div dir="rtl" style="text-align: right;">' . $content . '</div>';
            }
        }
        
        return $content;
    }
}
