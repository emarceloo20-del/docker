import { api, unwrap, money } from "@/lib/api";
import { EP } from "@/lib/config";
import type { Order } from "@/lib/types";
import PayButton from "@/components/PayButton";

export default async function Checkout({ params }: { params: Promise<{ orderId: string }> }) {
  const { orderId } = await params;
  const o = unwrap<Order>(await api(`${EP.orders}/${orderId}`, { auth: true }));
  return (
    <section className="grid max-w-md gap-4">
      <h1 className="text-2xl font-semibold">Pagar orden #{o.id}</h1>
      <p>Total a pagar: <strong>{money(o.total)}</strong></p>
      <PayButton orderId={orderId} />
    </section>
  );
}
