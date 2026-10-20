import type { Metadata } from "next";
import Link from "next/link";
import { cookies } from "next/headers";
import "./globals.css";
import CartProvider from "@/components/CartProvider";
import CartLink from "@/components/CartLink";
import { logout } from "@/actions/auth";

export const metadata: Metadata = { title: "Tienda", description: "E-commerce con Next.js" };

export default async function RootLayout({ children }: { children: React.ReactNode }) {
  const logged = !!(await cookies()).get("token");
  return (
    <html lang="es">
      <body>
        <CartProvider>
          <header className="border-b border-stone-200 bg-white">
            <nav className="mx-auto flex max-w-5xl items-center gap-5 px-4 py-3">
              <Link href="/" className="mr-auto text-lg font-semibold">Tienda</Link>
              <CartLink />
              {logged ? (
                <>
                  <Link href="/orders">Mis pedidos</Link>
                  <form action={logout}><button className="underline">Salir</button></form>
                </>
              ) : (
                <><Link href="/login">Entrar</Link><Link href="/register">Registro</Link></>
              )}
            </nav>
          </header>
          <main className="mx-auto max-w-5xl px-4 py-6">{children}</main>
        </CartProvider>
      </body>
    </html>
  );
}
