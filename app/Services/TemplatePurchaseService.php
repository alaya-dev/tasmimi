<?php

namespace App\Services;

use App\Models\Template;
use App\Models\TemplatePurchase;
use App\Models\User;
use Exception;

class TemplatePurchaseService
{
    protected $moyasarService;

    public function __construct(MoyasarService $moyasarService)
    {
        $this->moyasarService = $moyasarService;
    }

    /**
     * Create a template purchase payment.
     */
    public function createTemplatePurchasePayment(User $user, Template $template, array $paymentData)
    {
        try {
            // Check if user already purchased this template (paid status)
            $existingPaidPurchase = TemplatePurchase::where('user_id', $user->id)
                ->where('template_id', $template->id)
                ->where('status', TemplatePurchase::STATUS_PAID)
                ->first();

            if ($existingPaidPurchase) {
                throw new Exception('لقد قمت بشراء هذا القالب مسبقاً');
            }

            // Check if there's already a pending purchase and reuse it
            $existingPendingPurchase = TemplatePurchase::where('user_id', $user->id)
                ->where('template_id', $template->id)
                ->where('status', TemplatePurchase::STATUS_PENDING)
                ->first();

            if ($existingPendingPurchase) {
                \Log::info('Reusing existing pending purchase', [
                    'purchase_id' => $existingPendingPurchase->id,
                    'user_id' => $user->id,
                    'template_id' => $template->id
                ]);
                $purchase = $existingPendingPurchase;
            } else {
                // Create new purchase record
                $purchase = TemplatePurchase::create([
                    'user_id' => $user->id,
                    'template_id' => $template->id,
                    'amount' => $template->price,
                    'currency' => 'SAR',
                    'status' => TemplatePurchase::STATUS_PENDING,
                    'payment_method' => 'moyasar',
                ]);
            }

            // Check if template is free
            if ($template->isFree()) {
                throw new Exception('هذا القالب مجاني، لا يحتاج إلى شراء');
            }

            \Log::info('Creating template purchase payment', [
                'user_id' => $user->id,
                'template_id' => $template->id,
                'amount' => $template->price
            ]);



            // Create Moyasar payment using the service method
            $result = $this->moyasarService->createTemplatePurchasePayment($user, $template, $paymentData);

            // Update purchase with Moyasar payment ID
            $purchase->update([
                'payment_gateway_id' => $result['id'],
                'metadata' => $result,
            ]);

            \Log::info('Template purchase payment created', [
                'purchase_id' => $purchase->id,
                'moyasar_payment_id' => $result['id'],
                'status' => $result['status']
            ]);

            return $result;

        } catch (Exception $e) {
            \Log::error('Template purchase payment failed', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'template_id' => $template->id
            ]);

            throw $e;
        }
    }

    /**
     * Get payment status from Moyasar.
     */
    public function getPaymentStatus($paymentId)
    {
        return $this->moyasarService->getPayment($paymentId);
    }

    /**
     * Handle successful template purchase payment.
     */
    public function handlePaymentSuccess(TemplatePurchase $purchase)
    {
        $purchase->markAsPaid();

        \Log::info('Template purchase completed successfully', [
            'purchase_id' => $purchase->id,
            'user_id' => $purchase->user_id,
            'template_id' => $purchase->template_id
        ]);

        return $purchase;
    }

    /**
     * Handle failed template purchase payment.
     */
    public function handlePaymentFailure(TemplatePurchase $purchase, string $reason)
    {
        $purchase->markAsFailed();

        \Log::error('Template purchase payment failed', [
            'purchase_id' => $purchase->id,
            'user_id' => $purchase->user_id,
            'template_id' => $purchase->template_id,
            'reason' => $reason
        ]);

        return $purchase;
    }

    /**
     * Get purchase by Moyasar payment ID.
     */
    public function getPurchaseByPaymentId(string $paymentId)
    {
        return TemplatePurchase::where('payment_gateway_id', $paymentId)->first();
    }

    /**
     * Check if user can access template (has subscription or purchased it).
     */
    public function canUserAccessTemplate(User $user, Template $template): bool
    {
        // If template is free, everyone can access
        if ($template->isFree()) {
            return true;
        }

        // If user has active subscription, they can access all templates
        if ($user->hasActiveSubscription()) {
            return true;
        }

        // Check if user purchased this specific template
        return $user->hasPurchasedTemplate($template->id);
    }

    /**
     * Get user's template purchases with pagination.
     */
    public function getUserPurchases(User $user, int $perPage = 10)
    {
        return $user->templatePurchases()
            ->with('template')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}
