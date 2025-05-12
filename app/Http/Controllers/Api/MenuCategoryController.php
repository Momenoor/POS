<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MenuCategoryResource;
use App\Models\MenuCategory;
use App\Models\MenuCategoryItem;
use Illuminate\Http\Request;

class MenuCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $query = MenuCategoryItem::query();
        if ($request->has('menu_id')) {
            $query = $query->where('menu_id', $request->get('menu_id'));
        }
        if ($request->has('is_active')) {
            $query = $query->whereHas('category', function ($q) use ($request) {
                $q->where('is_active', $request->boolean('is_active'));
                $q->orderBy('sort_order', 'asc');
            });
        }
        return MenuCategoryResource::collection($query->get()->unique('category_id'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): MenuCategoryResource
    {
        $menuCategory = MenuCategory::create($request->all());
        return new MenuCategoryResource($menuCategory);
    }

    /**
     * Display the specified resource.
     */
    public function show(MenuCategory $menuCategory): MenuCategoryResource
    {
        return new MenuCategoryResource($menuCategory);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MenuCategory $menuCategory)
    {
        $menuCategory->update($request->all());
        return new MenuCategoryResource($menuCategory);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MenuCategory $menuCategory)
    {
        $menuCategory->delete();
        return response()->noContent();
    }
}
