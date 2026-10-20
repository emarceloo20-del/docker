import { Suspense } from "react";
import ProductList from "@/components/ProductList";
import Skeleton from "@/components/Skeleton";

export default function Home() {
  return (
    <>
      <h1 className="mb-4 text-2xl font-semibold">Catálogo</h1>
      <Suspense fallback={<Skeleton />}><ProductList /></Suspense>
    </>
  );
}
