<?php
// Directives pour permettre les requêtes (CORS)
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS"); // Autorise POST pour les mises à jour
header("Access-Control-Allow-Headers: Content-Type"); // Permet l'envoi de Content-Type

// 1. Inclut le fichier de connexion à la base de données
require_once 'db_config.php';

// Récupère les données envoyées en POST
$id = $_POST['id'] ?? null;
$titre = $_POST['titre'] ?? null;
$image_url = $_POST['image_url'] ?? null;
$date_publication = $_POST['date_publication'] ?? null;
$resume = $_POST['resume'] ?? null;
$contenu_complet = $_POST['contenu_complet'] ?? null;

try {
    // 2. Validation simple des données
    if (empty($id) || empty($titre) || empty($date_publication) || empty($resume) || empty($contenu_complet)) {
        echo json_encode(["success" => false, "message" => "Tous les champs requis (ID, Titre, Date, Résumé, Contenu) doivent être remplis."]);
        exit();
    }

    // 3. Prépare la requête SQL de mise à jour
    $sql = "UPDATE articles SET
                titre = ?,
                image_url = ?,
                date_publication = ?,
                resume = ?,
                contenu_complet = ?
            WHERE id = ?";

    // 4. Prépare la déclaration PDO
    $stmt = $pdo->prepare($sql);

    // 5. Vérifie si la préparation a échoué
    if ($stmt === false) {
        echo json_encode(["success" => false, "message" => "Erreur de préparation de la requête SQL."]);
        exit();
    }

    // 6. Lie les paramètres
    $stmt->bindParam(1, $titre, PDO::PARAM_STR);
    $stmt->bindParam(2, $image_url, PDO::PARAM_STR);
    $stmt->bindParam(3, $date_publication, PDO::PARAM_STR);
    $stmt->bindParam(4, $resume, PDO::PARAM_STR);
    $stmt->bindParam(5, $contenu_complet, PDO::PARAM_STR);
    $stmt->bindParam(6, $id, PDO::PARAM_INT); // L'ID doit être un entier

    // 7. Exécute la requête
    $stmt->execute();

    // 8. Vérifie si la mise à jour a affecté des lignes
    if ($stmt->rowCount() > 0) {
        echo json_encode(["success" => true, "message" => "Article mis à jour avec succès."]);
    } else {
        // Cela peut arriver si l'ID n'existe pas ou si aucune donnée n'a été modifiée
        echo json_encode(["success" => false, "message" => "Aucune modification n'a été apportée ou article non trouvé."]);
    }

} catch (PDOException $e) {
    // En cas d'erreur de base de données
    echo json_encode(["success" => false, "message" => "Erreur lors de la mise à jour de l'article : " . $e->getMessage()]);
}
?>