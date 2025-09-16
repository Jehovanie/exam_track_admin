
# Backend - API Platform avec Symfony

## Description

Ce backend est développé avec **Symfony** et utilise **API Platform** pour exposer des APIs RESTful permettant de gérer les examens. Il interagit avec une base de données MariaDB pour stocker les informations des examens et offre des fonctionnalités telles que la récupération, la création, la mise à jour et la suppression des examens.

Le projet utilise également Docker pour conteneuriser le backend et faciliter son déploiement dans un environnement isolé et reproductible.

## Prérequis

Avant de démarrer l'application backend, assurez-vous d'avoir installé les éléments suivants :

- **Docker** et **Docker Compose** pour la conteneurisation.
- **Symfony CLI** pour le développement local.
- **PHP** et **Composer** pour gérer les dépendances Symfony.

## Installation

1. Clonez le repository du backend.
2. Allez dans le répertoire du projet backend.
3. Installez les dépendances PHP avec Composer :

```bash
composer install
```

## Lancer l'application avec Docker

Si vous utilisez Docker pour démarrer l'ensemble du projet (backend + frontend + base de données), vous pouvez utiliser cette commande pour démarrer tous les services :

```bash
docker-compose -f docker-compose.yml -p dev up
```

Cela démarrera :
- Le service **backend** Symfony avec API Platform
- Le service **frontend** Angular
- Le service **base de données** MariaDB

## Lancer le backend en développement local

Si vous ne souhaitez pas utiliser Docker, vous pouvez démarrer l'application en local avec Symfony. Assurez-vous d'avoir installé Symfony et les dépendances PHP nécessaires :

```bash
symfony serve
```

Cela démarrera le serveur Symfony à l'adresse [http://localhost:8000](http://localhost:8000).

## Architecture

L'API expose plusieurs endpoints pour interagir avec les examens. Voici les principaux endpoints disponibles :

### Endpoints

- **GET /api/exams** : Récupère la liste de tous les examens.
- **POST /api/exams** : Crée un nouvel examen.
- **GET /api/exams/{id}** : Récupère un examen spécifique par son ID.
- **PUT /api/exams/{id}** : Met à jour un examen existant.
- **DELETE /api/exams/{id}** : Supprime un examen.

### Modèle de données des examens

L'API repose sur un modèle de données simple pour gérer les informations des examens. Le modèle `Exam` contient les champs suivants :

- **id** : Identifiant unique de l'examen.
- **studentName** : Nom de l'étudiant.
- **location** : Lieu de l'examen (facultatif).
- **date** : Date de l'examen (requis).
- **time** : Heure de l'examen (requis).
- **status** : Statut de l'examen (Confirmé, À organiser, Annulé, En recherche de place).

## Base de données

Le backend utilise une base de données **MariaDB** pour stocker les informations des examens. La configuration de la base de données est définie dans le fichier `docker-compose.yml` et peut être ajustée si nécessaire.

### Doctrine ORM

Symfony utilise **Doctrine ORM** pour interagir avec la base de données. Les entités sont définies dans le dossier `src/Entity`. L'entité `Exam` représente le modèle de données des examens.

### Commandes utiles

Pour générer des migrations et mettre à jour la base de données, utilisez les commandes suivantes :

- **Générer une migration** :

```bash
php bin/console make:migration
```

- **Appliquer la migration** :

```bash
php bin/console doctrine:migrations:migrate
```

## Sécurisation de l'API

L'API peut être sécurisée en utilisant des mécanismes d'authentification et d'autorisation. Par défaut, l'API expose des endpoints publics, mais vous pouvez ajouter des mesures de sécurité telles que :

- **JWT (JSON Web Tokens)** pour l'authentification.
- **Règles d'accès** basées sur les rôles des utilisateurs.

## Déploiement

Pour déployer le backend en production, utilisez Docker pour créer des images de production :

```bash
docker-compose -f docker-compose.yml -p prod up --build
```

Cela créera les images de production et lancera l'application dans un environnement de production.
