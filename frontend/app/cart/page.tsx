"use client";
import { useActionState } from "react";
import Link from "next/link";
import { useCart } from "@/components/CartProvider";
import { createOrder } from "@/actions/orders";
import { money } from "@/lib/format";

export default function CartPage() {
  const { items, add, remove, total } = useCart();
  const [state, action, pending] = useActionState(createOrder, undefined);
  if (!items.length) return <p>Tu carrito está vacío. <Link className="underline" href="/">Ver catálogo</Link></p>;
  return (
    <form action={action} className="grid max-w-xl gap-4">
      <h1 className="text-2xl font-semibold">Carrito</h1>
      <ul className="divide-y divide-stone-200 rounded-lg border border-stone-200 bg-white">
        {items.map((i) => (
          <li key={i.id} className="flex items-center gap-3 p-3">
            <span className="mr-auto">{i.name} × {i.quantity}</span>
            <span>{money(i.price * i.quantity)}</span>
            <button type="button" aria-label={`Quitar uno de ${i.name}`} onClick={() => remove(i.id)}>−</button>
            <button type="button" aria-label={`Agregar uno de ${i.name}`} onClick={() => add(i)}>+</button>
          </li>
        ))}
      </ul>
      <p className="text-right text-lg font-semibold">Total: {money(total)}</p>
      <input type="hidden" name="items" value={JSON.stringify(items)} />
      {state?.error && <p role="alert" className="text-sm text-red-700">{state.error}</p>}
      <button className="btn" disabled={pending}>{pending ? "Creando orden…" : "Crear orden y pagar"}</button>
    </form>
  );
}
