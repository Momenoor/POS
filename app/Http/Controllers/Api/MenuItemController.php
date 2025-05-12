<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MenuItemResource;
use App\Models\MenuCategoryItem;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class MenuItemController extends Controller
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
        if ($request->has('category_id')) {
            $query = $query->where('category_id', $request->get('category_id'));
        }
        if ($request->has('is_available')) {
            $query = $query->where('menu_is_available', $request->boolean('is_available'));
        }

        return MenuItemResource::collection($query->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): MenuItemResource
    {
        $menuItem = MenuItem::create($request->all());
        return new MenuItemResource($menuItem);
    }

    /**
     * Display the specified resource.
     */
    public function show(MenuItem $menuItem): MenuItemResource
    {
        return new MenuItemResource($menuItem);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MenuItem $menuItem): MenuItemResource
    {
        $menuItem->update($request->all());
        return new MenuItemResource($menuItem);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MenuItem $menuItem): \Illuminate\Http\Response
    {
        $menuItem->delete();
        return response()->noContent();
    }
}
