<?php
// Database connection
try {
    $pdo = new PDO("mysql:host=localhost;dbname=bdpointage", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Erreur de connexion à la base de données : " . $e->getMessage() . "</div>";
    die();
}

$parametre = null; // Initialize $parametre to null

// Retrieve parameter ID from URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_parametre = $_GET['id'];

    // Fetch parameter information from database
    $stmt = $pdo->prepare("SELECT * FROM parametre WHERE id_parametre = ?");
    $stmt->execute([$id_parametre]);
    $parametre = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch as associative array

    if (!$parametre) {
        echo "<div class='container mt-3'><div class='alert alert-warning'>Paramètre non trouvé.</div></div>";
        die();
    }
} else {
    echo "<div class='container mt-3'><div class='alert alert-danger'>ID du paramètre non spécifié ou invalide.</div></div>";
    die();
}

// Handle form submission for modification
if (isset($_POST['modifier'])) {
    $nom_parametre = $_POST['nom_parametre'] ?? '';
    $valeur_parametre = $_POST['valeur_parametre'] ?? '';

    // Basic validation
    if (empty($nom_parametre) || empty($valeur_parametre)) {
        echo "<div class='container mt-3'><div class='alert alert-warning'>Veuillez remplir tous les champs.</div></div>";
    } else {
        try {
            // Prepare and execute the UPDATE statement
            $stmt = $pdo->prepare("UPDATE parametre SET nom_parametre = ?, valeur_parametre = ? WHERE id_parametre = ?");
            if ($stmt->execute([$nom_parametre, $valeur_parametre, $id_parametre])) {
                // Redirect to the list page after successful update
                header("Location: index.php?status=success_update");
                exit(); // Important: Stop script execution after redirection
            } else {
                 echo "<div class='container mt-3'><div class='alert alert-danger'>Erreur lors de la modification du paramètre.</div></div>";
            }
        } catch (PDOException $e) {
            echo "<div class='container mt-3'><div class='alert alert-danger'>Erreur lors de la modification du paramètre : " . $e->getMessage() . "</div></div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le parametre</title> <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
            min-height: 100vh;
            overflow-x: hidden;
        }

        #menu-toggle {
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
            transition: background-color 0.3s;
        }

        #menu-toggle + label:hover {
            background-color: #1e9a83;
        }

        #menu-toggle:checked ~ .sidebar {
            left: -250px;
        }

        #menu-toggle:checked ~ .content-wrapper {
            margin-left: 0;
            width: 100%;
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
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
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
            background-position: center;
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
            color: #899DC1;
            transition: background 0.3s, color 0.3s;
        }

        .side-menu a.active {
            background: #2B384E;
            color: #fff;
        }

        .side-menu a:hover:not(.active) {
            background: #2B384E;
            color: #fff;
        }

        .side-menu a span {
            display: block;
            text-align: center;
            font-size: 1.7rem;
            margin-bottom: 5px;
        }

        .side-menu a span, .side-menu a small {
            color: inherit;
        }

        .content-wrapper {
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 20px;
            margin-left: 250px;
            transition: margin-left 300ms;
            width: calc(100% - 250px);
        }

        .form-container {
            width: 100%;
            max-width: 1000px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }

        .form-group {
            flex: 1;
            min-width: 280px;
        }
        .form-container button {
            margin-top: 20px;
            width: 100%;
            padding: 10px;
            font-size: 1.1rem;
            background-color: #007bff;
            border-color: #007bff;
        }

        .form-container button:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .sidebar {
                left: -250px;
            }

            #menu-toggle + label {
                display: block;
            }

            #menu-toggle:checked ~ .sidebar {
                left: 0;
            }

            .content-wrapper {
                margin-left: 0;
                width: 100%;
            }

            .form-row {
                flex-direction: column;
                gap: 0;
            }

            .form-group {
                min-width: unset;
                margin-bottom: 15px;
            }
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
                       <a href="http://localhost/pointage/accueil/accueil.php" class="">
                            <span class="las la-home"></span>
                            <small>Dashboard</small>
                        </a>
                    </li>
                    <li>
                       <a href="index.php">
                            <span class="las la-list"></span>
                            <small>Liste parametres</small>
                        </a>
                    </li>
                    <li>
                       <a href="parametre.php">
                            <span class="las la-plus-circle"></span>
                            <small>Ajouter parametres</small>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="content-wrapper">
        <div class="container mt-5 form-container">
            <center><h1>Modifier le parametre</h1></center> <form method="post" action="">
                <div class="form-row">
                    <div class="form-group">
                        <label for="nom_parametre">Nom parametre :</label>
                        <input type="text" name="nom_parametre" class="form-control" value="<?php echo htmlspecialchars($parametre['nom_parametre'] ?? ''); ?>" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="valeur_parametre">Valeur parametre :</label>
                        <input type="text" name="valeur_parametre" class="form-control" value="<?php echo htmlspecialchars($parametre['valeur_parametre'] ?? ''); ?>" required>
                    </div>
                </div> <button type="submit" name="modifier" class="btn btn-primary">Modifier</button> </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>