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

// Inclut le fichier de configuration de la base de données
require_once 'db_config.php';

// Vérifie si la méthode de requête est POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupère l'ID de l'article depuis les données POST
    // Note: Utilisation de FILTER_SANITIZE_NUMBER_INT pour s'assurer que l'ID est un entier
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

    // Vérifie si l'ID est valide
    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'ID de l\'article manquant ou invalide.']);
        exit();
    }

    try {
        // Prépare la requête de suppression
        $stmt = $pdo->prepare("DELETE FROM articles WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Exécute la requête
        if ($stmt->execute()) {
            // Vérifie si des lignes ont été affectées (si l'article a bien été supprimé)
            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => true, 'message' => 'Article supprimé avec succès.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Aucun article trouvé avec cet ID ou déjà supprimé.']);
            }
        } else {
            // Ajoute des informations sur l'erreur PDO si l'exécution échoue
            $errorInfo = $stmt->errorInfo();
            echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'exécution de la suppression. Détails: ' . $errorInfo[2]]);
        }
    } catch (PDOException $e) {
        // Capture les erreurs de base de données
        echo json_encode(['success' => false, 'message' => 'Erreur de base de données: ' . $e->getMessage()]);
    }
} else {
    // Si la méthode de requête n'est pas POST
    echo json_encode(['success' => false, 'message' => 'Méthode de requête non supportée. Seules les requêtes POST sont acceptées.']);
}
?>