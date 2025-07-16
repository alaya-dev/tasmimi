<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientTemplate extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'template_id',
        'name',
        'design_data',
        'editable_elements',
        'canvas_size',
        'thumbnail',
        'notes',
        'version',
        'last_edited_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'design_data' => 'array',
        'editable_elements' => 'array',
        'last_edited_at' => 'datetime',
    ];

    /**
     * Get the user that owns the client template.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the original template that this client template is based on.
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }

    /**
     * Get the thumbnail URL.
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->thumbnail ? asset('storage/' . $this->thumbnail) : null;
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
     * Scope for templates of a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for recently edited templates.
     */
    public function scopeRecentlyEdited($query, $days = 7)
    {
        return $query->where('last_edited_at', '>=', now()->subDays($days));
    }

    /**
     * Get the design data with watermark applied.
     */
    public function getDesignDataWithWatermarkAttribute(): array
    {
        $designData = $this->design_data;
        
        // Ajouter le filigrane obligatoire pour les clients
        if (is_array($designData)) {
            $designData['watermark'] = [
                'text' => 'سامقة للتصميم',
                'enabled' => true,
                'position' => 'bottom-right',
                'style' => [
                    'fontSize' => '16px',
                    'color' => 'rgba(0, 0, 0, 0.4)', // Plus visible que l'admin
                    'fontFamily' => 'Cairo, sans-serif',
                    'fontWeight' => 'bold',
                    'rotation' => '-15deg',
                    'opacity' => '0.6'
                ],
                'removable' => false // Ne peut jamais être supprimé
            ];
        }
        
        return $designData;
    }

    /**
     * Update the last edited timestamp.
     */
    public function touch($attribute = null)
    {
        if (!$attribute) {
            $this->last_edited_at = now();
        }
        
        return parent::touch($attribute);
    }
}
