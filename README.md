# سامقة - Système de Gestion des Rôles avec Laravel 10 et Vue.js 3

![Laravel](https://img.shields.io/badge/Laravel-10.x-red.svg)
![Vue.js](https://img.shields.io/badge/Vue.js-3.x-green.svg)
![PHP](https://img.shields.io/badge/PHP-8.1+-blue.svg)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-orange.svg)

سامقة est une application web moderne développée avec Laravel 10 et Vue.js 3, offrant un système complet de gestion des rôles et d'authentification. Le projet implémente une architecture robuste avec trois niveaux de permissions : Super Administrateur, Administrateur et Client.

## 🚀 Fonctionnalités Principales

### Système d'Authentification
- **Laravel Breeze** intégré avec Vue.js 3 et Inertia.js
- Authentification sécurisée avec sessions
- Vérification d'email automatique
- Gestion des mots de passe avec hachage bcrypt

### Gestion des Rôles
- **Super Administrateur** : Accès complet à toutes les fonctionnalités
- **Administrateur** : Gestion des utilisateurs et accès à l'interface d'administration
- **Client** : Accès limité aux fonctionnalités utilisateur standard

### Interface d'Administration
- Tableau de bord avec statistiques en temps réel
- Gestion complète des utilisateurs (CRUD)
- Filtrage et recherche avancés
- Interface responsive et moderne

### Sécurité
- Middlewares personnalisés pour la vérification des rôles
- Protection CSRF intégrée
- Validation des données côté serveur et client
- Contrôle d'accès granulaire

## 📋 Prérequis

Avant d'installer Bitaqati, assurez-vous que votre système dispose des éléments suivants :

### Logiciels Requis
- **PHP 8.1 ou supérieur** avec les extensions suivantes :
  - BCMath PHP Extension
  - Ctype PHP Extension
  - cURL PHP Extension
  - DOM PHP Extension
  - Fileinfo PHP Extension
  - JSON PHP Extension
  - Mbstring PHP Extension
  - OpenSSL PHP Extension
  - PCRE PHP Extension
  - PDO PHP Extension
  - Tokenizer PHP Extension
  - XML PHP Extension
  - ZIP PHP Extension

- **Composer 2.0 ou supérieur**
- **Node.js 16.0 ou supérieur**
- **NPM ou Yarn**
- **MySQL 8.0 ou supérieur** (ou MariaDB 10.3+)
- **Serveur web** (Apache, Nginx, ou serveur de développement PHP)

### Vérification des Prérequis

```bash
# Vérifier la version de PHP
php --version

# Vérifier les extensions PHP
php -m

# Vérifier Composer
composer --version

# Vérifier Node.js
node --version

# Vérifier NPM
npm --version
```

## 🛠️ Installation

### Étape 1 : Cloner le Projet

```bash
git clone <url-du-repository>
cd bitaqati
```

### Étape 2 : Installation des Dépendances PHP

```bash
# Installer les dépendances Composer
composer install

# Copier le fichier d'environnement
cp .env.example .env

# Générer la clé d'application
php artisan key:generate
```

### Étape 3 : Configuration de la Base de Données

Modifiez le fichier `.env` avec vos paramètres de base de données :

```env
APP_NAME=Bitaqati
APP_ENV=local
APP_KEY=base64:votre-cle-generee
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bitaqati
DB_USERNAME=votre_utilisateur_mysql
DB_PASSWORD=votre_mot_de_passe_mysql
```

### Étape 4 : Création de la Base de Données

```bash
# Créer la base de données MySQL
mysql -u root -p
CREATE DATABASE bitaqati CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

### Étape 5 : Exécution des Migrations

```bash
# Exécuter les migrations pour créer les tables
php artisan migrate

# (Optionnel) Exécuter les seeders pour les données de test
php artisan db:seed
```

### Étape 6 : Installation des Dépendances Frontend

```bash
# Installer les dépendances NPM
npm install

# Compiler les assets pour le développement
npm run dev

# Ou compiler pour la production
npm run build
```

## ⚙️ Configuration

### Configuration de l'Environnement

Le fichier `.env` contient toutes les variables d'environnement nécessaires. Voici les principales sections à configurer :

#### Base de Données
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bitaqati
DB_USERNAME=root
DB_PASSWORD=
```

#### Mail (Optionnel)
```env
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@samqa.com"
MAIL_FROM_NAME="${APP_NAME}"
```

#### Session et Cache
```env
SESSION_DRIVER=file
SESSION_LIFETIME=120
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
```

### Configuration des Permissions

Le système utilise trois rôles principaux définis dans le modèle `User` :

- `super_admin` : Accès complet au système
- `admin` : Accès à l'interface d'administration
- `client` : Accès utilisateur standard

Les middlewares correspondants sont automatiquement enregistrés :
- `super_admin` : Vérifie le rôle Super Administrateur
- `admin` : Vérifie les privilèges d'administration
- `role:role1,role2` : Vérifie plusieurs rôles

## 👑 Création d'un Super Administrateur

Pour créer votre premier Super Administrateur, utilisez la commande artisan personnalisée :

### Méthode Interactive

```bash
php artisan bitaqati:create-super-admin
```

La commande vous demandera interactivement :
- Le nom du Super Admin
- L'adresse email
- Le mot de passe

### Méthode avec Options

```bash
php artisan bitaqati:create-super-admin \
  --name="John Doe" \
  --email="admin@bitaqati.com" \
  --password="motdepasse123"
```

### Exemple d'Utilisation

```bash
$ php artisan bitaqati:create-super-admin

=== Création d'un Super Administrateur ===

 Nom du Super Admin:
 > John Doe

 Email du Super Admin:
 > admin@bitaqati.com

 Mot de passe du Super Admin:
 > 

✅ Super Admin créé avec succès !

Détails du compte :
ID: 1
Nom: John Doe
Email: admin@bitaqati.com
Rôle: super_admin
Créé le: 05/07/2025 23:45:12

Le Super Admin peut maintenant se connecter à l'interface d'administration.
```

## 🚀 Lancement du Projet

### Serveur de Développement

```bash
# Démarrer le serveur Laravel
php artisan serve

# Dans un autre terminal, compiler les assets en mode watch
npm run dev
```

L'application sera accessible à l'adresse : `http://localhost:8000`

### Serveur de Production

Pour un déploiement en production :

```bash
# Optimiser l'application
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Compiler les assets pour la production
npm run build

# Configurer les permissions
chmod -R 755 storage bootstrap/cache
```

## 📱 Utilisation

### Connexion

1. Accédez à `http://localhost:8000`
2. Cliquez sur "Se connecter"
3. Utilisez les identifiants du Super Admin créé précédemment

### Interface d'Administration

Une fois connecté avec un compte administrateur :

1. **Tableau de Bord** : Vue d'ensemble avec statistiques
   - Nombre total d'utilisateurs
   - Répartition par rôles
   - Utilisateurs récents

2. **Gestion des Utilisateurs** :
   - Liste paginée de tous les utilisateurs
   - Filtrage par rôle
   - Recherche par nom ou email
   - Création, modification et suppression d'utilisateurs

### Fonctionnalités par Rôle

#### Super Administrateur
- Accès complet à l'interface d'administration
- Gestion de tous les utilisateurs
- Création d'autres Super Administrateurs
- Modification et suppression de tous les comptes

#### Administrateur
- Accès à l'interface d'administration
- Gestion des utilisateurs (sauf Super Administrateurs)
- Création de nouveaux Administrateurs et Clients
- Modification des comptes non-Super Admin

#### Client
- Accès au tableau de bord utilisateur
- Gestion de son profil personnel
- Fonctionnalités utilisateur standard

## 🏗️ Architecture du Projet

### Structure des Dossiers

```
bitaqati/
├── app/
│   ├── Console/Commands/
│   │   └── CreateSuperAdmin.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── AdminController.php
│   │   │   │   └── UserManagementController.php
│   │   │   └── Auth/
│   │   └── Middleware/
│   │       ├── CheckAdmin.php
│   │       ├── CheckRole.php
│   │       └── CheckSuperAdmin.php
│   └── Models/
│       └── User.php
├── database/
│   ├── migrations/
│   │   └── add_role_to_users_table.php
│   └── seeders/
│       ├── DatabaseSeeder.php
│       └── UserSeeder.php
├── resources/
│   ├── js/
│   │   ├── Layouts/
│   │   │   └── AdminLayout.vue
│   │   └── Pages/
│   │       └── Admin/
│   │           ├── Dashboard.vue
│   │           └── Users/
│   │               ├── Index.vue
│   │               ├── Create.vue
│   │               └── Edit.vue
│   └── views/
└── routes/
    └── web.php
```

### Modèles de Données

#### Modèle User

Le modèle `User` étend le modèle d'authentification Laravel avec des fonctionnalités de gestion des rôles :

```php
class User extends Authenticatable
{
    const ROLE_SUPER_ADMIN = 'super_admin';
    const ROLE_ADMIN = 'admin';
    const ROLE_CLIENT = 'client';

    // Méthodes de vérification des rôles
    public function hasRole($role);
    public function isSuperAdmin();
    public function isAdmin();
    public function isClient();
    public function hasAdminPrivileges();
}
```

### Middlewares

#### CheckRole
Middleware flexible permettant de vérifier plusieurs rôles :
```php
Route::middleware(['auth', 'role:admin,super_admin'])->group(function () {
    // Routes accessibles aux admins et super admins
});
```

#### CheckAdmin
Middleware spécifique pour les privilèges d'administration :
```php
Route::middleware(['auth', 'admin'])->group(function () {
    // Routes d'administration
});
```

#### CheckSuperAdmin
Middleware restrictif pour les Super Administrateurs uniquement :
```php
Route::middleware(['auth', 'super_admin'])->group(function () {
    // Routes super admin uniquement
});
```

### Contrôleurs

#### AdminController
Gère le tableau de bord d'administration avec les statistiques.

#### UserManagementController
Contrôleur CRUD complet pour la gestion des utilisateurs avec :
- Pagination automatique
- Filtrage par rôle
- Recherche textuelle
- Validation des permissions

## 🔧 Personnalisation

### Ajout de Nouveaux Rôles

Pour ajouter un nouveau rôle :

1. **Modifier le modèle User** :
```php
const ROLE_MODERATOR = 'moderator';

public static function getRoles()
{
    return [
        self::ROLE_SUPER_ADMIN,
        self::ROLE_ADMIN,
        self::ROLE_MODERATOR, // Nouveau rôle
        self::ROLE_CLIENT,
    ];
}
```

2. **Créer une migration** :
```bash
php artisan make:migration update_user_role_enum
```

3. **Mettre à jour l'enum** :
```php
$table->enum('role', ['super_admin', 'admin', 'moderator', 'client'])->change();
```

### Personnalisation de l'Interface

L'interface utilise Tailwind CSS et peut être facilement personnalisée :

1. **Modifier les couleurs** dans `tailwind.config.js`
2. **Personnaliser les composants** Vue.js dans `resources/js/`
3. **Ajouter de nouveaux layouts** dans `resources/js/Layouts/`

### Ajout de Fonctionnalités

Pour ajouter de nouvelles fonctionnalités d'administration :

1. **Créer un contrôleur** :
```bash
php artisan make:controller Admin/NewFeatureController
```

2. **Ajouter les routes** dans `routes/web.php`
3. **Créer les composants Vue.js** correspondants
4. **Mettre à jour la navigation** dans `AdminLayout.vue`

## 🧪 Tests

### Tests Unitaires

```bash
# Exécuter tous les tests
php artisan test

# Tests spécifiques
php artisan test --filter UserTest
```

### Tests de Fonctionnalités

```bash
# Test des middlewares
php artisan test tests/Feature/MiddlewareTest.php

# Test de l'authentification
php artisan test tests/Feature/AuthTest.php
```

### Données de Test

Le projet inclut des seeders pour créer des données de test :

```bash
# Réinitialiser et peupler la base de données
php artisan migrate:fresh --seed
```

Comptes de test créés :
- **Admin** : admin@samqa.com / password
- **Client** : client@samqa.com / password
- **5 clients supplémentaires** générés automatiquement

## 🚨 Dépannage

### Problèmes Courants

#### Erreur de Permission
```bash
# Corriger les permissions des dossiers
sudo chown -R www-data:www-data storage bootstrap/cache
chmod -R 755 storage bootstrap/cache
```

#### Erreur de Base de Données
```bash
# Vérifier la connexion
php artisan tinker
DB::connection()->getPdo();
```

#### Erreur de Compilation Assets
```bash
# Nettoyer le cache NPM
npm cache clean --force
rm -rf node_modules package-lock.json
npm install
```

#### Erreur de Session
```bash
# Nettoyer le cache Laravel
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Logs et Débogage

Les logs sont disponibles dans `storage/logs/laravel.log` :

```bash
# Suivre les logs en temps réel
tail -f storage/logs/laravel.log

# Nettoyer les logs
echo "" > storage/logs/laravel.log
```

### Mode Debug

Pour activer le mode debug, modifiez `.env` :
```env
APP_DEBUG=true
LOG_LEVEL=debug
```

## 📚 Documentation Technique

### API Routes

Le projet expose les routes suivantes :

#### Routes d'Authentification
- `GET /login` - Page de connexion
- `POST /login` - Traitement de la connexion
- `POST /logout` - Déconnexion
- `GET /register` - Page d'inscription (clients uniquement)
- `POST /register` - Traitement de l'inscription

#### Routes d'Administration
- `GET /admin/dashboard` - Tableau de bord admin
- `GET /admin/users` - Liste des utilisateurs
- `GET /admin/users/create` - Formulaire de création
- `POST /admin/users` - Création d'utilisateur
- `GET /admin/users/{id}/edit` - Formulaire d'édition
- `PUT /admin/users/{id}` - Mise à jour d'utilisateur
- `DELETE /admin/users/{id}` - Suppression d'utilisateur

### Base de Données

#### Table users
```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    role ENUM('super_admin', 'admin', 'client') DEFAULT 'client',
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

### Configuration Avancée

#### Optimisation des Performances

```bash
# Cache de configuration
php artisan config:cache

# Cache des routes
php artisan route:cache

# Cache des vues
php artisan view:cache

# Optimisation de l'autoloader
composer install --optimize-autoloader --no-dev
```

#### Configuration du Serveur Web

##### Apache (.htaccess)
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

##### Nginx
```nginx
server {
    listen 80;
    server_name bitaqati.local;
    root /path/to/bitaqati/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

## 🤝 Contribution

### Guide de Contribution

1. **Fork** le projet
2. **Créer** une branche pour votre fonctionnalité (`git checkout -b feature/AmazingFeature`)
3. **Commiter** vos changements (`git commit -m 'Add some AmazingFeature'`)
4. **Pousser** vers la branche (`git push origin feature/AmazingFeature`)
5. **Ouvrir** une Pull Request

### Standards de Code

- Suivre les standards **PSR-12** pour PHP
- Utiliser **ESLint** pour JavaScript/Vue.js
- Documenter toutes les fonctions publiques
- Écrire des tests pour les nouvelles fonctionnalités

### Conventions de Nommage

- **Contrôleurs** : PascalCase avec suffixe `Controller`
- **Modèles** : PascalCase singulier
- **Migrations** : snake_case descriptif
- **Routes** : kebab-case
- **Composants Vue** : PascalCase

## 📄 Licence

Ce projet est sous licence MIT. Voir le fichier `LICENSE` pour plus de détails.

## 👥 Équipe

- **Développeur Principal** : Manus AI
- **Framework** : Laravel 10
- **Frontend** : Vue.js 3 + Inertia.js
- **Styling** : Tailwind CSS

## 📞 Support

Pour obtenir de l'aide ou signaler des problèmes :

1. **Issues GitHub** : Créer une issue détaillée
2. **Documentation** : Consulter ce README
3. **Logs** : Vérifier `storage/logs/laravel.log`

## 🔄 Mises à Jour

### Historique des Versions

#### v1.0.0 (Actuelle)
- Système d'authentification complet avec Laravel Breeze
- Gestion des rôles (Super Admin, Admin, Client)
- Interface d'administration avec Vue.js 3
- Commande artisan pour créer des Super Administrateurs
- Documentation complète

### Roadmap

#### v1.1.0 (Prochaine)
- Système de notifications
- Logs d'activité utilisateur
- Export des données utilisateurs
- API REST pour l'intégration mobile

#### v1.2.0 (Future)
- Authentification à deux facteurs (2FA)
- Gestion des permissions granulaires
- Interface de configuration avancée
- Thèmes personnalisables

---



