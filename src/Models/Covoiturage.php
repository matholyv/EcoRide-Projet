<?php
require_once __DIR__ . '/../Config/Database.php';

class Covoiturage {
    private $conn;
    private $table = "covoiturage";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function search($depart, $arrivee, $date, $filters) {
        $eco = isset($filters['eco']) && $filters['eco'] == 'on';
        $prixMax = $filters['prixMax'] ?? 1000;
        $dureeMax = $filters['dureeMax'] ?? 600;
        $noteMin = $filters['noteMin'] ?? 0;
        $fumeur = isset($filters['fumeur']) && $filters['fumeur'] == 'on';
        $animaux = isset($filters['animaux']) && $filters['animaux'] == 'on';

        // Fonction helper locale pour construire la requête
        $buildQuery = function($dateOperator = "=") use ($eco, $fumeur, $animaux, $noteMin) {
            $query = "SELECT c.*, v.modele, m.libelle as marque, u.pseudo, u.photo, u.pref_fumeur, u.pref_animaux,
                      (SELECT AVG(note) FROM avis WHERE id_destinataire = u.id_utilisateur) as note_moyenne
                      FROM " . $this->table . " c
                      JOIN voiture v ON c.id_voiture = v.id_voiture
                      JOIN marque m ON v.id_marque = m.id_marque
                      JOIN utilisateur u ON c.id_conducteur = u.id_utilisateur
                      WHERE c.lieu_depart LIKE :depart 
                      AND c.lieu_arrivee LIKE :arrivee 
                      AND c.statut = 'PLANIFIÉ'
                      AND c.nb_place > 0
                      AND c.prix_personne <= :prixMax
                      AND TIMESTAMPDIFF(MINUTE, CONCAT(c.date_depart, ' ', c.heure_depart), CONCAT(c.date_arrivee, ' ', c.heure_arrivee)) <= :dureeMax";

            if ($dateOperator === '=') {
                $query .= " AND c.date_depart = :date_depart";
            } else {
                // Recherche élargie (+/- 3 jours)
                $query .= " AND c.date_depart BETWEEN DATE_SUB(:date_depart, INTERVAL 3 DAY) AND DATE_ADD(:date_depart, INTERVAL 3 DAY)";
                $query .= " AND c.date_depart != :date_depart"; // Exclure la date exacte déjà cherchée vide
            }

            if ($eco) $query .= " AND c.est_ecologique = 1";
            if ($fumeur) $query .= " AND u.pref_fumeur = 1";
            if ($animaux) $query .= " AND u.pref_animaux = 1";

            // Filtres horaires (New)
            if (!empty($filters['heure_depart_min'])) {
                $query .= " AND c.heure_depart >= :heure_depart_min";
            }
            if (!empty($filters['heure_arrivee_max'])) {
                $query .= " AND c.heure_arrivee <= :heure_arrivee_max";
            }
            
            if ($noteMin > 0) $query .= " HAVING note_moyenne >= :noteMin";
            
            $query .= " ORDER BY c.date_depart ASC, c.heure_depart ASC";
            return $query;
        };

        // 1. Recherche exacte
        $stmt = $this->conn->prepare($buildQuery('='));
        $departLike = "%$depart%";
        $arriveeLike = "%$arrivee%";
        
        $stmt->bindParam(':depart', $departLike);
        $stmt->bindParam(':arrivee', $arriveeLike);
        $stmt->bindParam(':date_depart', $date);
        $stmt->bindParam(':prixMax', $prixMax);
        $stmt->bindParam(':dureeMax', $dureeMax);
        if ($noteMin > 0) $stmt->bindParam(':noteMin', $noteMin);
        if (!empty($filters['heure_depart_min'])) $stmt->bindParam(':heure_depart_min', $filters['heure_depart_min']);
        if (!empty($filters['heure_arrivee_max'])) $stmt->bindParam(':heure_arrivee_max', $filters['heure_arrivee_max']);
        
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 2. Si aucun résultat, recherche élargie (US 3)
        if (empty($results)) {
            $stmt = $this->conn->prepare($buildQuery('NEARBY'));
            $stmt->bindParam(':depart', $departLike);
            $stmt->bindParam(':arrivee', $arriveeLike);
            $stmt->bindParam(':date_depart', $date); // Sert de pivot
            $stmt->bindParam(':prixMax', $prixMax);
            $stmt->bindParam(':dureeMax', $dureeMax);
            if ($noteMin > 0) $stmt->bindParam(':noteMin', $noteMin);
            
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // On peut ajouter un flag pour dire à la vue "Ceci sont des dates proches"
            foreach($results as &$r) {
                $r['_is_nearby'] = true;
            }
        }

        return $results;
    }
    
    // Page d'accueil: Derniers trajets
    public function getLatests($limit = 3) {
        $query = "SELECT c.*, c.lieu_depart, c.lieu_arrivee, c.date_depart, c.prix_personne
                  FROM " . $this->table . " c
                  WHERE c.statut = 'PLANIFIÉ' AND c.nb_place > 0
                  ORDER BY c.date_depart ASC, c.heure_depart ASC
                  LIMIT :limit";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // US 9: Créer un trajet
    public function create($id_conducteur, $depart, $arrivee, $date, $heure, $date_arrivee, $heure_arrivee, $prix, $places, $id_voiture) {
        // 1. Vérifier si la voiture est écologique
        try {
            $checkSql = "SELECT energie FROM voiture WHERE id_voiture = ?";
            $stmtCheck = $this->conn->prepare($checkSql);
            $stmtCheck->execute([$id_voiture]);
            $voiture = $stmtCheck->fetch(PDO::FETCH_ASSOC);
            
            $estEcologique = 0;
            if ($voiture && stripos($voiture['energie'], 'Electrique') !== false) {
                $estEcologique = 1;
            }
        } catch(PDOException $e) {
            $estEcologique = 0;
        }

        $query = "INSERT INTO " . $this->table . " 
                  (date_depart, heure_depart, date_arrivee, heure_arrivee, lieu_depart, lieu_arrivee, nb_place, prix_personne, id_conducteur, id_voiture, statut, est_ecologique)
                  VALUES (:date, :heure, :date_arrivee, :heure_arrivee, :depart, :arrivee, :places, :prix, :conducteur, :voiture, 'PLANIFIÉ', :est_ecologique)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':heure', $heure);
        $stmt->bindParam(':date_arrivee', $date_arrivee);
        $stmt->bindParam(':heure_arrivee', $heure_arrivee);
        $stmt->bindParam(':depart', $depart);
        $stmt->bindParam(':arrivee', $arrivee);
        $stmt->bindParam(':places', $places);
        $stmt->bindParam(':prix', $prix);
        $stmt->bindParam(':conducteur', $id_conducteur);
        $stmt->bindParam(':voiture', $id_voiture);
        $stmt->bindParam(':est_ecologique', $estEcologique, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    // US 5: Détails d'un trajet
    public function getById($id) {
        $query = "SELECT c.*, 
                         v.modele, m.libelle as marque, v.energie, v.couleur, NULL as voiture_photo,
                         u.pseudo, u.photo as conducteur_photo, u.date_naissance,
                         (SELECT AVG(note) FROM avis WHERE id_destinataire = u.id_utilisateur) as note_conducteur
                  FROM " . $this->table . " c
                  JOIN voiture v ON c.id_voiture = v.id_voiture
                  JOIN marque m ON v.id_marque = m.id_marque
                  JOIN utilisateur u ON c.id_conducteur = u.id_utilisateur
                  WHERE c.id_covoiturage = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // US 6: Participer (Réserver)
    public function participer($id_covoiturage, $id_passager) {
        try {
            $this->conn->beginTransaction();
            
            // 1. Infos trajet
            $stmt = $this->conn->prepare("SELECT nb_place, prix_personne FROM covoiturage WHERE id_covoiturage = ?");
            $stmt->execute([$id_covoiturage]);
            $trajet = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$trajet || $trajet['nb_place'] <= 0) {
                $this->conn->rollBack();
                return "Plus de places disponibles.";
            }

            // 2. Vérifier solde passager
            $stmt = $this->conn->prepare("SELECT credits FROM utilisateur WHERE id_utilisateur = ?");
            $stmt->execute([$id_passager]);
            $passager = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($passager['credits'] < $trajet['prix_personne']) {
                $this->conn->rollBack();
                return "Solde insuffisant (" . $passager['credits'] . " crédits disponibles).";
            }
            
            // 3. Déjà inscrit ?
            $stmt = $this->conn->prepare("SELECT * FROM participation WHERE id_covoiturage = ? AND id_passager = ?");
            $stmt->execute([$id_covoiturage, $id_passager]);
            if($stmt->fetch()) {
                $this->conn->rollBack();
                return "Vous participez déjà à ce trajet.";
            }
            
            // 4. Mises à jour (Place --, Crédits Passager --)
            // On débite le passager maintenant (l'argent est 'bloqué' chez EcoRide).
            $stmt = $this->conn->prepare("UPDATE covoiturage SET nb_place = nb_place - 1 WHERE id_covoiturage = ?");
            $stmt->execute([$id_covoiturage]);
            
            $stmt = $this->conn->prepare("UPDATE utilisateur SET credits = credits - ? WHERE id_utilisateur = ?");
            $stmt->execute([$trajet['prix_personne'], $id_passager]);
            
            // 5. Création participation
            $stmt = $this->conn->prepare("INSERT INTO participation (id_covoiturage, id_passager, statut) VALUES (?, ?, 'CONFIRMÉ')");
            $stmt->execute([$id_covoiturage, $id_passager]);
            
            $this->conn->commit();
            
            // Mise à jour de la session pour l'affichage immédiat
            if(isset($_SESSION['user']['credits'])) {
                 $_SESSION['user']['credits'] -= $trajet['prix_personne'];
            }
            
            return true;
            
        } catch (Exception $e) {
            $this->conn->rollBack();
            return "Erreur technique : " . $e->getMessage();
        }
    }

    // US 10: Historique - Trajets en tant que Conducteur
    public function getRidesAsDriver($userId) {
        $query = "SELECT c.*, v.modele, m.libelle as marque,
                         (SELECT COUNT(*) FROM participation p WHERE p.id_covoiturage = c.id_covoiturage) as nb_participants
                  FROM " . $this->table . " c
                  JOIN voiture v ON c.id_voiture = v.id_voiture
                  JOIN marque m ON v.id_marque = m.id_marque
                  WHERE c.id_conducteur = :id
                  ORDER BY c.date_depart DESC, c.heure_depart DESC";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // US 10: Historique - Trajets en tant que Passager
    public function getRidesAsPassenger($userId) {
        $query = "SELECT c.*, v.modele, m.libelle as marque, u.pseudo as conducteur_pseudo, u.photo as conducteur_photo, p.statut as statut_participation
                  FROM participation p
                  JOIN covoiturage c ON p.id_covoiturage = c.id_covoiturage
                  JOIN voiture v ON c.id_voiture = v.id_voiture
                  JOIN marque m ON v.id_marque = m.id_marque
                  JOIN utilisateur u ON c.id_conducteur = u.id_utilisateur
                  WHERE p.id_passager = :id
                  ORDER BY c.date_depart DESC, c.heure_depart DESC";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // US 11: Conducteur démarre le trajet
    public function startRide($id_covoiturage, $id_conducteur) {
        $stmt = $this->conn->prepare("UPDATE " . $this->table . " SET statut = 'EN COURS' WHERE id_covoiturage = ? AND id_conducteur = ? AND statut = 'PLANIFIÉ'");
        if ($stmt->execute([$id_covoiturage, $id_conducteur])) {
            return $stmt->rowCount() > 0 ? true : "Impossible de démarrer ce trajet.";
        }
        return "Erreur technique.";
    }

    // US 11: Conducteur termine le trajet
    public function endRide($id_covoiturage, $id_conducteur) {
        $stmt = $this->conn->prepare("UPDATE " . $this->table . " SET statut = 'TERMINÉ' WHERE id_covoiturage = ? AND id_conducteur = ? AND statut = 'EN COURS'");
        if ($stmt->execute([$id_covoiturage, $id_conducteur])) {
            return $stmt->rowCount() > 0 ? true : "Impossible de terminer ce trajet (doit être EN COURS).";
        }
        return "Erreur technique.";
    }

    // US 11: Passager soumet son bilan (Validation ou Litige)
    public function processReview($id_covoiturage, $id_passager, $is_incident, $note, $commentaire) {
        try {
            $this->conn->beginTransaction();

            // 1. Récupérer les infos
            $stmt = $this->conn->prepare("
                SELECT c.prix_personne, c.id_conducteur, p.statut as statut_participation 
                FROM " . $this->table . " c
                JOIN participation p ON c.id_covoiturage = p.id_covoiturage
                WHERE c.id_covoiturage = ? AND p.id_passager = ? AND c.statut = 'TERMINÉ'
            ");
            $stmt->execute([$id_covoiturage, $id_passager]);
            $infos = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$infos) {
                $this->conn->rollBack();
                return "Trajet non terminable ou participation introuvable.";
            }

            if ($infos['statut_participation'] === 'VALIDÉ' || $infos['statut_participation'] === 'LITIGE' || $infos['statut_participation'] === 'REMBOURSÉ') {
                $this->conn->rollBack();
                return "Vous avez déjà donné votre avis ou le trajet a été remboursé.";
            }

            // 2. Traitement selon incident ou succès
            if (!$is_incident) {
                // CAS SUCCÈS : On paie le chauffeur
                $commission = \Database::getGlobalParam('commission_trajet', 2);
                $gain_conducteur = max(0, $infos['prix_personne'] - $commission);
                
                $stmt = $this->conn->prepare("UPDATE utilisateur SET credits = credits + ? WHERE id_utilisateur = ?");
                $stmt->execute([$gain_conducteur, $infos['id_conducteur']]);
                
                $nouveau_statut = 'VALIDÉ';
                
                // US 12: Auto-validation intelligente
                if ($note >= 4) {
                    $avis_statut = 'VALIDÉ'; // Bonne note => Publication directe
                } else {
                    $avis_statut = 'EN_ATTENTE'; // Mauvaise note => Modération requise
                }
            } else {
                // CAS LITIGE : On NE paie PAS
                $nouveau_statut = 'LITIGE';
                $avis_statut = 'LITIGE'; // Statut spécial à traiter en priorité
                $note = 0; 
            }

            // 3. Mise à jour participation
            $stmt = $this->conn->prepare("UPDATE participation SET statut = ? WHERE id_covoiturage = ? AND id_passager = ?");
            $stmt->execute([$nouveau_statut, $id_covoiturage, $id_passager]);

            // 4. Insertion de l'avis (ou signalement)
            // Note: id_destinataire = id_conducteur
            $stmt = $this->conn->prepare("INSERT INTO avis (commentaire, note, statut, id_covoiturage, id_auteur, id_destinataire) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$commentaire, $note, $avis_statut, $id_covoiturage, $id_passager, $infos['id_conducteur']]);

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollBack();
            return "Erreur technique : " . $e->getMessage();
        }
    }

    // US 11: Récupérer les participants d'un trajet
    public function getParticipants($idCovoiturage) {
        $stmt = $this->conn->prepare("
            SELECT u.pseudo, u.bio, u.photo, p.statut
            FROM participation p
            JOIN utilisateur u ON p.id_passager = u.id_utilisateur
            WHERE p.id_covoiturage = ?
        ");
        $stmt->execute([$idCovoiturage]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // US 10 : Annulation par le passager (Désistement)
    public function cancelParticipation($id_covoiturage, $id_passager) {
        try {
            $this->conn->beginTransaction();

            $stmt = $this->conn->prepare("
                SELECT p.statut as p_statut, c.statut as c_statut, c.prix_personne
                FROM participation p
                JOIN covoiturage c ON p.id_covoiturage = c.id_covoiturage
                WHERE p.id_covoiturage = ? AND p.id_passager = ?
            ");
            $stmt->execute([$id_covoiturage, $id_passager]);
            $info = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$info || $info['p_statut'] === 'ANNULÉ') {
                $this->conn->rollBack();
                return "Participation introuvable ou déjà annulée.";
            }

            if ($info['c_statut'] !== 'PLANIFIÉ') {
                $this->conn->rollBack();
                return "Le voyage a déjà commencé ou est terminé, impossible d'annuler.";
            }

            // Rembourser Passager
            $stmt = $this->conn->prepare("UPDATE utilisateur SET credits = credits + ? WHERE id_utilisateur = ?");
            $stmt->execute([$info['prix_personne'], $id_passager]);

            // Libérer la place
            $stmt = $this->conn->prepare("UPDATE covoiturage SET nb_place = nb_place + 1 WHERE id_covoiturage = ?");
            $stmt->execute([$id_covoiturage]);

            // Marquer participation comme ANNULÉ
            $stmt = $this->conn->prepare("UPDATE participation SET statut = 'ANNULÉ' WHERE id_covoiturage = ? AND id_passager = ?");
            $stmt->execute([$id_covoiturage, $id_passager]);

            $this->conn->commit();
            
            // MAJ Session (si c'est bien l'user co)
            if(isset($_SESSION['user']) && $_SESSION['user']['id'] == $id_passager) {
                $_SESSION['user']['credits'] += $info['prix_personne'];
            }

            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return "Erreur : " . $e->getMessage();
        }
    }

    // US 10 : Annulation par le conducteur (Suppression trajet)
    public function cancelRide($id_covoiturage, $id_conducteur) {
        try {
            $this->conn->beginTransaction();

            $stmt = $this->conn->prepare("SELECT statut, prix_personne FROM covoiturage WHERE id_covoiturage = ? AND id_conducteur = ?");
            $stmt->execute([$id_covoiturage, $id_conducteur]);
            $ride = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$ride) return "Trajet introuvable.";
            if ($ride['statut'] !== 'PLANIFIÉ') return "Impossible d'annuler un trajet en cours ou terminé.";

            // Rembourser TOUS les passagers
            $stmt = $this->conn->prepare("SELECT id_passager FROM participation WHERE id_covoiturage = ? AND statut IN ('CONFIRMÉ', 'VALIDÉ')");
            $stmt->execute([$id_covoiturage]);
            $passengers = $stmt->fetchAll(PDO::FETCH_COLUMN);

            if (!empty($passengers)) {
                $stmtRefund = $this->conn->prepare("UPDATE utilisateur SET credits = credits + ? WHERE id_utilisateur = ?");
                $stmtUpdatePart = $this->conn->prepare("UPDATE participation SET statut = 'ANNULÉ_PAR_CONDUCTEUR' WHERE id_covoiturage = ? AND id_passager = ?");
                
                foreach ($passengers as $pId) {
                    $stmtRefund->execute([$ride['prix_personne'], $pId]);
                    $stmtUpdatePart->execute([$id_covoiturage, $pId]);
                }
            }

            // Annuler le trajet
            $stmt = $this->conn->prepare("UPDATE covoiturage SET statut = 'ANNULÉ' WHERE id_covoiturage = ?");
            $stmt->execute([$id_covoiturage]);

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollBack();
            return "Erreur : " . $e->getMessage();
        }
    }
}
