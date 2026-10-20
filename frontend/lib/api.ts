import { cookies } from "next/headers";
import { BASE_URL } from "./config";

export class ApiError extends Error {
  constructor(public status: number, message: string) { super(message); }
}
type Opts = { method?: string; body?: unknown; auth?: boolean; tags?: string[]; revalidate?: number };

export async function api<T = any>(path: string, o: Opts = {}): Promise<T> {
  const token = o.auth ? (await cookies()).get("token")?.value : undefined;
  if (o.auth && !token) throw new ApiError(401, "No autenticado");
  const res = await fetch(BASE_URL + path, {
    method: o.method ?? "GET",
    headers: {
      Accept: "application/json", "Content-Type": "application/json",
      ...(token ? { Authorization: `Bearer ${token}` } : {}),
    },
    body: o.body ? JSON.stringify(o.body) : undefined,
    ...(o.auth || o.method ? { cache: "no-store" } : { next: { tags: o.tags, revalidate: o.revalidate ?? 60 } }),
  });
  const json = await res.json().catch(() => ({}));
  if (!res.ok) {
    const first = json?.errors ? Object.values(json.errors as Record<string, string[]>)[0]?.[0] : null;
    throw new ApiError(res.status, first ?? json?.message ?? `Error ${res.status}`);
  }
  return json as T;
}
export const unwrap = <T,>(j: any): T => (j && typeof j === "object" && "data" in j ? j.data : j);
export const money = (n: number | string | undefined) => `$${Number(n ?? 0).toFixed(2)}`;
