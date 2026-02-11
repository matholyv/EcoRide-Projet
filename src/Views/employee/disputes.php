<?php
$pageTitle = 'Gestion des Litiges - Espace Employé';
require_once __DIR__ . '/../templates/header.php';
?>

<main class="container" style="padding: 2rem 0;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1 style="color: var(--primary-color);">Gestion des Litiges</h1>
        <div style="display: flex; gap: 10px;">
            <a href="index.php?page=employee_reviews" class="btn-secondary" style="background: #eee; color: #333;">Modération Avis</a>
            <a href="index.php?page=employee_disputes" class="btn-cta" style="background: var(--primary-color);">Dossiers Litiges</a>
        </div>
    </div>

    <!-- Alertes de succès/erreur -->
    <?php if (isset($_GET['success'])): ?>
        <div style="background: #e8f5e9; color: #2e7d32; padding: 1rem; border-radius: 5px; margin-bottom: 1.5rem;">
            <?= $_GET['success'] == 'resolved' ? 'Litige résolu avec succès !' : 'Opération réussie.' ?>
        </div>
    <?php endif; ?>
    <?php if (isset($_GET['error'])): ?>
        <div style="background: #ffebee; color: #c62828; padding: 1rem; border-radius: 5px; margin-bottom: 1.5rem;">
            <?= $_GET['error'] == 'not_found' ? 'Litige introuvable.' : 'Une erreur technique est survenue.' ?>
        </div>
    <?php endif; ?>

    <!-- Liste des Litiges en Cours -->
    <h2 style="color: #d32f2f;">⚠ Litiges en cours (<?= count($disputes) ?>)</h2>
    
    <?php if (empty($disputes)): ?>
        <p style="text-align: center; color: #666; padding: 2rem; background: white; border-radius: 10px; border: 1px dashed #ddd;">
            Aucun litige signalé. Tout va bien ! 🎉
        </p>
    <?php else: ?>
        <div style="display: grid; gap: 1.5rem;">
            <?php foreach ($disputes as $d): ?>
                <div style="background: white; padding: 1.5rem; border-radius: 10px; border-left: 5px solid #d32f2f; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                        <div>
                            <strong>Plaignant (Passager) :</strong> <?= htmlspecialchars($d['auteur']) ?> (<?= htmlspecialchars($d['email_auteur']) ?>)<br>
                            <strong>Accusé (Conducteur) :</strong> <?= htmlspecialchars($d['destinataire']) ?> (<?= htmlspecialchars($d['email_destinataire']) ?>)
                        </div>
                        <div style="text-align: right;">
                            <strong>Montant bloqué :</strong> <span style="font-size: 1.2rem; color: var(--primary-color);"><?= $d['prix_personne'] ?> Crédits</span>
                        </div>
                    </div>
                    
                    <div style="background: #fff3e0; padding: 1rem; border-radius: 5px; margin-bottom: 1rem;">
                        <strong>Plainte :</strong> "<?= nl2br(htmlspecialchars($d['commentaire'])) ?>"
                    </div>
                    
                    <div style="display: flex; gap: 10px;">
                        <a href="index.php?page=employee_resolve_conflict&id=<?= $d['id_avis'] ?>&decision=refund" 
                           class="btn-cta" 
                           style="flex: 1; text-align: center; background-color: #ef6c00;"
                           onclick="return confirm('Confirmer le remboursement du passager ?\nLe conducteur ne recevra rien.');">
                           ↩️ Rembourser Passager
                        </a>
                        <a href="index.php?page=employee_resolve_conflict&id=<?= $d['id_avis'] ?>&decision=pay" 
                           class="btn-cta" 
                           style="flex: 1; text-align: center; background-color: #2e7d32;"
                           onclick="return confirm('Confirmer le paiement au conducteur ?\nLa plainte sera classée sans suite.');">
                           💰 Payer Conducteur
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Historique Récents -->
    <h3 style="margin-top: 3rem; color: #666;">Historique des résolutions (10 derniers)</h3>
    <table style="width: 100%; border-collapse: collapse; background: white; margin-top: 1rem;">
        <thead>
            <tr style="background: #f4f4f4; text-align: left;">
                <th style="padding: 10px;">Date</th>
                <th style="padding: 10px;">Passager</th>
                <th style="padding: 10px;">Conducteur</th>
                <th style="padding: 10px;">Décision</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($history as $h): ?>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 10px; color: #666;"><?= date('d/m/Y', strtotime($h['date_avis'])) ?></td>
                    <td style="padding: 10px;"><?= htmlspecialchars($h['auteur']) ?></td>
                    <td style="padding: 10px;"><?= htmlspecialchars($h['destinataire']) ?></td>
                    <td style="padding: 10px;">
                        <?php if ($h['statut_final'] == 'REMBOURSÉ'): ?>
                            <span style="color: #ef6c00; font-weight: bold;">Remboursé</span>
                        <?php elseif ($h['statut_final'] == 'VALIDÉ'): ?>
                            <span style="color: #2e7d32; font-weight: bold;">Payé (Validé)</span>
                        <?php else: ?>
                            <?= htmlspecialchars($h['statut_final']) ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</main>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
