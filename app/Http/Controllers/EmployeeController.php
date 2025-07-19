<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = Employee::all();
        return response()->json(['data' => $employees], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $employee = Employee::create([
                'name' => $request->name,
                'role' => $request->role,
                'status' => $request->status,
                'email' => $request->email,
            ]);
            DB::commit();
            return response()->json(['data' => $employee, 'message' => 'Operation completed successfully']);
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
    public function show(Employee $employee, $id)
    {
        $employee = $employee->findOrFail($id);
        return response()->json($employee);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee, $id)
    {
        $employee = $employee->findOrFail($id);
        if (!$employee) {
            return response()->json(['status' => 'error', 'message' => 'Employee not found!'], 404);
        }
        return response()->json(['data' => $employee, 'status' => 'success'], 202);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee, $id)
    {
        try {
            DB::beginTransaction();
            $employee = $employee->findOrFail($id);
            $employee = $employee->update([
                'name' => $request->name,
                'role' => $request->role,
                'status' => $request->status,
                'email' => $request->email,
            ]);
            DB::commit();

            return response()->json([
                'data' => $employee,
                'message' => 'Employee updated'
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
    public function destroy(Employee $employee, $id)
    {
        $employee = $employee->findOrFail($id);
        if (!$employee) {
            return response()->json(['error' => 'Employee not found'], 404);
        }

        $employee->delete();
        return response()->json(null, 204);
    }
}
