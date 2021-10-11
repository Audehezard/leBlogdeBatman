# Projet Le Blog de Batman

## Installation

### Cloner le projet

```
git clone https://github.com/Audehezard/leBlogdeBatman.git
```

### Modifier les paramètres d'environnement dans le fichier .env (changer user_db et password_db)

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
```