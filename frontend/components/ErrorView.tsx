"use client";
export default function ErrorView({ error, reset }: { error: Error; reset: () => void }) {
  return (
    <div role="alert" className="mt-10 grid gap-3">
      <h2 className="text-xl font-semibold">No pudimos cargar esta sección</h2>
      <p className="text-stone-600">{error.message}</p>
      <button className="btn w-fit" onClick={reset}>Reintentar</button>
    </div>
  );
}
