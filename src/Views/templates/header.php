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
                                // Gestion robuste de l'avatar
                                $photoPath = $_SESSION['user']['photo'] ?? '';
                                $pseudo = $_SESSION['user']['pseudo'] ?? 'User';
                                
                                // URL par défaut : Avatar généré avec les initiales (via ui-avatars.com)
                                $avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode($pseudo) . "&background=random&color=fff&size=128";

                                // Si une photo est définie en base
                                if (!empty($photoPath)) {
                                    // On vérifie si c'est une URL externe (ex: http...) ou un fichier local
                                    if (filter_var($photoPath, FILTER_VALIDATE_URL)) {
                                        $avatarUrl = $photoPath;
                                    } 
                                    // Sinon on regarde si le fichier existe dans public/uploads/
                                    elseif (file_exists(__DIR__ . '/../../../public/uploads/' . $photoPath)) {
                                        $avatarUrl = 'uploads/' . $photoPath;
                                    }
                                    // Ou peut-être directement dans public/ (pour les anciens chemins)
                                    elseif (file_exists(__DIR__ . '/../../../public/' . $photoPath)) {
                                        $avatarUrl = $photoPath;
                                    }
                                }
                            ?>
                            <img src="<?= htmlspecialchars($avatarUrl) ?>" alt="Profil" class="profile-pic" style="width: 35px; height: 35px; border-radius: 50%; object-fit: cover;">
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

