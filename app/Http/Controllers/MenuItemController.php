<?php

namespace App\Http\Controllers;

use App\Http\Resources\MenuItemResource;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class MenuItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return MenuItemResource::collection(MenuItem::all());
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
