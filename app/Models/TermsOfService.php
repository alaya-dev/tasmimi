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
        'content_blocks',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'extracted_content',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'content_blocks' => 'array',
        'file_size' => 'integer',
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
     * Check if this terms has an uploaded file.
     */
    public function hasFile(): bool
    {
        return !empty($this->file_path);
    }

    /**
     * Get file URL for download.
     */
    public function getFileUrlAttribute(): ?string
    {
        if (!$this->hasFile()) {
            return null;
        }
        
        return asset('storage/' . $this->file_path);
    }

    /**
     * Get formatted file size.
     */
    public function getFormattedFileSizeAttribute(): ?string
    {
        if (!$this->file_size) {
            return null;
        }
        
        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' ميجابايت';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' كيلوبايت';
        } else {
            return $bytes . ' بايت';
        }
    }
}
