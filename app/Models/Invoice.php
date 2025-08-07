<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Carbon\Carbon;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'user_id',
        'invoiceable_type',
        'invoiceable_id',
        'amount',
        'currency',
        'status',
        'type',
        'invoice_date',
        'due_date',
        'paid_at',
        'description',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'invoice_date' => 'datetime',
        'due_date' => 'datetime',
        'paid_at' => 'datetime',
        'metadata' => 'array',
    ];

    // Constants for invoice status
    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELED = 'canceled';
    const STATUS_REFUNDED = 'refunded';

    // Constants for invoice types
    const TYPE_SUBSCRIPTION = 'subscription';
    const TYPE_TEMPLATE = 'template';

    /**
     * Get the user that owns the invoice.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the invoiceable model (Payment or TemplatePurchase).
     */
    public function invoiceable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Generate a unique invoice number.
     */
    public static function generateInvoiceNumber(): string
    {
        $date = now()->format('Y-m-d');
        $count = self::whereDate('created_at', $date)->count() + 1;

        return 'INV-' . now()->format('Y-m-') . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get the Arabic type label.
     */
    public function getTypeArabicAttribute(): string
    {
        return match($this->type) {
            self::TYPE_SUBSCRIPTION => 'اشتراك',
            self::TYPE_TEMPLATE => 'قالب',
            default => $this->type
        };
    }

    /**
     * Get the Arabic status label.
     */
    public function getStatusArabicAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'في الانتظار',
            self::STATUS_PAID => 'مدفوع',
            self::STATUS_FAILED => 'فشل',
            self::STATUS_CANCELED => 'ملغي',
            self::STATUS_REFUNDED => 'مسترد',
            default => $this->status
        };
    }

    /**
     * Check if invoice is paid.
     */
    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    /**
     * Check if invoice is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->due_date < now() && !$this->isPaid();
    }

    /**
     * Get the related item name (subscription name or template name).
     */
    public function getItemNameAttribute(): string
    {
        if ($this->type === self::TYPE_SUBSCRIPTION && $this->invoiceable) {
            return $this->invoiceable->subscription->name ?? 'اشتراك';
        }

        if ($this->type === self::TYPE_TEMPLATE && $this->invoiceable) {
            return $this->invoiceable->template->name ?? 'قالب';
        }

        return 'غير محدد';
    }
}
