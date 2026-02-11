<?php
$pageTitle = 'Créer un Employé - Administration';
require_once __DIR__ . '/../templates/header.php';
?>

<main class="container" style="padding: 2rem 0; max-width: 500px;">
    
    <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
        <h2 style="margin-bottom: 0.5rem; text-align: center; color: var(--primary-color);">Nouveau Compte Employé 👮</h2>
        <p style="text-align: center; color: #666; margin-bottom: 2rem;">
            Créer un accès pour un membre de l'équipe EcoRide.
        </p>

        <?php if (isset($_GET['error'])): ?>
            <div style="background: #ffebee; color: #c62828; padding: 1rem; border-radius: 5px; margin-bottom: 1.5rem;">
                <?php if ($_GET['error'] == 'email_exists'): ?>
                    Cette adresse email est déjà utilisée.
                <?php elseif ($_GET['error'] == 'empty_fields'): ?>
                    Veuillez remplir tous les champs.
                <?php else: ?>
                    Une erreur est survenue.
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <form action="index.php?page=admin_create_employee_action" method="POST">
            <div class="form-group" style="margin-bottom: 1rem;">
                <label style="font-weight: 500;">Pseudo</label>
                <input type="text" name="pseudo" required class="form-input" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;" placeholder="Ex: Moderateur1">
            </div>
            
            <div class="form-group" style="margin-bottom: 1rem;">
                <label style="font-weight: 500;">Email Professionnel</label>
                <input type="email" name="email" required class="form-input" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;" placeholder="exemple@ecoride.com">
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label style="font-weight: 500;">Mot de passe provisoire</label>
                <input type="password" name="password" required class="form-input" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;" placeholder="********">
                <small style="color: #666; display: block; margin-top: 5px;">Le mot de passe pourra être modifié par l'employé.</small>
            </div>

            <button type="submit" class="btn-cta" style="width: 100%; padding: 12px; font-size: 1rem; border: none; cursor: pointer;">
                Créer l'employé
            </button>
            
            <div style="text-align: center; margin-top: 1.5rem;">
                <a href="index.php?page=admin_dashboard" style="color: #666; text-decoration: none;">Annuler et Retour au Dashboard</a>
            </div>
        </form>
    </div>

</main>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
