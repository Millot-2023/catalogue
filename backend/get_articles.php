<?php
// Définit l'en-tête pour indiquer que la réponse sera du JSON
header('Content-Type: application/json');
// Permet à n'importe quelle origine d'accéder à cette ressource.
// À FAIRE : Pour la production, remplacez '*' par le domaine spécifique de votre frontend
header('Access-Control-Allow-Origin: *');

// Initialisation de la réponse JSON avec un état d'échec par défaut
$response = ['success' => false, 'message' => ''];

// Informations de connexion à la base de données
// Assurez-vous que ces informations correspondent à votre configuration XAMPP
$servername = "localhost";
$username = "root"; // Nom d'utilisateur par défaut de XAMPP
$password = "";     // Mot de passe par défaut de XAMPP (vide)
$dbname = "articles_db"; // Nom de votre base de données

try {
    // Crée une nouvelle connexion PDO à la base de données
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    // Définit le mode d'erreur PDO à Exception, ce qui permet de capturer les erreurs avec try/catch
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Requête SQL pour sélectionner tous les articles depuis la table 'articles'
    // Ordonné par 'created_at' (le plus récent en premier) ou 'id' si vous préférez
    $stmt = $conn->prepare("SELECT id, titre, image_url, date_publication, resume, contenu_complet FROM articles ORDER BY created_at DESC");
    $stmt->execute();

    // Récupère tous les résultats sous forme de tableau associatif
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Si des articles sont trouvés
    if ($articles) {
        $response['success'] = true;
        $response['message'] = 'Articles chargés avec succès depuis la base de données.';
        $response['articles'] = $articles; // Affecte le tableau des articles récupérés
    } else {
        // Aucun article trouvé dans la base de données
        $response['success'] = true; // C'est un succès, mais il n'y a juste pas d'articles
        $response['message'] = 'Aucun article trouvé dans la base de données.';
        $response['articles'] = []; // Retourne un tableau vide
    }

} catch (PDOException $e) {
    // En cas d'erreur de connexion ou de requête SQL
    $response['message'] = 'Erreur de base de données : ' . $e->getMessage();
    // Log l'erreur pour le débogage (ne pas afficher en production pour des raisons de sécurité)
    error_log('Erreur dans get_articles.php : ' . $e->getMessage());
}

// Encode la réponse PHP en format JSON et l'affiche
echo json_encode($response);
?>