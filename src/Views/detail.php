<?php
$pageTitle = 'Détails du trajet - EcoRide';
ob_start();
?>
<style>
    .detail-container {
        display: flex;
        gap: 40px;
        margin-top: 2rem;
        flex-wrap: wrap;
    }
    
    .main-info {
        flex: 2;
        min-width: 300px;
    }
    
    .driver-info {
        flex: 1;
        min-width: 250px;
        background: white;
        padding: 2rem;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        height: fit-content;
    }
    
    .trip-header {
        background: white;
        padding: 2rem;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        margin-bottom: 2rem;
    }
    
    .timeline {
        position: relative;
        padding-left: 20px;
        margin: 2rem 0;
        border-left: 2px solid #ddd;
    }
    
    .timeline-item {
        margin-bottom: 2rem;
        position: relative;
    }
    
    .timeline-item::before {
        content: '';
        width: 12px;
        height: 12px;
        background: var(--primary-color);
        border-radius: 50%;
        position: absolute;
        left: -27px;
        top: 5px;
    }
    
    .car-card {
        background: #f9f9f9;
        padding: 1.5rem;
        border-radius: 8px;
        margin-top: 1rem;
        display: flex;
        align-items: center;
        gap: 20px;
    }
    
    .price-tag {
        font-size: 2rem;
        font-weight: bold;
        color: var(--primary-color);
        display: block;
        margin-bottom: 1rem;
        text-align: center;
    }
</style>
<?php
$extraStyles = ob_get_clean();
require_once __DIR__ . '/templates/header.php';
?>

<main class="container">
    <!-- Messages Feedback -->
    <?php if(isset($_GET['success']) && $_GET['success'] == 'booked'): ?>
        <div style="background: #e8f5e9; color: #2e7d32; padding: 1rem; border-radius: 5px; margin-top: 2rem; text-align: center;">
            ✅ Réservation confirmée ! Vos crédits ont été débités. Bon voyage !
        </div>
    <?php endif; ?>
    
    <?php if(isset($error)): ?>
        <div style="background: #ffebee; color: #c62828; padding: 1rem; border-radius: 5px; margin-top: 2rem; text-align: center;">
            ❌ <?= $error ?>
        </div>
    <?php endif; ?>

    <div class="detail-container">
        <div class="main-info">
            <div class="trip-header">
                <h1><?= date('l d F Y', strtotime($ride['date_depart'])) ?></h1>
                
                <div class="timeline">
                    <div class="timeline-item">
                        <h3><?= date('H:i', strtotime($ride['heure_depart'])) ?> · <?= htmlspecialchars($ride['lieu_depart']) ?></h3>
                    </div>
                    <div class="timeline-item">
                        <h3><?= date('H:i', strtotime($ride['heure_arrivee'])) ?> · Arrivée · <?= htmlspecialchars($ride['lieu_arrivee']) ?></h3>
                        <span style="color: #666; font-size: 0.9em;">
                            (Durée : <?= (new DateTime($ride['date_depart'].' '.$ride['heure_depart']))->diff(new DateTime($ride['date_arrivee'].' '.$ride['heure_arrivee']))->format('%hh%I') ?>)
                        </span>
                    </div>
                </div>
                
                <div style="display: flex; gap: 10px; margin-top: 1rem;">
                    <?php if($ride['est_ecologique']): ?>
                        <span style="background: #e8f5e9; color: #2e7d32; padding: 5px 10px; border-radius: 20px; font-weight: 500;">🌿 Voyage Écologique</span>
                    <?php endif; ?>
                    <span style="background: #eee; padding: 5px 10px; border-radius: 20px;"><?= $ride['nb_place'] ?> places restantes</span>
                </div>
            </div>
            
            <h3 style="margin-bottom: 1rem;">Véhicule</h3>
            <div class="car-card">
                <!-- Photo voiture par défaut si vide -->
                <img src="<?= !empty($ride['voiture_photo']) ? $ride['voiture_photo'] : 'assets/img/default_car.png' ?>" 
                     alt="Voiture" style="width: 100px; height: 60px; object-fit: contain;">
                <div>
                    <h4><?= htmlspecialchars($ride['marque']) ?> <?= htmlspecialchars($ride['modele']) ?></h4>
                    <p style="color: #666;"><?= htmlspecialchars($ride['couleur']) ?> · <?= htmlspecialchars($ride['energie']) ?></p>
                </div>
            </div>

            <!-- LISTE DES PASSAGERS (Visible pour le Conducteur) -->
             <?php 
             $current_user_id = $_SESSION['user']['id'] ?? 0;
             $is_driver = ($current_user_id == $ride['id_conducteur']);
             
             if ($is_driver && !empty($participants)): 
             ?>
                <h3 style="margin-top: 2rem; margin-bottom: 1rem;">Passagers inscrits (<?= count($participants) ?>)</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1rem;">
                    <?php foreach($participants as $p): ?>
                        <div style="background: white; padding: 1rem; border-radius: 8px; border: 1px solid #eee; display: flex; align-items: flex-start; gap: 12px; box-shadow: 0 2px 5px rgba(0,0,0,0.03);">
                            <img src="<?= !empty($p['photo']) ? $p['photo'] : 'assets/img/default_user.png' ?>" alt="P" style="width: 45px; height: 45px; border-radius: 50%; object-fit: cover; background: #eee;">
                            <div>
                                <div style="font-weight: 600; color: var(--dark-color);"><?= htmlspecialchars($p['pseudo']) ?></div>
                                
                                <?php if(!empty($p['bio'])): ?>
                                    <div style="font-size: 0.85rem; color: #666; margin-top: 4px; line-height: 1.4; font-style: italic;">
                                        "<?= htmlspecialchars(substr($p['bio'], 0, 60)) . (strlen($p['bio'])>60 ? '...' : '') ?>"
                                    </div>
                                <?php else: ?>
                                    <div style="font-size: 0.8rem; color: #bbb; margin-top: 4px;">(Aucune bio renseignée)</div>
                                <?php endif; ?>
                                
                                <div style="margin-top: 8px; font-size: 0.75rem; font-weight: 600; color: var(--primary-color); text-transform: uppercase; letter-spacing: 0.5px;">
                                    <?= htmlspecialchars($p['statut']) ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
             <?php endif; ?>
        </div>
        
        <aside class="driver-info">
            <div style="text-align: center; margin-bottom: 1.5rem;">
                <span class="price-tag"><?= $ride['prix_personne'] ?> Crédits</span>
                <p style="color: #666;">par personne</p>
            </div>
            
            <hr style="border: 0; border-top: 1px solid #eee; margin: 1.5rem 0;">
            
            <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 1rem;">
                <img src="<?= !empty($ride['conducteur_photo']) ? $ride['conducteur_photo'] : 'assets/img/default_user.png' ?>" 
                     alt="Conducteur" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
                <div>
                    <h4 style="margin:0;"><?= htmlspecialchars($ride['pseudo']) ?></h4>
                    <span style="color: #ffc107;">★ <?= number_format($ride['note_conducteur'] ?? 0, 1) ?>/5</span>
                </div>
            </div>
            
            <p style="font-size: 0.9rem; color: #666; margin-bottom: 2rem;">
                Membre vérifié.
                <br>
                <a href="index.php?page=driver_reviews&id=<?= $ride['id_conducteur'] ?>" style="color: var(--primary-color);">Voir les avis</a>
            </p>
            
            <?php if(isset($_SESSION['user']) && $_SESSION['user']['id'] == $ride['id_conducteur']): ?>
                <div style="background: #e3f2fd; color: #0d47a1; padding: 1rem; border-radius: 5px; text-align: center;">
                    🚗 Vous êtes le conducteur de ce trajet.
                </div>
            <?php elseif($ride['nb_place'] > 0): ?>
                <form id="bookingForm" action="index.php?page=book&id=<?= $ride['id_covoiturage'] ?>" method="POST">
                    <button type="button" onclick="openConfirmModal()" class="btn-cta" style="display: block; width: 100%; padding: 1rem; border: none; font-size: 1rem; cursor: pointer;">
                        Participer
                    </button>
                </form>
            <?php else: ?>
                <button disabled style="width: 100%; padding: 1rem; background: #ddd; border: none; border-radius: 5px; color: #888;">Complet</button>
            <?php endif; ?>
        </aside>
    </div>
</main>

<!-- Modale de confirmation -->
<div id="confirmModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; justify-content:center; align-items:center;">
    <div style="background:white; padding:2rem; border-radius:8px; width:90%; max-width:400px; text-align:center; box-shadow:0 4px 15px rgba(0,0,0,0.2);">
        <h3 style="margin-top:0;">Confirmer la réservation</h3>
        <p>Voulez-vous vraiment participer à ce trajet pour <strong style="color:var(--primary-color)"><?= $ride['prix_personne'] ?> Crédits</strong> ?</p>
        <div style="display:flex; justify-content:space-between; margin-top:1.5rem;">
            <button onclick="closeModal()" style="padding:10px 20px; border:1px solid #ddd; background:#f4f4f4; border-radius:4px; cursor:pointer;">Annuler</button>
            <button onclick="submitBooking()" style="padding:10px 20px; border:none; background:var(--primary-color); color:white; border-radius:4px; cursor:pointer;">Confirmer</button>
        </div>
    </div>
</div>

<script>
    function openConfirmModal() {
        document.getElementById('confirmModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('confirmModal').style.display = 'none';
    }

    function submitBooking() {
        document.getElementById('bookingForm').submit();
    }
</script>

<?php require_once __DIR__ . '/templates/footer.php'; ?>
