<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Perfume;


class OrderController extends Controller
{

public function index(Request $request)
{
    return $request->user()->orders()->with('items.perfume.primaryImage')->get();
}

public function store(Request $request)
{
    $request->validate([
        'items' => 'required|array',
        'items.*.perfume_id' => 'required|exists:perfumes,id',
        'items.*.quantity' => 'required|integer|min:1'
    ]);

    $total = 0;
    $items = [];

    foreach ($request->items as $itemData) {
        $perfume = Perfume::find($itemData['perfume_id']);
        if ($perfume->stock < $itemData['quantity']) {
            return response()->json(['error' => "Stok tidak cukup untuk {$perfume->name}"], 400);
        }
        $items[] = [
            'perfume_id' => $perfume->id,
            'quantity' => $itemData['quantity'],
            'price' => $perfume->price
        ];
        $total += $perfume->price * $itemData['quantity'];
    }

    $order = Order::create([
        'user_id' => $request->user()->id,
        'total_amount' => $total
    ]);

    foreach ($items as $item) {
        OrderItem::create([
            'order_id' => $order->id,
            'perfume_id' => $item['perfume_id'],
            'quantity' => $item['quantity'],
            'price' => $item['price']
        ]);

        
        $perfume = Perfume::find($item['perfume_id']);
        $perfume->decrement('stock', $item['quantity']);
    }

    return response()->json($order->load('items.perfume.primaryImage'), 201);
}
}
