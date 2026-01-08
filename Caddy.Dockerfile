# Use official Caddy 2 Alpine variant
FROM caddy:2-alpine

# Copy Caddyfile from repo root
COPY Caddyfile /etc/caddy/Caddyfile
