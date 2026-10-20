import { Suspense } from "react";
import { api, unwrap, money } from "@/lib/api";
import { EP } from "@/lib/config";
import type { Order } from "@/lib/types";

async function OrderList() {
  const raw = unwrap<Order[]>(await api(EP.orders, { auth: true }));
  const orders: Order[] = Array.isArray(raw) ? raw : (raw as any)?.data ?? [];
  if (!orders.length) return <p>Todavía no tienes pedidos.</p>;
  return (
    <ul className="grid gap-3">
      {orders.map((o) => (
        <li key={o.id} className="rounded-lg border border-stone-200 bg-white p-4">
          <p className="font-medium">Orden #{o.id} — {o.status ?? "pendiente"}</p>
          <p className="text-sm text-stone-600">{o.created_at ? new Date(o.created_at).toLocaleString("es") : ""}</p>
          <p className="font-semibold">{money(o.total)}</p>
        </li>
      ))}
    </ul>
  );
}
export default function Orders() {
  return (
    <>
      <h1 className="mb-4 text-2xl font-semibold">Mis pedidos</h1>
      <Suspense fallback={<div className="h-24 animate-pulse rounded-lg bg-stone-200" />}><OrderList /></Suspense>
    </>
  );
}
