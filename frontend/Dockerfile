# ---------- deps ----------
FROM node:20-alpine AS deps
WORKDIR /app/frontend
COPY package*.json ./

# installe les deps de base du projet
RUN npm ci || npm install

# ---------- dev ----------
FROM node:20-alpine AS dev
WORKDIR /app/frontend
COPY --from=deps /app/frontend/node_modules ./node_modules
COPY . .
EXPOSE 5173
CMD ["npm","run","dev","--","--host","0.0.0.0"]

# ---------- build ----------
FROM node:20-alpine AS build
WORKDIR /app/frontend
COPY --from=deps /app/frontend/node_modules ./node_modules
COPY . .
RUN npm run build

# ---------- prod ----------
FROM nginx:1.27-alpine AS prod
COPY --from=build /app/frontend/dist /usr/share/nginx/html
EXPOSE 80
CMD ["nginx","-g","daemon off;"]
