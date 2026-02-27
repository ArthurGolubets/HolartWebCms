<?php

namespace HolartWeb\HolartCMS\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use HolartWeb\HolartCMS\Models\TAdministrator;

class AdministratorController extends Controller
{
    /**
     * Display a listing of administrators.
     */
    public function index()
    {
        $administrators = TAdministrator::all();
        return response()->json($administrators);
    }

    /**
     * Store a newly created administrator.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:t_administrators,email',
            'password' => 'required|string|min:8',
            'role' => 'required|in:super_admin,administrator,manager',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $administrator = TAdministrator::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_active' => $request->is_active ?? true,
        ]);

        return response()->json($administrator, 201);
    }

    /**
     * Update the specified administrator.
     */
    public function update(Request $request, $id)
    {
        $administrator = TAdministrator::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:t_administrators,email,' . $id,
            'password' => 'sometimes|nullable|string|min:8',
            'role' => 'sometimes|required|in:super_admin,administrator,manager',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->only(['name', 'email', 'role', 'is_active']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $administrator->update($data);

        return response()->json($administrator);
    }

    /**
     * Remove the specified administrator.
     */
    public function destroy($id)
    {
        $administrator = TAdministrator::findOrFail($id);

        // Prevent deleting yourself
        if ($administrator->id === auth('admin')->id()) {
            return response()->json(['error' => 'Вы не можете удалить себя'], 403);
        }

        $administrator->delete();

        return response()->json(['message' => 'Администратор удален'], 200);
    }
}
