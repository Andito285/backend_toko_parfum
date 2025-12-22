<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Perfume;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalPerfumes = Perfume::count();
        $totalUsers = User::count();
        $lowStockCount = Perfume::where('stock', '<=', 5)->count();
        
        // Calculate today's sales
        $todaySales = Order::whereDate('created_at', today())->sum('total_amount');
        $totalOrders = Order::count();

        return response()->json([
            'totalPerfumes' => $totalPerfumes,
            'totalUsers' => $totalUsers,
            'totalSales' => $todaySales,
            'lowStockCount' => $lowStockCount,
            'totalOrders' => $totalOrders,
        ]);
    }

    /**
     * Get detailed reports
     */
    public function reports(Request $request)
    {
        // Daily sales for last 7 days
        $dailySales = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $sales = Order::whereDate('created_at', $date)->sum('total_amount');
            $orders = Order::whereDate('created_at', $date)->count();
            $dailySales->push([
                'date' => $date->format('Y-m-d'),
                'day' => $date->format('D'),
                'sales' => $sales,
                'orders' => $orders,
            ]);
        }

        // Monthly sales for last 6 months
        $monthlySales = collect();
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::today()->subMonths($i);
            $sales = Order::whereYear('created_at', $month->year)
                         ->whereMonth('created_at', $month->month)
                         ->sum('total_amount');
            $orders = Order::whereYear('created_at', $month->year)
                          ->whereMonth('created_at', $month->month)
                          ->count();
            $monthlySales->push([
                'month' => $month->format('M Y'),
                'sales' => $sales,
                'orders' => $orders,
            ]);
        }

        // Top selling perfumes
        $topPerfumes = Perfume::withCount(['images'])
            ->orderBy('stock', 'asc')
            ->take(5)
            ->get();

        // Summary stats
        $totalRevenue = Order::sum('total_amount');
        $totalOrders = Order::count();
        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
        $thisMonthRevenue = Order::whereMonth('created_at', now()->month)
                                 ->whereYear('created_at', now()->year)
                                 ->sum('total_amount');

        return response()->json([
            'dailySales' => $dailySales,
            'monthlySales' => $monthlySales,
            'topPerfumes' => $topPerfumes,
            'summary' => [
                'totalRevenue' => $totalRevenue,
                'totalOrders' => $totalOrders,
                'avgOrderValue' => round($avgOrderValue, 2),
                'thisMonthRevenue' => $thisMonthRevenue,
            ]
        ]);
    }

    /**
     * Get all orders for admin
     */
    public function orders(Request $request)
    {
        $perPage = $request->get('per_page', 15);

        $orders = Order::with(['user', 'items.perfume'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json($orders);
    }
}