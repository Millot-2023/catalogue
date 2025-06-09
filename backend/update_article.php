<?php

// Inclure le fichier de configuration de la base de données
require_once 'db_config.php';

// Définit le type de contenu de la réponse comme JSON
header('Content-Type: application/json');

// Récupérer le contenu JSON envoyé dans le corps de la requête
$input = file_get_contents('php://input');
$data = json_decode($input, true); // true pour obtenir un tableau associatif

// Vérifier si le décodage JSON a réussi
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['success' => false, 'message' => 'Données JSON invalides reçues. Erreur: ' . json_last_error_msg()]);
    exit();
}

// Assurez-vous que les clés existent et nettoyez/validez les données
// Les requêtes préparées de PDO protègent contre les injections SQL.
// htmlspecialchars est utilisé pour la protection XSS lors de l'affichage ultérieur dans le HTML.

$id = filter_var($data['id'] ?? null, FILTER_SANITIZE_NUMBER_INT);
$titre = htmlspecialchars($data['titre'] ?? '', ENT_QUOTES, 'UTF-8');
$image_url = filter_var($data['image_url'] ?? '', FILTER_SANITIZE_URL);
$date_publication = $data['date_publication'] ?? ''; // La date devrait être au format YYYY-MM-DD
$resume = htmlspecialchars($data['resume'] ?? '', ENT_QUOTES, 'UTF-8');
$contenu_complet = htmlspecialchars($data['contenu_complet'] ?? '', ENT_QUOTES, 'UTF-8');

// Validation basique des données obligatoires
// L'ID doit être un entier positif, le titre et la date ne doivent pas être vides.
if (empty($id) || !is_numeric($id) || $id <= 0 || empty($titre) || empty($date_publication)) {
    echo json_encode(['success' => false, 'message' => 'ID, titre ou date de publication manquants ou invalides.']);
    exit();
}

try {
    // Préparer la requête SQL de mise à jour
    $stmt = $pdo->prepare("UPDATE articles SET titre = ?, image_url = ?, date_publication = ?, resume = ?, contenu_complet = ? WHERE id = ?");

    // Exécuter la requête avec les valeurs nettoyées
    $stmt->execute([
        $titre,
        $image_url,
        $date_publication,
        $resume,
        $contenu_complet,
        $id
    ]);

    // Vérifier si la mise à jour a affecté des lignes
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Article mis à jour avec succès !']);
    } else {
        // Cela peut arriver si l'ID n'existe pas ou si aucune modification n'a été faite
        echo json_encode(['success' => false, 'message' => 'Aucun article trouvé avec cet ID ou aucune modification effectuée.']);
    }

} catch (PDOException $e) {
    // Capturer et renvoyer les erreurs de base de données en JSON
    error_log("Erreur PDO dans update_article.php: " . $e->getMessage()); // Enregistre l'erreur dans le log Apache
    echo json_encode(['success' => false, 'message' => 'Erreur de base de données lors de la mise à jour : ' . $e->getMessage()]);
}