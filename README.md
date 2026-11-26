# MiniShop - Application e-commerce

MiniShop est une application e-commerce développée avec Symfony.
Elle permet de consulter des produits, gérer un panier et passer une commande.

## Fonctionnalités

- Affichage des produits
- Consultation des détails d'un produit
- Gestion du panier
- Ajout et suppression de produits
- Modification des quantités
- Calcul automatique du total
- Passage et enregistrement des commandes
- Gestion des stocks

## Technologies

- PHP
- Symfony
- Twig
- Doctrine ORM
- MariaDB / MySQL
- Bootstrap 5
- HTML / CSS
- JavaScript

## Installation

Cloner le projet :

    git clone https://github.com/MuSqL-03/minishop.git

Entrer dans le dossier :

    cd minishop

Installer les dépendances :

    composer install

Configurer la base de données dans `.env.local`.

Créer la base de données :

    php bin/console doctrine:database:create

Exécuter les migrations :

    php bin/console doctrine:migrations:migrate

Charger les données de démonstration :

    php bin/console doctrine:fixtures:load

Lancer le serveur :

    symfony server:start

## Auteur

Développé dans le cadre de mon portfolio de développeur Symfony.
