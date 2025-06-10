<?php
// Active l'affichage des erreurs pour le débogage (à désactiver en production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// --- DÉBUT DES EN-TÊTES CORS ---
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT"); // Ajout de PUT pour les mises à jour
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Max-Age: 3600");

// Gère la requête OPTIONS (Preflight request)
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

// Récupérer le contenu JSON envoyé dans le corps de la requête
$input = file_get_contents('php://input');
$data = json_decode($input, true); // true pour obtenir un tableau associatif

// Vérifier si le décodage JSON a réussi
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['success' => false, 'message' => 'Données JSON invalides reçues. Erreur: ' . json_last_error_msg()]);
    exit();
}

// Assurez-vous que les clés existent et nettoyez/validez les données
$id = filter_var($data['id'] ?? null, FILTER_SANITIZE_NUMBER_INT);
// Note : htmlspecialchars est appliqué ici pour la sécurité XSS au cas où ces valeurs seraient réaffichées directement.
// Les requêtes préparées de PDO protègent déjà contre les injections SQL.
$titre = htmlspecialchars($data['titre'] ?? '', ENT_QUOTES, 'UTF-8');
$image_url = filter_var($data['image_url'] ?? '', FILTER_SANITIZE_URL);
$date_publication = $data['date_publication'] ?? ''; // La date devrait être au format YYYY-MM-DD
$resume = htmlspecialchars($data['resume'] ?? '', ENT_QUOTES, 'UTF-8');
$contenu_complet = htmlspecialchars($data['contenu_complet'] ?? '', ENT_QUOTES, 'UTF-8');

// Validation basique des données obligatoires
if (empty($id) || !is_numeric($id) || $id <= 0 || empty($titre) || empty($date_publication)) {
    echo json_encode(['success' => false, 'message' => 'ID, titre ou date de publication manquants ou invalides.']);
    exit();
}

try {
    // Préparer la requête SQL de mise à jour
    // Utilisation de marqueurs nominatifs pour plus de clarté
    $sql = "UPDATE articles SET titre = :titre, image_url = :image_url, date_publication = :date_publication, resume = :resume, contenu_complet = :contenu_complet WHERE id = :id";
    $stmt = $pdo->prepare($sql);

    // Exécuter la requête avec les valeurs liées aux marqueurs nominatifs
    $stmt->execute([
        ':titre' => $titre,
        ':image_url' => $image_url,
        ':date_publication' => $date_publication,
        ':resume' => $resume,
        ':contenu_complet' => $contenu_complet,
        ':id' => $id
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
?>