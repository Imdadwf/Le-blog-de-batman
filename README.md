# Projet le Blog de Batman

## Installation

```
git clone https://github.com/Imdadwf/Le-blog-de-batman.git
```

### Modifier les paramètres d'environnement dans le fichier .env pour les faire correspondre à votre environnement (Accès base de données, clé Google Recaptcha, ect..)

```
# Accès base de données à modifier
DATABASE_URL="mysql://root:@127.0.0.1:3306/leblogdebatman?serverVersion=5.7&charset=utf8mb4"

# Clés Google Recaptcha à modifier
GOOGLE_RECAPTCHA_SITE_KEY=XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
GOOGLE_RECAPTCHA_PRIVATE_KEY=XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
```

## Déplacer le terminal dans le dossier cloné du projet 

```
cd leblogdebatman
```

## Taper les commandes suivantes :
```
composer install
symfony console doctrine:database:create
symfony console make:migration
symfony console doctrine:migrations:migrate
symfony console doctrine:fixtures:load
symfony console assets:install public
```

Les fixtures créeront :
* Un compte admin (email: admin@a.a, mot de passe : azerty)
* 10 comptes utilisateurs (email aléatoire, mot de passe : azerty)
* 200 articles
* Entre 0 et 10 commentaires par article

### Démarrer le serveur Symfony
```
symfony serve
```
