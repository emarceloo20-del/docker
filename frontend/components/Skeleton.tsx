export default function Skeleton({ n = 6 }: { n?: number }) {
  return (
    <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3" aria-busy="true">
      {Array.from({ length: n }, (_, i) => <div key={i} className="h-40 animate-pulse rounded-lg bg-stone-200" />)}
    </div>
  );
}
