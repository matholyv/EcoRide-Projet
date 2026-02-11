<?php
$pageTitle = 'Avis Conducteur - EcoRide';
require_once __DIR__ . '/templates/header.php';
?>

<main class="container" style="max-width: 800px; margin-top: 3rem;">
    
    <?php if(!$driver): ?>
        <div style="text-align:center; padding: 2rem;">Conducteur introuvable.</div>
    <?php else: ?>

        <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); text-align: center; margin-bottom: 2rem;">
            <img src="<?= !empty($driver['photo']) ? $driver['photo'] : 'assets/img/default_user.png' ?>" 
                 alt="Conducteur" 
                 style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; margin-bottom: 1rem;">
            
            <h1 style="margin: 0; font-size: 1.8rem;"><?= htmlspecialchars($driver['pseudo']) ?></h1>
            
            <div style="margin-top: 5px; color: #ffc107; font-size: 1.2rem; font-weight: bold;">
                <?php if($driver['note_moyenne']): ?>
                    ★ <?= number_format($driver['note_moyenne'], 1) ?>/5
                <?php else: ?>
                    <span style="color: #bbb; font-size: 1rem; font-weight: normal;">Aucun avis reçu pour le moment</span>
                <?php endif; ?>
            </div>
            
            <p style="color: #666; font-size: 0.9rem; margin-top: 10px;">
                Membre depuis le <?= date('d/m/Y', strtotime($driver['date_inscription'] ?? 'now')) ?>
            </p>
        </div>

        <h2 style="margin-bottom: 1.5rem;">Avis récents (<?= count($reviews) ?>)</h2>

        <?php if(empty($reviews)): ?>
            <div style="background: #f9f9f9; padding: 2rem; border-radius: 8px; text-align: center; color: #666;">
                Ce conducteur n'a pas encore reçu d'avis.
            </div>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <?php foreach($reviews as $avis): ?>
                    <div style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.03);">
                        <div style="display: flex; gap: 15px; margin-bottom: 10px; align-items: center;">
                            <img src="<?= !empty($avis['auteur_photo']) ? $avis['auteur_photo'] : 'assets/img/default_user.png' ?>" 
                                 alt="Auteur" 
                                 style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                            
                            <div>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <strong><?= htmlspecialchars($avis['auteur']) ?></strong>
                                    <span style="color: #ffc107; font-weight: bold;">★ <?= $avis['note'] ?>/5</span>
                                </div>
                                <span style="font-size: 0.8rem; color: #999;">
                                    Le <?= date('d/m/Y', strtotime($avis['date_avis'] ?? 'now')) ?>
                                </span>
                            </div>
                        </div>
                        
                        <p style="color: #555; margin: 0; line-height: 1.5; font-style: italic;">
                            "<?= htmlspecialchars($avis['commentaire']) ?>"
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    <?php endif; ?>

    <div style="text-align: center; margin-top: 3rem; margin-bottom: 2rem;">
        <a href="javascript:history.back()" style="color: var(--primary-color); text-decoration: none; font-weight: 500;">&larr; Retour au trajet</a>
    </div>

</main>

<?php require_once __DIR__ . '/templates/footer.php'; ?>
