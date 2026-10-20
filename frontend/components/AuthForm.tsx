"use client";
import { useActionState } from "react";
import Link from "next/link";
import { login, register } from "@/actions/auth";

export default function AuthForm({ mode }: { mode: "login" | "register" }) {
  const [state, action, pending] = useActionState(mode === "login" ? login : register, undefined);
  return (
    <form action={action} className="mx-auto mt-10 grid max-w-sm gap-3">
      <h1 className="text-2xl font-semibold">{mode === "login" ? "Iniciar sesión" : "Crear cuenta"}</h1>
      {mode === "register" && <input className="input" name="name" placeholder="Nombre" required />}
      <input className="input" name="email" type="email" placeholder="Correo" required />
      <input className="input" name="password" type="password" placeholder="Contraseña" minLength={8} required />
      {state?.error && <p role="alert" className="text-sm text-red-700">{state.error}</p>}
      <button className="btn" disabled={pending}>{pending ? "Enviando…" : mode === "login" ? "Entrar" : "Registrarme"}</button>
      <Link className="text-sm underline" href={mode === "login" ? "/register" : "/login"}>
        {mode === "login" ? "¿No tienes cuenta? Regístrate" : "¿Ya tienes cuenta? Inicia sesión"}
      </Link>
    </form>
  );
}
