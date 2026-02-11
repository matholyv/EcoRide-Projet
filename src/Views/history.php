<?php
$pageTitle = 'Mes Trajets - EcoRide';
ob_start();
?>
<style>
    .tabs {
        display: flex;
        gap: 20px;
        margin-bottom: 2rem;
        border-bottom: 1px solid #ddd;
    }
    
    .tab {
        padding: 1rem 2rem;
        cursor: pointer;
        font-weight: 600;
        color: #666;
        border-bottom: 3px solid transparent;
    }
    
    .tab.active {
        color: var(--primary-color);
        border-bottom-color: var(--primary-color);
    }
    
    .history-card {
        background: white;
        padding: 1.5rem;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        margin-bottom: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .status-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
    }
    
    .status-planifie { background: #e3f2fd; color: #1565c0; }
    .status-termine { background: #e8f5e9; color: #2e7d32; }
    .status-annule { background: #ffebee; color: #c62828; }
    .status-cours { background: #fff3e0; color: #ef6c00; }

    @media (max-width: 768px) {
        .history-card { flex-direction: column; align-items: flex-start; }
        .history-card > div:last-child { width: 100%; border-top: 1px solid #eee; margin-top: 10px; padding-top: 10px; display: flex; justify-content: space-between; align-items: center; }
    }
</style>
<?php
$extraStyles = ob_get_clean();
require_once __DIR__ . '/templates/header.php';
?>

<main class="container" style="padding: 2rem 0;">
    <h1>Mes Trajets (Historique)</h1>
    
    <?php 
        $activeTab = isset($_GET['tab']) && $_GET['tab'] === 'driver' ? 'driver' : 'passenger';
    ?>

    <div class="tabs">
        <div id="tab-pass" class="tab <?= $activeTab === 'passenger' ? 'active' : '' ?>" onclick="switchTab('passenger')">Je voyage (Passager)</div>
        <div id="tab-driver" class="tab <?= $activeTab === 'driver' ? 'active' : '' ?>" onclick="switchTab('driver')">Je conduis (Conducteur)</div>
    </div>

    <div id="passenger-list" style="display: <?= $activeTab === 'passenger' ? 'block' : 'none' ?>;">
        <?php if(empty($passengerRides)): ?>
            <p style="text-align: center; color: #666; padding: 2rem;">Vous n'avez pas encore réservé de trajet.</p>
            <div style="text-align: center;">
                <a href="index.php?page=covoiturages" class="btn-cta">Rechercher un trajet</a>
            </div>
        <?php else: ?>
            <?php foreach($passengerRides as $ride): ?>
                <div class="history-card">
                    <div>
                        <div style="font-weight: bold; font-size: 1.1rem;">
                            <?= date('d/m/Y', strtotime($ride['date_depart'])) ?> · <?= date('H:i', strtotime($ride['heure_depart'])) ?> - <?= date('H:i', strtotime($ride['heure_arrivee'])) ?>
                            <br>
                            <span style="font-size:0.9em; font-weight:normal; color:#555;">(<?= (new DateTime($ride['date_depart'].' '.$ride['heure_depart']))->diff(new DateTime($ride['date_arrivee'].' '.$ride['heure_arrivee']))->format('%hh%I') ?>)</span>
                        </div>
                        <div style="margin: 5px 0;">
                            <?= htmlspecialchars($ride['lieu_depart']) ?> ➝ <?= htmlspecialchars($ride['lieu_arrivee']) ?>
                        </div>
                        <div style="font-size: 0.9rem; color: #666;">
                            Avec <?= htmlspecialchars($ride['conducteur_pseudo']) ?> · 
                            <span class="status-badge status-<?= strtolower($ride['statut'] == 'EN COURS' ? 'cours' : ($ride['statut'] == 'TERMINÉ' ? 'termine' : 'planifie')) ?>">
                                <?= htmlspecialchars($ride['statut']) ?>
                            </span>
                        </div>
                    </div>
                    <div>
                        <span style="font-weight: bold; color: var(--primary-color);"><?= $ride['prix_personne'] ?> Crédits</span>
                        <div style="margin-top: 5px; text-align: right;">
                        <?php if($ride['statut'] == 'TERMINÉ'): ?>
                            <?php if($ride['statut_participation'] == 'VALIDÉ'): ?>
                                <span style="color: green; font-size: 0.9rem;">✅ Validé</span>
                            <?php elseif($ride['statut_participation'] == 'LITIGE'): ?>
                                <span style="color: #d32f2f; font-size: 0.9rem;">⚠ Litige signalé</span>
                            <?php elseif($ride['statut_participation'] == 'REMBOURSÉ'): ?>
                                <span style="color: #d32f2f; font-size: 0.9rem;">↩️ Remboursé</span>
                            <?php else: ?>
                                <a href="index.php?page=review&id=<?= $ride['id_covoiturage'] ?>" class="btn-secondary" style="font-size: 0.9rem; padding: 5px 10px; border-radius: 4px;">
                                   Valider le trajet
                                </a>
                            <?php endif; ?>
                        <?php elseif($ride['statut'] == 'PLANIFIÉ' && $ride['statut_participation'] != 'ANNULÉ'): ?>
                             <button onclick="openCancelModal('index.php?page=cancel&id=<?= $ride['id_covoiturage'] ?>&role=passenger', 'Voulez-vous vraiment annuler votre réservation ?\nVous serez remboursé.')" style="font-size: 0.9rem; padding: 5px 10px; background-color: #d32f2f; color: white; border:none; border-radius: 4px; cursor: pointer;">
                               Annuler
                             </button>
                        <?php elseif($ride['statut'] == 'ANNULÉ' || $ride['statut_participation'] == 'ANNULÉ' || $ride['statut_participation'] == 'ANNULÉ_PAR_CONDUCTEUR'): ?>
                            <span style="color: #999; font-style: italic;">Annulé</span>
                        <?php else: ?>
                            <a href="index.php?page=detail&id=<?= $ride['id_covoiturage'] ?>" class="btn-secondary" style="font-size: 0.9rem; padding: 5px 10px; border-radius: 4px;">Détails</a>
                        <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div id="driver-list" style="display: <?= $activeTab === 'driver' ? 'block' : 'none' ?>;">
        <?php if(empty($driverRides)): ?>
            <p style="text-align: center; color: #666; padding: 2rem;">Vous n'avez publié aucun trajet.</p>
            <div style="text-align: center;">
                <a href="index.php?page=publish" class="btn-cta">Publier un trajet</a>
            </div>
        <?php else: ?>
            <?php foreach($driverRides as $ride): ?>
                <div class="history-card">
                    <div>
                        <div style="font-weight: bold; font-size: 1.1rem;">
                            <?= date('d/m/Y', strtotime($ride['date_depart'])) ?> · <?= date('H:i', strtotime($ride['heure_depart'])) ?> - <?= date('H:i', strtotime($ride['heure_arrivee'])) ?>
                            <br>
                            <span style="font-size:0.9em; font-weight:normal; color:#555;">(<?= (new DateTime($ride['date_depart'].' '.$ride['heure_depart']))->diff(new DateTime($ride['date_arrivee'].' '.$ride['heure_arrivee']))->format('%hh%I') ?>)</span>
                        </div>
                        <div style="margin: 5px 0;">
                            <?= htmlspecialchars($ride['lieu_depart']) ?> ➝ <?= htmlspecialchars($ride['lieu_arrivee']) ?>
                        </div>
                        <div style="font-size: 0.9rem; color: #666;">
                            <?= htmlspecialchars($ride['marque']) ?> · 
                             <span class="status-badge status-<?= strtolower($ride['statut'] == 'EN COURS' ? 'cours' : ($ride['statut'] == 'TERMINÉ' ? 'termine' : 'planifie')) ?>">
                                <?= htmlspecialchars($ride['statut']) ?>
                            </span>
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <div style="margin-bottom: 5px;"><?= $ride['nb_participants'] ?> / <?= ($ride['nb_place'] + $ride['nb_participants']) ?> inscrits</div>
                        
                        <?php if($ride['statut'] == 'PLANIFIÉ'): ?>
                            <div style="display: flex; gap: 5px; align-items: center; justify-content: flex-end;">
                                <a href="index.php?page=detail&id=<?= $ride['id_covoiturage'] ?>" class="btn-secondary" style="background: #eee; color: #333; padding: 5px 10px; border-radius: 4px; text-decoration: none; font-size: 0.9rem;">Détails</a>
                                <button onclick="openCancelModal('index.php?page=cancel&id=<?= $ride['id_covoiturage'] ?>&role=driver', 'Attention ! Cela annulera le trajet et remboursera tous les passagers.\nContinuer ?')" style="padding: 5px 10px; background: #666; color: white; border:none; border-radius: 4px; cursor:pointer; font-size: 0.9rem;">🚫 Annuler</button>
                                <form action="index.php?page=start&id=<?= $ride['id_covoiturage'] ?>" method="POST" style="margin:0;">
                                    <button type="submit" class="btn-cta" style="font-size: 0.9rem; padding: 5px 10px; border:none; cursor:pointer;">
                                       ▶ Démarrer
                                    </button>
                                </form>
                            </div>
                        <?php elseif($ride['statut'] == 'EN COURS'): ?>
                            <div style="display: flex; gap: 5px; align-items: center; justify-content: flex-end;">
                                <a href="index.php?page=detail&id=<?= $ride['id_covoiturage'] ?>" class="btn-secondary" style="background: #eee; color: #333; padding: 5px 10px; border-radius: 4px; text-decoration: none; font-size: 0.9rem;">Détails</a>
                                <form action="index.php?page=end&id=<?= $ride['id_covoiturage'] ?>" method="POST" style="margin:0;">
                                    <button type="submit" class="btn-cta" style="font-size: 0.9rem; padding: 5px 10px; background-color: #d32f2f; border:none; cursor:pointer;">
                                       🏁 Terminer
                                    </button>
                                </form>
                            </div>
                        <?php elseif($ride['statut'] == 'TERMINÉ'): ?>
                            <div style="display: flex; gap: 5px; align-items: center; justify-content: flex-end;">
                                <span style="color: green; font-weight: bold; font-size: 0.9rem;">Trajet terminé</span>
                                <a href="index.php?page=detail&id=<?= $ride['id_covoiturage'] ?>" class="btn-secondary" style="background: #eee; color: #333; padding: 5px 10px; border-radius: 4px; text-decoration: none; font-size: 0.9rem;">Détails</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>

<!-- Modale de Confirmation -->
<div id="confirmModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; justify-content:center; align-items:center;">
    <div style="background:white; padding:2rem; border-radius:8px; width:90%; max-width:400px; text-align:center; box-shadow:0 4px 15px rgba(0,0,0,0.2);">
        <h3 style="margin-top:0;">Confirmation</h3>
        <p id="modalMessage" style="color:#555; margin-bottom:1.5rem;">Êtes-vous sûr ?</p>
        <div style="display:flex; justify-content:space-between; gap: 10px;">
            <button onclick="closeModal()" style="padding:10px 20px; border:1px solid #ddd; background:#f4f4f4; border-radius:4px; cursor:pointer; flex: 1;">Non, retour</button>
            <a id="confirmBtn" href="#" style="text-align:center; padding:10px 20px; border:none; background:#d32f2f; color:white; border-radius:4px; cursor:pointer; text-decoration:none; flex: 1; display:flex; align-items:center; justify-content:center;">Oui, annuler</a>
        </div>
    </div>
</div>

<script>
    function switchTab(type) {
        document.getElementById('passenger-list').style.display = 'none';
        document.getElementById('driver-list').style.display = 'none';
        
        document.getElementById('tab-pass').classList.remove('active');
        document.getElementById('tab-driver').classList.remove('active');
        
        document.getElementById(type + '-list').style.display = 'block';
        
        if(type === 'passenger') document.getElementById('tab-pass').classList.add('active');
        else document.getElementById('tab-driver').classList.add('active');
    }

    function openCancelModal(url, message) {
        document.getElementById('modalMessage').innerText = message;
        document.getElementById('confirmBtn').href = url;
        document.getElementById('confirmModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('confirmModal').style.display = 'none';
    }

    window.onclick = function(event) {
        var modal = document.getElementById('confirmModal');
        if (event.target == modal) {
            closeModal();
        }
    }
</script>

<?php require_once __DIR__ . '/templates/footer.php'; ?>
