<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CategoryRequest\StoreCategoryRequest;
use App\Http\Requests\CategoryRequest\DestroyCategoryRequest;
use App\Http\Requests\CategoryRequest\ShowCategoryRequest;
use App\Http\Requests\CategoryRequest\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $perPage = 12;
        $eventId = $request->route('event_id') ?? $request->query('event_id');

        $query = Category::query();

        if ($eventId) {
            $query->where('event_id', $eventId);
        }

        $categories = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'data' => $categories->items(),
            'meta' => [
                'current_page' => $categories->currentPage(),
                'per_page' => $categories->perPage(),
                'total' => $categories->total(),
                'last_page' => $categories->lastPage(),
            ]
        ], 200);
    }

    public function store(StoreCategoryRequest $request)
    {
        $validated = $request->validated();
        $category = Category::create($validated);

        return response()->json([
            'message' => 'Category created successfully.',
            'data' => new CategoryResource($category),
        ], 201);
    }

    public function show(ShowCategoryRequest $request) 
    {
        $validated = $request->validated();

        $category = Category::where('category_id', $validated['category_id'])
            ->where('event_id', $validated['event_id'])
            ->firstOrFail();

        return response()->json(new CategoryResource($category), 200);
    }

    public function update(UpdateCategoryRequest $request) 
    {
        $validated = $request->validated();

        $category = Category::where('category_id', $validated['category_id'])
            ->where('event_id', $validated['event_id'])
            ->firstOrFail();

        $category->update($validated);

        return response()->json([
            'message' => 'Category updated successfully.',
            'category' => new CategoryResource($category),
        ]);
    }

    public function destroy(DestroyCategoryRequest $request) 
    {
        $validated = $request->validated();

        $category = Category::where('category_id', $validated['category_id'])
            ->where('event_id', $validated['event_id'])
            ->firstOrFail();

        $category->delete();

        return response()->json(['message' => 'Category deleted successfully.'], 204);
    }
}
