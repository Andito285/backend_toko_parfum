<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    /** @use HasFactory<\Database\Factories\OrderItemFactory> */
    // app/Models/OrderItem.php
    protected $fillable = ['order_id', 'perfume_id', 'quantity', 'price'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function perfume()
    {
        return $this->belongsTo(Perfume::class);
    }
    use HasFactory;
}
