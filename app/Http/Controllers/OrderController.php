<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return OrderResource::collection(Order::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): OrderResource
    {
        $order = Order::create($request->all());
        return new OrderResource($order);
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order): OrderResource
    {
        return new OrderResource($order);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order): OrderResource
    {
        $order->update($request->all());
        return new OrderResource($order);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order): \Illuminate\Http\Response
    {
        $order->delete();
        return response()->noContent();
    }
}
