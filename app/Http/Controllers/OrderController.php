<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order; // Asire w ou gen modèl sa yo
use App\Models\OrderItem;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validasyon done ki soti nan JS
        $request->validate([
            'table_id' => 'required',
            'items' => 'required|array', // Tablo atik yo
            'note' => 'nullable|string'
        ]);

        // 2. Kreye lòd la (Order)
        $order = Order::create([
            'table_id' => $request->table_id,
            'statut' => 'en_attente',
            'note' => $request->note,
            'total' => $this->calculateTotal($request->items)
        ]);

        // 3. Anrejistre chak atik nan lòd la (OrderItems)
        foreach ($request->items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'plat_id' => $item['id'],
                'quantite' => $item['qty']
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Lòd ou a byen resevwa!',
            'order_id' => $order->id
        ]);
    }

    private function calculateTotal($items) {
        // Lojik pou kalkile total la depi baz done a
        // (pa janm fè konfyans total ki soti nan frontend sèlman)
        return array_reduce($items, function($sum, $item) {
            return $sum + ($item['prix'] * $item['qty']);
        }, 0);
    }
}








