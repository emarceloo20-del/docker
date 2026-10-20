import Link from "next/link";
import { api, unwrap, money } from "@/lib/api";
import { EP } from "@/lib/config";
import type { Product } from "@/lib/types";
import AddToCart from "./AddToCart";

export default async function ProductList() {
  const list = unwrap<Product[]>(await api(EP.products, { tags: ["products"] }));
  const products = Array.isArray(list) ? list : (list as any)?.data ?? [];
  if (!products.length) return <p>Aún no hay productos disponibles.</p>;
  return (
    <ul className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
      {products.map((p: Product) => (
        <li key={p.id} className="flex flex-col gap-2 rounded-lg border border-stone-200 bg-white p-4">
          <Link href={`/products/${p.id}`} className="text-lg font-medium hover:underline">{p.name}</Link>
          <p className="line-clamp-2 text-sm text-stone-600">{p.description}</p>
          <p className="mt-auto font-semibold">{money(p.price)}</p>
          <AddToCart id={p.id} name={p.name} price={Number(p.price)} />
        </li>
      ))}
    </ul>
  );
}
