# 📚 Carnet de Lecture - Application Portfolio

Application web de gestion de lectures personnelles développée avec Laravel 11 (API) et Nuxt 3 (frontend), le tout conteneurisé avec Docker pour un déploiement simple et reproductible.

## 🎯 Fonctionnalités

- 📖 Gestion de bibliothèque personnelle
- ⭐ Notation et avis sur les livres
- 📊 Statistiques de lecture
- 🔍 Recherche de livres par API externe
- 📱 Interface responsive et moderne
- 🔐 Authentification sécurisée

## 📋 Prérequis

- [Docker](https://www.docker.com/products/docker-desktop/) (version 20.10+)
- [Docker Compose](https://docs.docker.com/compose/install/) (version 2.0+)
- Git

## 🚀 Installation en 3 commandes

```bash
# 1. Cloner le projet
git clone [url-de-votre-repo] carnet-lecture
cd carnet-lecture

# 2. Configurer l'environnement
cp .env.example backend/.env

# 3. Lancer le projet
make setup
```

## 🌐 URLs d'accès
### Service	URL	Description
- Frontend	http://localhost:3000	Interface Nuxt 3
- Backend	http://localhost:8000	API Laravel
- Database	localhost:5432	PostgreSQL 16

## 🛠️ Commandes utiles
### Docker & Make
```bash

make up          # Démarrer tous les services
make down        # Arrêter tous les services
make restart     # Redémarrer les services
make logs        # Voir tous les logs
make ps          # État des containers
```

### Base de données
```bash

make migrate     # Lancer les migrations
make seed        # Peupler la base de données
make fresh       # Réinitialiser la base de données
make shell-db    # Accéder au shell PostgreSQL
```

### Laravel Artisan
```bash

make artisan cmd="make:model Book"    # Créer un modèle
make artisan cmd="route:list"         # Lister les routes
make test                             # Lancer les tests
```

### Frontend Nuxt
```bash
make npm cmd="add @nuxt/ui"          # Ajouter un package
make npm cmd="run build"             # Builder pour la production
make logs-front                      # Logs du frontend
```

## 📁 Structure du projet
```bash
carnet-lecture/
├── docker/                    # Configuration Docker
│   ├── php/
│   │   └── Dockerfile        # Image PHP 8.3 optimisée
│   └── node/
│       └── Dockerfile        # Image Node 20 pour Nuxt
├── backend/                   # Application Laravel 11
│   ├── app/                   # Code métier
│   ├── config/               # Configuration
│   ├── database/             # Migrations & Seeds
│   ├── routes/               # Routes API
│   └── tests/                # Tests unitaires
├── frontend/                  # Application Nuxt 3
│   ├── components/           # Composants Vue
│   ├── pages/                # Pages
│   ├── composables/          # Logique réutilisable
│   └── assets/               # Styles & images
├── scripts/                   # Scripts utilitaires
│   └── wait-for-it.sh        # Attente PostgreSQL
├── docker-compose.yml         # Orchestration des services
├── Makefile                   # Commandes facilitées
└── README.md                  # Documentation
```

## 🔧 Stack technique

    Backend: PHP 8.3, Laravel 11, PostgreSQL 16

    Frontend: Vue 3, Nuxt 3, TypeScript, Tailwind CSS

    Infrastructure: Docker, Docker Compose

    Outils: Composer, NPM, Git


## 🐳 Services Docker
- app	PHP 8.3-fpm-alpine	8000	API Laravel
- db	PostgreSQL 16-alpine	5432	Base de données
- front	Node 20-alpine	3000	Serveur Nuxt dev