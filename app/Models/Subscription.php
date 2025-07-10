<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'price',
        'description',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
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
}
