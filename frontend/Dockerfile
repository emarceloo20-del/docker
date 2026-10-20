# syntax=docker/dockerfile:1.7

# ---------- Etapa 1: dependencias ----------
# Solo depende de package*.json: mientras no cambien, esta capa se reutiliza desde caché.
FROM node:20-alpine AS deps
RUN apk add --no-cache libc6-compat
WORKDIR /app
COPY package.json package-lock.json ./
RUN --mount=type=cache,target=/root/.npm npm ci

# ---------- Etapa 2: build ----------
FROM node:20-alpine AS builder
WORKDIR /app
ENV NEXT_TELEMETRY_DISABLED=1
COPY --from=deps /app/node_modules ./node_modules
COPY . .
# next.config.ts usa output: "standalone". Todas las rutas son dinámicas,
# por lo que el build NO necesita que la API esté disponible.
RUN npm run build

# ---------- Etapa 3: runtime ----------
FROM node:20-alpine AS runner
WORKDIR /app
# BASE_URL se lee en el servidor en runtime (lib/config.ts); se define en docker-compose.yml
ENV NODE_ENV=production \
    NEXT_TELEMETRY_DISABLED=1 \
    PORT=3000 \
    HOSTNAME=0.0.0.0

RUN addgroup -g 1001 -S nodejs && adduser -S nextjs -u 1001 -G nodejs

# Solo lo necesario para ejecutar: servidor standalone + estáticos + public
COPY --from=builder --chown=nextjs:nodejs /app/public ./public
COPY --from=builder --chown=nextjs:nodejs /app/.next/standalone ./
COPY --from=builder --chown=nextjs:nodejs /app/.next/static ./.next/static

USER nextjs
EXPOSE 3000
HEALTHCHECK --interval=30s --timeout=5s --start-period=15s --retries=3 \
  CMD wget -qO- http://127.0.0.1:3000/ >/dev/null || exit 1

CMD ["node", "server.js"]
