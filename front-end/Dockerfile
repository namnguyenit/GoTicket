# User Frontend (React/Vite) - build + Nginx static
FROM node:20-alpine AS build
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

FROM nginx:1.27-alpine
WORKDIR /usr/share/nginx/html
COPY --from=build /app/dist/ ./
# Minimal SPA config
RUN printf "server {\n  listen 80;\n  server_name _;\n  root /usr/share/nginx/html;\n  index index.html;\n  location / {\n    try_files $uri /index.html;\n  }\n  location = /nginx_health { return 200 'ok'; }\n}\n" > /etc/nginx/conf.d/default.conf
EXPOSE 80
