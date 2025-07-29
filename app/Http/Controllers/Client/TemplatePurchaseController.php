<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Template;
use App\Models\TemplatePurchase;
use App\Services\TemplatePurchaseService;
use App\Services\MoyasarService;
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

        return Inertia::render('Client/TemplatePurchase', [
            'template' => [
                'id' => $template->id,
                'name' => $template->name,
                'price' => $template->price,
                'thumbnail_url' => $template->thumbnail_url,
                'category' => $template->category->name ?? null,
            ],
            'paymentConfig' => app(MoyasarService::class)->createTemplatePurchasePaymentConfig($user, $template),
        ]);
    }

    /**
     * Save payment ID after Moyasar form completion (called from frontend)
     */
    public function savePaymentId(Request $request, Template $template)
    {
        $request->validate([
            'payment_id' => 'required|string',
        ]);

        $user = Auth::user();

        try {
            $purchase = app(MoyasarService::class)->savePaymentIdForTemplate(
                $user, 
                $template, 
                $request->payment_id
            );

            return response()->json([
                'success' => true,
                'purchase_id' => $purchase->id,
                'message' => 'تم حفظ معرف الدفع بنجاح'
            ]);
        } catch (Exception $e) {
            \Log::error('Failed to save payment ID for template', [
                'error' => $e->getMessage(),
                'payment_id' => $request->payment_id,
                'user_id' => $user->id,
                'template_id' => $template->id
            ]);

            return response()->json([
                'error' => 'فشل في حفظ معرف الدفع'
            ], 500);
        }
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
            if ($moyasarPayment['status'] === 'paid' || $moyasarPayment['status'] === 'initiated') {
                $purchase = $this->templatePurchaseService->getPurchaseByPaymentId($moyasarPayment['id']);
                $this->templatePurchaseService->handlePaymentSuccess($purchase);

                \Log::info('Template purchase successful', [
                    'purchase_id' => $purchase->id,
                    'moyasar_status' => $moyasarPayment['status'],
                    'user_id' => $purchase->user_id,
                    'template_id' => $purchase->template_id
                ]);

                // Check if it's an Inertia request
                if (request()->header('X-Inertia')) {
                    return redirect()->route('client.my-designs')
                        ->with('success', 'تم شراء القالب بنجاح! يمكنك الآن استخدامه بالكامل وحفظ تصميماتك وتحميلها.');
                }

                return response()->json([
                    'success' => true,
                    'message' => 'تم شراء القالب بنجاح',
                    'redirect' => route('client.my-designs')
                ]);
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
            return redirect()->route('client.templates.index')
                ->with('error', 'معرف الدفع مفقود');
        }

        try {
            // Get purchase by payment ID
            $purchase = $this->templatePurchaseService->getPurchaseByPaymentId($paymentId);

            if (!$purchase) {
                return redirect()->route('client.templates.index')
                    ->with('error', 'عملية الشراء غير موجودة');
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

                return redirect()->route('client.my-designs')
                    ->with('success', 'تم شراء القالب بنجاح! يمكنك الآن استخدامه بالكامل وحفظ تصميماتك وتحميلها.');
            } else {
                $errorMessage = $moyasarPayment['source']['message'] ?? 'فشل الدفع';
                $this->templatePurchaseService->handlePaymentFailure($purchase, $errorMessage);

                return redirect()->route('client.templates.purchase', $purchase->template)
                    ->with('error', 'فشل في عملية الدفع: ' . $errorMessage);
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
}
