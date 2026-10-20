"use client";
import { useCart } from "./CartProvider";
export default function AddToCart({ id, name, price }: { id: number; name: string; price: number }) {
  const { add } = useCart();
  return <button className="btn" onClick={() => add({ id, name, price })}>Agregar al carrito</button>;
}
