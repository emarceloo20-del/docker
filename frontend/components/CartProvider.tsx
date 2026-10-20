"use client";
import { createContext, useContext, useEffect, useState, type ReactNode } from "react";
import type { CartItem } from "@/lib/types";

type Ctx = { items: CartItem[]; add: (i: Omit<CartItem, "quantity">) => void; remove: (id: number) => void; clear: () => void; count: number; total: number };
const C = createContext<Ctx | null>(null);
export const useCart = () => { const c = useContext(C); if (!c) throw new Error("CartProvider"); return c; };

export default function CartProvider({ children }: { children: ReactNode }) {
  const [items, setItems] = useState<CartItem[]>([]);
  useEffect(() => { try { setItems(JSON.parse(localStorage.getItem("cart") ?? "[]")); } catch {} }, []);
  useEffect(() => { localStorage.setItem("cart", JSON.stringify(items)); }, [items]);
  const add: Ctx["add"] = (p) => setItems((s) => s.some((i) => i.id === p.id) ? s.map((i) => i.id === p.id ? { ...i, quantity: i.quantity + 1 } : i) : [...s, { ...p, quantity: 1 }]);
  const remove = (id: number) => setItems((s) => s.flatMap((i) => i.id !== id ? [i] : i.quantity > 1 ? [{ ...i, quantity: i.quantity - 1 }] : []));
  const clear = () => setItems([]);
  const count = items.reduce((a, i) => a + i.quantity, 0);
  const total = items.reduce((a, i) => a + i.quantity * i.price, 0);
  return <C.Provider value={{ items, add, remove, clear, count, total }}>{children}</C.Provider>;
}
