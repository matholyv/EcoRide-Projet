<?php
$pageTitle = 'Bilan du trajet - EcoRide';
require_once __DIR__ . '/templates/header.php';
?>

<main class="container" style="padding: 3rem 0; max-width: 600px;">
    <div class="card" style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
        <h1 style="margin-bottom: 1.5rem; text-align: center;">Comment s'est passé votre trajet ?</h1>
        
        <form action="index.php?page=submit_review" method="POST">
            <input type="hidden" name="id_covoiturage" value="<?= htmlspecialchars($_GET['id']) ?>">
            
            <div style="margin-bottom: 2rem;">
                <label style="font-weight: bold; display: block; margin-bottom: 10px;">Résultat du voyage :</label>
                
                <div style="display: flex; gap: 20px;">
                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; padding: 15px; border: 1px solid #ddd; border-radius: 8px; flex: 1;">
                        <input type="radio" name="statut" value="ok" checked onclick="toggleIncident(false)">
                        <span style="color: green; font-weight: bold;">👍 Bien passé</span>
                    </label>
                    
                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; padding: 15px; border: 1px solid #ddd; border-radius: 8px; flex: 1;">
                        <input type="radio" name="statut" value="ko" onclick="toggleIncident(true)">
                        <span style="color: #d32f2f; font-weight: bold;">👎 Mal passé</span>
                    </label>
                </div>
            </div>

            <!-- Section Note (Visible si OK) -->
            <div id="section-note" style="margin-bottom: 2rem;">
                <label style="font-weight: bold; display: block; margin-bottom: 10px;">Votre note (1 à 5) :</label>
                <select name="note" class="form-input" style="width: 100%; padding: 10px;">
                    <option value="5">⭐⭐⭐⭐⭐ (Parfait)</option>
                    <option value="4">⭐⭐⭐⭐ (Très bien)</option>
                    <option value="3">⭐⭐⭐ (Bien)</option>
                    <option value="2">⭐⭐ (Moyen)</option>
                    <option value="1">⭐ (Mauvais)</option>
                </select>
            </div>

            <div style="margin-bottom: 2rem;">
                <label id="label-commentaire" style="font-weight: bold; display: block; margin-bottom: 10px;">Votre avis :</label>
                <textarea name="commentaire" class="form-input" rows="5" style="width: 100%; padding: 10px;" placeholder="Dites-nous en plus..."></textarea>
                <p id="help-incident" style="color: #d32f2f; font-size: 0.9rem; display: none; margin-top: 5px; background: #ffebee; padding: 10px; border-radius: 5px;">
                    ⚠️ En cas d'incident, les crédits du conducteur seront bloqués jusqu'à résolution du litige par nos équipes. Expliquez le problème en détail.
                </p>
            </div>

            <button type="submit" class="btn-cta" style="width: 100%; padding: 1rem; border: none; font-size: 1.1rem; cursor: pointer;">
                Valider le bilan
            </button>
        </form>
    </div>
</main>

<script>
    function toggleIncident(isIncident) {
        const sectionNote = document.getElementById('section-note');
        const helpIncident = document.getElementById('help-incident');
        const labelCommentaire = document.getElementById('label-commentaire');
        
        if(isIncident) {
            sectionNote.style.display = 'none';
            helpIncident.style.display = 'block';
            labelCommentaire.innerText = 'Détaillez le problème (Obligatoire) :';
            document.querySelector('[name="commentaire"]').required = true;
        } else {
            sectionNote.style.display = 'block';
            helpIncident.style.display = 'none';
            labelCommentaire.innerText = 'Votre avis (Optionnel) :';
            document.querySelector('[name="commentaire"]').required = false;
        }
    }
</script>

<?php require_once __DIR__ . '/templates/footer.php'; ?>
