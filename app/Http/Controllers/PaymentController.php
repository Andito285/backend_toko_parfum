<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    /**
     * Upload payment proof for an order
     */
    public function uploadProof(Request $request, Order $order)
    {
        // Check if user owns this order
        if ($order->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Check if order is in pending status
        if ($order->payment_status !== 'pending') {
            return response()->json(['error' => 'Order sudah dibayar atau diverifikasi'], 400);
        }

        $request->validate([
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // Delete old proof if exists
        if ($order->payment_proof) {
            Storage::disk('public')->delete($order->payment_proof);
        }

        // Store new proof
        $path = $request->file('payment_proof')->store('payment_proofs', 'public');

        $order->update([
            'payment_proof' => $path,
            'payment_status' => 'paid',
            'payment_date' => now(),
        ]);

        return response()->json([
            'message' => 'Bukti pembayaran berhasil diupload',
            'order' => $order->load(['items.perfume.primaryImage', 'user'])
        ]);
    }

    /**
     * Get order detail with payment info
     */
    public function show(Request $request, Order $order)
    {
        // Check if user owns this order or is admin
        if ($order->user_id !== $request->user()->id && $request->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($order->load(['items.perfume.primaryImage', 'user', 'verifiedByUser']));
    }
}
