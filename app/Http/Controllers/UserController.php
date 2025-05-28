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
use App\Services\CloudinaryService; // Ensure this service is created for handling Cloudinary operations

class UserController extends Controller
{
    protected $cloudinaryService;

    public function __construct(CloudinaryService $cloudinaryService)
    {
        $this->cloudinaryService = $cloudinaryService;
    }

    public function index(Request $request)
    {
        $query = User::query();

        if ($request->has('roles')) {
            $roles = explode(',', $request->query('roles'));
            $query->whereIn('role', $roles);
        } elseif ($request->has('role')) {
            $query->where('role', $request->query('role'));
        }

        $users = $query->get();

        return UserResource::collection($users);
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = bcrypt($data['password']);

        // Handle Cloudinary profile photo upload
        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            if ($file->isValid()) {
                $uploadResult = $this->cloudinaryService->upload($file, 'profile_photos', [
                    'transformation' => [
                        'width' => 400,
                        'height' => 400,
                        'crop' => 'fill',
                        'gravity' => 'face',
                        'quality' => 'auto',
                        'fetch_format' => 'auto'
                    ]
                ]);
                
                if ($uploadResult) {
                    $data['profile_photo_url'] = $uploadResult['url'];
                    $data['profile_photo_public_id'] = $uploadResult['public_id'];
                } else {
                    // Fallback to local storage if Cloudinary fails
                    Log::warning('Cloudinary upload failed, falling back to local storage', [
                        'user_data' => $data['email'] ?? 'unknown'
                    ]);
                    $data['profile_photo'] = $file->store('profile_photos', 'public');
                }
            }
        }

        $user = User::create($data);

        return response()->json([
            'message' => 'User created successfully.',
            'data' => new UserResource($user),
        ], 201);
    }

    public function show(Request $request) 
    {
        $user = $request->user()->fresh();
        Log::info('User from token:', ['user' => $user]);

        // Handle Google profile photo if needed - SAVE THE CHANGES
        if (
            $user->profile_photo &&
            filter_var($user->profile_photo, FILTER_VALIDATE_URL) &&
            strpos($user->profile_photo, 'google') !== false &&
            !$user->profile_photo_url // Only if no Cloudinary URL exists
        ) {
            $user->profile_photo_url = $user->profile_photo;
            $user->save(); // Add this line to persist the change
        }

        Log::info('Final profile photo path:', ['photo' => $user->profile_photo]);

        return new UserResource($user);
    }

    public function update(UpdateUserRequest $request, $user_id) 
    {
        $user = User::where('user_id', $user_id)->firstOrFail();
        $validatedData = $request->validated();

        if (isset($validatedData['password'])) {
            $validatedData['password'] = bcrypt($validatedData['password']);
        }

        // Handle Cloudinary profile photo upload
        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            if ($file->isValid()) {
                // Delete old image from Cloudinary
                if ($user->profile_photo_public_id) {
                    $this->cloudinaryService->delete($user->profile_photo_public_id);
                }

                // Upload new image
                $uploadResult = $this->cloudinaryService->upload($file, 'profile_photos');
                if ($uploadResult) {
                    $validatedData['profile_photo_url'] = $uploadResult['url'];
                    $validatedData['profile_photo_public_id'] = $uploadResult['public_id'];
                }
            }
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

        // Delete profile photo from Cloudinary
        if ($user->profile_photo_public_id) {
            $this->cloudinaryService->delete($user->profile_photo_public_id);
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
            'profile_photo' => 'nullable|image|max:2048',
        ]);

        // Handle Cloudinary profile photo upload
        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            if ($file->isValid()) {
                // Delete old image from Cloudinary
                if ($user->profile_photo_public_id) {
                    $this->cloudinaryService->delete($user->profile_photo_public_id);
                }

                // Upload new image
                $uploadResult = $this->cloudinaryService->upload($file, 'profile_photos');
                if ($uploadResult) {
                    $data['profile_photo_url'] = $uploadResult['url'];
                    $data['profile_photo_public_id'] = $uploadResult['public_id'];
                }
            }
        }

        $user = User::find($user->user_id);
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

                // Delete profile photo from Cloudinary
                if ($user->profile_photo_public_id) {
                    $this->cloudinaryService->delete($user->profile_photo_public_id);
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

    /* protected function proxyGoogleProfilePhoto($googleUrl)
    {
        try {
            $hash = md5($googleUrl);
            $filename = "uploads/profile_photos/{$hash}.jpg";
            $storagePath = public_path($filename);

            if (!file_exists($storagePath)) {
                if (!file_exists(dirname($storagePath))) {
                    mkdir(dirname($storagePath), 0755, true);
                }

                $imageContent = file_get_contents($googleUrl);
                if ($imageContent === false) {
                    return null;
                }

                file_put_contents($storagePath, $imageContent);
            }

            return $filename;
        } catch (\Exception $e) {
            Log::error('Failed to proxy Google profile photo: ' . $e->getMessage());
            return null;
        }
    } */
}
