<?php
$pageTitle = 'Gestion des Litiges - Espace Employé';
require_once __DIR__ . '/../templates/header.php';
?>

<main style="width: 95%; max-width: 1600px; margin: 0 auto; padding: 2rem 0;">
    <h1 style="color: var(--primary-color); margin-bottom: 1.5rem;">Gestion des Litiges</h1>
    
    <div style="display: flex; gap: 20px; margin-bottom: 2rem; border-bottom: 2px solid #eee; padding-bottom: 1px;">
        <a href="index.php?page=employee_reviews" style="padding: 10px 20px; text-decoration: none; color: #666; font-weight: 500;">
            📝 Valider Avis
        </a>
        <a href="index.php?page=employee_disputes" style="padding: 10px 20px; text-decoration: none; color: #d32f2f; border-bottom: 3px solid #d32f2f; font-weight: bold;">
            ⚖️ Gérer Litiges (Actif)
        </a>
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
        <div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem; width: 100%;">
            <?php foreach ($disputes as $d): ?>
                <div style="background: white; padding: 2rem; border-radius: 10px; border-left: 5px solid #d32f2f; box-shadow: 0 4px 10px rgba(0,0,0,0.05); width: 100%; box-sizing: border-box;">
                    <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem;">
                        <div style="flex: 1; margin-right: 20px;">
                            <strong>Plaignant (Passager) :</strong> <br><?= htmlspecialchars($d['auteur']) ?> (<?= htmlspecialchars($d['email_auteur']) ?>)<br>
                            <span style="display:block; margin-top:5px;"><strong>Accusé (Conducteur) :</strong> <br><?= htmlspecialchars($d['destinataire']) ?> (<?= htmlspecialchars($d['email_destinataire']) ?>)</span>
                        </div>
                        <div style="text-align: right; white-space: nowrap;">
                            <strong>Montant bloqué :</strong><br> 
                            <span style="font-size: 1.4rem; font-weight: bold; color: var(--primary-color); display: block; margin-top: 5px;"><?= $d['prix_personne'] ?> Crédits</span>
                        </div>
                    </div>
                    
                    <div style="background: #f9f9f9; padding: 1rem; border-radius: 5px; margin-bottom: 1rem; border: 1px solid #eee;">
                        <h4 style="margin: 0 0 0.5rem 0; color: #555;">Info Trajet #<?= $d['id_covoiturage'] ?></h4>
                        <p style="margin: 0;">
                            <strong><?= htmlspecialchars($d['lieu_depart']) ?> ➝ <?= htmlspecialchars($d['lieu_arrivee']) ?></strong><br>
                            📅 <?= date('d/m/Y', strtotime($d['date_depart'])) ?> à <?= date('H:i', strtotime($d['heure_depart'])) ?>
                        </p>
                    </div>
                    
                    <div style="background: #fff3e0; padding: 1rem; border-radius: 5px; margin-bottom: 1rem;">
                        <strong>Plainte :</strong> "<?= nl2br(htmlspecialchars($d['commentaire'])) ?>"
                    </div>
                    
                    <div style="display: flex; gap: 10px; margin-top: 1rem;">
                        <!-- Boutons CONTACTER (MailTo pré-rempli) -->
                        <a href="mailto:<?= htmlspecialchars($d['email_auteur']) ?>?subject=EcoRide - Litige #<?= $d['id_avis'] ?>&body=Bonjour <?= htmlspecialchars($d['auteur']) ?>," 
                           class="btn-secondary" style="flex: 1; text-align: center; border: 1px solid #ccc; background: white; color: #333; text-decoration: none; padding: 10px;">
                           ✉️ Contacter Passager
                        </a>
                        <a href="mailto:<?= htmlspecialchars($d['email_destinataire']) ?>?subject=EcoRide - Litige #<?= $d['id_avis'] ?>&body=Bonjour <?= htmlspecialchars($d['destinataire']) ?>," 
                           class="btn-secondary" style="flex: 1; text-align: center; border: 1px solid #ccc; background: white; color: #333; text-decoration: none; padding: 10px;">
                           ✉️ Contacter Conducteur
                        </a>
                    </div>
                    
                    <div style="display: flex; gap: 10px; margin-top: 10px;">
                        <button onclick="openConfirmModal('index.php?page=employee_resolve_conflict&id=<?= $d['id_avis'] ?>&decision=refund', 'Rembourser le passager ?\nLe conducteur ne recevra rien.')" 
                           class="btn-cta" 
                           style="flex: 1; text-align: center; background-color: #ef6c00; border: none; cursor: pointer; padding: 10px; color: white; font-size: 1rem;">
                           ↩️ Rembourser Passager
                        </button>
                        <button onclick="openConfirmModal('index.php?page=employee_resolve_conflict&id=<?= $d['id_avis'] ?>&decision=pay', 'Payer le conducteur ?\nLa plainte sera classée sans suite.')" 
                           class="btn-cta" 
                           style="flex: 1; text-align: center; background-color: #2e7d32; border: none; cursor: pointer; padding: 10px; color: white; font-size: 1rem;">
                           💰 Payer Conducteur
                        </button>
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

<!-- Modale de confirmation (Style Windows modernisé) -->
<div id="confirmationModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; justify-content:center; align-items:center;">
    <div style="background:white; padding:2rem; border-radius:12px; width:90%; max-width:400px; text-align:center; box-shadow:0 10px 25px rgba(0,0,0,0.2); animation: fadeIn 0.2s ease-out;">
        <h3 style="margin-top:0; color:var(--dark-color); margin-bottom: 1rem;">Confirmation</h3>
        <p id="modalMessage" style="color:#666; margin-bottom:2rem; font-size: 1.1rem; line-height: 1.5;"></p>
        
        <div style="display:flex; gap:10px; justify-content:center;">
            <button onclick="closeModal()" style="padding:10px 20px; border:1px solid #ccc; background:white; border-radius:5px; cursor:pointer; font-size:1rem;">Annuler</button>
            <a id="confirmBtn" href="#" style="padding:10px 20px; background:var(--primary-color); color:white; border:none; border-radius:5px; cursor:pointer; text-decoration:none; font-size:1rem; font-weight:bold;">Confirmer</a>
        </div>
    </div>
</div>

<script>
function openConfirmModal(url, message) {
    document.getElementById('modalMessage').innerText = message;
    document.getElementById('confirmBtn').href = url;
    document.getElementById('confirmationModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('confirmationModal').style.display = 'none';
}

// Fermer si clic en dehors
window.onclick = function(event) {
    var modal = document.getElementById('confirmationModal');
    if (event.target == modal) {
        closeModal();
    }
}
</script>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
