FROM node:20-alpine

LABEL maintainer="votre-nom@email.com"
LABEL description="Node.js 20 pour Nuxt 3 avec hot-reload"

# Installation des dépendances système
RUN apk add --no-cache \
    git \
    python3 \
    make \
    g++ \
    curl

# Création de l'utilisateur non-root
RUN addgroup -g 1000 nodejs && \
    adduser -G nodejs -g nodejs -s /bin/sh -D nodejs

WORKDIR /app

# Copie des fichiers de dépendances en premier pour le cache
COPY --chown=nodejs:nodejs package*.json ./

# Configuration NPM pour le développement
ENV NPM_CONFIG_PRODUCTION=false
ENV NODE_ENV=development

USER nodejs

EXPOSE 3000

CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0"]