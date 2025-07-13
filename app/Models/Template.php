<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Template extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'category_id',
        'thumbnail',
        'background_image',
        'design_data',
        'editable_elements',
        'canvas_size',
        'has_watermark',
        'design_notes',
        'version',
        'is_active',
        'created_by',
        'last_edited_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'has_watermark' => 'boolean',
        'design_data' => 'array',
        'editable_elements' => 'array',
        'last_edited_at' => 'datetime',
    ];

    /**
     * Get the category that owns the template.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the user that created the template.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the thumbnail URL.
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->thumbnail ? asset('storage/' . $this->thumbnail) : null;
    }

    /**
     * Get the background image URL.
     */
    public function getBackgroundImageUrlAttribute(): ?string
    {
        return $this->background_image ? asset('storage/' . $this->background_image) : null;
    }

    /**
     * Get canvas dimensions as array.
     */
    public function getCanvasDimensionsAttribute(): array
    {
        $dimensions = explode('x', $this->canvas_size ?? '800x600');
        return [
            'width' => (int) ($dimensions[0] ?? 800),
            'height' => (int) ($dimensions[1] ?? 600)
        ];
    }

    /**
     * Check if template has editable elements.
     */
    public function hasEditableElements(): bool
    {
        return !empty($this->editable_elements);
    }

    /**
     * Get count of editable elements.
     */
    public function getEditableElementsCountAttribute(): int
    {
        return count($this->editable_elements ?? []);
    }

    /**
     * Update last edited timestamp.
     */
    public function touchLastEdited(): void
    {
        $this->update(['last_edited_at' => now()]);
    }

    /**
     * Scope for active templates.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for templates by category.
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope for recently edited templates.
     */
    public function scopeRecentlyEdited($query, $days = 7)
    {
        return $query->where('last_edited_at', '>=', now()->subDays($days));
    }



    /**
     * Get the thumbnail URL attribute.
     */
    protected function thumbnailUrl(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->thumbnail ? asset('storage/' . $this->thumbnail) : null,
        );
    }
}
