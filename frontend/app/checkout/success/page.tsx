import Link from "next/link";
import ClearCart from "@/components/ClearCart";

export default async function Success({ searchParams }: { searchParams: Promise<{ order?: string }> }) {
  const { order } = await searchParams;
  return (
    <section className="grid max-w-md gap-3">
      <ClearCart />
      <h1 className="text-2xl font-semibold">¡Compra confirmada!</h1>
      <p>{order ? `Tu orden #${order} fue pagada.` : "Tu pago fue recibido."}</p>
      <Link className="underline" href="/orders">Ver mis pedidos</Link>
    </section>
  );
}
