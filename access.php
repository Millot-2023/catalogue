<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accès Administrateur</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <div class="login-container">
        <form class="login-form" action="" method="post">
            <h2>Accès Administrateur</h2>
            <div class="form-group">
                <label for="identifier">Identifiant :</label>
                <input type="text" id="identifier" name="identifier" required>
            </div>
            <div class="form-group">
                <label for="secret_key">Clé Secrète :</label>
                <input type="password" id="secret_key" name="secret_key" required>
            </div>
            <button type="submit">Se connecter</button>
        </form>
    </div>
</body>
</html>