<?php

namespace App\Http\Controllers;

use App\Enum\orderStatus;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::with('customer', 'orderDetails')
            ->whereNotIn('order_status', [
                orderStatus::PENDING->value,
                orderStatus::PREPARING->value,
                orderStatus::COMPLETED->value,
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        DB::beginTransaction();
        try {
            // Fetch order with details
            $order = Order::with('orderDetails')->findOrFail($request->order_id);

            $order->update([
                'order_status' => $request->order_status
            ]);
            //     // // Calculate total amount
            $total = $order->orderDetails->sum(function ($detail) {
                return $detail->item_price * $detail->quantity;
            });
            $payment = Payment::create([
                'order_id' => $request->order_id,
                'amount' => $total,
                'payment_method' => $request->payment_method, // Probably should be $request->payment_method
            ]);

            DB::commit();
            return response()->json([
                // 'data' => $payment,
                'message' => 'Operation completed successfully'
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'error' => 'Something went wrong!',
                'details' => $th->getMessage()
            ], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function revenue()
    {
        $orders = Order::with('customer', 'orderDetails')
            ->where('order_status', orderStatus::COMPLETED->value)
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
     * Show the form for editing the specified resource.
     */
    public function edit(Payment $payment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payment $payment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        //
    }
}
