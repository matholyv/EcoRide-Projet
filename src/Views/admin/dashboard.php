<?php
$pageTitle = 'Tableau de Bord Administrateur - EcoRide';
require_once __DIR__ . '/../templates/header.php';
?>

<div style="background: #f4f6f9; min-height: 100vh; padding-bottom: 3rem;">
<main class="container" style="padding: 2rem 0;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1 style="color: var(--primary-color); margin:0;">Administration EcoRide 👑</h1>
        <div style="display: flex; gap: 10px;">
            <a href="index.php?page=admin_create_employee" class="btn-cta" style="background: var(--secondary-color);">+ Créer Employé</a>
            <!-- <a href="index.php?page=admin_settings" class="btn-secondary" style="background: #eee; color: #333;">⚙️ Paramètres</a> -->
        </div>
    </div>

    <!-- KPI Section (Chiffres clés) -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
        <div style="background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-left: 5px solid var(--primary-color);">
            <div style="font-size: 0.9rem; color: #666; text-transform: uppercase; font-weight: 600;">Utilisateurs Inscrits</div>
            <div style="font-size: 2.5rem; font-weight: bold; color: var(--dark-color);"><?= $stats['nb_users'] ?? 0 ?></div>
        </div>
        <div style="background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-left: 5px solid var(--secondary-color);">
            <div style="font-size: 0.9rem; color: #666; text-transform: uppercase; font-weight: 600;">Covoiturages Terminés</div>
            <div style="font-size: 2.5rem; font-weight: bold; color: var(--dark-color);"><?= $stats['nb_rides'] ?? 0 ?></div>
        </div>
        <div style="background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-left: 5px solid #ff9800;">
            <div style="font-size: 0.9rem; color: #666; text-transform: uppercase; font-weight: 600;">Revenu Estimé (Crédits)</div>
            <h2 style="font-size: 2.5rem; margin: 0; color: #ff9800;">
                <?= number_format($stats['total_credits'], 0, ',', ' ') ?> <small style="font-size: 1rem; color: #666;">cr</small>
            </h2>
        </div>
    </div>

    <!-- Graphique (Si US 6 demande un graph) -->
    <!-- On va simuler un graph avec des barres CSS simple pour l'instant -->
    <!-- Graphique 1 : Covoiturages par jour -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 3rem;">
        <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
            <h2 style="margin-bottom: 2rem;">🚗 Covoiturages par jour</h2>
            <?php if(empty($graphData)): ?>
                <p style="text-align:center; color:#666;">Pas assez de données.</p>
            <?php else: ?>
                <div style="display: flex; align-items: flex-end; justify-content: space-around; height: 200px; padding-top: 20px;">
                    <?php 
                        $maxVal = 0;
                        foreach($graphData as $d) if($d['count'] > $maxVal) $maxVal = $d['count'];
                        if($maxVal == 0) $maxVal = 1; 
                    ?>
                    <?php foreach($graphData as $d): ?>
                        <div style="text-align: center; flex: 1; display: flex; flex-direction: column; justify-content: flex-end; align-items: center; height: 100%;">
                            <div style="background: var(--secondary-color); width: 30px; border-radius: 5px 5px 0 0; transition: height 0.5s; height: <?= ($d['count'] / $maxVal) * 100 ?>%;">
                                 <div style="color: white; font-weight: bold; padding-top: 5px; font-size: 0.8rem;"><?= $d['count'] ?></div>
                            </div>
                            <div style="margin-top: 10px; font-size: 0.8rem; color: #666;"><?= date('d/m', strtotime($d['date_depart'])) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Graphique 2 : Revenus par jour -->
        <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
            <h2 style="margin-bottom: 2rem;">💰 Revenus (Crédits) par jour</h2>
            <?php if(empty($graphData)): ?>
                <p style="text-align:center; color:#666;">Pas assez de données.</p>
            <?php else: ?>
                <div style="display: flex; align-items: flex-end; justify-content: space-around; height: 200px; padding-top: 20px;">
                    <?php 
                        $maxRev = 0;
                        foreach($graphData as $d) if($d['revenue'] > $maxRev) $maxRev = $d['revenue'];
                        if($maxRev == 0) $maxRev = 1; 
                    ?>
                    <?php foreach($graphData as $d): ?>
                        <div style="text-align: center; flex: 1; display: flex; flex-direction: column; justify-content: flex-end; align-items: center; height: 100%;">
                            <div style="background: #ff9800; width: 30px; border-radius: 5px 5px 0 0; transition: height 0.5s; height: <?= ($d['revenue'] / $maxRev) * 100 ?>%;">
                                 <div style="color: white; font-weight: bold; padding-top: 5px; font-size: 0.8rem;"><?= number_format($d['revenue'], 0) ?></div>
                            </div>
                            <div style="margin-top: 10px; font-size: 0.8rem; color: #666;"><?= date('d/m', strtotime($d['date_depart'])) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Gestion Utilisateurs (Dashboard principal pour Admin) -->
    <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h2 style="margin: 0;">Gestion des Utilisateurs</h2>
            <form action="index.php" method="GET" style="display: flex; gap: 10px;">
                <input type="hidden" name="page" value="admin_dashboard">
                <input type="text" name="search" placeholder="Rechercher (Pseudo, Email)..." style="padding: 10px; border: 1px solid #ddd; border-radius: 5px; width: 250px;">
                <button type="submit" class="btn-cta" style="border: none; cursor: pointer;">🔍</button>
            </form>
        </div>

        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f9f9f9; text-align: left; border-bottom: 2px solid #eee;">
                    <th style="padding: 1rem;">ID</th>
                    <th style="padding: 1rem;">Pseudo</th>
                    <th style="padding: 1rem;">Email</th>
                    <th style="padding: 1rem;">Rôle</th>
                    <th style="padding: 1rem;">Crédits</th>
                    <th style="padding: 1rem;">État</th>
                    <th style="padding: 1rem; text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($users as $u): ?>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 1rem; color: #666;"><?= $u['id_utilisateur'] ?></td>
                        <td style="padding: 1rem; font-weight: 500;">
                            <?= htmlspecialchars($u['pseudo']) ?>
                        </td>
                        <td style="padding: 1rem;"><?= htmlspecialchars($u['email']) ?></td>
                        <td style="padding: 1rem;">
                            <?php if($u['id_role'] == 4): ?> <span style="color: purple; font-weight: bold;">👑 Admin</span>
                            <?php elseif($u['id_role'] == 3): ?> <span style="color: blue; font-weight: bold;">👮 Employé</span>
                            <?php elseif($u['id_role'] == 2): ?> <span>Utilisateur</span>
                            <?php else: ?> <span>Visiteur</span> <?php endif; ?>
                        </td>
                        <td style="padding: 1rem;"><?= $u['credits'] ?> pts</td>
                        <td style="padding: 1rem;">
                            <?php if($u['is_suspended']): ?>
                                <span style="background: #ffebee; color: #c62828; padding: 5px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: bold;">SUSPENDU</span>
                            <?php else: ?>
                                <span style="background: #e8f5e9; color: #2e7d32; padding: 5px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: bold;">ACTIF</span>
                            <?php endif; ?>
                        </td>
                        <td style="padding: 1rem; text-align: right;">
                            <a href="index.php?page=admin_user_detail&id=<?= $u['id_utilisateur'] ?>" class="btn-secondary" style="background: #eee; color: #333; padding: 5px 10px; font-size: 0.9rem; text-decoration: none; border-radius: 4px;">Détails</a>
                            
                            <?php if($u['id_role'] != 4): // On ne suspend pas un admin ?>
                                <?php if($u['is_suspended']): ?>
                                    <a href="index.php?page=admin_toggle_suspend&id=<?= $u['id_utilisateur'] ?>&action=reactivate" 
                                       onclick="return confirm('Réactiver ce compte ?')"
                                       style="color: #2e7d32; font-weight: bold; margin-left: 10px; text-decoration: none;">
                                       ✅ Réactiver
                                    </a>
                                <?php else: ?>
                                    <a href="index.php?page=admin_toggle_suspend&id=<?= $u['id_utilisateur'] ?>&action=suspend" 
                                       onclick="return confirm('Suspendre ce compte ? L\'utilisateur ne pourra plus se connecter.')"
                                       style="color: #d32f2f; font-weight: bold; margin-left: 10px; text-decoration: none;">
                                       🚫 Suspendre
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</main>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
