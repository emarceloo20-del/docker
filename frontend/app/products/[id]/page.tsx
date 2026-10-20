import { notFound } from "next/navigation";
import { api, unwrap, money, ApiError } from "@/lib/api";
import { EP } from "@/lib/config";
import type { Product } from "@/lib/types";
import AddToCart from "@/components/AddToCart";

export default async function ProductPage({ params }: { params: Promise<{ id: string }> }) {
  const { id } = await params;
  let p: Product;
  try { p = unwrap<Product>(await api(EP.product(id), { tags: ["products", `product-${id}`] })); }
  catch (e) { if (e instanceof ApiError && e.status === 404) notFound(); throw e; }
  return (
    <article className="grid max-w-xl gap-3">
      <h1 className="text-3xl font-semibold">{p.name}</h1>
      <p className="text-stone-700">{p.description}</p>
      <p className="text-2xl font-semibold">{money(p.price)}</p>
      <AddToCart id={p.id} name={p.name} price={Number(p.price)} />
    </article>
  );
}
