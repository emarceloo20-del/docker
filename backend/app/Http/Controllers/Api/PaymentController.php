<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProcessPaymentRequest;
use App\Models\Order;
use App\Services\StripeService;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
use Stripe\Exception\ApiErrorException;

class PaymentController extends Controller
{
    public function __construct(protected StripeService $stripeService) {}

    /**
     * @OA\Post(
     *     path="/payments",
     *     tags={"Pagos"},
     *     summary="Procesar el pago de una orden mediante Stripe",
     *     description="Crea y confirma un PaymentIntent de Stripe para la orden indicada, usando un payment_method_id generado en el cliente (Stripe.js / SDK móvil).",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"order_id","payment_method_id"},
     *             @OA\Property(property="order_id", type="integer", example=1),
     *             @OA\Property(property="payment_method_id", type="string", example="pm_card_visa")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Pago procesado exitosamente", @OA\JsonContent(@OA\Property(property="data", ref="#/components/schemas/Payment"))),
     *     @OA\Response(response=402, description="El pago fue rechazado por Stripe", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")),
     *     @OA\Response(response=403, description="La orden no pertenece al usuario autenticado", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")),
     *     @OA\Response(response=409, description="La orden ya fue pagada o no está pendiente", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")),
     *     @OA\Response(response=422, description="Error de validación", @OA\JsonContent(ref="#/components/schemas/ErrorResponse"))
     * )
     */
    public function store(ProcessPaymentRequest $request)
    {
        $order = Order::findOrFail($request->validated('order_id'));

        if ($order->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para pagar esta orden.',
            ], 403);
        }

        if ($order->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Esta orden no está pendiente de pago (estado actual: '.$order->status.').',
            ], 409);
        }

        try {
            $intent = $this->stripeService->chargeOrder($order, $request->validated('payment_method_id'));

            $status = $intent->status === 'succeeded' ? 'succeeded' : 'pending';

            $payment = $order->payment()->create([
                'user_id' => $request->user()->id,
                'stripe_payment_intent_id' => $intent->id,
                'stripe_charge_id' => $intent->latest_charge,
                'amount' => $order->total,
                'currency' => $intent->currency,
                'status' => $status,
                'payment_method' => $intent->payment_method_types[0] ?? null,
                'metadata' => ['stripe_status' => $intent->status],
            ]);

            if ($status === 'succeeded') {
                $order->update(['status' => 'paid']);
            }

            return response()->json([
                'success' => true,
                'message' => $status === 'succeeded'
                    ? 'Pago procesado exitosamente.'
                    : 'El pago requiere una acción adicional o está pendiente de confirmación.',
                'data' => $payment,
            ]);
        } catch (ApiErrorException $e) {
            $order->update(['status' => 'failed']);

            return response()->json([
                'success' => false,
                'message' => 'El pago fue rechazado por Stripe: '.$e->getMessage(),
            ], 402);
        }
    }

    /**
     * @OA\Get(
     *     path="/payments/{id}",
     *     tags={"Pagos"},
     *     summary="Consultar el estado de un pago",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Detalle del pago", @OA\JsonContent(@OA\Property(property="data", ref="#/components/schemas/Payment"))),
     *     @OA\Response(response=403, description="No autorizado", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")),
     *     @OA\Response(response=404, description="Pago no encontrado", @OA\JsonContent(ref="#/components/schemas/ErrorResponse"))
     * )
     */
    public function show(Request $request, \App\Models\Payment $payment)
    {
        if ($payment->user_id !== $request->user()->id && ! $request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para ver este pago.',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $payment,
        ]);
    }
}
