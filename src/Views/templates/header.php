<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$pageTitle = $pageTitle ?? 'EcoRide - Covoiturage Écologique';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="icon" type="image/svg+xml" href="assets/img/favicon.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <?php if (isset($extraStyles)) echo $extraStyles; ?>
</head>
<body>
    <header>
        <div class="container">
            <nav>
                <a href="index.php" class="logo">EcoRide 🌿</a>
                

                    
                <ul class="nav-links">
                    <li><a href="index.php">Accueil</a></li>
                    <li><a href="index.php?page=covoiturages">Covoiturages</a></li>
                    <li><a href="index.php?page=contact">Contact</a></li>
                </ul>

                <div class="nav-auth" style="display: flex; align-items: center; gap: 15px;">
                    <?php if (isset($_SESSION['user'])): ?>
                        <div class="user-credits" title="Vos crédits" style="font-weight: 600; color: var(--secondary-color); font-size: 0.9rem; padding: 5px 10px; background: #e0f2f1; border-radius: 15px; white-space: nowrap;">
                            💰 <?= $_SESSION['user']['credits'] ?>
                        </div>
                        
                        <div class="user-menu" style="position: relative; display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <?php 
                                $photoName = !empty($_SESSION['user']['photo']) ? $_SESSION['user']['photo'] : 'default_user.png';
                                // Si c'est un chemin complet ou juste le nom, on normalise (hack rapide)
                                if (strpos($photoName, '/') === false) {
                                    $photoUrl = 'uploads/' . $photoName;
                                } else {
                                    $photoUrl = $photoName; // Cas où c'est déjà un chemin (ex: assets/img/...)
                                }
                                // Fallback si le fichier n'existe pas (optionnel mais propre)
                                if (!file_exists(__DIR__ . '/../../../public/' . $photoUrl) && strpos($photoUrl, 'default') === false) {
                                    $photoUrl = 'assets/img/default_user.png';
                                }
                            ?>
                            <img src="<?= htmlspecialchars($photoUrl) ?>" alt="Profil" class="profile-pic" style="width: 35px; height: 35px; border-radius: 50%; object-fit: cover;">
                            <span style="font-weight: 500; font-size: 0.95rem; display: none;"><?= htmlspecialchars($_SESSION['user']['pseudo']) ?></span> 
                            <!-- Pseudo masqué sur mobile/tablette si besoin, ou on le garde -->
                            <span class="user-name"><?= htmlspecialchars($_SESSION['user']['pseudo']) ?></span>
                            
                            <div class="dropdown-content">
                                <?php if ($_SESSION['user']['role_id'] == 4): ?>
                                    <a href="index.php?page=admin_dashboard" style="color: var(--primary-color); font-weight: bold;">⚙️ Administration</a>
                                <?php elseif ($_SESSION['user']['role_id'] == 3): ?>
                                    <a href="index.php?page=employee_reviews" style="color: var(--primary-color); font-weight: bold;">💼 Espace Employé</a>
                                <?php endif; ?>
                                <a href="index.php?page=profile">Mon Profil</a>
                                <a href="index.php?page=publish">Publier un trajet</a>
                                <a href="index.php?page=history">Mes Trajets</a>
                                <a href="index.php?page=logout" style="color: #e74c3c;">Déconnexion</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="index.php?page=login" style="margin-right: 10px; padding: 8px 15px; border: 1px solid var(--primary-color); border-radius: 5px; color: var(--primary-color); text-decoration: none; font-weight: 500; transition: 0.3s;">Connexion</a>
                        <a href="index.php?page=register" class="btn-cta">Inscription</a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </header>

