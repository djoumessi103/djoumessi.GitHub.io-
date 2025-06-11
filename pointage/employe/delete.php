<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=bdpointage", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erreur de connexion à la base de données : " . $e->getMessage();
    die();
}

// Récupérer l'ID de l'employe à supprimer
if (isset($_GET['id'])) {
    $id_employe = $_GET['id'];

    // Récupérer les informations de l'employe à partir de la base de données
    $stmt = $pdo->prepare("SELECT * FROM employe WHERE id_employe = ?");
    $stmt->execute([$id_employe]);
    $employe = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$employe) {
        echo "Employe non trouvé.";
        die();
    }
} else {
    echo "ID de l'employe non spécifié.";
    die();
}

// Traitement de la suppression
if (isset($_POST['Supprimer'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM employe WHERE id_employe = ?");
        $stmt->execute([$id_employe]);

        // Instead of echoing HTML, set a JavaScript variable.
        echo "<script>var deletionSuccess = true;</script>";
        // Redirect to a list of suppliers or another appropriate page
         header("Location: index.php"); // Replace with your actual list page
        exit(); // Ensure that no other HTML is sent after the redirect
       

    } catch (PDOException $e) {
        echo "<div class='container mt-3'><div class='alert alert-danger'>Erreur lors de la suppression de l'employe : " . $e->getMessage() . "</div></div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestion des employes</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Supprimer un employe</h1>
        <p>Êtes-vous sûr de vouloir supprimer l'employe "<?php echo $employe['nom'] . ' ' . $employe['prenom']; ?>"?</p>
        <form method="post">
            <button type="submit" name="Supprimer" value="Supprimer" class="btn btn-danger">Supprimer</button>
            <a href="index.php" class="btn btn-secondary">Annuler</a>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Check the JavaScript variable and display the message if it's set.
        if (typeof deletionSuccess !== 'undefined' && deletionSuccess === true) {
            alert("Employe supprimé avec succès!");
        }
    </script>
</body>
</html>

