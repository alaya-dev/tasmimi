<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TemplatePurchase;
use App\Models\Template;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Carbon\Carbon;

class TemplateSalesController extends Controller
{
    /**
     * Display template sales statistics and list.
     */
    public function index(Request $request): Response
    {
        $query = TemplatePurchase::with(['user', 'template.category'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search by template name or user email
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->whereHas('template', function($templateQuery) use ($request) {
                    $templateQuery->where('name', 'like', '%' . $request->search . '%');
                })->orWhereHas('user', function($userQuery) use ($request) {
                    $userQuery->where('email', 'like', '%' . $request->search . '%');
                });
            });
        }

        $purchases = $query->paginate(15);

        // Calculate statistics
        $stats = $this->calculateSalesStatistics($request);

        // Get top selling templates
        $topTemplates = $this->getTopSellingTemplates();

        return Inertia::render('Admin/TemplateSales/Index', [
            'purchases' => $purchases,
            'stats' => $stats,
            'topTemplates' => $topTemplates,
            'filters' => $request->only(['status', 'date_from', 'date_to', 'search']),
            'statuses' => TemplatePurchase::getStatuses(),
        ]);
    }

    /**
     * Calculate sales statistics.
     */
    private function calculateSalesStatistics(Request $request): array
    {
        $query = TemplatePurchase::query();

        // Apply same filters as main query
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Total sales
        $totalSales = $query->where('status', TemplatePurchase::STATUS_PAID)->sum('amount');
        $totalPurchases = $query->where('status', TemplatePurchase::STATUS_PAID)->count();
        $pendingPurchases = $query->where('status', TemplatePurchase::STATUS_PENDING)->count();
        $failedPurchases = $query->where('status', TemplatePurchase::STATUS_FAILED)->count();

        // This month statistics
        $thisMonth = TemplatePurchase::where('status', TemplatePurchase::STATUS_PAID)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year);

        $thisMonthSales = $thisMonth->sum('amount');
        $thisMonthPurchases = $thisMonth->count();

        // Last month statistics for comparison
        $lastMonth = TemplatePurchase::where('status', TemplatePurchase::STATUS_PAID)
            ->whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year);

        $lastMonthSales = $lastMonth->sum('amount');
        $lastMonthPurchases = $lastMonth->count();

        // Calculate growth percentages
        $salesGrowth = $lastMonthSales > 0 ? (($thisMonthSales - $lastMonthSales) / $lastMonthSales) * 100 : 0;
        $purchasesGrowth = $lastMonthPurchases > 0 ? (($thisMonthPurchases - $lastMonthPurchases) / $lastMonthPurchases) * 100 : 0;

        return [
            'total_sales' => $totalSales,
            'total_purchases' => $totalPurchases,
            'pending_purchases' => $pendingPurchases,
            'failed_purchases' => $failedPurchases,
            'this_month_sales' => $thisMonthSales,
            'this_month_purchases' => $thisMonthPurchases,
            'sales_growth' => round($salesGrowth, 1),
            'purchases_growth' => round($purchasesGrowth, 1),
        ];
    }

    /**
     * Get top selling templates.
     */
    private function getTopSellingTemplates(int $limit = 5): array
    {
        return TemplatePurchase::select('template_id')
            ->selectRaw('COUNT(*) as sales_count')
            ->selectRaw('SUM(amount) as total_revenue')
            ->where('status', TemplatePurchase::STATUS_PAID)
            ->with('template:id,name,price,thumbnail')
            ->groupBy('template_id')
            ->orderBy('sales_count', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($purchase) {
                return [
                    'template' => $purchase->template,
                    'sales_count' => $purchase->sales_count,
                    'total_revenue' => $purchase->total_revenue,
                ];
            })
            ->toArray();
    }

    /**
     * Show detailed sales report for a specific template.
     */
    public function show(Template $template): Response
    {
        $purchases = TemplatePurchase::where('template_id', $template->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total_sales' => TemplatePurchase::where('template_id', $template->id)
                ->where('status', TemplatePurchase::STATUS_PAID)
                ->sum('amount'),
            'total_purchases' => TemplatePurchase::where('template_id', $template->id)
                ->where('status', TemplatePurchase::STATUS_PAID)
                ->count(),
            'pending_purchases' => TemplatePurchase::where('template_id', $template->id)
                ->where('status', TemplatePurchase::STATUS_PENDING)
                ->count(),
            'failed_purchases' => TemplatePurchase::where('template_id', $template->id)
                ->where('status', TemplatePurchase::STATUS_FAILED)
                ->count(),
        ];

        return Inertia::render('Admin/TemplateSales/Show', [
            'template' => $template->load('category'),
            'purchases' => $purchases,
            'stats' => $stats,
        ]);
    }

    /**
     * Export sales data to CSV.
     */
    public function export(Request $request)
    {
        $query = TemplatePurchase::with(['user', 'template'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $purchases = $query->get();

        $csvData = "ID,اسم القالب,البريد الإلكتروني,المبلغ,العملة,الحالة,تاريخ الشراء,تاريخ الدفع\n";

        foreach ($purchases as $purchase) {
            $csvData .= sprintf(
                "%d,%s,%s,%.2f,%s,%s,%s,%s\n",
                $purchase->id,
                $purchase->template->name ?? 'غير محدد',
                $purchase->user->email ?? 'غير محدد',
                $purchase->amount,
                $purchase->currency,
                TemplatePurchase::getStatuses()[$purchase->status] ?? $purchase->status,
                $purchase->created_at->format('Y-m-d H:i:s'),
                $purchase->paid_at ? $purchase->paid_at->format('Y-m-d H:i:s') : 'غير مدفوع'
            );
        }

        return response($csvData)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="template-sales-' . date('Y-m-d') . '.csv"');
    }
}
