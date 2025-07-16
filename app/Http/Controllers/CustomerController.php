<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers = Customer::all();
        return response()->json(['data' => $customers], 200);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(CustomerRequest $request)
    {
        DB::beginTransaction();
        try {
            $customer = Customer::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'address' => $request->address,
            ]);
            DB::commit();
            return response()->json(['data' => $customer, 'message' => 'Operation completed successfully']);
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
    public function show(Customer $customer, $id)
    {
        $customer = $customer->findOrFail($id);
        return response()->json($customer);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer, $id)
    {
        $customer = $customer->findOrFail($id);
        if (!$customer) {
            return response()->json(['status' => 'error', 'message' => 'Customer not found!'], 404);
        }
        return response()->json(['data' => $customer, 'status' => 'success'], 202);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CustomerRequest $request, Customer $customer, $id)
    {
        try {
            DB::beginTransaction();
            $customer = $customer->findOrFail($id);
            $customer = $customer->update([
                'name' => $request->name,
                'phone' => $request->phone,
                'address' => $request->address,
            ]);
            DB::commit();

            return response()->json([
                'data'    => $customer,
                'message' => 'Customer updated'
            ], 200);
            //  return redirect()->back()->with('success', 'Operation completed successfully');
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
    public function destroy(Customer $customer, $id)
    {
        $customer = $customer->findOrFail($id);
        if (! $customer) {
            return response()->json(['error' => 'Customer not found'], 404);
        }

        $customer->delete();
        return response()->json(null, 204);
    }
}
