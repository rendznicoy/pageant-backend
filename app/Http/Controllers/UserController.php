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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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
        $user = $request->user();
        // Check if profile_photo is a Google URL and proxy it if needed
        if ($user->profile_photo && filter_var($user->profile_photo, FILTER_VALIDATE_URL) && strpos($user->profile_photo, 'google') !== false) {
            $localPath = $this->proxyGoogleProfilePhoto($user->profile_photo);
            if ($localPath) {
                $user->profile_photo = $localPath; // Update with local path
            }
        }
        return new UserResource($user);
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

    public function destroy(DestroyUserRequest $request, $user_id)
    {
        $user = User::where('user_id', $user_id)->firstOrFail();
        
        if ($user->user_id === auth()->user()->user_id) {
            return response()->json(['message' => 'Cannot delete self'], 422);
        }
        
        if ($user->events()->exists() || $user->judge()->exists()) {
            return response()->json(['message' => 'User has associated events or judge profile'], 422);
        }
        
        $user->delete();
        
        return response()->json(['message' => 'User deleted successfully.'], 204);
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'first_name' => 'sometimes|string',
            'last_name' => 'sometimes|string',
            'profile_photo' => 'nullable|image|max:2048', // max 2MB
        ]);

        if ($request->hasFile('profile_photo')) {
            $filename = time().'_'.$request->profile_photo->getClientOriginalName();
            $request->profile_photo->move(public_path('uploads/profile_photos'), $filename);
            $data['profile_photo'] = 'uploads/profile_photos/' . $filename;
        }

        $user = User::find($user->user_id); // Get the actual model instance
        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
            'user' => new UserResource($user),
        ]);
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,user_id',
        ]);

        $failed = [];
        $success = [];
        $currentUserId = auth()->user()->user_id;

        foreach ($request->user_ids as $user_id) {
            try {
                $user = User::where('user_id', $user_id)->firstOrFail();
                if ($user->user_id === $currentUserId) {
                    $failed[] = ['id' => $user_id, 'message' => 'Cannot delete self'];
                    continue;
                }
                if ($user->events()->exists() || $user->judge()->exists()) {
                    $failed[] = ['id' => $user_id, 'message' => 'User has associated events or judge profile'];
                    continue;
                }
                $user->delete();
                $success[] = $user_id;
            } catch (\Exception $e) {
                $failed[] = ['id' => $user_id, 'message' => $e->getMessage()];
            }
        }

        if (count($failed) === count($request->user_ids)) {
            return response()->json(['message' => 'Failed to delete all users', 'failed' => $failed], 422);
        }

        return response()->json([
            'message' => count($failed) ? 'Some users deleted successfully' : 'All users deleted successfully',
            'success' => $success,
            'failed' => $failed,
        ]);
    }

    protected function proxyGoogleProfilePhoto($googleUrl)
    {
        try {
            $filename = 'google_' . time() . '.jpg';
            $localPath = 'uploads/profile_photos/' . $filename;

            // Download and save the image
            $imageContent = file_get_contents($googleUrl);
            if ($imageContent === false) {
                return null; // Failed to fetch
            }

            file_put_contents(public_path($localPath), $imageContent);
            return $localPath;
        } catch (\Exception $e) {
            Log::error('Failed to proxy Google profile photo: ' . $e->getMessage());
            return null;
        }
    }
}