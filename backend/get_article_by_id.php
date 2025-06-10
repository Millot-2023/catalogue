<?php
// Directives pour permettre les requêtes (CORS) et définir le type de contenu JSON
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");

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

try {
    // Vérifie si un ID d'article est présent dans l'URL (GET)
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $article_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT); // Nettoie l'ID pour s'assurer que c'est un entier

        // Si après nettoyage l'ID est vide, c'est invalide
        if (empty($article_id)) {
            echo json_encode(["success" => false, "message" => "ID d'article invalide après nettoyage."]);
            exit();
        }

        // Prépare la requête SQL pour sélectionner un article par son ID
        $sql = "SELECT id, titre, image_url, date_publication, resume, contenu_complet, created_at FROM articles WHERE id = :id";

        // Prépare la déclaration PDO
        $stmt = $pdo->prepare($sql);

        // Vérifie si la préparation a échoué
        if ($stmt === false) {
            echo json_encode(["success" => false, "message" => "Erreur de préparation de la requête SQL."]);
            exit();
        }

        // Lie l'ID à la requête préparée (en utilisant un marqueur nominatif pour plus de clarté)
        $stmt->bindParam(':id', $article_id, PDO::PARAM_INT);

        // Exécute la requête
        $stmt->execute();

        // Récupère le résultat (un seul article attendu)
        $article = $stmt->fetch(PDO::FETCH_ASSOC);

        // Vérifie si un article a été trouvé
        if ($article) {
            // Succès : renvoie l'article au format JSON
            echo json_encode(["success" => true, "article" => $article]);
        } else {
            // Article non trouvé
            echo json_encode(["success" => false, "message" => "Article non trouvé."]);
        }

        // La déclaration est automatiquement fermée par PHP en fin de script.

    } else {
        // ID manquant dans la requête
        echo json_encode(["success" => false, "message" => "ID d'article manquant."]);
    }

} catch (PDOException $e) {
    // En cas d'erreur de base de données (exécution de requête)
    echo json_encode(["success" => false, "message" => "Erreur lors de la récupération de l'article : " . $e->getMessage()]);
}
?>