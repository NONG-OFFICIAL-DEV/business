FROM caddy:2.8.6-alpine

# Copy Caddyfile from root
COPY Caddyfile /etc/caddy/Caddyfile
