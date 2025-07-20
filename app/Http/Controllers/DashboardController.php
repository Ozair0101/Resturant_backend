<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function statistics()
    {
        try {
            // Get counts
            $totalOrders = Order::count();
            $totalCustomers = Customer::count();
            $totalMenuItems = MenuItem::count();
            
            // Calculate total revenue from order details
            $totalRevenue = Order::with('orderDetails')
                ->get()
                ->sum(function ($order) {
                    return $order->orderDetails->sum(function ($detail) {
                        return ($detail->quantity * $detail->item_price) ?? 0;
                    });
                });

            // Get category distribution
            $categoryDistribution = MenuItem::select('category', DB::raw('count(*) as count'))
                ->groupBy('category')
                ->get()
                ->map(function ($item) {
                    return [
                        'name' => $item->category,
                        'value' => $item->count
                    ];
                });

            // Get sales by channel (using order status as channel for demo)
            $salesByChannel = Order::select('order_status', DB::raw('count(*) as count'))
                ->groupBy('order_status')
                ->get()
                ->map(function ($item) {
                    return [
                        'name' => ucfirst($item->order_status),
                        'value' => $item->count
                    ];
                });

            // Get recent orders for sales overview
            $recentOrders = Order::with(['customer', 'orderDetails'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($order) {
                    // Calculate total amount from order details
                    $totalAmount = $order->orderDetails->sum(function ($detail) {
                        return ($detail->quantity * $detail->item_price) ?? 0;
                    });
                    
                    return [
                        'date' => $order->created_at->format('M d'),
                        'amount' => $totalAmount,
                        'orders' => 1
                    ];
                });

            return response()->json([
                'statistics' => [
                    'totalOrders' => $totalOrders,
                    'totalCustomers' => $totalCustomers,
                    'totalMenuItems' => $totalMenuItems,
                    'totalRevenue' => $totalRevenue
                ],
                'categoryDistribution' => $categoryDistribution,
                'salesByChannel' => $salesByChannel,
                'recentOrders' => $recentOrders
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch dashboard statistics',
                'details' => $e->getMessage()
            ], 500);
        }
    }
} 