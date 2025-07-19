<?php

namespace App\Http\Controllers;

use App\Enum\category;
use App\Enum\orderStatus;
use App\Models\Customer;
use App\Models\MenuItem;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Requests\OrderRequest;
class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::whereNotIn('order_status', [
            orderStatus::COMPLETED->value,
            orderStatus::SERVED->value
        ])->get();

        return response()->json(['menuItem' => $orders], 200);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $status = orderStatus::values();
        $customers = Customer::all();
        // Get all menu items
        $menuItems = MenuItem::whereIn('category', category::values())->get();

        // Group by category
        $grouped = $menuItems->groupBy('category');

        return response()->json([
            'status' => $status,
            'customers' => $customers,
            'shirini' => $grouped[category::SHIRINI_BAB->value] ?? [],
            'khuraka' => $grouped[category::KHURAKA_BAB->value] ?? [],
            'nushaba' => $grouped[category::NUSHABA_BAB->value] ?? [],
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OrderRequest $request)
    {
        DB::beginTransaction();
        try {
            $order = Order::create([
                'order_status' => $request->order_status,
                'customer_id' => $request->customer_id,
                'table_number' => $request->table_number,
            ]);
            DB::commit();
            return response()->json(['data' => $order, 'message' => 'Operation completed successfully']);
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
            DB::beginTransaction();
            $order = $order->findOrFail($id);
            $order = $order->update([
                'order_status' => $request->order_status,
                'customer_id' => $request->customer_id,
                'table_number' => $request->table_number,
            ]);
            DB::commit();

            return response()->json([
                'data' => $order,
                'message' => 'Order updated'
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
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
}
