export type Product = { id: number; name: string; description?: string; price: number | string; image?: string | null; stock?: number };
export type OrderItem = { id?: number; product_id?: number; name?: string; quantity: number; price?: number | string };
export type Order = { id: number; status?: string; total?: number | string; created_at?: string; items?: OrderItem[] };
export type CartItem = { id: number; name: string; price: number; quantity: number };
