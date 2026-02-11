<?php
$pageTitle = 'Modération des Avis - Espace Employé';
require_once __DIR__ . '/../templates/header.php';
?>

<main class="container" style="padding: 2rem 0;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1 style="color: var(--primary-color);">Espace Modération</h1>
        <div style="display: flex; gap: 10px;">
            <a href="index.php?page=employee_reviews" class="btn-cta" style="background: var(--primary-color);">Valider Avis</a>
            <a href="index.php?page=employee_disputes" class="btn-secondary" style="background: #eee; color: #333; padding: 0.5rem 1rem; border-radius: 5px;">Gérer Litiges</a>
        </div>
    </div>

    <h2>Avis en attente de validation (<?= count($avis) ?>)</h2>
    
    <?php if (isset($_GET['success'])): ?>
        <div style="background: #e8f5e9; color: #2e7d32; padding: 1rem; border-radius: 5px; margin-bottom: 1.5rem;">
            <?php if ($_GET['success'] == 'validated'): ?>
                Avis validé avec succès !
            <?php elseif ($_GET['success'] == 'rejected'): ?>
                Avis refusé et supprimé.
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if (empty($avis)): ?>
        <p style="text-align: center; color: #666; padding: 3rem; background: white; border-radius: 10px;">
            ✅ Aucun avis en attente. Tout est à jour !
        </p>
    <?php else: ?>
        <div style="display: grid; gap: 1.5rem;">
            <?php foreach ($avis as $a): ?>
                <div style="background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); display: flex; flex-direction: column; gap: 10px;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <div>
                            <strong>De :</strong> <?= htmlspecialchars($a['auteur']) ?> (<?= htmlspecialchars($a['email_auteur']) ?>)<br>
                            <strong>Pour :</strong> <?= htmlspecialchars($a['destinataire']) ?>
                        </div>
                        <div style="color: #ffc107; font-weight: bold; font-size: 1.2rem;">
                            ★ <?= $a['note'] ?>/5
                        </div>
                    </div>
                    
                    <div style="background: #f9f9f9; padding: 1rem; border-radius: 5px; font-style: italic; color: #555;">
                        "<?= nl2br(htmlspecialchars($a['commentaire'])) ?>"
                    </div>
                    
                    <div style="display: flex; gap: 10px; margin-top: 10px;">
                        <a href="index.php?page=employee_validate_review&id=<?= $a['id_avis'] ?>" class="btn-cta" style="flex: 1; text-align: center; background-color: #2e7d32;">Valider ✅</a>
                        <a href="index.php?page=employee_reject_review&id=<?= $a['id_avis'] ?>" class="btn-cta" style="flex: 1; text-align: center; background-color: #d32f2f;" onclick="return confirm('Êtes-vous sûr de vouloir refuser cet avis ?');">Refuser 🗑️</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
