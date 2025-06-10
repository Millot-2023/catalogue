<?php
// Active l'affichage des erreurs pour le débogage (à désactiver en production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// --- DÉBUT DES EN-TÊTES CORS ---
// Permet à n'importe quelle origine d'accéder à la ressource. Pour la production, remplacez '*' par l'URL exacte de votre frontend.
header("Access-Control-Allow-Origin: *");
// Permet les méthodes HTTP spécifiques (POST, GET, OPTIONS, etc.)
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
// Permet les en-têtes spécifiques dans la requête
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
// Permet l'envoi de cookies (si nécessaire pour les sessions/authentification)
header("Access-Control-Allow-Credentials: true");
// Définit le temps pendant lequel les résultats de la requête Preflight (OPTIONS) peuvent être mis en cache
header("Access-Control-Max-Age: 3600");

// Gère la requête OPTIONS (Preflight request) qui est envoyée avant la requête réelle par certains navigateurs
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(); // Termine la requête OPTIONS sans exécuter le reste du script
}
// --- FIN DES EN-TÊTES CORS ---

// Définit l'en-tête pour indiquer une réponse JSON
header('Content-Type: application/json');

// --- DÉBUT DE LA GESTION DE LA CONFIGURATION DE LA BASE DE DONNÉES ---
// Détermination de l'environnement (local ou production)
$config_file = 'db_config.local.php'; // Par défaut, environnement local

// Vérifie si l'hôte du serveur n'est PAS 'localhost' ou '127.0.0.1' (environnements locaux)
if (isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] !== 'localhost' && $_SERVER['SERVER_NAME'] !== '127.0.0.1') {
    $config_file = 'db_config.prod.php'; // Si ce n'est pas local, c'est la production
}

// Inclut le fichier de configuration approprié
require_once __DIR__ . '/' . $config_file;

// Connexion PDO à la base de données
try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Configure PDO pour lancer des exceptions en cas d'erreur
} catch (PDOException $e) {
    // En cas d'erreur de connexion à la base de données, retourne une erreur JSON
    echo json_encode(["success" => false, "message" => "Erreur de connexion à la base de données : " . $e->getMessage()]);
    exit(); // Arrête l'exécution du script
}
// --- FIN DE LA GESTION DE LA CONFIGURATION DE LA BASE DE DONNÉES ---


// Vérifie si la méthode de requête est POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupère l'ID de l'article depuis les données POST
    // Utilise file_get_contents("php://input") car les données DELETE/POST peuvent être envoyées en JSON
    $data = json_decode(file_get_contents("php://input"), true);
    $id = filter_var($data['id'] ?? '', FILTER_SANITIZE_NUMBER_INT); // Assure que l'ID est un entier propre

    // Vérifie si l'ID est valide
    if (empty($id)) {
        echo json_encode(['success' => false, 'message' => 'ID de l\'article manquant ou invalide.']);
        exit();
    }

    try {
        // Prépare la requête de suppression
        // Utilisation de marqueurs nominatifs (:id) pour une meilleure lisibilité
        $stmt = $pdo->prepare("DELETE FROM articles WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT); // Liage du paramètre en tant qu'entier

        // Exécute la requête
        if ($stmt->execute()) {
            // Vérifie si des lignes ont été affectées (si l'article a bien été supprimé)
            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => true, 'message' => 'Article supprimé avec succès.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Aucun article trouvé avec cet ID ou déjà supprimé.']);
            }
        } else {
            // Cette branche est rarement atteinte avec PDO::ERRMODE_EXCEPTION, mais par sécurité
            $errorInfo = $stmt->errorInfo();
            echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'exécution de la suppression. Détails: ' . $errorInfo[2]]);
        }
    } catch (PDOException $e) {
        // Capture les erreurs de base de données spécifiques à l'exécution de la requête
        echo json_encode(['success' => false, 'message' => 'Erreur de base de données: ' . $e->getMessage()]);
    }
} else {
    // Si la méthode de requête n'est pas POST
    echo json_encode(['success' => false, 'message' => 'Méthode de requête non supportée. Seules les requêtes POST (ou DELETE si configuré en JS) sont acceptées.']);
}
?>