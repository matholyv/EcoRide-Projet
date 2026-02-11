<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Employé - EcoRide</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .admin-header { background: #333; color: white; padding: 1rem 0; }
        .admin-nav a { color: #ccc; text-decoration: none; margin-right: 20px; }
        .admin-nav a:hover, .admin-nav a.active { color: white; font-weight: bold; }
        
        .card { background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f9f9f9; font-weight: 600; }
        
        .btn-validate { background: #2e7d32; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; text-decoration: none; font-size: 0.9rem; }
        .btn-reject { background: #c62828; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; text-decoration: none; font-size: 0.9rem; }
    </style>
</head>
<body style="background-color: #f4f6f8;">

    <header class="admin-header">
        <div class="container" style="display: flex; justify-content: space-between; align-items: center;">
            <div style="font-family: 'Outfit', sans-serif; font-weight: 700; font-size: 1.2rem;">EcoRide <span style="font-weight: 400; opacity: 0.8;">| Espace Employé</span></div>
            <nav class="admin-nav">
                <a href="#" class="active">Avis</a>
                <a href="#">Signalements</a>
                <a href="index.php?page=logout" style="color: #ff8a80;">Déconnexion</a>
            </nav>
        </div>
    </header>

    <main class="container" style="padding: 2rem 0;">
        <h1 style="margin-bottom: 2rem;">Modération des Avis</h1>

        <?php if(isset($_GET['success'])): ?>
            <div style="background: #e8f5e9; color: #2e7d32; padding: 1rem; border-radius: 5px; margin-bottom: 2rem;">
                Opération effectuée avec succès.
            </div>
        <?php endif; ?>

        <div class="card">
            <h2 style="font-size: 1.2rem; color: var(--primary-color);">Avis en attente de validation (<?= count($avis) ?>)</h2>
            
            <?php if(empty($avis)): ?>
                <p style="padding: 2rem; text-align: center; color: #666;">Aucun avis à modérer pour le moment. Bon travail ! ☕</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Auteur (Passager)</th>
                            <th>Destinataire (Conducteur)</th>
                            <th>Détails</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($avis as $a): ?>
                        <?php $isLitige = ($a['statut'] === 'LITIGE'); ?>
                        
                        <tr style="<?= $isLitige ? 'background-color: #ffebee; border-left: 5px solid #c62828;' : '' ?>">
                            <td>
                                <?php if($isLitige): ?>
                                    <span style="background:#c62828; color:white; padding:4px 8px; border-radius:4px; font-weight:bold; font-size:0.8rem;">🚨 LITIGE</span>
                                <?php else: ?>
                                    <span style="background:#e0e0e0; color:#333; padding:4px 8px; border-radius:4px; font-size:0.8rem;">Avis</span>
                                <?php endif; ?>
                            </td>
                            
                            <td>
                                <strong><?= htmlspecialchars($a['auteur']) ?></strong><br>
                                <a href="mailto:<?= htmlspecialchars($a['email_auteur']) ?>" style="font-size:0.85rem; color:#666;">✉️ Contacter</a>
                            </td>
                            
                            <td>
                                <?= htmlspecialchars($a['destinataire']) ?><br>
                                <a href="mailto:<?= htmlspecialchars($a['email_destinataire']) ?>" style="font-size:0.85rem; color:#666;">✉️ Contacter</a>
                            </td>
                            
                            <td>
                                <?php if(!$isLitige): ?>
                                    <span style="color: #ffc107; font-weight: bold;">★ <?= $a['note'] ?></span><br>
                                <?php endif; ?>
                                "<i><?= htmlspecialchars($a['commentaire']) ?></i>"
                            </td>
                            
                            <td>
                                <?php if($isLitige): ?>
                                    <div style="display:flex; flex-direction:column; gap:5px;">
                                        <a href="index.php?page=employee_resolve&id=<?= $a['id_avis'] ?>&decision=refund" class="btn-reject" onclick="return confirm('Annuler le paiement et rembourser le passager ?')">↩️ Rembourser Passager</a>
                                        <a href="index.php?page=employee_resolve&id=<?= $a['id_avis'] ?>&decision=pay" class="btn-validate" onclick="return confirm('Rejeter le signalement et payer le conducteur ?')">💰 Payer Conducteur</a>
                                    </div>
                                <?php else: ?>
                                    <a href="index.php?page=employee_validate&id=<?= $a['id_avis'] ?>" class="btn-validate">Valider</a>
                                    <a href="index.php?page=employee_reject&id=<?= $a['id_avis'] ?>" class="btn-reject" onclick="return confirm('Supprimer cet avis ?')">Refuser</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </main>

</body>
</html>
