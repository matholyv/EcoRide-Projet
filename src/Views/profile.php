<?php
$pageTitle = 'Mon Profil - EcoRide';
ob_start();
?>
<style>
    .profile-section { background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); height: 100%; }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
    .car-card { border: 1px solid #eee; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center; background: #fff; }
    .tag-pref { display: inline-block; padding: 5px 10px; background: #eee; border-radius: 20px; font-size: 0.8rem; margin-right: 5px; }
    
    .profile-layout {
        display: grid;
        grid-template-columns: 1fr;
        gap: 2rem;
    }

    /* Version Desktop : 2 colonnes */
    @media (min-width: 992px) {
        .profile-layout {
            grid-template-columns: 3fr 2fr; /* Info perso (plus large) | Véhicules */
            align-items: start;
        }
    }

    @media (max-width: 768px) {
        .form-grid { grid-template-columns: 1fr; }
    }
</style>
<?php
$extraStyles = ob_get_clean();
require_once __DIR__ . '/templates/header.php';
?>

<main class="container" style="padding: 2rem 0; max-width: 1200px;">
    <h1 style="margin-bottom: 2rem;">Mon Profil</h1>

    <?php if(isset($_GET['success'])): ?>
        <div style="background-color: #e8f5e9; color: #2e7d32; padding: 1rem; border-radius: 5px; margin-bottom: 1.5rem;">
            <?= htmlspecialchars($_GET['success']) ?>
        </div>
    <?php endif; ?>

    <div class="profile-layout">
        <!-- Colonne Gauche -->
        <div class="layout-col-left">
    <section class="profile-section">
        <h2 style="margin-bottom: 1.5rem; color: var(--primary-color);">Informations Personnelles</h2>
        
        <!-- Mode Lecture : Affiché par défaut si des données existent -->
        <?php $hasInfo = !empty($user['nom']) || !empty($user['prenom']); ?>
        
        <div id="view-profile" style="display: <?= $hasInfo ? 'block' : 'none' ?>;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem;">
                <div style="display: flex; align-items: center; gap: 20px;">
                    <?php 
                        $photoUrl = !empty($user['photo']) ? 'uploads/' . $user['photo'] : 'assets/img/default_user.png';
                    ?>
                    <img src="<?= htmlspecialchars($photoUrl) ?>" alt="Profil" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 3px solid #eee;">
                    
                    <div>
                        <h2 style="margin: 0; color: var(--primary-color);">
                            <?= htmlspecialchars(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? '')) ?>
                        </h2>
                        
                        <!-- Section Réputation -->
                        <div style="margin-top: 5px; display: flex; align-items: center; gap: 10px;">
                            <?php if (!empty($reputation['moyenne'])): ?>
                                <span style="font-weight: bold; color: #fbc02d; font-size: 1.1rem;">
                                    ⭐ <?= number_format($reputation['moyenne'], 1) ?> <span style="color: #666; font-size: 0.9rem;">/ 5</span>
                                </span>
                                <a href="index.php?page=driver_reviews&id=<?= $user['id_utilisateur'] ?>" style="color: var(--secondary-color); text-decoration: none; font-size: 0.9rem; font-weight: 500;">
                                    (<?= $reputation['total_avis'] ?> avis)
                                </a>
                            <?php else: ?>
                                <span style="color: #999; font-size: 0.9rem; font-style: italic;">Aucun avis pour le moment</span>
                            <?php endif; ?>
                        </div>

                        <p style="color: #666; margin-top: 5px;">
                            <?= !empty($user['bio']) ? nl2br(htmlspecialchars($user['bio'])) : '<i>Aucune biographie renseignée.</i>' ?>
                        </p>
                    </div>
                </div>
                <button onclick="toggleEditMode()" class="btn-secondary" style="background: #eee; color: #333; border: none; padding: 0.5rem 1rem; border-radius: 5px; cursor: pointer; font-weight: 500;">
                    ✏️ Modifier
                </button>
            </div>

            <div class="info-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 2rem; background: #f9f9f9; padding: 1.5rem; border-radius: 8px;">
                <div>
                    <strong style="color: #555; font-size: 0.9rem;">Email</strong>
                    <div style="font-weight: 500;"><?= htmlspecialchars($user['email']) ?></div>
                </div>
                <div>
                    <strong style="color: #555; font-size: 0.9rem;">Téléphone</strong>
                    <div style="font-weight: 500;"><?= htmlspecialchars($user['telephone'] ?: 'Non renseigné') ?></div>
                </div>
                <div style="grid-column: span 2;">
                    <strong style="color: #555; font-size: 0.9rem;">Adresse</strong>
                    <div style="font-weight: 500;"><?= htmlspecialchars($user['adresse'] ?: 'Non renseignée') ?></div>
                </div>
            </div>

            <h3 style="margin-bottom: 1rem; font-size: 1.1rem;">Préférences de Voyage</h3>
            <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 1rem;">
                <?php if($user['pref_fumeur']): ?>
                    <span class="tag-pref" style="background: #ffebee; color: #c62828;">🚬 Fumeur accepté</span>
                <?php else: ?>
                    <span class="tag-pref" style="background: #e8f5e9; color: #2e7d32;">🚭 Non Fumeur</span>
                <?php endif; ?>

                <?php if($user['pref_animaux']): ?>
                    <span class="tag-pref" style="background: #fff3e0; color: #ef6c00;">🐶 Animaux bienvenus</span>
                <?php else: ?>
                    <span class="tag-pref" style="background: #eee; color: #666;">🚫 Pas d'animaux</span>
                <?php endif; ?>
            </div>
            
            <?php if(!empty($user['pref_voyage'])): ?>
                <div style="background: #f0f4f8; padding: 1rem; border-left: 4px solid var(--secondary-color); border-radius: 4px;">
                    <strong>Note du voyageur :</strong> "<?= htmlspecialchars($user['pref_voyage']) ?>"
                </div>
            <?php endif; ?>
        </div>

        <!-- Mode Édition : Affiché si aucune info ou au clic sur Modifier -->
        <form id="edit-profile" action="index.php?page=profile_update" method="POST" enctype="multipart/form-data" style="display: <?= $hasInfo ? 'none' : 'block' ?>;">
            <div style="margin-bottom: 1.5rem; border-bottom: 1px solid #eee; padding-bottom: 1rem;">
                <h3 style="color: var(--primary-color);">Modifier mes informations</h3>
            </div>

            <div style="margin-bottom: 2rem; text-align: center;">
                <label style="display: block; margin-bottom: 15px; font-weight: 500; color: #555;">Photo de profil</label>
                
                <div style="position: relative; width: 120px; height: 120px; margin: 0 auto;">
                    <!-- L'image de prévisualisation (cliquable) -->
                    <img id="photo-preview" 
                         src="<?= !empty($user['photo']) ? 'uploads/' . htmlspecialchars($user['photo']) : 'assets/img/default_user.png' ?>" 
                         alt="Aperçu"
                         onclick="document.getElementById('photo-input').click();"
                         style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid white; box-shadow: 0 5px 15px rgba(0,0,0,0.1); cursor: pointer; transition: transform 0.2s;">
                    
                    <!-- L'icône appareil photo par-dessus -->
                    <div onclick="document.getElementById('photo-input').click();" 
                         style="position: absolute; bottom: 0; right: 0; background: var(--primary-color); color: white; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 2px 5px rgba(0,0,0,0.2); border: 2px solid white;">
                        📷
                    </div>
                </div>

                <!-- Input caché -->
                <input type="file" id="photo-input" name="photo" accept="image/*" style="display: none;" onchange="previewImage(this)">
                
                <p style="font-size: 0.85rem; color: #888; margin-top: 10px;">Cliquez sur l'image pour modifier (Max 2Mo)</p>
            </div>

            <script>
                function previewImage(input) {
                    if (input.files && input.files[0]) {
                        var reader = new FileReader();
                        reader.onload = function(e) {
                            document.getElementById('photo-preview').src = e.target.result;
                        }
                        reader.readAsDataURL(input.files[0]);
                    }
                }
            </script>

            <div class="form-grid">
                <div>
                    <label>Nom</label>
                    <input type="text" name="nom" value="<?= htmlspecialchars($user['nom'] ?? '') ?>" class="form-input" style="width: 100%; padding: 0.8rem;">
                </div>
                <div>
                    <label>Prénom</label>
                    <input type="text" name="prenom" value="<?= htmlspecialchars($user['prenom'] ?? '') ?>" class="form-input" style="width: 100%; padding: 0.8rem;">
                </div>
                <div>
                    <label>Téléphone</label>
                    <input type="text" name="telephone" value="<?= htmlspecialchars($user['telephone'] ?? '') ?>" class="form-input" style="width: 100%; padding: 0.8rem;">
                </div>
                <div>
                    <label>Adresse complète</label>
                    <input type="text" name="adresse" value="<?= htmlspecialchars($user['adresse'] ?? '') ?>" class="form-input" style="width: 100%; padding: 0.8rem;">
                </div>
            </div>

            <div style="margin-top: 1.5rem;">
                <label>Biographie</label>
                <textarea name="bio" rows="3" class="form-input" style="width: 100%; padding: 0.8rem;"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
            </div>

            <h3 style="margin-top: 2rem; margin-bottom: 1rem; font-size: 1.1rem;">Mes Préférences de Voyage</h3>
            <div style="display: flex; gap: 2rem;">
                <label style="display: flex; align-items: center; gap: 8px;">
                    <input type="checkbox" name="pref_fumeur" <?= ($user['pref_fumeur'] ?? 0) ? 'checked' : '' ?>>
                    🚬 Fumeur accepté
                </label>
                <label style="display: flex; align-items: center; gap: 8px;">
                    <input type="checkbox" name="pref_animaux" <?= ($user['pref_animaux'] ?? 0) ? 'checked' : '' ?>>
                    🐶 Animaux acceptés
                </label>
            </div>
            
            <div style="margin-top: 1.5rem;">
                <label>Préférences de voyage (ex: Pas de valise, musique, silence...)</label>
                <input type="text" name="pref_voyage" value="<?= htmlspecialchars($user['pref_voyage'] ?? '') ?>" class="form-input" style="width: 100%; padding: 0.8rem;" placeholder="Vos conditions particulières">
            </div>

            <div style="margin-top: 2rem; display: flex; gap: 10px;">
                <button type="submit" class="btn-cta" style="border: none; cursor: pointer;">Enregistrer</button>
                <?php if($hasInfo): ?>
                    <button type="button" onclick="toggleEditMode()" style="background: #eee; color: #333; border: none; padding: 1rem; border-radius: 5px; cursor: pointer;">Annuler</button>
                <?php endif; ?>
            </div>
        </form>

        <script>
            function toggleEditMode() {
                const view = document.getElementById('view-profile');
                const form = document.getElementById('edit-profile');
                if (view.style.display === 'none') {
                    view.style.display = 'block';
                    form.style.display = 'none';
                } else {
                    view.style.display = 'none';
                    form.style.display = 'block';
                }
            }
        </script>
    </section>
    </div> <!-- Fin Colonne Gauche -->

    <!-- Colonne Droite : Véhicules -->
    <div class="layout-col-right">
        <!-- Section 2 : Mes Véhicules -->
        <section class="profile-section">
            <h2 style="margin-bottom: 1.5rem; color: var(--primary-color);">Mes Véhicules</h2>
        
        <?php if(empty($cars)): ?>
            <p style="color: #666; font-style: italic;">Aucun véhicule enregistré pour le moment.</p>
        <?php else: ?>
            <?php foreach($cars as $car): ?>
                <div class="car-card">
                    <div>
                        <strong><?= htmlspecialchars($car['marque_libelle']) ?> <?= htmlspecialchars($car['modele']) ?></strong>
                        <br>
                        <span style="color: #666; font-size: 0.9rem;"><?= htmlspecialchars($car['immatriculation']) ?> · <?= htmlspecialchars($car['energie']) ?> · <?= htmlspecialchars($car['couleur']) ?></span>
                    </div>
                    <a href="index.php?page=car_delete&id=<?= $car['id_voiture'] ?>" onclick="return confirm('Supprimer ce véhicule ?')" style="color: #d32f2f; text-decoration: none; font-weight: bold;">Supprimer 🗑️</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <div style="text-align: center; margin-top: 2rem;">
            <button id="btn-add-car" onclick="toggleCarForm()" class="btn-secondary" style="border: 1px solid var(--primary-color); color: var(--primary-color); background: white; padding: 0.8rem 1.5rem; border-radius: 5px; cursor: pointer; font-weight: 500; font-size: 1rem;">
                ➕ Ajouter un véhicule
            </button>
        </div>

        <div id="car-form-container" style="display: none; margin-top: 1.5rem; background: #f9f9f9; padding: 1.5rem; border-radius: 8px; animation: fadeIn 0.3s ease-in-out;">
            <h3 style="margin-bottom: 1.5rem; color: var(--primary-color);">Nouveau véhicule</h3>
            <form action="index.php?page=car_add" method="POST">
                <div class="form-grid">
                    <div>
                        <label>Marque et Modèle</label>
                        <br>
                        <select name="id_marque" class="form-input" style="width: 48%; padding: 0.8rem; display:inline-block;" required>
                            <option value="">Marque...</option>
                            <?php foreach($marques as $m): ?>
                                <option value="<?= $m['id_marque'] ?>"><?= htmlspecialchars($m['libelle']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="text" name="modele" required class="form-input" style="width: 49%; padding: 0.8rem; display:inline-block;" placeholder="Modèle">
                    </div>
                    <div>
                        <label>Immatriculation</label>
                        <input type="text" name="immatriculation" required class="form-input" style="width: 100%; padding: 0.8rem;" placeholder="XX-123-YY">
                    </div>
                    <div>
                        <label>Energie</label>
                        <select name="energie" class="form-input" style="width: 100%; padding: 0.8rem;">
                            <option value="Essence">Essence</option>
                            <option value="Diesel">Diesel</option>
                            <option value="Hybride">Hybride</option>
                            <option value="Electrique">Electrique</option>
                        </select>
                    </div>
                     <div>
                        <label>Couleur</label>
                        <input type="text" name="couleur" class="form-input" style="width: 100%; padding: 0.8rem;">
                    </div>
                    <div>
                        <label>Nombre de places</label>
                        <input type="number" name="places" value="4" min="1" max="9" class="form-input" style="width: 100%; padding: 0.8rem;">
                    </div>
                </div>
                <div style="margin-top: 1.5rem; display: flex; gap: 10px;">
                    <button type="submit" class="btn-cta" style="background-color: var(--secondary-color); border: none; cursor: pointer;">Enregistrer le véhicule</button>
                    <button type="button" onclick="toggleCarForm()" style="background: #e0e0e0; color: #333; border: none; padding: 0 1.5rem; border-radius: 5px; cursor: pointer;">Annuler</button>
                </div>
            </form>
        </div>

        <script>
            function toggleCarForm() {
                const container = document.getElementById('car-form-container');
                const btn = document.getElementById('btn-add-car');
                
                if (container.style.display === 'none') {
                    container.style.display = 'block';
                    btn.style.display = 'none';
                } else {
                    container.style.display = 'none';
                    btn.style.display = 'inline-block';
                }
            }
        </script>
        </section>
    </div> <!-- Fin Colonne Droite -->
    </div> <!-- Fin Profile Grid -->

</main>

<?php require_once __DIR__ . '/templates/footer.php'; ?>
