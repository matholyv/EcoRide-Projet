# EcoRide - Guide de Déploiement Local

Ce fichier explique comment installer et lancer l'application **EcoRide** sur votre machine.

## 1. Prérequis

*   **Serveur Web** : XAMPP, WAMP, MAMP, ou un serveur PHP interne.
*   **PHP** : Version 8.0 ou supérieure.
*   **Base de données** : MySQL ou MariaDB.

## 2. Installation Rapide

1.  Ouvrez un terminal à la racine du projet `EcoRide/`.
2.  Lancez le script d'installation automatique pour créer la base de données :
    ```bash
    php install/reinstall_db.php
    ```
    *(Ce script crée la base `ecoride`, les tables, et les utilisateurs par défaut)*

3.  Ajoutez des trajets de test (Optionnel mais recommandé) :
    ```bash
    php install/seed.php
    ```

## 3. Configuration BDD

Le fichier de configuration est situé dans : `src/Config/Database.php`.

Par défaut, il est configuré pour un environnement local standard (XAMPP/WAMP) :
*   **Host** : `127.0.0.1`
*   **User** : `root`
*   **Password** : `(vide)`

*Si votre configuration MySQL est différente (ex: mot de passe 'root'), modifiez ce fichier.*
> **Attention :** Si vous modifiez les identifiants dans `Database.php`, pensez à les modifier également dans le fichier `install/reinstall_db.php` (ligne 6) pour que le script d'installation fonctionne.

## 4. Lancement du Site

### Option A : Avec le serveur interne PHP (Recommandé)
Lancez cette commande dans votre terminal à la racine du projet :
```bash
php -S localhost:8000 -t public
```
Ouvrez votre navigateur sur : [http://localhost:8000](http://localhost:8000)

### Option B : Avec XAMPP / WAMP
1.  Déplacez le dossier `EcoRide` dans votre dossier `htdocs` ou `www`.
2.  Accédez via : `http://localhost/EcoRide/public/`

## 5. Comptes de Test (Identifiants)

Voici les comptes pré-créés pour tester les différents rôles :

| Rôle | Email | Mot de Passe |
| :--- | :--- | :--- |
| **Utilisateur** | `test@test.com` | `test` |
| **Employé** | `employe@test.com` | `test` |
| **Administrateur** | `admin@test.com` | `test` |

> **Conseil :** Pour tester le parcours complet (Inscription, Crédits de bienvenue, Réservation), nous vous invitons à **créer votre propre compte** depuis la page d'inscription.

