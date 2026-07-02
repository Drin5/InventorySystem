<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\PurchaseOrder;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportController extends Controller
{
    public const TYPES = [
        'sales' => 'Sales Report',
        'inventory' => 'Inventory Report',
        'stock-in' => 'Stock In Report',
        'stock-out' => 'Stock Out Report',
        'purchase-orders' => 'Purchase Orders Report',
        'profit' => 'Profit Report',
        'low-stock' => 'Low Stock Report',
        'dead-stock' => 'Dead Stock Report',
        'fast-moving' => 'Fast Moving Products',
    ];

    public function index()
    {
        return view('reports.index', ['types' => self::TYPES]);
    }

    public function show(Request $request, string $type)
    {
        abort_unless(isset(self::TYPES[$type]), 404);

        [$from, $to] = $this->range($request);
        $report = $this->build($type, $from, $to);

        return view('reports.show', [
            'type' => $type,
            'title' => self::TYPES[$type],
            'report' => $report,
            'from' => $from,
            'to' => $to,
            'types' => self::TYPES,
        ]);
    }

    public function export(Request $request, string $type, string $format)
    {
        abort_unless(isset(self::TYPES[$type]), 404);
        [$from, $to] = $this->range($request);
        $report = $this->build($type, $from, $to);
        $filename = $type.'-report-'.now()->format('Ymd_His');

        if ($format === 'csv' || $format === 'excel') {
            $ext = $format === 'csv' ? 'csv' : 'xlsx';

            return response()->streamDownload(function () use ($report, $ext): void {
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->fromArray([$report['headers'], ...$report['rows']], null, 'A1', true);

                if ($ext === 'csv') {
                    $writer = new Csv($spreadsheet);
                    $writer->setUseBOM(true);
                    $writer->setDelimiter(',');
                } else {
                    $writer = new Xlsx($spreadsheet);
                }

                $writer->save('php://output');
                $spreadsheet->disconnectWorksheets();
                unset($spreadsheet);
            }, "$filename.$ext", [
                'Content-Type' => $ext === 'csv'
                    ? 'text/csv; charset=UTF-8'
                    : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        }

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('reports.pdf', [
                'title' => self::TYPES[$type],
                'report' => $report,
                'from' => $from,
                'to' => $to,
            ])->setPaper('a4', 'landscape');

            return $pdf->download("$filename.pdf");
        }

        abort(404);
    }

    private function range(Request $request): array
    {
        $from = $request->filled('from') ? Carbon::parse($request->from)->startOfDay() : Carbon::now()->startOfMonth();
        $to = $request->filled('to') ? Carbon::parse($request->to)->endOfDay() : Carbon::now()->endOfDay();

        return [$from, $to];
    }

    private function build(string $type, Carbon $from, Carbon $to): array
    {
        return match ($type) {
            'sales' => $this->salesReport($from, $to),
            'inventory' => $this->inventoryReport(),
            'stock-in' => $this->movementReport('in', $from, $to),
            'stock-out' => $this->movementReport('out', $from, $to),
            'purchase-orders' => $this->purchaseOrderReport($from, $to),
            'profit' => $this->profitReport($from, $to),
            'low-stock' => $this->lowStockReport(),
            'dead-stock' => $this->deadStockReport(),
            'fast-moving' => $this->fastMovingReport($from, $to),
        };
    }

    private function wrap(array $headers, $rows, array $summary = []): array
    {
        return ['headers' => $headers, 'rows' => collect($rows)->values()->all(), 'summary' => $summary];
    }

    private function salesReport($from, $to): array
    {
        $sales = Sale::with('customer', 'cashier')->whereBetween('sale_date', [$from, $to])->latest('sale_date')->get();
        $rows = $sales->map(fn ($s) => [
            $s->invoice_number,
            $s->sale_date->format('Y-m-d H:i'),
            $s->customer?->name ?? 'Walk-in',
            $s->cashier?->name ?? '—',
            ucfirst($s->payment_method),
            number_format($s->total, 2),
        ]);

        return $this->wrap(
            ['Invoice', 'Date', 'Customer', 'Cashier', 'Payment', 'Total'],
            $rows,
            ['Total Sales' => number_format($sales->sum('total'), 2), 'Transactions' => $sales->count()]
        );
    }

    private function inventoryReport(): array
    {
        $variants = ProductVariant::with('product')->get();
        $rows = $variants->map(fn ($v) => [
            $v->product->name,
            $v->name,
            $v->sku,
            $v->stock_quantity,
            number_format($v->cost_price, 2),
            number_format($v->stock_quantity * $v->cost_price, 2),
        ]);

        return $this->wrap(
            ['Product', 'Variant', 'SKU', 'Stock', 'Cost', 'Stock Value'],
            $rows,
            ['Inventory Value' => number_format($variants->sum(fn ($v) => $v->stock_quantity * $v->cost_price), 2)]
        );
    }

    private function movementReport(string $direction, $from, $to): array
    {
        $moves = StockMovement::with('variant.product', 'user')
            ->where('direction', $direction)
            ->whereBetween('created_at', [$from, $to])->latest()->get();
        $rows = $moves->map(fn ($m) => [
            $m->created_at->format('Y-m-d H:i'),
            $m->variant?->display_name ?? '—',
            ucfirst(str_replace('_', ' ', $m->type)),
            $m->quantity,
            $m->user?->name ?? '—',
            $m->note,
        ]);

        return $this->wrap(
            ['Date', 'Product', 'Type', 'Qty', 'User', 'Note'],
            $rows,
            ['Total Quantity' => $moves->sum('quantity')]
        );
    }

    private function purchaseOrderReport($from, $to): array
    {
        $pos = PurchaseOrder::with('supplier')->whereBetween('order_date', [$from, $to])->latest()->get();
        $rows = $pos->map(fn ($p) => [
            $p->po_number,
            $p->order_date->format('Y-m-d'),
            $p->supplier?->name ?? '—',
            PurchaseOrder::STATUSES[$p->status] ?? $p->status,
            number_format($p->total, 2),
        ]);

        return $this->wrap(
            ['PO Number', 'Date', 'Supplier', 'Status', 'Total'],
            $rows,
            ['Total Value' => number_format($pos->sum('total'), 2)]
        );
    }

    private function profitReport($from, $to): array
    {
        $items = SaleItem::with('variant.product', 'sale')
            ->whereHas('sale', fn ($q) => $q->whereBetween('sale_date', [$from, $to]))->get();
        $grouped = $items->groupBy('product_variant_id')->map(function ($group) {
            $qty = $group->sum('quantity');
            $revenue = $group->sum('total');
            $cost = $group->sum(fn ($i) => $i->unit_cost * $i->quantity);

            return [
                'name' => $group->first()->variant?->display_name ?? '—',
                'qty' => $qty,
                'revenue' => $revenue,
                'cost' => $cost,
                'profit' => $revenue - $cost,
            ];
        })->sortByDesc('profit');

        $rows = $grouped->map(fn ($r) => [
            $r['name'], $r['qty'], number_format($r['revenue'], 2),
            number_format($r['cost'], 2), number_format($r['profit'], 2),
        ]);

        return $this->wrap(
            ['Product', 'Units Sold', 'Revenue', 'Cost', 'Profit'],
            $rows,
            ['Total Profit' => number_format($grouped->sum('profit'), 2), 'Total Revenue' => number_format($grouped->sum('revenue'), 2)]
        );
    }

    private function lowStockReport(): array
    {
        $variants = ProductVariant::with('product')
            ->whereColumn('stock_quantity', '<=', 'reorder_level')->orderBy('stock_quantity')->get();
        $rows = $variants->map(fn ($v) => [
            $v->product->name, $v->name, $v->sku, $v->stock_quantity, $v->reorder_level,
            $v->stock_quantity <= 0 ? 'Out of Stock' : 'Low',
        ]);

        return $this->wrap(['Product', 'Variant', 'SKU', 'Stock', 'Reorder Level', 'Status'], $rows,
            ['Items needing restock' => $variants->count()]);
    }

    private function deadStockReport(): array
    {
        $soldIds = SaleItem::whereHas('sale', fn ($q) => $q->where('sale_date', '>=', now()->subDays(90)))
            ->pluck('product_variant_id')->unique();
        $variants = ProductVariant::with('product')->whereNotIn('id', $soldIds)
            ->where('stock_quantity', '>', 0)->get();
        $rows = $variants->map(fn ($v) => [
            $v->product->name, $v->name, $v->sku, $v->stock_quantity,
            number_format($v->stock_quantity * $v->cost_price, 2),
        ]);

        return $this->wrap(['Product', 'Variant', 'SKU', 'Stock', 'Tied-up Value'], $rows,
            ['Dead stock items (no sales in 90 days)' => $variants->count()]);
    }

    private function fastMovingReport($from, $to): array
    {
        $items = SaleItem::with('variant.product')
            ->whereHas('sale', fn ($q) => $q->whereBetween('sale_date', [$from, $to]))
            ->select('product_variant_id', DB::raw('SUM(quantity) as sold'), DB::raw('SUM(total) as revenue'))
            ->groupBy('product_variant_id')->orderByDesc('sold')->limit(50)->get();
        $rows = $items->map(fn ($i) => [
            $i->variant?->display_name ?? '—', $i->sold, number_format($i->revenue, 2),
        ]);

        return $this->wrap(['Product', 'Units Sold', 'Revenue'], $rows,
            ['Total units sold' => $items->sum('sold')]);
    }
}
