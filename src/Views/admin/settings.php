<?php
$pageTitle = 'Paramètres Plateforme - Admin EcoRide';
require_once __DIR__ . '/../../templates/header.php';
?>

<main class="container" style="padding: 2rem 0;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1 style="color: var(--primary-color);">Paramètres de la Plateforme</h1>
        <a href="index.php?page=admin_dashboard" class="btn-secondary" style="background: #eee; color: #333; text-decoration: none; padding: 5px 10px; border-radius: 4px;">&larr; Retour au Dashboard</a>
    </div>

    <p style="margin-bottom: 2rem; color: #666;">Modifiez les valeurs globales de l'application.</p>

    <?php if(isset($_GET['success'])): ?>
        <div style="background: #d4edda; color: #155724; padding: 1rem; border-radius: 5px; margin-bottom: 2rem;">
            ✅ Paramètres mis à jour avec succès.
        </div>
    <?php endif; ?>

    <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); max-width: 600px;">
        <form action="index.php?page=admin_update_settings" method="POST">
            
            <?php foreach($params as $p): ?>
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">
                        <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $p['propriete']))) ?>
                    </label>
                    <input type="text" name="params[<?= htmlspecialchars($p['propriete']) ?>]" 
                           value="<?= htmlspecialchars($p['valeur']) ?>" 
                           class="form-control"
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                    
                    <small style="color: #888; display: block; margin-top: 5px;">
                        <?php 
                        if($p['propriete'] == 'commission_trajet') echo "Crédits prélevés sur chaque trajet.";
                        elseif($p['propriete'] == 'credits_inscription') echo "Crédits offerts à l'inscription.";
                        elseif($p['propriete'] == 'email_contact') echo "Email contact affiché sur le site.";
                        ?>
                    </small>
                </div>
            <?php endforeach; ?>

            <button type="submit" class="btn-cta" style="width: 100%; border: none; cursor: pointer; font-size: 1rem; padding: 12px;">Enregistrer les modifications</button>
        </form>
    </div>
</main>

<?php require_once __DIR__ . '/../../templates/footer.php'; ?>
