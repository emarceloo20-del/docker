<?php

namespace App\Http\Controllers;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="API de E-commerce Segura",
 *     version="1.0.0",
 *     description="API RESTful para la gestión de un e-commerce: clientes, catálogo de productos, órdenes de compra y pagos vía Stripe. Autenticación mediante JWT.",
 *     @OA\Contact(email="soporte@ecommerce-api.test")
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="Servidor API"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Introduce el token JWT obtenido en /api/auth/login con el prefijo 'Bearer '."
 * )
 *
 * @OA\Tag(name="Autenticación", description="Registro, login y gestión de sesión de clientes")
 * @OA\Tag(name="Productos", description="Catálogo de productos (público y administración)")
 * @OA\Tag(name="Órdenes", description="Creación y consulta de órdenes de compra")
 * @OA\Tag(name="Pagos", description="Procesamiento de pagos con Stripe")
 *
 * @OA\Schema(
 *     schema="ErrorResponse",
 *     type="object",
 *     @OA\Property(property="success", type="boolean", example=false),
 *     @OA\Property(property="message", type="string", example="Ha ocurrido un error."),
 *     @OA\Property(property="errors", type="object", nullable=true)
 * )
 */
abstract class Controller
{
    //
}
