<?php
$pageTitle = 'EcoRide - Résultats';
ob_start();
?>
<style>
    .results-container {
        display: flex;
        gap: 30px;
        margin-top: 2rem;
    }
    
    .filters {
        width: 250px;
        background: white;
        padding: 1.5rem;
        border-radius: 10px;
        height: fit-content;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    
    .results-list {
        flex: 1;
    }
    
    .covoiturage-card {
        background: white;
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: transform 0.2s;
    }
    
    .covoiturage-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .card-left {
        display: flex;
        gap: 20px;
        align-items: center;
    }
    
    .driver-img {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        object-fit: cover;
        background: #eee;
    }
    
    .trip-info h3 {
        margin-bottom: 0.5rem;
        color: var(--dark-color);
    }
    
    .trip-details {
        color: #666;
        font-size: 0.9rem;
    }
    
    .eco-tag {
        background-color: #e8f5e9;
        color: #2e7d32;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    
    .card-right {
        text-align: right;
    }
    
    .price {
        font-size: 1.5rem;
        font-weight: bold;
        color: var(--primary-color);
        display: block;
        margin-bottom: 0.5rem;
    }
    
    @media (max-width: 768px) {
        .results-container { flex-direction: column; }
        .filters { width: 100%; }
        .covoiturage-card { flex-direction: column; align-items: flex-start; gap: 1rem; }
        .card-right { text-align: left; width: 100%; display: flex; justify-content: space-between; align-items: center; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #eee; }
    }
</style>
<?php
$extraStyles = ob_get_clean();
require_once __DIR__ . '/templates/header.php';
?>

<main class="container">
    <h2 style="margin-top: 2rem;">Résultats pour <?= htmlspecialchars($depart ?? '') ?> ➝ <?= htmlspecialchars($arrivee ?? '') ?></h2>
    
    <div class="results-container">
        <!-- Filtres (US 4) -->
        <aside class="filters">
            <form action="index.php" method="GET">
                <input type="hidden" name="page" value="covoiturages">
                <input type="hidden" name="depart" value="<?= htmlspecialchars($_GET['depart'] ?? '') ?>">
                <input type="hidden" name="arrivee" value="<?= htmlspecialchars($_GET['arrivee'] ?? '') ?>">
                <input type="hidden" name="date" value="<?= htmlspecialchars($_GET['date'] ?? '') ?>">
                
                <h3>Filtres</h3>
                <br>
                <label style="display:block; margin-bottom:5px;">Départ après :</label>
                <input type="time" name="heure_depart_min" value="<?= htmlspecialchars($_GET['heure_depart_min'] ?? '') ?>" style="width:100%; padding:5px; margin-bottom: 1rem; border:1px solid #ddd; border-radius:5px;">

                <label style="display:block; margin-bottom:5px;">Arrivée avant :</label>
                <input type="time" name="heure_arrivee_max" value="<?= htmlspecialchars($_GET['heure_arrivee_max'] ?? '') ?>" style="width:100%; padding:5px; margin-bottom: 1rem; border:1px solid #ddd; border-radius:5px;">
                
                <hr style="margin-bottom: 1rem; border: 0; border-top: 1px solid #eee;">
                <label>
                    <input type="checkbox" name="eco" <?= (isset($_GET['eco']) && $_GET['eco'] == 'on') ? 'checked' : '' ?>> 
                    Écologique (Électrique)
                </label>
                <br><br>
                
                <label>Prix max (Crédits) :</label>
                <input type="number" name="prixMax" placeholder="Pas de limite" min="0" value="<?= htmlspecialchars($_GET['prixMax'] ?? '') ?>" style="width:100%; padding:5px; margin-bottom: 1rem; border:1px solid #ddd; border-radius:5px;">
                <br>

                <label>Durée max : <span id="duree-val"><?= isset($_GET['dureeMax']) ? floor($_GET['dureeMax']/60).'h '.($_GET['dureeMax']%60).'m' : '10h 00m' ?></span></label>
                <input type="range" name="dureeMax" min="30" max="1440" step="30" value="<?= htmlspecialchars($_GET['dureeMax'] ?? 600) ?>" 
                       oninput="let h = Math.floor(this.value/60); let m = this.value%60; document.getElementById('duree-val').innerText = h + 'h ' + (m<10?'0':'') + m + 'm'">
                <br><br>
                
                <label>Note min conducteur</label>
                <select name="noteMin" style="width:100%; padding:5px; margin-bottom: 1rem;">
                    <option value="0">Toutes</option>
                    <option value="1" <?= (isset($_GET['noteMin']) && $_GET['noteMin'] == 1) ? 'selected' : '' ?>>⭐ 1+</option>
                    <option value="2" <?= (isset($_GET['noteMin']) && $_GET['noteMin'] == 2) ? 'selected' : '' ?>>⭐⭐ 2+</option>
                    <option value="3" <?= (isset($_GET['noteMin']) && $_GET['noteMin'] == 3) ? 'selected' : '' ?>>⭐⭐⭐ 3+</option>
                    <option value="4" <?= (isset($_GET['noteMin']) && $_GET['noteMin'] == 4) ? 'selected' : '' ?>>⭐⭐⭐⭐ 4+</option>
                </select>

                <label style="display:block; margin-bottom: 5px;">
                    <input type="checkbox" name="fumeur" <?= (isset($_GET['fumeur']) && $_GET['fumeur'] == 'on') ? 'checked' : '' ?>> 
                    🚬 Fumeur accepté
                </label>
                
                <label style="display:block; margin-bottom: 1rem;">
                    <input type="checkbox" name="animaux" <?= (isset($_GET['animaux']) && $_GET['animaux'] == 'on') ? 'checked' : '' ?>> 
                    🐾 Animaux acceptés
                </label>

                <button type="submit" class="btn-cta" style="width:100%; font-size: 0.9rem;">Appliquer</button>
                <!-- Lien Reset -->
                <div style="text-align: center; margin-top: 10px;">
                    <a href="index.php?page=covoiturages&depart=<?= htmlspecialchars($_GET['depart'] ?? '') ?>&arrivee=<?= htmlspecialchars($_GET['arrivee'] ?? '') ?>&date=<?= htmlspecialchars($_GET['date'] ?? '') ?>" style="font-size: 0.8rem; color: #666;">Réinitialiser</a>
                </div>
            </form>
        </aside>
        
        <div class="results-list">
            <?php if (empty($results)): ?>
                <div style="text-align:center; padding: 3rem; background:white; border-radius:10px;">
                    <h3>Aucun trajet trouvé 😔</h3>
                    <p>Essayez une autre date ou un autre itinéraire.</p>
                </div>
            <?php else: ?>
                <?php if(isset($results[0]['_is_nearby'])): ?>
                    <div style="background: #e3f2fd; color: #0d47a1; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                        ℹ️ Aucun résultat pour la date demandée. Voici des trajets proches (+/- 3 jours).
                    </div>
                <?php endif; ?>
                
                <?php foreach($results as $ride): ?>
                <div class="covoiturage-card">
                    <div class="card-left">
                        <img src="<?= $ride['photo'] ?? 'assets/img/default_user.png' ?>" alt="Conducteur" class="driver-img">
                        <div class="trip-info">
                            <h3>
                                <span style="font-weight: normal; font-size: 0.9em; color: #555;">
                                    <?= date('d/m', strtotime($ride['date_depart'])) ?>
                                </span> 
                                <?= date('H:i', strtotime($ride['heure_depart'])) ?> - <?= date('H:i', strtotime($ride['heure_arrivee'])) ?> 
                                (<?= (new DateTime($ride['date_depart'].' '.$ride['heure_depart']))->diff(new DateTime($ride['date_arrivee'].' '.$ride['heure_arrivee']))->format('%hh%I') ?>)
                                <br>
                                <?= htmlspecialchars($ride['lieu_depart']) ?> ➝ <?= htmlspecialchars($ride['lieu_arrivee']) ?>
                            </h3>
                            <div class="trip-details">
                                <p>Conducteur : <?= htmlspecialchars($ride['pseudo']) ?> (<?= $ride['note_moyenne'] ?? 'N/A' ?>/5)</p>
                                <p>Voiture : <?= htmlspecialchars($ride['marque']) ?> <?= htmlspecialchars($ride['modele']) ?></p>
                                <?php if($ride['est_ecologique']): ?>
                                    <span class="eco-tag">🌿 Voyage Écologique</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="card-right">
                        <span class="price"><?= $ride['prix_personne'] ?> Crédits</span>
                        <span style="color:#666"><?= $ride['nb_place'] ?> places rest.</span>
                        <br><br>
                        <a href="index.php?page=detail&id=<?= $ride['id_covoiturage'] ?>" class="btn-cta">Détails</a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/templates/footer.php'; ?>
