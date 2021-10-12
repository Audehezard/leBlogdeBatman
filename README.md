# Projet Le Blog de Batman

## Installation

### Cloner le projet

```
git clone https://github.com/Audehezard/leBlogdeBatman.git
```

### Modifier les paramètres d'environnement dans le fichier .env (changer user_db et password_db, clés google de Recaptcha)

### Déplacer le terminal dans le dossier cloné
```
cd leblogdebatman
```

### Taper les commandes suivantes :

```
composer install
symfony console doctrine:database:create
symfony console make:migration
symfony console doctrine:migrations:migrate

symfony console doctrines:fixtures:load
symfony console assets:install public
```
Les fixtures créeront:
* un compte admin (email admin@a.a, password : 666aaa)
* 10 comptes utilisateurs (email aléatoire, password 666aaa
* 200 articles
* entre 0 et 10 commentaires par article)