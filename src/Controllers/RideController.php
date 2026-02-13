<?php
class RideController {
    
    // Afficher le formulaire de publication
    public function create() {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?page=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once __DIR__ . '/../Models/Covoiturage.php';
            
            $depart = $_POST['depart'];
            $arrivee = $_POST['arrivee'];
            $adresse_depart = $_POST['adresse_depart'];
            $adresse_arrivee = $_POST['adresse_arrivee'];
            $date = $_POST['date'];
            $heure = $_POST['heure'];
            
            $date_arrivee = $_POST['date_arrivee'];
            $heure_arrivee = $_POST['heure_arrivee'];

            $prix = $_POST['prix'];
            $places = $_POST['places'];
            $voiture_id = $_POST['voiture'];
            $user_id = $_SESSION['user']['id'];
            
            $model = new Covoiturage();
            $success = $model->create($user_id, $depart, $arrivee, $date, $heure, $date_arrivee, $heure_arrivee, $prix, $places, $voiture_id, $adresse_depart, $adresse_arrivee);
            
            if ($success) {
                header('Location: index.php?success=trajet_cree');
                exit;
            } else {
                $error = "Erreur lors de la création du trajet.";
            }
        }
        
        require_once __DIR__ . '/../Config/Database.php';
        $db = (new Database())->getConnection();
        $stmt = $db->prepare("SELECT * FROM voiture WHERE id_utilisateur = ?");
        $stmt->execute([$_SESSION['user']['id']]);
        $voitures = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $commission = \Database::getGlobalParam('commission_trajet', 2);
        require_once __DIR__ . '/../Views/publish.php';
    }
    
    // US 5: Afficher les détails
    public function detail() {
        if (!isset($_GET['id'])) {
            header('Location: index.php');
            exit;
        }
        
        $id = $_GET['id'];
        require_once __DIR__ . '/../Models/Covoiturage.php';
        $model = new Covoiturage();
        $ride = $model->getById($id);
        
        if (!$ride) {
            echo "Trajet introuvable.";
            exit;
        }

        // Récupérer les participants pour l'affichage (US demandée)
        $participants = $model->getParticipants($id);
        
        require_once __DIR__ . '/../Views/detail.php';
    }
    
    // US 6: Participer
    public function book() {
        if (!isset($_SESSION['user'])) {
            // Si pas connecté, redirection vers login (avec retour)
            header('Location: index.php?page=login&redirect=detail&id=' . $_GET['id']);
            exit;
        }
        
        if (!isset($_GET['id'])) {
            header('Location: index.php');
            exit;
        }
        
        $id_covoiturage = $_GET['id'];
        $id_passager = $_SESSION['user']['id'];
        
        require_once __DIR__ . '/../Models/Covoiturage.php';
        $model = new Covoiturage();
        
        // C'est le conducteur lui-même ?
        $ride = $model->getById($id_covoiturage);
        if ($ride['id_conducteur'] == $id_passager) {
            $error = "Vous ne pouvez pas participer à votre propre trajet.";
            require_once __DIR__ . '/../Views/detail.php'; // Réafficher avec erreur
            return;
        }
        
        $result = $model->participer($id_covoiturage, $id_passager);
        
        if ($result === true) {
            header('Location: index.php?page=detail&id=' . $id_covoiturage . '&success=booked');
            exit;
        } else {
            $error = $result; // Message d'erreur (Solde insuffisant, etc.)
            // IMPORTANT : On doit recharger les infos du trajet ($ride) pour réafficher la vue
            $ride = $model->getById($id_covoiturage);
            require_once __DIR__ . '/../Views/detail.php';
        }
    }

    // US 10: Historique
    public function history() {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?page=login');
            exit;
        }

        require_once __DIR__ . '/../Models/Covoiturage.php';
        $model = new Covoiturage();
        
        $userId = $_SESSION['user']['id'];
        
        $driverRides = $model->getRidesAsDriver($userId);
        $passengerRides = $model->getRidesAsPassenger($userId);
        
        require_once __DIR__ . '/../Views/history.php';
    }

    // US 11: Conducteur démarre le trajet
    public function start() {
        if (!isset($_SESSION['user']) || !isset($_GET['id'])) { header('Location: index.php'); exit; }
        
        require_once __DIR__ . '/../Models/Covoiturage.php';
        $model = new Covoiturage();
        $res = $model->startRide($_GET['id'], $_SESSION['user']['id']);
        
        $msg = ($res === true) ? '&success=started' : '&error=' . urlencode($res);
        header('Location: index.php?page=history&tab=driver' . $msg);
    }

    // US 11: Conducteur termine le trajet
    public function end() {
        if (!isset($_SESSION['user']) || !isset($_GET['id'])) { header('Location: index.php'); exit; }
        
        require_once __DIR__ . '/../Models/Covoiturage.php';
        $model = new Covoiturage();
        $res = $model->endRide($_GET['id'], $_SESSION['user']['id']);
        
        // Envoi de mail aux passagers pour valider
        if ($res === true) {
            $participants = $model->getParticipants($_GET['id']);
            $rideDetails = $model->getById($_GET['id']); // Fetch ride details for email context

            foreach ($participants as $p) {
                $to = $p['email'];
                $subject = "EcoRide - Trajet terminé ! Validez votre expérience";
                
                // Détection automatique du domaine (localhost ou votre-site.com)
                $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
                $domainName = $_SERVER['HTTP_HOST'];
                // On suppose que index.php est à la racine de public/
                $baseUrl = $protocol . $domainName . dirname($_SERVER['SCRIPT_NAME']);
                
                $message = "Bonjour " . htmlspecialchars($p['pseudo']) . ",\n\n" .
                           "Le trajet " . htmlspecialchars($rideDetails['lieu_depart']) . " -> " . htmlspecialchars($rideDetails['lieu_arrivee']) . " est terminé.\n" .
                           "Pour débloquer les crédits du conducteur et laisser un avis, rendez-vous sur votre espace :\n" .
                           $baseUrl . "?page=review&id=" . $_GET['id'] . "\n\n" .
                           "L'équipe EcoRide.";
                
                // Headers pour l'email
                $headers = 'From: no-reply@ecoride.com' . "\r\n" .
                           'Reply-To: no-reply@ecoride.com' . "\r\n" .
                           'X-Mailer: PHP/' . phpversion();

                // 1. Tentative d'envoi réel (Fonctionnera chez l'hébergeur)
                @mail($to, $subject, $message, $headers);

                // 2. LOG pour débug (A conserver ou supprimer en production, peu importe)
                $logContent = "---------------------------------------------------\n";
                $logContent .= "DATE    : " . date('Y-m-d H:i:s') . "\n";
                $logContent .= "DE      : no-reply@ecoride.com\n";
                $logContent .= "À       : " . $to . "\n";
                $logContent .= "SUJET   : " . $subject . "\n";
                $logContent .= "LIEN    : " . $baseUrl . "?page=review&id=" . $_GET['id'] . "\n";
                $logContent .= "MESSAGE :\n" . $message . "\n";
                $logContent .= "---------------------------------------------------\n\n";
                
                file_put_contents(__DIR__ . '/../../emails.log', $logContent, FILE_APPEND);
            }
        }
        
        $msg = ($res === true) ? '&success=ended' : '&error=' . urlencode($res);
        header('Location: index.php?page=history&tab=driver' . $msg);
    }
    
    // US 11: Passager va laisser un avis (affichage page)
    public function review() {
        if (!isset($_SESSION['user']) || !isset($_GET['id'])) { header('Location: index.php'); exit; }
        // Simple affichage de la vue
        require_once __DIR__ . '/../Views/review.php';
    }

    // US 11: Passager soumet son avis
    public function submitReview() {
        if (!isset($_SESSION['user'])) { header('Location: index.php'); exit; }
        
        $id_covoiturage = $_POST['id_covoiturage'] ?? null;
        $statut = $_POST['statut'] ?? 'ok'; // 'ok' ou 'ko'
        $note = $_POST['note'] ?? 5;
        $commentaire = $_POST['commentaire'] ?? '';
        
        if (!$id_covoiturage) { header('Location: index.php?page=history'); exit; }
        
        $is_incident = ($statut === 'ko');
        if ($is_incident && empty(trim($commentaire))) {
            // Commentaire obligatoire si incident
            header("Location: index.php?page=review&id=$id_covoiturage&error=comment_required");
            exit;
        }

        require_once __DIR__ . '/../Models/Covoiturage.php';
        $model = new Covoiturage();
        $res = $model->processReview($id_covoiturage, $_SESSION['user']['id'], $is_incident, $note, $commentaire);
        
        $msg = ($res === true) ? (($is_incident) ? '&success=incident_reported' : '&success=reviewed') : '&error=' . urlencode($res);
        header('Location: index.php?page=history&tab=passenger' . $msg);
    }

    // Affiche les avis reçus par un conducteur
    public function showDriverReviews() {
        $id_driver = $_GET['id'] ?? null;

        if (!$id_driver) {
            header('Location: index.php');
            exit;
        }

        require_once __DIR__ . '/../Models/Covoiturage.php';
        $model = new Covoiturage();
        
        // On récupère infos conducteur + ses avis
        // Note: Idéalement cela devrait être dans un UserModel, mais on utilise CovoiturageModel pour l'instant
        $db = (new Database())->getConnection();

        // Infos conducteur
        $stmt = $db->prepare("SELECT pseudo, photo, (SELECT AVG(note) FROM avis WHERE id_destinataire = ?) as note_moyenne FROM utilisateur WHERE id_utilisateur = ?");
        $stmt->execute([$id_driver, $id_driver]);
        $driver = $stmt->fetch(PDO::FETCH_ASSOC);

        // Liste des avis reçus (seulement les valides/positifs pour l'instant)
        // Les litiges non résolus ne sont pas affichés publiquement
        $stmt = $db->prepare("
            SELECT a.*, u.pseudo as auteur, u.photo as auteur_photo
            FROM avis a
            JOIN utilisateur u ON a.id_auteur = u.id_utilisateur
            WHERE a.id_destinataire = ? AND a.statut = 'VALIDÉ'
            ORDER BY a.id_avis DESC
        ");
        $stmt->execute([$id_driver]);
        $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../Views/driver_reviews.php';
    }
    // US 10 : Annuler un trajet (Conducteur) ou une participation (Passager)
    public function cancel() {
        if (!isset($_SESSION['user']) || !isset($_GET['id'])) { header('Location: index.php'); exit; }
        
        $role = $_GET['role'] ?? 'passenger'; // 'driver' ou 'passenger'
        $id = $_GET['id'];
        $userId = $_SESSION['user']['id'];
        
        require_once __DIR__ . '/../Models/Covoiturage.php';
        $model = new Covoiturage();
        
        if ($role === 'driver') {
            $res = $model->cancelRide($id, $userId);
            $tab = 'driver';
        } else {
            $res = $model->cancelParticipation($id, $userId);
            $tab = 'passenger';
        }
        
        $msg = ($res === true) ? '&success=cancelled' : '&error=' . urlencode($res);
        header('Location: index.php?page=history&tab=' . $tab . $msg);
    }
}
