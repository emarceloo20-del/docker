<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Payment",
 *     type="object",
 *     title="Payment",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="order_id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="stripe_payment_intent_id", type="string", example="pi_3Nk1234567890"),
 *     @OA\Property(property="stripe_charge_id", type="string", nullable=true),
 *     @OA\Property(property="amount", type="number", format="float", example=59.97),
 *     @OA\Property(property="currency", type="string", example="usd"),
 *     @OA\Property(property="status", type="string", enum={"pending","succeeded","failed","refunded","canceled"}, example="succeeded"),
 *     @OA\Property(property="payment_method", type="string", nullable=true, example="card"),
 *     @OA\Property(property="created_at", type="string", format="date-time")
 * )
 */
class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'stripe_payment_intent_id',
        'stripe_charge_id',
        'amount',
        'currency',
        'status',
        'payment_method',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'metadata' => 'array',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
