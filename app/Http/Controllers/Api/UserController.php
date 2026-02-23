<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('roles')->get();
        return response()->json(['data' => $users]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|min:8',
            'role' => 'required|exists:roles,name'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        $user->assignRole($request->role);

        return response()->json([
            'message' => 'User berhasil ditambahkan',
            'data' => $user->load('roles')
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => ['sometimes', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'sometimes|string|min:8',
            'role' => 'sometimes|exists:roles,name'
        ]);

        if($request->has('name')) $user->name = $request->name;
        if($request->has('email')) $user->email = $request->email;
        if($request->has('password')) $user->password = Hash::make($request->password);

        $user->save();

        if($request->has('role')){
            $user->syncRoles($request->role);
        }

        return response()->json([
            'message' => 'Data karyawan berhasil diperbarui',
            'data' => $user->load('roles')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if($user->hasRole('super_admin')) {
            return response()->json([
                'message' => 'Akun SuperAdmin tidak boleh dihapus!'
            ], 403);
        }

        $user->delete();

        return response()->json([
            'message' => 'Data karyawan berhasil dihapus'
        ]);
    }
}
