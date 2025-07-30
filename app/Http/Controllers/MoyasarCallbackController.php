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

            Log::info('Subscription payment callback received', [
                'payment_id' => $paymentId,
                'status' => $status,
                'message' => $message
            ]);

            if (!$paymentId) {
                return redirect()->route('client.subscriptions.index')
                    ->with('error', 'معرف الدفع مفقود');
            }

            // Verify payment with Moyasar API
            $moyasarPayment = $this->moyasarService->verifyPayment($paymentId);
            
            // Find local payment record
            $payment = Payment::where('payment_gateway_id', $paymentId)->first();

            if (!$payment) {
                // Try to find by metadata or pending ID
                $payment = Payment::whereJsonContains('metadata->payment_id', $paymentId)
                    ->orWhere('payment_gateway_id', 'like', 'pending_%')
                    ->first();
            }

            if (!$payment) {
                Log::error('Local payment not found for callback', [
                    'moyasar_payment_id' => $paymentId
                ]);
                
                return redirect()->route('client.subscriptions.index')
                    ->with('error', 'لم يتم العثور على معلومات الدفع');
            }

            // Update payment with Moyasar ID if needed
            if ($payment->payment_gateway_id !== $paymentId) {
                $payment->update(['payment_gateway_id' => $paymentId]);
            }

            // Handle payment based on status
            if ($status === 'paid' && $moyasarPayment['status'] === 'paid') {
                // Verify payment details
                $expectedAmount = $payment->amount * 100; // Convert to halalas
                if ($moyasarPayment['amount'] != $expectedAmount) {
                    Log::error('Payment amount mismatch', [
                        'expected' => $expectedAmount,
                        'received' => $moyasarPayment['amount'],
                        'payment_id' => $paymentId
                    ]);
                    
                    return redirect()->route('client.subscriptions.index')
                        ->with('error', 'خطأ في مبلغ الدفع');
                }

                // Process successful payment
                $this->moyasarService->handleSubscriptionPaymentSuccess($payment);

                return redirect()->route('client.subscription.manage')
                    ->with('success', 'تم تفعيل اشتراكك بنجاح!');

            } else {
                // Handle failed payment
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
                'payment_id' => $request->query('id'),
                'status' => $request->query('status')
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
                'template_id' => $templateId
            ]);

            if (!$paymentId) {
                return redirect()->route('client.templates')
                    ->with('error', 'معرف الدفع مفقود');
            }

            // Verify payment with Moyasar API
            $moyasarPayment = $this->moyasarService->verifyPayment($paymentId);
            
            // Find local template purchase record using the new TemplatePurchase model
            $templatePurchase = \App\Models\TemplatePurchase::where('payment_gateway_id', $paymentId)->first();

            if (!$templatePurchase) {
                Log::error('Local template purchase not found for callback', [
                    'moyasar_payment_id' => $paymentId
                ]);
                
                return redirect()->route('client.templates')
                    ->with('error', 'لم يتم العثور على معلومات الشراء');
            }

            $template = $templatePurchase->template;
            if (!$template) {
                return redirect()->route('client.templates')
                    ->with('error', 'لم يتم العثور على القالب');
            }

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
                    
                    return redirect()->route('client.templates')
                        ->with('error', 'خطأ في مبلغ الدفع');
                }

                // Process successful payment - mark as paid
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
                return redirect()->route('client.templates.create', $template)
                    ->with('success', 'تم شراء القالب بنجاح! يمكنك الآن استخدامه وإنشاء تصميماتك.');

            } else {
                // Handle failed payment
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
                'payment_id' => $request->query('id'),
                'status' => $request->query('status')
            ]);

            return redirect()->route('client.templates')
                ->with('error', 'حدث خطأ في معالجة الدفع');
        }
    }
}
