<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Template;
use App\Models\TemplatePurchase;
use App\Models\Payment;
use App\Services\TemplatePurchaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Exception;

class TemplatePurchaseController extends Controller
{
    protected $templatePurchaseService;

    public function __construct(TemplatePurchaseService $templatePurchaseService)
    {
        $this->templatePurchaseService = $templatePurchaseService;
    }

    /**
     * Show the template purchase page.
     */
    public function show(Template $template): Response|RedirectResponse
    {
        $user = Auth::user();

        // Check if template exists and is active
        if (!$template->is_active) {
            abort(404, 'القالب غير متاح');
        }

        // Check if template is free
        if ($template->isFree()) {
            return redirect()->route('client.templates.create', $template)
                ->with('success', 'هذا القالب مجاني، يمكنك استخدامه مباشرة');
        }

        // Check if user already has access
        if ($this->templatePurchaseService->canUserAccessTemplate($user, $template)) {
            return redirect()->route('client.templates.create', $template)
                ->with('success', 'يمكنك استخدام هذا القالب');
        }

        // Redirect to the simple Moyasar payment page
        return redirect()->route('client.templates.purchase.simple', $template);
    }

    /**
     * Process the template purchase payment.
     */
    public function processPayment(Request $request, Template $template)
    {
        $request->validate([
            'card_name' => 'required|string|max:255',
            'card_number' => 'required|string',
            'card_cvc' => 'required|string|size:3',
            'card_month' => 'required|integer|between:1,12',
            'card_year' => 'required|integer|min:' . date('Y'),
        ], [
            'card_name.required' => 'اسم حامل البطاقة مطلوب',
            'card_number.required' => 'رقم البطاقة مطلوب',
            'card_cvc.required' => 'رمز الأمان مطلوب',
            'card_cvc.size' => 'رمز الأمان يجب أن يكون 3 أرقام',
            'card_month.required' => 'شهر انتهاء البطاقة مطلوب',
            'card_year.required' => 'سنة انتهاء البطاقة مطلوبة',
        ]);

        $user = Auth::user();

        try {
            // Check if user can purchase this template
            if ($this->templatePurchaseService->canUserAccessTemplate($user, $template)) {
                if (request()->header('X-Inertia')) {
                    return redirect()->route('client.templates.create', $template)
                        ->with('info', 'لديك وصول لهذا القالب بالفعل');
                }
                return response()->json([
                    'error' => 'لديك وصول لهذا القالب بالفعل'
                ], 400);
            }

            // Process payment
            $moyasarPayment = $this->templatePurchaseService->createTemplatePurchasePayment($user, $template, [
                'name' => $request->card_name,
                'number' => $request->card_number,
                'cvc' => $request->card_cvc,
                'month' => $request->card_month,
                'year' => $request->card_year,
            ]);

            // Check payment status
            if ($moyasarPayment['status'] === 'paid') {
                // Payment is completed
                $purchase = $this->templatePurchaseService->getPurchaseByPaymentId($moyasarPayment['id']);
                $this->templatePurchaseService->handlePaymentSuccess($purchase);

                \Log::info('Template purchase completed', [
                    'purchase_id' => $purchase->id,
                    'moyasar_status' => $moyasarPayment['status'],
                    'user_id' => $purchase->user_id,
                    'template_id' => $purchase->template_id
                ]);

                if (request()->header('X-Inertia')) {
                    return redirect()->route('client.my-designs')
                        ->with('success', 'تم شراء القالب بنجاح! يمكنك الآن استخدامه بالكامل وحفظ تصميماتك وتحميلها.');
                }

                return response()->json([
                    'success' => true,
                    'message' => 'تم شراء القالب بنجاح',
                    'redirect' => route('client.my-designs')
                ]);
            } elseif ($moyasarPayment['status'] === 'initiated') {
                // Payment is created but needs to be completed via Moyasar interface
                \Log::info('Template purchase initiated, redirecting to Moyasar', [
                    'payment_id' => $moyasarPayment['id'],
                    'moyasar_status' => $moyasarPayment['status']
                ]);

                // Redirect to Moyasar payment page
                if (isset($moyasarPayment['source']['transaction_url'])) {
                    // For Inertia requests, redirect directly to the payment URL
                    return redirect()->away($moyasarPayment['source']['transaction_url']);
                }

                // Fallback: redirect back with message
                return redirect()->back()->with('info', 'تم إنشاء الدفع بنجاح. يرجى إتمام عملية الدفع.');
            } elseif ($moyasarPayment['status'] === 'failed') {
                $purchase = $this->templatePurchaseService->getPurchaseByPaymentId($moyasarPayment['id']);
                $errorMessage = $moyasarPayment['source']['message'] ?? 'فشل الدفع';
                $this->templatePurchaseService->handlePaymentFailure($purchase, $errorMessage);

                if (request()->header('X-Inertia')) {
                    return redirect()->route('client.templates.purchase', $template)
                        ->with('error', 'فشل في معالجة الدفع: ' . $errorMessage);
                }

                return response()->json([
                    'error' => 'فشل في معالجة الدفع: ' . $errorMessage
                ], 400);
            } else {
                // Payment is pending or requires additional action
                if (request()->header('X-Inertia')) {
                    return redirect()->route('client.templates.purchase', $template)
                        ->with('info', 'الدفع قيد المعالجة، سيتم إعلامك بالنتيجة قريباً');
                }

                return response()->json([
                    'pending' => true,
                    'payment_id' => $moyasarPayment['id'],
                    'message' => 'الدفع قيد المعالجة'
                ]);
            }

        } catch (Exception $e) {
            \Log::error('Template purchase failed', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'template_id' => $template->id
            ]);

            $errorMessage = $e->getMessage();

            // Check if it's a validation error (contains Arabic text about card details)
            $isValidationError = str_contains($errorMessage, 'رقم البطاقة') ||
                               str_contains($errorMessage, 'رمز الأمان') ||
                               str_contains($errorMessage, 'تاريخ انتهاء');

            if (request()->header('X-Inertia')) {
                if ($isValidationError) {
                    // Return validation errors to show on the form
                    return back()->withErrors([
                        'payment' => $errorMessage
                    ])->withInput();
                } else {
                    return redirect()->route('client.templates.purchase', $template)
                        ->with('error', 'حدث خطأ في معالجة الدفع، يرجى المحاولة مرة أخرى');
                }
            }

            return response()->json([
                'error' => $errorMessage
            ], $isValidationError ? 422 : 500);
        }
    }

    /**
     * Handle payment callback from Moyasar.
     */
    public function callback(Request $request)
    {
        $paymentId = $request->get('id');

        if (!$paymentId) {
            return $this->renderPaymentResult(false, 'معرف الدفع مفقود', route('client.templates.index'));
        }

        try {
            // Get purchase by payment ID
            $purchase = $this->templatePurchaseService->getPurchaseByPaymentId($paymentId);

            if (!$purchase) {
                return $this->renderPaymentResult(false, 'عملية الشراء غير موجودة', route('client.templates.index'));
            }

            // Get payment status from Moyasar using the service method
            $moyasarPayment = $this->templatePurchaseService->getPaymentStatus($paymentId);

            if ($moyasarPayment['status'] === 'paid' || $moyasarPayment['status'] === 'initiated') {
                $this->templatePurchaseService->handlePaymentSuccess($purchase);

                \Log::info('Template purchase callback successful', [
                    'purchase_id' => $purchase->id,
                    'moyasar_status' => $moyasarPayment['status'],
                    'payment_id' => $paymentId
                ]);

                return $this->renderPaymentResult(true, 'تم شراء القالب بنجاح! يمكنك الآن استخدامه بالكامل وحفظ تصميماتك وتحميلها.', route('client.my-designs'));
            } else {
                $errorMessage = $moyasarPayment['source']['message'] ?? 'فشل الدفع';
                $this->templatePurchaseService->handlePaymentFailure($purchase, $errorMessage);

                return $this->renderPaymentResult(false, 'فشل في عملية الدفع: ' . $errorMessage, route('client.templates.purchase', $purchase->template));
            }

        } catch (Exception $e) {
            \Log::error('Template purchase callback failed', [
                'error' => $e->getMessage(),
                'payment_id' => $paymentId
            ]);

            return redirect()->route('client.templates.index')
                ->with('error', 'حدث خطأ في معالجة الدفع');
        }
    }

    /**
     * Show user's template purchases.
     */
    public function index(): Response
    {
        $user = Auth::user();
        $purchases = $this->templatePurchaseService->getUserPurchases($user);

        return Inertia::render('Client/TemplatePurchases', [
            'purchases' => $purchases,
        ]);
    }

    /**
     * Create payment and redirect directly to Moyasar payment page
     */
    public function payWithMoyasar(Template $template)
    {
        try {
            $user = auth()->user();

            // Check if user already has access to this template
            if ($this->templatePurchaseService->canUserAccessTemplate($user, $template)) {
                return redirect()->route('client.templates.create', $template)
                    ->with('success', 'يمكنك استخدام هذا القالب');
            }

            // Create local payment record first (like subscription)
            $payment = Payment::create([
                'user_id' => $user->id,
                'subscription_id' => null, // No subscription for template purchase
                'payment_gateway' => 'moyasar',
                'payment_gateway_id' => 'temp_' . time(), // Temporary ID
                'amount' => $template->price,
                'currency' => 'SAR',
                'status' => Payment::STATUS_PENDING,
                'payment_method' => 'moyasar_invoice',
                'metadata' => json_encode([
                    'template_id' => $template->id,
                    'purchase_type' => 'template'
                ])
            ]);

            // Check if user already purchased this template
            $existingPurchase = TemplatePurchase::where('user_id', $user->id)
                ->where('template_id', $template->id)
                ->first();

            if ($existingPurchase && $existingPurchase->status === TemplatePurchase::STATUS_PAID) {
                return redirect()->route('client.templates')
                    ->with('error', 'لقد قمت بشراء هذا القالب مسبقاً');
            }

            // If there's a pending purchase, use it, otherwise create new one
            if ($existingPurchase && $existingPurchase->status === TemplatePurchase::STATUS_PENDING) {
                $purchase = $existingPurchase;
                $purchase->update(['payment_id' => $payment->id]);
            } else {
                // Create template purchase record
                $purchase = TemplatePurchase::create([
                    'user_id' => $user->id,
                    'template_id' => $template->id,
                    'payment_id' => $payment->id,
                    'amount' => $template->price,
                    'currency' => 'SAR',
                    'status' => TemplatePurchase::STATUS_PENDING,
                ]);
            }

            // Create invoice via Moyasar API (hosted payment)
            $invoiceData = [
                'amount' => $template->price * 100, // Convert to halalas
                'currency' => 'SAR',
                'description' => 'شراء قالب: ' . $template->name,
                'callback_url' => route('client.payment.return', ['type' => 'template']),
                'metadata' => [
                    'user_id' => $user->id,
                    'template_id' => $template->id,
                    'user_email' => $user->email,
                    'purchase_type' => 'template',
                ],
            ];

            // Create invoice using InvoiceService (better for hosted payments)
            $invoiceService = app(\App\Services\Moyasar\InvoiceService::class);
            $moyasarInvoice = $invoiceService->create($invoiceData);

            // Update payment record with Moyasar invoice ID
            $payment->update([
                'payment_gateway_id' => $moyasarInvoice['id'],
                'metadata' => json_encode([
                    'template_id' => $template->id,
                    'purchase_type' => 'template',
                    'moyasar_status' => $moyasarInvoice['status'],
                    'moyasar_amount' => $moyasarInvoice['amount'],
                ])
            ]);

            // Also update the purchase record with the Moyasar ID
            $purchase->update([
                'payment_gateway_id' => $moyasarInvoice['id']
            ]);

            \Log::info('Template invoice created, redirecting to Moyasar', [
                'purchase_id' => $purchase->id,
                'moyasar_invoice_id' => $moyasarInvoice['id'],
                'user_id' => $user->id,
                'template_id' => $template->id
            ]);

            // Redirect directly to Moyasar invoice page in Arabic
            $invoiceUrl = $moyasarInvoice['url']; // Moyasar provides a direct URL for invoices

            // Add Arabic language parameter
            $invoiceUrl .= (strpos($invoiceUrl, '?') !== false ? '&' : '?') . 'lang=ar';

            return redirect()->away($invoiceUrl);

        } catch (Exception $e) {
            \Log::error('Moyasar template payment creation failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'template_id' => $template->id
            ]);

            return redirect()->back()
                ->withErrors(['payment' => 'فشل في إنشاء الدفع: ' . $e->getMessage()]);
        }
    }

    /**
     * Show the simple template purchase page (direct to Moyasar)
     */
    public function showSimple(Template $template)
    {
        $user = auth()->user();

        // Check if template is free
        if ($template->isFree()) {
            return redirect()->route('client.templates.create', $template)
                ->with('success', 'هذا القالب مجاني، يمكنك استخدامه مباشرة');
        }

        // Check if user already has access
        if ($this->templatePurchaseService->canUserAccessTemplate($user, $template)) {
            return redirect()->route('client.templates.create', $template)
                ->with('success', 'يمكنك استخدام هذا القالب');
        }

        return Inertia::render('Client/TemplatePurchaseSimple', [
            'template' => [
                'id' => $template->id,
                'name' => $template->name,
                'price' => $template->price,
                'image_url' => $template->thumbnail_url,
                'category' => $template->category,
            ],
        ]);
    }

    /**
     * Render payment result page
     */
    private function renderPaymentResult($success, $message, $redirectUrl)
    {
        return Inertia::render('Client/PaymentResult', [
            'success' => $success,
            'message' => $message,
            'redirectUrl' => $redirectUrl
        ]);
    }
}
