<?php
// Active l'affichage des erreurs pour le débogage (à désactiver en production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// --- DÉBUT DES EN-TÊTES CORS ---
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE"); // Ajout de DELETE pour les suppressions
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Max-Age: 3600");

// Gère la requête OPTIONS (Preflight request) - à laisser pour les requêtes modernes (frontend)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();
}
// --- FIN DES EN-TÊTES CORS ---

// Définit le type de contenu de la réponse comme JSON
header('Content-Type: application/json');

// --- DÉBUT DE LA GESTION DE LA CONFIGURATION DE LA BASE DE DONNÉES ---
// Détermination de l'environnement (local ou production)
$config_file = 'db_config.local.php'; // Par défaut, environnement local

// Vérifie si l'hôte du serveur n'est PAS 'localhost' ou '127.0.0.1' (environnements locaux)
if (isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] !== 'localhost' && $_SERVER['SERVER_NAME'] !== '127.0.0.1') {
    $config_file = 'db_config.prod.php'; // Si ce n'est pas local, c'est la production
}

// Inclut le fichier de configuration approprié
// C'est dans ce fichier (db_config.local.php) que la fonction getDbConnection() DOIT être définie.
require_once __DIR__ . '/' . $config_file;
// --- FIN DE LA GESTION DE LA CONFIGURATION DE LA BASE DE DONNÉES ---

$response = ['success' => false, 'message' => ''];

$id = null; // Initialiser l'ID à null

// Récupérer l'ID de l'article à supprimer
// Ce script peut recevoir l'ID via POST (formulaire), DELETE/PUT (JSON), ou GET (URL) pour plus de flexibilité.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Cas où l'ID est envoyé via un formulaire POST standard
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE' || $_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Cas où l'ID est envoyé dans le corps JSON pour une requête DELETE ou PUT (plus moderne pour les API REST)
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    if (json_last_error() === JSON_ERROR_NONE && isset($data['id'])) {
        $id = filter_var($data['id'], FILTER_VALIDATE_INT);
    }
} elseif (isset($_GET['id'])) {
    // Cas où l'ID est envoyé via l'URL (GET), moins recommandé pour DELETE mais utile pour un test simple direct dans le navigateur
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
}


// --- DÉBUT DE LA LOGIQUE DE SUPPRESSION ---
if ($id) {
    try {
        // Appelle la fonction getDbConnection() qui DOIT être définie dans db_config.local.php
        $pdo = getDbConnection(); 

        $stmt = $pdo->prepare("DELETE FROM articles WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            if ($stmt->rowCount() > 0) {
                $response['success'] = true;
                $response['message'] = "Article ID $id supprimé avec succès.";
            } else {
                $response['message'] = "Aucun article trouvé avec l'ID $id.";
            }
        } else {
            $response['message'] = "Erreur lors de l'exécution de la suppression dans la base de données.";
        }
    } catch (PDOException $e) {
        $response['message'] = "Erreur de base de données: " . $e->getMessage();
        error_log("DB Error in delete_article.php: " . $e->getMessage());
    } catch (Exception $e) {
        $response['message'] = "Erreur interne du serveur: " . $e->getMessage();
        error_log("General Error in delete_article.php: " . $e->getMessage());
    }
} else {
    $response['message'] = "ID d'article manquant ou invalide.";
}
// --- FIN DE LA LOGIQUE DE SUPPRESSION ---

echo json_encode($response);
?>