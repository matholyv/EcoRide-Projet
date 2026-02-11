<?php
$pageTitle = 'Publier un trajet - EcoRide';
require_once __DIR__ . '/templates/header.php';
?>

<main class="container" style="padding: 2rem 0;">
    <div style="max-width: 600px; margin: 0 auto; background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
        <h2 style="margin-bottom: 0.5rem; color: var(--primary-color);">Publier un covoiturage</h2>
        <p style="margin-bottom: 2rem; color: #666;">Remplissez les informations pour proposer votre trajet.</p>
        
        <?php if(isset($error)): ?>
            <p style="color: red; margin-bottom: 1rem; background: #fee; padding: 10px; border-radius: 5px;"><?= $error ?></p>
        <?php endif; ?>

        <form action="index.php?page=publish" method="POST">
            <div class="form-group" style="margin-bottom: 1rem;">
                <label style="font-weight: 500;">Lieu de départ</label>
                <input type="text" name="depart" required class="form-input" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;" placeholder="Ex: Paris">
            </div>
            
            <div class="form-group" style="margin-bottom: 1rem;">
                <label style="font-weight: 500;">Lieu d'arrivée</label>
                <input type="text" name="arrivee" required class="form-input" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;" placeholder="Ex: Lyon">
            </div>
            
            <div style="display: flex; gap: 15px; margin-bottom: 1rem;">
                <div style="flex: 1;">
                    <label style="font-weight: 500;">Date Débart</label>
                    <input type="date" name="date" required class="form-input" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                </div>
                <div style="flex: 1;">
                    <label style="font-weight: 500;">Heure Départ</label>
                    <input type="time" name="heure" required class="form-input" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                </div>
            </div>

            <div style="display: flex; gap: 15px; margin-bottom: 1rem;">
                <div style="flex: 1;">
                    <label style="font-weight: 500;">Date Arrivée</label>
                    <input type="date" name="date_arrivee" required class="form-input" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                </div>
                <div style="flex: 1;">
                    <label style="font-weight: 500;">Heure Arrivée</label>
                    <input type="time" name="heure_arrivee" required class="form-input" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                </div>
            </div>
            
            <div style="display: flex; gap: 15px; margin-bottom: 1rem;">
                <div style="flex: 1;">
                    <label style="font-weight: 500;">Prix par personne (Crédits)</label>
                    <input type="number" name="prix" step="1" required class="form-input" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                    <small style="color: #666; font-size: 0.8rem; display:block; margin-top:5px;">⚠️ Frais EcoRide : 2 crédits/pers.</small>
                </div>
                <div style="flex: 1;">
                    <label style="font-weight: 500;">Places disponibles</label>
                    <input type="number" name="places" min="1" max="6" value="3" required class="form-input" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label style="font-weight: 500;">Véhicule utilisé</label>
                <select name="voiture" required class="form-input" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                    <?php if(empty($voitures)): ?>
                        <option value="">🚫 Aucune voiture (Allez dans Profil > Véhicules)</option>
                    <?php else: ?>
                        <?php foreach($voitures as $voiture): ?>
                            <option value="<?= $voiture['id_voiture'] ?>">
                                <?= htmlspecialchars($voiture['modele']) ?> (<?= htmlspecialchars($voiture['immatriculation']) ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <?php if(empty($voitures)): ?>
                    <a href="index.php?page=profile" style="font-size: 0.9rem; color: var(--primary-color);">Ajouter un véhicule maintenant</a>
                <?php endif; ?>
            </div>
            
            <button type="submit" class="btn-cta" style="width: 100%; border: none; cursor: pointer; font-size: 1rem;">Publier le trajet</button>
        </form>
    </div>
</main>

<?php require_once __DIR__ . '/templates/footer.php'; ?>
