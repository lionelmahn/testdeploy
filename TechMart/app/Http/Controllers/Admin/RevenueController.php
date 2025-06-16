<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RevenueController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(Request $request)
    {
        $period = $request->get('period', 'month'); // day, week, month, year
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Xác định khoảng thời gian
        $dateRange = $this->getDateRange($period, $startDate, $endDate);

        // Thống kê tổng quan
        $overview = $this->getOverviewStats($dateRange['start'], $dateRange['end']);

        // Biểu đồ doanh thu theo thời gian
        $revenueChart = $this->getRevenueChart($period, $dateRange['start'], $dateRange['end']);

        // Top sản phẩm bán chạy
        $topProducts = $this->getTopProducts($dateRange['start'], $dateRange['end']);

        // Doanh thu theo danh mục
        $categoryRevenue = $this->getCategoryRevenue($dateRange['start'], $dateRange['end']);

        // Thống kê đơn hàng theo trạng thái
        $orderStats = $this->getOrderStats($dateRange['start'], $dateRange['end']);

        // Khách hàng mua nhiều nhất
        $topCustomers = $this->getTopCustomers($dateRange['start'], $dateRange['end']);

        return view('admin.revenue.index', compact(
            'overview',
            'revenueChart',
            'topProducts',
            'categoryRevenue',
            'orderStats',
            'topCustomers',
            'period',
            'startDate',
            'endDate'
        ));
    }

    private function getDateRange($period, $startDate = null, $endDate = null)
    {
        if ($startDate && $endDate) {
            return [
                'start' => Carbon::parse($startDate)->startOfDay(),
                'end' => Carbon::parse($endDate)->endOfDay()
            ];
        }

        $now = Carbon::now();

        switch ($period) {
            case 'day':
                return [
                    'start' => $now->copy()->startOfDay(),
                    'end' => $now->copy()->endOfDay()
                ];
            case 'week':
                return [
                    'start' => $now->copy()->startOfWeek(),
                    'end' => $now->copy()->endOfWeek()
                ];
            case 'month':
                return [
                    'start' => $now->copy()->startOfMonth(),
                    'end' => $now->copy()->endOfMonth()
                ];
            case 'year':
                return [
                    'start' => $now->copy()->startOfYear(),
                    'end' => $now->copy()->endOfYear()
                ];
            default:
                return [
                    'start' => $now->copy()->startOfMonth(),
                    'end' => $now->copy()->endOfMonth()
                ];
        }
    }

    private function getOverviewStats($startDate, $endDate)
    {
        $orders = Order::whereBetween('order_date', [$startDate, $endDate]);

        // Doanh thu hiện tại
        $currentRevenue = $orders->clone()
            ->whereIn('status', ['delivered', 'completed'])
            ->sum('total_amount');

        // Tổng đơn hàng
        $totalOrders = $orders->clone()->count();

        // Đơn hàng hoàn thành
        $completedOrders = $orders->clone()
            ->whereIn('status', ['delivered', 'completed'])
            ->count();

        // Giá trị đơn hàng trung bình
        $avgOrderValue = $completedOrders > 0 ? $currentRevenue / $completedOrders : 0;

        // So sánh với kỳ trước
        $previousPeriod = $this->getPreviousPeriod($startDate, $endDate);
        $previousRevenue = Order::whereBetween('order_date', [$previousPeriod['start'], $previousPeriod['end']])
            ->whereIn('status', ['delivered', 'completed'])
            ->sum('total_amount');

        $revenueGrowth = $previousRevenue > 0 
            ? (($currentRevenue - $previousRevenue) / $previousRevenue) * 100 
            : 0;

        return [
            'current_revenue' => $currentRevenue,
            'total_orders' => $totalOrders,
            'completed_orders' => $completedOrders,
            'avg_order_value' => $avgOrderValue,
            'revenue_growth' => $revenueGrowth,
            'completion_rate' => $totalOrders > 0 ? ($completedOrders / $totalOrders) * 100 : 0
        ];
    }

    private function getPreviousPeriod($startDate, $endDate)
    {
        $duration = $startDate->diffInDays($endDate);
        
        return [
            'start' => $startDate->copy()->subDays($duration + 1),
            'end' => $startDate->copy()->subDay()
        ];
    }

    private function getRevenueChart($period, $startDate, $endDate)
    {
        $format = $this->getDateFormat($period);
        $groupBy = $this->getGroupByFormat($period);

        $data = Order::select(
                DB::raw("DATE_FORMAT(order_date, '$format') as period"),
                DB::raw('SUM(total_amount) as revenue'),
                DB::raw('COUNT(*) as orders')
            )
            ->whereBetween('order_date', [$startDate, $endDate])
            ->whereIn('status', ['delivered', 'completed'])
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        // Tạo labels và data cho chart
        $labels = [];
        $revenues = [];
        $orderCounts = [];

        // Tạo tất cả các period trong khoảng thời gian
        $current = $startDate->copy();
        while ($current <= $endDate) {
            $periodLabel = $current->format($this->getDisplayFormat($period));
            $labels[] = $periodLabel;
            
            // Tìm dữ liệu cho period này
            $periodData = $data->firstWhere('period', $current->format($this->getDbFormat($period)));
            $revenues[] = $periodData ? (float)$periodData->revenue : 0;
            $orderCounts[] = $periodData ? (int)$periodData->orders : 0;

            // Tăng current theo period
            $current = $this->incrementPeriod($current, $period);
        }

        return [
            'labels' => $labels,
            'revenues' => $revenues,
            'orders' => $orderCounts
        ];
    }

    private function getDateFormat($period)
    {
        switch ($period) {
            case 'day':
                return '%Y-%m-%d %H:00:00';
            case 'week':
            case 'month':
                return '%Y-%m-%d';
            case 'year':
                return '%Y-%m';
            default:
                return '%Y-%m-%d';
        }
    }

    private function getGroupByFormat($period)
    {
        switch ($period) {
            case 'day':
                return 'HOUR(order_date)';
            case 'week':
            case 'month':
                return 'DATE(order_date)';
            case 'year':
                return 'YEAR(order_date), MONTH(order_date)';
            default:
                return 'DATE(order_date)';
        }
    }

    private function getDisplayFormat($period)
    {
        switch ($period) {
            case 'day':
                return 'H:00';
            case 'week':
            case 'month':
                return 'd/m';
            case 'year':
                return 'm/Y';
            default:
                return 'd/m';
        }
    }

    private function getDbFormat($period)
    {
        switch ($period) {
            case 'day':
                return 'Y-m-d H:00:00';
            case 'week':
            case 'month':
                return 'Y-m-d';
            case 'year':
                return 'Y-m';
            default:
                return 'Y-m-d';
        }
    }

    private function incrementPeriod($date, $period)
    {
        switch ($period) {
            case 'day':
                return $date->addHour();
            case 'week':
            case 'month':
                return $date->addDay();
            case 'year':
                return $date->addMonth();
            default:
                return $date->addDay();
        }
    }

    private function getTopProducts($startDate, $endDate, $limit = 10)
    {
        return OrderItem::select(
                'products.name',
                'products.image_url',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.total) as total_revenue')
            )
            ->join('orders', 'order_items.order_id', '=', 'orders.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.product_id')
            ->whereBetween('orders.order_date', [$startDate, $endDate])
            ->whereIn('orders.status', ['delivered', 'completed'])
            ->groupBy('order_items.product_id', 'products.name', 'products.image_url')
            ->orderBy('total_revenue', 'desc')
            ->limit($limit)
            ->get();
    }

    private function getCategoryRevenue($startDate, $endDate)
    {
        return OrderItem::select(
                'categories.category_name',
                DB::raw('SUM(order_items.total) as total_revenue'),
                DB::raw('SUM(order_items.quantity) as total_quantity')
            )
            ->join('orders', 'order_items.order_id', '=', 'orders.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.product_id')
            ->join('categories', 'products.category_id', '=', 'categories.category_id')
            ->whereBetween('orders.order_date', [$startDate, $endDate])
            ->whereIn('orders.status', ['delivered', 'completed'])
            ->groupBy('categories.category_id', 'categories.category_name')
            ->orderBy('total_revenue', 'desc')
            ->get();
    }

    private function getOrderStats($startDate, $endDate)
    {
        return Order::select(
                'status',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total_amount) as total_amount')
            )
            ->whereBetween('order_date', [$startDate, $endDate])
            ->groupBy('status')
            ->get()
            ->keyBy('status');
    }

    private function getTopCustomers($startDate, $endDate, $limit = 10)
    {
        return Order::select(
                'users.name',
                'users.email',
                DB::raw('COUNT(orders.order_id) as total_orders'),
                DB::raw('SUM(orders.total_amount) as total_spent')
            )
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->whereBetween('orders.order_date', [$startDate, $endDate])
            ->whereIn('orders.status', ['delivered', 'completed'])
            ->groupBy('orders.user_id', 'users.name', 'users.email')
            ->orderBy('total_spent', 'desc')
            ->limit($limit)
            ->get();
    }

    public function export(Request $request)
    {
        $period = $request->get('period', 'month');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $dateRange = $this->getDateRange($period, $startDate, $endDate);

        // Lấy dữ liệu chi tiết
        $orders = Order::with(['user', 'orderItems.product'])
            ->whereBetween('order_date', [$dateRange['start'], $dateRange['end']])
            ->whereIn('status', ['delivered', 'completed'])
            ->orderBy('order_date', 'desc')
            ->get();

        $filename = 'revenue_report_' . $dateRange['start']->format('Y-m-d') . '_to_' . $dateRange['end']->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, [
                'Mã đơn hàng',
                'Ngày đặt',
                'Khách hàng',
                'Email',
                'Tổng tiền',
                'Trạng thái',
                'Phương thức thanh toán'
            ]);

            // Data
            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_number ?? '#' . $order->order_id,
                    $order->order_date->format('d/m/Y H:i'),
                    $order->user->name ?? 'N/A',
                    $order->user->email ?? 'N/A',
                    number_format($order->total_amount, 0, ',', '.') . '₫',
                    $order->status_label,
                    $order->payment_method_label
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
