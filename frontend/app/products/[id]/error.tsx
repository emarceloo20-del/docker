"use client";
import ErrorView from "@/components/ErrorView";

export default function Error(props: { error: Error; reset: () => void }) {
  return <ErrorView {...props} />;
}