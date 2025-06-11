<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=bdpointage", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erreur de connexion à la base de données : " . $e->getMessage();
    die();
}

// Récupérer l'ID du log à supprimer
if (isset($_GET['id'])) {
    $id_log = $_GET['id'];

    // Récupérer les informations du log à partir de la base de données
    $stmt = $pdo->prepare("SELECT * FROM log WHERE id_log = ?");
    $stmt->execute([$id_log]);
    $log = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$log) {
        echo "<div class='container mt-3'><div class='alert alert-danger'>Log non trouvé.</div></div>";
        die();
    }
} else {
    echo "<div class='container mt-3'><div class='alert alert-danger'>ID du log non spécifié.</div></div>";
    die();
}

// Traitement de la suppression
if (isset($_POST['Supprimer'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM log WHERE id_log = ?");
        $stmt->execute([$id_log]);

        echo "<div class='container mt-3'><div class='alert alert-success'>Log supprimé avec succès.</div></div>";
        // Rediriger vers la liste des logs
        header("Location: index.php");
        exit(); // Important : Arrêter l'exécution après la redirection
    } catch (PDOException $e) {
        echo "<div class='container mt-3'><div class='alert alert-danger'>Erreur lors de la suppression du log : " . $e->getMessage() . "</div></div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Supprimer un log</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Supprimer un log</h1>
        <p>Êtes-vous sûr de vouloir supprimer le log "<?php echo htmlspecialchars($log['date_heure_action'] . ' ' . $log['utilisateur']); ?>" ?</p>
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
