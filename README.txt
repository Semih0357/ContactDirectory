Annuaire téléphonique créé par BASBUNAR Semih dans le cadre du module Programmation Web Avancé.

Ceci est un projet Symfony d'un annuaire téléphonique permettant de gérer des contacts.

Prérequis : 

Avant de commencer, assurez-vous d'avoir installé les éléments suivants sur votre machine :

    PHP (version 8.2.2)
    Composer (version 2.5.2)
    Symfony CLI (version 6.1.3)
    MySQL (version 8.0.32) ou tout autre système de gestion de base de données de votre choix

Installation :

Clonez ce dépôt Git sur votre machine :
git clone https://github.com/Semih0357/ContactDirectory.git

Accédez au répertoire du projet :
cd annuaire

Installez les dépendances du projet avec Composer :
composer install

Configurez les paramètres de base de données dans le fichier .env :
# Modifier les paramètres suivants avec les informations de votre base de données
DATABASE_URL=mysql://user:password@localhost:3306/nom_base_de_donnees

Créez la base de données :
php bin/console doctrine:database:create

Exécutez les migrations pour créer les tables de la base de données :
php bin/console doctrine:migrations:migrate

Lancez le serveur de développement Symfony :
symfony server:start

Accédez à l'application dans votre navigateur à l'adresse http://localhost:8000.

Utilisation : 
Connectez-vous à l'application en utilisant vos identifiants.
Une fois connecté, vous pouvez gérer vos contacts en les ajoutant, les modifiant ou les supprimant.
Vous pouvez également effectuer des recherches dans votre annuaire téléphonique.
