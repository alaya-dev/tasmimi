<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the user's invoices.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Invoice::where('user_id', $user->id)->with(['invoiceable']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type (subscription/template)
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('invoice_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('invoice_date', '<=', $request->date_to);
        }

        // Order by latest first
        $invoices = $query->orderBy('created_at', 'desc')
                         ->paginate(10)
                         ->withQueryString();

        // Transform the data to include additional information
        $invoices->getCollection()->transform(function ($invoice) {
            $invoiceData = $invoice->toArray();

            // Add Arabic labels
            $invoiceData['type_arabic'] = $invoice->type_arabic;
            $invoiceData['status_arabic'] = $invoice->status_arabic;
            $invoiceData['item_name'] = $invoice->item_name;

            // Add subscription/template specific data
            if ($invoice->type === Invoice::TYPE_SUBSCRIPTION && $invoice->invoiceable) {
                $invoiceData['subscription_name'] = $invoice->invoiceable->subscription->name ?? null;
            } elseif ($invoice->type === Invoice::TYPE_TEMPLATE && $invoice->invoiceable) {
                $invoiceData['template_name'] = $invoice->invoiceable->template->name ?? null;
            }

            return $invoiceData;
        });

        // Calculate user statistics
        $stats = [
            'total' => Invoice::where('user_id', $user->id)->count(),
            'paid' => Invoice::where('user_id', $user->id)->where('status', Invoice::STATUS_PAID)->count(),
            'pending' => Invoice::where('user_id', $user->id)->where('status', Invoice::STATUS_PENDING)->count(),
            'total_amount' => Invoice::where('user_id', $user->id)->where('status', Invoice::STATUS_PAID)->sum('amount'),
        ];

        return Inertia::render('Client/Invoices/Index', [
            'invoices' => $invoices,
            'stats' => $stats,
            'filters' => $request->only(['status', 'type', 'date_from', 'date_to']),
            'statusOptions' => [
                Invoice::STATUS_PENDING => 'في الانتظار',
                Invoice::STATUS_PAID => 'مدفوع',
                Invoice::STATUS_FAILED => 'فشل',
                Invoice::STATUS_CANCELED => 'ملغي',
                Invoice::STATUS_REFUNDED => 'مسترد',
            ],
            'typeOptions' => [
                Invoice::TYPE_SUBSCRIPTION => 'اشتراك',
                Invoice::TYPE_TEMPLATE => 'قالب',
            ]
        ]);
    }

    /**
     * Display the specified invoice (only if it belongs to the authenticated user).
     */
    public function show(Invoice $invoice)
    {
        $user = Auth::user();

        // Ensure the invoice belongs to the authenticated user
        if ($invoice->user_id !== $user->id) {
            abort(403, 'غير مسموح لك بعرض هذه الفاتورة');
        }

        $invoice->load(['invoiceable']);

        $invoiceData = $invoice->toArray();

        // Add Arabic labels
        $invoiceData['type_arabic'] = $invoice->type_arabic;
        $invoiceData['status_arabic'] = $invoice->status_arabic;
        $invoiceData['item_name'] = $invoice->item_name;

        // Add subscription/template specific data
        if ($invoice->type === Invoice::TYPE_SUBSCRIPTION && $invoice->invoiceable) {
            $invoiceData['subscription_name'] = $invoice->invoiceable->subscription->name ?? null;
            $invoiceData['subscription_details'] = $invoice->invoiceable->subscription ?? null;
        } elseif ($invoice->type === Invoice::TYPE_TEMPLATE && $invoice->invoiceable) {
            $invoiceData['template_name'] = $invoice->invoiceable->template->name ?? null;
            $invoiceData['template_details'] = $invoice->invoiceable->template ?? null;
        }

        return Inertia::render('Client/Invoices/Show', [
            'invoice' => $invoiceData
        ]);
    }
}
