<?php
$pageTitle = 'Connexion - EcoRide';
require_once __DIR__ . '/templates/header.php';
?>

<main class="container" style="display:flex; justify-content:center; align-items:center; height:80vh;">
    <div style="background:white; padding:2rem; border-radius:10px; box-shadow:0 10px 25px rgba(0,0,0,0.1); width:100%; max-width:400px;">
        <h2 style="text-align:center; margin-bottom:1.5rem;">Connexion</h2>
        
        <?php if(isset($_GET['success'])): ?>
            <div style="background:#e8f5e9; color:#2e7d32; padding:10px; border-radius:5px; margin-bottom:1rem; text-align:center;">
                <?= htmlspecialchars($_GET['success']) ?>
            </div>
        <?php endif; ?>

        <?php if(isset($error)): ?>
            <p style="color:red; text-align:center; background:#fee; padding:10px; border-radius:5px; margin-bottom:1rem;">
                <?= $error ?>
            </p>
        <?php endif; ?>
        
        <?php if(isset($_GET['error'])): ?>
             <p style="color:red; text-align:center; background:#fee; padding:10px; border-radius:5px; margin-bottom:1rem;">
                <?= htmlspecialchars($_GET['error']) ?>
            </p>
        <?php endif; ?>

        <form action="index.php?page=login" method="POST">
            <div style="margin-bottom:1rem;">
                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($email ?? $_GET['email'] ?? '') ?>" placeholder="exemple@email.com" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;" required>
            </div>
            <div style="margin-bottom:1.5rem;">
                <label>Mot de passe</label>
                <input type="password" name="password" value="" placeholder="********" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;" required>
            </div>
            <button type="submit" class="btn-cta" style="width: 100%; padding: 1rem; border: none; font-size: 1.1rem; cursor: pointer;">
                Se connecter
            </button>
        </form>
        
        <p style="text-align: center; margin-top: 1.5rem;">
            Pas encore de compte ? <a href="index.php?page=register" style="color: var(--primary-color); font-weight: bold;">Créer un compte</a>
        </p>
    </div>
</main>

<?php require_once __DIR__ . '/templates/footer.php'; ?>
