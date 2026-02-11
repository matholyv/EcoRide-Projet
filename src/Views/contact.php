<?php
$pageTitle = 'Contact - EcoRide';
ob_start();
?>
<style>
    .contact-container {
        display: flex;
        gap: 40px;
        margin-top: 3rem;
        flex-wrap: wrap;
    }
    
    .contact-info {
        flex: 1;
        min-width: 300px;
        background: #f9f9f9;
        padding: 2rem;
        border-radius: 10px;
    }
    
    .contact-form-card {
        flex: 2;
        min-width: 300px;
        background: white;
        padding: 2rem;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    
    .contact-item {
        display: flex;
        align-items: flex-start;
        gap: 15px;
        margin-bottom: 1.5rem;
    }
    
    .contact-icon {
        background: var(--primary-color);
        color: white;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }
    
    .contact-label {
        display: block;
        font-weight: bold;
        margin-bottom: 5px;
        color: var(--dark-color);
    }
    
    .form-group { margin-bottom: 1.5rem; }
    .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 500; }
    .form-group input, .form-group select, .form-group textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-family: inherit;
        font-size: 1rem;
    }
    
    @media (max-width: 768px) {
        .contact-container { flex-direction: column; }
    }
</style>
<?php
$extraStyles = ob_get_clean();
require_once __DIR__ . '/templates/header.php';
?>

<main class="container">
    <div style="text-align: center; margin-top: 3rem;">
        <h1 style="color: var(--primary-color);">Contactez-nous</h1>
        <p style="color: #666; max-width: 600px; margin: 1rem auto;">Une question ? Un problème ? Notre équipe est là pour vous aider.</p>
    </div>

    <div class="contact-container">
        <!-- Infos -->
        <div class="contact-info">
            <h2 style="font-size: 1.2rem; margin-bottom: 1.5rem;">Informations</h2>
            
            <div class="contact-item">
                <span class="contact-icon">📍</span>
                <div>
                    <span class="contact-label">Adresse</span>
                    EcoRide SAS<br>
                    123 Avenue de l'Écologie<br>
                    75000 Paris, France
                </div>
            </div>
            
            <div class="contact-item">
                <span class="contact-icon">📧</span>
                <div>
                    <span class="contact-label">Email</span>
                    <a href="mailto:support@ecoride.com" style="color: inherit;">support@ecoride.com</a>
                </div>
            </div>
            
            <div class="contact-item">
                <span class="contact-icon">📞</span>
                <div>
                    <span class="contact-label">Téléphone</span>
                    01 23 45 67 89
                </div>
            </div>

            <hr style="margin: 2rem 0; border: 0; border-top: 1px solid #eee;">
            
            <h3 style="font-size: 1rem;">Horaires du support</h3>
            <p style="color: #666; font-size: 0.9rem; margin-top: 0.5rem;">
                Lundi - Vendredi : 9h - 18h<br>
                Samedi : 10h - 17h
            </p>
        </div>

        <!-- Formulaire -->
        <div class="contact-form-card">
            <h2 style="font-size: 1.2rem; margin-bottom: 1.5rem;">Envoyez-nous un message</h2>
            <form action="index.php?page=contact_action" method="POST">
                <div class="form-group">
                    <label>Votre Email</label>
                    <input type="email" name="email" required placeholder="exemple@email.com" 
                           value="<?= isset($_SESSION['user']['email']) ? htmlspecialchars($_SESSION['user']['email']) : '' ?>">
                </div>
                
                <div class="form-group">
                    <label>Sujet</label>
                    <select name="subject" required>
                        <option value="">-- Choisir un sujet --</option>
                        <option value="general">Question générale</option>
                        <option value="bug">Signaler un bug</option>
                        <option value="account">Problème de compte</option>
                        <option value="partnership">Partenariat</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Message</label>
                    <textarea name="message" required placeholder="Comment pouvons-nous vous aider ?" rows="5"></textarea>
                </div>
                
                <button type="submit" class="btn-cta" style="width: 100%; border:none; cursor:pointer; font-size:1rem;">Envoyer le message</button>
            </form>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/templates/footer.php'; ?>
