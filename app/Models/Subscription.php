<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'duration_days',
        'price',
        'gateway_price_id',
        'gateway_product_id',
        'description',
        'features',
        'is_active',
        'sort_order',
        'is_popular',
        'color',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'is_popular' => 'boolean',
        'features' => 'array',
        'duration_days' => 'integer',
        'sort_order' => 'integer',
    ];

    // Constants for subscription types
    const TYPE_ANNUAL = 'annual';
    const TYPE_MONTHLY = 'monthly';
    const TYPE_WEEKLY = 'weekly';

    public static function getTypes()
    {
        return [
            self::TYPE_ANNUAL => 'سنوي',
            self::TYPE_MONTHLY => 'شهري',
            self::TYPE_WEEKLY => 'أسبوعي',
        ];
    }

    public function getTypeNameAttribute()
    {
        $types = self::getTypes();
        return $types[$this->type] ?? $this->type;
    }

    /**
     * Get the duration in a human-readable format.
     */
    public function getDurationTextAttribute()
    {
        if ($this->duration_days) {
            if ($this->duration_days == 7) {
                return 'أسبوعي';
            } elseif ($this->duration_days == 30) {
                return 'شهري';
            } elseif ($this->duration_days == 90) {
                return 'ربع سنوي';
            } elseif ($this->duration_days == 365) {
                return 'سنوي';
            } else {
                return $this->duration_days . ' يوم';
            }
        }

        return $this->type_name;
    }

    /**
     * Scope a query to order subscriptions by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('price');
    }

    /**
     * Scope a query to only include active subscriptions.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get popular subscription.
     */
    public static function popular()
    {
        return static::where('is_popular', true)->first();
    }

    /**
     * Get the user subscriptions for this plan.
     */
    public function userSubscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class);
    }

    /**
     * Get the payments for this subscription plan.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Calculate the end date based on start date and duration.
     */
    public function calculateEndDate($startDate = null)
    {
        $startDate = $startDate ?: now();
        $endDate = $startDate->copy(); // Create a copy to avoid modifying the original

        if ($this->duration_days) {
            return $endDate->addDays($this->duration_days);
        }

        // Fallback to type-based calculation
        switch ($this->type) {
            case self::TYPE_WEEKLY:
                return $endDate->addWeek();
            case self::TYPE_MONTHLY:
                return $endDate->addMonth();
            case self::TYPE_ANNUAL:
                return $endDate->addYear();
            default:
                return $endDate->addMonth(); // Default to monthly
        }
    }
}
