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
        return response()->json($customers);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CustomerRequest $request)
    {
        try {
            DB::beginTransaction(); 
            $customer = Customer::create([
                'name' => $request->name, 
                'phone' => $request->phone, 
                'address' => $request->address,
             ]);
             DB::commit(); 
             return redirect()->back()->with('success', 'Operation completed successfully');
        } catch (\Throwable $th) {
            DB::rollBack(); 
            return redirect()->back()->with('error', 'Something went wrong!'); 
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
        if(!$customer){
            return response()->json(['status' => 'error', 'message' => 'Customer not found!'], 404); 
        } 
        return response()->json([ 'data' => $customer, 'status' => 'success'], 202);
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
             return redirect()->back()->with('success', 'Operation completed successfully');
        } catch (\Throwable $th) {
            DB::rollBack(); 
            return redirect()->back()->with('error', 'Something went wrong!'); 
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer, $id)
    {
        try {     
            $customer = $customer->findOrFail($id); 
            $customer = $customer->delete(); 
            return redirect()->back()->with('success', 'Operation completed successfully');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Something went wrong!'); 
        }
    }
}
