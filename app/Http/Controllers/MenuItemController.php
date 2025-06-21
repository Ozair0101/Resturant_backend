<?php

namespace App\Http\Controllers;

use App\Http\Requests\MenuItemRequest;
use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MenuItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $menuItems = MenuItem::all();
        $category = Category::all() ;
        return response()->json(['menuItem' => $menuItems,'category'=> $category]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(MenuItemRequest $request)
    {
        DB::beginTransaction(); 
        try {
            $customer = MenuItem::create([
                'name' => $request->name, 
                'price' => $request->price, 
                'is-available' => $request->is_available,
                'category_id' => $request->category_id,
                'description' => $request->description
             ]);
             DB::commit(); 
             return response()->json([ 'data' => $customer , 'message' => 'Operation completed successfully']);
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
    public function edit(MenuItem $menuItem, $id)
    {
        $menuItem = $menuItem->findOrFail($id);
        if(!$menuItem){
            return response()->json(['status' => 'error', 'message' => 'Customer not found!'], 404); 
        } 
        return response()->json([ 'data' => $menuItem, 'status' => 'success'], 202);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MenuItemRequest $request, MenuItem $menuItem, $id)
    {
          try {
            DB::beginTransaction(); 
            $menuItem = $menuItem->findOrFail($id);
            $menuItem = $menuItem->update([
                'name' => $request->name, 
                'price' => $request->price, 
                'is-available' => $request->is_available,
                'category_id' => $request->category_id,
                'description' => $request->description
             ]);
             DB::commit(); 
            
            return response()->json([
                'data'    => $menuItem,
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
    public function destroy(MenuItem $menuItem, $id)
    {
        $menuItem = $menuItem->findOrFail($id); 
        if (! $menuItem) {
            return response()->json(['error' => 'Customer not found'], 404);
        }
            
        $menuItem->delete();
        return response()->json(null, 204);
    }
}