export const BASE_URL = process.env.BASE_URL ?? "http://127.0.0.1:8000/api";
export const EP = {
  register: "/auth/register",
  login: "/auth/login",
  products: "/products",
  product: (id: string | number) => `/products/${id}`,
  orders: "/orders",
  payments: "/payments",
};