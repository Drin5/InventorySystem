<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $today = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();
        $isCashierDashboard = $user->hasRole('Cashier')
            || ($user->can('process sales')
                && ! $user->can('view reports')
                && ! $user->can('manage products')
                && ! $user->can('manage stock_in'));

        $stats = [
            'total_products' => Product::count(),
            'total_categories' => Category::active()->count(),
            'low_stock' => ProductVariant::lowStock()->count(),
            'out_of_stock' => ProductVariant::outOfStock()->count(),
            'today_sales' => (float) Sale::whereDate('sale_date', $today)->sum('total'),
            'today_orders' => Sale::whereDate('sale_date', $today)->count(),
            'month_sales' => (float) Sale::where('sale_date', '>=', $monthStart)->sum('total'),
            'inventory_value' => (float) ProductVariant::sum(DB::raw('stock_quantity * cost_price')),
        ];

        if ($isCashierDashboard) {
            $cashierSalesToday = Sale::withCount('items')
                ->where('cashier_id', $user->id)
                ->whereDate('sale_date', $today)
                ->latest('sale_date')
                ->get();

            $recentSales = $cashierSalesToday->take(6);
            $paymentBreakdown = $cashierSalesToday
                ->groupBy('payment_method')
                ->map(fn ($sales) => [
                    'count' => $sales->count(),
                    'total' => (float) $sales->sum('total'),
                ]);

            $cashierMetrics = [
                'today_sales' => (float) $cashierSalesToday->sum('total'),
                'today_orders' => $cashierSalesToday->count(),
                'items_rung' => (int) $cashierSalesToday->sum('items_count'),
                'average_sale' => (float) ($cashierSalesToday->count() ? $cashierSalesToday->avg('total') : 0),
            ];

            return view('dashboard', [
                'dashboardMode' => 'cashier',
                'cashierMetrics' => $cashierMetrics,
                'paymentBreakdown' => $paymentBreakdown,
                'recentSales' => $recentSales,
            ]);
        }

        $lowStockItems = ProductVariant::with('product')
            ->where('stock_quantity', '<=', DB::raw('reorder_level'))
            ->orderBy('stock_quantity')
            ->limit(8)
            ->get();

        $recentActivities = StockMovement::with(['variant.product', 'user'])
            ->latest()
            ->limit(8)
            ->get();

        $bestSellers = SaleItem::select('product_variant_id', DB::raw('SUM(quantity) as sold'), DB::raw('SUM(total) as revenue'))
            ->with('variant.product')
            ->groupBy('product_variant_id')
            ->orderByDesc('sold')
            ->limit(5)
            ->get();

        return view('dashboard', [
            'dashboardMode' => 'default',
            'stats' => $stats,
            'lowStockItems' => $lowStockItems,
            'recentActivities' => $recentActivities,
            'bestSellers' => $bestSellers,
        ]);
    }

    public function chartData(Request $request)
    {
        // Sales (last 14 days)
        $days = collect(range(13, 0))->map(fn ($i) => Carbon::today()->subDays($i));
        $salesByDay = Sale::where('sale_date', '>=', Carbon::today()->subDays(13))
            ->selectRaw('DATE(sale_date) as d, SUM(total) as total')
            ->groupBy('d')->pluck('total', 'd');

        // Stock movement (in vs out, last 14 days)
        $movements = StockMovement::where('created_at', '>=', Carbon::today()->subDays(13))
            ->selectRaw('DATE(created_at) as d, direction, SUM(quantity) as qty')
            ->groupBy('d', 'direction')->get();

        $labels = $days->map(fn ($d) => $d->format('M d'));
        $sales = $days->map(fn ($d) => round((float) ($salesByDay[$d->toDateString()] ?? 0), 2));
        $stockIn = $days->map(fn ($d) => (int) $movements->where('d', $d->toDateString())->where('direction', 'in')->sum('qty'));
        $stockOut = $days->map(fn ($d) => (int) $movements->where('d', $d->toDateString())->where('direction', 'out')->sum('qty'));

        return response()->json([
            'labels' => $labels,
            'sales' => $sales,
            'stock_in' => $stockIn,
            'stock_out' => $stockOut,
        ]);
    }
}
