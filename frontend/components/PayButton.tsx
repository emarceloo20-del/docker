"use client";
import { useActionState } from "react";
import { payOrder } from "@/actions/orders";
export default function PayButton({ orderId }: { orderId: string }) {
  const [state, action, pending] = useActionState(payOrder.bind(null, orderId), undefined);
  return (
    <form action={action} className="grid gap-3">
      {state?.error && <p role="alert" className="text-sm text-red-700">{state.error}</p>}
      <button className="btn" disabled={pending}>{pending ? "Procesando pago…" : "Pagar con Stripe"}</button>
    </form>
  );
}
