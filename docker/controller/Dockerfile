# syntax=docker/dockerfile:1.7

ARG NODE_VERSION=22

FROM node:${NODE_VERSION}-alpine AS frontend-build

WORKDIR /app

COPY frontend/package*.json ./
RUN npm install

COPY frontend/ ./

ARG VITE_API_BASE_URL=/api
ENV VITE_API_BASE_URL=${VITE_API_BASE_URL}

RUN npm run build

FROM nginx:stable-alpine AS runtime

COPY docker/controller/nginx.conf /etc/nginx/conf.d/default.conf
COPY --from=frontend-build /app/dist /usr/share/nginx/html

EXPOSE 80
