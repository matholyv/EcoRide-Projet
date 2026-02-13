<?php
$pageTitle = 'Détails du trajet - EcoRide';
ob_start();
?>
<style>
    .detail-container {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 30px;
        align-items: start;
        margin-top: 2rem;
    }
    
    @media (max-width: 900px) {
        .detail-container {
            grid-template-columns: 1fr;
        }
    }
    
    .card-box {
        background: white;
        padding: 2rem;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        margin-bottom: 2rem;
        border: 1px solid #f0f0f0;
    }
    
    .timeline {
        position: relative;
        padding-left: 20px;
        margin: 2rem 0 1rem;
        border-left: 2px solid #e0e0e0;
    }
    
    .timeline-item {
        margin-bottom: 2rem;
        position: relative;
    }
    
    .timeline-item:last-child {
        margin-bottom: 0;
    }
    
    .timeline-item::before {
        content: '';
        width: 14px;
        height: 14px;
        background: var(--white);
        border: 3px solid var(--primary-color);
        border-radius: 50%;
        position: absolute;
        left: -29px;
        top: 4px;
    }
    
    .car-info-box {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 15px;
        margin-top: 1rem;
    }

    .sidebar-sticky {
        position: sticky;
        top: 20px;
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
        <!-- COLONNE GAUCHE : Itinéraire + Infos -->
        <div class="main-info">
            <div class="card-box">
                <?php
                // Logique Date (Préservée)
                $bg_jours = ['Sunday'=>'Dimanche', 'Monday'=>'Lundi', 'Tuesday'=>'Mardi', 'Wednesday'=>'Mercredi', 'Thursday'=>'Jeudi', 'Friday'=>'Vendredi', 'Saturday'=>'Samedi'];
                $bg_mois = ['January'=>'Janvier', 'February'=>'Février', 'March'=>'Mars', 'April'=>'Avril', 'May'=>'Mai', 'June'=>'Juin', 'July'=>'Juillet', 'August'=>'Août', 'September'=>'Septembre', 'October'=>'Octobre', 'November'=>'Novembre', 'December'=>'Décembre'];
                
                $ts = strtotime($ride['date_depart']);
                $jour_en = date('l', $ts);
                $mois_en = date('F', $ts);
                $dateFr = ($bg_jours[$jour_en] ?? $jour_en) . ' ' . date('d', $ts) . ' ' . ($bg_mois[$mois_en] ?? $mois_en) . ' ' . date('Y', $ts);
                ?>
                
                <h1 style="margin-bottom: 1.5rem; font-size: 1.8rem;"><?= $dateFr ?></h1>
                
                <div class="timeline">
                    <!-- Départ -->
                    <div class="timeline-item">
                        <h3 style="margin:0; font-size:1.1rem;"><?= date('H:i', strtotime($ride['heure_depart'])) ?> · <?= htmlspecialchars($ride['lieu_depart']) ?></h3>
                        <?php if(!empty($ride['adresse_depart'])): ?>
                            <p style="margin: 5px 0 0; color: #555; font-size: 0.95rem;">📍 <?= htmlspecialchars($ride['adresse_depart']) ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Arrivée -->
                    <div class="timeline-item">
                        <h3 style="margin:0; font-size:1.1rem;"><?= date('H:i', strtotime($ride['heure_arrivee'])) ?> · <?= htmlspecialchars($ride['lieu_arrivee']) ?></h3>
                        <?php if(!empty($ride['adresse_arrivee'])): ?>
                            <p style="margin: 5px 0 0; color: #555; font-size: 0.95rem;">📍 <?= htmlspecialchars($ride['adresse_arrivee']) ?></p>
                        <?php endif; ?>
                        
                        <div style="margin-top: 8px; font-size: 0.85rem; color: #888;">
                            ⏱ Durée : <?= (new DateTime($ride['date_depart'].' '.$ride['heure_depart']))->diff(new DateTime($ride['date_arrivee'].' '.$ride['heure_arrivee']))->format('%hh%I') ?>
                        </div>
                    </div>
                </div>

                <hr style="border:0; border-top:1px solid #eee; margin:1.5rem 0;">

                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <?php if($ride['est_ecologique']): ?>
                        <span style="background: #e8f5e9; color: #2e7d32; padding: 6px 12px; border-radius: 20px; font-weight: 500; font-size: 0.9rem;">🌿 Voyage Écologique</span>
                    <?php endif; ?>
                    <span style="background: #f0f0f0; color: #333; padding: 6px 12px; border-radius: 20px; font-size: 0.9rem;"><?= $ride['nb_place'] ?> places restantes</span>
                </div>
            </div>

            <!-- Carte Véhicule (Intégrée à gauche) -->
            <div class="card-box">
                <h3 style="margin-bottom: 1rem;">Véhicule</h3>
                <div class="car-info-box">
                    <img src="<?= !empty($ride['voiture_photo']) ? $ride['voiture_photo'] : 'assets/img/default_car.png' ?>" 
                         alt="Voiture" style="width: 80px; height: 50px; object-fit: contain;">
                    <div>
                        <div style="font-weight: 600; font-size: 1.1rem;"><?= htmlspecialchars($ride['marque']) ?> <?= htmlspecialchars($ride['modele']) ?></div>
                        <div style="color: #666; font-size: 0.9rem;"><?= htmlspecialchars($ride['couleur']) ?> · <?= htmlspecialchars($ride['energie']) ?></div>
                    </div>
                </div>
            </div>

            <!-- Liste des Passagers (Si Conducteur) -->
            <?php 
             $current_user_id = $_SESSION['user']['id'] ?? 0;
             $is_driver = ($current_user_id == $ride['id_conducteur']);
             
             if ($is_driver && !empty($participants)): 
             ?>
                <div class="card-box">
                     <h3 style="margin-bottom: 1rem;">Passagers inscrits (<?= count($participants) ?>)</h3>
                     <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem;">
                        <?php foreach($participants as $p): ?>
                            <div style="padding: 10px; border: 1px solid #eee; border-radius: 8px; display: flex; align-items: center; gap: 10px;">
                                <img src="<?= !empty($p['photo']) ? $p['photo'] : 'assets/img/default_user.png' ?>" alt="P" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                <div>
                                    <div style="font-weight: 600; font-size: 0.9rem;"><?= htmlspecialchars($p['pseudo']) ?></div>
                                    <div style="font-size: 0.75rem; color: var(--primary-color); font-weight: bold;"><?= htmlspecialchars($p['statut']) ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                     </div>
                </div>
             <?php endif; ?>
        </div>
        
        <!-- COLONNE DROITE (SIDEBAR) -->
        <aside class="sidebar-sticky">
            <div class="card-box">
                <div style="text-align: center; margin-bottom: 1.5rem;">
                    <span style="font-size: 2.2rem; font-weight: 700; color: var(--primary-color); display: block;"><?= $ride['prix_personne'] ?> Crédits</span>
                    <span style="color: #666; font-size: 0.9rem;">par personne</span>
                </div>
                
                <hr style="border: 0; border-top: 1px solid #eee; margin: 1.5rem 0;">
                
                <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 1.5rem;">
                    <?php 
                    $photoPath = !empty($ride['conducteur_photo']) ? $ride['conducteur_photo'] : '';
                    if($photoPath) {
                        if(strpos($photoPath, 'uploads/') === false) $photoPath = 'uploads/' . $photoPath;
                        $photoPath = str_replace(' ', '%20', $photoPath);
                    }
                    ?>
                    <img src="<?= $photoPath ?: 'assets/img/default_user.png' ?>" 
                         alt="Conducteur" style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover;">
                    <div>
                        <h4 style="margin:0; font-size: 1.1rem;"><?= htmlspecialchars($ride['pseudo']) ?></h4>
                        <div style="color: #ffc107; font-weight: bold;">★ <?= number_format($ride['note_conducteur'] ?? 0, 1) ?>/5</div>
                        <a href="index.php?page=driver_reviews&id=<?= $ride['id_conducteur'] ?>" style="font-size: 0.85rem; color: #666; text-decoration: underline;">Voir les avis</a>
                    </div>
                </div>

                <div style="margin-bottom: 2rem; display: flex; flex-wrap: wrap; gap: 8px;">
                     <?php if($ride['pref_fumeur']): ?>
                        <span style="background: #e8f5e9; color: #2e7d32; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem;">🚬 Fumeur OK</span>
                    <?php else: ?>
                        <span style="background: #ffebee; color: #c62828; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem;">🚭 Non-fumeur</span>
                    <?php endif; ?>

                    <?php if($ride['pref_animaux']): ?>
                        <span style="background: #e8f5e9; color: #2e7d32; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem;">🐾 Animaux OK</span>
                    <?php else: ?>
                        <span style="background: #ffebee; color: #c62828; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem;">🚫 Pas d'animaux</span>
                    <?php endif; ?>
                </div>
                
                <!-- BOUTON RESERVATION -->
                <?php if(isset($_SESSION['user']) && $_SESSION['user']['id'] == $ride['id_conducteur']): ?>
                    <div style="background: #e3f2fd; color: #0d47a1; padding: 1rem; border-radius: 8px; text-align: center; font-weight: 500;">
                        Vous conduisez 🚗
                    </div>
                <?php else: ?>
                    <?php
                    // Check participation logic
                    $is_participant = false;
                    if (isset($_SESSION['user']['id']) && !empty($participants)) {
                        foreach($participants as $p) {
                            if ($p['id_utilisateur'] == $_SESSION['user']['id']) {
                                $is_participant = true;
                                break;
                            }
                        }
                    }
                    ?>

                    <?php if($is_participant): ?>
                        <button disabled style="width: 100%; padding: 1rem; background: #e8f5e9; border: 1px solid #a5d6a7; border-radius: 8px; color: #2e7d32; font-weight: bold; cursor: not-allowed;">
                            ✅ Déjà Inscrit
                        </button>
                    <?php elseif($ride['nb_place'] > 0): ?>
                        <form id="bookingForm" action="index.php?page=book&id=<?= $ride['id_covoiturage'] ?>" method="POST">
                            <button type="button" onclick="openConfirmModal()" class="btn-cta" style="display: block; width: 100%; padding: 1rem; border: none; font-size: 1.1rem; cursor: pointer; border-radius: 8px;">
                                Réserver une place
                            </button>
                        </form>
                    <?php else: ?>
                        <button disabled style="width: 100%; padding: 1rem; background: #f0f0f0; border: none; border-radius: 8px; color: #999; font-weight: bold;">Complet</button>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
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
