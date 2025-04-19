<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest\ShowUserRequest;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest\StoreUserRequest;
use App\Http\Requests\UserRequest\UpdateUserRequest;
use App\Http\Requests\UserRequest\DestroyUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->has('role')) {
            $query->where('role', $request->query('role'));
        }

        $users = $query->get();

        return response()->json(UserResource::collection($users));
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = bcrypt($data['password']);

        $user = User::create($data);

        return response()->json([
            'message' => 'User created successfully.',
            'data' => new UserResource($user),
        ], 201);
    }

    public function show(Request $request) 
    {
        /* $validated = $request->validated();

        $user = User::where('user_id', $validated['user_id'])->firstOrFail(); */

        return new UserResource($request->user());
    }

    public function update(UpdateUserRequest $request, $user_id) 
    {
        $user = User::where('user_id', $user_id)->firstOrFail();
        $validatedData = $request->validated();
        
        if (isset($validatedData['password'])) {
            $validatedData['password'] = bcrypt($validatedData['password']);
        }
        
        $user->update($validatedData);
        
        return response()->json([
            'message' => 'User updated successfully.',
            'user' => new UserResource($user),
        ]);
    }

    public function destroy(DestroyUserRequest $request, $id) 
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User deleted successfully.'], 204);
    }
}
