<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class OrderService
{
    /**
     * Crea una orden a partir de un arreglo de items [{product_id, quantity}, ...].
     *
     * @throws RuntimeException si no hay stock suficiente
     */
    public function createOrder(User $user, array $items, ?string $shippingAddress = null, ?string $notes = null): Order
    {
        return DB::transaction(function () use ($user, $items, $shippingAddress, $notes) {
            $total = 0;
            $lineItems = [];

            foreach ($items as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                if (! $product->is_active) {
                    throw new RuntimeException("El producto '{$product->name}' no está disponible.");
                }

                if (! $product->hasStock($item['quantity'])) {
                    throw new RuntimeException("Stock insuficiente para el producto '{$product->name}'.");
                }

                $subtotal = $product->price * $item['quantity'];
                $total += $subtotal;

                $lineItems[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                    'subtotal' => $subtotal,
                ];
            }

            $order = Order::create([
                'user_id' => $user->id,
                'total' => $total,
                'status' => 'pending',
                'shipping_address' => $shippingAddress,
                'notes' => $notes,
            ]);

            foreach ($lineItems as $lineItem) {
                $order->items()->create([
                    'product_id' => $lineItem['product']->id,
                    'product_name' => $lineItem['product']->name,
                    'quantity' => $lineItem['quantity'],
                    'unit_price' => $lineItem['unit_price'],
                    'subtotal' => $lineItem['subtotal'],
                ]);

                $lineItem['product']->decrementStock($lineItem['quantity']);
            }

            return $order->load('items');
        });
    }
}
