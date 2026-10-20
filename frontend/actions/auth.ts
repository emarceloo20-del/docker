"use server";
import { cookies } from "next/headers";
import { redirect } from "next/navigation";
import { api } from "@/lib/api";
import { EP } from "@/lib/config";

export type FormState = { error?: string } | undefined;

async function start(path: string, body: Record<string, unknown>): Promise<FormState> {
  try {
    const j = await api(path, { method: "POST", body });
    const token = j.token ?? j.access_token ?? j.data?.token ?? j.data?.access_token;
    if (!token) return { error: "La API no devolvió un token" };
    (await cookies()).set("token", token, {
      httpOnly: true, secure: process.env.NODE_ENV === "production",
      sameSite: "lax", path: "/", maxAge: 60 * 60 * 24 * 7,
    });
  } catch (e) { return { error: (e as Error).message }; }
  redirect("/");
}
export async function login(_: FormState, f: FormData) {
  return start(EP.login, { email: f.get("email"), password: f.get("password") });
}
export async function register(_: FormState, f: FormData) {
  return start(EP.register, {
    name: f.get("name"), email: f.get("email"),
    password: f.get("password"), password_confirmation: f.get("password"),
  });
}
export async function logout() {
  (await cookies()).delete("token");
  redirect("/");
}
