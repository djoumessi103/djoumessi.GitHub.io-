<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=bdpointage", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erreur de connexion à la base de données : " . $e->getMessage();
    die();
}

// Récupérer l'ID de l'utilisateur à supprimer
if (isset($_GET['id'])) {
    $id_utilisateur = $_GET['id'];

    // Récupérer les informations de l'utilisateur à partir de la base de données
    $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE id_utilisateur = ?");
    $stmt->execute([$id_utilisateur]);
    $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$utilisateur) {
        echo "Utilisateur non trouvé.";
        die();
    }
} else {
    echo "ID de l'utilisateur non spécifié.";
    die();
}

// Traitement de la suppression
if (isset($_POST['Supprimer'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM utilisateur WHERE id_utilisateur = ?");
        $stmt->execute([$id_utilisateur]);

        echo "<div class='container mt-3'><div class='alert alert-success'>utilisateur supprimé avec succès.</div></div>";
        // Redirect to a list of suppliers or another appropriate page
         header("Location: index.php"); // Replace with your actual list page
        // exit;

    } catch (PDOException $e) {
        echo "<div class='container mt-3'><div class='alert alert-danger'>Erreur lors de la suppression du utilisateur : " . $e->getMessage() . "</div></div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestion des utilisateurs</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Supprimer un utilisateur</h1>
        <p>Êtes-vous sûr de vouloir supprimer l'utilisateur "<?php echo $utilisateur['nom_utilisateur'] . ' ' . $utilisateur['mot_passe']; ?>"?</p>
        <form method="post">
            <button type="submit" name="Supprimer" value="Supprimer" class="btn btn-danger">Supprimer</button>
            <a href="index.php" class="btn btn-secondary">Annuler</a>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
