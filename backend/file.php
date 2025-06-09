<?php
// Directives pour permettre les requêtes (CORS)
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");

// 1. Inclut le fichier de connexion à la base de données
require_once 'db_config.php';

try {
    // 2. Vérifie si un ID d'article est présent dans l'URL (GET)
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $article_id = $_GET['id'];

        // 3. Prépare la requête SQL pour sélectionner un article par son ID
        // Utilise un placeholder '?' pour la sécurité (prévention des injections SQL)
        $sql = "SELECT id, titre, image_url, date_publication, resume, contenu_complet, created_at FROM articles WHERE id = ?";

        // 4. Prépare la déclaration PDO
        $stmt = $pdo->prepare($sql);

        // 5. Vérifie si la préparation a échoué
        if ($stmt === false) {
            echo json_encode(["success" => false, "message" => "Erreur de préparation de la requête SQL."]);
            exit();
        }

        // 6. Lie l'ID à la requête préparée
        $stmt->bindParam(1, $article_id, PDO::PARAM_INT); // PDO::PARAM_INT indique que c'est un entier

        // 7. Exécute la requête
        $stmt->execute();

        // 8. Récupère le résultat (un seul article attendu)
        $article = $stmt->fetch(PDO::FETCH_ASSOC);

        // 9. Vérifie si un article a été trouvé
        if ($article) {
            // Succès : renvoie l'article au format JSON
            echo json_encode(["success" => true, "article" => $article]);
        } else {
            // Article non trouvé
            echo json_encode(["success" => false, "message" => "Article non trouvé."]);
        }

        // 10. Ferme la déclaration
        $stmt = null;

    } else {
        // ID manquant dans la requête
        echo json_encode(["success" => false, "message" => "ID d'article manquant."]);
    }

} catch (PDOException $e) {
    // En cas d'erreur de base de données
    echo json_encode(["success" => false, "message" => "Erreur lors de la récupération de l'article : " . $e->getMessage()]);
}
?>