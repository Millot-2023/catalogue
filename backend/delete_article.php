<?php
header('Content-Type: application/json');

// Inclure le fichier de connexion à la base de données
// Assurez-vous que db_connect.php initialise bien une variable $pdo pour PDO
require_once 'db_connect.php';

// Les lignes error_log vont écrire dans le fichier de log de PHP (php_error.log ou error.log)
// Ces logs ne sont pas envoyés directement au navigateur, ils sont pour le débogage côté serveur.

// Récupérer les données brutes de la requête
$input = file_get_contents('php://input');
$data = json_decode($input, true);

error_log("DELETE_ARTICLE_PHP: Requête de suppression reçue. Contenu brut: " . $input);
error_log("DELETE_ARTICLE_PHP: Données décodées: " . print_r($data, true));

if (isset($data['id']) && !empty($data['id'])) {
    $article_id = intval($data['id']); // Convertir en entier immédiatement pour la sécurité
    error_log("DELETE_ARTICLE_PHP: ID d'article reçu: " . $article_id);
} else {
    error_log("DELETE_ARTICLE_PHP: Erreur: ID d'article manquant ou vide dans les données reçues.");
    echo json_encode(['success' => false, 'message' => 'ID d\'article manquant ou invalide.']);
    exit();
}

// Vérifier si l'ID est valide après conversion
if ($article_id <= 0) {
    error_log("DELETE_ARTICLE_PHP: ID d'article invalide après conversion: " . $article_id);
    echo json_encode(['success' => false, 'message' => 'ID d\'article invalide.']);
    exit();
}

try {
    // Utilisation de PDO : $pdo est la variable de connexion de db_connect.php
    $stmt = $pdo->prepare("DELETE FROM articles WHERE id = :id");
    $stmt->bindParam(':id', $article_id, PDO::PARAM_INT); // PDO::PARAM_INT pour les entiers
    $stmt->execute();

    // rowCount() avec PDO pour vérifier le nombre de lignes affectées
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Article supprimé avec succès.']);
        error_log("DELETE_ARTICLE_PHP: Article ID " . $article_id . " supprimé avec succès.");
    } else {
        echo json_encode(['success' => false, 'message' => 'Aucun article trouvé avec cet ID ou suppression échouée.']);
        error_log("DELETE_ARTICLE_PHP: Aucun article trouvé avec ID " . $article_id . " ou suppression échouée.");
    }
} catch (PDOException $e) {
    // Gérer les erreurs PDO
    error_log("DELETE_ARTICLE_PHP: Erreur de base de données (PDOException): " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erreur de base de données lors de la suppression: ' . $e->getMessage()]);
}

// La connexion PDO n'a pas besoin d'être fermée explicitement, PHP la gérera à la fin du script.
// $pdo = null; // Optionnel, mais pas nécessaire en fin de script
?>