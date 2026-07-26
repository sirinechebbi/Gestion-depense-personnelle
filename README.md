# GestDépenses — Application de gestion de dépenses

Application web de gestion de dépenses personnelles développée avec **Symfony 6.4** et **MySQL**.

## Fonctionnalités

- **Tableau de bord** avec statistiques (total du mois, évolution, graphiques)
- **Gestion des dépenses** : ajouter, modifier, supprimer
- **Filtres avancés** : par catégorie, période, montant, recherche textuelle
- **Gestion des catégories** personnalisables avec couleur et icône
- **Authentification** sécurisée (inscription / connexion)
- **Catégories par défaut** créées automatiquement à l'inscription
- **Graphiques** mensuels et par catégorie (Chart.js)

---

## Prérequis

- PHP 8.1 ou supérieur
- Composer
- MySQL 8.0 ou supérieur (ou MariaDB 10.4+)
- Serveur web (Apache, Nginx, ou Symfony CLI)

---

## Installation

### 1. Décompresser et accéder au dossier

```bash
cd gestion-depenses
```

### 2. Installer les dépendances PHP

```bash
composer install
```

### 3. Configurer la base de données

Copiez le fichier d'exemple et modifiez-le avec vos informations MySQL :

```bash
cp .env.example .env
```

Editez le fichier `.env` :

```
DATABASE_URL="mysql://VOTRE_UTILISATEUR:VOTRE_MOT_DE_PASSE@127.0.0.1:3306/gestion_depenses?serverVersion=8.0&charset=utf8mb4"
APP_SECRET=une_chaine_aleatoire_de_32_caracteres
```

### 4. Créer la base de données et exécuter les migrations

```bash
# Créer la base de données
php bin/console doctrine:database:create

# Exécuter les migrations (crée les tables)
php bin/console doctrine:migrations:migrate
```

### 5. Vider le cache (optionnel en dev)

```bash
php bin/console cache:clear
```

### 6. Lancer le serveur

#### Avec Symfony CLI (recommandé) :
```bash
symfony server:start
```
Accédez à : http://localhost:8000

#### Avec le serveur PHP intégré :
```bash
php -S localhost:8000 -t public/
```

#### Avec Apache / Nginx :
Pointez votre virtual host vers le dossier `public/`.

---

## Utilisation

1. Accédez à l'application dans votre navigateur
2. Cliquez sur **"Créer un compte"** pour vous inscrire
3. Des catégories par défaut sont créées automatiquement (Alimentation, Transport, Logement, etc.)
4. Commencez à saisir vos dépenses depuis le menu **"Nouvelle dépense"**

---

## Structure du projet

```
gestion-depenses/
├── config/              # Configuration Symfony
├── migrations/          # Migrations de base de données
├── public/              # Point d'entrée web (index.php)
├── src/
│   ├── Controller/      # Contrôleurs (Dashboard, Dépense, Catégorie, Sécurité)
│   ├── Entity/          # Entités Doctrine (User, Depense, Categorie)
│   ├── Form/            # Formulaires Symfony
│   └── Repository/      # Repositories avec requêtes personnalisées
├── templates/           # Templates Twig
│   ├── base.html.twig   # Layout principal avec sidebar
│   ├── dashboard/       # Tableau de bord
│   ├── depense/         # CRUD dépenses
│   ├── categorie/       # Gestion catégories
│   ├── security/        # Page de connexion
│   └── registration/    # Page d'inscription
├── .env                 # Configuration (à personnaliser)
└── composer.json        # Dépendances
```

---

## Dépendances principales

| Package | Version | Rôle |
|---------|---------|------|
| symfony/framework-bundle | 6.4 | Framework principal |
| doctrine/orm | 3.x | ORM base de données |
| symfony/security-bundle | 6.4 | Authentification |
| symfony/form | 6.4 | Formulaires |
| symfony/twig-bundle | 6.4 | Templates |
| Bootstrap | 5.3 | Interface utilisateur |
| Chart.js | 4.4 | Graphiques |
| Bootstrap Icons | 1.11 | Icônes |

---

## Commandes utiles

```bash
# Créer une migration après modification d'entité
php bin/console make:migration

# Appliquer les migrations
php bin/console doctrine:migrations:migrate

# Vider le cache
php bin/console cache:clear

# Lister les routes
php bin/console debug:router
```

---

## Résolution de problèmes

**Erreur de connexion à la base de données :**
Vérifiez le `DATABASE_URL` dans votre fichier `.env`.

**Page blanche :**
Vérifiez les logs dans `var/log/dev.log`.

**Erreur 500 :**
Activez le mode debug : `APP_ENV=dev` dans `.env`.
