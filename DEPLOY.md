# Documentation de Déploiement - Application EcoRide

## 1. Description de l'Application

EcoRide est une application web dynamique de covoiturage écologique.

*   **Type** : Application Web Dynamique (Back-end)
*   **Langage** : PHP 8.x (sans framework lourd, architecture MVC maison)
*   **Base de Données** : MySQL / MariaDB (Relationnelle)
*   **Serveur Web Cible** : Apache ou Nginx

Ce document décrit la procédure étape par étape pour déployer l'application sur un environnement de production (ex: VPS Linux, Hébergement mutualisé, ou machine virtuelle).

---

## 2. Prérequis Système

Avant de commencer, assurez-vous que le serveur dispose des composants suivants :

*   **Serveur Web** : Apache 2.4+ (avec `mod_rewrite` activé) OU Nginx.
*   **Langage** : PHP 8.0 ou supérieur.
    *   Extensions requises : `pdo`, `pdo_mysql`, `mbstring`.
*   **Base de Données** : MySQL 5.7+ ou MariaDB 10.3+.
*   **Accès** : FTP/SFTP ou SSH pour transférer les fichiers.

---

## 3. Installation de la Base de Données

1.  **Créer la base de données** :
    Connectez-vous à votre gestionnaire de base de données (phpMyAdmin, DBeaver, ou ligne de commande) et créez une nouvelle base de données (ex: `ecoride_prod`).
    ```sql
    CREATE DATABASE ecoride_prod CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ```

2.  **Importer la structure** :
    Importez le fichier SQL situé dans le dossier du projet : `sql/schema.sql`.
    *   *Commande CLI* : `mysql -u user -p ecoride_prod < sql/schema.sql`

3.  **(Optionnel) Peupler avec des données initiales** :
    Si vous souhaitez des données de test (marques de voitures, admins), vous pouvez exécuter le script de seed.
    *   Attention : En production, il est recommandé de créer uniquement le compte Admin manuellement ou via un script sécurisé, plutôt que d'utiliser des données de test fictives.

---

## 4. Déploiement des Fichiers

1.  **Transférer les fichiers** :
    Copiez l'ensemble du dossier `EcoRide` vers le répertoire racine de votre serveur web (ex: `/var/www/html/` ou `public_html`).

2.  **Structure recommandée sur le serveur** :
    Pour plus de sécurité, seul le dossier `public/` doit être accessible directement via le navigateur.
    
    ```
    /var/www/ecoride/
    ├── src/             <-- Code source (Non accessible publiquement)
    ├── sql/             <-- Scripts SQL
    ├── public/          <-- Racine Web (Document Root)
    │   ├── index.php
    │   ├── assets/
    │   └── .htaccess
    └── .env             <-- Fichier de configuration (Voir section 5)
    ```

---

## 5. Configuration de l'Application

L'application EcoRide utilise une classe de configuration base de données (`src/Config/Database.php`).
Pour la production, il est crucial de ne pas laisser les identifiants "en dur" dans le code.

1.  **Configurer la connexion BDD** :
    Ouvrez `src/Config/Database.php`.
    Modifiez les constantes ou assurez-vous qu'elles lisent des variables d'environnement.

    *Exemple de modification pour la production :*
    
    ```php
    // Dans src/Config/Database.php
    private $host = "localhost"; // Ou IP de votre serveur BDD
    private $db_name = "ecoride_prod";
    private $username = "votre_utilisateur_bdd";
    private $password = "votre_mot_de_passe_securise";
    ```

---

## 6. Configuration du Serveur Web (Apache)

L'application utilise l'URL Rewriting pour gérer les routes via `index.php`. Le fichier `.htaccess` est fourni dans le dossier `public/`.

1.  **Activer `mod_rewrite`** :
    Sur un serveur dédié : `sudo a2enmod rewrite && sudo service apache2 restart`.

2.  **Autoriser `.htaccess`** :
    Dans la configuration Apache (`vhost`), assurez-vous que `AllowOverride All` est défini pour le répertoire `public`.

    ```apache
    <VirtualHost *:80>
        ServerName ecoride.com
        DocumentRoot /var/www/ecoride/public
        
        <Directory /var/www/ecoride/public>
            AllowOverride All
            Require all granted
        </Directory>
    </VirtualHost>
    ```

---

## 7. Tests de Validation post-déploiement

Une fois déployé, effectuez les vérifications suivantes :

1.  **Page d'accueil** : Accédez à `http://votre-domaine.com`. La page d'accueil doit s'afficher sans erreur PHP.
2.  **Connexion BDD** : Tentez de vous connecter ou de faire une recherche de trajet. Si une erreur "Connexion refusée" apparaît, vérifiez vos identifiants dans `Database.php`.
3.  **Routing** : Cliquez sur les liens du menu (Covoiturages, Contact). Si vous avez une erreur 404, c'est que l'URL Rewriting (`.htaccess`) ne fonctionne pas.
4.  **Permissions** : Si l'upload d'images (profil, voiture) est prévu, assurez-vous que le dossier `public/assets/uploads/` (s'il existe) a les droits en écriture (`chmod 755` ou `775`).

---

## 8. Maintenance et Mises à jour

*   **Sauvegardes** : Mettez en place un dump SQL régulier de la base de données.
*   **Logs** : Surveillez les logs d'erreurs PHP (`/var/log/apache2/error.log` ou équivalent) pour détecter d'éventuels bugs en production.


