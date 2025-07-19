<?php

namespace App\Http\Controllers;

use App\Enum\category;
use App\Enum\orderStatus;
use App\Models\Customer;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use App\Http\Requests\OrderRequest;
use App\Models\OrderDetails;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::with('customer', 'orderDetails')
            ->whereNotIn('order_status', [
                orderStatus::COMPLETED->value,
                orderStatus::SERVED->value
            ])
            ->get()
            ->map(function ($order) {
                $total = $order->orderDetails->sum(function ($detail) {
                    return $detail->item_price * $detail->quantity;
                });

                return [
                    'id' => $order->id,
                    'customer' => $order->customer->name ?? 'Unknown',
                    'total' => $total,
                    'status' => $order->order_status,
                    'date' => $order->created_at->format('Y-m-d'),
                    'table_number' => $order->table_number,
                ];
            });

        return response()->json(['orders' => $orders], 200);
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get all statuses except SERVED and COMPLETED
        $status = array_filter(orderStatus::values(), function ($s) {
            return !in_array($s, [
                orderStatus::SERVED->value,
                orderStatus::COMPLETED->value
            ]);
        });

        $customers = Customer::all();

        // Get all menu items
        $menuItems = MenuItem::whereIn('category', category::values())->get();

        // Group by category
        $grouped = $menuItems->groupBy('category');

        return response()->json([
            'status' => array_values($status), // reset array keys
            'customers' => $customers,
            'shirini' => $grouped[category::SHIRINI_BAB->value] ?? [],
            'khuraka' => $grouped[category::KHURAKA_BAB->value] ?? [],
            'nushaba' => $grouped[category::NUSHABA_BAB->value] ?? [],
        ], 200);
    }


    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            // Create order
            $order = Order::create([
                'order_status' => $request->order_status,
                'customer_id' => $request->customer_id,
                'table_number' => $request->table_number,
            ]);

            foreach ($request->items as $item) {
                $menu = MenuItem::findOrFail($item['menu_item_id']);
                $order->orderDetails()->create([
                    'menu_item_id' => $item['menu_item_id'], // must match column name
                    'quantity' => $item['quantity'],
                    'item_price' => $menu->price,
                ]);
            }
            DB::commit();
            return response()->json(['data' => $order, 'message' => 'Order created successfully!']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'error' => 'Something went wrong!',
                'details' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        $order = $order->findOrFail($id);
        if (!$order) {
            return response()->json(['status' => 'error', 'message' => 'Order not found!'], 404);
        }
        return response()->json(['data' => $order, 'status' => 'success'], 202);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(OrderRequest $request, Order $order, $id)
    {
        try {
            // DB::beginTransaction();
            $order = $order->findOrFail($id);
            $order = $order->update([
                'order_status' => $request->order_status,
                'customer_id' => $request->customer_id,
                'table_number' => $request->table_number,
            ]);
            // DB::commit();

            return response()->json([
                'data' => $order,
                'message' => 'Order updated'
            ], 200);
        } catch (\Throwable $th) {
            // DB::rollBack();
            return response()->json([
                'error' => 'Update failed',
                'details' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        $order = $order->findOrFail($id);
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        $order->delete();
        return response()->json(null, 204);
    }

    /**
     * Update the status resource in storage.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'order_status' => [
                'required',
                'string',
                Rule::in(orderStatus::values()), // Use enum values for validation
            ],
        ]);

        $order = Order::findOrFail($id);
        $order->order_status = $request->order_status;
        $order->save();

        return response()->json(['message' => 'Order status updated successfully', 'order' => $order], 200);
    }
}
