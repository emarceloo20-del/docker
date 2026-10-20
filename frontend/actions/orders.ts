"use server";
import { redirect } from "next/navigation";
import { revalidatePath } from "next/cache";
import { api, unwrap } from "@/lib/api";
import { EP } from "@/lib/config";
import type { FormState } from "./auth";

export async function createOrder(_: FormState, f: FormData): Promise<FormState> {
  let id: number;
  try {
    const items = JSON.parse(String(f.get("items") ?? "[]")) as { id: number; quantity: number }[];
    if (!items.length) return { error: "El carrito está vacío" };
    const j = await api(EP.orders, {
      method: "POST", auth: true,
      body: { items: items.map((i) => ({ product_id: i.id, quantity: i.quantity })) },
    });
    id = unwrap<{ id: number }>(j).id;
  } catch (e) {
    if ((e as any).status === 401) redirect("/login");
    return { error: (e as Error).message };
  }
  revalidatePath("/orders");
  redirect(`/checkout/${id}`);
}

export async function payOrder(orderId: string, _: FormState): Promise<FormState> {
  let url: string | undefined;
  try {
    const j = await api(EP.payments, {
      method: "POST", auth: true,
      body: { order_id: Number(orderId), payment_method_id: "pm_card_visa" },
    });
    const d = unwrap<any>(j);
    url = d?.checkout_url ?? d?.url;
  } catch (e) { return { error: (e as Error).message }; }
  revalidatePath("/orders");
  redirect(url ?? `/checkout/success?order=${orderId}`);
}