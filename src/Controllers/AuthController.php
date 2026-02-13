<?php
class AuthController {
    
    public function login() {
        require_once __DIR__ . '/../Config/Database.php';
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];
            
            $db = (new Database())->getConnection();
            $stmt = $db->prepare("SELECT * FROM utilisateur WHERE email = :email");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password'])) {
                // Vérifier si suspendu
                if ($user['is_suspended'] == 1) {
                    $error = "Votre compte a été suspendu bloqué. Si vous pensez que c'est une erreur, contactez le support à support@ecoride.com.";
                } else {
                    // Connexion réussie
                    $_SESSION['user'] = [
                        'id' => $user['id_utilisateur'],
                        'pseudo' => $user['pseudo'],
                    'role' => $user['id_role'],
                    'photo' => $user['photo'],
                    'credits' => $user['credits'],
                    'role_id' => $user['id_role'] // Ajout explicite pour cohérence
                ];
                
                if ($user['id_role'] == 3) {
                    header('Location: index.php?page=employee_reviews');
                } elseif ($user['id_role'] == 4) {
                    header('Location: index.php?page=admin_dashboard');
                } else {
                    header('Location: index.php');
                }
                }
            } else {
                $error = "Email ou mot de passe incorrect.";
            }
        }
        
        require_once __DIR__ . '/../Views/login.php';
    }
    
    public function logout() {
        session_destroy();
        header('Location: index.php');
        exit;
    }

    // US 7: Création de compte (Affichage)
    public function register() {
        require_once __DIR__ . '/../Views/register.php';
    }

    // US 7: Création de compte (Traitement)
    public function registerAction() {
        $pseudo = trim($_POST['pseudo'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['password_confirm'] ?? '';
        $dateNaissance = $_POST['date_naissance'] ?? null;

        if (empty($pseudo) || empty($email) || empty($password) || empty($dateNaissance)) {
            header('Location: index.php?page=register&error=Tous les champs sont requis&pseudo=' . urlencode($pseudo) . '&email=' . urlencode($email));
            exit;
        }
        
        // Vérification Age (Optionnel : > 18 ans)
        $age = (new DateTime())->diff(new DateTime($dateNaissance))->y;
        if ($age < 18) {
            header('Location: index.php?page=register&error=Vous devez avoir au moins 18 ans pour vous inscrire&pseudo=' . urlencode($pseudo) . '&email=' . urlencode($email));
            exit;
        }

        if ($password !== $confirm) {
            header('Location: index.php?page=register&error=Les mots de passe ne correspondent pas&pseudo=' . urlencode($pseudo) . '&email=' . urlencode($email));
            exit;
        }
        
        // Sécurisation du mot de passe (8 caractères, 1 maj, 1 min, 1 chiffre, 1 spécial)
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
             header('Location: index.php?page=register&error=Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial&pseudo=' . urlencode($pseudo) . '&email=' . urlencode($email));
             exit;
        }

        require_once __DIR__ . '/../Config/Database.php';
        $db = new \Database();
        $conn = $db->getConnection();

        // Vérifier si email existe déjà
        $stmt = $conn->prepare("SELECT COUNT(*) FROM utilisateur WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() > 0) {
            header('Location: index.php?page=register&error=Cet email est déjà utilisé&pseudo=' . urlencode($pseudo) . '&email=' . urlencode($email));
            exit;
        }

        // Vérifier si pseudo existe déjà
        $stmt = $conn->prepare("SELECT COUNT(*) FROM utilisateur WHERE pseudo = ?");
        $stmt->execute([$pseudo]);
        if ($stmt->fetchColumn() > 0) {
            header('Location: index.php?page=register&error=Ce pseudo est déjà pris&pseudo=' . urlencode($pseudo) . '&email=' . urlencode($email));
            exit;
        }

        // Hachage du mot de passe
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Insertion avec crédits dynamiques (US 7) et rôle Utilisateur (2)
        // Ajout de la date de naissance
        // Si le paramètre n'existe pas en base, on donne 20 par défaut.
        $creditsInit = \Database::getGlobalParam('credits_inscription', 20); 

        $sql = "INSERT INTO utilisateur (pseudo, email, password, date_naissance, credits, id_role) VALUES (?, ?, ?, ?, ?, 2)";
        $stmt = $conn->prepare($sql);
        
        if ($stmt->execute([$pseudo, $email, $hashedPassword, $dateNaissance, $creditsInit])) {
            header('Location: index.php?page=login&success=Compte créé avec succès ! Connectez-vous.');
        } else {
            header('Location: index.php?page=register&error=Erreur technique lors de la création');
        }
    }
}
