<?php
$pageTitle = 'Détails Utilisateur - Administration';
require_once __DIR__ . '/../templates/header.php';
?>

<main class="container" style="padding: 2rem 0; max-width: 800px;">
    
    <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <h1 style="margin: 0; font-size: 1.8rem; color: var(--primary-color);">Fiche Utilisateur</h1>
                <p style="color: #666; font-size: 0.9rem;">ID: #<?= $user['id_utilisateur'] ?></p>
            </div>
            
            <a href="index.php?page=admin_dashboard" class="btn-secondary" style="background: #eee; color: #333; decoration: none; padding: 5px 10px; border-radius: 4px;">&larr; Retour</a>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-top: 2rem;">
            <div>
                <?php 
                    $photoPath = !empty($user['photo']) ? 'assets/uploads/' . $user['photo'] : 'assets/img/default_user.png';
                ?>
                <img src="<?= htmlspecialchars($photoPath) ?>" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid #eee;">
            </div>
            <div>
                <div style="margin-bottom: 10px;">
                    <h2 style="margin: 0; font-size: 1.4rem; color: #333;"><?= htmlspecialchars(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? '')) ?></h2>
                    <small style="color: #666;">@<?= htmlspecialchars($user['pseudo']) ?></small>
                </div>
                <div style="margin-bottom: 10px;">
                    <strong>Email :</strong> <a href="mailto:<?= htmlspecialchars($user['email']) ?>"><?= htmlspecialchars($user['email']) ?></a>
                </div>
                <div style="margin-bottom: 10px;">
                    <strong>Rôle :</strong> 
                    <?php 
                        $roles = [1 => 'Visiteur', 2 => 'Utilisateur', 3 => 'Employé', 4 => 'Administrateur'];
                        echo $roles[$user['id_role']] ?? 'Inconnu';
                    ?>
                </div>
                <div style="margin-bottom: 10px;">
                    <strong>État :</strong>
                    <?php if($user['is_suspended']): ?>
                        <span style="color: #c62828; font-weight: bold;">SUSPENDU</span>
                    <?php else: ?>
                        <span style="color: #2e7d32; font-weight: bold;">ACTIF</span>
                    <?php endif; ?>
                </div>
                 <div style="margin-bottom: 10px;">
                    <strong>Crédits :</strong> <?= $user['credits'] ?>
                </div>
            </div>
        </div>
        
        <div style="margin-top: 2rem; border-top: 1px solid #eee; padding-top: 1rem; text-align: center;">
            <h3 style="margin-bottom: 1rem;">Actions Administratives</h3>
            <?php if($user['id_role'] != 4): ?>
                <?php if($user['is_suspended']): ?>
                    <a href="index.php?page=admin_toggle_suspend&id=<?= $user['id_utilisateur'] ?>&action=reactivate" onclick="return confirm('Réactiver ?')" class="btn-cta" style="background: #2e7d32; text-decoration: none; padding: 10px 20px;">Réactiver le compte</a>
                <?php else: ?>
                    <a href="index.php?page=admin_toggle_suspend&id=<?= $user['id_utilisateur'] ?>&action=suspend" onclick="return confirm('Suspendre ?')" class="btn-cta" style="background: #d32f2f; text-decoration: none; padding: 10px 20px;">Suspendre le compte</a>
                <?php endif; ?>
            <?php else: ?>
                <p style="color: #999;">Impossible de suspendre un administrateur.</p>
            <?php endif; ?>

            <div style="margin-top: 2rem; padding-top: 1rem; border-top: 1px dashed #eee;">
                <h4 style="margin-bottom: 1rem; color: #555;">Gestion des Crédits</h4>
                <form action="index.php?page=admin_update_credits" method="POST" style="display: flex; gap: 10px; justify-content: center; align-items: center;">
                    <input type="hidden" name="user_id" value="<?= $user['id_utilisateur'] ?>">
                    <input type="number" name="credits_amount" placeholder="+/- Montant" required style="padding: 8px; width: 110px; border: 1px solid #ccc; border-radius: 4px; text-align: center;">
                    <button type="submit" class="btn-secondary" style="background: #0288d1; color: white; border: none; padding: 8px 15px; cursor: pointer; border-radius: 4px; font-weight: 500;">
                        Mettre à jour
                    </button>
                </form>
                <small style="color: #888; display: block; margin-top: 5px;">Utilisez une valeur négative pour retirer des crédits.</small>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
        <!-- Avis Reçus -->
        <div>
            <h3>Avis Reçus (<?= count($avisRecus) ?>)</h3>
            <?php if(empty($avisRecus)): ?>
                <p style="color: #999; font-style: italic;">Aucun avis reçu.</p>
            <?php else: ?>
                <?php foreach($avisRecus as $a): ?>
                    <div style="background: white; padding: 1rem; margin-bottom: 1rem; border-radius: 8px; font-size: 0.9rem;">
                        <div style="display: flex; justify-content: space-between;">
                            <strong><?= htmlspecialchars($a['auteur_pseudo']) ?></strong>
                            <span style="color: #ffc107;">★ <?= $a['note'] ?></span>
                        </div>
                        <p style="margin: 5px 0; color: #555;">"<?= htmlspecialchars($a['commentaire']) ?>"</p>
                        <small style="color: #999;"><?= $a['statut'] ?></small>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Avis Émis -->
        <div>
            <h3>Avis Donnés (<?= count($avisDonnes) ?>)</h3>
            <?php if(empty($avisDonnes)): ?>
                <p style="color: #999; font-style: italic;">Aucun avis donné.</p>
            <?php else: ?>
                <?php foreach($avisDonnes as $a): ?>
                    <div style="background: white; padding: 1rem; margin-bottom: 1rem; border-radius: 8px; font-size: 0.9rem;">
                        <div style="display: flex; justify-content: space-between;">
                            <strong>Pour <?= htmlspecialchars($a['destinataire_pseudo']) ?></strong>
                            <span style="color: #ffc107;">★ <?= $a['note'] ?></span>
                        </div>
                        <p style="margin: 5px 0; color: #555;">"<?= htmlspecialchars($a['commentaire']) ?>"</p>
                        <small style="color: #999;"><?= $a['statut'] ?></small>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

</main>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
