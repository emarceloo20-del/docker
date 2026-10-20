# Frontend E-commerce (Next.js 16 + TypeScript + Tailwind)

Consume la API REST Laravel 12 (Swagger + Stripe). Lecturas con Server Components, mutaciones con Server Actions, token en cookie `httpOnly`.

## Configuración
```bash
cp .env.example .env.local   # define BASE_URL, p. ej. http://localhost:8000/api
npm install
npm run dev                  # http://localhost:3000
npm run build && npm start   # producción (necesario para Lighthouse)
```
> **Importante:** si las rutas de tu Swagger difieren (`/login`, `/products`, `/orders`, `/orders/{id}/pay`), edítalas en `lib/config.ts`.

## Rutas
| Ruta | Descripción | Acceso |
|---|---|---|
| `/` | Catálogo (Suspense + `loading.tsx` + `error.tsx`) | Público |
| `/products/[id]` | Detalle de producto | Público |
| `/login`, `/register` | Autenticación (Server Actions) | Público |
| `/cart` | Carrito (estado local) y creación de orden | Protegida |
| `/checkout/[orderId]` | Pago con Stripe | Protegida |
| `/checkout/success` | Confirmación de compra | Protegida |
| `/orders` | Historial de compras (Suspense) | Protegida |

## Decisiones técnicas
- `proxy.ts` (antes `middleware.ts` en Next 16) protege rutas privadas.
- `revalidatePath("/orders")` tras crear orden y pagar; catálogo con `tags: ["products"]`.
- Errores de la API se muestran en formularios y en `error.tsx`.

## Evidencias (agregar en `/evidencias`)
Swagger, flujo completo de compra y reporte Lighthouse.
