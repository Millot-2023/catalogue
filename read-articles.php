<?php
header("Access-Control-Allow-Origin: *"); // Permet à n'importe quel domaine d'accéder (utile pour le développement)
header("Content-Type: application/json; charset=UTF-8"); // Indique que la réponse est du JSON

// Paramètres de connexion à la base de données
$servername = "localhost";
$username = "root"; // Nom d'utilisateur par défaut de MySQL avec XAMPP
$password = "";     // Mot de passe par défaut vide pour MySQL avec XAMPP
$dbname = "catalogue_db";

// Créer une connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die(json_encode(["message" => "Échec de la connexion à la base de données: " . $conn->connect_error]));
}

// Requête SQL pour récupérer tous les articles
$sql = "SELECT id, titre, image_url, date_publication, resume, contenu_complet, created_at FROM articles";
$result = $conn->query($sql);

$articles = array();

if ($result->num_rows > 0) {
    // Parcourir chaque ligne de résultat et ajouter à un tableau
    while($row = $result->fetch_assoc()) {
        $articles[] = $row;
    }
} else {
    // Aucune donnée trouvée
    // Vous pouvez choisir de renvoyer un tableau vide ou un message
    // $articles = []; // C'est déjà le cas si num_rows est 0
}

// Fermer la connexion
$conn->close();

// Renvoyer les données en format JSON
echo json_encode($articles);

?>