<?php
// Point d'entrée unique de l'application
session_start();

require_once __DIR__ . '/../src/Controllers/HomeController.php';
require_once __DIR__ . '/../src/Controllers/AuthController.php';
require_once __DIR__ . '/../src/Controllers/RideController.php';
require_once __DIR__ . '/../src/Config/Database.php';

// Sécurité : Vérifier si l'utilisateur connecté a été suspendu (Check temps réel)
if (isset($_SESSION['user']['id'])) {
    $db = (new Database())->getConnection();
    $stmt = $db->prepare("SELECT is_suspended FROM utilisateur WHERE id_utilisateur = ?");
    $stmt->execute([$_SESSION['user']['id']]);
    $userState = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($userState && $userState['is_suspended'] == 1) {
        // Déconnexion forcée
        session_destroy();
        session_start();
        $msg = "Votre compte a été suspendu. Si vous pensez qu'il s'agit d'une erreur, veuillez contacter le support à support@ecoride.com.";
        header('Location: index.php?page=login&error=' . urlencode($msg));
        exit;
    } else {
        // Mise à jour des crédits en temps réel pour l'affichage
        if(isset($userState['credits'])) {
            $_SESSION['user']['credits'] = $userState['credits'];
        } else {
             // Fallback si la requête précédente n'a pas pris les crédits (il faut modifier le SELECT)
             $stmt = $db->prepare("SELECT credits FROM utilisateur WHERE id_utilisateur = ?");
             $stmt->execute([$_SESSION['user']['id']]);
             $credits = $stmt->fetchColumn();
             if($credits !== false) {
                 $_SESSION['user']['credits'] = $credits;
             }
        }
    }
}

$page = $_GET['page'] ?? 'home';

// Message flash pour succès
if (isset($_GET['success'])) {
    // On pourrait stocker ça proprement, ici on fera simple dans les vues
}

switch ($page) {
    case 'home':
        $controller = new HomeController();
        $controller->index();
        break;
        
    case 'search':
        $controller = new HomeController();
        $controller->search();
        break;
        
    case 'covoiturages':
        $controller = new HomeController();
        $controller->search(); 
        break;
        
    case 'login':
    case 'logout':
    case 'register':
    case 'register_action':
        $controller = new AuthController();
        if ($page === 'login') $controller->login();
        elseif ($page === 'logout') $controller->logout();
        elseif ($page === 'register') $controller->register();
        elseif ($page === 'register_action') $controller->registerAction();
        break;

    case 'profile':
    case 'profile_update':
    case 'car_add':
    case 'car_delete':
        require_once __DIR__ . '/../src/Controllers/ProfileController.php';
        $controller = new ProfileController();
        if ($page === 'profile') $controller->index();
        elseif ($page === 'profile_update') $controller->update();
        elseif ($page === 'car_add') $controller->addCar();
        elseif ($page === 'car_delete') $controller->deleteCar();
        break;

    case 'employee_reviews':
    case 'employee_disputes':
    case 'employee_validate_review':
    case 'employee_reject_review':
    case 'employee_resolve_conflict':
        require_once __DIR__ . '/../src/Controllers/EmployeeController.php';
        $controller = new EmployeeController();
        if ($page === 'employee_reviews') $controller->reviews();
        elseif ($page === 'employee_disputes') $controller->disputes();
        elseif ($page === 'employee_validate_review') $controller->validateReview();
        elseif ($page === 'employee_reject_review') $controller->rejectReview();
        elseif ($page === 'employee_resolve_conflict') $controller->resolveConflict();
        break;

    case 'admin_dashboard':
    case 'admin_ban': // Compatibilité
    case 'admin_toggle_suspend':
    case 'admin_user_detail':
    case 'admin_create_employee':
    case 'admin_create_employee_action':
    case 'admin_settings':
    case 'admin_update_settings':
    case 'admin_update_credits':
        require_once __DIR__ . '/../src/Controllers/AdminController.php';
        $controller = new AdminController();
        if ($page === 'admin_dashboard') $controller->dashboard();
        elseif ($page === 'admin_ban' || $page === 'admin_toggle_suspend') $controller->toggleSuspend();
        elseif ($page === 'admin_user_detail') $controller->userDetails();
        elseif ($page === 'admin_create_employee') $controller->createEmployee();
        elseif ($page === 'admin_create_employee_action') $controller->createEmployeeAction();
        elseif ($page === 'admin_settings') $controller->settings();
        elseif ($page === 'admin_update_settings') $controller->updateSettings();
        elseif ($page === 'admin_update_credits') $controller->updateCredits();
        break;
        
    case 'publish':
        $controller = new RideController();
        $controller->create();
        break;
        
    case 'contact':
    case 'contact_action':
        $controller = new HomeController();
        if ($page === 'contact') $controller->contact();
        elseif ($page === 'contact_action') $controller->contactAction();
        break;
        
    case 'detail':
        $controller = new RideController();
        $controller->detail();
        break;
        
    case 'book':
        $controller = new RideController();
        $controller->book();
        break;

    case 'history':
        $controller = new RideController();
        $controller->history();
        break;

    case 'start':
        $controller = new RideController();
        $controller->start();
        break;
        
    case 'end':
        $controller = new RideController();
        $controller->end();
        break;

    case 'review':
        $controller = new RideController();
        $controller->review();
        break;

    case 'submit_review':
        $controller = new RideController();
        $controller->submitReview();
        break;

    case 'driver_reviews':
        $controller = new RideController();
        $controller->showDriverReviews();
        break;

    case 'cancel':
        $controller = new RideController();
        $controller->cancel();
        break;
        
    default:
        $controller = new HomeController();
        $controller->index();
        break;
}
