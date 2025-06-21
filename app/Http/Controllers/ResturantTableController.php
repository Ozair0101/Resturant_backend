<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResturantTableRequest;
use App\Models\ResturantTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResturantTableController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $resturent = ResturantTable::all();
        return response()->json(['data' => $resturent]); 
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ResturantTableRequest $request)
    {
        DB::beginTransaction(); 
        try {
            $resturantTable = ResturantTable::create([
                'number' => $request->number, 
                'status' => $request->status,
                'capacity' => $request->capacity
             ]);
             DB::commit(); 
             return response()->json([ 'data' => $resturantTable , 'message' => 'Operation completed successfully']);
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
    public function edit(ResturantTable $resturantTable, $id) 
    {
        $resturantTable = $resturantTable->findOrFail($id);
        if(!$resturantTable){
            return response()->json(['status' => 'error', 'message' => 'Table not found!'], 404); 
        } 
        return response()->json([ 'data' => $resturantTable, 'status' => 'success'], 202);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ResturantTable $resturantTable, $id)
    {
        try {
            DB::beginTransaction(); 
            $resturantTable = $resturantTable->findOrFail($id);
            $resturantTable = $resturantTable->update([
                'number' => $request->number, 
                'status' => $request->status,
                'capacity' => $request->capacity
             ]);
             DB::commit(); 
            
            return response()->json([
                'data'    => $resturantTable,
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
    public function destroy(ResturantTable $resturantTable, $id)
    {
        $resturantTable = $resturantTable->findOrFail($id); 
        if (! $resturantTable) {
            return response()->json(['error' => 'Customer not found'], 404);
        }
            
        $resturantTable->delete();
        return response()->json(null, 204);
    }
}
