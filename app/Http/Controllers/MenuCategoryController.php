<?php

namespace App\Http\Controllers;

use App\Http\Resources\MenuCategoryResource;
use App\Models\MenuCategory;
use Illuminate\Http\Request;

class MenuCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return MenuCategoryResource::collection(MenuCategory::with('menuItems')->get());
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
