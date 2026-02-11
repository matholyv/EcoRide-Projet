<?php

class AdminController {

    private function checkAuth() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 4) {
            header('Location: index.php?page=login&error=Accès réservé aux administrateurs');
            exit;
        }
    }

    public function dashboard() {
        $this->checkAuth();
        
        $db = new Database();
        $conn = $db->getConnection();
        
        // 1. Stats KPI (Chiffres clés)
        $stats = [];
        $stats['nb_users'] = $conn->query("SELECT COUNT(*) FROM utilisateur")->fetchColumn();
        $stats['nb_rides'] = $conn->query("SELECT COUNT(*) FROM covoiturage WHERE statut = 'TERMINÉ'")->fetchColumn();
        // Revenu total estimé (2 crédits par trajet terminé)
        $stats['total_revenue'] = $conn->query("SELECT COUNT(*) * 2 FROM covoiturage WHERE statut = 'TERMINÉ'")->fetchColumn();
        
        // 2. Stats pour le Graphique (Trajets par jour sur les 7 derniers jours)
        $sqlGraph = "SELECT date_depart, COUNT(*) as count 
                     FROM covoiturage 
                     WHERE date_depart >= DATE(NOW() - INTERVAL 7 DAY)
                     GROUP BY date_depart 
                     ORDER BY date_depart ASC";
        $graphData = $conn->query($sqlGraph)->fetchAll(PDO::FETCH_ASSOC);

        // 3. Liste des utilisateurs (Top 50 récents ou Recherche)
        $search = $_GET['search'] ?? '';
        $sqlUsers = "SELECT id_utilisateur, pseudo, email, id_role, credits, is_suspended 
                     FROM utilisateur";
        
        $params = [];
        if (!empty($search)) {
            $sqlUsers .= " WHERE pseudo LIKE ? OR email LIKE ?";
            $params[] = "%$search%";
            $params[] = "%$search%";
        } else {
            $sqlUsers .= " ORDER BY id_utilisateur DESC LIMIT 50";
        }
        
        $stmtUsers = $conn->prepare($sqlUsers);
        $stmtUsers->execute($params);
        $users = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../Views/admin/dashboard.php';
    }

    // Action : Suspendre ou Réactiver un utilisateur
    public function toggleSuspend() {
        $this->checkAuth();
        
        $id = $_GET['id'] ?? null;
        // On récupère l'état actuel pour l'inverser, ou on passe une action explicite
        // Le dashboard enverra 'suspend' ou 'reactivate'
        $action = $_GET['action'] ?? 'suspend'; 

        if ($id && $id != $_SESSION['user']['id']) {
            $db = new Database();
            $conn = $db->getConnection();
            
            $newState = ($action === 'suspend') ? 1 : 0;
            
            $stmt = $conn->prepare("UPDATE utilisateur SET is_suspended = ? WHERE id_utilisateur = ?");
            $stmt->execute([$newState, $id]);
            
            header("Location: index.php?page=admin_dashboard&success=user_{$action}ed");
        } else {
            header('Location: index.php?page=admin_dashboard&error=invalid_action');
        }
    }

    // Page : Détails d'un utilisateur
    public function userDetails() {
        $this->checkAuth();
        
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: index.php?page=admin_dashboard');
            exit;
        }
        
        $db = new Database();
        $conn = $db->getConnection();
        
        // 1. Infos utilisateur
        $stmt = $conn->prepare("SELECT * FROM utilisateur WHERE id_utilisateur = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if(!$user) {
             header('Location: index.php?page=admin_dashboard&error=not_found');
             exit;
        }

        // 2. Avis Reçus (en tant que conducteur)
        $stmt = $conn->prepare("
            SELECT a.*, u.pseudo as auteur_pseudo 
            FROM avis a 
            JOIN utilisateur u ON a.id_auteur = u.id_utilisateur 
            WHERE a.id_destinataire = ? 
            ORDER BY a.id_avis DESC
        ");
        $stmt->execute([$id]);
        $avisRecus = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 3. Avis Donnés (en tant que passager)
        $stmt = $conn->prepare("
            SELECT a.*, u.pseudo as destinataire_pseudo 
            FROM avis a 
            JOIN utilisateur u ON a.id_destinataire = u.id_utilisateur 
            WHERE a.id_auteur = ? 
            ORDER BY a.id_avis DESC
        ");
        $stmt->execute([$id]);
        $avisDonnes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        require_once __DIR__ . '/../Views/admin/user_detail.php'; // À créer
    }

    // Afficher formulaire création employé
    public function createEmployee() {
        $this->checkAuth();
        require_once __DIR__ . '/../Views/admin/create_employee.php'; // À créer
    }

    // Traiter création employé
    public function createEmployeeAction() {
        $this->checkAuth();
        
        $email = trim($_POST['email']);
        $pseudo = trim($_POST['pseudo']);
        $password = $_POST['password'];
        
        if (empty($email) || empty($password)) {
             header('Location: index.php?page=admin_create_employee&error=empty_fields');
             exit;
        }

        $db = new Database();
        $conn = $db->getConnection();
        
        // Vérif doublon
        $stmt = $conn->prepare("SELECT COUNT(*) FROM utilisateur WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() > 0) {
             header('Location: index.php?page=admin_create_employee&error=email_exists');
             exit;
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        // ID_ROLE = 3 (Employé)
        $stmt = $conn->prepare("INSERT INTO utilisateur (email, pseudo, password, id_role, credits) VALUES (?, ?, ?, 3, 0)");
        
        if ($stmt->execute([$email, $pseudo, $hash])) {
             header('Location: index.php?page=admin_dashboard&success=employee_created');
        } else {
             header('Location: index.php?page=admin_create_employee&error=db_error');
        }
    }

    // Gestion des Paramètres de la plateforme
    public function settings() {
        $this->checkAuth();
        
        $db = new Database();
        $conn = $db->getConnection();
        
        // Récupérer les paramètres
        $stmt = $conn->query("SELECT * FROM parametre");
        $params = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        require_once __DIR__ . '/../Views/admin/settings.php';
    }

    public function updateSettings() {
        $this->checkAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = new Database();
            $conn = $db->getConnection();
            
            foreach ($_POST['params'] as $key => $value) {
                // On met à jour la valeur pour la clé correspondante
                $stmt = $conn->prepare("UPDATE parametre SET valeur = ? WHERE propriete = ?");
                $stmt->execute([$value, $key]);
            }
            
            header("Location: index.php?page=admin_settings&success=updated");
            exit;
        }
    }
    public function updateCredits() {
        $this->checkAuth();
        
        $userId = $_POST['user_id'] ?? null;
        $amount = $_POST['credits_amount'] ?? 0;
        
        if ($userId && is_numeric($amount)) {
            $db = new Database();
            $conn = $db->getConnection();
            
            // On s'assure que l'utilisateur existe
            $stmt = $conn->prepare("UPDATE utilisateur SET credits = credits + ? WHERE id_utilisateur = ?");
            if ($stmt->execute([$amount, $userId])) {
                // Si on modifie nos propres crédits (cas rare admin), mettre à jour la session
                if ($userId == $_SESSION['user']['id']) {
                    $_SESSION['user']['credits'] += $amount;
                }
                header("Location: index.php?page=admin_user_detail&id=$userId&success=credits_updated");
            } else {
                header("Location: index.php?page=admin_user_detail&id=$userId&error=update_failed");
            }
        } else {
            header("Location: index.php?page=admin_dashboard&error=invalid_input");
        }
        exit;
    }
}
