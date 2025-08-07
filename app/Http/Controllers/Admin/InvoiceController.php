<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\User;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices with filtering.
     */
    public function index(Request $request)
    {
        $query = Invoice::with(['user', 'invoiceable']);

        // Filter by invoice number
        if ($request->filled('invoice_number')) {
            $query->where('invoice_number', 'like', '%' . $request->invoice_number . '%');
        }

        // Filter by user email
        if ($request->filled('user_email')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('email', 'like', '%' . $request->user_email . '%');
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type (subscription/template)
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by subscription ID (for subscription invoices)
        if ($request->filled('subscription_id')) {
            $query->where('type', Invoice::TYPE_SUBSCRIPTION)
                  ->whereHasMorph('invoiceable', ['App\Models\Payment'], function ($q) use ($request) {
                      $q->where('subscription_id', $request->subscription_id);
                  });
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
                         ->paginate(15)
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
                $invoiceData['subscription_id'] = $invoice->invoiceable->subscription_id ?? null;
                $invoiceData['subscription_name'] = $invoice->invoiceable->subscription->name ?? null;
            } elseif ($invoice->type === Invoice::TYPE_TEMPLATE && $invoice->invoiceable) {
                $invoiceData['template_id'] = $invoice->invoiceable->template_id ?? null;
                $invoiceData['template_name'] = $invoice->invoiceable->template->name ?? null;
            }

            return $invoiceData;
        });

        // Calculate statistics
        $stats = [
            'total' => Invoice::count(),
            'paid' => Invoice::where('status', Invoice::STATUS_PAID)->count(),
            'pending' => Invoice::where('status', Invoice::STATUS_PENDING)->count(),
            'failed' => Invoice::where('status', Invoice::STATUS_FAILED)->count(),
            'total_amount' => Invoice::where('status', Invoice::STATUS_PAID)->sum('amount'),
            'subscription_invoices' => Invoice::where('type', Invoice::TYPE_SUBSCRIPTION)->count(),
            'template_invoices' => Invoice::where('type', Invoice::TYPE_TEMPLATE)->count(),
        ];

        return Inertia::render('Admin/Invoices/Index', [
            'invoices' => $invoices,
            'stats' => $stats,
            'filters' => $request->only(['invoice_number', 'user_email', 'status', 'type', 'subscription_id', 'date_from', 'date_to']),
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
     * Display the specified invoice.
     */
    public function show(Invoice $invoice)
    {
        $invoice->load(['user', 'invoiceable']);

        $invoiceData = $invoice->toArray();

        // Add Arabic labels
        $invoiceData['type_arabic'] = $invoice->type_arabic;
        $invoiceData['status_arabic'] = $invoice->status_arabic;
        $invoiceData['item_name'] = $invoice->item_name;

        // Add subscription/template specific data
        if ($invoice->type === Invoice::TYPE_SUBSCRIPTION && $invoice->invoiceable) {
            $invoiceData['subscription_id'] = $invoice->invoiceable->subscription_id ?? null;
            $invoiceData['subscription_name'] = $invoice->invoiceable->subscription->name ?? null;
            $invoiceData['subscription_details'] = $invoice->invoiceable->subscription ?? null;
        } elseif ($invoice->type === Invoice::TYPE_TEMPLATE && $invoice->invoiceable) {
            $invoiceData['template_id'] = $invoice->invoiceable->template_id ?? null;
            $invoiceData['template_name'] = $invoice->invoiceable->template->name ?? null;
            $invoiceData['template_details'] = $invoice->invoiceable->template ?? null;
        }

        return Inertia::render('Admin/Invoices/Show', [
            'invoice' => $invoiceData
        ]);
    }
}
