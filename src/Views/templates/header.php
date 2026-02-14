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
                                
                                // URL par défaut
                                $avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode($pseudo) . "&background=random&color=fff&size=128";

                                if (!empty($photoPath)) {
                                    if (filter_var($photoPath, FILTER_VALIDATE_URL)) { $avatarUrl = $photoPath; } 
                                    elseif (file_exists(__DIR__ . '/../../../public/uploads/' . $photoPath)) { $avatarUrl = 'uploads/' . $photoPath; }
                                    elseif (file_exists(__DIR__ . '/../../../public/' . $photoPath)) { $avatarUrl = $photoPath; }
                                }

                                // Calculer notifs ici
                                $pendingCount = 0;
                                if (isset($_SESSION['user']['id'])) {
                                    if (class_exists('Covoiturage')) {
                                        $pendingCount = Covoiturage::countPendingValidations($_SESSION['user']['id']);
                                    } else {
                                        require_once __DIR__ . '/../../Models/Covoiturage.php';
                                        $pendingCount = Covoiturage::countPendingValidations($_SESSION['user']['id']);
                                    }
                                }
                            ?>
                            <div style="position: relative;">
                                <img src="<?= htmlspecialchars($avatarUrl) ?>" alt="Profil" class="profile-pic" style="width: 35px; height: 35px; border-radius: 50%; object-fit: cover;">
                                <?php if($pendingCount > 0): ?>
                                    <span style="position: absolute; top: -2px; right: -2px; width: 10px; height: 10px; background-color: #d32f2f; border-radius: 50%; border: 2px solid white;"></span>
                                <?php endif; ?>
                            </div>
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
                                
                                <?php
                                    // Notif badge
                                    $pendingCount = 0;
                                    if (class_exists('Covoiturage')) {
                                        $pendingCount = Covoiturage::countPendingValidations($_SESSION['user']['id']);
                                    } else {
                                        // Fallback si la classe n'est pas chargée (rare mais possible selon l'include)
                                        require_once __DIR__ . '/../../Models/Covoiturage.php';
                                        $pendingCount = Covoiturage::countPendingValidations($_SESSION['user']['id']);
                                    }
                                ?>
                                <a href="index.php?page=history" style="display: flex; justify-content: space-between; align-items: center;">
                                    Mes Trajets
                                    <?php if($pendingCount > 0): ?>
                                        <span style="background: #d32f2f; color: white; padding: 2px 6px; border-radius: 50%; font-size: 0.75rem; font-weight: bold;"><?= $pendingCount ?></span>
                                    <?php endif; ?>
                                </a>

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

