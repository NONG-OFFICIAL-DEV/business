# Stage 1: Build frontend
FROM node:20 AS build-stage

WORKDIR /app

# Copy package.json & install dependencies
COPY package*.json ./
RUN npm install

# Copy source files
COPY . .

# Build the frontend (Vite, React, Vue, etc.)
RUN npm run build

# Stage 2: Serve with Caddy
FROM caddy:2-alpine

# Copy built Vue app
COPY --from=build-stage /app/dist /usr/share/caddy

# Optional: custom Caddyfile
# COPY Caddyfile /etc/caddy/Caddyfile
