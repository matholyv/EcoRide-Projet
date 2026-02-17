# Documentation Service Email (Simulation)

## Contexte
Le projet EcoRide utilise le mailing transactionnel pour informer les utilisateurs :
*   Confirmation de trajet terminé (pour laisser un avis).
*   Notification de validation ou refus d'avis.
*   Suivi des litiges.

## Problématique & Solution
En environnement de développement local (WAMP/MAMP), la configuration d'un serveur SMTP est souvent complexe et non nécessaire. 
De plus, l'utilisation de services tiers (SendGrid, Mailgun) peut engendrer des coûts ou nécessiter une validation de domaine.

### Choix Technique : Simulation via Logs
Nous avons mis en place un système de **simulation d'envoi d'emails** :
1.  Le code exécute la fonction native PHP `mail()` (prêt pour la production).
2.  En parallèle, le contenu complet de l'email (Destinataire, Sujet, Corps, Lien) est écrit dans un fichier de log local : `emails.log` à la racine du projet.

## Comment tester les emails ?
1.  Effectuez une action qui déclenche un email (ex: Terminer un trajet en tant que conducteur).
2.  Ouvrez le fichier `emails.log` situé à la racine du projet.
3.  Vous y trouverez le contenu de l'email généré.
4.  Copiez-collez le lien fourni dans le log pour simuler le clic de l'utilisateur (ex: lien vers le formulaire d'avis).

## Code Source (Extrait)
Le mécanisme est implémenté dans `src/Controllers/RideController.php` :

```php
@mail($to, $subject, $message, $headers); // Tentative réelle

// Simulation (Log)
$logContent = "DATE : " . date('Y-m-d H:i:s') . "\n";
$logContent .= "À : " . $to . "\n";
// ...
file_put_contents(__DIR__ . '/../../emails.log', $logContent, FILE_APPEND);
```

Ce système permet de valider 100% du workflow fonctionnel sans dépendance externe.
