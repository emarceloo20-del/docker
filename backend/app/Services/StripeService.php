<?php

namespace App\Services;

use App\Models\Order;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent;
use Stripe\StripeClient;

class StripeService
{
    protected StripeClient $client;

    public function __construct()
    {
        $this->client = new StripeClient(config('services.stripe.secret'));
    }

    /**
     * Crea y confirma un PaymentIntent para una orden usando un payment_method de Stripe.
     *
     * @throws ApiErrorException
     */
    public function chargeOrder(Order $order, string $paymentMethodId): PaymentIntent
    {
        return $this->client->paymentIntents->create([
            'amount' => $this->toCents((float) $order->total),
            'currency' => config('services.stripe.currency', 'usd'),
            'payment_method' => $paymentMethodId,
            'confirmation_method' => 'manual',
            'confirm' => true,
            'automatic_payment_methods' => [
                'enabled' => true,
                'allow_redirects' => 'never',
            ],
            'description' => "Pago de la orden {$order->order_number}",
            'metadata' => [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'user_id' => $order->user_id,
            ],
        ]);
    }

    public function retrievePaymentIntent(string $paymentIntentId): PaymentIntent
    {
        return $this->client->paymentIntents->retrieve($paymentIntentId);
    }

    public function refund(string $paymentIntentId): \Stripe\Refund
    {
        return $this->client->refunds->create([
            'payment_intent' => $paymentIntentId,
        ]);
    }

    protected function toCents(float $amount): int
    {
        return (int) round($amount * 100);
    }
}
