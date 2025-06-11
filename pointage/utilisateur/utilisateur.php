

<?php
// Database connection
try {
    $pdo = new PDO("mysql:host=localhost;dbname=bdpointage", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Erreur de connexion à la base de données : " . $e->getMessage();
    die();
}

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $nom_utilisateur = $_POST['nom_utilisateur'] ?? '';
    $mot_passe = $_POST['mot_passe'] ?? '';
    $role = $_POST['role'] ?? '';

    // Basic validation
    if (empty($nom_utilisateur) || empty($mot_passe) || empty($role)) {
        echo "<script>alert('Veuillez remplir tous les champs.');</script>";
    } else {
        // Hash the password for security
        // Use password_hash() for strong, secure password hashing
        $hashed_password = password_hash($mot_passe, PASSWORD_DEFAULT);

        try {
            // Prepare an SQL INSERT statement
            $stmt = $pdo->prepare("INSERT INTO utilisateur (nom_utilisateur, mot_passe, role) VALUES (:nom_utilisateur, :mot_passe, :role)");

            // Bind parameters
            $stmt->bindParam(':nom_utilisateur', $nom_utilisateur);
            $stmt->bindParam(':mot_passe', $hashed_password); // Store the hashed password
            $stmt->bindParam(':role', $role);

            // Execute the statement
            if ($stmt->execute()) {
                // Redirect to the user list page after successful insertion
                header("Location: index.php?success=1"); // Add a success parameter for feedback
                exit(); // Always exit after a header redirect
            } else {
                echo "<script>alert('Erreur lors de l\\'ajout de l\\'utilisateur.');</script>";
            }
        } catch(PDOException $e) {
            echo "Erreur d'insertion : " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un utilisateur</title>
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
            min-height: 100vh; /* Ensure body takes full viewport height */
        }

        #menu-toggle {
            display: none; /* Hide the checkbox itself */
        }

        #menu-toggle + label {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 102; /* Ensure the label/button is clickable */
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
            background-color: #1e9a83; /* Slightly darker on hover */
        }

        #menu-toggle:checked ~ .sidebar {
            left: -250px;
        }

        #menu-toggle:checked ~ .content-wrapper {
            margin-left: 0;
            width: 100%; /* Take full width when sidebar is hidden */
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
            background-position: center; /* Center the background image */
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
            color: #899DC1; /* Default link color */
            transition: background 0.3s, color 0.3s;
        }

        .side-menu a.active {
            background: #2B384E;
            color: #fff; /* Active link color */
        }

        .side-menu a:hover:not(.active) {
            background: #2B384E; /* Hover effect */
            color: #fff;
        }

        .side-menu a span {
            display: block;
            text-align: center;
            font-size: 1.7rem;
            margin-bottom: 5px; /* Space between icon and text */
        }

        .side-menu a span, .side-menu a small {
            color: inherit; /* Inherit color from parent 'a' tag */
        }

        .content-wrapper {
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 20px;
            margin-left: 250px; /* Account for the sidebar width */
            transition: margin-left 300ms;
            width: calc(100% - 250px); /* Adjust width to not overflow */
        }

        .form-container {
            width: 100%;
            max-width: 1000px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px; /* Slightly more rounded corners */
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); /* Enhanced shadow */
        }
        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
            flex-wrap: wrap; /* Allow items to wrap on smaller screens */
        }

        .form-group {
            flex: 1;
            min-width: 280px; /* Ensure a minimum width for form groups */
        }
        .form-container button {
            margin-top: 20px;
            width: 100%;
            padding: 10px;
            font-size: 1.1rem;
            background-color: #007bff; /* Bootstrap's primary blue */
            border-color: #007bff;
        }

        .form-container button:hover {
            background-color: #0056b3; /* Darker blue on hover */
            border-color: #0056b3;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .sidebar {
                left: -250px; /* Hide by default on smaller screens */
            }

            #menu-toggle + label {
                display: block; /* Show toggle button on smaller screens */
            }

            #menu-toggle:checked ~ .sidebar {
                left: 0; /* Show sidebar when toggled */
            }

            .content-wrapper {
                margin-left: 0; /* Always start at 0 margin on small screens */
                width: 100%;
            }

            #menu-toggle:checked ~ .content-wrapper {
                /* When sidebar is open, you might want to overlay or push content */
                /* For now, it will just not have the margin-left of the sidebar */
            }

            .form-row {
                flex-direction: column; /* Stack form groups vertically on small screens */
                gap: 0;
            }

            .form-group {
                min-width: unset; /* Remove min-width for stacking */
                margin-bottom: 15px; /* Add margin between stacked groups */
            }
        }
    </style>
</head>
<body>
    <input type="checkbox" id="menu-toggle">
    <label for="menu-toggle"><span class="las la-bars"></span></label> <div class="sidebar">
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
                       <a href="index.php" class="active"> <span class="las la-users"></span> <small>Liste utilisateurs</small>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="content-wrapper">
        <div class="container mt-5 form-container">
            <center><h1>Ajouter un utilisateur</h1></center>
            <form method="post" action=""> <div class="form-row">
                    <div class="form-group">
                        <label for="nom_utilisateur">Nom :</label>
                        <input type="text" name="nom_utilisateur" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="mot_passe">Mot de passe :</label>
                        <input type="password" name="mot_passe" class="form-control" required> </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="role">Rôle :</label>
                        <select name="role" id="role" class="form-control" required> <option value="">Sélectionner un rôle</option> <option value="Admin">Admin</option>
                            <option value="Users">Utilisateur</option>
                            <option value="Secretaire">Secrétaire</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Ajouter</button>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>