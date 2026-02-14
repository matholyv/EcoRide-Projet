<?php

class EmployeeController {

    private function checkAuth() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 3) {
            // Pas employé ? Dehors.
            header('Location: index.php?page=login&error=Accès refusé');
            exit;
        }
    }

    // Page : Modération des Avis (Notes faibles)
    public function reviews() {
        $this->checkAuth();
        
        $db = new Database();
        $conn = $db->getConnection();
        
        $sql = "SELECT a.*, 
                       u1.pseudo as auteur, u1.email as email_auteur,
                       u2.pseudo as destinataire 
                FROM avis a
                JOIN utilisateur u1 ON a.id_auteur = u1.id_utilisateur
                JOIN utilisateur u2 ON a.id_destinataire = u2.id_utilisateur
                WHERE a.statut = 'EN_ATTENTE'
                ORDER BY a.id_avis DESC";
        
        $avis = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../Views/employee/reviews.php';
    }

    // Page : Gestion des Litiges (Incidents)
    public function disputes() {
        $this->checkAuth();
        
        $db = new Database();
        $conn = $db->getConnection();
        
        $sql = "SELECT a.*, 
                       u1.pseudo as auteur, u1.email as email_auteur,
                       u2.pseudo as destinataire, u2.email as email_destinataire,
                       c.id_covoiturage,
                       c.lieu_depart, c.lieu_arrivee,
                       c.date_depart, c.heure_depart,
                       c.date_arrivee, c.heure_arrivee,
                       c.prix_personne
                FROM avis a
                JOIN utilisateur u1 ON a.id_auteur = u1.id_utilisateur
                JOIN utilisateur u2 ON a.id_destinataire = u2.id_utilisateur
                JOIN covoiturage c ON a.id_covoiturage = c.id_covoiturage
                WHERE a.statut = 'LITIGE'
                ORDER BY a.id_avis DESC";
        
        $disputes = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        // Historique des litiges résolus (Note = 0 indique un ancien litige traité)
        $sqlHistory = "SELECT a.*, 
                       u1.pseudo as auteur, 
                       u2.pseudo as destinataire,
                       c.prix_personne,
                       p.statut as statut_final
                FROM avis a
                JOIN utilisateur u1 ON a.id_auteur = u1.id_utilisateur
                JOIN utilisateur u2 ON a.id_destinataire = u2.id_utilisateur
                JOIN covoiturage c ON a.id_covoiturage = c.id_covoiturage
                JOIN participation p ON (a.id_covoiturage = p.id_covoiturage AND a.id_auteur = p.id_passager)
                WHERE a.note = 0 AND a.statut != 'LITIGE'
                ORDER BY a.id_avis DESC LIMIT 10";
        
        $history = $conn->query($sqlHistory)->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../Views/employee/disputes.php';
    }

    // Action : Valider un avis
    public function validateReview() {
        $this->checkAuth();
        
        if (isset($_GET['id'])) {
            $db = new Database();
            $conn = $db->getConnection();
            $stmt = $conn->prepare("UPDATE avis SET statut = 'VALIDÉ' WHERE id_avis = ?");
            $stmt->execute([$_GET['id']]);
        }
        
        header('Location: index.php?page=employee_reviews&success=validated');
    }

    // Action : Refuser un avis
    public function rejectReview() {
        $this->checkAuth();
        
        if (isset($_GET['id'])) {
            $db = new Database();
            $conn = $db->getConnection();
            $stmt = $conn->prepare("UPDATE avis SET statut = 'REFUSÉ' WHERE id_avis = ?");
            $stmt->execute([$_GET['id']]);
        }
        
        header('Location: index.php?page=employee_reviews&success=rejected');
    }

    // Action : Résoudre un litige (Payer ou Rembourser)
    public function resolveConflict() {
        $this->checkAuth();
        
        $id_avis = $_GET['id'] ?? null;
        $decision = $_GET['decision'] ?? null; // 'pay' ou 'refund'
        
        if (!$id_avis || !$decision) {
            header('Location: index.php?page=employee_disputes');
            exit;
        }

        $db = new Database();
        $conn = $db->getConnection();
        
        // 1. Récupérer infos du trajet via l'avis
        $stmt = $conn->prepare("
            SELECT a.id_covoiturage, a.id_auteur, a.id_destinataire, c.prix_personne
            FROM avis a
            JOIN covoiturage c ON a.id_covoiturage = c.id_covoiturage
            WHERE a.id_avis = ? AND a.statut = 'LITIGE'
        ");
        $stmt->execute([$id_avis]);
        $info = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$info) {
             header('Location: index.php?page=employee_disputes&error=not_found');
             exit;
        }

        try {
            $conn->beginTransaction();

            if ($decision === 'pay') {
                // Payer le conducteur (Destinataire de l'avis/litige)
                // On applique la commission
                $commission = \Database::getGlobalParam('commission_trajet', 2);
                $gain = max(0, $info['prix_personne'] - $commission);
                $stmt = $conn->prepare("UPDATE utilisateur SET credits = credits + ? WHERE id_utilisateur = ?");
                $stmt->execute([$gain, $info['id_destinataire']]);
                
                // Statut AVIS -> REFUSÉ (Important : on cache l'avis 0/5 car le conducteur n'est pas en tort)
                $query = "UPDATE avis SET statut = 'REFUSÉ', note = 0 WHERE id_avis = ?";
                $stmt = $conn->prepare($query);
                $stmt->execute([$id_avis]);
                
            } elseif ($decision === 'refund') {
                // Rembourser le passager (Auteur de l'avis/litige)
                $stmt = $conn->prepare("UPDATE utilisateur SET credits = credits + ? WHERE id_utilisateur = ?");
                $stmt->execute([$info['prix_personne'], $info['id_auteur']]);
                
                // Statut AVIS -> REFUSÉ (Le litige est clos en faveur du passager, donc on supprime l'avis/signalement du dashboard)
                // On met note = 0 pour le retrouver dans l'historique
                $stmt = $conn->prepare("UPDATE avis SET statut = 'REFUSÉ', note = 0 WHERE id_avis = ?");
            }
            
            $stmt->execute([$id_avis]);
            
            // Mettre à jour la participation pour clore définitivement
            $statut_part = ($decision === 'pay') ? 'VALIDÉ' : 'REMBOURSÉ';
            $stmt = $conn->prepare("UPDATE participation SET statut = ? WHERE id_covoiturage = ? AND id_passager = ?");
            $stmt->execute([$statut_part, $info['id_covoiturage'], $info['id_auteur']]);
            
            $conn->commit();
            header('Location: index.php?page=employee_disputes&success=resolved');

        } catch (Exception $e) {
            $conn->rollBack();
            header('Location: index.php?page=employee_disputes&error=db_error');
        }
    }
}
