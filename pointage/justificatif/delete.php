<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=bdpointage", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Erreur de connexion à la base de données : " . $e->getMessage() . "</div>";
    die();
}

$justificatif = null; // Initialize to null

// Récupérer l'ID de la justification à supprimer
if (isset($_GET['id_justificatif'])) {
    $id_justificatif = filter_input(INPUT_GET, 'id_justificatif', FILTER_SANITIZE_NUMBER_INT);

    if (!$id_justificatif) {
        echo "<div class='alert alert-danger'>ID de justificatif invalide.</div>";
        die();
    }

    // Récupérer les informations de la justification à partir de la base de données
    $stmt = $pdo->prepare("SELECT * FROM justificatif WHERE id_justificatif = ?");
    $stmt->execute([$id_justificatif]);
    $justificatif = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$justificatif) {
        echo "<div class='alert alert-warning'>Justificatif non trouvé.</div>";
        die();
    }
} else {
    echo "<div class='alert alert-danger'>ID de justificatif non spécifié.</div>";
    die();
}

// Traitement de la suppression
if (isset($_POST['Supprimer'])) {
    try {
        // First, get the file path to delete the actual file
        $stmt_file = $pdo->prepare("SELECT fichier_justificatif FROM justificatif WHERE id_justificatif = ?");
        $stmt_file->execute([$id_justificatif]);
        $file_to_delete = $stmt_file->fetchColumn();

        $stmt = $pdo->prepare("DELETE FROM justificatif WHERE id_justificatif = ?");
        $stmt->execute([$id_justificatif]);

        // Delete the associated file from the server
        if ($file_to_delete && file_exists($file_to_delete) && is_file($file_to_delete)) {
            if (unlink($file_to_delete)) {
                // File successfully deleted
            } else {
                // Log or handle error if file could not be deleted
                error_log("Erreur: Impossible de supprimer le fichier: " . $file_to_delete);
            }
        }

        // Redirection vers la liste des justifications
        header("Location: index.php?delete_success=1");
        exit(); // Crucial to prevent further output

    } catch (PDOException $e) {
        echo "<div class='container mt-3'><div class='alert alert-danger'>Erreur lors de la suppression du justificatif : " . $e->getMessage() . "</div></div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Supprimer une justification</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Supprimer une justification</h1>
        <?php if ($justificatif): ?>
            <p>Êtes-vous sûr de vouloir supprimer la justification "<?php echo htmlspecialchars($justificatif['type_justificatif']) . ' (' . htmlspecialchars(basename($justificatif['fichier_justificatif'])) . ')'; ?>"?</p>
            <form method="post">
                <button type="submit" name="Supprimer" value="Supprimer" class="btn btn-danger">Supprimer</button>
                <a href="index.php" class="btn btn-secondary">Annuler</a>
            </form>
        <?php else: ?>
            <p>Justification non trouvée ou ID non spécifié.</p>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>