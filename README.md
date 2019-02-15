[![Build Status](https://travis-ci.com/scoorp29/Budjet-Project-Pro.svg?branch=master)](https://travis-ci.com/scoorp29/Budjet-Project-Pro)

## Description

Bank Pro est une application de gestion financière pour tous. Un utilisateur est lié à un abonnement et peut avoir un nombre nul ou infini de cartes de crédit. Lors de l'inscription, l'utilisateur est dans l'obligation d'informer l'abonnement sélectionné (créé par un administrateur).

##### Un visiteur peut : 

- Voir un utilisateur
- Voir une subscription
- Voir toutes les utilisateurs
- Voir toutes les subscriptions
- Créer un compte (avec une souscription obligatoire et la carte de crédit en option)

##### Un utilisateur peut :

- Voir son profile
- Voir tous ces cartes de crédit
- Éditer son profile
- Changer son souscription
- Créer une cart de crédit

##### Un admin peut : 

- Voir son profile
- Voir tous ces carts de crédit
- Voir le profil d'un utilisateur
- Voir tous les utilisateurs
- Voir tous les souscription
- Voir tous les cartes de crédit
- Changer son souscription
- Créer un utilisateur
- Créer une souscription
- Créer une cart de crédit et l'assigner à un utilisateur
- Éditer son profile
- Éditer une souscription
- Éditer n'importe quelles cartes de crédit



## Intallation

Comment installer le projet : 

- Lancer docker : `docker-compose up -d`
- Accéder au container nginx : `docker-compose exec web /bin/bash`
- Installer les dépendances avec composer : `composer install`
- Modifier le .env :
```bash
DATABASE_URL=mysql://root:root@database:3306/symfonyapi
```
- Installer la bdd (si non créée) : `php bin/console doctrine:database:create`
- Update la bdd : `php bin/console d:s:u --force`
- Générer les fixtures : `php bin/console hautelook:fixtures:load`

## ApiKey

Clé Api d'Administrateur : `72312`

Clé Api d'un Utilisateur : `93324`

## Accès

##### Pour accéder à la doc de Nelmio : 

```bash
localhost/api/doc
```

##### Pour acceder à phpMyAdmin :
```bash
localhost:8080
```
- Utilisateur : root
- Mot de passe :  root

## Route

Sur la page de `localhost/api/` faire attention au apiKey.

Un Visiteur ne peut pas aller sur la route d'Utilisateur ou Admin.

Un Utilisateur ne peut pas aller sur la route d'Administrateur.

Un Administrateur ne peut pas aller sur la route d'Utilisateur.

#### Route `localhost/api/`
Accessible pour tout le monde.
#### Route `localhost/user/`
Cette route est accessible seulement pour un Utilisateur.
#### Route `localhost/admin/`
Cette route est accessible seulement pour un Admin.

## Commande
##### Accéder au container nginx : 
```bash
docker-compose exec web /bin/bash
```

#### Créer un admin en ligne de commande
##### Prend 2 arguments : 
- 1er argument est l'email qui est obligatoire
- 2e argument qui est optionnel est l'ID de souscription qu'on veut ajouter à notre admin

##### Exemple d'utilisation: 
```bash
php bin/console app:create-admin email@email.fr
```
Ou
```bash
php bin/console app:user-count-card bwalker@rath.com
```

#### Afficher le nombre de cartes par email
##### Prend 1 argument : 
- Argument est l'email qui est obligatoire
##### Exemple d'utilisation: 
```bash
php bin/console app:create-admin email@email.fr
```

## Faker

##### User
- Pour générer les pays j'utilise la fonction : `countryCode`. Qui renvoie les abréviations, par exemple: FR pour la France.
##### Card
- Pour le générer un nombre pour la carte Bancaire, la méthode de Faker `<Creditcardnumber()>` n'est pas très bien, parce que ça génère le même nombre pour chaque carte alors que c'est logique qu'un numéro de carte est unique.
- Pour générer la valeur j'utilise : `<numberBetween(10000000, 20000000)>`