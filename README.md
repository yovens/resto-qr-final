<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>
# 🍽️ Resto Kay-Y – Système Intelligent de Gestion de Restaurant

## 📌 Présentation

**Resto Kay-Y** est une application web moderne développée avec **Laravel** permettant la gestion complète d’un restaurant avec menu digital et commandes en temps réel.

Le système permet aux clients de scanner un **QR Code** depuis leur table afin d’accéder au menu, ajouter des plats au panier et envoyer leurs commandes directement en cuisine.

---

## ✨ Fonctionnalités principales

### 👨‍🍳 Gestion Cuisine

* Réception des commandes en temps réel
* Acceptation des commandes
* Mise à jour du statut :

  * Nouvelle commande
  * Acceptée
  * En préparation
  * Prête
  * Servie
* Historique des ventes
* Archivage automatique des commandes terminées

---

### 🍽️ Menu Digital QR Code

* QR Code unique pour chaque table
* Accès rapide au menu sans application
* Interface mobile moderne et responsive
* Recherche et filtrage par catégorie
* Images et descriptions des plats

---

### 🛒 Panier Intelligent

* Ajout / suppression de plats
* Modification des quantités
* Calcul automatique :

  * Sous-total
  * Service (10%)
  * Total
* Notes personnalisées pour la cuisine
* Sauvegarde locale du panier

---

### ⚡ Commandes Temps Réel

Grâce à **Laravel Broadcasting + Pusher** :

* Les commandes apparaissent instantanément en cuisine
* Les statuts sont mis à jour sans recharger la page
* Communication rapide entre client et cuisine

---

### 🪑 Gestion des Tables

* Création des tables
* Génération automatique des QR Codes
* Accès menu personnalisé par table

---

### 📊 Historique & Administration

* Dashboard administrateur
* Gestion des plats
* Gestion des catégories
* Gestion des tables
* Historique des commandes et ventes

---

## 🛠️ Technologies utilisées

### Backend

* Laravel
* PHP
* MySQL
* Eloquent ORM
* Broadcasting Events

### Frontend

* HTML5
* CSS3
* JavaScript
* Responsive Design

### Temps Réel

* Pusher
* Laravel Echo

### QR Code

* Simple QrCode

---

## ⚙️ Installation

### 1. Cloner le projet

```bash
git clone votre-repository.git
```

### 2. Installer les dépendances

```bash
composer install
```

```bash
npm install
```

### 3. Configurer le fichier .env

Configurer :

* Base de données MySQL
* Pusher
* APP_URL

Puis :

```bash
php artisan key:generate
```

### 4. Migration Base de données

```bash
php artisan migrate
```

### 5. Lancer le projet

```bash
php artisan serve
```

---

## 📱 Utilisation

### Client

1. Scanner QR Code
2. Choisir les plats
3. Ajouter au panier
4. Envoyer commande
5. Suivre la préparation

### Cuisine

1. Voir commandes instantanément
2. Accepter
3. Préparer
4. Marquer prête
5. Servir et archiver

---

## 🔒 Sécurité

* Protection CSRF Laravel
* Validation des formulaires
* Gestion sécurisée des requêtes

---

## 👨‍💻 Développeur

Projet développé par **Jocelyn Youvens**
Application Laravel de gestion de restaurant moderne avec QR Code et commandes temps réel.

---

## 📄 Licence

Projet éducatif et professionnel – Tous droits réservés.

