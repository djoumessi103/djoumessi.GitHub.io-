<?php
// Database connection
try {
    $pdo = new PDO("mysql:host=localhost;dbname=bdpointage", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Erreur de connexion à la base de données : " . $e->getMessage() . "</div>";
    die();
}

$erreurs = [];
$success_message = '';
$justificatif = null;

// Retrieve justification ID from the URL
$idjustificatif = filter_input(INPUT_GET, 'id_justificatif', FILTER_SANITIZE_NUMBER_INT);

if (empty($idjustificatif)) {
    echo "<div class='alert alert-danger'>ID de justificatif invalide.</div>";
    exit;
}

// Fetch the justification data
$requete_select = $pdo->prepare("SELECT j.id_justificatif, j.id_absence, DATE_FORMAT(a.date_debut, '%Y-%m-%d') AS date_debut, j.type_justificatif, j.fichier_justificatif
                                     FROM justificatif j
                                     JOIN absence a ON j.id_absence = a.id_absence
                                     WHERE j.id_justificatif = ?");
$requete_select->execute([$idjustificatif]);
$justificatif = $requete_select->fetch(PDO::FETCH_ASSOC);

if (!$justificatif) {
    echo "<div class='alert alert-warning'>Justificatif non trouvé.</div>";
    exit;
}

// Fetch all absences for the dropdown
$stmtAbsences = $pdo->query("SELECT id_absence, DATE_FORMAT(date_debut, '%Y-%m-%d') AS date_debut FROM absence");
$absences = $stmtAbsences->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idabsence = filter_input(INPUT_POST, 'id_absence', FILTER_SANITIZE_NUMBER_INT);
    $type_justificatif = filter_input(INPUT_POST, 'type_justificatif', FILTER_SANITIZE_STRING);
    $ancien_fichier_chemin_complet = $justificatif['fichier_justificatif']; // Get the full path from the database
    $fichier_justificatif_nouveau_chemin = $ancien_fichier_chemin_complet; // Default to the old file path

    if (empty($idabsence)) {
        $erreurs[] = "L'ID de l'absence ne peut pas être vide.";
    }

    if (empty($type_justificatif)) {
        $erreurs[] = "Le type de justificatif ne peut pas être vide.";
    }

    $dossier_destination = '../uploads/';
    if (!is_dir($dossier_destination)) {
        if (!mkdir($dossier_destination, 0777, true)) {
            $erreurs[] = "Erreur : Impossible de créer le dossier de destination 'uploads/'. Veuillez vérifier les permissions.";
        }
    }

    // Handle file upload if a new file is provided
    if (isset($_FILES['fichier_justificatif']) && $_FILES['fichier_justificatif']['error'] === UPLOAD_ERR_OK) {
        $nom_fichier = basename($_FILES['fichier_justificatif']['name']);
        $tmp_fichier = $_FILES['fichier_justificatif']['tmp_name'];
        $nouveau_chemin_fichier = $dossier_destination . $nom_fichier;

        // Verify file type and size
        $types_autorises = ['application/pdf', 'image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($_FILES['fichier_justificatif']['type'], $types_autorises)) {
            $erreurs[] = "Erreur : Seuls les fichiers PDF, JPEG, PNG et GIF sont autorisés.";
        }
        if ($_FILES['fichier_justificatif']['size'] > 5000000) { // Example: 5MB
            $erreurs[] = "Erreur : La taille du fichier ne doit pas dépasser 5MB.";
        }

        if (empty($erreurs)) {
            if (move_uploaded_file($tmp_fichier, $nouveau_chemin_fichier)) {
                // Delete the old file if a new one was uploaded successfully
                if (!empty($ancien_fichier_chemin_complet) && file_exists($ancien_fichier_chemin_complet) && is_file($ancien_fichier_chemin_complet)) {
                    if (unlink($ancien_fichier_chemin_complet)) {
                        // Old file successfully deleted
                    } else {
                        error_log("Avertissement : Impossible de supprimer l'ancien fichier: " . $ancien_fichier_chemin_complet);
                    }
                }
                $fichier_justificatif_nouveau_chemin = $nouveau_chemin_fichier; // Update path to new file
            } else {
                $erreurs[] = "Erreur lors du téléchargement du nouveau fichier.";
            }
        }
    } elseif (isset($_FILES['fichier_justificatif']) && $_FILES['fichier_justificatif']['error'] !== UPLOAD_ERR_NO_FILE) {
        // Handle other file upload errors
        $erreurs[] = "Erreur lors du téléchargement du fichier : Code d'erreur " . $_FILES['fichier_justificatif']['error'];
    }

    if (empty($erreurs)) {
        $requete_update = $pdo->prepare("UPDATE justificatif SET id_absence = ?, type_justificatif = ?, fichier_justificatif = ? WHERE id_justificatif = ?");
        try {
            $requete_update->execute([$idabsence, $type_justificatif, $fichier_justificatif_nouveau_chemin, $idjustificatif]);
            $success_message = "<div class='alert alert-success'>Justificatif mis à jour avec succès. Vous allez être redirigé...</div>";
            header("refresh:2;url=index.php?update_success=1"); // Redirect after 2 seconds
            exit; // Crucial to prevent further output
        } catch (PDOException $e) {
            echo "<div class='alert alert-danger'>Erreur lors de la mise à jour de la justification : " . $e->getMessage() . "</div>";
        }
    } else {
        foreach ($erreurs as $erreur) {
            echo "<div class='alert alert-danger'>" . $erreur . "</div>";
        }
    }
}

// Re-fetch justificatif data after potential POST to show latest data
$requete_select = $pdo->prepare("SELECT j.id_justificatif, j.id_absence, DATE_FORMAT(a.date_debut, '%Y-%m-%d') AS date_debut, j.type_justificatif, j.fichier_justificatif
                                     FROM justificatif j
                                     JOIN absence a ON j.id_absence = a.id_absence
                                     WHERE j.id_justificatif = ?");
$requete_select->execute([$idjustificatif]);
$justificatif = $requete_select->fetch(PDO::FETCH_ASSOC);

$pdo = null; // Close the database connection
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier la justification</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Merriweather+Sans:wght@300;400;500;600&display=swap');

        :root {
            --main-color: #22BAA0;
            --color-dark: #34425A;
            --text-grey: #B0B0B0;
        }

        * {
            margin: 0;
            padding: 0;
            text-decoration: none;
            list-style-type: none;
            box-sizing: border-box;
            font-family: 'Merriweather Sans', sans-serif;
        }

        body {
            display: flex;
            overflow-x: hidden;
        }

        #menu-toggle {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 101;
            display: none;
        }

        #menu-toggle + label {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 102;
            display: block;
            width: 40px;
            height: 40px;
            background-color: var(--main-color);
            color: #fff;
            text-align: center;
            line-height: 40px;
            font-size: 1.5rem;
            cursor: pointer;
            border-radius: 5px;
        }

        #menu-toggle:checked ~ .sidebar {
            left: -250px;
        }

        .sidebar {
            position: fixed;
            height: 100%;
            width: 250px;
            left: 0;
            bottom: 0;
            top: 0;
            z-index: 100;
            background: var(--color-dark);
            transition: left 300ms;
            overflow-y: auto;
        }

        .side-header {
            box-shadow: 0px 5px 5px -5px rgb(0 0 0 /10%);
            background: var(--main-color);
            height: 60px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .side-header h3, .side-header span {
            color: #fff;
            font-weight: 400;
        }

        .side-content {
            overflow: auto;
            padding: 1rem;
        }

        .profile {
            text-align: center;
            padding: 2rem 0rem;
        }

        .bg-img {
            background-repeat: no-repeat;
            background-size: cover;
            border-radius: 50%;
            background-size: cover;
        }

        .profile-img {
            height: 80px;
            width: 80px;
            display: inline-block;
            margin: 0 auto .5rem auto;
            border: 3px solid #899DC1;
        }

        .profile h4 {
            color: #fff;
            font-weight: 500;
        }

        .profile small {
            color: #899DC1;
            font-weight: 600;
        }

        .side-menu ul {
            text-align: center;
            margin-bottom: 1rem;
            border-bottom: 1px solid #44546E;
            padding-bottom: 1rem;
        }

        .side-menu a {
            display: block;
            padding: 1.2rem 0rem;
        }

        .side-menu a.active {
            background: #2B384E;
        }

        .side-menu a.active span, .side-menu a.active small {
            color: #fff;
        }

        .side-menu a span {
            display: block;
            text-align: center;
            font-size: 1.7rem;
        }

        .side-menu a span, .side-menu a small {
            color: #899DC1;
        }

        .content-wrapper {
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 20px;
            margin-left: 250px;
            transition: margin-left 300ms;
        }

        #menu-toggle:checked ~ .content-wrapper {
            margin-left: 0;
        }

        .form-container {
            width: 100%;
            max-width: 1000px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
        }

        .form-group {
            flex: 1;
        }
        .form-container button {
            margin-top: 20px;
            width: 100%;
        }
    </style>
</head>
<body>
    <input type="checkbox" id="menu-toggle">
    <label for="menu-toggle"><span class="las la-bars"></span></label>
    <div class="sidebar">
        <div class="side-header">
            <h3>Tech<span>vision</span></h3>
        </div>

        <div class="side-content">
            <div class="profile">
                <div class="profile-img bg-img" style="background-image: url(img.png)"></div>
                <h4>Jessy</h4>
                <small>Art web</small>
            </div>

            <div class="side-menu">
                <ul>
                    <li>
                       <a href="http://localhost/pointage/accueil/accueil.php" class="active">
                            <span class="las la-home"></span>
                            <small>Dashboard</small>
                        </a>
                    </li>
                    <li>
                       <a href="index.php">
                            <span class="las la-file-alt"></span> <small>Liste des justifications</small>
                        </a>
                    </li>
                    <li>
                       <a href="justificatif.php">
                            <span class="las la-plus-square"></span> <small>Ajouter Justificatif</small>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="content-wrapper">
        <div class="container mt-5 form-container">
            <center><h1>Modifier la justification</h1></center>
            <?php echo $success_message; ?>
            <?php if ($justificatif): ?>
                <form action="modifier.php?id_justificatif=<?php echo htmlspecialchars($justificatif['id_justificatif']); ?>" method="post" enctype="multipart/form-data">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="id_absence">Absence :</label>
                            <select name="id_absence" class="form-control" required>
                                <option value="">Sélectionner une absence</option>
                                <?php foreach ($absences as $absence): ?>
                                    <option value="<?php echo htmlspecialchars($absence['id_absence']); ?>" <?php if ($absence['id_absence'] == $justificatif['id_absence']) echo 'selected'; ?>><?php echo htmlspecialchars($absence['date_debut']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="type_justificatif">Type justification :</label>
                            <input type="text" name="type_justificatif" class="form-control" value="<?php echo htmlspecialchars($justificatif['type_justificatif']); ?>" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="fichier_justificatif">Fichier justificatif :</label>
                            <input type="file" name="fichier_justificatif" class="form-control">
                            <small class="form-text text-muted">Fichier actuel : <?php echo htmlspecialchars(basename($justificatif['fichier_justificatif'])); ?><br>Joindre un nouveau fichier pour remplacer l'ancien ou laissez vide pour conserver l'actuel.</small>
                            </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Modifier</button>
                    <a href="index.php" class="btn btn-secondary ml-2">Annuler</a>
                </form>
            <?php else: ?>
                <p>Aucune justification trouvée avec cet ID.</p>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>