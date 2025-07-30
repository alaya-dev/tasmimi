<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\MoyasarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class MoyasarCallbackController extends Controller
{
    protected $moyasarService;

    public function __construct(MoyasarService $moyasarService)
    {
        $this->moyasarService = $moyasarService;
    }

    /**
     * Handle subscription payment callback
     */
    public function handleSubscriptionCallback(Request $request)
    {
        try {
            $paymentId = $request->query('id');
            $status = $request->query('status');
            $message = $request->query('message');
            $subscriptionId = $request->query('subscription_id');

            Log::info('Subscription payment callback received', [
                'payment_id' => $paymentId,
                'status' => $status,
                'message' => $message,
                'subscription_id' => $subscriptionId,
                'full_request' => $request->all()
            ]);

            if (!$paymentId) {
                Log::error('Subscription callback: Missing payment ID');
                return redirect()->route('client.subscriptions.index')
                    ->with('error', 'معرف الدفع مفقود');
            }

            // Verify payment with Moyasar API
            Log::info('Subscription callback: Verifying payment with Moyasar', ['payment_id' => $paymentId]);
            $moyasarPayment = $this->moyasarService->verifyPayment($paymentId);
            Log::info('Subscription callback: Moyasar payment details', $moyasarPayment);
            
            // Find local payment record
            Log::info('Subscription callback: Looking for local payment', ['payment_id' => $paymentId]);
            $payment = Payment::where('payment_gateway_id', $paymentId)->first();

            if (!$payment) {
                // Try to find by metadata or pending ID
                $payment = Payment::whereJsonContains('metadata->payment_id', $paymentId)
                    ->orWhere('payment_gateway_id', 'like', 'pending_%')
                    ->first();
            }

            if (!$payment) {
                Log::error('Local payment not found for subscription callback', [
                    'moyasar_payment_id' => $paymentId
                ]);
                
                return redirect()->route('client.subscriptions.index')
                    ->with('error', 'لم يتم العثور على معلومات الدفع');
            }

            Log::info('Subscription callback: Found payment', [
                'payment_id' => $payment->id,
                'user_id' => $payment->user_id,
                'subscription_id' => $payment->subscription_id,
                'current_status' => $payment->status
            ]);

            // Update payment with Moyasar ID if needed
            if ($payment->payment_gateway_id !== $paymentId) {
                $payment->update(['payment_gateway_id' => $paymentId]);
            }

            // Handle payment based on status
            if ($status === 'paid' && $moyasarPayment['status'] === 'paid') {
                // Verify payment details
                $expectedAmount = $payment->amount * 100; // Convert to halalas
                if ($moyasarPayment['amount'] != $expectedAmount) {
                    Log::error('Subscription payment amount mismatch', [
                        'expected' => $expectedAmount,
                        'received' => $moyasarPayment['amount'],
                        'payment_id' => $paymentId
                    ]);
                    
                    return redirect()->route('client.subscriptions.index')
                        ->with('error', 'خطأ في مبلغ الدفع');
                }

                // Process successful payment
                Log::info('Subscription callback: Processing successful payment');
                $this->moyasarService->handleSubscriptionPaymentSuccess($payment);

                Log::info('Subscription payment successful via callback', [
                    'payment_id' => $payment->id,
                    'user_id' => $payment->user_id,
                    'subscription_id' => $payment->subscription_id
                ]);

                return redirect()->route('client.subscription.manage')
                    ->with('success', 'تم تفعيل اشتراكك بنجاح!');

            } else {
                // Handle failed payment
                Log::info('Subscription callback: Payment failed or not paid', [
                    'status' => $status,
                    'moyasar_status' => $moyasarPayment['status'] ?? 'unknown'
                ]);
                
                $payment->markAsFailed();
                
                Log::info('Subscription payment failed', [
                    'payment_id' => $paymentId,
                    'status' => $status,
                    'moyasar_status' => $moyasarPayment['status'] ?? 'unknown'
                ]);

                return redirect()->route('client.subscriptions.index')
                    ->with('error', 'فشل في عملية الدفع: ' . ($message ?? 'خطأ غير معروف'));
            }

        } catch (Exception $e) {
            Log::error('Subscription callback error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payment_id' => $request->query('id'),
                'status' => $request->query('status'),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            return redirect()->route('client.subscriptions.index')
                ->with('error', 'حدث خطأ في معالجة الدفع');
        }
    }

    /**
     * Handle template payment callback
     */
    public function handleTemplateCallback(Request $request)
    {
        try {
            $paymentId = $request->query('id');
            $status = $request->query('status');
            $message = $request->query('message');
            $templateId = $request->query('template_id');

            Log::info('Template payment callback received', [
                'payment_id' => $paymentId,
                'status' => $status,
                'message' => $message,
                'template_id' => $templateId,
                'full_request' => $request->all()
            ]);

            if (!$paymentId) {
                Log::error('Template callback: Missing payment ID');
                return redirect()->route('client.my-designs')
                    ->with('error', 'معرف الدفع مفقود');
            }

            // Verify payment with Moyasar API
            Log::info('Template callback: Verifying payment with Moyasar', ['payment_id' => $paymentId]);
            $moyasarPayment = $this->moyasarService->verifyPayment($paymentId);
            Log::info('Template callback: Moyasar payment details', $moyasarPayment);
            
            // Find local template purchase record using the new TemplatePurchase model
            Log::info('Template callback: Looking for local template purchase', ['payment_id' => $paymentId]);
            $templatePurchase = \App\Models\TemplatePurchase::where('payment_gateway_id', $paymentId)->first();

            if (!$templatePurchase) {
                Log::error('Local template purchase not found for callback', [
                    'moyasar_payment_id' => $paymentId
                ]);
                
                return redirect()->route('client.my-designs')
                    ->with('error', 'لم يتم العثور على معلومات الشراء');
            }

            Log::info('Template callback: Found template purchase', [
                'purchase_id' => $templatePurchase->id,
                'template_id' => $templatePurchase->template_id,
                'user_id' => $templatePurchase->user_id,
                'current_status' => $templatePurchase->status
            ]);

            $template = $templatePurchase->template;
            if (!$template) {
                Log::error('Template callback: Template not found', [
                    'template_id' => $templatePurchase->template_id
                ]);
                return redirect()->route('client.my-designs')
                    ->with('error', 'لم يتم العثور على القالب');
            }

            Log::info('Template callback: Found template', [
                'template_id' => $template->id,
                'template_name' => $template->name
            ]);

            // Handle payment based on status
            if ($status === 'paid' && $moyasarPayment['status'] === 'paid') {
                // Verify payment details
                $expectedAmount = $templatePurchase->amount * 100; // Convert to halalas
                if ($moyasarPayment['amount'] != $expectedAmount) {
                    Log::error('Template payment amount mismatch', [
                        'expected' => $expectedAmount,
                        'received' => $moyasarPayment['amount'],
                        'payment_id' => $paymentId
                    ]);
                    
                    return redirect()->route('client.templates.purchase', $template)
                        ->with('error', 'خطأ في مبلغ الدفع');
                }

                // Process successful payment - mark as paid
                Log::info('Template callback: Marking purchase as paid');
                $templatePurchase->update([
                    'status' => \App\Models\TemplatePurchase::STATUS_PAID,
                    'paid_at' => now(),
                ]);

                Log::info('Template purchase successful via callback', [
                    'purchase_id' => $templatePurchase->id,
                    'template_id' => $template->id,
                    'user_id' => $templatePurchase->user_id
                ]);

                // Redirect to template create page
                Log::info('Template callback: Redirecting to template create page', [
                    'template_id' => $template->id,
                    'redirect_route' => route('client.templates.create', $template)
                ]);
                
                return redirect()->route('client.templates.create', $template)
                    ->with('success', 'تم شراء القالب بنجاح! يمكنك الآن استخدامه وإنشاء تصميماتك.');

            } else {
                // Handle failed payment
                Log::info('Template callback: Payment failed or not paid', [
                    'status' => $status,
                    'moyasar_status' => $moyasarPayment['status'] ?? 'unknown'
                ]);
                
                $templatePurchase->update([
                    'status' => \App\Models\TemplatePurchase::STATUS_FAILED
                ]);
                
                Log::info('Template payment failed', [
                    'payment_id' => $paymentId,
                    'template_id' => $template->id,
                    'status' => $status,
                    'moyasar_status' => $moyasarPayment['status'] ?? 'unknown'
                ]);

                return redirect()->route('client.templates.purchase', $template)
                    ->with('error', 'فشل في عملية الدفع: ' . ($message ?? 'خطأ غير معروف'));
            }

        } catch (Exception $e) {
            Log::error('Template callback error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payment_id' => $request->query('id'),
                'status' => $request->query('status'),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            return redirect()->route('client.my-designs')
                ->with('error', 'حدث خطأ في معالجة الدفع');
        }
    }
}
