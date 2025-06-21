<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();
        return response()->json(['data' => $categories]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction(); 
        try {
            $category = Category::create([
                'name' => $request->name, 
                'description' => $request->description,
             ]);
             DB::commit(); 
             return response()->json([ 'data' => $category , 'message' => 'Operation completed successfully']);
        } catch (\Throwable $th) {
            DB::rollBack(); 
             return response()->json([
                'error' => 'Something went wrong!',
                'details' => $th->getMessage()
            ], 500);
        }
    }
}
