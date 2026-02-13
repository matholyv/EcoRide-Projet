<?php
$pageTitle = 'Mentions Légales - EcoRide';
ob_start();
?>
<style>
    .legal-content h2 { color: var(--primary-color); margin-top: 2rem; }
    .legal-content p { line-height: 1.6; color: #555; margin-bottom: 1rem; }
    .legal-container { max-width: 800px; margin: 4rem auto; padding: 2rem; background: white; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
</style>
<?php
$extraStyles = ob_get_clean();
require_once __DIR__ . '/templates/header.php';
?>

<main class="container">
    <div class="legal-container">
        <h1>Mentions Légales</h1>
        <p>En vigueur au 01/01/2026</p>

        <div class="legal-content">
            <h2>1. Éditeur du site</h2>
            <p>
                Le site EcoRide est édité par l'association <strong>EcoRide</strong>, association fictive à but pédagogique.<br>
                Siège social : 123 Avenue de l'Écologie, 75000 Paris, France.<br>
                Email : contact@ecoride.test<br>
                Directeur de la publication : José Garcia (Président).
            </p>

            <h2>2. Hébergement</h2>
            <p>
                Le site est hébergé par la société <strong>Railway Corp.</strong><br>
                Adresse : San Francisco, CA, USA.<br>
                Site web : <a href="https://railway.app" target="_blank">railway.app</a>
            </p>

            <h2>3. Propriété intellectuelle</h2>
            <p>
                L'ensemble de ce site relève de la législation française et internationale sur le droit d'auteur et la propriété intellectuelle. Tous les droits de reproduction sont réservés, y compris pour les documents téléchargeables et les représentations iconographiques et photographiques.
            </p>

            <h2>4. Données personnelles</h2>
            <p>
                Conformément au Règlement Général sur la Protection des Données (RGPD), vous disposez d'un droit d'accès, de rectification et de suppression des données vous concernant. Vous pouvez exercer ce droit en contactant notre délégué à la protection des données à l'adresse email ci-dessus.
            </p>
            
            <h2>5. Crédits</h2>
            <p>
                Conception et réalisation : <strong>Mathias Olive</strong><br>
                Images : Unsplash, Placehold.co<br>
                Icônes : FontAwesome / UTF-8 Emojis
            </p>

            <h2>6. Conditions Générales d'Utilisation (CGU)</h2>
            <p>
                L'utilisation de la plateforme EcoRide implique l'acceptation pleine et entière des conditions générales d'utilisation. 
                Les utilisateurs s'engagent à respecter la charte de bonne conduite (courtoisie, ponctualité, respect du code de la route).
                EcoRide agit en tant qu'intermédiaire de mise en relation et ne saurait être tenu responsable des incidents survenant durant les trajets.
            </p>
        </div>
        
        <div style="margin-top: 3rem; text-align: center;">
            <a href="index.php" class="btn-cta">Retour à l'accueil</a>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/templates/footer.php'; ?>
