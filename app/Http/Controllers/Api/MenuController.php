<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MenuResource;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        if ($request->has('is_active')) {
            return MenuResource::collection(Menu::query()->where('is_active', $request->boolean('is_active'))->get());
        }
        return MenuResource::collection(Menu::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): MenuResource
    {
        $menu = Menu::create($request->all());
        return new MenuResource($menu);
    }

    /**
     * Display the specified resource.
     */
    public function show(Menu $menu): MenuResource
    {
        return new MenuResource($menu);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Menu $menu)
    {
        $menu->update($request->all());
        return new MenuResource($menu);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Menu $menu)
    {
        $menu->delete();
        return response()->noContent();
    }
}
