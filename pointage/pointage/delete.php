<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=bdpointage", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Erreur de connexion à la base de données : " . $e->getMessage() . "</div>";
    die();
}

// Récupérer l'ID du pointage à supprimer
if (isset($_GET['id'])) {
    $id_pointage = $_GET['id'];

    // Récupérer les informations du pointage à partir de la base de données
    $stmt = $pdo->prepare("SELECT * FROM pointage WHERE id_pointage = ?");
    $stmt->execute([$id_pointage]);
    $pointage = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pointage) {
        echo "<div class='alert alert-warning'>Pointage non trouvé.</div>";
        die();
    }
} else {
    echo "<div class='alert alert-danger'>ID du pointage non spécifié.</div>";
    die();
}

// Traitement de la suppression
if (isset($_POST['Supprimer'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM pointage WHERE id_pointage = ?");
        $stmt->execute([$id_pointage]);

        echo "<div class='container mt-3'><div class='alert alert-success'>Pointage supprimé avec succès.</div></div>";
        // Redirection vers la liste des pointages
        header("Location: index.php");
        exit();

    } catch (PDOException $e) {
        echo "<div class='container mt-3'><div class='alert alert-danger'>Erreur lors de la suppression du pointage : " . $e->getMessage() . "</div></div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestion des pointages</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Supprimer un pointage</h1>
        <p>Êtes-vous sûr de vouloir supprimer le pointage du <?php echo $pointage['date'] . ' à ' . $pointage['heure_pointage']; ?>?</p>
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