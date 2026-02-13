<?php
$pageTitle = 'EcoRide - Accueil';
ob_start();
?>
<style>
    /* Styles spécifiques à la Home */
    .hero {
        background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('assets/img/hero_bg.jpg');
        background-size: cover;
        background-position: center;
        color: white;
        padding: 100px 0;
        text-align: center;
        border-radius: 0 0 50px 50px;
        margin-bottom: 2rem;
    }

    .hero h1 {
        color: white;
        font-size: 3rem;
        margin-bottom: 1rem;
    }
    
    .search-box {
        background: white;
        padding: 2rem;
        border-radius: 15px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        max-width: 900px;
        margin: -50px auto 0; /* Chevauche le hero */
        position: relative;
        z-index: 10;
    }
    
    .search-form {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }
    
    .form-group {
        flex: 1;
        min-width: 200px;
    }
    
    .form-group input {
        width: 100%;
        padding: 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 1rem;
        font-family: var(--font-body);
    }
    
    .presentation {
        padding: 4rem 0;
        text-align: center;
    }
    
    .features {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
        margin-top: 3rem;
    }

    /* Centre la derniÃ¨re carte si elle est seule sur sa ligne */
    .features > .feature-card:last-child:nth-child(odd) {
        grid-column: 1 / -1;
        max-width: 400px;
        margin: 0 auto;
    }
    
    .feature-card {
        background: white;
        padding: 2rem;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        transition: transform 0.3s;
    }
    
    .feature-card:hover {
        transform: translateY(-5px);
    }
</style>
<?php
$extraStyles = ob_get_clean();
require_once __DIR__ . '/templates/header.php';
?>

    <div class="hero">
        <div class="container">
            <h1>Voyagez moins cher,<br>préservez la planète.</h1>
            <p>Le covoiturage écologique qui vous récompense.</p>
        </div>
    </div>

    <main class="container">
        <div class="search-box">
            <form action="index.php" method="GET" class="search-form">
                <input type="hidden" name="page" value="search">
                <div class="form-group">
                    <input type="text" name="depart" placeholder="Départ (ex: Paris)" required>
                </div>
                <div class="form-group">
                    <input type="text" name="arrivee" placeholder="Arrivée (ex: Lyon)" required>
                </div>
                <div class="form-group">
                    <input type="date" name="date" value="<?= date('Y-m-d') ?>" required>
                </div>
                <button type="submit" class="btn-cta" style="border:none; cursor:pointer; font-size:1rem;">Rechercher</button>
            </form>
        </div>

        <section class="presentation">
            <h2>Pourquoi choisir EcoRide ?</h2>
            <p style="max-width: 700px; margin: 1rem auto; color: #666;">
                EcoRide est la première plateforme de covoiturage qui valorise les déplacements en véhicule électrique.
                Rejoignez notre communauté engagée !
            </p>
            
            <div class="features">
                <div class="feature-card" style="text-align: center;">
                    <img src="https://placehold.co/100x100/4CAF50/white?text=Eco" alt="Écologique" style="border-radius: 50%; margin-bottom: 1rem;">
                    <h3>🌱 100% Écologique</h3>
                    <p>Nous mettons en avant les véhicules électriques et les comportements responsables.</p>
                </div>
                <div class="feature-card" style="text-align: center;">
                    <img src="https://placehold.co/100x100/2196F3/white?text=Prix" alt="Économique" style="border-radius: 50%; margin-bottom: 1rem;">
                    <h3>💰 Économique</h3>
                    <p>Partagez les frais de voyage sans commission cachée. Juste 2 crédits par trajet.</p>
                </div>
                <div class="feature-card" style="text-align: center;">
                    <img src="https://placehold.co/100x100/FF9800/white?text=Team" alt="Communauté" style="border-radius: 50%; margin-bottom: 1rem;">
                    <h3>🤝 Communauté</h3>
                    <p>Des conducteurs et passagers vérifiés pour des trajets en toute confiance.</p>
                </div>
            </div>
            
            <div style="margin-top: 4rem; display: flex; align-items: center; gap: 40px; flex-wrap: wrap; justify-content: center;">
                <img src="assets/img/beautiful-landscape-bamboo-grove-forest-arashiyama-kyoto.jpg" alt="Voyage EcoRide" style="border-radius: 10px; box-shadow: 0 10px 20px rgba(0,0,0,0.1); max-width: 100%; width: 400px; height: 250px; object-fit: cover;">
                <div style="max-width: 400px; text-align: left;">
                    <h3>Une nouvelle façon de voyager</h3>
                    <p style="color: #555; line-height: 1.6;">
                        En choisissant EcoRide, vous participez activement à la réduction de l'empreinte carbone. 
                        Nos algorithmes privilégient les trajets courts et les véhicules à faible émission.
                    </p>
                    <a href="index.php?page=covoiturages" class="btn-cta" style="margin-top: 1rem; display: inline-block;">Voir les trajets</a>
                </div>
            </div>
        </section>
        
        <?php if (!empty($latestRides)): ?>
        <section style="margin: 4rem 0;">
            <h3>Derniers trajets publiés</h3>
            <div class="features">
                <?php foreach($latestRides as $ride): ?>
                <div class="feature-card">
                    <h4><?= htmlspecialchars($ride['lieu_depart']) ?> ➝ <?= htmlspecialchars($ride['lieu_arrivee']) ?></h4>
                    <p>📅 <?= date('d/m/Y', strtotime($ride['date_depart'])) ?></p>
                    <p style="margin:5px 0; color:#555;">
                        <?= date('H:i', strtotime($ride['heure_depart'])) ?> - <?= date('H:i', strtotime($ride['heure_arrivee'])) ?>
                        <br>
                        <span style="font-size:0.9em">(<?= (new DateTime($ride['date_depart'].' '.$ride['heure_depart']))->diff(new DateTime($ride['date_arrivee'].' '.$ride['heure_arrivee']))->format('%hh%I') ?>)</span>
                    </p>
                    <p style="color: var(--primary-color); font-weight: bold;"><?= htmlspecialchars($ride['prix_personne']) ?> Crédits</p>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>
    </main>

<?php require_once __DIR__ . '/templates/footer.php'; ?>
