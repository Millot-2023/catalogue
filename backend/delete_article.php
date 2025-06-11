<?php
header('Content-Type: application/json'); // Indique que la réponse sera au format JSON

// CORRECTION ICI : Remplacer 'config.php' par 'db_config.local.php'
require_once 'db_config.local.php'; // Inclure le fichier de configuration de la base de données

$response = ['success' => false, 'message' => ''];

// Vérifier si la requête est de type POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer l'ID de l'article depuis les données POST
    // S'assurer que l'ID est bien un entier pour des raisons de sécurité
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    if ($id) {
        try {
            $pdo = getDbConnection(); // Obtenir la connexion PDO à partir de db_config.local.php

            // CORRECTION ICI : Remplacer 'id_article' par 'id' dans la clause WHERE
            $stmt = $pdo->prepare("DELETE FROM articles WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            // Exécuter la requête
            if ($stmt->execute()) {
                // Vérifier si des lignes ont été affectées (si l'article a bien été supprimé)
                if ($stmt->rowCount() > 0) {
                    $response['success'] = true;
                    $response['message'] = "Article ID $id supprimé avec succès.";
                } else {
                    $response['message'] = "Aucun article trouvé avec l'ID $id.";
                }
            } else {
                $response['message'] = "Erreur lors de l'exécution de la suppression dans la base de données.";
                // Pour le débogage, vous pouvez ajouter $stmt->errorInfo() ici
                // $response['debug'] = $stmt->errorInfo();
            }
        } catch (PDOException $e) {
            $response['message'] = "Erreur de base de données: " . $e->getMessage();
            error_log("DB Error in delete_article.php: " . $e->getMessage()); // Enregistrer l'erreur dans les logs du serveur
        } catch (Exception $e) {
            $response['message'] = "Erreur interne du serveur: " . $e->getMessage();
            error_log("General Error in delete_article.php: " . $e->getMessage());
        }
    } else {
        $response['message'] = "ID d'article manquant ou invalide.";
    }
} else {
    $response['message'] = "Méthode de requête non autorisée.";
}

echo json_encode($response);
?>