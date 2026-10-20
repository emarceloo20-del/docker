<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
use RuntimeException;

class OrderController extends Controller
{
    public function __construct(protected OrderService $orderService) {}

    /**
     * @OA\Post(
     *     path="/orders",
     *     tags={"Órdenes"},
     *     summary="Crear una nueva orden de compra",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"items"},
     *             @OA\Property(property="items", type="array", @OA\Items(
     *                 @OA\Property(property="product_id", type="integer", example=1),
     *                 @OA\Property(property="quantity", type="integer", example=2)
     *             )),
     *             @OA\Property(property="shipping_address", type="string", example="Colonia Escalón, San Salvador"),
     *             @OA\Property(property="notes", type="string", example="Entregar en horario de oficina")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Orden creada exitosamente", @OA\JsonContent(@OA\Property(property="data", ref="#/components/schemas/Order"))),
     *     @OA\Response(response=401, description="No autenticado", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")),
     *     @OA\Response(response=422, description="Error de validación o stock insuficiente", @OA\JsonContent(ref="#/components/schemas/ErrorResponse"))
     * )
     */
    public function store(StoreOrderRequest $request)
    {
        try {
            $order = $this->orderService->createOrder(
                user: $request->user(),
                items: $request->validated('items'),
                shippingAddress: $request->validated('shipping_address'),
                notes: $request->validated('notes'),
            );

            return response()->json([
                'success' => true,
                'message' => 'Orden creada exitosamente. Procede al pago para confirmarla.',
                'data' => $order,
            ], 201);
        } catch (RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * @OA\Get(
     *     path="/orders",
     *     tags={"Órdenes"},
     *     summary="Consultar el historial de compras del usuario autenticado",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="status", in="query", @OA\Schema(type="string"), description="Filtrar por estado"),
     *     @OA\Response(
     *         response=200,
     *         description="Historial de órdenes",
     *         @OA\JsonContent(@OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Order")))
     *     ),
     *     @OA\Response(response=401, description="No autenticado", @OA\JsonContent(ref="#/components/schemas/ErrorResponse"))
     * )
     */
    public function index(Request $request)
    {
        $query = $request->user()->orders()->with('items', 'payment');

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $orders = $query->latest()->paginate($request->integer('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $orders,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/orders/{id}",
     *     tags={"Órdenes"},
     *     summary="Obtener el detalle de una orden propia",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Detalle de la orden", @OA\JsonContent(@OA\Property(property="data", ref="#/components/schemas/Order"))),
     *     @OA\Response(response=403, description="No autorizado para ver esta orden", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")),
     *     @OA\Response(response=404, description="Orden no encontrada", @OA\JsonContent(ref="#/components/schemas/ErrorResponse"))
     * )
     */
    public function show(Request $request, Order $order)
    {
        if ($order->user_id !== $request->user()->id && ! $request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para ver esta orden.',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $order->load('items', 'payment'),
        ]);
    }
}
