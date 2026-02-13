<?php
require_once __DIR__ . '/../Models/Covoiturage.php';

class HomeController {
    
    public function index() {
        $covoiturageModel = new Covoiturage();
        
        // On pourrait récupérer quelques trajets pour animer l'accueil
        $latestRides = $covoiturageModel->getLatests();
        
        // Chargement de la vue
        require_once __DIR__ . '/../Views/home.php';
    }
    
    public function search() {
        $depart = $_GET['depart'] ?? '';
        $arrivee = $_GET['arrivee'] ?? '';
        $date = !empty($_GET['date']) ? $_GET['date'] : date('Y-m-d');
        
        $filters = [
            'eco' => $_GET['eco'] ?? null,
            'prixMax' => !empty($_GET['prixMax']) ? $_GET['prixMax'] : 999999, // Si vide, prix très haut = infini
            'dureeMax' => $_GET['dureeMax'] ?? 1440, // Max 24h par défaut
            'noteMin' => $_GET['noteMin'] ?? 0,
            'fumeur' => $_GET['fumeur'] ?? null,
            'animaux' => $_GET['animaux'] ?? null,
            'heure_depart_min' => $_GET['heure_depart_min'] ?? null,
            'heure_arrivee_max' => $_GET['heure_arrivee_max'] ?? null
        ];
        
        $covoiturageModel = new Covoiturage();
        $results = $covoiturageModel->search($depart, $arrivee, $date, $filters);
        
        // Si aucun résultat, on peut proposer des alternatives (US 3: "modifier sa date")
        // Pour l'instant on affiche juste les résultats
        
        require_once __DIR__ . '/../Views/covoiturages.php';
    }

    public function contact() {
        require_once __DIR__ . '/../Views/contact.php';
    }

    public function contactAction() {
        // Simulation d'envoi de mail
        $email = $_POST['email'] ?? '';
        $subject = $_POST['subject'] ?? '';
        $message = $_POST['message'] ?? '';
        
        if (!empty($email) && !empty($message)) {
            // Simulation : Mail envoyé avec succès
            header('Location: index.php?page=contact&success=message_sent');
        } else {
            header('Location: index.php?page=contact&error=missing_fields');
        }
    }
}
