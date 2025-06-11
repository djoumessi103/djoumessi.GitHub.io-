<?php
session_start();

// Affichages des erreurs
if (isset($_SESSION['error']) == true) {
    echo '<div class="container mt-3"><div class="alert alert-danger">' . $_SESSION['error'] . '</div></div>';
    unset($_SESSION['error']);
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gespointage - Connexion</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/fontawasome.min.css" />
    <link rel="stylesheet" type="text/css" href="css/animate.css" />
    <link rel="stylesheet" type="text/css" href="css/connexion.css" />
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background-color: #f8f9fa;
        }

        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 40px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #343a40;
        }

        .form-group label {
            font-weight: bold;
            color: #495057;
        }

        .form-control {
            border-radius: 3px;
        }

        .btn-primary {
            width: 100%;
            border-radius: 3px;
        }

        .alert-danger {
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <h1>Gespointage</h1>
        <form name="form_connexion" method="POST" action="connexion.php">
            <fieldset>
                <legend class="sr-only">Connectez-vous</legend>

                <div class="form-group">
                    <label for="username">Nom d'utilisateur</label>
                    <input type="text" class="form-control" name="username" id="username" required placeholder="Nom d'utilisateur" />
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" class="form-control" name="password" id="password" required placeholder="Mot de passe" />
                </div>

                <button type="submit" class="btn btn-primary" name="connexion" id="connexion">Connexion</button>
            </fieldset>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>