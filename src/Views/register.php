<?php
$pageTitle = 'Inscription - EcoRide';
require_once __DIR__ . '/templates/header.php';
?>

<main class="container" style="padding: 3rem 0; max-width: 500px;">
    <div class="card" style="background: white; padding: 2.5rem; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
        <h1 style="text-align: center; margin-bottom: 2rem;">Créer un compte</h1>
        
        <?php if(isset($_GET['error'])): ?>
            <div style="background-color: #ffebee; color: #c62828; padding: 1rem; border-radius: 5px; margin-bottom: 1.5rem;">
                <?= htmlspecialchars($_GET['error']) ?>
            </div>
        <?php endif; ?>

        <form action="index.php?page=register_action" method="POST">
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label for="pseudo" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Pseudo</label>
                <input type="text" id="pseudo" name="pseudo" required class="form-input" style="width: 100%; padding: 0.8rem; border: 1px solid #ddd; border-radius: 5px;" value="<?= htmlspecialchars($_GET['pseudo'] ?? '') ?>">
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label for="email" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Email</label>
                <input type="email" id="email" name="email" required class="form-input" style="width: 100%; padding: 0.8rem; border: 1px solid #ddd; border-radius: 5px;" value="<?= htmlspecialchars($_GET['email'] ?? '') ?>">
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label for="date_naissance" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Date de Naissance</label>
                <input type="date" id="date_naissance" name="date_naissance" required class="form-input" style="width: 100%; padding: 0.8rem; border: 1px solid #ddd; border-radius: 5px;">
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label for="password" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Mot de passe</label>
                <input type="password" id="password" name="password" required class="form-input" style="width: 100%; padding: 0.8rem; border: 1px solid #ddd; border-radius: 5px;">
                <small style="color: #666; font-size: 0.8rem;">Min. 8 caractères, 1 Maj, 1 Min, 1 Chiffre, 1 Caractère spécial.</small>
            </div>
            
            <div class="form-group" style="margin-bottom: 2rem;">
                <label for="password_confirm" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Confirmer le mot de passe</label>
                <input type="password" id="password_confirm" name="password_confirm" required class="form-input" style="width: 100%; padding: 0.8rem; border: 1px solid #ddd; border-radius: 5px;">
            </div>

            <button type="submit" class="btn-cta" style="width: 100%; padding: 1rem; border: none; font-size: 1.1rem; cursor: pointer;">
                🚀 S'inscrire (et recevoir 20 crédits)
            </button>
        </form>
        
        <p style="text-align: center; margin-top: 1.5rem;">
            Déjà un compte ? <a href="index.php?page=login" style="color: var(--primary-color);">Se connecter</a>
        </p>
    </div>
</main>

<?php require_once __DIR__ . '/templates/footer.php'; ?>
