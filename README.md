# OPEN CLASSROOM : PROJET 5 Blog en Php

Création d'un blog en Php 8.3

## Liens utiles

- [Dépôt du projet](https://github.com/Raphael-Marchese/OpenClassroom_P5)
- [Gestionnaire de tâches](https://github.com/Raphael-Marchese/OpenClassroom_P5/issues)

## Installation / utilisation du projet

```shell
git clone git@github.com:Raphael-Marchese/OpenClassroom_P5.git   # Clone du projet
cd OpenClassroom_P5              # Se place dans le dossier du projet
php -S localhost:8080
```

## URL du projet en local

- http://localhost:8080 ou autre port si vous en avez fourni un différent

## Pour générer la base de données

Modifier le fichier Config.php avec en changeant les variables d'environnement suivantes:

```
HOST_DATABASE
USER_DATABASE
PASSWORD_DATABASE
NAME_DATABASE
```

Puis, dans le dossier "Database", jouer les données de:

```
professionnal_blog.sql #Crée la bdd
fill_database.sql  #Injecte les fixtures
```

## Pour l'envoi des emails

Par défaut dans ce projet j'utilise symfony/mailer avec le protocole de transport intégré smtp pour envoyer les emails.

Vous avez juste à changer la variables d'environnement suivante dans le Config.php (en suivant la forme fournie en
exemple):

```
DSN: smtp://user:pass@smtp.example.com:25
```

Toute la documentation est disponible ici : https://symfony.com/doc/current/mailer.html

## Pour se connecter

En tant qu'admin

```
mail:       admin@example.com
password:   test
```

En tant que simple user

```
mail:       user@example.com
password:   test
```

## Usage du site

Une fois le serveur lancé, le projet est disponible à l'adresse suivante :

```
http://localhost:8080 ou autre port si vous en avez fourni un différent
```

Il existe 2 parcours sur le site :

- Le parcours d'un simple user, qui accède au site à la racine, il peut consulter les articles, et écrire des
  commentaires.
- Le parcours administrateur , qui peut accèder à l'URL /admin, sur laquelle il peut modérer les commentaires, il peut
  également créer des posts, les modifier et/ou supprimer.

## Rôles

Les utilisateurs du site sont répartis dans 2 rôles différents :

- <b>ROLE_USER</b>, il s'agit des utilisateurs du site
- <b>ROLE_ADMIN</b>, il s'agit de l'administrateurs du site
